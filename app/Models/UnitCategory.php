<?php

namespace App\Models;

use App\Traits\Auditable;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UnitCategory extends Model
{
    use HasFactory, SoftDeletes, Auditable;


    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
//
//            $user = auth()->user();
//
//            Notification::make()
//                ->title('New Unit Category')
//                ->icon('heroicon-o-shopping-bag')
//                ->body("**{$model->name} is created.**")
////            ->actions([
////                Action::make('View')
////                    ->url(UnitCategory::getUrl('edit', ['record' => $model])),
////            ])
//                ->sendToDatabase($user)->send();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
