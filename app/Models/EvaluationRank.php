<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationRank extends Model
{
    use HasFactory;

    protected $table = 'evaluation_ranks';

    protected $fillable = [
        'evaluation_id',
        'user_id',
        'council_id',
        'final_score',
        'rank',
        'status',
    ];

    protected $casts = [
        'final_score' => 'decimal:3',
    ];

    public const WEIGHTS = [
        'adviser' => 0.65,
        'peer' => 0.25,
        'self' => 0.10,
    ];

    public const RANK_THRESHOLDS = [
        'gold' => 2.41,
        'silver' => 1.81,
        'bronze' => 1.21,
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class, 'council_id');
    }

    public static function updateForUser(int $evaluationId, int $userId, ?int $councilId): void
    {
        $evaluationForms = EvaluationForm::where('evaluation_id', $evaluationId)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('evaluator_type');

        $rank = self::firstOrCreate([
            'evaluation_id' => $evaluationId,
            'user_id' => $userId,
            'council_id' => $councilId,
        ]);

        $breakdown = self::calculateBreakdown($evaluationForms);
        $isFinalized = self::isFinalized($evaluationForms);

        [$finalScore, $rankTier, $status] = self::computeFinalRanking($breakdown, $isFinalized);

        $rank->update([
            'final_score' => $finalScore,
            'rank' => $rankTier,
            'status' => $status,
        ]);
    }

    protected static function calculateBreakdown(\Illuminate\Support\Collection $evaluationForms): array
    {
        $breakdown = [];

        foreach (self::WEIGHTS as $evaluatorType => $weight) {
            if (isset($evaluationForms[$evaluatorType])) {
                $score = $evaluationForms[$evaluatorType]->evaluator_score;
                $breakdown[$evaluatorType] = [
                    'score' => $score,
                    'weight' => $weight,
                    'weighted_score' => $score * $weight,
                ];
            }
        }

        return $breakdown;
    }

    protected static function isFinalized(\Illuminate\Support\Collection $evaluationForms): bool
    {
        return isset($evaluationForms['adviser']) && isset($evaluationForms['peer']) && isset($evaluationForms['self']);
    }

    protected static function computeFinalRanking(array $breakdown, bool $isFinalized): array
    {
        if (!$isFinalized) {
            return [null, null, 'pending'];
        }

        $totalWeightedScore = array_sum(array_column($breakdown, 'weighted_score'));
        $finalScore = round($totalWeightedScore, 3);
        $rankTier = self::calculateRank($finalScore);

        return [$finalScore, $rankTier, 'finalized'];
    }

    protected static function calculateRank(float $score): string
    {
        foreach (self::RANK_THRESHOLDS as $tier => $threshold) {
            if ($score >= $threshold) {
                return $tier;
            }
        }

        return 'none';
    }

    public function getRankColorAttribute(): string
    {
        return match ($this->rank) {
            'gold' => 'warning',
            'silver' => 'gray',
            'bronze' => 'orange',
            default => 'danger',
        };
    }

    public function getRankDisplayAttribute(): string
    {
        return match ($this->rank) {
            'gold' => 'Gold',
            'silver' => 'Silver',
            'bronze' => 'Bronze',
            'none' => 'None',
            default => 'Pending',
        };
    }
}
