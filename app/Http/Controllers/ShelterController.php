<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use App\Models\Shelter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ShelterController extends Controller
{
    public function index(): ResourceCollection
    {
        return Shelter::orderBy('name', 'asc')->get()->toResourceCollection();
    }

    public function store(CreateShelterRequest $request): JsonResource
    {
        return Shelter::create($request->validated())->toResource();
    }

    public function show(Shelter $shelter): JsonResource
    {
        return $shelter->toResource();
    }

    public function update(UpdateShelterRequest $request, Shelter $shelter): JsonResource
    {
        $shelter->update($request->validated());

        return $shelter->refresh()->toResource();
    }

    public function destroy(Shelter $shelter): Response
    {
        $this->authorize('delete', $shelter);
        $shelter->delete();

        return response()->noContent();
    }
}
