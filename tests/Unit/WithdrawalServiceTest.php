<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Models\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_withdrawal_less_than_a_year_old()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subMonths(6)
        ]);

        $withdrawalService = new WithdrawalService();
        $amount = $withdrawalService->withdrawInvestment($investment, today());

        $this->assertSame(1024.5, $amount);
        $this->assertTrue($investment->hasBeenWithdrawn());
    }

    public function test_withdrawal_between_one_and_two_years_old()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subYear()
        ]);

        $withdrawalService = new WithdrawalService();
        $amount = $withdrawalService->withdrawInvestment($investment, today());

        $this->assertSame(1052.34, $amount);
        $this->assertTrue($investment->hasBeenWithdrawn());
    }

    public function test_withdrawal_older_than_two_years()
    {
        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subYears(2)
        ]);

        $withdrawalService = new WithdrawalService();
        $amount = $withdrawalService->withdrawInvestment($investment, today());

        $this->assertSame(1112.68, $amount);
        $this->assertTrue($investment->hasBeenWithdrawn());
    }

    public function test_do_not_withdraw_with_a_future_date()
    {
        $this->expectException(\InvalidArgumentException::class);

        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subYears(2)
        ]);

        $tomorrow = today()->addDay();

        $withdrawalService = new WithdrawalService();
        $withdrawalService->withdrawInvestment($investment, $tomorrow);
    }

    public function test_do_not_withdraw_before_creation_date()
    {
        $this->expectException(\InvalidArgumentException::class);

        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()
        ]);

        $yesterday = today()->subDay();

        $withdrawalService = new WithdrawalService();
        $withdrawalService->withdrawInvestment($investment, $yesterday);
    }

    public function test_do_not_withdraw_an_investment_already_withdrawn()
    {
        $this->expectException(\DomainException::class);

        $investment = Investment::factory()->make([
            'amount' => 1000.00,
            'creation_date' => today()->subMonths(12),
            'withdrawal_at' => today()
        ]);

        $withdrawalService = new WithdrawalService();
        $withdrawalService->withdrawInvestment($investment, today());
    }

}
