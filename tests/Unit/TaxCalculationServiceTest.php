<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Models\TaxCalculationService;
use Tests\TestCase;

class TaxCalculationServiceTest extends TestCase
{
    public function test_calculate_tax_less_than_a_year()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subMonths(6)
        ]);

        $withdrawalDate = today();

        $taxCalculation = new TaxCalculationService();
        $tax = $taxCalculation->calculate($investment, $withdrawalDate);

        $this->assertSame(7.11, $tax);
    }

    public function test_calculate_tax_between_one_and_two_years()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subYear()
        ]);

        $withdrawalDate = today();

        $taxCalculation = new TaxCalculationService();
        $tax = $taxCalculation->calculate($investment, $withdrawalDate);

        $this->assertSame(11.88, $tax);
    }

    public function test_calculate_tax_older_than_two_years()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subYears(2)
        ]);

        $withdrawalDate = today();

        $taxCalculation = new TaxCalculationService();
        $tax = $taxCalculation->calculate($investment, $withdrawalDate);

        $this->assertSame(19.88, $tax);
    }
}
