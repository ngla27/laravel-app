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
     * HomePage: Get latest versions of posts with published status
     */
    public function index()
    {
        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, datetime(start_timestamp, 'unixepoch') as published_at
            FROM posts
            JOIN ( 
                SELECT post_versions.*, MAX(start_timestamp) as start_timestamp
                FROM post_versions
                WHERE start_timestamp <= :currentTimestamp
                GROUP BY post_id 
            ) AS latest_versions
            ON posts.id = latest_versions.post_id 
            AND posts.status = :status
            ORDER BY latest_versions.start_timestamp DESC";

        $posts = collect(DB::select($query,[
            'status' => 'published',
            'currentTimestamp' => now(),
        ]));
        
        // Pass the posts data to the view
        return view('home', compact('posts'));
    }

    /**
     * HomePage: Get latest version of a single post
     */
    public function showPost($postVersionId)
    {
        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, datetime(start_timestamp, 'unixepoch') as published_at
            FROM posts
            JOIN ( 
                SELECT post_versions.*, MAX(start_timestamp) as start_timestamp
                FROM post_versions
                WHERE start_timestamp <= :currentTimestamp
                GROUP BY post_id
            ) AS latest_versions
            ON posts.id = latest_versions.post_id 
            AND posts.status = :status
            AND latest_versions.post_id = :postVersionId
            ORDER BY latest_versions.start_timestamp DESC
            LIMIT 1";

        $post = collect(DB::select($query,[
            'status' => 'published',
            'currentTimestamp' => now(),
            'postVersionId' => $postVersionId
        ]))->first();

        return view('post.showPost', compact('post'));
    }

    /**
     * Backoffice: Get latest versions of all posts
     */
    public function listPosts()
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }

        $query = "SELECT 
            posts.status, posts.authored_by,
            latest_versions.*, post_id as post_version_id, datetime(start_timestamp, 'unixepoch') as published_at
            FROM posts
            JOIN ( 
                SELECT post_versions.*, MAX(start_timestamp) as start_timestamp
                FROM post_versions
                WHERE start_timestamp <= :currentTimestamp
                GROUP BY post_id 
            ) AS latest_versions
            ON posts.id = latest_versions.post_id
            ORDER BY latest_versions.start_timestamp DESC";

        $posts = collect(DB::select($query,[
            'currentTimestamp' => now(),
        ]));
        
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
        if ($request->action === 'createAndPublish') {
            $this->authorize('publishPost', $user);
        }
        
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
            'status' => $validated['status'],
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
            'start_timestamp' => Carbon::parse($validated['start_timestamp'])->timestamp * 1000
        ]);

		return redirect()->route('show.account');
	}

    public function showEditPost($postVersionId)
	{
		return view('post.editPost');
	}

    public function editPost($postVersionId)
	{
		return view('post.editPost');
	}
}
