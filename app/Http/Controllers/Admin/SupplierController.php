<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by supplier type
        if ($request->has('type') && $request->type != '') {
            $query->where('supplier_type', $request->type);
        }

        // Search by name or email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('commercial_register', 'like', "%{$search}%");
            });
        }

        // Order by latest
        $suppliers = $query->latest()->paginate(20);

        // Get counts for badges
        $counts = [
            'all' => Supplier::count(),
            'pending' => Supplier::where('status', 'pending')->count(),
            'approved' => Supplier::where('status', 'approved')->count(),
            'rejected' => Supplier::where('status', 'rejected')->count(),
            'suspended' => Supplier::where('status', 'suspended')->count(),
        ];

        return view('admin.suppliers.index', compact('suppliers', 'counts'));
    }

    /**
     * Display the specified supplier
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Approve supplier
     */
    public function approve($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $supplier->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        \App\Models\ActivityLog::record($supplier, 'status_changed', 'تم قبول المورد', [
            'old' => 'pending',
            'new' => 'approved',
        ]);

        // TODO: Send approval email notification to supplier

        return redirect()->back()->with('success', 'تم قبول المورد بنجاح');
    }

    /**
     * Reject supplier
     */
    public function reject(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $supplier->update([
            'status' => 'rejected',
        ]);

        \App\Models\ActivityLog::record($supplier, 'status_changed', 'تم رفض المورد', [
            'old' => 'pending',
            'new' => 'rejected',
        ]);

        // TODO: Send rejection email notification to supplier with reason

        return redirect()->back()->with('success', 'تم رفض المورد');
    }

    /**
     * Suspend supplier
     */
    public function suspend(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $supplier->update([
            'status' => 'suspended',
        ]);

        \App\Models\ActivityLog::record($supplier, 'status_changed', 'تم تعليق المورد', [
            'old' => 'approved',
            'new' => 'suspended',
        ]);

        // TODO: Send suspension email notification to supplier

        return redirect()->back()->with('success', 'تم تعليق المورد');
    }

    /**
     * Activate suspended supplier
     */
    public function activate($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $supplier->update([
            'status' => 'approved',
        ]);

        \App\Models\ActivityLog::record($supplier, 'status_changed', 'تم إعادة تفعيل المورد', [
            'old' => 'suspended',
            'new' => 'approved',
        ]);

        return redirect()->back()->with('success', 'تم إعادة تفعيل المورد');
    }

    /**
     * Delete supplier
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Delete uploaded files
        if ($supplier->commercial_register_file) {
            Storage::delete($supplier->commercial_register_file);
        }
        if ($supplier->tax_certificate_file) {
            Storage::delete($supplier->tax_certificate_file);
        }
        if ($supplier->company_profile_file) {
            Storage::delete($supplier->company_profile_file);
        }
        if ($supplier->portfolio_files) {
            foreach ($supplier->portfolio_files as $file) {
                Storage::delete($file);
            }
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'تم حذف المورد بنجاح');
    }

    /**
     * Download supplier document
     */
    public function downloadDocument($id, $type)
    {
        $supplier = Supplier::findOrFail($id);
        
        $filePath = match($type) {
            'commercial_register' => $supplier->commercial_register_file,
            'tax_certificate' => $supplier->tax_certificate_file,
            'company_profile' => $supplier->company_profile_file,
            default => null
        };

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::download($filePath);
    }
}
