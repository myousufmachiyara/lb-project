<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseVoucher extends Model
{
    protected $fillable = ['voucher_id', 'coa_id', 'date', 'status', 'created_by'];

    public function details()
    {
        return $this->hasMany(PurchaseVoucherDetail::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'coa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
