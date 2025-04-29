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
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
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
