<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use App\Models\Shelter;

class ShelterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shelters = Shelter::orderBy('name', 'asc')->get();

        return response()->json($shelters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateShelterRequest $request)
    {
        $shelter = Shelter::create($request->validated());

        return response()->json($shelter, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shelter $shelter)
    {
        return response()->json($shelter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        $shelter->update($request->validated());

        return response()->json($shelter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shelter $shelter)
    {
        $shelter->delete();

        return response()->noContent();
    }
}
