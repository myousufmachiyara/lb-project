<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCostingDetail extends Model
{
    protected $fillable = ['project_costing_id', 'service_id', 'description', 'qty', 'rate', 'service_percent'];

    public function costing()
    {
        return $this->belongsTo(ProjectCosting::class, 'project_costing_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
