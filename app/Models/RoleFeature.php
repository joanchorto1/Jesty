<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleFeature extends Pivot
{
    use HasFactory;

    protected $fillable = ['role_id', 'feature_id'];
}
