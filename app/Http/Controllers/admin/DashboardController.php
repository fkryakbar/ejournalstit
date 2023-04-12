<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\FileModel;
use App\Models\JournalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $journals = JournalModel::all();
        return view('dashboard.dashboard', [
            'journals' => $journals
        ]);
    }


    public function add()
    {
        return view('dashboard.add');
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
        $request->merge(['u_id' => $u_id]);
        JournalModel::create($request->except(['_token', 'file']));
        return redirect()->to('admin/dashboard')->with('message', 'Jurnal berhasil disubmit');
    }

    public function delete($journal_id)
    {
        JournalModel::where('u_id', $journal_id)->firstOrFail();
        $files = FileModel::where('journal_id', $journal_id)->get();
        foreach ($files as $file) {
            Storage::delete($file->path);
        }
        JournalModel::where('u_id', $journal_id)->delete();
        FileModel::where('journal_id', $journal_id)->delete();

        return redirect('admin/dashboard')->with('message', 'Jurnal berhasil dihapus');
    }

    public function edit($journal_id)
    {
        $journal = JournalModel::where('u_id', $journal_id)->firstOrFail();
        $files = FileModel::where('journal_id', $journal_id)->get();
        return view('dashboard.edit', [
            'journal' => $journal,
            'files' => $files
        ]);
    }


    public function update(Request $request, $journal_id)
    {
        $request->validate([
            'title' => ['required', 'max:300'],
            'writer' => ['required', 'max:300'],
            'writer_email' => ['required', 'max:300'],
            'publisher' => ['max:100'],
        ], [
            'title.required' => 'Judul wajib diisi',
            'title.max' => 'Judul Maksimal 300 Karakter',
            'writer.required' => 'Penulis wajib diisi',
            'writer_email.required' => 'Email Penulis wajib diisi',
            'writer.max' => 'Penulis maksimal 100 karakter',
            'publisher.max' => 'Judul Maksimal 100 Karakter',
        ]);

        if ($request->file('file')) {
            foreach ($request->file('file') as $file) {
                $path = $file->store('');
                FileModel::create([
                    'file_id' => 'file_' . Str::uuid(),
                    'filename' => $file->getClientOriginalName(),
                    'journal_id' => $journal_id,
                    'path' => $path
                ]);
            }
        }

        JournalModel::where('u_id', $journal_id)->update($request->except(['_token', 'file']));
        return back()->with('message', 'Jurnal berhasil disimpan');
    }
}
