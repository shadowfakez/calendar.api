<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarRequest;
use App\Http\Requests\IntervalRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/*use Spatie\Period\Period;
use Spatie\Period\Precision;*/

use Illuminate\Support\Facades\Auth;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IntervalRequest $request)
    {

        //dd(Carbon::parse($element->date)->addMinutes($element->duration)->format('Y-m-d H:i:s')); - добавить время

        $calendar = Calendar::timeRangeQuery($request);

        return CalendarResource::collection($calendar);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CalendarResource|\Illuminate\Http\Response
     */
    public function store(CalendarRequest $request)
    {
        if (!Auth::check()) {
            if (in_array(1, Calendar::checkOverlapsResult($request))) {
                return response('There is already a record in this timeslot. Choose a different time or log in.');
            }
        }
        $newEntry = Calendar::create($request->validated());
        return new CalendarResource($newEntry);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return CalendarResource|\Illuminate\Http\Response
     */
    public function update(CalendarRequest $request, Calendar $calendar)
    {
        if ($calendar->checkDifference() > 120) {

            $calendar->update($request->validated());

            return new CalendarResource($calendar);
        }

        return response('You can\'t update the entry - less than 3 hours left');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        if ($calendar->checkDifference() > 120) {

            $calendar->delete();

            return response(null, Response::HTTP_NO_CONTENT);
        }

        return response('You can\'t delete the entry - less than 3 hours left');
    }
}
