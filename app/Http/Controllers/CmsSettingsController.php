<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Services\SiteSettingService;
use Illuminate\Http\Request;

class CmsSettingsController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private SiteSettingService $settings) {}

    public function index()
    {
        return view('admin.cms.settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'site_name' => 'nullable|string|max:100',
            'site_tagline' => 'nullable|string|max:191',
            'phone_primary' => 'nullable|string|max:30',
            'phone_secondary' => 'nullable|string|max:30',
            'email_contact' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:30',
            'messenger_url' => 'nullable|string|max:500',
            'session_timeout_minutes' => 'nullable|integer|min:5|max:480',
        ]);

        try {
            if ($request->hasFile('logo')) {
                $this->deleteFile($this->settings->get('logo'));
                $path = $this->saveFile($request->file('logo'), 'cms/settings');
                $this->settings->updateLogo($path);
            }

            $textKeys = ['site_name', 'site_tagline', 'phone_primary', 'phone_secondary', 'email_contact', 'address', 'whatsapp', 'messenger_url'];
            $values = [];
            foreach ($textKeys as $key) {
                if ($request->has($key)) {
                    $values[$key] = $request->input($key);
                }
            }
            $this->settings->updateTextSettings($values);

            if ($request->has('session_timeout_minutes')) {
                $this->settings->updateSessionTimeout((int) $request->input('session_timeout_minutes', 15));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar la configuración. Inténtalo de nuevo.');
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    public function removeLogo()
    {
        $this->deleteFile($this->settings->get('logo'));
        $this->settings->removeLogo();

        return back()->with('success', 'Logo eliminado. Se usará el logo de texto.');
    }
}
