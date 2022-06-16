<?php

namespace App\Models;

class GainCalculatingService
{
    const MONTHLY_GAIN = 0.0052;

    public function calculateAmount(Investment $investment): float
    {
        $quantityMonths = today()->diffInMonths($investment->getCreationDate());

        $total = $investment->getAmount() * pow(1 + self::MONTHLY_GAIN, $quantityMonths);
        $roundedTotal = number_format($total, 2, '.', '');

        return (float) $roundedTotal;
    }
}
