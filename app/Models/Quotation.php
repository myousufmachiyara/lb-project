<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_name', 'date', 'created_by'];

    public function details()
    {
        return $this->hasMany(QuotationDetail::class);
    }

}
