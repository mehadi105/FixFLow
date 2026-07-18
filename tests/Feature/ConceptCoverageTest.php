<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConceptCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_responses_include_fixflow_trace_headers(): void
    {
        $response = $this->withHeader('X-Request-ID', 'course-demo-request')->get('/');

        $response->assertOk()
            ->assertHeader('X-FixFlow-App', 'FixFlow')
            ->assertHeader('X-Request-ID', 'course-demo-request');
    }

    public function test_login_requests_are_rate_limited(): void
    {
        $email = 'rate-limit@example.test';
        $key = Str::transliterate(Str::lower($email).'|127.0.0.1');

        RateLimiter::clear($key);

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post('/login', [
                'email' => $email,
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post('/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_admin_report_uses_joined_technician_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create([
            'name' => 'Query Builder Technician',
            'role' => User::ROLE_TECHNICIAN,
        ]);

        RepairRequest::factory()->create([
            'technician_id' => $technician->id,
            'status' => RepairRequest::STATUS_COMPLETED,
        ]);

        $this->actingAs($admin)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Query Builder Technician')
            ->assertSee('100%');
    }

    public function test_user_can_store_a_compact_table_cookie(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('preferences.table-density'), ['density' => 'compact'])
            ->assertRedirect()
            ->assertCookie('ff_table_density');
    }

    public function test_admin_can_delete_only_a_draft_invoice(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create();
        $repair = RepairRequest::factory()->create([
            'user_id' => $customer->id,
            'status' => RepairRequest::STATUS_COMPLETED,
        ]);
        $invoice = Invoice::factory()->create([
            'repair_request_id' => $repair->id,
            'user_id' => $customer->id,
            'payment_status' => Invoice::STATUS_DRAFT,
        ]);

        $this->actingAs($admin)
            ->delete(route('invoices.destroy', $invoice))
            ->assertRedirect(route('repair-requests.show', $repair));

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_sent_invoice_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create();
        $repair = RepairRequest::factory()->create(['user_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'repair_request_id' => $repair->id,
            'user_id' => $customer->id,
            'payment_status' => Invoice::STATUS_UNPAID,
        ]);

        $this->actingAs($admin)
            ->delete(route('invoices.destroy', $invoice))
            ->assertSessionHasErrors('invoice');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
    }
}
