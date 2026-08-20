<?php

namespace App\Http\Controllers\Api;

use App\Actions\Applications\CreateReverbApplication;
use App\Actions\Applications\DeleteReverbApplication;
use App\Actions\Applications\RotateReverbApplicationCredentials;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreApplicationRequest;
use App\Http\Requests\Api\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\ReverbApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApplicationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ApplicationResource::collection(
            ReverbApplication::query()->latest()->get(),
        );
    }

    public function store(
        StoreApplicationRequest $request,
        CreateReverbApplication $createReverbApplication,
    ): JsonResponse {
        $validated = $request->validated();

        $result = $createReverbApplication->handle(
            $validated['name'],
            $validated['allowed_origins'],
        );

        return response()->json(
            ApplicationResource::withSecret(
                $result['application'],
                $result['credentials']['secret'],
            ),
            201,
        );
    }

    public function update(
        UpdateApplicationRequest $request,
        ReverbApplication $application,
    ): ApplicationResource {
        $application->enabled = $request->boolean('enabled');
        $application->save();

        return new ApplicationResource($application->refresh());
    }

    public function rotate(
        ReverbApplication $application,
        RotateReverbApplicationCredentials $rotateReverbApplicationCredentials,
    ): JsonResponse {
        $result = $rotateReverbApplicationCredentials->handle($application);

        return response()->json(
            ApplicationResource::withSecret(
                $result['application'],
                $result['credentials']['secret'],
            ),
        );
    }

    public function destroy(
        ReverbApplication $application,
        DeleteReverbApplication $deleteReverbApplication,
    ): JsonResponse {
        $deleteReverbApplication->handle($application);

        return response()->json(null, 204);
    }
}
