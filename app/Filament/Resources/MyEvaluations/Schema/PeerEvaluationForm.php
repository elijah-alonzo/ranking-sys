<?php

namespace App\Filament\Resources\MyEvaluations\Schema;

use App\Models\EvaluationForm as EvaluationFormModel;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;

class PeerEvaluationForm
{
    public static function getSchema(): array
    {
        $questions = EvaluationFormModel::getQuestionsForEvaluator('peer');
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
                ->description('Please rate each statement based on your peer\'s performance')
                ->schema($fields)
                ->collapsible()
                ->columns(1);
        }
        
        return $sections;
    }
}