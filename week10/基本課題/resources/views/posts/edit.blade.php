<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            投稿を編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('posts.update', $post) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="block font-medium">タイトル</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" class="border rounded w-full p-2 mb-1">
                    @error('title') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

                    <label class="block font-medium">内容</label>
                    <textarea name="content" rows="6" class="border rounded w-full p-2 mb-1">{{ old('content', $post->content) }}</textarea>
                    @error('content') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

                    <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded">更新する</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
