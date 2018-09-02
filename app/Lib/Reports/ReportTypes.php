<?php

namespace App\Lib\Reports;

use App\Lib\Reports\src\AggregatedOverdueReport;
use App\Lib\Reports\src\WeekReport;

class ReportTypes {

    protected static $reports = [
        1 => AggregatedOverdueReport::class,
        2 => WeekReport::class,
    ];

    public static function get($id)
    {
        if(!array_key_exists($id, self::$reports)) {
            throw new \Exception('Invalid report id: '. $id, 422);
        }
        return new self::$reports[$id];
    }
}