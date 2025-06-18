<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCosting extends Model
{
    protected $fillable = ['project_id', 'date', 'remarks'];

    public function details()
    {
        return $this->hasMany(ProjectCostingDetail::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
