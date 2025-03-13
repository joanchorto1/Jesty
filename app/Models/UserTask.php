<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'status', 'user_id'];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
