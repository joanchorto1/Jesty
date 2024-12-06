<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\TaskController;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Opportunity;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['route.features.access:4'])->group(function () {


    Route::get('dashboard/crm', function () {
        $company = Company::where('id', Auth::user()->company_id)->first();
        return Inertia::render('DashboardCrm', [
            'leads' => Lead::where('company_id', $company->id)->get(),
            'opportunities' => Opportunity::where('company_id', $company->id)->get(),
            'activities' => Activity::where('company_id', $company->id)->get(),
            'notes' => Note::where('company_id', $company->id)->get(),
            'tasks' => Task::where('company_id', $company->id)->get(),
        ]);
    })->name('dashboard.crm');

    Route::get('/crm/all', function () {
        $company = Company::where('id', Auth::user()->company_id)->first();
        return Inertia::render('CRM/VistaUnificada', [
            'leads' => Lead::where('company_id', $company->id)->get(),
            'opportunities' => Opportunity::where('company_id', $company->id)->get(),
            'activities' => Activity::where('company_id', $company->id)->get(),
            'notes' => Note::where('company_id', $company->id)->get(),
            'tasks' => Task::where('company_id', $company->id)->get(),
        ]);
    })->name('crm.all');


    Route::middleware(['auth'])->group(function () {
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::post('/leads/{lead}/convertToClient', [LeadController::class, 'convertToClient'])->name('leads.convertToClient');
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
        Route::get('/opportunities/create', [OpportunityController::class, 'create'])->name('opportunities.create');
        Route::get('/opportunities/create/{lead}', [OpportunityController::class, 'createWithLead'])->name('opportunities.createWithLead');
        Route::get('/opportunities/gotocreate/{lead}', [OpportunityController::class, 'goToCreateWL'])->name('opportunities.goToCreateWL');
        Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
        Route::get('/opportunities/{opportunity}', [OpportunityController::class, 'show'])->name('opportunities.show');
        Route::get('/opportunities/{opportunity}/edit', [OpportunityController::class, 'edit'])->name('opportunities.edit');
        Route::get('/opportunities/{opportunity}/goToEdit', [OpportunityController::class, 'goToEdit'])->name('opportunities.goToEdit');
        Route::put('/opportunities/{opportunity}', [OpportunityController::class, 'update'])->name('opportunities.update');
        Route::delete('/opportunities/{opportunity}', [OpportunityController::class, 'destroy'])->name('opportunities.destroy');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/create/{lead}/{opportunity}', [ActivityController::class, 'create'])->name('activities.create');
        Route::get('/activities/gotocreate/{lead}/{opportunity}', [ActivityController::class, 'goToCreate'])->name('activities.goToCreate');
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::get('/activities/{activity}/goToEdit', [ActivityController::class, 'goToEdit'])->name('activities.goToEdit');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
        Route::get('/notes/create/{opportunity}/{lead}', [NoteController::class, 'create'])->name('notes.create');
        Route::get('/notes/gotocreate/{opportunity}/{lead}', [NoteController::class, 'goToCreate'])->name('notes.goToCreate');
        Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
        Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
        Route::get('/notes/{note}/goToEdit', [NoteController::class, 'goToEdit'])->name('notes.goToEdit');
        Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

}); // Route middleware

