<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarRequest;
use App\Http\Requests\IntervalRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IntervalRequest $request)
    {
        $calendar = Calendar::timeRangeQuery($request);

        return CalendarResource::collection($calendar);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|CalendarResource
     */
    public function store(CalendarRequest $request)
    {
        if (!Auth::check()) {
            if (in_array(1, Calendar::checkOverlapsResult($request))) {
                return response()->json([
                    'message' => 'There is already a record in this timeslot. Choose a different time or login.'
                ], 401);
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
     * @return CalendarResource|\Illuminate\Http\JsonResponse
     */
    public function update(CalendarRequest $request, Calendar $calendar)
    {
        if ($calendar->checkDifference() > 120) {

            $calendar->update($request->validated());

            return new CalendarResource($calendar);
        }

        return response()->json([
            'message' => 'You can\'t update the entry - less than 3 hours left'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Calendar $calendar)
    {
        if ($calendar->checkDifference() > 120) {

            $calendar->delete();

            return response()->json(null, 204);
        }

        return response()->json([
            'message' => 'You can\'t delete the entry - less than 3 hours left'
        ]);
    }
}
