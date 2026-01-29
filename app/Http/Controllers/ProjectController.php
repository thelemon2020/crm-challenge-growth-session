<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatusEnum;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = null;

        if (auth()->user()->can('manage projects')) {
            $projects = Project::all();
        } else if (auth()->user()->can('view own projects')) {
            $projects = Project::where('user_id', auth()->id())->get();
        }

        return Inertia::render('Project/Index', [
            'projects' => ProjectResource::collection($projects)
        ]);
    }

    public function create()
    {
        $isAdmin = auth()->user()->can('manage projects');

        $users = UserResource::collection(User::all());
        $clients = $isAdmin ? ClientResource::collection(Client::all()) : ClientResource::collection(Client::where('user_id', auth()->id())->get());

        return Inertia::render('Project/Create', [
            'users' => $users,
            'clients' => $clients,
            'status' => ProjectStatusEnum::cases()
        ]);
    }

    public function store(CreateProjectRequest $request)
    {
        Project::create($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return $project;
    }

    public function edit(Project $project)
    {
        if (auth()->user()->cannot('manage projects') && $project->user->id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Project/Edit', [
            'project' => new ProjectResource($project)
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([

        ]);

        $project->update($data);

        return $project;
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json();
    }
}
