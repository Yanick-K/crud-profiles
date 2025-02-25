<?php

namespace App\Services;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ImageUploadService
{
    protected string $disk;

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }

    /**
     * @return array<string, int|bool|string>
     */
    public function upload(UploadedFile $file, string $directory = 'uploads'): array
    {
            $path = $file->store($directory, $this->disk);

            if (!$path) {
                return [
                    'success' => false,
                    'message' => 'Image upload failed',
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
            }

            return [
                'success' => true,
                'path' => $path,
                'message' => 'Image uploaded successfully',
                'status' => Response::HTTP_CREATED,
            ];
    }

    public function handleImageUpload(StoreProfileRequest|UpdateProfileRequest $request, array &$validated, string $fieldName = 'image', string $directory = 'uploads') : ?JsonResponse
    {
        if ($request->hasFile($fieldName)) {
            $uploadResult = $this->upload($request->file($fieldName), $directory);

            if (!$uploadResult['success']) {
                return response()->json(['message' => $uploadResult['message']], $uploadResult['status']);
            }

            $validated[$fieldName] = $uploadResult['path'];
        }
        return null;
    }

}
