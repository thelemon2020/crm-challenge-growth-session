<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
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
        return Inertia::render('Project/Create');
    }

    public function store(StoreProjectRequest $request)
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
