<?php

namespace App\Http\Controllers\Account;

use App\Release;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyReleases extends Controller
{
    public function index()
    {
        return Release::where('uploaded_by', auth()->id())
            // ->where(function ($query) {
            //     $query->where('status', 'live');
            //     $query->where('release_date', '<=', date('Y-m-d'));
            // })
            // ->orWhere('status', '!=', 'live')
            ->with([
                'image',
                'tracks',
                'tracks.artist',
                'genres',
                'tracks.preview',
                'tracks.release',
                'uploader' => function ($query) {
                    $query->select(['id', 'name', 'path']);
                }
            ])
            ->latest('release_date')
            ->paginate(10);
    }

    public function update(Request $request, $release)
    {
        $release = Release::findOrFail($release);
        $release->status = $request->status;
        $release->save();

        return [
            'type' => 'success',
            'message' => "The Release status has been changed to $request->status",
        ];
    }

    public function destroy(Request $request, $release)
    {
        $release = Release::findOrFail($release);
        $release->delete();

        return [
            'type' => 'success',
            'message' => 'Release has been deleted',
        ];
    }
}
