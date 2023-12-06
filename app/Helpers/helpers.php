<?php

/**
 * Write code on Method
 *
 * @return response()
 */
if (!function_exists('getListOfYears')) {
    function getListOfYears()
    {

        $current_year = date('Y');
        $range = range($current_year, 2022);
        $years = array_combine($range, $range);

        return $years;
    }
}

if (!function_exists('changeDateFormatToUS')) {
    function changeDateFormatToUS($date)
    {
        return date("m-d-Y", strtotime($date));
    }
}
