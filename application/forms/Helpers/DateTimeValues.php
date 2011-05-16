<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: Oct 22, 2010
 * Time: 9:20:38 AM
 * @version $Id: DateTimeValues.php 4375 2010-11-16 08:50:46Z khueoreeskyy@webfact.de $
 * To change this template use File | Settings | File Templates.
 */

class App_Form_Helpers_DateTimeValues
{

    /**
     * GEt array values start to end
     * @static
     * @param int $start
     * @return
     */
    public static function getAry($start=1, $end=1) //
    {
        $v=array();
        for($i=$start;$i<=$end;$i++){$v[$i]=$i;}
        return $v;
    }

    /**
     * Get years from start value
     * @return array years
     */
    public static function getYears($start, $end = null)
    {
        static $y;
        if($y===null)
        {
            $yc = (($end == null) ? date('Y') : $end);
            for($i=$start;$i<=$yc;$i++){ $y[$i]=$i; };
        }
        return $y;
    }

    /**
     * Generate hours
     * @return array with 24 items 00 - 23
     */
    public static function getHours($postFix='')
    {
        static $h;
        if(null === $h) { for($i=0;$i<24;$i++){ $i = ($i)?$i:'00'; $h[$i]=$i.$postFix; }}
        return $h;
    }

    /**
     * Get array months
     * @return array with 12 items 01 - 12
     */
    public static function getMonth()
    {
        static $m;
        if(null === $m) { for($i=1;$i<13;$i++){ if(strlen($i) == 1) {$n = "0$i";} else {$n = $i;} $m["$n"]="$n";}}
        return $m;
    }

    /**
     * Get days
     * @return array with 31 items 01 - 31
     */
    public static function getDays()
    {
        static $d;
        if(null === $d) { for($i=1;$i<=31;$i++){ if(strlen($i) == 1) {$n = "0$i";} else {$n = $i;} $d[$n]=$n; }}
        return $d;
    }

    /**
     * Get minutes
     * @return array with 60 items 00 - 59
     */
    public static function getMinutes()
    {
        static $m;
        if(null === $m) { for($i=0;$i<60;$i++){ $i = ($i)?$i:'00'; $m[$i]=$i; }}
        return $m;
    }
}
