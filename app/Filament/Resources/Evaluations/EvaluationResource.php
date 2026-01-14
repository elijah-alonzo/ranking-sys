<?php

namespace App\Filament\Resources\Evaluations;

use App\Filament\Resources\Evaluations\Pages\CreateEvaluation;
use App\Filament\Resources\Evaluations\Pages\EditEvaluation;
use App\Filament\Resources\Evaluations\Pages\ListEvaluations;
use App\Filament\Resources\Evaluations\Pages\ViewEvaluation;
use App\Filament\Resources\Evaluations\RelationManagers;
use App\Filament\Resources\Evaluations\Schemas\EvaluationForm;
use App\Filament\Resources\Evaluations\Tables\EvaluationsTable;
use App\Models\Evaluation;
use App\Models\EvaluationPeerEvaluator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Section::make('Evaluation Details')
                    ->description('View evaluation period and council information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('council.name')
                                ->label('Council')
                                ->icon('heroicon-m-building-office'),

                            TextEntry::make('academic_year')
                                ->label('Academic Year')
                                ->icon('heroicon-m-calendar-days'),
                        ]),

                        TextEntry::make('adviser.name')
                            ->label('Council Adviser')
                            ->icon('heroicon-m-user')
                            ->color('primary'),
                    ])
                    ->columnSpan(1),

                Section::make('Assigned Peer Evaluators')
                    ->description('Students assigned to conduct peer evaluations')
                    ->schema([
                        RepeatableEntry::make('peer_evaluators')
                            ->label('Students')
                            ->state(function ($record) {
                                // Get all unique peer evaluators for this evaluation
                                $peerEvaluators = EvaluationPeerEvaluator::where('evaluation_id', $record->id)
                                    ->with(['evaluatorUser'])
                                    ->get()
                                    ->groupBy('evaluator_user_id');

                                if ($peerEvaluators->isEmpty()) {
                                    return [['no_evaluators' => true]];
                                }

                                return $peerEvaluators->map(function ($assignments, $evaluatorId) use ($record) {
                                    $evaluator = $assignments->first()->evaluatorUser;
                                    if (!$evaluator) return null;

                                    // Get the user's position in this evaluation
                                    $position = $record->users()
                                        ->where('user_id', $evaluatorId)
                                        ->first()
                                        ?->pivot
                                        ?->position ?? 'No position assigned';

                                    return [
                                        'pfp' => $evaluator->pfp,
                                        'name' => $evaluator->name,
                                        'position' => $position,
                                        'evaluatee_count' => $assignments->count(),
                                    ];
                                })->filter()->values()->toArray();
                            })
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->weight('semibold')
                                    ->formatStateUsing(fn ($state, $record) =>
                                        isset($record['no_evaluators']) ?
                                        'No peer evaluators assigned yet' :
                                        $state
                                    )
                                    ->color(fn ($record) =>
                                        isset($record['no_evaluators']) ? 'gray' : 'primary'
                                    )
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => isset($record['no_evaluators'])),

                                Grid::make(4)
                                    ->schema([
                                        ImageEntry::make('pfp')
                                            ->hiddenLabel()
                                            ->circular()
                                            ->size(40)
                                            ->defaultImageUrl(fn ($state, $record) =>
                                                'https://ui-avatars.com/api/?name=' .
                                                urlencode($record['name'] ?? 'Unknown') .
                                                '&color=7F9CF5&background=EBF4FF'
                                            ),

                                        TextEntry::make('name')
                                            ->hiddenLabel(),

                                        TextEntry::make('position')
                                            ->hiddenLabel()
                                            ->color('primary')
                                            ->badge(),

                                        TextEntry::make('evaluatee_count')
                                            ->hiddenLabel()
                                            ->suffix(fn ($state) => $state == 1 ? ' student' : ' students')
                                            ->color('success')
                                            ->weight('medium')
                                            ->icon('heroicon-m-users'),
                                    ])
                                    ->visible(fn ($record) => !isset($record['no_evaluators'])),
                            ])
                            ->contained(false)
                            ->grid(1),
                    ])
                    ->columnSpan(1),
            ])->columnSpanFull(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return EvaluationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluations::route('/'),
            'create' => CreateEvaluation::route('/create'),
            'view' => ViewEvaluation::route('/{record}'),
            'edit' => EditEvaluation::route('/{record}/edit'),
        ];
    }
}
