<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    
    // protected $fillable = ['name', 'acc_id', 'total_pcs', 'description', 'status_id'];
    
    protected $fillable = ['name', 'total_pcs', 'description', 'status_id'];

    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class, 'proj_id');
    }

    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('sort_order');
    }

    public function pcsInOut()
    {
        return $this->hasMany(ProjectPcsInOut::class, 'project_id');
    }

    // public function account()
    // {
    //     return $this->belongsTo(ChartOfAccounts::class, 'acc_id');
    // }
    public static function totalPiecesInProcess()
    {
        return DB::table('projects as p')
            ->leftJoin('project_pcs_in_out as io', 'p.id', '=', 'io.project_id')
            ->where('p.status_id', 2)
            ->selectRaw('
                SUM(p.total_pcs) 
                + SUM(CASE WHEN io.type = "in" THEN io.pcs ELSE 0 END)
                - SUM(CASE WHEN io.type = "out" THEN io.pcs ELSE 0 END)
                as total
            ')
            ->value('total') ?? 0;  // fallback if null
    }
}
