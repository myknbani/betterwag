<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use App\Models\Shelter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ShelterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $shelters = Shelter::orderBy('name', 'asc')->get();

        return response()->json($shelters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateShelterRequest $request): JsonResponse
    {
        $shelter = Shelter::create($request->validated());

        return response()->json($shelter, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shelter $shelter): JsonResponse
    {
        return response()->json($shelter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShelterRequest $request, Shelter $shelter): JsonResponse
    {
        $shelter->update($request->validated());

        return response()->json($shelter->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shelter $shelter): Response
    {
        $shelter->delete();

        return response()->noContent();
    }
}
