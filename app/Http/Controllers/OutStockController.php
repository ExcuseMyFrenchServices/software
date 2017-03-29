<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\OutStockRequests;
use App\OutStock;
use App\Stock;

class OutStockController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OutStock  $outstock
     * @return \Illuminate\Http\Response
     */
    public function destroy($outstock,$eventId)
    {
        $outstock = OutStock::find($outstock);
        $stock = Stock::where('name','=',$outstock->name)->first();

        if(count($stock) >= 1)
        {
        	$stock->quantity = $stock->quantity + $outstock->quantity;
        	$stock->save();
        }
        else
        {
        	$stock = Stock::create([
        		'name'		=>	$outstock->name,
        		'category'	=>	$outstock->category,
        		'quantity'	=>	$outstock->quantity,
        	]);
        }

        $outstock->delete();
        return redirect()->back();
    }
}
