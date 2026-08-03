<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-500 mb-4">投稿者: {{ $post->user->name }}</p>
                <p class="mb-4 whitespace-pre-line">{{ $post->content }}</p>

                @if (auth()->id() === $post->user_id)
                    <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 underline">編集</a>

                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 underline">削除</button>
                    </form>
                @endif

                <p class="mt-4"><a href="{{ route('posts.index') }}" class="text-blue-600 underline">← 一覧へ戻る</a></p>
            </div>
        </div>
    </div>
</x-app-layout>
