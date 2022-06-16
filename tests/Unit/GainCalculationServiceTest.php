<?php

namespace Tests\Unit;


use App\Models\GainCalculatingService;
use App\Models\Investment;
use App\Models\Owner;
use Tests\TestCase;

class GainCalculationServiceTest extends TestCase
{
    /**
     * @dataProvider providerGain
     */
    public function test_calculate_gain($creationDate, $expectedGain)
    {
        $owner = Owner::factory()->make();
        $investment = Investment::make($owner, 1000.00, $creationDate);

        $gainService = new GainCalculatingService();
        $total = $gainService->calculateAmount($investment);

        $this->assertSame($expectedGain, $total);
    }

    public function providerGain()
    {
        return [
            [
                today(),
                1000.0
            ],
            [
                today()->subMonths(6),
                1031.61
            ],
            [
                today()->subMonths(12),
                1064.22
            ]
        ];
    }
}
