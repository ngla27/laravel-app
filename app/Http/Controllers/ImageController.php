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
            if ($request->has('media') && preg_match('/^data:image\/(jpeg|png|jpg|gif|svg);base64,/', $request->media)) {
                // If the image is a base64 string, validate it by converting it into an image
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->media));

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

    public function uploadVideo(Request $request)
    {
        $user = Auth::user();
        $this->authorize('editPost', $user);

        try {
            // Check if the 'video' is a base64 string
            if ($request->has('media') && preg_match('/^data:video\/(mp4|webm|ogg);base64,/', $request->media)) {
                // If the video is a base64 string, validate it by converting it into a video file
                $videoData = base64_decode(preg_replace('#^data:video/\w+;base64,#i', '', $request->media));

                if (!$videoData) {
                    return response()->json(['error' => 'Invalid base64 string'], 400);
                }

                // Get the MIME type of the video (mp4, webm, etc.)
                preg_match('#^data:video/(mp4|webm|ogg);base64,#i', $request->media, $matches);
                $mimeType = $matches[1] ?? 'mp4';  // Default to 'mp4' if MIME type is not found

                // Set file extension based on MIME type
                $extension = '';
                switch ($mimeType) {
                    case 'mp4':
                        $extension = 'mp4';
                        break;
                    case 'webm':
                        $extension = 'webm';
                        break;
                    case 'ogg':
                        $extension = 'ogg';
                        break;
                    default:
                        return response()->json(['error' => 'Unsupported video type'], 400);
                }

                // Generate a unique name for the video
                $videoName = time() . '.' . $extension;  // Use the MIME type extension

                // Store the video in the 'public' disk (storage/app/public/videos)
                $path = storage_path('app/public/videos/' . $videoName);

                // Save the video data as a file
                file_put_contents($path, $videoData);

                // Return the file path (public URL) for the video
                $appUrl = config('app.url');
                return response()->json([
                    'path' => $appUrl . '/storage/videos/' . $videoName
                ]);
            }

            return response()->json(['error' => 'Invalid base64 string or unsupported format'], 400);
        } catch (Exception $e) {
            Log::error('Error uploading video', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'path' => 'No video uploaded'
        ]);
    }
}
