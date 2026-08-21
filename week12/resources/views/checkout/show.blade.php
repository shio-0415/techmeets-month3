<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            商品購入
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">{{ $product['name'] }}</h1>
                <p class="mb-6">価格: ¥{{ number_format($product['price']) }}</p>

                <form method="POST" action="{{ route('checkout.create') }}">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded">
                        購入する（テスト決済）
                    </button>
                </form>

                <p class="mt-4 text-sm text-gray-500">
                    テストカード番号: 4242 4242 4242 4242 / 有効期限は未来の日付 / CVCは任意の3桁
                </p>
            </div>
        </div>
    </div>
</x-app-layout>