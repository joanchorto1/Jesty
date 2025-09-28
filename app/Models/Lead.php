<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_name', 'email', 'phone', 'position', 'source', 'status', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }
}
