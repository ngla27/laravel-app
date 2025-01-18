<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MetaController extends Controller
{
    protected $openAIService;
    use AuthorizesRequests;

    // Inject the OpenAIService into the controller
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Generate the meta information for an article.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateMeta(Request $request)
    {
        $user = Auth::user();
        $this->authorize('editPost', $user);

        // Validate incoming request data (optional but recommended)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);

        // Call the OpenAI service to generate meta information
        $metaInfo = $this->openAIService->generateMetaInfo($validated['title'], $validated['description']);

        // Return the meta information as a JSON response
        return response()->json($metaInfo);
    }
}