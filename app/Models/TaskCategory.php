<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskCategory extends Model
{
    use HasFactory;

    protected $table = 'task_categories';
    
    protected $fillable = ['name', 'code'];

    public function projects()
    {
        // return $this->hasMany(Project::class, 'status_id');
        return $this->hasMany(Project::class);
    }
}
