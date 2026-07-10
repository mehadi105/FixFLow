<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RepairRequest;

class InvoiceService
{
    /**
     * Create a draft invoice when a repair is marked completed (if one does not exist).
     */
    public function ensureInvoiceForCompletedRepair(RepairRequest $repair): ?Invoice
    {
        if ($repair->status !== RepairRequest::STATUS_COMPLETED) {
            return null;
        }

        $repair->loadMissing('invoice');

        if ($repair->invoice) {
            $this->syncFulfillmentStatus($repair);

            return $repair->invoice;
        }

        $serviceCharge = 85.00;
        $partsCost = $this->estimatePartsCost($repair);
        $discount = 0.0;
        $total = max(0, $serviceCharge + $partsCost - $discount);

        $invoice = Invoice::create([
            'repair_request_id' => $repair->id,
            'user_id' => $repair->user_id,
            'service_charge' => $serviceCharge,
            'parts_cost' => $partsCost,
            'discount' => $discount,
            'total' => $total,
            'payment_status' => Invoice::STATUS_DRAFT,
        ]);

        $invoice->update([
            'invoice_number' => 'INV-'.now()->year.'-'.str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT),
        ]);

        $repair->update([
            'fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_INVOICE,
        ]);

        return $invoice->fresh();
    }

    /**
     * Admin sends a reviewed draft invoice to the customer for payment.
     */
    public function sendInvoiceToCustomer(Invoice $invoice): void
    {
        if (! $invoice->isDraft()) {
            return;
        }

        $invoice->update(['payment_status' => Invoice::STATUS_UNPAID]);

        $repair = $invoice->repairRequest;

        if ($repair && $repair->status === RepairRequest::STATUS_COMPLETED) {
            $repair->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_PAYMENT]);
        }
    }

    /**
     * Keep fulfillment status aligned with invoice payment state.
     */
    public function syncFulfillmentStatus(RepairRequest $repair): void
    {
        if ($repair->status !== RepairRequest::STATUS_COMPLETED) {
            return;
        }

        $repair->loadMissing('invoice');

        if (! $repair->invoice) {
            return;
        }

        if ($repair->fulfillment_status === RepairRequest::FULFILLMENT_FULFILLED) {
            return;
        }

        if ($repair->invoice->isDraft()) {
            $repair->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_INVOICE]);

            return;
        }

        if ($repair->invoice->isPayable()) {
            $repair->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_PAYMENT]);

            return;
        }

        if ($repair->invoice->isPaid() && in_array($repair->fulfillment_status, [
            null,
            RepairRequest::FULFILLMENT_AWAITING_INVOICE,
            RepairRequest::FULFILLMENT_AWAITING_PAYMENT,
        ], true)) {
            $repair->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_CHOICE]);
        }
    }

    /**
     * After an invoice is paid, let the customer choose pickup or delivery.
     */
    public function handleInvoicePaid(Invoice $invoice): void
    {
        $repair = $invoice->repairRequest;

        if (! $repair || $repair->status !== RepairRequest::STATUS_COMPLETED) {
            return;
        }

        if ($repair->fulfillment_status === RepairRequest::FULFILLMENT_FULFILLED) {
            return;
        }

        $repair->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_CHOICE]);
    }

    public function recalculateTotal(Invoice $invoice, float $serviceCharge, float $partsCost, float $discount): float
    {
        return max(0, $serviceCharge + $partsCost - $discount);
    }

    private function estimatePartsCost(RepairRequest $repair): float
    {
        $issue = strtolower($repair->issue_description ?? '');

        if (str_contains($issue, 'screen') || str_contains($issue, 'crack')) {
            return 145.00;
        }

        if (str_contains($issue, 'battery')) {
            return 65.00;
        }

        if (str_contains($issue, 'liquid') || str_contains($issue, 'water')) {
            return 120.00;
        }

        return 95.00;
    }
}
