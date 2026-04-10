<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount(['cards as available_count' => fn($q) => $q->where('status', 'available')])->get();
        return view('admin.packages', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'speed' => 'nullable|string',
            'duration' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ]);

        Package::create($request->only(['name', 'speed', 'duration', 'price', 'cost']));
        return back()->with('success', 'تم إضافة الباقة بنجاح');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ]);

        Package::findOrFail($id)->update($request->only(['name', 'speed', 'duration', 'price', 'cost']));
        return back()->with('success', 'تم تحديث الباقة');
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        if ($package->availableCards()->exists()) {
            return back()->with('error', 'لا يمكن حذف الباقة لأنها تحتوي على بطاقات متوفرة. احذف البطاقات أولاً أو انقلها لباقة أخرى.');
        }

        $package->delete();
        return back()->with('success', 'تم حذف الباقة');
    }
}
