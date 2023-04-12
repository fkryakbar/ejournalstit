<?php

namespace App\Http\Controllers;

use App\Models\SettingsModel;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SettingsModel::where('id', 1)->firstOrFail();
        return view('dashboard.settings', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:200'],
            'allow_to_submit' => ['required']
        ], [
            'title.required' => 'Judul Wajib diisi',
            'title.max' => 'Maksimal judul 200 karakter',
        ]);
        SettingsModel::where('id', 1)->firstOrFail()->update($request->except('_token'));
        return back()->with('message', 'Pengaturan berhasil disimpan');
    }
}
