<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'category_id',
        'status_id',
        'project_id',
        'description',
        'due_date',
        'due_time',
        'sort_order',
        'is_recurring',
        'recurring_frequency',
        'last_completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'last_completed_at' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
