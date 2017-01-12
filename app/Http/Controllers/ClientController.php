<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $clients = Client::all()->sortBy('name');

        return view('client.index')->with(compact('clients'));
    }

    public function search(Request $search)
    {
        $clients = Client::where('name','=',$search->search)
                        ->orWhere('email', '=', $search->search)
                        ->orWhere('phone_number', '=', $search->search)
                        ->get();
        return view('client.search', compact('clients', 'search'));        
    }

    public function create()
    {
        return view('client.create');
    }

    public function store(CreateClientRequest $request)
    {
        Client::create([
            'name'          => $request->input('name'),
            'phone_number'  => $request->input('phone_number'),
            'email'         => $request->input('email'),
        ]);

        return redirect('client');
    }

    public function edit($clientId)
    {
        $client = Client::find($clientId);

        return view('client.create')->with(compact('client'));
    }

    public function update(UpdateClientRequest $request, $clientId)
    {
        $client = Client::find($clientId);

        $client->name           = $request->input('name');
        $client->email          = $request->input('email');
        $client->phone_number   = $request->input('phone_number');

        $client->save();

        Session::flash('success', 'Client successfully updated');

        return redirect()->back();
    }

    public function destroy($clientId)
    {
        $client = Client::find($clientId);

        $client->delete();

        return redirect('client');

    }
}
