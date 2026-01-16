<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Unified Evaluate Student Page
 *
 * Provides dynamic evaluation forms based on evaluator type (adviser, peer, self).
 * Uses custom Blade views for clean evaluation forms.
 */
class EvaluateStudentPage extends Page
{
    protected static string $resource = MyEvaluationResource::class;
    protected static bool $shouldRegisterNavigation = false;
    
    // Use the main evaluation partial as the view (adviser/peer/self will be handled in the view logic)
    protected string $view = 'EvaluationForm.AdviserEvaluation';

    public ?Evaluation $evaluation = null;
    public ?User $evaluatee = null;
    public string $evaluationType = '';
    public ?EvaluationForm $existingForm = null;
    public bool $isLocked = false;
    public array $data = [];
    public array $questions = [];

    /**
     * Mount the evaluation page with explicit model binding for evaluation, user, and type.
     *
     * @param Evaluation $evaluation
     * @param User $user
     * @param string $type
     */
    public function mount(Evaluation $evaluation, User $user, string $type): void
    {
        // Handle POST submission
        if (request()->isMethod('post')) {
            $this->evaluation = $evaluation;
            $this->evaluatee = $user;
            $this->evaluationType = $type;
            $this->handleFormSubmission();
            return;
        }

        $this->evaluation = $evaluation;
        $this->evaluatee = $user;
        $this->evaluationType = $type;

        if (!$this->evaluation) {
            abort(404, 'Evaluation not found');
        }

        if (!$this->evaluatee) {
            abort(404, 'User not specified');
        }

        // Validate permissions based on evaluation type
        $this->validatePermissions();

        // Load questions for this evaluator type
        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);

        // Load existing evaluation if any
        $this->loadExistingEvaluation();

        // Lock form if already submitted
        $this->isLocked = $this->existingForm !== null;

        // Pre-fill form data if exists
        if ($this->existingForm) {
            $this->data = $this->existingForm->answers ?? [];
        }
    }

    protected function handleFormSubmission(): void
    {
        $this->evaluationType = request('type', 'self');
        $this->validatePermissions();
        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);
        $this->loadExistingEvaluation();
        $this->isLocked = $this->existingForm !== null;
        
        // Now handle the submission
        $this->submit();
    }

    protected function validatePermissions(): void
    {
        $user = auth()->user();
        
        switch ($this->evaluationType) {
            case 'adviser':
                if ($this->evaluation->council_adviser_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate as an adviser.');
                }
                break;
                
            case 'peer':
                if (!EvaluationPeerEvaluator::canEvaluateAsPeer(
                    $this->evaluation->id, 
                    $user->id, 
                    $this->evaluatee->id
                )) {
                    abort(403, 'You are not authorized to evaluate this user as a peer.');
                }
                break;
                
            case 'self':
                if ($this->evaluatee->id !== $user->id) {
                    abort(403, 'You can only perform self-evaluation on your own record.');
                }
                
                $isParticipating = $this->evaluation->users()
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$isParticipating) {
                    abort(403, 'You are not participating in this evaluation.');
                }
                break;
                
            default:
                abort(404, 'Invalid evaluation type');
        }
    }

    protected function loadExistingEvaluation(): void
    {
        $query = EvaluationForm::where([
            'evaluation_id' => $this->evaluation->id,
            'user_id' => $this->evaluatee->id,
            'evaluator_type' => $this->evaluationType,
        ]);

        if ($this->evaluationType === 'peer') {
            $query->where('evaluator_id', auth()->id());
        } else {
            $query->where(function ($q) {
                $q->whereNull('evaluator_id')
                  ->orWhere('evaluator_id', auth()->id());
            });
        }

        $this->existingForm = $query->first();
    }

    public function submit(): void
    {
        if ($this->isLocked) {
            Notification::make()
                ->title('Evaluation Already Submitted')
                ->body('This evaluation has already been submitted and cannot be edited.')
                ->warning()
                ->send();
            return;
        }

        $answers = request('answers', []);
        
        // Validate all questions answered
        foreach (array_keys($this->questions) as $questionKey) {
            if (!isset($answers[$questionKey]) || $answers[$questionKey] === '') {
                Notification::make()
                    ->title('Please answer all questions')
                    ->body('All evaluation questions must be answered before submitting.')
                    ->danger()
                    ->send();
                return;
            }
        }

        try {
            EvaluationForm::updateOrCreate(
                [
                    'evaluation_id' => $this->evaluation->id,
                    'user_id' => $this->evaluatee->id,
                    'evaluator_type' => $this->evaluationType,
                    'evaluator_id' => $this->evaluationType === 'peer' ? auth()->id() : null,
                ],
                [
                    'answers' => $answers,
                ]
            );

            $evaluationTypeLabel = ucfirst($this->evaluationType);
            
            Notification::make()
                ->title("{$evaluationTypeLabel} Evaluation Submitted Successfully")
                ->body('Your evaluation has been recorded and scoring calculated.')
                ->success()
                ->send();

            $this->redirect(MyEvaluationResource::getUrl('view', ['record' => $this->evaluation]));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Submitting Evaluation')
                ->body('There was an error saving your evaluation. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string|Htmlable
    {
        $evaluationTypeLabel = match ($this->evaluationType) {
            'adviser' => 'Adviser',
            'peer' => 'Peer',
            'self' => 'Self',
            default => 'Unknown'
        };

        $targetName = $this->evaluationType === 'self' ? 'Yourself' : ($this->evaluatee->name ?? 'Unknown');
        
        return "{$evaluationTypeLabel} Evaluation for {$targetName}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        $councilName = $this->evaluation->council->name ?? 'Council';
        $academicYear = $this->evaluation->academic_year ?? 'Unknown Year';
        
        return "Council: {$councilName} | Academic Year: {$academicYear}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Evaluation')
                ->url(MyEvaluationResource::getUrl('view', ['record' => $this->evaluation]))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    public static function getRouteName(?\Filament\Panel $panel = null): string
    {
        return 'evaluate-student';
    }
}