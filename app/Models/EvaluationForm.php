<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationForm extends Model
{
	use HasFactory;

	protected $fillable = [
		'evaluation_id',
		'user_id',
		'evaluator_type',
		'evaluator_id',
		'answers',
		'evaluator_score',
	];

	protected $casts = [
		'answers' => 'array',
		'evaluator_score' => 'decimal:3',
	];

	public function evaluation(): BelongsTo
	{
		return $this->belongsTo(Evaluation::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function evaluator(): BelongsTo
	{
		return $this->belongsTo(User::class, 'evaluator_id');
	}

	// ========================================
	// QUESTION MANAGEMENT
	// ========================================

	public static function getQuestionsForEvaluator(string $evaluatorType): array
	{
		$allQuestions = self::getAllQuestions();

		return match ($evaluatorType) {
			'adviser' => $allQuestions,
			'peer' => self::getPeerQuestions($allQuestions),
			'self' => self::getSelfQuestions($allQuestions),
			default => [],
		};
	}

	protected static function getPeerQuestions(array $allQuestions): array
	{
		return [
			'domain_2_strand_1_q1' => $allQuestions['domain_2_strand_1_q1'],
			'domain_2_strand_2_q1' => $allQuestions['domain_2_strand_2_q1'],
			'domain_2_strand_2_q2' => $allQuestions['domain_2_strand_2_q2'],
			'domain_2_strand_3_q1' => $allQuestions['domain_2_strand_3_q1'],
			'domain_2_strand_3_q2' => $allQuestions['domain_2_strand_3_q2'],
			'domain_3_strand_1_q1' => $allQuestions['domain_3_strand_1_q1'],
			'domain_3_strand_2_q1' => $allQuestions['domain_3_strand_2_q1'],
		];
	}

	protected static function getSelfQuestions(array $allQuestions): array
	{
		return [
			'domain_2_strand_1_q1' => $allQuestions['domain_2_strand_1_q1'],
			'domain_2_strand_2_q1' => $allQuestions['domain_2_strand_2_q1'],
			'domain_2_strand_2_q2' => $allQuestions['domain_2_strand_2_q2'],
			'domain_3_strand_1_q1' => $allQuestions['domain_3_strand_1_q1'],
			'domain_3_strand_2_q1' => $allQuestions['domain_3_strand_2_q1'],
		];
	}

	public static function getAllQuestions(): array
	{
		return [
			'domain_1_strand_1_q1' => [
				'text' => 'The Paulinian Leader organizes/co-organizes and/or serves as resource speaker in seminars and activities for the organization.',
				'domain' => 'Domain 1: Paulinian Leadership as Social Responsibility',
				'strand' => 'Strand 1: Participation in Organization Activities',
			],
			'domain_1_strand_1_q2' => [
				'text' => 'The Paulinian Leader facilitates/co-facilitates seminars and activities for the organization.',
				'domain' => 'Domain 1: Paulinian Leadership as Social Responsibility',
				'strand' => 'Strand 1: Participation in Organization Activities',
			],
			'domain_1_strand_1_q3' => [
				'text' => 'The Paulinian Leader participates in seminars/activities of the organization.',
				'domain' => 'Domain 1: Paulinian Leadership as Social Responsibility',
				'strand' => 'Strand 1: Participation in Organization Activities',
			],
			'domain_1_strand_1_q4' => [
				'text' => 'The Paulinian Leader attends SPUP-organized seminars and activities related to the organization.',
				'domain' => 'Domain 1: Paulinian Leadership as Social Responsibility',
				'strand' => 'Strand 1: Participation in Organization Activities',
			],
			'domain_1_strand_2_q1' => [
				'text' => 'The Paulinian Leader ensures quality in all tasks/assignments given.',
				'domain' => 'Domain 1: Paulinian Leadership as Social Responsibility',
				'strand' => 'Strand 2: Quality of Work',
			],
			'domain_2_strand_1_q1' => [
				'text' => 'The Paulinian Leader performs related tasks outside the given assignment: initiates actions to solve issues among students and those that concern the organization/university; and participates in the aftercare during activities.',
				'domain' => 'Domain 2: Paulinian Leadership as a Life of Service',
				'strand' => 'Strand 1: Initiative and Service',
			],
			'domain_2_strand_2_q1' => [
				'text' => 'The Paulinian Leader shares in the organization\'s management and evaluation of the organization.',
				'domain' => 'Domain 2: Paulinian Leadership as a Life of Service',
				'strand' => 'Strand 2: Management and Evaluation',
			],
			'domain_2_strand_2_q2' => [
				'text' => 'The Paulinian Leader shares in the organization: management and evaluation of projects/activities of the university.',
				'domain' => 'Domain 2: Paulinian Leadership as a Life of Service',
				'strand' => 'Strand 2: Management and Evaluation',
			],
			'domain_2_strand_3_q1' => [
				'text' => 'The Paulinian Leader attends regular meetings.',
				'domain' => 'Domain 2: Paulinian Leadership as a Life of Service',
				'strand' => 'Strand 3: Attendance',
			],
			'domain_2_strand_3_q2' => [
				'text' => 'The Paulinian Leader attends all emergency meetings called.',
				'domain' => 'Domain 2: Paulinian Leadership as a Life of Service',
				'strand' => 'Strand 3: Attendance',
			],
			'domain_3_strand_1_q1' => [
				'text' => 'The Paulinian Leader is a model of grooming and proper decorum.',
				'domain' => 'Domain 3: Paulinian Leader as Leading by Example (Discipline/Decorum)',
				'strand' => 'Strand 1: Grooming and Decorum',
			],
			'domain_3_strand_2_q1' => [
				'text' => 'The Paulinian Leader ensures cleanliness and orderliness of office/workplace.',
				'domain' => 'Domain 3: Paulinian Leader as Leading by Example (Discipline/Decorum)',
				'strand' => 'Strand 2: Cleanliness and Orderliness',
			],
			'length_of_service' => [
				'text' => 'Paulinian Leader had served the Department/University',
				'domain' => 'Other',
				'strand' => 'Other',
			],
		];
	}

	// ========================================
	// SCORE CALCULATION
	// ========================================

	public function calculateScore(): float
	{
		if (!$this->answers || empty($this->answers)) {
			return 0;
		}

		$scores = array_filter($this->answers, 'is_numeric');

		return count($scores) > 0
			? round(array_sum($scores) / count($scores), 3)
			: 0;
	}

	protected static function booted(): void
	{
		static::saving(function (EvaluationForm $form) {
			$form->evaluator_score = $form->calculateScore();
		});

		static::saved(function (EvaluationForm $form) {
			$councilId = $form->evaluation->council_id ?? null;
			EvaluationRank::updateForUser(
				$form->evaluation_id,
				$form->user_id,
				$councilId
			);
		});
	}
}

