<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_id',
        'council_adviser_id',
        'academic_year',
    ];

    public function council()
    {
        return $this->belongsTo(Council::class);
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'council_adviser_id');
    }
}
