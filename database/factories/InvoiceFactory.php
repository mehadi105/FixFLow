<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $service = fake()->randomFloat(2, 40, 200);
        $parts = fake()->randomFloat(2, 0, 300);
        $discount = fake()->randomElement([0, 0, 0, 10, 20, 25]);

        return [
            'repair_request_id' => RepairRequest::factory(),
            'user_id' => User::factory(),
            'service_charge' => $service,
            'parts_cost' => $parts,
            'discount' => $discount,
            'total' => max(0, $service + $parts - $discount),
            'payment_status' => fake()->randomElement([Invoice::STATUS_PAID, Invoice::STATUS_UNPAID]),
        ];
    }

    /**
     * Assign the invoice number once the row has an id.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Invoice $invoice) {
            if (! $invoice->invoice_number) {
                $invoice->forceFill([
                    'invoice_number' => 'INV-'.$invoice->created_at->year.'-'.str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function paid(): static
    {
        return $this->state(fn () => ['payment_status' => Invoice::STATUS_PAID]);
    }

    public function unpaid(): static
    {
        return $this->state(fn () => ['payment_status' => Invoice::STATUS_UNPAID]);
    }
}
