<?php

namespace App\Http\Controllers\api;

use App\Events\Chat\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\MensagensModel;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class MensagensController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = $request->input();
        $dados['mensagem'] = strip_tags($dados['mensagem']);
        $dados['de'] = Auth::user()->id;

        $newMesage = DB::transaction(function() use($dados){
            return MensagensModel::create($dados);
        });

        if($newMesage){
            Event::dispatch(new SendMessage($newMesage, $newMesage->para));
            return response()->noContent(Response::HTTP_CREATED);
        }else {
            return response()->noContent(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function listMensages(User $user)
    {
        $de = Auth::user();

        $mensagens = MensagensModel::where(function($q) use($user, $de) {
            $q->where('de', $de->id);
            $q->where('para', $user->id);
        })->orWhere(function($q)  use($user, $de){
            $q->where('de', $user->id);
            $q->where('para', $de->id);
        })
        ->orderBy('created_at', 'ASC')
        ->get();

        return response()->json(['msgs' => $mensagens], Response::HTTP_OK);
    }
}
