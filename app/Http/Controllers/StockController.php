<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\StockRequests;
use App\Stock;

class StockController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stock::all();

        return view('stock.index')->with(compact('stocks'));
    }
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {	
    	$category = $request->input('category');
    	
    	if($category != 'all')
    	{
        	$stocks = Stock::where('category',"=",$category)->get();
        	return view('stock.index')->with(compact('stocks','category'));
    	}
    	return redirect('stocks');

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stock.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stock = Stock::create([
        	'name'		=>		$request->input('name'),
        	'category'	=>		$request->input('category'),
        	'quantity'	=>		$request->input('quantity'),
        ]);

        return redirect('stocks');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function edit($stock)
    {
        $stock = Stock::find($stock);

        return view('stock.create')->with(compact('stock'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $stock)
    {
        $stock = Stock::find($stock);

        $stock->name        = $request->input('name');
        $stock->category    = $request->input('category');
        $stock->quantity    = $request->input('quantity');

        $stock->save();

        return redirect('stocks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy($stock)
    {
        $stock = Stock::find($stock);
        $stock->delete();
        return redirect('stocks');
    }
}
