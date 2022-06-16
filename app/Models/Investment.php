<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'creation_date'
    ];

    protected $casts = [
        'amount' => 'double'
    ];

    protected $dates = ['creation_date'];

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

    public static function make(Owner $owner, $amount, \DateTime $creationDate)
    {
        $instance = new Investment();
        $instance->owner()->associate($owner);
        $instance->amount = $amount;
        $instance->creation_date = $creationDate;

        return $instance;
    }
}
