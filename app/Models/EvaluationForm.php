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


	/**
	 * Returns a flat array of questions for the given evaluator type, preserving the old interface.
	 * Each question includes: text, domain, strand, criteria, and a unique key.
	 */
	public static function getQuestionsForEvaluator(string $evaluatorType): array
	{
		$rubric = self::getRubricStructure();
		$questionKeys = match ($evaluatorType) {
			'adviser' => self::getAllQuestionKeys($rubric),
			'peer' => [
				['Domain 2','Strand 1','1.1'],
				['Domain 2','Strand 2','2.1'],
				['Domain 2','Strand 2','2.2'],
				['Domain 2','Strand 3','3.1'],
				['Domain 2','Strand 3','3.2'],
				['Domain 3','Strand 1','1.1'],
				['Domain 3','Strand 2','2.1'],
			],
			'self' => [
				['Domain 2','Strand 1','1.1'],
				['Domain 2','Strand 2','2.1'],
				['Domain 2','Strand 2','2.2'],
				['Domain 3','Strand 1','1.1'],
				['Domain 3','Strand 2','2.1'],
			],
			default => [],
		};

		$questions = [];
		foreach ($questionKeys as $key) {
			// For adviser, $key is [domain, strand, qkey]; for others, same
			if (is_string($key)) {
				// For adviser, get all keys
				$parts = explode('|', $key);
				if (count($parts) !== 3) continue;
				[$domain, $strand, $qkey] = $parts;
			} else {
				[$domain, $strand, $qkey] = $key;
			}
			if (isset($rubric[$domain]['strands'][$strand]['questions'][$qkey])) {
				$q = $rubric[$domain]['strands'][$strand]['questions'][$qkey];
				$questions["{$domain}|{$strand}|{$qkey}"] = [
					'text' => $q['text'],
					'domain' => $rubric[$domain]['title'],
					'domain_key' => $domain,
					'domain_description' => $rubric[$domain]['description'],
					'strand' => $rubric[$domain]['strands'][$strand]['title'],
					'strand_key' => $strand,
					'criteria' => $q['criteria'],
					'qkey' => $qkey,
				];
			}
		}
		return $questions;
	}

	/**
	 * Returns all question keys in the rubric as [domain|strand|qkey] strings.
	 */
	protected static function getAllQuestionKeys(array $rubric): array
	{
		$keys = [];
		foreach ($rubric as $domainKey => $domain) {
			foreach ($domain['strands'] as $strandKey => $strand) {
				foreach ($strand['questions'] as $qkey => $q) {
					$keys[] = "$domainKey|$strandKey|$qkey";
				}
			}
		}
		return $keys;
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

	public static function getRubricStructure(): array
	{
		// This structure is based on evaluation-contents.md and houses all domains, strands, questions, descriptions, and rating criteria.
		return [
			'Domain 1' => [
				'title' => 'Paulinian Leadership as Social Responsibility',
				'description' => "This focuses on the account that Paulinian Leaders demonstrate good leadership in the activities of the organization, of the university, and of their respective community. This domain is further exemplified by the Paulinian Leaders' plans, actions, accomplishments, and social interaction with the sisters, administrators, faculty, and their fellow students.",
				'strands' => [
					'Strand 1' => [
						'title' => 'The Paulinian Leader submits himself/herself to professional growth and development',
						'questions' => [
							'1.1' => [
								'text' => 'The Paulinian Leader organizes/co-organizes and/or serves as resource speaker in seminars and activities for the organization.',
								'criteria' => [
									3 => 'Has organized/co-organized more than two seminars/activities',
									2 => 'Has organized/co-organized two seminars/activities',
									1 => 'Has organized/co-organized one seminar/activity',
									0 => 'Has not organized/co-organized seminars/activities',
								],
							],
							'1.2' => [
								'text' => 'The Paulinian Leader facilitates/co-facilitates seminars and activities for the organization.',
								'criteria' => [
									3 => 'Has facilitated/co-facilitated more than two seminars/activities',
									2 => 'Has facilitated/co-facilitated two seminars/activities',
									1 => 'Has facilitated/co-facilitated one seminar/activity',
									0 => 'Has not facilitated/co-facilitated seminars/activities',
								],
							],
							'1.3' => [
								'text' => 'The Paulinian Leader participates in seminars/activities of the organization.',
								'criteria' => [
									3 => 'Has participated in more than four seminars/activities',
									2 => 'Has participated in three to four seminars/activities',
									1 => 'Has participated in one to two seminars/activities',
									0 => 'Has not participated in any seminars/activities',
								],
							],
							'1.4' => [
								'text' => 'The Paulinian Leader attends SPUP-organized seminars and activities related to the organization.',
								'criteria' => [
									3 => 'Has attended more than four seminars/activities',
									2 => 'Has attended three to four seminars/activities',
									1 => 'Has attended one to two seminars/activities',
									0 => 'Has not attended any seminars/activities',
								],
							],
						],
					],
					'Strand 2' => [
						'title' => 'The Paulinian Leader is quality result-oriented',
						'questions' => [
							'2.1' => [
								'text' => 'The Paulinian Leader ensures quality in all tasks/assignments given.',
								'criteria' => [
									3 => 'Performs outstanding/excellent on task/assignments',
									2 => 'Performs very satisfactory on task/assignments',
									1 => 'Performs satisfactory on task/assignments',
									0 => 'Performs but needs improvement on task/assignments',
								],
							],
						],
					],
				],
			],
			'Domain 2' => [
				'title' => 'Paulinian Leadership as a Life of Service',
				'description' => "This gears towards the fulfillment of the Paulinian Leaders' active and utmost involvement in the organization, management, and evaluation of the activities of the organization, university, and community. In this domain, voluntary and special services rendered are notable manifestations of accomplishments.",
				'strands' => [
					'Strand 1' => [
						'title' => 'The Paulinian Leader serves the organization, its members, and the university',
						'questions' => [
							'1.1' => [
								'text' => 'The Paulinian Leader: a. performs related tasks outside the given assignment; b. initiates actions to solve issues among students and those that concern the organization/university; c. participates in the aftercare during activities.',
								'criteria' => [
									3 => 'All three indicators are met',
									2 => 'Only two of the given indicators are met',
									1 => 'Only one of the given indicators is met',
									0 => 'None of the indicators is met',
								],
							],
						],
					],
					'Strand 2' => [
						'title' => 'The Paulinian Leader actively participates in the activities of the organization and university',
						'questions' => [
							'2.1' => [
								'text' => 'The Paulinian Leader shares in the organization\'s management and evaluation of the organization.',
								'criteria' => [
									3 => 'Has participated in three or more varied organizational activities',
									2 => 'Has participated in two varied organizational activities',
									1 => 'Has participated in one organizational activity',
									0 => 'Has not participated in any organizational activity',
								],
							],
							'2.2' => [
								'text' => 'The Paulinian Leader shares in the organization, management, and evaluation of projects/activities of the university.',
								'criteria' => [
									3 => 'Has participated in three or more varied university activities',
									2 => 'Has participated in two varied university activities',
									1 => 'Has participated in one university activity',
									0 => 'Has not participated in any university activity',
								],
							],
						],
					],
					'Strand 3' => [
						'title' => 'The Paulinian Leader shows utmost commitment by participating in related activities',
						'questions' => [
							'3.1' => [
								'text' => 'The Paulinian Leader attends regular meetings.',
								'criteria' => [
									3 => 'Has attended 100% of regular meetings',
									2 => 'Has attended 90%-99% of regular meetings',
									1 => 'Has attended 80%-89% of regular meetings',
									0 => 'Has attended less than 79% of regular meetings',
								],
							],
							'3.2' => [
								'text' => 'The Paulinian Leader attends all emergency meetings called.',
								'criteria' => [
									3 => 'Has attended 90%-100% of all meetings',
									2 => 'Has attended 80%-89% of all meetings',
									1 => 'Has attended 70%-79% of all meetings',
									0 => 'Has attended less than 70% of all meetings',
								],
							],
						],
					],
				],
			],
			'Domain 3' => [
				'title' => 'Paulinian Leader as Leading by Example (Discipline/Decorum)',
				'description' => "This refers to how the Paulinian Leaders conform to Paulinian norms and conduct. It is reflected in their fidelity to policies on decorum, proper grooming, as well as showing Paulinian traits—promptness, warmth, simplicity, proactiveness and hospitality—to people they are dealing with. Their compliance to the Environmental Stewardship advocacy of the university is also notably assessed.",
				'strands' => [
					'Strand 1' => [
						'title' => 'The Paulinian Leader is a model of grooming and proper decorum',
						'questions' => [
							'1.1' => [
								'text' => 'The Paulinian Leader: a. wears the correct uniform with its prescribed accessories (shoes, ID strap, undergarment, and bag); b. wears ID at all times while on campus; c. observes Silence Policy corridors/offices; d. shows courtesy to the SPUP community; e. shows warmth and respect to visitors and guests of the University; f. models prescribed haircut (male) or hairstyle and accessories (female); g. exhibits punctuality during meeting and activities.',
								'criteria' => [
									3 => 'All seven indicators are met',
									2 => 'All first three indicators and any two of the remaining indicators are met',
									1 => 'Only the first three indicators are met',
									0 => 'Any of the indicators are not met',
								],
							],
							'1.2' => [
								'text' => 'The Paulinian Leader submits reports regularly.',
								'criteria' => [
									3 => 'Complete and before deadline',
									2 => 'Complete and on the deadline',
									1 => 'Complete but after deadline/incomplete but on the deadline',
									0 => 'Incomplete and after deadline / not submitted any reports',
								],
							],
						],
					],
					'Strand 2' => [
						'title' => 'The Paulinian Leader ensures cleanliness and orderliness of office/workplace',
						'questions' => [
							'2.1' => [
								'text' => 'The Paulinian Leader ensures cleanliness and orderliness of office/workplace.',
								'criteria' => [
									3 => 'Clean beyond schedule and without being told',
									2 => 'Cleans only on schedule upon a command',
									1 => 'Joins cleaning but comes in late',
									0 => 'Never cleans at all',
								],
							],
						],
					],
				],
			],
			'Length of Service' => [
				'title' => 'Length of Service',
				'description' => null,
				'strands' => [
					'Service' => [
						'title' => 'Service',
						'questions' => [
							'service' => [
								'text' => 'Paulinian Leader had served the Department/University',
								'criteria' => [
									3 => '3 years and up',
									2 => '2 years',
									1 => '1 year',
									0 => 'Did not finish the term',
								],
							],
						],
					],
				],
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

