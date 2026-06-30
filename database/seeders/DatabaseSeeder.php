<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Message;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Named demo accounts (password: "password") -------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@fixflow.test'],
            ['name' => 'Admin User', 'role' => User::ROLE_ADMIN, 'password' => Hash::make('password')]
        );

        $technicians = collect(['Mike Torres', 'Lisa Chen', 'David Park'])->map(function (string $name, int $i) {
            return User::updateOrCreate(
                ['email' => 'technician'.($i === 0 ? '' : $i + 1).'@fixflow.test'],
                ['name' => $name, 'role' => User::ROLE_TECHNICIAN, 'password' => Hash::make('password')]
            );
        });

        $john = User::updateOrCreate(
            ['email' => 'customer@fixflow.test'],
            ['name' => 'John Customer', 'role' => User::ROLE_CUSTOMER, 'password' => Hash::make('password')]
        );

        if (RepairRequest::count() > 0) {
            if (Message::count() === 0) {
                $this->seedDemoChat($john);
            }

            return;
        }

        $customers = collect([$john])->merge(
            User::factory(6)->create(['role' => User::ROLE_CUSTOMER])
        );

        // --- Repair requests across the last 6 months --------------------
        // status => [share, hasTechnician, hasDiagnosis]
        $blueprint = [
            [RepairRequest::STATUS_COMPLETED, 9, true, true],
            [RepairRequest::STATUS_REPAIRING, 4, true, true],
            [RepairRequest::STATUS_DIAGNOSING, 3, true, true],
            [RepairRequest::STATUS_ASSIGNED, 3, true, false],
            [RepairRequest::STATUS_PENDING, 3, false, false],
        ];

        $diagnosisSamples = [
            'Screen digitizer and LCD assembly require replacement. No internal damage detected.',
            'Battery health at 62%. Recommending battery replacement and port cleaning.',
            'Liquid corrosion on logic board; cleaned and tested. Monitoring for stability.',
            'Charging IC faulty. Replaced and verified charging at full rate.',
            'Thermal paste degraded. Reapplied and confirmed temperatures back to normal.',
        ];

        foreach ($blueprint as [$status, $count, $hasTech, $hasDiagnosis]) {
            for ($i = 0; $i < $count; $i++) {
                $createdAt = Carbon::now()->subDays(rand(5, 175))->setTime(rand(8, 17), rand(0, 59));
                $customer = $i === 0 ? $john : $customers->random();
                $technician = $hasTech
                    ? ($customer->id === $john->id ? $technicians->first() : $technicians->random())
                    : null;

                $repair = RepairRequest::factory()->create([
                    'user_id' => $customer->id,
                    'technician_id' => $technician?->id,
                    'status' => $status,
                    'diagnosis_notes' => $hasDiagnosis ? fake()->randomElement($diagnosisSamples) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Completed jobs get an invoice and a warranty.
                if ($status === RepairRequest::STATUS_COMPLETED) {
                    $invoice = Invoice::factory()->create([
                        'repair_request_id' => $repair->id,
                        'user_id' => $repair->user_id,
                        'payment_status' => fake()->boolean(75) ? Invoice::STATUS_PAID : Invoice::STATUS_UNPAID,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    Warranty::factory()->create([
                        'repair_request_id' => $repair->id,
                        'user_id' => $repair->user_id,
                        'start_date' => $createdAt->copy(),
                        'end_date' => $createdAt->copy()->addMonths(6),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                } elseif ($status === RepairRequest::STATUS_REPAIRING && fake()->boolean(50)) {
                    // Some in-progress repairs already have an unpaid invoice.
                    Invoice::factory()->unpaid()->create([
                        'repair_request_id' => $repair->id,
                        'user_id' => $repair->user_id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            }
        }

        $this->seedDemoChat($john);
    }

    /**
     * Seed sample chat threads for the live demo (C8).
     */
    private function seedDemoChat(User $john): void
    {
        $repairing = RepairRequest::query()
            ->where('user_id', $john->id)
            ->where('status', RepairRequest::STATUS_REPAIRING)
            ->whereNotNull('technician_id')
            ->first();

        $diagnosing = RepairRequest::query()
            ->where('user_id', $john->id)
            ->where('status', RepairRequest::STATUS_DIAGNOSING)
            ->whereNotNull('technician_id')
            ->first();

        $completed = RepairRequest::query()
            ->where('user_id', $john->id)
            ->where('status', RepairRequest::STATUS_COMPLETED)
            ->whereNotNull('technician_id')
            ->first();

        if ($repairing) {
            $technician = $repairing->technician;
            $base = Carbon::now()->subDays(2)->setTime(10, 15);
            $this->seedMessage($repairing, $john, 'Hi '.$technician->name.', any update on my device?', $base, true);
            $this->seedMessage($repairing, $technician, 'Yes — screen assembly is on order. Should arrive tomorrow.', $base->copy()->addMinutes(18), true);
            $this->seedMessage($repairing, $john, 'Great, thanks! Will I get a message when repair starts?', $base->copy()->addMinutes(42), false);
        }

        if ($diagnosing) {
            $technician = $diagnosing->technician;
            $base = Carbon::now()->subDay()->setTime(14, 30);
            $this->seedMessage($diagnosing, $john, 'Just checking in — have you started the diagnosis yet?', $base, true);
            $this->seedMessage($diagnosing, $technician, 'Yes, battery health is at 58%. I recommend a replacement.', $base->copy()->addMinutes(25), true);
            $this->seedMessage($diagnosing, $technician, 'I can start the swap this afternoon if you approve the quote.', $base->copy()->addMinutes(30), false);
        }

        if ($completed) {
            $technician = $completed->technician;
            $base = Carbon::now()->subDays(10)->setTime(9, 0);
            $this->seedMessage($completed, $technician, 'Your device is ready for pickup!', $base, true);
            $this->seedMessage($completed, $john, 'Thanks — invoice paid. Everything looks good.', $base->copy()->addHours(3), true);
        }
    }

    private function seedMessage(
        RepairRequest $repair,
        User $sender,
        string $body,
        Carbon $at,
        bool $read,
    ): void {
        Message::create([
            'repair_request_id' => $repair->id,
            'user_id' => $sender->id,
            'body' => $body,
            'read_at' => $read ? $at->copy()->addMinutes(5) : null,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }
}
