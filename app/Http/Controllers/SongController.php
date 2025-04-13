<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestSongRequest;
use App\Http\Requests\UpdateSongRequest;
use App\Models\Songs;
use App\Services\SongService;

class SongController extends Controller
{
    public function __construct(private SongService $songService) {}

    public function index()
    {
        $requests = Songs::orderBy('views', 'desc')->paginate(5);
        return response()->json($requests);
    }

    public function store(RequestSongRequest $request)
    {
        $this->songService->createSong($request->get('link'));
        return response()->json(['message' => 'Musica criada com sucesso']);
    }

    public function show($song_id)
    {
        $song = Songs::findOrFail($song_id);
        return response()->json(['song' => $song]);
    }

    public function update(UpdateSongRequest $request, $song_id)
    {
        $data = $request->only(array_keys($request->rules()));
        Songs::where('id', $song_id)->update($data);
        return response()->json(['message' => 'Musica atualizada com sucesso']);
    }

    public function destroy($song_id)
    {
        Songs::where('id', $song_id)->delete();
        return response()->json(['message' => 'Musica apagada com sucesso']);
    }
}
