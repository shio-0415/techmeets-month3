<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {}

    public function getPaginatedPosts(int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->paginate($perPage);
    }

    public function findPost(int $id): Post
    {
        return $this->postRepository->findOrFail($id);
    }

    public function createPost(array $data, int $userId): Post
    {
        $data['user_id'] = $userId;

        return $this->postRepository->create($data);
    }

    public function updatePost(Post $post, array $data): Post
    {
        return $this->postRepository->update($post, $data);
    }

    public function deletePost(Post $post): void
    {
        $this->postRepository->delete($post);
    }
}
