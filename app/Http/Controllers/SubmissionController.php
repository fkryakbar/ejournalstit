<?php

namespace App\Http\Controllers;

use App\Models\FileModel;
use App\Models\JournalModel;
use App\Models\SettingsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function index()
    {
        $settings = SettingsModel::where('id', 1)->firstOrFail();
        return view('submission.submission', [
            'settings' => $settings,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:300'],
            'writer' => ['required', 'max:300'],
            'writer_email' => ['required', 'max:300'],
            'publisher' => ['max:100'],
            "file" => ["required"],
            "file.*" => ["mimes:pdf"],
        ], [
            'title.required' => 'Judul wajib diisi',
            'title.max' => 'Judul Maksimal 300 Karakter',
            'writer.required' => 'Penulis wajib diisi',
            'writer_email.required' => 'Email Penulis wajib diisi',
            'writer.max' => 'Penulis maksimal 100 karakter',
            'publisher.max' => 'Judul Maksimal 100 Karakter',
            'file.required' => 'File wajib diupload',
            'file.*.mimes' => 'File harus format .pdf'
        ]);
        $u_id = Str::uuid();
        foreach ($request->file('file') as $file) {
            $path = $file->store('');
            FileModel::create([
                'file_id' => 'file_' . Str::uuid(),
                'filename' => $file->getClientOriginalName(),
                'journal_id' => $u_id,
                'path' => $path
            ]);
        }
        $request->merge(['u_id' => $u_id, 'visibility' => 'pending']);
        JournalModel::create($request->except(['_token', 'file']));
        return redirect()->to('submission')->with('message', 'Jurnal berhasil disubmit, selanjutnya akan divalidasi oleh Admin');
    }
}
