<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // إضافة حقول التنافس
            $table->timestamp('expires_at')->nullable()->after('booking_reference');
            $table->integer('notified_suppliers_count')->default(0)->after('expires_at');
            $table->integer('views_count')->default(0)->after('notified_suppliers_count');
            $table->timestamp('accepted_at')->nullable()->after('views_count');
            
            // تحديث حقل supplier_id ليصبح nullable (سيتم تعيينه عند القبول)
            $table->foreignId('supplier_id')->nullable()->change();
            
            // إضافة indexes للأداء
            $table->index('expires_at');
            $table->index(['status', 'expires_at']);
        });
        
        // تحديث enum للحالات لتشمل 'awaiting_supplier' و 'expired'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'awaiting_supplier', 'confirmed', 'cancelled', 'expired', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'expires_at',
                'notified_suppliers_count',
                'views_count',
                'accepted_at'
            ]);
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['status', 'expires_at']);
        });
        
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
    }
};
