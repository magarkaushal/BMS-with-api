<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostsExport;
class PostController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:sanctum');

        $this->middleware(PermissionMiddleware::class . ':post-create')->only('store');
        $this->middleware(PermissionMiddleware::class . ':post-edit')->only('update');
        $this->middleware(PermissionMiddleware::class . ':post-delete')->only('destroy');
        $this->middleware(PermissionMiddleware::class . ':post-export')->only('export');

    }
   

    public function index()
    {
        return PostResource::collection(Post::with(['category', 'author'])->latest()->paginate(10));
    }

    public function store(StorePostRequest $request)
    {

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
            'author_id' => $request->user()->id,
        ]);

        return new PostResource($post);
    }

    public function show(Post $post)
    {

        return new PostResource($post->load(['category', 'author']));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        if ($request->user()->can('post-edit') && ($post->author_id == $request->user()->id || $request->user()->hasRole('admin'))) {
            $post->update($request->only(['title', 'body', 'category_id']));
            return new PostResource($post);
        }
        abort(403, 'Unauthorized');
    }

    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->can('post-delete') && ($post->author_id == $request->user()->id || $request->user()->hasRole('admin'))) {
            $post->delete();
            return response()->json(['message' => 'Post deleted']);
        }
        abort(403, 'Unauthorized');
    }

    public function export()
    {
        return Excel::download(new PostsExport, 'posts.xlsx');
    }
}
