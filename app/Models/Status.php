<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    
    protected $fillable = ['name', 'color'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'status_id');
    }
}
