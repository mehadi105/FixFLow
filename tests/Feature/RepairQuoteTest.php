<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\RepairRequest;
use App\Models\TechnicianApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_can_send_quote_after_diagnosis(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $technician = User::factory()->create(['role' => User::ROLE_TECHNICIAN]);
        TechnicianApplication::create([
            'user_id' => $technician->id,
            'phone' => '555-0100',
            'years_experience' => 3,
            'specialties' => 'Phones',
            'motivation' => 'Love repairs',
            'status' => TechnicianApplication::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);
        $repair = RepairRequest::factory()->create([
            'user_id' => $customer->id,
            'technician_id' => $technician->id,
            'status' => RepairRequest::STATUS_DIAGNOSING,
            'diagnosis_notes' => 'Battery needs replacement.',
            'issue_description' => 'Battery drains quickly',
        ]);

        $this->actingAs($technician)
            ->post(route('repair-requests.quote', $repair), [
                'quote_service_charge' => 85,
                'quote_parts_cost' => 65,
                'quote_discount' => 0,
                'diagnosis_fee' => 25,
                'quote_notes' => 'Battery kit + labour',
            ])
            ->assertRedirect();

        $repair->refresh();

        $this->assertSame(RepairRequest::STATUS_QUOTED, $repair->status);
        $this->assertEquals(85.0, (float) $repair->quote_service_charge);
        $this->assertEquals(65.0, (float) $repair->quote_parts_cost);
        $this->assertNotNull($repair->quoted_at);
    }

    public function test_customer_can_approve_quote_and_continue_repair(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $repair = RepairRequest::factory()->create([
            'user_id' => $customer->id,
            'status' => RepairRequest::STATUS_QUOTED,
            'diagnosis_notes' => 'Screen replacement required.',
            'quote_service_charge' => 85,
            'quote_parts_cost' => 145,
            'quote_discount' => 0,
            'diagnosis_fee' => 25,
            'quoted_at' => now(),
        ]);

        $this->actingAs($customer)
            ->post(route('repair-requests.quote.approve', $repair))
            ->assertRedirect();

        $repair->refresh();

        $this->assertSame(RepairRequest::STATUS_REPAIRING, $repair->status);
        $this->assertNotNull($repair->quote_responded_at);
    }

    public function test_customer_decline_creates_diagnosis_fee_invoice(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $repair = RepairRequest::factory()->create([
            'user_id' => $customer->id,
            'status' => RepairRequest::STATUS_QUOTED,
            'diagnosis_notes' => 'Logic board repair recommended.',
            'quote_service_charge' => 120,
            'quote_parts_cost' => 200,
            'quote_discount' => 0,
            'diagnosis_fee' => 25,
            'quoted_at' => now(),
        ]);

        $this->actingAs($customer)
            ->post(route('repair-requests.quote.decline', $repair))
            ->assertRedirect();

        $repair->refresh();

        $this->assertSame(RepairRequest::STATUS_DECLINED, $repair->status);
        $this->assertDatabaseHas('invoices', [
            'repair_request_id' => $repair->id,
            'user_id' => $customer->id,
            'service_charge' => 25,
            'parts_cost' => 0,
            'total' => 25,
            'payment_status' => Invoice::STATUS_UNPAID,
        ]);
        $this->assertSame(
            RepairRequest::FULFILLMENT_AWAITING_PAYMENT,
            $repair->fulfillment_status
        );
    }

    public function test_completed_invoice_uses_approved_quote_amounts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $repair = RepairRequest::factory()->create([
            'user_id' => $customer->id,
            'technician_id' => null,
            'status' => RepairRequest::STATUS_REPAIRING,
            'diagnosis_notes' => 'Done after approval.',
            'quote_service_charge' => 90,
            'quote_parts_cost' => 110,
            'quote_discount' => 15,
            'diagnosis_fee' => 25,
            'quoted_at' => now()->subDay(),
            'quote_responded_at' => now()->subHours(12),
        ]);

        $this->actingAs($admin)
            ->post(route('repair-requests.status', $repair), [
                'status' => RepairRequest::STATUS_COMPLETED,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'repair_request_id' => $repair->id,
            'service_charge' => 90,
            'parts_cost' => 110,
            'discount' => 15,
            'total' => 185,
            'payment_status' => Invoice::STATUS_DRAFT,
        ]);
    }
}
