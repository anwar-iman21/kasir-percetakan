<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = InvoiceSetting::getSetting();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
        ]);

        $setting = InvoiceSetting::getSetting();
        $setting->update([
            'business_name' => $request->business_name,
            'business_tagline' => $request->business_tagline,
            'address' => $request->address,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'invoice_prefix' => $request->invoice_prefix ?? 'INV',
            'footer_text' => $request->footer_text,
            'custom_notes' => $request->custom_notes,
            'tax_enabled' => $request->boolean('tax_enabled'),
            'tax_percent' => $request->tax_percent ?? 0,
            'primary_color' => $request->primary_color ?? '#0d6efd',
            'font_size' => $request->font_size ?? '12',
            'paper_size' => $request->paper_size ?? 'a4',
            'show_qr' => $request->boolean('show_qr'),
            'dark_mode' => $request->boolean('dark_mode'),
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = InvoiceSetting::getSetting();

        if ($setting->logo) {
            Storage::disk('public')->delete($setting->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $setting->update(['logo' => $path]);

        return redirect()->back()->with('success', 'Logo berhasil diupload.');
    }
}
