<?php

namespace App\Models;

class WithdrawalService
{
    public function withdrawInvestment(Investment $investment, \DateTime $withdrawAt) : float
    {


        $expectedBalance = $investment->calculateExpectedBalance();
        $gains = $investment->calculateGains();
        $tax = $this->factoryTax($investment->getCreationDate(), $withdrawAt);
        $taxation = $tax * $gains;

        $total = number_format($expectedBalance - $taxation, 2, '.', '');

        $investment->setWithdrawalDate($withdrawAt);
        $investment->save();

        return (float) $total;
    }

    private function factoryTax(\DateTime $creationInvestment, \DateTime $withdrawAt)
    {
        $years = $withdrawAt->diff($creationInvestment)->y;

        if($years < 1)
            return 0.225;
        if($years >= 1 && $years < 2)
            return 0.185;

        return 0.15;
    }
}
