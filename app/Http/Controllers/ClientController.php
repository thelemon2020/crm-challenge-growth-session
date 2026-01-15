<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();

        return view('clients.index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
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

    public function update(Request $request, Client $client)
    {
    }

    public function destroy(Client $client)
    {
    }
}
