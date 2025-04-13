<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestSongRequest;
use App\Models\Request;
use App\Services\SongService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function __construct(private SongService $songService) {}

    public function index()
    {
        $requests = Request::orderBy('created_at', 'asc')->paginate(5);
        return response()->json($requests);
    }

    public function send(RequestSongRequest $request)
    {
        Request::create(['user_id' => Auth::id(), 'link' => $request->get('link')]);
        return response()->json(['message' => 'request enviada com sucesso']);
    }

    public function acceptRequest($request_id)
    {
        DB::transaction(function () use ($request_id) {
            $request = Request::where('id',$request_id)->first();
            $request->update(['approved' => true, 'admin_id' => Auth::id()]);
            $this->songService->createSong($request->link, $request->user_id);
            $request->delete();
        });
        return response()->json(['message' => 'request aprovada com sucesso']);
    }

    public function refuseRequest($request_id)
    {
        Request::where('id',$request_id)->update(['admin_id' => Auth::id(), 'deleted_at' => now()]);
        return response()->json(['message' => 'request recusada com sucesso']);
    }
}
