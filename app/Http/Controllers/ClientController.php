<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();

        return Inertia::render('Client/Index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        return Inertia::render('Create');
    }

    public function store(CreateClientRequest $request)
    {
        $data = $request->validated();
        Client::create($data);

        return redirect()->route('clients.index')
            ->with('success', 'Client created!');
    }

    public function show(Client $client)
    {
        $projects = $client->projects()->get();

        return view('clients.show', [
            'client' => $client,
            'projects' => $projects
        ]);
    }

    public function edit(Client $client)
    {
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated successfully');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully');
    }
}
