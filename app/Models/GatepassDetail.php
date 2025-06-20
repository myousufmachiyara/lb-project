<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatepassDetail extends Model
{
    protected $fillable = [
        'gatepass_id',
        'image',
        'project_id',
        'service_id',
        'description',
        'qty',
        'unit',
        'rate',
    ];

    public function gatepass()
    {
        return $this->belongsTo(Gatepass::class);
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
