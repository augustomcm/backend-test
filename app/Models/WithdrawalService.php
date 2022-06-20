<?php

namespace App\Models;

class WithdrawalService
{
    protected $taxCalculation;

    public function __construct(TaxCalculationService $taxCalculation)
    {
        $this->taxCalculation = $taxCalculation;
    }

    public function withdrawInvestment(Investment $investment, \DateTime $withdrawAt) : float
    {
        $expectedBalance = $investment->calculateExpectedBalance();
        $taxation = $this->taxCalculation->calculate($investment, $withdrawAt);

        $total = number_format($expectedBalance - $taxation, 2, '.', '');

        $investment->setWithdrawalDate($withdrawAt);
        $investment->save();

        return (float) $total;
    }
}
