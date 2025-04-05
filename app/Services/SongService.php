<?php

namespace App\Services;

use App\Actions\GetParametersFromLink;
use App\Models\Songs;
use Illuminate\Support\Facades\Auth;

class SongService
{
    public function createSong($link, $user_id = null)
    {
        if(is_null($user_id)){
            $user_id = Auth::id();
        }
        $songData =  GetParametersFromLink::execute($link);
        Songs::create([
            'user_id' => $user_id,
            'title' => $songData['title'],
            'views' => $songData['views'],
            'youtube_id' => $songData['youtube_id'],
            'thumb' => $songData['thumb']
        ]);
    }
}
