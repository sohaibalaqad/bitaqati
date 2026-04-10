<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller {
    public function index() {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request) {
        $validCodes = array_keys(\App\Helpers\CurrencyHelper::CURRENCIES);

        $request->validate([
            'network_name'    => 'nullable|string|max:255',
            'mikrotik_url'    => 'nullable|url|max:500',
            'dealer_url'      => 'nullable|url|max:500',
            'support_phone'   => 'nullable|string|max:50',
            'currency'        => ['nullable', 'string', \Illuminate\Validation\Rule::in($validCodes)],
        ]);

        $fields = ['network_name','mikrotik_url','dealer_url','support_phone','currency'];
        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''));
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
