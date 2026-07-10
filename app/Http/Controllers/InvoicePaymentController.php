<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class InvoicePaymentController extends Controller
{
    /**
     * Redirect the customer to Stripe Checkout.
     */
    public function checkout(Request $request, Invoice $invoice, StripePaymentService $stripe): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $invoice->user_id !== $user->id) {
            abort(403);
        }

        if (! $invoice->isPayable()) {
            $message = $invoice->isDraft()
                ? 'This invoice is still being reviewed. You will be notified when payment is due.'
                : 'This invoice is already paid.';

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('status', $message);
        }

        if (! StripePaymentService::isConfigured()) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->withErrors(['payment' => 'Online payment is not configured. Please pay at the service center.']);
        }

        $session = $stripe->createCheckoutSession($invoice);

        $invoice->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Stripe success return URL (backup if webhook is delayed).
     */
    public function success(Request $request, Invoice $invoice, StripePaymentService $stripe): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $invoice->user_id !== $user->id) {
            abort(403);
        }

        $sessionId = $request->string('session_id')->value();

        if ($sessionId && StripePaymentService::isConfigured()) {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $stripe->markInvoicePaidFromSession($session);
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', $invoice->fresh()->isPaid()
                ? 'Payment successful. Thank you!'
                : 'Payment received — your invoice will update shortly.');
    }

    /**
     * Stripe cancel return URL.
     */
    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $invoice->user_id !== $user->id) {
            abort(403);
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->withErrors(['payment' => 'Payment was cancelled. You can try again when ready.']);
    }

    /**
     * Stripe webhook endpoint.
     */
    public function webhook(Request $request, StripePaymentService $stripe): Response
    {
        try {
            $event = $stripe->constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $stripe->markInvoicePaidFromSession($event->data->object);
        }

        return response('OK', 200);
    }
}
