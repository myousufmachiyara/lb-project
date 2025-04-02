<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccounts extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shoa_id',
        'name',
        'receivables',
        'payables',
        'opening_date',
        'remarks',
        'address',
        'phone_no',
        'created_by'
    ];

    // Define the relationship with SubHeadOfAccounts (belongs to)
    public function subHeadOfAccount()
    {
        return $this->belongsTo(SubHeadOfAccounts::class, 'shoa_id', 'id');
    }
}
