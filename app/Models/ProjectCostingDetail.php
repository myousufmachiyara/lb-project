<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCostingDetail extends Model
{
    protected $fillable = [
        'project_costing_id', 'purchase_voucher_detail_id', 'service',
        'qty', 'rate', 'service_percentage', 'total_rate', 'total_amount'
    ];

    public function costing()
    {
        return $this->belongsTo(ProjectCosting::class);
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseVoucherDetail::class);
    }

}
