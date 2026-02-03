<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $canManageClients = auth()->user()->can('manage clients');

        $clients = Client::all();

        return Inertia::render('Clients/Index', [
            'can' => [
                'manage_clients' => $canManageClients,
            ],
            'clients' => ClientResource::collection($clients)
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(CreateClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Client created!');
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => new ClientResource($client)
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()->route('clients.index', $client)
            ->with('success', 'Client updated successfully');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully');
    }
}
