<?php

namespace App\Http\Controllers;

use App\Models\FileModel;
use App\Models\JournalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index($file_id)
    {
        $is_admin = false;
        if (Auth::check()) {
            if (Auth::user()->role == 'admin') {
                $is_admin = true;
            }
        }
        $file = FileModel::where('file_id', $file_id)->firstOrFail();
        $journal = JournalModel::where('u_id', $file->journal_id)->firstOrFail();
        if ($journal->visibility == 'public' || $is_admin) {
            header("Content-type: application/pdf");
            header('Content-Disposition: inline; filename=' . $file->filename);
            readfile(storage_path('app/public/') .   $file->path);
            return true;
        }
        return view('errors.not_allowed');
    }

    public function delete($file_id)
    {

        $file = FileModel::where('file_id', $file_id)->firstOrFail();
        Storage::delete($file->path);
        FileModel::where('file_id', $file_id)->delete();
        return back()->with('message', 'File berhasil dihapus');
    }
}
