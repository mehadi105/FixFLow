<?php

namespace Database\Factories;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepairRequest>
 */
class RepairRequestFactory extends Factory
{
    public function definition(): array
    {
        $deviceType = fake()->randomElement(['Smartphone', 'Laptop', 'Tablet', 'Desktop']);

        $brands = [
            'Smartphone' => ['Apple', 'Samsung', 'Google'],
            'Laptop' => ['Apple', 'Dell', 'HP', 'Lenovo'],
            'Tablet' => ['Apple', 'Samsung', 'Microsoft'],
            'Desktop' => ['HP', 'Dell', 'Asus'],
        ];

        $issues = [
            'Cracked screen, touch partially unresponsive',
            'Battery drains quickly and will not hold charge',
            'Device will not power on',
            'Liquid damage after accidental spill',
            'Charging port loose and intermittent',
            'Overheating during normal use',
            'Speaker distortion at high volume',
            'Keyboard keys sticking or unresponsive',
        ];

        return [
            'user_id' => User::factory(),
            'technician_id' => null,
            'device_type' => $deviceType,
            'brand' => fake()->randomElement($brands[$deviceType]),
            'model' => fake()->bothify('Model ##??'),
            'serial_number' => strtoupper(fake()->bothify('SN-####-????')),
            'issue_description' => fake()->randomElement($issues),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => RepairRequest::STATUS_PENDING,
            'image_path' => null,
        ];
    }

    /**
     * Assign the human-friendly reference once the row has an id.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (RepairRequest $repairRequest) {
            if (! $repairRequest->reference) {
                $repairRequest->forceFill(['reference' => 'RR-'.(1000 + $repairRequest->id)])->saveQuietly();
            }
        });
    }
}
