<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Investment>
 */
class InvestmentFactory extends Factory
{
    public function definition()
    {
        return [
            "owner_id" => Owner::factory(),
            "amount" => 1000.00,
            "creation_date" => today()->subMonths(12)
        ];
    }
}
