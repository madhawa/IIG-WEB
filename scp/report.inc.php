<?php
//this function will accept a row returned from mysql query
function ticketisidle($data) {
    if ( is_array($data) ) {
        if ( $data['lastresponse'] == NULL ) {
            $cr_date = new DateTime($data['created']);
            $now_date = new DateTime();
            $interval = $now_date->diff($cr_date);
        } else {
            $resp_date = new DateTime($data['lastresponse']);
            $now_date = new DateTime();
            $interval = $now_date->diff($resp_date);
        }
    } else {
        die('invalid data supplied to indle ticket detector function, array expected');
    }
    if ( $interval->y ) {
        return true;
    } elseif ( $interval->m ) {
        return true;
    } elseif ( $interval->d ) {
        return true;
    } elseif ( $interval->h ) {
        return true;
    } elseif ( $interval->i > 15 ) {
        return true;
    } else {
        return false;
    }
}


function ticketidleintervalstring($data) {
    if ( is_array($data) ) {
        if ( $data['lastresponse'] == NULL ) {
            $cr_date = new DateTime($data['created']);
            $now_date = new DateTime();
            $interval = $now_date->diff($cr_date);
        } else {
            $resp_date = new DateTime($data['lastresponse']);
            $now_date = new DateTime();
            $interval = $now_date->diff($resp_date);
        }
    } else {
        die('invalid data supplied to indle ticket detector function, array expected');
    }
    
    $str = '';
    if ( $interval->y ) {
        $str .= $interval->y . ' year ';
    }
    if ( $interval->m ) {
        $str .= $interval->m . ' month ';
    }
    if ( $interval->d ) {
        $str .= $interval->d . ' day ';
    }
    if ( $interval->h ) {
        $str .= $interval->h . ' hour ';
    }
    if ( $interval->i ) {
        $str .= $interval->i . ' minute ';
    }
    return $str;
}

function is_today($timestring) { //the supplied timestring represents today
    if ( date('Y:m:d', $timestring) == date('Y:m:d') ) {
        return true;
    } else {
        return false;
    }
}

function getintervalminutes($start, $end) {
    $start = new DateTime($start);
    if ( !$end ) {
        $end = new DateTime();
    } else {
        $end = new DateTime($end);
    }
    $interval = $end->diff($start);
    $total = $interval->y*365*24*60 + $interval->m*30*24*60 + $interval->d*24*60 + $interval->h*60 + $interval->i;
    return $total;
}

?>