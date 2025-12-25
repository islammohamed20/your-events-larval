<?php

namespace App\Console\Commands;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ConvertUserToSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan user:convert-to-supplier email@example.com
     */
    protected $signature = 'user:convert-to-supplier {email : Email of the user to convert} {--force-password= : Force a new password for the supplier}';

    /**
     * The console command description.
     */
    protected $description = 'Convert an existing user account to a supplier account and revoke admin privileges';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $forcedPassword = $this->option('force-password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error('User not found for email: '.$email);

            return self::FAILURE;
        }

        // Prepare supplier data using best-effort mapping
        $supplierData = [
            'supplier_type' => 'company',
            'name' => $user->company_name ?: $user->name,
            'email' => $user->email,
            'password' => $forcedPassword
                ? Hash::make($forcedPassword)
                : ($user->password ?? Hash::make(str()->random(12))),
            'primary_phone' => $user->phone ?: 'N/A',
            'secondary_phone' => null,
            'headquarters_city' => $user->company_name ? 'غير محدد' : 'غير محدد',
            'address' => null,
            'description' => null,
            'commercial_register' => null,
            'tax_number' => $user->tax_number,
            'services_offered' => [],
            'social_media' => [],
            'status' => 'approved',
            'email_verified_at' => now(),
            'terms_accepted' => true,
            'privacy_accepted' => true,
        ];

        // Check if a supplier already exists with this email
        $existingSupplier = Supplier::where('email', $email)->first();
        if ($existingSupplier) {
            $this->warn('A supplier already exists with this email. Updating status and basics.');
            $existingSupplier->update([
                'status' => 'approved',
                'email_verified_at' => now(),
                'name' => $supplierData['name'],
                'primary_phone' => $supplierData['primary_phone'],
                'tax_number' => $supplierData['tax_number'],
            ]);
            $supplier = $existingSupplier;
        } else {
            $supplier = Supplier::create($supplierData);
        }

        // Revoke admin and set role to user
        $user->forceFill([
            'role' => 'user',
            'is_admin' => false,
        ])->save();

        $this->info('Conversion completed successfully.');
        $this->line('- Supplier ID: '.$supplier->id);
        $this->line('- Supplier email: '.$supplier->email);
        $this->line('- User role set to: '.$user->role.' and is_admin revoked');

        return self::SUCCESS;
    }
}
