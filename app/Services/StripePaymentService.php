<?php

namespace App\Services;

use App\Models\Invoice;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public static function isConfigured(): bool
    {
        return filled(config('services.stripe.secret')) && filled(config('services.stripe.key'));
    }

    /**
     * Create a Stripe Checkout session for an invoice.
     */
    public function createCheckoutSession(Invoice $invoice): Session
    {
        $invoice->loadMissing(['customer', 'repairRequest']);

        return Session::create([
            'mode' => 'payment',
            'customer_email' => $invoice->customer->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'usd'),
                    'product_data' => [
                        'name' => 'FixFlow Repair Invoice '.$invoice->invoice_number,
                        'description' => 'Repair '.$invoice->repairRequest->reference.' — '.$invoice->repairRequest->device_label,
                    ],
                    'unit_amount' => $this->amountInCents($invoice->total),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'invoice_id' => (string) $invoice->id,
            ],
            'success_url' => route('invoices.payment.success', $invoice).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('invoices.payment.cancel', $invoice),
        ]);
    }

    /**
     * Mark an invoice paid from a completed Checkout session.
     */
    public function markInvoicePaidFromSession(Session $session): ?Invoice
    {
        $invoiceId = $session->metadata->invoice_id ?? null;

        if (! $invoiceId) {
            return null;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->isPaid()) {
            return $invoice;
        }

        if ($session->payment_status !== 'paid') {
            return null;
        }

        $invoice->update([
            'payment_status' => Invoice::STATUS_PAID,
            'payment_method' => 'stripe',
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => is_string($session->payment_intent)
                ? $session->payment_intent
                : ($session->payment_intent->id ?? null),
            'paid_at' => now(),
        ]);

        app(InvoiceService::class)->handleInvoicePaid($invoice->fresh());

        return $invoice->fresh();
    }

    /**
     * Verify and handle a Stripe webhook payload.
     */
    public function constructEvent(string $payload, ?string $signature): \Stripe\Event
    {
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            throw new UnexpectedValueException('Stripe webhook secret is not configured.');
        }

        try {
            return Webhook::constructEvent($payload, $signature ?? '', $secret);
        } catch (SignatureVerificationException $exception) {
            throw $exception;
        }
    }

    private function amountInCents(float|string $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
