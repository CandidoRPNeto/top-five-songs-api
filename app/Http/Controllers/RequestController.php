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
        $requests = Request::paginate(6);
        return response()->json($requests);
    }

    public function send(RequestSongRequest $request)
    {
        try {
            Request::create(['user_id' => Auth::id(), 'link' => $request->get('link')]);
            return response()->json(['status' => 'success', 'message' => 'request enviada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }

    public function acceptRequest($request_id)
    {
        try {
            DB::transaction(function () use ($request_id) {
                $request = Request::where('id',$request_id)->first();
                $request->update(['approved' => true, 'admin_id' => Auth::id(), 'deleted_at' => now()]);
                $this->songService->createSong($request->link, $request->user_id);
            });
            return response()->json(['status' => 'success', 'message' => 'request aprovada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }

    public function refuseRequest($request_id)
    {
        try {
            DB::transaction(function () use ($request_id) {
                Request::where('id',$request_id)->update(['admin_id' => Auth::id(), 'deleted_at' => now()]);
            });
            return response()->json(['status' => 'success', 'message' => 'request recusada com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }
}
