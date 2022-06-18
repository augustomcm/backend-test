<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_of_investment()
    {
        $owner = Owner::factory()->create();

        $response = $this->postJson('/api/investments', [
            'owner' => $owner->id,
            'amount' => 1000.00,
            'creation_date' => now()->format('Y-m-d')
        ]);

        $response->assertStatus(201);
        $this->assertNotEmpty($response['id']);
    }

    public function test_do_not_create_investment_with_nonexistent_owner()
    {
        $response = $this->postJson('/api/investments', [
            'owner' => 1,
            'amount' => 1000.00,
            'creation_date' => now()->format('Y-m-d')
        ]);

        $response->assertStatus(400);
    }

    public function test_do_not_create_investment_with_invalid_data()
    {
        $response = $this->postJson('/api/investments', [
            'owner' => 'any value',
            'amount' => -1000,
            'creation_date' => now()->addDay()->format('Y-m-d')
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'owner',
                'amount',
                'creation_date'
            ]
        ]);
    }

    public function test_view_of_an_investment()
    {
        $investment = Investment::factory()->create([
            'amount' => 1000.00,
            'creation_date' => today()->subMonths(12)
        ]);

        $response = $this->getJson("/api/investments/{$investment->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'initial_amount' => 1000.00,
            'expected_balance' => 1064.22
        ]);
    }

    public function test_withdrawal_of_an_investment()
    {
        $investment = Investment::factory()->create([
            'amount' => 1000.00,
            'creation_date' => today()->subMonths(12)
        ]);

        $response = $this->putJson("/api/investments/{$investment->id}/withdraw", [
            "date" => today()->format('Y-m-d')
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'amount_withdrawn' => 1052.34
        ]);
    }

    public function test_do_not_withdraw_with_invalid_date()
    {
        $investment = Investment::factory()->create([
            'amount' => 1000.00,
            'creation_date' => today()
        ]);

        $beforeInvestment = today()->subDay();

        $response = $this->putJson("/api/investments/{$investment->id}/withdraw", [
            "date" => $beforeInvestment->format('Y-m-d')
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'date',
            ]
        ]);
    }
}
