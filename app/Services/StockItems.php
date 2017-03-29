<?php
namespace App\Services;

use App\Event;
use App\Stock;
use App\OutStock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

/* 
*
    If while creating/updating an event, a category is choosen, the app checks if the item is registered in the stock database. 
    Then, the quantity asked is reduced from the stock. 
    If the user doesn't ask too much items, the quantity asked is stored to OutStock, which contains all the items used for the event. 
    If the amount of items in stock = 0, the object is destroyed for avoiding to be chosen again by the user in another event creation.  
*
*/
class StockItems
{
    public function outStockItems($category,$request,$eventId)
    {
        $stockItems = Stock::where('category','=',$category)->get();
        if(!empty($stockItems))
        {
            foreach ($stockItems as $stockItem) 
            {
                $newItem = Stock::find($stockItem->id);

                $newItem->quantity = $newItem->quantity - $request->input(str_replace(' ','_',$stockItem->name));

                if($newItem->quantity < 0)
                {
                    Session::flash('danger', 'Too Many '.$newItem->name.' asked !');
                    return redirect()->back();
                }
                elseif($newItem->quantity == 0)
                {
                    $newItem->delete();
                } 
                else 
                {
                    $outstock = OutStock::where('event_id','=',$eventId)->where('name','=',$newItem->name)->get();
                    if(count($outstock) >= 1)
                    {
                        $outstock->quantity = $outstock->quantity + $newItem->quantity;
                        $stock->save();
                    }
                    else
                    {
                        $stock = Stock::create([
                            'name'      =>  $outstock->name,
                            'category'  =>  $outstock->category,
                            'quantity'  =>  $outstock->quantity,
                        ]);

                    }
                    $newItem->save();
                }
            }
        }
        return $message = "ok";
    }
}