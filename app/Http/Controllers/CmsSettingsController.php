<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsSettingsController extends Controller
{
    public function index()
    {
        return view('admin.cms.settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'site_name'      => 'nullable|string|max:100',
            'site_tagline'   => 'nullable|string|max:191',
            'phone_primary'  => 'nullable|string|max:30',
            'phone_secondary'=> 'nullable|string|max:30',
            'email_contact'  => 'nullable|email|max:191',
            'address'        => 'nullable|string|max:500',
            'whatsapp'       => 'nullable|string|max:30',
            'messenger_url'  => 'nullable|string|max:500',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $old = SiteSetting::get('logo');
            if ($old && !str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->store('cms/settings', 'public');
            SiteSetting::set('logo', $path);
        }

        // Simple text settings
        $textKeys = ['site_name', 'site_tagline', 'phone_primary', 'phone_secondary', 'email_contact', 'address', 'whatsapp', 'messenger_url'];
        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key));
            }
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    public function removeLogo()
    {
        $old = SiteSetting::get('logo');
        if ($old && !str_starts_with($old, 'http')) {
            Storage::disk('public')->delete($old);
        }
        SiteSetting::set('logo', null);
        return back()->with('success', 'Logo eliminado. Se usará el logo de texto.');
    }
}
