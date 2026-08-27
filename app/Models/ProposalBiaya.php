<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalBiaya extends Model
{
    protected $table = 'proposal_biaya';

    protected $fillable = [
        'proposal_id',
        'component_id',
        'nama',
        'nominal',
        'persen',
        'account_id',
        'is_deducted_from_disbursement',
        'user_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function component()
    {
        return $this->belongsTo(LoanCostComponent::class, 'component_id');
    }
}
