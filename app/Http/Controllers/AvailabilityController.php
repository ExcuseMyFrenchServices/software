<?php
namespace App\Http\Controllers;

use App\Availability;
use App\Http\Requests\Availability\CreateAvailabilityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $availabilities = Availability::where('user_id', '=', Auth::user()->id)
            ->where('date', '>=', date('Y-m-d'))
            ->get()
            ->sortBy('date');

        return view('availability.index')->with(compact('availabilities'));
    }

    public function dates()
    {
        return view('availability.select-dates');
    }

    public function store(CreateAvailabilityRequest $request)
    {
        $dates = array_values(array_filter(array_flatten($request->input('dates'))));
        $userId = Auth::user()->id;
        $availabilities = Availability::where('user_id', $userId)->whereIn('date', $dates)->get();

        foreach ($dates as $date) {
            if ($availabilities->where('date', $date.' 00:00:00')->isEmpty()) {
                $availability = Availability::create([
                    'date' => $date,
                    'times' => [],
                    'user_id' => $userId
                ]);

                $availabilities->push($availability);
            }
        }

        $availabilities = $availabilities->sortBy('date');

        return view('availability.edit')->with(compact('availabilities'));
    }

    public function destroy($availabilityId)
    {
        $availability = Availability::find($availabilityId);

        $availability->delete();

        return redirect('availability');
    }


    public function show($availabilityId)
    {
        $availabilities = collect([Availability::find($availabilityId)]);

        return view('availability.edit')->with(compact('availabilities'));
    }

    public function update(Request $request)
    {
        $availabilitiesMap = array_fill_keys($request->get('availabilities'), []);

        foreach (array_keys($request->except(['_method', '_token', 'availabilities'])) as $key) {
            list($id, $hour) = explode('-', $key);
            $availabilitiesMap[$id][] = $hour;
        }

        foreach ($availabilitiesMap as $id => $times) {
            $availability = Availability::find($id);
            $availability->times = $times;

            $availability->save();
        }

        return redirect('availability');
    }
}