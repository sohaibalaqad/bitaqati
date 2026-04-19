<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('plan', 'owner')->latest()->paginate(20);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('superadmin.tenants.form', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'subdomain'         => ['required','string','max:50','unique:tenants,subdomain','regex:/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/', new \App\Rules\SubdomainNotReserved()],
            'plan_id'           => 'required|exists:plans,id',
            'status'            => 'required|in:active,suspended,trial,pending',
            'admin_name'        => 'required|string|max:255',
            'admin_email'       => 'required|email|unique:users,email',
            'admin_phone'       => 'required|string|max:50|unique:users,phone',
            'admin_password'    => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            // Create tenant
            $tenant = Tenant::create([
                'name'      => $request->name,
                'subdomain' => $request->subdomain,
                'plan_id'   => $request->plan_id,
                'status'    => $request->status,
            ]);

            // Create network admin for this tenant
            $admin = User::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'phone'     => $request->admin_phone,
                'password'  => Hash::make($request->admin_password),
                'role'      => 'network_admin',
                'balance'   => 0,
                'status'    => 'active',
            ]);

            // Link owner
            $tenant->update(['owner_id' => $admin->id]);

            DB::commit();
            return redirect()->route('superadmin.tenants')
                             ->with('success', "تم إنشاء الشبكة \"{$tenant->name}\" بنجاح");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    public function edit(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        $plans  = Plan::where('is_active', true)->get();
        return view('superadmin.tenants.form', compact('tenant', 'plans'));
    }

    public function update(Request $request, int $id)
    {
        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'subdomain' => ['required','string','max:50','regex:/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/','unique:tenants,subdomain,'.$id],
            'plan_id'   => 'required|exists:plans,id',
            'status'    => 'required|in:active,suspended,trial,pending',
        ]);

        $tenant->update($request->only('name', 'subdomain', 'plan_id', 'status'));

        return redirect()->route('superadmin.tenants')
                         ->with('success', 'تم تحديث الشبكة بنجاح');
    }

    public function destroy(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        // Soft-delete by suspending rather than hard delete (data safety)
        $tenant->update(['status' => 'suspended']);

        return back()->with('success', 'تم تعليق الشبكة بنجاح');
    }

    public function toggleStatus(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update([
            'status' => $tenant->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'تم تحديث حالة الشبكة');
    }

    public function approve(int $id)
    {
        $tenant = Tenant::with('owner')->findOrFail($id);

        if ($tenant->status !== 'pending') {
            return back()->with('error', 'هذه الشبكة ليست بحالة انتظار');
        }

        DB::beginTransaction();
        try {
            $tenant->update(['status' => 'active']);

            if ($tenant->owner) {
                $tenant->owner->update(['status' => 'active']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء القبول: ' . $e->getMessage());
        }

        // Notify the network admin (outside transaction — non-critical)
        if ($tenant->owner) {
            \App\Models\Notification::withoutGlobalScopes()->create([
                'user_id'   => $tenant->owner->id,
                'tenant_id' => $tenant->id,
                'type'      => 'approval',
                'title'     => 'تم قبول طلب تسجيل شبكتك!',
                'message'   => "مبارك! تم تفعيل شبكة \"{$tenant->name}\". يمكنك الآن تسجيل الدخول.",
                'url'       => '/admin',
                'is_read'   => false,
            ]);
        }

        return back()->with('success', "✓ تم قبول شبكة \"{$tenant->name}\" وتفعيل حساب المشرف");
    }

    public function reject(int $id)
    {
        $tenant = Tenant::with('owner')->findOrFail($id);

        if ($tenant->status !== 'pending') {
            return back()->with('error', 'هذه الشبكة ليست بحالة انتظار');
        }

        DB::beginTransaction();
        try {
            $tenant->update(['status' => 'suspended']);

            if ($tenant->owner) {
                $tenant->owner->update(['status' => 'inactive']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الرفض');
        }

        return back()->with('success', "تم رفض طلب شبكة \"{$tenant->name}\"");
    }
}
