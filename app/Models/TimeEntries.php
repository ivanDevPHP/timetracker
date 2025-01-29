<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntries extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'start_date',
        'project_id',
        'task',
        'planned',
        'task_summary',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    public function project()
    {
        return $this->belongsTo(Projects::class);
    }
}
