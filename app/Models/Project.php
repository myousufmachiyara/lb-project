<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    
    protected $fillable = ['name', 'acc_id', 'total_pcs'];
    
    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class, 'proj_id');
    }
}
