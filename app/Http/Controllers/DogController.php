<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDogRequest;
use App\Http\Requests\UpdateDogRequest;
use App\Models\Dog;
use App\Models\Shelter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Shelter $shelter): JsonResponse
    {
        $dogs = Dog::orderByDesc('is_urgent')->orderBy('name', 'asc')->get();

        return response()->json($shelter->dogs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDogRequest $request, Shelter $shelter): JsonResponse
    {
        $dog = Dog::create([...$request->validated(), 'shelter_id' => $shelter->id]);

        return response()->json($dog, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dog $dog): JsonResponse
    {
        return response()->json($dog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDogRequest $request, Dog $dog): JsonResponse
    {
        $dog->update($request->validated());

        return response()->json($dog->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dog $dog): Response
    {
        $dog->delete();

        return response()->noContent();
    }
}
