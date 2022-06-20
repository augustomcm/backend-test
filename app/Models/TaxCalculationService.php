<?php

namespace App\Models;

class TaxCalculationService
{
    const LESS_THAN_ONE_YEAR = 0.225;
    const BETWEEN_ONE_AND_TWO_YEARS = 0.185;
    const OLDER_THAN_TWO_YEARS = 0.15;

    public function calculate(Investment $investment, \DateTime $withdrawAt) : float
    {
        $gains = $investment->calculateGains();
        $taxRate = $this->factoryTaxRate($investment->getCreationDate(), $withdrawAt);

        $tax = $taxRate * $gains;

        return (float) number_format($tax, 2, '.', '');
    }

    private function factoryTaxRate(\DateTime $creationInvestment, \DateTime $withdrawAt)
    {
        $years = $withdrawAt->diff($creationInvestment)->y;

        if($years < 1)
            return self::LESS_THAN_ONE_YEAR;
        if($years >= 1 && $years < 2)
            return self::BETWEEN_ONE_AND_TWO_YEARS;

        return self::OLDER_THAN_TWO_YEARS;
    }
}
