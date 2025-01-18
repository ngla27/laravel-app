<?php

namespace App\Http\Controllers;

class AccountController extends Controller
{
    public function showAccountManagement () {
        $classA = new PostController();
        $posts = $classA->listPosts();

        return view('accountManagement', compact('posts'));
    }
}
