<?php

namespace App\Filament\Clusters\UserManagements\Resources;

use App\Filament\Clusters\UserManagements;
use App\Filament\Clusters\UserManagements\Resources\RoleResource\Pages;
use App\Filament\Clusters\UserManagements\Resources\RoleResource\RelationManagers;
use App\Models\Feature;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Role and Permissions';

    protected static ?string $cluster = UserManagements::class;

    public static function form(Form $form): Form
    {
        // Fetch features and their permissions
        $features = Feature::with('permissions')->get();

        // Structure the permissions with feature names included
        $permissionsOptions = [];
        foreach ($features as $feature) {
            foreach ($feature->permissions as $permission) {
                $permissionsOptions[$permission->id] = new HtmlString("<strong>{$feature->name}:</strong> {$permission->name}");
            }
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Role Name')
                    ->required()
                    ->maxLength(255),


                Forms\Components\CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->options($permissionsOptions)
                    ->bulkToggleable()
                    ->searchable()
                    ->columns(3)
                    ->columnSpanFull()
                    ->selectAllAction(
                        fn (Action $action) => $action->label('Select all permissions'),
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
