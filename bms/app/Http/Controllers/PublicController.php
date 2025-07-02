<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;

class PublicController extends Controller
{
    public function posts()
    {
        $posts = Post::with(['author', 'category'])->paginate(15);
        return PostResource::collection($posts);
    }
}
