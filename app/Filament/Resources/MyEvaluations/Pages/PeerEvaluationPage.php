<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class PeerEvaluationPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = MyEvaluationResource::class;
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    public ?Evaluation $evaluation = null;
    public ?User $evaluatee = null;
    public ?EvaluationForm $existingForm = null;

    public function mount(): void
    {
        $evaluationId = request('evaluation');
        $evaluateeId = request('user');

        if (!$evaluationId || !$evaluateeId) {
            abort(404);
        }

        $this->evaluation = Evaluation::findOrFail($evaluationId);
        $this->evaluatee = User::findOrFail($evaluateeId);
        
        // Check permissions - user must be assigned as peer evaluator
        if (!EvaluationPeerEvaluator::canEvaluateAsPeer(
            $this->evaluation->id, 
            auth()->id(), 
            $this->evaluatee->id
        )) {
            abort(403, 'You are not authorized to evaluate this user as a peer.');
        }

        // Check for existing form
        $this->existingForm = EvaluationForm::where([
            'evaluation_id' => $this->evaluation->id,
            'user_id' => $this->evaluatee->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => auth()->id(),
        ])->first();

        // Pre-fill form if exists
        if ($this->existingForm) {
            $this->data = $this->existingForm->answers ?? [];
        }

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->buildEvaluationFormSchema('peer', 'Please rate each statement based on your observation and interaction'))
            ->statePath('data');
    }

    private function buildEvaluationFormSchema(string $evaluatorType, string $description): array
    {
        $questions = EvaluationForm::getQuestionsForEvaluator($evaluatorType);
        $sections = [];
        
        // Group questions by domain
        $groupedQuestions = [];
        foreach ($questions as $key => $question) {
            $domain = $question['domain'];
            $groupedQuestions[$domain][$key] = $question;
        }
        
        foreach ($groupedQuestions as $domain => $domainQuestions) {
            $fields = [];
            
            foreach ($domainQuestions as $key => $question) {
                $fields[] = Radio::make($key)
                    ->label($question['text'])
                    ->options([
                        '0' => '0 - Never/Poor',
                        '1' => '1 - Sometimes/Fair',
                        '2' => '2 - Often/Good',
                        '3' => '3 - Always/Excellent'
                    ])
                    ->inline()
                    ->required()
                    ->columnSpanFull();
            }
            
            $sections[] = Section::make($domain)
                ->description($description)
                ->schema($fields)
                ->collapsible()
                ->columns(1);
        }
        
        return $sections;
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        
        // Validate all questions answered
        $questions = EvaluationForm::getQuestionsForEvaluator('peer');
        foreach (array_keys($questions) as $questionKey) {
            if (!isset($data[$questionKey]) || $data[$questionKey] === '') {
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
                    'evaluator_type' => 'peer',
                    'evaluator_id' => auth()->id(),
                ],
                [
                    'answers' => $data,
                ]
            );

            Notification::make()
                ->title('Peer Evaluation Submitted Successfully')
                ->body('Your peer evaluation has been recorded and scoring calculated.')
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
        return "Peer Evaluation for {$this->evaluatee->name}";
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url(MyEvaluationResource::getUrl('view', ['record' => $this->evaluation])),
        ];
    }

    public static function getRouteName(?\Filament\Panel $panel = null): string
    {
        return 'peer-evaluation';
    }
}