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
        $requests = Songs::paginate(6);
        return response()->json($requests);
    }

    public function store(RequestSongRequest $request)
    {
        try {
            $this->songService->createSong($request->get('link'));
            return response()->json(['status' => 'success', 'message' => 'Musica criada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }

    public function show($song_id)
    {
        try {
            $song = Songs::findOrFail($song_id);
            return response()->json(['status' => 'success', 'song' => $song]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }

    public function update(UpdateSongRequest $request, $song_id)
    {
        try {
            $data = $request->only(array_keys($request->rules()));
            Songs::where('id', $song_id)->update($data);
            return response()->json(['status' => 'success', 'message' => 'Musica atualizada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }

    public function destroy($song_id)
    {
        try {
            Songs::delete($song_id);
            return response()->json(['status' => 'success', 'message' => 'Musica apagada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }
}
