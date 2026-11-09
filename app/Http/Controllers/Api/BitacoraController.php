<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Bitacora::with('user')->orderBy('id', 'desc')->get();
    }

    public function show($id)
    {
        // Carga la relación 'user' solo devolviendo 'id', 'name' y 'email'
        $bitacora = Bitacora::with('user:id,name,email')->findOrFail($id);

        return response()->json($bitacora);
    }

}
