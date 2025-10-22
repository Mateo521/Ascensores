<?php
// app/Http/Controllers/ConfiguracionController.php

namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $empresa = Setting::getValue('empresa', []);
        if (!empty($empresa['logo_path'])) {
            $empresa['logo_url'] = asset('storage/'.$empresa['logo_path']);
        }

        $app = Setting::getValue('app', [
            'offline_enabled' => true,
            'checklist_categories' => [],
            'pdf_footer' => '',
        ]);

        return Inertia::render('Configuration/Index', [
            'empresa' => $empresa,
            'app' => $app,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'empresa' => 'required|array',
            'empresa.nombre' => 'required|string|max:190',
            'empresa.cuit' => 'nullable|string|max:50',
            'empresa.inicio_actividad' => 'nullable|date',
            'empresa.telefono' => 'nullable|string|max:50',
            'empresa.email' => 'nullable|email|max:190',
            'empresa.direccion' => 'nullable|string|max:190',
            'logo' => 'nullable|image|max:2048',
            'app' => 'required|array',
            'app.offline_enabled' => 'boolean',
            'app.checklist_categories' => 'array',
            'app.checklist_categories.*' => 'string',
            'app.pdf_footer' => 'nullable|string|max:255',
        ]);

        $empresa = Setting::getValue('empresa', []);
        $empresa = array_merge($empresa, $data['empresa']);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $empresa['logo_path'] = $path;
        }

        Setting::setValue('empresa', $empresa);
        Setting::setValue('app', $data['app']);

        return back()->with('success', 'Configuración guardada.');
    }
}