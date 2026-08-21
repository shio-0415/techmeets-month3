code resources/views/checkout/cancel.blade.php

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            お支払い完了
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">お支払いが完了しました</h1>
                <p>購入ID: {{ $purchase->id }}</p>
                <p>金額: ¥{{ number_format($purchase->amount) }}</p>
                <p>ステータス: {{ $purchase->status }}</p>
            </div>
        </div>
    </div>
</x-app-layout>