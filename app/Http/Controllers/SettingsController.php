<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const APPLICATION_KEYS = [
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
    ];

    public function index(Request $request): View
    {
        $application = [];
        foreach (self::APPLICATION_KEYS as $key) {
            $application[$key] = Setting::get($key, '');
        }

        return view('settings.index', [
            'user' => $request->user(),
            'application' => $application,
        ]);
    }

    public function updateApplication(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone' => ['nullable', 'string', 'max:80'],
            'company_email' => ['nullable', 'email', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value !== null && $value !== '' ? $value : null);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Paramètres entreprise enregistrés.');
    }
}
