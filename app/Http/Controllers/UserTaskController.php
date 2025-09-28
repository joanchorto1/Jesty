<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTask;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserTaskController extends Controller
{


    public function index()
    {
        $company_users = User::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('User_tasks/Index', [
            //tareas de todos los usuarios de la empresa
            'tasks' => UserTask::whereIn('user_id',$company_users->pluck('id'))
                ->with(['user','project'])
                ->orderBy('created_at', 'desc')
                ->get(),
            'users'=>User::where('company_id',Auth::user()->company_id)->get(),
            'projects' => Project::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }
    public function create()
    {
        return Inertia::render('User_tasks/Create', [
            'projects' => Project::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }
    public function adminCreate()
    {
        return Inertia::render('User_tasks/AdminCreate',[
            'users'=>User::where('company_id',Auth::user()->company_id)->get(),
            'projects' => Project::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

            UserTask::create([
               'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => 'pending',
                'user_id'=> Auth::user()->id,
                'project_id' => $request->project_id,
            ]);

        return Inertia::location('/dashboard');
    }
public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

            UserTask::create([
               'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => 'pending',
                'user_id'=> $request->user_id,
                'project_id' => $request->project_id,
            ]);

        return Inertia::location('/user_tasks');
    }


    public function edit(UserTask $userTask)
    {

        return Inertia::render('User_tasks/Edit', [
            'task' => $userTask,
            'projects' => Project::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }
    public function adminEdit(UserTask $userTask)
    {

        return Inertia::render('User_tasks/AdminEdit', [
            'task' => $userTask,
            'users'=>User::where('company_id',Auth::user()->company_id)->get(),
            'projects' => Project::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    public function update(Request $request, UserTask $userTask)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $userTask->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'project_id' => $request->project_id,
        ]);

        return Inertia::location('/dashboard');
    }
    public function adminUpdate(Request $request, UserTask $userTask)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $userTask->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => $request->user_id,
            'project_id' => $request->project_id,
        ]);

        return Inertia::location('/user_tasks');
    }

    public function destroy(UserTask $userTask)
    {
        $userTask->delete();
        $role = Auth::user()->role_id;
        $adimistration_feature_id= \App\Models\Feature::where('name','Administradores')->first()->id;
        $role_features = \App\Models\RoleFeature::where('role_id',$role)->where('feature_id',$adimistration_feature_id)->get();
        if ($role_features->count()>0){
            return Inertia::location('/user_tasks');
        }else{
            return Inertia::location('/dashboard');
        }

    }

    public function markAsCompleted(UserTask $userTask)
    {
        $userTask->update(['status' => 'completed']);

        return Inertia::location('/dashboard');
    }

    public function markAsInProgress(UserTask $userTask)
    {

        $userTask->update(['status' => 'in_progress']);

        return Inertia::location('/dashboard');

    }
    public function show(UserTask $userTask)
    {

        return Inertia::render('User_tasks/Show', [
            'task' => $userTask->load('project'),
        ]);

    }
}
