<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
