<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCostComponent extends Model
{
    protected $table = 'loan_cost_components';

    protected $fillable = [
        'name',
        'calculation_type',
        'amount',
        'percentage',
        'account_id',
        'is_mandatory',
        'is_deducted_from_disbursement',
        'is_paid_separately',
        'active',
        'user_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
