<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 27/02/2018
 * Time: 09:30
 */

use DateInterval;
use DateTime;

class pipeDateTime {

    public function expireTime($seconds)
    {
        $aRet = array();

        if ($seconds < 0) {
            $seconds = $seconds * -1;
            $aRet['status'] = 'overdue';
        } else {
            $aRet['status'] = 'ontime';
        }

        $aRet['time'] = $this->fromSeconds($seconds);
        return $aRet ;
    }


    public function fromSeconds($seconds)
    {
        $seconds = (int)$seconds;
        $dateTime = new DateTime();
        $dateTime->sub(new DateInterval("PT{$seconds}S"));
        $interval = (new DateTime())->diff($dateTime);
        //$pieces = explode(' ', $interval->format('%y %m %d %h %i %s'));
        //$intervals = ['year', 'month', 'day', 'hour', 'minute', 'second'];
        $pieces = explode(' ', $interval->format('%y %m %d %h %i'));
        //$intervals = ['year', 'month', 'day', 'hour', 'minute'];
        $intervals = ['ano', 'mês', 'dia', 'hora', 'minuto'];
        $intervalsPlural = ['anos', 'meses', 'dias', 'horas', 'minutos'];
        $result = [];
        foreach ($pieces as $i => $value) {
            if (!$value) {
                continue;
            }
            $periodName = $intervals[$i];
            if ($value > 1) {
                $periodName = $intervalsPlural[$i];
            } else {
                $periodName = $intervals[$i];
            }
            $result[] = "{$value} {$periodName}";
        }
        return implode(', ', $result);
    }

    public function getSingleTime($start, $end)
    {
        $singular = array('Year', 'Month', 'Day', 'Hour', 'Minute', 'Second');
        $plural = array('Years', 'Months', 'Days', 'Hours', 'Minutes', 'Seconds');

        $a = $this->getFullTimeDifference($start, $end);
        if($a['status']=='ERROR')
            return $a;

        $i = 0;
        foreach ($a as $index => $value) {
            if ($value > 0) {
                if ($value > 1)
                    return array('value' => $value, 'sufix' => $plural[$i]);
                else
                    return array('value' => $value, 'sufix' => $singular[$i]);
            }
            $i++;
        }
    }

    public function getFullTimeDifference( $start, $end )
    {
        // https://stackoverflow.com/questions/365191/how-to-get-time-difference-in-minutes-in-php
        $uts['start']      =    strtotime( $start );
        $uts['end']        =    strtotime( $end );

        if( $uts['start']!==-1 && $uts['end']!==-1 )
        {
            if( $uts['end'] >= $uts['start'] )
            {
                $diff    =    $uts['end'] - $uts['start'];
                if( $years=intval((floor($diff/31104000))) )
                    $diff = $diff % 31104000;
                if( $months=intval((floor($diff/2592000))) )
                    $diff = $diff % 2592000;
                if( $days=intval((floor($diff/86400))) )
                    $diff = $diff % 86400;
                if( $hours=intval((floor($diff/3600))) )
                    $diff = $diff % 3600;
                if( $minutes=intval((floor($diff/60))) )
                    $diff = $diff % 60;
                $diff    =    intval( $diff );

                return( array('years'=>$years,'months'=>$months,'days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
            }
            else
            {
                return $this->makeMessage("ERROR","Ending date/time is earlier than the start date/time");
            }
        }
        else
        {
            return $this->makeMessage("ERROR","Invalid date/time data detected");
        }
    }

    function makeMessage($status,$message)
    {
        $aRet = array(
            "status" => $status,
            "message" => $message
        );
        return $aRet;
    }


}