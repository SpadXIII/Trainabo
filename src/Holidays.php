<?php

namespace Treinabo;

use Holiday\Holiday;
use Holiday\Netherlands;

class Holidays extends Netherlands
{
    protected static $holidays_csv = [];

    /**
     * @inheritdoc
     */
    protected function getHolidays($year)
    {
        $data = parent::getHolidays($year);

        return array_merge($data, self::$holidays_csv);
    }

    /**
     * Loads the csv with holidays.
     *
     * @param $csv
     * @throws \Exception
     */
    public function setHolidaysCsv($csv)
    {
        if (!file_exists($csv)) {
            throw new \Exception('Holidays file does not exist?', 1);
        }

        $holidays = file($csv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        self::$holidays_csv = array_map(function ($item) {
            return new Holiday($item, 'personal holiday');
        }, $holidays);
    }
}
