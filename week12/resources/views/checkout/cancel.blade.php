<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済キャンセル
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">決済がキャンセルされました</h1>
                <a href="{{ route('checkout.show') }}" class="text-indigo-600 underline">商品ページに戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>