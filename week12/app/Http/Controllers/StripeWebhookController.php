<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload');
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook: signature verification failed');
            return response('Invalid signature', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;

            Log::info('Stripe payment succeeded', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ]);
        }

        return response('Webhook handled', 200);
    }
}