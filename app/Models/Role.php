<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'company_id'];

    /**
     * Relación con las compañías.
     * Un rol pertenece a una compañía.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación con los usuarios.
     * Un rol puede estar asociado a varios usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con las features.
     * Muchos a muchos: Un rol puede tener acceso a múltiples features.
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'role_feature');
    }
}
