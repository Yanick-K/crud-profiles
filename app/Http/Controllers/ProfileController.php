<?php

namespace App\Http\Controllers;

use App\Enums\ProfileStatus;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    protected ImageUploadService $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ProfileResource::collection(Profile::where('status', ProfileStatus::ACTIVE)->get());
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Create');
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $this->imageUploadService->handleImageUpload($request, $validated);
            $profile = Profile::create($validated);
            return response()->json(['message' => 'Profile created successfully.', 'data' => new ProfileResource($profile)], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Profile $profile): ProfileResource
    {
        return new ProfileResource($profile);
    }

    public function edit(Profile $profile): \Inertia\Response
    {
        return Inertia::render('Edit', ['profile' => new ProfileResource($profile), 'storageUrl' => asset('storage')]);
    }

    public function update(UpdateProfileRequest $request, Profile $profile): JsonResponse
    {
        $validated = $request->validated();
        try {
            if ($request->action === 'update') {
                $this->imageUploadService->handleImageUpload($request, $validated);
                $profile->update($validated);
                return response()->json(['message' => 'Profile updated successfully.', 'data' => new ProfileResource($profile)], Response::HTTP_OK);
            } else {
                $profile->delete();
                return response()->json(['message' => 'Profile deleted successfully.']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Profile $profile): JsonResponse
    {
        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully.']);
    }
}
