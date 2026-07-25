<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\AuditLogger;

class SiteSettingService
{
    public function get(string $key, ?string $default = null): ?string
    {
        return SiteSetting::get($key, $default);
    }

    public function set(string $key, ?string $value): void
    {
        SiteSetting::set($key, $value);
        AuditLogger::record("setting.update:{$key}");
    }

    public function fileUrl(string $key): ?string
    {
        return SiteSetting::fileUrl($key);
    }

    public function homepageSettings(): array
    {
        return [
            'name' => $this->get('site_name', 'Golden Cabo Transportation'),
            'tagline' => $this->get('site_tagline', 'Traslados privados de lujo'),
            'phone_primary' => $this->get('phone_primary', '(+52) 333 303 4455'),
            'phone_secondary' => $this->get('phone_secondary', '(+52) 624 121 6527'),
            'email' => $this->get('email_contact', 'goldencabotransportation@gmail.com'),
            'address' => $this->get('address', 'Calle Huanacastle Esq. Eucalipto Mza 70 lte 1, Col. Las Veredas, CP 23436, San José del Cabo, BCS'),
            'whatsapp' => $this->get('whatsapp', '+523333034455'),
            'messenger_url' => $this->get('messenger_url', ''),
            'logo' => $this->fileUrl('logo'),
        ];
    }

    public function updateTextSettings(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function updateLogo(string $path): void
    {
        $this->set('logo', $path);
    }

    public function removeLogo(): void
    {
        $this->set('logo', null);
    }

    public function updateSessionTimeout(int $minutes): void
    {
        $this->set('session_timeout_minutes', (string) max(5, min(480, $minutes)));
    }

    public function sessionTimeoutMinutes(): int
    {
        return (int) $this->get('session_timeout_minutes', '15');
    }
}
