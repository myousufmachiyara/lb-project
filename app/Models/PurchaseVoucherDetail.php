<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseVoucherDetail extends Model
{
    protected $fillable = ['purchase_voucher_id', 'project_id', 'service', 'description', 'image', 'qty', 'unit', 'rate'];

    public function voucher()
    {
        return $this->belongsTo(PurchaseVoucher::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
