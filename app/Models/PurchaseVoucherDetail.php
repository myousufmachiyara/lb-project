<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseVoucherDetail extends Model
{
    protected $fillable = ['purchase_voucher_id', 'project_id', 'service_id', 'description', 'image', 'qty', 'unit', 'rate'];

    public function voucher()
    {
        return $this->belongsTo(PurchaseVoucher::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
