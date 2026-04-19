<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantRegistrationController extends Controller
{
    public function showForm()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        return view('register.index', compact('plans'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'plan_id'        => 'required|exists:plans,id',
            'network_name'   => 'required|string|max:255',
            'subdomain'      => ['required', 'string', 'max:50', 'unique:tenants,subdomain', 'regex:/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/', new \App\Rules\SubdomainNotReserved()],
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:50|unique:users,phone',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
        ], [
            'plan_id.required'      => 'يرجى اختيار باقة الاشتراك',
            'plan_id.exists'        => 'الباقة المختارة غير صالحة',
            'network_name.required' => 'اسم الشبكة مطلوب',
            'subdomain.required'    => 'الرابط المختصر مطلوب',
            'subdomain.unique'      => 'هذا الرابط مستخدم مسبقاً، اختر رابطاً آخر',
            'subdomain.regex'       => 'الرابط يقبل أحرف إنجليزية صغيرة وأرقام فقط، ولا يبدأ أو ينتهي بشرطة',
            'name.required'         => 'الاسم مطلوب',
            'phone.required'        => 'رقم الهاتف مطلوب',
            'phone.unique'          => 'رقم الهاتف مستخدم من قِبل حساب آخر',
            'email.required'        => 'البريد الإلكتروني مطلوب',
            'email.unique'          => 'البريد الإلكتروني مستخدم من قِبل حساب آخر',
            'password.min'          => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'    => 'تأكيد كلمة المرور غير متطابق',
        ]);

        DB::beginTransaction();
        try {
            // Create tenant in pending state — not accessible until super admin approves
            $tenant = Tenant::create([
                'name'      => $request->network_name,
                'subdomain' => strtolower($request->subdomain),
                'plan_id'   => $request->plan_id,
                'status'    => 'pending',
            ]);

            // Create the network admin with inactive status
            $admin = User::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'name'      => $request->name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'network_admin',
                'balance'   => 0,
                'status'    => 'inactive',
            ]);

            $tenant->update(['owner_id' => $admin->id]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Tenant registration failed', [
                'subdomain' => $request->subdomain,
                'email'     => $request->email,
                'error'     => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'حدث خطأ أثناء التسجيل، يرجى المحاولة مجدداً');
        }

        // Notify all super admins (outside transaction — non-critical)
        User::withoutGlobalScopes()
            ->where('role', 'super_admin')
            ->pluck('id')
            ->each(function ($superAdminId) use ($tenant, $admin) {
                Notification::send(
                    $superAdminId,
                    'registration',
                    'طلب تسجيل شبكة جديدة',
                    "{$admin->name} طلب تسجيل شبكة \"{$tenant->name}\" — بانتظار الموافقة",
                    route('superadmin.tenants')
                );
            });

        return redirect()->route('register.pending', ['subdomain' => $tenant->subdomain]);
    }

    public function pending(Request $request)
    {
        $subdomain = $request->query('subdomain');
        $tenant    = $subdomain ? Tenant::where('subdomain', $subdomain)->first() : null;

        return view('register.pending', compact('tenant'));
    }
}
