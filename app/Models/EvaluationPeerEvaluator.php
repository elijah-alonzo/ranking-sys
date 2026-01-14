<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationPeerEvaluator extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'evaluatee_user_id',
        'evaluator_user_id',
        'assigned_by_user_id',
        'assignment_notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluateeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluatee_user_id');
    }

    public function evaluatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Check if a user can evaluate another user as peer
     */
    public static function canEvaluateAsPeer(int $evaluationId, int $evaluatorUserId, int $evaluateeUserId): bool
    {
        // Users cannot evaluate themselves
        if ($evaluatorUserId === $evaluateeUserId) {
            return false;
        }

        // Check if this peer assignment exists
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_user_id', $evaluatorUserId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->exists();
    }

    /**
     * Get all users that a specific user can evaluate as peer in an evaluation
     */
    public static function getEvaluatableUsers(int $evaluationId, int $evaluatorUserId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_user_id', $evaluatorUserId)
            ->with('evaluateeUser')
            ->get()
            ->pluck('evaluateeUser.id')
            ->toArray();
    }

    /**
     * Get all peer evaluators assigned to evaluate a specific user
     */
    public static function getAssignedPeerEvaluators(int $evaluationId, int $evaluateeUserId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->with('evaluatorUser')
            ->get()
            ->pluck('evaluatorUser.id')
            ->toArray();
    }
}
