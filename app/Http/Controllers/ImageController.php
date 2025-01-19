<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ImageController extends Controller
{
    use AuthorizesRequests;

    public function uploadImage(Request $request)
    {
        $user = Auth::user();
        $this->authorize('editPost', $user);

        try {
            // Check if the 'image' is a base64 string
            if ($request->has('image') && preg_match('/^data:image\/(jpeg|png|jpg|gif|svg);base64,/', $request->image)) {
                // If the image is a base64 string, validate it by converting it into an image
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));

                if (!$imageData) {
                    return response()->json(['error' => 'Invalid base64 string'], 400);
                }

                // Generate a unique name for the image
                $imageName = time() . '.jpg';  // You can dynamically set the extension based on the image type

                // Store the base64 image in the 'public' disk (storage/app/public/images)
                $path = storage_path('app/public/images/' . $imageName);

                // Save the image data as a file
                file_put_contents($path, $imageData);

                // Return the file path
                $appUrl = config('app.url');
                return response()->json([
                    'path' => $appUrl . '/storage/images/' . $imageName
                ]);
            }

            return response()->json(['error' => 'Invalid base64 string'], 400);
        } catch (Exception $e) {
            Log::error('Error uploading image', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'path' => 'No image'
        ]);
    }
}
