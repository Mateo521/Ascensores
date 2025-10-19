<?php
// app/Http/Controllers/ConfiguracionController.php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $empresa = Setting::getValue('empresa', [
            'nombre' => '', 'direccion' => '', 'telefono' => '', 'email' => ''
        ]);
        $app = Setting::getValue('app', [
            'offline_enabled' => true,
            'checklist_categories' => [],
            'pdf_footer' => '',
        ]);

        return Inertia::render('Configuracion/Index', [
            'empresa' => $empresa,
            'app' => $app,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'empresa' => 'required|array',
            'empresa.nombre' => 'required|string|max:190',
            'empresa.direccion' => 'nullable|string|max:190',
            'empresa.telefono' => 'nullable|string|max:50',
            'empresa.email' => 'nullable|email|max:190',
            'app' => 'required|array',
            'app.offline_enabled' => 'boolean',
            'app.checklist_categories' => 'array',
            'app.checklist_categories.*' => 'string',
            'app.pdf_footer' => 'nullable|string|max:255',
        ]);

        Setting::setValue('empresa', $data['empresa']);
        Setting::setValue('app', $data['app']);

        return back()->with('success', 'Configuración guardada.');
    }
}
