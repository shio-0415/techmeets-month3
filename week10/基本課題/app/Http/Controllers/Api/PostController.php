<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}

    public function index()
    {
        $posts = $this->postService->getPaginatedPosts();
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->createPost(
            $request->validated(),
            auth()->id()
        );

        return new PostResource($post);
    }
}
