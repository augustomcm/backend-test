<?php

namespace Tests\Feature;

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
}
