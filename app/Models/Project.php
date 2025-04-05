<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    
    protected $fillable = ['name', 'acc_id', 'total_pcs', 'description', 'status_id'];
    
    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class, 'proj_id');
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'acc_id');
    }
}
