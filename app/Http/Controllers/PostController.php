<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::whereNotNull('published_at')->get();
        $posts = $posts->map(function($post) {
            $post->updated_at = Carbon::parse($post->updated_at)->format('j F Y');
            $post->published_at = Carbon::parse($post->published_at)->format('j F Y');
            return $post;
        });

        // Pass the posts data to the view
        return view('home', compact('posts'));
    }

    public function showPost($id)
    {
        $post = Post::findOrFail($id);
        $post->updated_at = Carbon::parse($post->updated_at)->format('j F Y');
        $post->published_at = Carbon::parse($post->published_at)->format('j F Y');
        
        return view('post.showPost', compact('post'));
    }

    public function listPosts()
    {
        return Post::all();
    }
}
