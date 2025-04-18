<?php

use App\Track;

Route::get('/new-music/{count?}', function ($count = 100) {
    return Track::select([
        'id', 'name', 'preview_id', 'release_id', 'streamable_id', 'slug', 'status',
        'uploaded_by', 'created_at', 'asset_id', 'length', 'price'
    ])
        ->with([
            'preview' => function ($query) {
                $query->select('id', 'created_at');
            },
            'release' => function ($query) {
                $query->select([
                    'id', 'name', 'class', 'status', 'image_id', 'uploaded_by', 'created_at', 'release_date'
                ]);
            },
            'release.uploader' => function ($query) {
                $query->select('id', 'name', 'path');
            },
            'release.image',
            'artist',
        ])
        ->whereHas('release', function($query) {
                    $query->statuslive();
            })
        ->withCount([
            'comments as comments_count', 'likes as likes_count', 'shares as shares_count'
        ])
        ->where('status', 'approved')
        ->take($count)
        ->orderByDesc('created_at')
        ->paginate(15);
});
