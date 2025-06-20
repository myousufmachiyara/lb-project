<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gatepass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'coa_id',
        'date',
        'created_by',
    ];

    // Vendor (Chart of Account)
    public function coa()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'coa_id');
    }

    // Created By User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Gatepass Details
    public function details()
    {
        return $this->hasMany(GatepassDetail::class);
    }
}
