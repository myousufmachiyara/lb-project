<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCosting extends Model
{
    protected $table = 'project_costings';
    protected $fillable = ['project_id', 'created_by', 'date'];

    public function details()
    {
        return $this->hasMany(ProjectCostingDetail::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
