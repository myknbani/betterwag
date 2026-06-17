<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDogRequest;
use App\Http\Requests\UpdateDogRequest;
use App\Models\Dog;
use Illuminate\Http\Response;

class DogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dogs = Dog::orderByDesc('is_urgent')->orderBy('name')->get();

        return response()->json($dogs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDogRequest $request)
    {
        $dog = Dog::create($request->validated());

        return response()->json($dog, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dog $dog)
    {
        return response()->json($dog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDogRequest $request, Dog $dog)
    {
        $dog->update($request->validated());

        return response()->json($dog->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dog $dog)
    {
        $dog->delete();

        return response()->noContent();
    }
}
