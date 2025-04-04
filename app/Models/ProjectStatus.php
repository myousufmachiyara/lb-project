<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectStatus extends Model
{
    use HasFactory;

    protected $table = 'project_status';
    
    protected $fillable = ['name', 'color'];
}
