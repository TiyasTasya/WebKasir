<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'nama')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
