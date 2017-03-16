<?php

namespace App\Http\Controllers;

use App\Uniform;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\UniformRequest;
use Illuminate\Support\Facades\Session;
use DateTime;

class UniformController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin', [
            'only' => ['index']
        ]);
    }

    public function index()
    {
    	$uniforms = Uniform::All();
    	return view('uniforms.index')->with(compact('uniforms'));
    }

    public function show($uniformId)
    {
    	$uniform = Uniform::find($uniformId);
    	return view('uniforms.show')->with(compact('uniform'));
    }

    public function create()
    {
    	return view('uniforms.create');
    }

    public function store(UniformRequest $request)
    {
    	$uniform = Uniform::create([
    		'set_name'			=>$request->input('set_name'),
    		'jacket'			=>$request->input('jacket'),
    		'jacket_color'		=>$request->input('jacket_color'),
    		'shirt'				=>$request->input('shirt'),
    		'shirt_color'		=>$request->input('shirt_color'),
    		'pant'				=>$request->input('pant'),
    		'pant_color'		=>$request->input('pant_color'),
    		'shoes'				=>$request->input('shoes'),
    		'shoes_color'		=>$request->input('shoes_color'),
    	]);

    	return redirect('uniforms');
    }

    public function edit($uniformId)
    {
    	$uniform = Uniform::find($uniformId);
    	return view('uniforms.create')->with(compact('uniform'));
    }

    public function update(UniformRequest $request,$uniformId)
    {
    	$uniform = Uniform::find($uniformId);

    	$uniform->set_name  		=	$request->input('set_name');
    	$uniform->jacket			=	$request->input('jacket');
    	$uniform->jacket_color		=	$request->input('jacket_color');
    	$uniform->shirt				=	$request->input('shirt');
    	$uniform->shirt_color		=	$request->input('shirt_color');
    	$uniform->pant 				=	$request->input('pant');
    	$uniform->pant_color 		=	$request->input('pant_color');
    	$uniform->shoes 			=	$request->input('shoes');
    	$uniform->shoes_color		=	$request->input('shoes_color');

    	$uniform->save();

    	return redirect('uniforms');
    }

    public function destroy($uniformId)
    {
    	$uniform = Uniform::find($uniformId);
    	$uniform->delete();
    	return redirect('uniforms');
    }
}
