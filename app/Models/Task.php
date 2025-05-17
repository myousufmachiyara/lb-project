<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'task_name',
        'category_id',
        'status_id',
        'project_id',
        'description',
        'due_date',
        'sort_order',  
        'is_recurring',         
        'recurring_frequency', 
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
