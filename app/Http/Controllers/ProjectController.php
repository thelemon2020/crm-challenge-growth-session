<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatusEnum;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = null;

        if (auth()->user()->can('manage projects')) {
            $projects = Project::with(['client', 'user'])->latest()->get();
        } else if (auth()->user()->can('view own projects')) {
            $projects = Project::with(['client', 'user'])->where('user_id', auth()->id())->latest()->get();
        }

        return Inertia::render('Projects/Index', [
            'can' => [
                'delete_project' => auth()->user()->can('manage projects')
            ],
            'projects' => ProjectResource::collection($projects)
        ]);
    }

    public function create()
    {
        $isAdmin = auth()->user()->can('manage projects');

        $users = UserResource::collection(User::all());
        $clients = $isAdmin ? ClientResource::collection(Client::all()) : ClientResource::collection(Client::where('user_id', auth()->id())->get());
        $projectStatuses = collect(ProjectStatusEnum::cases())->map(function ($enum) {
            return [
                'label' => $enum->name,
                'value' => $enum->value,
            ];
        });

        return Inertia::render('Projects/Create', [
            'users' => $users,
            'clients' => $clients,
            'projectStatuses' => $projectStatuses
        ]);
    }

    public function store(CreateProjectRequest $request)
    {
        $validated = $request->validated();

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return $project;
    }

    public function edit(Project $project)
    {
        $users = UserResource::collection(User::all());
        $clients = ClientResource::collection(Client::all());
        $projectStatuses = collect(ProjectStatusEnum::cases())->map(function ($enum) {
            return [
                'label' => $enum->name,
                'value' => $enum->value,
            ];
        });

        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
            'users' => $users,
            'clients' => $clients,
            'projectStatuses' => $projectStatuses,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->safe()->except(['file']));

        if ($request->file('file')) {
           $this->storeFile($request->file('file'), $project);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if (auth()->user()->cannot('manage projects')) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    private function storeFile(UploadedFile $file, Project $project)
    {
        $isStored = Storage::disk('local')
            ->put($file->getClientOriginalName(), $file);

        if (! $isStored) {
            Log::error('File could not be stored.', [
                'file' => $file->getClientOriginalName()
            ]);
        }

        $project->files()->create([
            'disk' => 'local',
            'path' => 'temp/' . $file->path(),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
