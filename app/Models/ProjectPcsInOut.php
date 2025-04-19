<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectPcsInOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_pcs_in_out';

    protected $fillable = [
        'project_id',
        'date',
        'type',
        'pcs',
        'remarks',
    ];

    /**
     * Get the project associated with this record.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
