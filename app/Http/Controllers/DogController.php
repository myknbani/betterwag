<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDogRequest;
use App\Http\Requests\UpdateDogRequest;
use App\Models\Dog;
use App\Models\Shelter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class DogController extends Controller
{
    public function index(Shelter $shelter): AnonymousResourceCollection
    {
        return $shelter
            ->dogs()
            ->orderByDesc('is_urgent')
            ->orderBy('name', 'asc')
            ->get()
            ->toResourceCollection();
    }

    public function store(CreateDogRequest $request, Shelter $shelter): JsonResource
    {
        return Dog::create([...$request->validated(), 'shelter_id' => $shelter->id])
            ->toResource();
    }

    public function show(Dog $dog): JsonResource
    {
        return $dog->toResource();
    }

    public function update(UpdateDogRequest $request, Dog $dog): JsonResource
    {
        $dog->update($request->validated());

        return $dog->refresh()->toResource();
    }

    public function destroy(Dog $dog): Response
    {
        $this->authorize('delete', $dog);
        $dog->delete();

        return response()->noContent();
    }
}
