<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        // N+1問題対策: with('user') で投稿者情報を事前に読み込む
        return Post::with('user')->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Post
    {
        return Post::with('user')->findOrFail($id);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}
