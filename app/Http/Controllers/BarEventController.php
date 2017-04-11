<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\BarEvent;
use App\OutStock;

class BarEventController extends Controller
{	
	public function index()
	{
		$barEvents = BarEvent::all();

		return view('barEvent.index')->with(compact('barEvents'));
	}

    public function show($barEventId)
    {
    	$barEvent = BarEvent::find($barEventId);

    	return view('barEvent.details')->with(compact('barEvent'));
    }

    public function edit($barEventId)
    {
    	$barEvent = BarEvent::find($barEventId);
    	$event = $barEvent->event;
    	$eventId = $event->id;

    	$items = OutStock::where('event_id','=',$eventId)->get();

    	$beers = BarEventController::fetchItems('beers',$eventId);
    	$whines = BarEventController::fetchItems('whine',$eventId);
    	$spirits = BarEventController::fetchItems('spirits',$eventId);
    	$cocktails = BarEventController::fetchItems('cocktails',$eventId);
    	$shots = BarEventController::fetchItems('shots',$eventId);
    	$ingredients = BarEventController::fetchItems('ingredients',$eventId);
    	$softs = BarEventController::fetchItems('softs',$eventId);
    	$bars = BarEventController::fetchItems('bars',$eventId);
    	$furnitures = BarEventController::fetchItems('furnitures',$eventId);

    	return view('barEvent.create')->with(compact('barEvent','items','beers','whines','spirits','cocktails','shots','ingredients','softs','bars','furnitures','event'));
    }

    public function store($barEventId, Request $request)
    {
    	$barEvent = BarEvent::find($barEventId);

    	if($request->input('staff') == 'on')
    	{
    		$barEvent->private = $request->input('privateNumber');
    		$barEvent->bar_back = $request->input('barBackNumber');
    		$barEvent->bar_runner = $request->input('barRunnerNumber');
    		$barEvent->classic_bartender = $request->input('classicBartenderNumber');
    		$barEvent->cocktail_bartender = $request->input('cocktailBartenderNumber');
    		$barEvent->flair_bartender = $request->input('falirBartenderNumber');
    		$barEvent->mixologist = $request->input('mixologistNumber');
    	}

    	if($request->input('alcohol') == 'on')
    	{
    		BarEventController::outStockBarItems('beers',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('whine',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('spirits',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('cocktails',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('shots',$request,$barEvent->event->id);
    	}

    	if($request->input('supplies') == 'on')
    	{
    		BarEventController::outStockBarItems('softs',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('ingredients',$request,$barEvent->event->id);
    	}

    	if($request->input('glasses') == 'on')
    	{
    		$barEvent->glass_type = $request->input('glassChoice');
    	}

    	if($request->input('equipment') == 'on')
    	{
    		$barEvent->ice = $request->input('ice') == 'on';
    		$barEvent->bar_number = $request->input('barNumber');
    		BarEventController::outStockBarItems('bars',$request,$barEvent->event->id);
    		BarEventController::outStockBarItems('furnitures',$request,$barEvent->event->id);
    	}

    	$barEvent->notes = $request->input('notes');
    	if($barEvent->status == 0)
		{
    		$barEvent->status = 1;
    	}
    	$barEvent->save();

    	return redirect('event');
    }

    public function confirm($barEventId)
    {
    	$barEvent = BarEvent::find($barEventId);
    	$barEvent->status = 2;
    	$barEvent->save();

    	return redirect('event');
    }

    public function outStockBarItems($item,$request,$eventId)
	{
		if($request->input($item) == 'on')
		{
			for($i=0; $i < $request->input($item.'counter'); $i++)
			{
				if(!empty($request->input($item.'Name'.$i)))
				{
					$outStock = OutStock::where('event_id','=',$eventId)->where('name','=',$request->input($item.'Name'.$i))->first();
					
					if(empty($outStock))
					{
						OutStock::create([
							'event_id'		=>	$eventId,
							'name'			=>	$request->input($item.'Name'.$i),
							'category'		=>  $item,
							'quantity'		=>  $request->input($item.'Number'.$i),
							'brand'			=>  $request->input($item.'List'.$i),
						]);
					}
					else
					{
						$outStock->name = $request->input($item.'Name'.$i);
						$outStock->category = $item;
						$outStock->quantity = $request->input($item.'Number'.$i);
						$outStock->brand = $request->input($item.'List'.$i);
						$outStock->save();
					}
				}
			}
		}
	}

	public function fetchItems($itemName, $eventId)
	{
		$item = OutStock::where('event_id','=',$eventId)->where('category','=',$itemName)->get();
		return $item;
	}
}
