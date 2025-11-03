<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HabilitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Datos de ejemplo (temporal)
    $alumnos = [
        (object)['id' => 1, 'nombre' => 'Ana', 'apellido' => 'García López', 'rut' => '12345678'],
        (object)['id' => 2, 'nombre' => 'Juan', 'apellido' => 'Pérez', 'rut' => '87654321'],
    ];

    // Retorna la vista existente y le pasa los alumnos
    return view('actualizar_eliminar', ['alumnos' => $alumnos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
}
