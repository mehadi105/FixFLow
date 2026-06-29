<?php

namespace Database\Factories;

use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warranty>
 */
class WarrantyFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-5 months', 'now');

        return [
            'repair_request_id' => RepairRequest::factory(),
            'user_id' => User::factory(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+6 months'),
        ];
    }

    /**
     * Assign the warranty code once the row has an id.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Warranty $warranty) {
            if (! $warranty->warranty_code) {
                $warranty->forceFill([
                    'warranty_code' => 'WRN-'.str_pad((string) $warranty->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }
}
