<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'opportunity_id', 'project_id', 'title', 'description', 'due_date', 'status', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
