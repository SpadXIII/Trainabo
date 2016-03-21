<?php

namespace Treinabo;

use Carbon\Carbon;

class CarbonTreinabo extends Carbon
{
    /**
     * Days of the work week
     *
     * @var array
     */
    protected static $workDays = array(self::MONDAY, self::TUESDAY, self::WEDNESDAY, self::THURSDAY);

    /**
     * Holidays calculator
     *
     * @var Holidays
     */
    protected static $holidays;

    /**
     * Passes the holidays csv on to the holidays calculator
     * 
     * @param $csv
     * @throws \Exception
     */
    public static function setHolidaysCsv($csv)
    {
        if (!file_exists($csv)) {
            throw new \Exception('Holidays file does not exist?', 1);
        }

        self::getHolidays()->setHolidaysCsv($csv);
    }

    /**
     * Get the Holiday calculator.
     *
     * @return Holidays
     */
    protected static function getHolidays()
    {
        if (!self::$holidays) {
            self::$holidays = new Holidays();
        }

        return self::$holidays;
    }

    /**
     * Determines if the instance is a holiday
     *
     * @return bool
     */
    public function isHoliday()
    {
        return self::getHolidays()->isHoliday($this);
    }

    /**
     * Determines if the instance is a workday
     *
     * @return bool
     */
    public function isWorkday()
    {
        return in_array($this->dayOfWeek, self::$workDays);
    }
}
