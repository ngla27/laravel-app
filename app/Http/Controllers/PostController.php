<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostVersion;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
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

    public function listPosts()
    {
        return Post::all();
    }
}
