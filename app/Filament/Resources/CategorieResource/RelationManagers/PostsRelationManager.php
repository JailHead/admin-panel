<?php

namespace App\Filament\Resources\CategorieResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Update your post')
                ->description('Put the new information and save it')
                ->schema([
                    TextInput::make('title')->rules(['min:3'])->required(),
                    TextInput::make('slug')->required(),                       

                    ColorPicker::make('color')->required(),
                    MarkdownEditor::make('content')->required()->columnSpan(2),
                ])->columnSpan(2)->columns(2),

                Group::make()
                    ->schema([
                        Section::make('Image')
                            ->collapsible()
                            ->description('Put a image to make alive your post')
                            ->schema([
                                FileUpload::make('thumbnail')->nullable()->disk('public')
                                    ->directory('thumbnails'),
                            ]),

                        Section::make('Meta')
                            ->collapsible()
                            ->description('Publish your post or not')
                            ->schema([
                                TagsInput::make('tags'),
                                Checkbox::make('published')
                            ])
                    ]),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),                
                Tables\Columns\CheckboxColumn::make('published'),                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
