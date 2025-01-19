<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * HomePage: Get latest versions of posts with published status: start_timestamp
     */
    public function index()
    {
        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, datetime(start_timestamp, 'unixepoch') as published_at
            FROM posts
            JOIN ( 
                SELECT *, MAX(created_at) as created_at
                FROM post_versions
                WHERE start_timestamp <= :currentTimestamp
                GROUP BY post_id 
            ) AS latest_versions
            ON posts.id = latest_versions.post_id 
            AND posts.status = :status
            ORDER BY latest_versions.created_at DESC";

        $posts = collect(DB::select($query, [
            'status' => 'published',
            'currentTimestamp' => Carbon::now()->timestamp,
        ]));
        
        // Pass the posts data to the view
        return view('home', compact('posts'));
    }

    /**
     * HomePage: Get latest version of a single post with published status
     */
    public function showPost($postVersionId)
    {
        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, datetime(start_timestamp, 'unixepoch') as published_at
            FROM posts
            JOIN ( 
                SELECT *, MAX(created_at) as created_at
                FROM post_versions
                WHERE start_timestamp <= :currentTimestamp
                GROUP BY post_id 
            ) AS latest_versions
            ON posts.id = latest_versions.post_id 
            AND posts.status = :status
            AND latest_versions.post_id = :postVersionId
            LIMIT 1";

        $post = collect(DB::select($query,[
            'status' => 'published',
            'currentTimestamp' => Carbon::now()->timestamp,
            'postVersionId' => $postVersionId
        ]))->first();

        return view('post.showPost', compact('post'));
    }

    /**
     * Backoffice: Get latest versions of all posts: created_at
     */
    public function listPosts()
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }

        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, latest_versions.created_at as published_at, 
            datetime(start_timestamp, 'unixepoch') as start_timestamp
            FROM posts
            JOIN ( 
                SELECT post_versions.*, MAX(created_at) as created_at
                FROM post_versions
                GROUP BY post_id
            ) AS latest_versions
            ON posts.id = latest_versions.post_id
            ORDER BY latest_versions.created_at DESC";
        $posts = collect(DB::select($query));
        
        // Pass the posts data to the view
        return $posts;
    }

    public function showCreatePost()
	{
        $user = Auth::user();
        $this->authorize('createPost', $user);
        
		return view('post.editPost');
	}

    public function createPost(Request $request)
	{
        $user = Auth::user();
        $this->authorize('createPost', $user);

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|min:5|string',
            'start_timestamp' => 'required|date',
            'meta_title' => 'required|string|min:5|max:255',
            'meta_description' => 'required|min:5|string',
            'keywords' => 'required|string|min:3|max:255'
        ]);
        
        // creating post
        $postCreated = Post::create([
            'status' => 'draft',
            'authored_by' => $user->email
        ]);
        // creating post version
        PostVersion::create([
            'post_id' => $postCreated->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'keywords' => $validated['keywords'],
            'edited_by' => $user->email,
            'start_timestamp' => Carbon::parse($validated['start_timestamp'])->timestamp
        ]);

        return redirect()->route('show.editPost', ['id' => $postCreated->id])
                         ->with('success', 'Post created successfully!');
	}

    public function editPost(Request $request, $postVersionId) {
        $user = Auth::user();
        $this->authorize('editPost', $user);
        
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|min:5|string',
            'start_timestamp' => 'required|date',
            'meta_title' => 'required|string|min:5|max:255',
            'meta_description' => 'required|min:5|string',
            'keywords' => 'required|string|min:3|max:255'
        ]);
        // creating new post version
        PostVersion::create([
            'post_id' => $postVersionId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'keywords' => $validated['keywords'],
            'edited_by' => $user->email,
            'start_timestamp' => Carbon::parse($validated['start_timestamp'])->timestamp
        ]);

        return redirect()->route('show.editPost', ['id' => $postVersionId])
                        ->with('success', 'Post saved successfully!');
    }

    public function showEditPost($postVersionId)
	{
        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, latest_versions.created_at as published_at
            FROM posts
            JOIN ( 
                SELECT post_versions.*, MAX(created_at) as created_at
                FROM post_versions
                GROUP BY post_id
            ) AS latest_versions
            ON posts.id = latest_versions.post_id
            AND latest_versions.post_id = :postVersionId
            ORDER BY latest_versions.created_at DESC
            LIMIT 1";
        $post = collect(DB::select($query,[
            'postVersionId' => $postVersionId
        ]))->first();

		return view('post.editPost', compact('post'));
	}

    public function publishPost(Request $request, $postVersionId) {
        $user = Auth::user();
        $this->authorize('publishPost', $user);
        Post::where('id', $postVersionId)->update(['status' => 'published']);

        return redirect()->route('show.editPost', ['id' => $postVersionId])
                        ->with('success', 'Post published!');
    }

    public function unPublishPost(Request $request, $postVersionId) {
        $user = Auth::user();
        $this->authorize('publishPost', $user);
        Post::where('id', $postVersionId)->update(['status' => 'draft']);

        return redirect()->route('show.editPost', ['id' => $postVersionId])
                        ->with('success', 'Post unpublished!');
    }
}
