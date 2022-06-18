<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Models\Owner;
use Tests\TestCase;

class InvestmentTest extends TestCase
{

    public function test_make_investment()
    {
        $user = Owner::factory()->make();

        $creationDate = now();
        $investment = Investment::make($user, 1000.00, $creationDate);

        $this->assertInstanceOf(Investment::class, $investment);
        $this->assertEquals($user, $investment->owner);
        $this->assertEquals(1000.00, $investment->amount);
        $this->assertEquals($creationDate, $investment->creation_date);
    }

    public function test_dont_make_investment_with_negative_amount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = Owner::factory()->make();

        Investment::make($user, -1000.00, now());
    }

    public function test_dont_make_investment_with_a_future_date()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = Owner::factory()->make();
        $tomorrow = now()->addDay();

        Investment::make($user, 1000.00, $tomorrow);
    }

    /**
     * @dataProvider providerGain
     */
    public function test_calculate_expected_balance($creationDate, $expectedGain)
    {
        $owner = Owner::factory()->make();
        $investment = Investment::make($owner, 1000.00, $creationDate);

        $total = $investment->calculateExpectedBalance();

        $this->assertSame($expectedGain, $total);
    }

    public function test_calculate_gain()
    {
        $owner = Owner::factory()->make();
        $investment = Investment::make($owner, 1000.00, today()->subMonths(6));

        $gains = $investment->calculateGains();

        $this->assertSame(31.61, $gains);
    }

    public function test_assign_withdrawal_date()
    {
        $owner = Owner::factory()->make();
        $investment = Investment::make($owner, 1000.00, today()->subMonths(6));

        $investment->setWithdrawalDate(today());

        $this->assertTrue($investment->hasBeenWithdrawn());
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
