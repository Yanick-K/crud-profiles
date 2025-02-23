<?php

namespace App\Services;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;

class ImageUploadService
{
    protected string $disk;

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }

    /**
     * Handle image upload.
     */
    public function upload(UploadedFile $file, string $directory = 'uploads'): array
    {
        try {
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

        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An unexpected error occurred during image upload',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }

    /**
     * Process image upload from request and update validated data
     */
    public function handleImageUpload(StoreProfileRequest|UpdateProfileRequest $request, array &$validated, string $fieldName = 'image', string $directory = 'uploads')
    {
        if ($request->hasFile($fieldName)) {
            $uploadResult = $this->upload($request->file($fieldName), $directory);

            if (!$uploadResult['success']) {
                return response()->json(['message' => $uploadResult['message']], $uploadResult['status']);
            }

            $validated[$fieldName] = $uploadResult['path'];
        }
    }

}
