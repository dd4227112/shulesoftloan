<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentSchedule extends Model
{
    use HasFactory;
    protected $fillable=[
        'loan_id',
        'due_date',
        'installment_amount',
        'interest',
        'penality',
        'total_amount',
        'status',
        'parent_id',
    ];
    public function Loans()
    {
        return $this->hasOne('App\Models\Loan', 'id', 'loan_id');
    }
}
