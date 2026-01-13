<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Council extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'description',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users for the council.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the evaluations for the council.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
