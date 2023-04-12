<?php

namespace App\Http\Controllers;

use App\Models\FileModel;
use App\Models\JournalModel;
use App\Models\SettingsModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $settings = SettingsModel::where('id', 1)->firstOrFail();
        $journals = JournalModel::where('visibility', 'public')->orWhere('visibility', 'restricted')->orderBy('id', 'DESC')->paginate(5);
        if ($request->search) {
            $journals = JournalModel::where('title', 'LIKE', "%{$request->search}%")->orWhere('abstract', 'LIKE', "%{$request->search}%")->orWhere('writer', 'LIKE', "%{$request->search}%")->orWhere('u_id', 'LIKE', "%{$request->search}%")->orWhere('publisher', 'LIKE', "%{$request->search}%")->where(function (Builder $query) {
                return $query->where('visibility', 'public')->orWhere('visibility', 'restricted');
            })->orderby('id', 'DESC')->paginate(5);
        }
        return view('landing_page.welcome', [
            'journals' => $journals,
            'settings' => $settings
        ]);
    }

    public function detail($journal_id)
    {
        $journal = JournalModel::where('u_id', $journal_id)->firstOrFail();
        $files = FileModel::where('journal_id', $journal_id)->get();
        $others = JournalModel::inRandomOrder()->limit(5)->get();
        $settings = SettingsModel::where('id', 1)->firstOrFail();
        return view('landing_page.detail', [
            'files' => $files,
            'journal' => $journal,
            'others' => $others,
            'settings' => $settings
        ]);
    }
}
