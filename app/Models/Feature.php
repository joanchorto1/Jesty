<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Relación con los planes.
     * Muchos a muchos: Un feature puede estar en varios planes.
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_feature');
    }

    /**
     * Relación con los roles.
     * Muchos a muchos: Un feature puede estar disponible para varios roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_feature');
    }
}
