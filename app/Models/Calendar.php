<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Period\Period;
use Spatie\Period\Precision;

class Calendar extends Model
{
    use HasFactory, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'duration',
        'title',
        'description',
    ];

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($date) => Carbon::create($date)->format('Y-m-d H:i:s'),
            set: fn ($date) => Carbon::create($date)->format('Y-m-d H:i:s'),
        );
    }

    public function getStartTime()
    {
        $getStartTime = Carbon::parse($this->date)->format('Y-m-d H:i:s');
        return $getStartTime;
    }

    public function getEndTime()
    {
        $getEndTime = Carbon::parse($this->date)->addMinutes($this->duration)->format('Y-m-d H:i:s');
        return $getEndTime;
    }

    public static function timeRangeQuery($request)
    {
        $calendarQuery = Calendar::query();
        if ($request->filled('date_start')) {
            $calendarQuery->where('date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $calendarQuery->where('date', '<=', $request->date_end);
        }
        return $calendarQuery->get();
    }

    public function checkDifference(): int
    {
        $calendarDate = $this->date;
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return Carbon::parse($now)->diffInMinutes($calendarDate, false);
    }

    public static function checkOverlapsResult($request)
    {
        $checkOverlaps = [];
        $calendars = Calendar::all();
        $requestStart = Carbon::create($request->date)->format('Y-m-d H:i:s');
        $requestEnd = Carbon::parse($request->date)->addMinutes($request->duration)->format('Y-m-d H:i:s');
        foreach ($calendars as $calendar) {
            $entryStart = $calendar->getStartTime();

            $entryEnd = $calendar->getEndTime();

            $calPeriod = Period::make($entryStart, $entryEnd, Precision::MINUTE());

            $requestPeriod = Period::make($requestStart, $requestEnd, Precision::MINUTE());

            $checkOverlaps[] += $requestPeriod->overlapsWith($calPeriod);
        }
        return $checkOverlaps;
    }

}
