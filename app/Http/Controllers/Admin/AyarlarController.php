<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Setting;

class AyarlarController extends AdminController
{
    public function index(Request $request)
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.ayarlar.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.ayarlar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar oluşturuldu!');
    }

    public function show($id)
    {
        $setting = Setting::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($setting);
        }

        return view('admin.ayarlar.show', compact('setting'));
    }

    public function edit($id)
    {
        $ayar = Setting::findOrFail($id);
        return view('admin.ayarlar.edit', compact('ayar'));
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $validated = $request->validate([
            'value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar güncellendi!');
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar silindi!');
    }
}
