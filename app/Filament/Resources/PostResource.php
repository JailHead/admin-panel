<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Categorie;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Create new post')
                ->tabs([
                    Tab::make('Title')
                    ->icon('heroicon-o-document-minus')
                    ->schema([
                        TextInput::make('title')->rules(['min:3'])->required(),
                        TextInput::make('slug')->required(),
                        Select::make('categorie_id')
                        ->label('Category')
                        ->searchable()
                        ->relationship('categorie', 'name'),
                        ColorPicker::make('color')->required(),
                    ]),

                    Tab::make('Message')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([                        
                        MarkdownEditor::make('content')->required()->columnSpan(2),
                    ]),

                    Tab::make('Meta')
                    ->schema([
                        FileUpload::make('thumbnail')->nullable()->disk('public')
                        ->directory('thumbnails'),
                        TagsInput::make('tags'),
                        Checkbox::make('published')
                    ]),

                ])->columnSpanFull()->activeTab(2)->persistTabInQueryString(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->toggleable(isToggledHiddenByDefault:true),
                ImageColumn::make('thumbnail'),
                ColorColumn::make('color'),
                TextColumn::make('title')
                ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('slug')
                ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('categorie.name'),
                TextColumn::make('tags'),
                TextColumn::make('created_at')
                ->label('Posted at')
                ->date('Y M')
                ->sortable()                
                ->searchable()
                ->toggleable(isToggledHiddenByDefault:true),
                CheckboxColumn::make('published'),
            ])
            ->filters([
                // Filter::make('Published posts')->query(
                //     function ($query){
                //         return $query->where('published', true);
                //     }
                // ),
                // Filter::make('Unpublished posts')->query(
                //     function ($query){
                //         return $query->where('published', false);
                //     }
                // ),
                TernaryFilter::make('published'),
                SelectFilter::make('categorie_id')
                ->label('Category')                
                ->options(Categorie::all()->pluck('name', 'id'))
                ->preload()
                ->searchable()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
