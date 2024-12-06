<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    /**
     * Relación con las features.
     * Muchos a muchos: Un plan puede tener múltiples features.
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_feature');
    }

    /**
     * Relación con las compañías.
     * Uno a muchos: Un plan puede estar asociado a varias compañías.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
