<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'role',
        'is_active',
        'bio',
        'password',
        'pfp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the evaluations where this user is the adviser.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'council_adviser_id');
    }

    /**
     * Get the evaluations this user is participating in
     */
    public function participatingEvaluations()
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_user')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get peer evaluations where this user is the evaluator
     */
    public function peerEvaluations()
    {
        return $this->hasMany(EvaluationPeerEvaluator::class, 'evaluator_user_id');
    }

    /**
     * Get peer evaluations where this user is being evaluated
     */
    public function receivedPeerEvaluations()
    {
        return $this->hasMany(EvaluationPeerEvaluator::class, 'evaluatee_user_id');
    }

}
