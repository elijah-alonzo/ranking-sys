<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_id',
        'council_adviser_id',
        'academic_year',
    ];

    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class);
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'council_adviser_id');
    }

    /**
     * The users that belong to this evaluation with their positions
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'evaluation_user')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * All peer evaluator assignments for this evaluation
     */
    public function peerEvaluators(): HasMany
    {
        return $this->hasMany(EvaluationPeerEvaluator::class);
    }

    /**
     * Generate URL for evaluating a specific user with the unified evaluation page
     */
    public function getEvaluationUrl(int $userId, string $evaluatorType): string
    {
        return \App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl(
            'evaluate-student',
            [
                'evaluation' => $this->id,
                'user' => $userId,
                'type' => $evaluatorType,
            ]
        );
    }
}
