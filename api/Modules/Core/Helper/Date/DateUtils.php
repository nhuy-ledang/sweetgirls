<?php namespace Modules\Core\Helper\Date;

class DateUtils {
    public static function getIdOfDate($date) {
        $nameDay = date('l');
        if (isset($date)) {
            $nameDay = date('l', strtotime($date));
        }

        $result = 1;
        if ($nameDay == 'Sunday') {
            $result = 7;
        } else if ($nameDay == 'Monday') {
            $result = 1;
        } else if ($nameDay == 'Tuesday') {
            $result = 2;
        } else if ($nameDay == 'Wednesday') {
            $result = 3;
        } else if ($nameDay == 'Thursday') {
            $result = 4;
        } else if ($nameDay == 'Friday') {
            $result = 5;
        } else if ($nameDay == 'Saturday') {
            $result = 6;
        }

        return $result;

    }

    public static function isDayOff($listDayOff, $date) {
        $nameDay = date('l');
        if (isset($date)) {
            $nameDay = date('l', strtotime($date));
        }

        $result = false;

        foreach ($listDayOff as $d) {
            if (strtolower($d) == 'sunday' && strtolower($nameDay) == 'sunday') {
                $result = true;
                break;
            } else if (strtolower($d) == 'saturday' && strtolower($nameDay) == 'saturday') {
                $result = true;
                break;
            } else if (strtolower($d) == 'holiday' && DateUtils::isHolidayVietNam($date)) {
                $result = true;
                break;
            } else {
                $result = false;
            }

        }

        return $result;

    }

    /**
     * Get Lunar
     * @param $date Y-m-d
     * @return false|string
     */
    public static function getLunar($date) {
        $solar = new Solar();
        $solar->solarYear = date("Y", strtotime($date));
        $solar->solarMonth = date("m", strtotime($date));;
        $solar->solarDay = date("d", strtotime($date));
        $lunar = LunarSolarConverter::solarToLunar($solar);

        return date("Y-m-d", strtotime($lunar->lunarYear . '-' . $lunar->lunarMonth . '-' . $lunar->lunarDay));
    }

    /**
     * Get Solar
     * @param $date Y-m-d
     * @return false|string
     */
    public static function getSolar($date) {
        $lunar = new Lunar();
        $lunar->lunarYear = date("Y", strtotime($date));
        $lunar->lunarMonth = date("m", strtotime($date));;
        $lunar->lunarDay = date("d", strtotime($date));
        $solar = LunarSolarConverter::lunarToSolar($lunar);

        return date("Y-m-d", strtotime($solar->solarYear . '-' . $solar->solarMonth . '-' . $solar->solarDay));
    }

    public static function isHolidayVietNam($date) {
        $result = false;

        $newDate = date("*-m-d", strtotime($date));
        if (in_array($newDate, HOLIDAYS)) {
            $result = true;
        }

        if(!$result) {
            $solar = new Solar();
            $solar->solarYear = date("Y", strtotime($date));
            $solar->solarMonth = date("m", strtotime($date));;
            $solar->solarDay = date("d", strtotime($date));
            $lunar = LunarSolarConverter::solarToLunar($solar);

            $dateLunar = date("*-m-d", strtotime($lunar->lunarYear . '-' . $lunar->lunarMonth . '-' . $lunar->lunarDay));
            if (in_array($dateLunar, LUNAR_HOLIDAYS)) {
                $result = true;
            }
        }

        return $result;
    }

    public static function getHolidays($start_date, $end_date) {
        $output = [];

        do {
            if (DateUtils::isHolidayVietNam($start_date)) {
                $output[] = $start_date;
            }

            $start_date = date('Y-m-d', strtotime('+1 days', strtotime($start_date)));
        } while(strtotime($start_date) <= strtotime($end_date));

        return $output;
    }
}
