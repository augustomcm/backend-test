<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    const MONTHLY_GAIN = 0.0052;

    protected $fillable = [
        'amount',
        'creation_date',
        'withdrawal_at'
    ];

    protected $casts = [
        'amount' => 'double'
    ];

    protected $dates = ['creation_date', 'withdrawal_at'];

    public $timestamps = false;

    public function setAmountAttribute($value)
    {
        if($value < 0)
            throw new \InvalidArgumentException("The amount must be positive");

        $this->attributes['amount'] = $value;
    }

    public function setCreationDateAttribute($value)
    {
        if($value > now())
            throw new \InvalidArgumentException("The creation date of an investment must be today or a date in the past");

        $this->attributes['creation_date'] = $value;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function calculateExpectedBalance(): float
    {
        $quantityMonths = today()->diffInMonths($this->getCreationDate());

        $total = $this->getAmount() * pow(1 + self::MONTHLY_GAIN, $quantityMonths);
        $roundedTotal = number_format($total, 2, '.', '');

        return (float) $roundedTotal;
    }

    public function calculateGains(): float
    {
        $expectedBalance = $this->calculateExpectedBalance();
        return number_format($expectedBalance - $this->getAmount(), 2, '.', '');
    }

    public function hasBeenWithdrawn(): bool
    {
        return !!$this->withdrawal_at;
    }

    public function setWithdrawalDate(\DateTime $date)
    {
        if($date < $this->getCreationDate() || $date > today()){
            throw new \InvalidArgumentException("Withdrawals can't happen before the investment creation or the future");
        }

        $this->withdrawal_at = $date;
    }

    public static function make(Owner $owner, $amount, \DateTime $creationDate)
    {
        $instance = new Investment();
        $instance->owner()->associate($owner);
        $instance->amount = $amount;
        $instance->creation_date = $creationDate;

        return $instance;
    }
}
