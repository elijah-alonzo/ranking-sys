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
use Illuminate\Database\Eloquent\Builder;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show navigation to admin users
        $user = auth()->user();
        return $user && $user->role === 'admin';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Evaluation Details')
                ->description('View evaluation period and council information')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('council.name')
                            ->label('Council')
                            ->icon('heroicon-m-building-office')
                            ->columnSpan(1),

                        TextEntry::make('adviser.name')
                            ->label('Council Adviser')
                            ->icon('heroicon-m-user')
                            ->color('primary')
                            ->columnSpan(1),

                        TextEntry::make('academic_year')
                            ->label('Academic Year')
                            ->icon('heroicon-m-calendar-days')
                            ->columnSpan(1),
                    ]),
                ])
                ->columnSpanFull(),
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

    public static function getEloquentQuery(): Builder
    {
        // Admin users can see all evaluations
        return parent::getEloquentQuery()->with(['adviser', 'council']);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Only admins can edit evaluations in this resource
        return $user->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        // Only admins can delete evaluations
        return $user && $user->role === 'admin';
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
