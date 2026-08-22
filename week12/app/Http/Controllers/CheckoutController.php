<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    /**
     * 商品ページ（決済ボタンを表示する画面）
     */
    public function show()
    {
        $product = [
            'name' => 'テスト商品',
            'price' => 1000,
        ];

        return view('checkout.show', compact('product'));
    }

    /**
     * Stripe Checkout Session を作成し、Stripeの決済ページへリダイレクトする
     */
    public function create(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeCheckoutSession::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => 'テスト商品',
                        ],
                        'unit_amount' => 1000,
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'client_reference_id' => Auth::id(),
                'metadata' => [
                    'user_id' => Auth::id(),
                ],
            ]);
        } catch (ApiErrorException $e) {
            // Stripeとの通信失敗・APIエラー時にログを残し、利用者にはわかりやすいメッセージを返す
            Log::error('Stripe checkout session creation failed', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.show')
                ->with('error', '決済ページの作成に失敗しました。時間をおいて再度お試しください。');
        }

        return redirect($session->url);
    }

    /**
     * 決済完了後にStripeから戻ってくる画面
     */
    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('checkout.show')->with('error', 'セッション情報がありません');
        }

        try {
            $session = StripeCheckoutSession::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session retrieval failed', [
                'message' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->route('checkout.show')
                ->with('error', '決済情報の確認に失敗しました。時間をおいて再度お試しください。');
        }

        $purchase = Purchase::firstOrCreate(
            ['stripe_session_id' => $session->id],
            [
                'user_id' => $session->client_reference_id ?: Auth::id(),
                'amount' => $session->amount_total,
                'currency' => $session->currency,
                'status' => $session->payment_status,
                'stripe_payment_intent' => $session->payment_intent,
            ]
        );

        return view('checkout.success', compact('purchase'));
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}