<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'active'
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}
