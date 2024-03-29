<?php

namespace App\Http\Repositories\Main;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

interface ImageRepositoryInterface
{
    public function upload(UploadedFile $image, string $directory): string;
}

/**
 * 2024-02-04 || @Andikss
 * ==============================================================================
 * I'm using Intervention Image  V.2.7 here. You could use any other version, 
 * but I suggest using 2.7 anyway. Learn how to install it on Laravel bellow 
 * => https://image.intervention.io/v3/introduction/frameworks
*/

class ImageRepository implements ImageRepositoryInterface
{
    public function upload(UploadedFile $image, string $directory): string
    {
        if ($image) {
            try {
                # Generate a unique filename for the compressed image
                $filename = uniqid() . Str::random(10) . '.webp';

                # Compress the image using Intervention Image
                $compressedImage = Image::make($image->getRealPath())
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 80);

                File::makeDirectory($directory, 0777, true, true);
                $filePath = $directory . '/' . $filename;
                $compressedImage->save($filePath);

                return $filePath;
            } catch (Exception $e) {
                Log::critical('Image Upload : ' . $e->getMessage());
                throw new Exception('Failed to save image: ' . $e->getMessage());
            }
        }
    }
}
