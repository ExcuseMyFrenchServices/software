<?php

namespace App\Http\Controllers;

use App\PublicHoliday;
use App\Http\Requests\PublicHolidayRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use DateTime;

class PublicHolidayController extends Controller
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
    	$year = date('Y');
        $public_holidays = PublicHoliday::where('year','=',$year)
        								->orWhere('year', '=', 1)	
        								->orderBy('public_holiday_date', 'ASC')
        								->get();

        return view('public_holidays.index')->with(compact('public_holidays','year'));
    }

    public function create()
    {
    	return view('public_holidays.create');
    }

    public function store(PublicHolidayRequest $request)
    {
    	if(null !== $request->input('reccurent-date') && $request->input('reccurent-date') == "yes")
    	{
    		$year = 1;
    	}
    	else
    	{
    		$year = date('Y', strtotime($request->input('public_holiday_date')));
    	}
    	$public_holiday = PublicHoliday::create([
    		'public_holiday_name'	=>$request->input('public_holiday_name'),
    		'public_holiday_date'	=>$request->input('public_holiday_date'),
    		'year'					=>$year,
    	]);

    	return redirect('public-holidays');
    }

    public function show($publicHolidayId)
    {
    	$public_holiday = PublicHoliday::find($publicHolidayId);

    	return view('public_holidays.create')->with(compact('public_holiday'));
    }

    public function edit($publicHolidayId)
    {
    	$public_holiday = PublicHoliday::find($publicHolidayId);

    	return view('public_holidays.create')->with(compact('public_holiday'));
    }

    public function update(PublicHolidayRequest $request, $publicHolidayId)
    {

    	if(null !== $request->input('reccurent-date') && $request->input('reccurent-date') == "yes")
    	{
    		$year = 1;
    	}
    	else
    	{
    		$year = date('Y', strtotime($request->input('public_holiday_date')));
    	}    	

    	$public_holiday = PublicHoliday::find($publicHolidayId);

    	$public_holiday->public_holiday_name	 = $request->input('public_holiday_name');
    	$public_holiday->public_holiday_date	 = $request->input('public_holiday_date');
    	$public_holiday->year 					 = $year;	

    	$public_holiday->save();

    	return redirect('public-holidays');
    }

    public function destroy($publicHolidayId)
    {
    	$public_holiday = PublicHoliday::find($publicHolidayId);

    	$public_holiday->delete();

    	return redirect('public-holidays');
    }
}
