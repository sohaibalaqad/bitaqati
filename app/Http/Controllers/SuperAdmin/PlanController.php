<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('tenants')->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'max_cards'     => 'required|integer|min:0',
            'max_users'     => 'required|integer|min:0',
            'max_packages'  => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',
        ]);

        Plan::create($request->only('name', 'max_cards', 'max_users', 'max_packages', 'price'));

        return redirect()->route('superadmin.plans')
                         ->with('success', 'تم إنشاء الباقة بنجاح');
    }

    public function edit(int $id)
    {
        $plan = Plan::findOrFail($id);
        return view('superadmin.plans.form', compact('plan'));
    }

    public function update(Request $request, int $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'max_cards'    => 'required|integer|min:0',
            'max_users'    => 'required|integer|min:0',
            'max_packages' => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
        ]);

        $plan->update($request->only('name', 'max_cards', 'max_users', 'max_packages', 'price'));

        return redirect()->route('superadmin.plans')
                         ->with('success', 'تم تحديث الباقة بنجاح');
    }

    public function destroy(int $id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->tenants()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف باقة مرتبطة بشبكات. قم بنقل الشبكات أولاً.']);
        }

        $plan->delete();
        return back()->with('success', 'تم حذف الباقة بنجاح');
    }
}
