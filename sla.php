<?php

require_once 'client.inc.php';
require_once INCLUDE_DIR.'class.ticket.php';
//require('secure.inc.php');
if (!is_object($thisuser) || !$thisuser->isValid())
    die('Access denied'); //Double check again.

$thistab = strtolower($_REQUEST['page'] ? $_REQUEST['page'] : 'index');

$date = new DateTime();
$data = array();
if ($thistab == 'last_year') {
    $date_from = $date->format('Y') - 1 . '-' . '01' . '-' . '01 00:00:00';
    $last_year = DateTime::createFromFormat('Y-m-d H:i:s', $date_from);
    $date_to = $date->format('Y') - 1 . '-' . '12' . '-' . '31 23:59:59';
    $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()) . ' AND created BETWEEN ' . db_input($date_from) . ' AND ' . db_input($date_to);
    $res = db_query($sql);
    $tickets = db_assoc_array($res, true);
    for ($i = 1; $i < 13; $i++) { //for each month
        $date_from = DateTime::createFromFormat('Y-m-d H:i:s', $last_year->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . '01 00:00:00');
        $date_to = DateTime::createFromFormat('Y-m-d H:i:s', $last_year->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $date_from->format('t').' 23:59:59');
        $E = 0;
        $U = 0;
        $M = $date_from->format('t') * 24;
        $tickets_matched = 0;
        if (count($tickets)) {
            foreach ($tickets as $ticket) {
                $tt = new Ticket($ticket['ticket_id']);
                $ticket_date = DateTime::createFromFormat('Y-m-d H:i:s', $ticket['created']);
                if ($tt && ($ticket_date >= $date_from) && ($ticket_date <= $date_to)) {
                    $tickets_matched++;
                    $U += $tt->get_outage_duration() / 60;
                    $E += $tt->get_service_duration()/60;
                }
            }
        }
        $sla_title = 'SLA for last year: ' . $last_year->format('Y');
        if (!$tickets_matched) {
            $sla = 'No ticket';
            $data[$date_from->format('M-Y')] = $sla;
        } else {
            $sla = ((($M - $E - $U) / ($M - $E)) * 100);
            $data[$date_from->format('M-Y')] = number_format($sla, 2) . ' %';
        }
    }
} elseif ($thistab == 'current_year') {
    $date_from = $date->format('Y') . '-' . '01' . '-' . '01 00:00:00';
    $date_to = $date->format('Y-m-') . ($date->format('d')) . ' 23:59:59';
    $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()) . ' AND created BETWEEN ' . db_input($date_from) . ' AND ' . db_input($date_to);
    $res = db_query($sql);
    $tickets = db_assoc_array($res, true);
    for ($i = 1; $i <= $date->format('n'); $i++) {
        $tickets_matched = 0;
        $date_from = DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . '01 00:00:00');
        $E = 0;
        $U = 0;
        $M = $date_from->format('t') * 24;
        $date_to = DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $date_from->format('t').' 23:59:59');
        if ( count($tickets) ) {
            foreach ($tickets as $ticket) {
                $tt = new Ticket($ticket['ticket_id']);
                $ticket_date = DateTime::createFromFormat('Y-m-d H:i:s', $ticket['created']);
                if ($tt && ($ticket_date >= $date_from) && ($ticket_date <= $date_to)) {
                    $tickets_matched++;
                    $U += $tt->get_outage_duration() / 60;
                    $E += $tt->get_service_duration()/60;
                }
            }
        }
        $sla_title = 'SLA for this year: '.$date_from->format('Y');
        if (!$tickets_matched) {
            $sla = 'No ticket';
            $data[$date_from->format('M-Y')] = $sla;
        } else {
            $sla = ((($M - $E - $U) / ($M - $E)) * 100);
            $data[$date_from->format('M-Y')] = number_format($sla, 2) . ' %';
        }
    }
} elseif ($thistab == 'last_month') {
    $last_month_year = ($date->format('n') == 1) ? $date->format('Y') - 1 : $date->format('Y');
    $date_from = $last_month_year . '-' . ($date->format('n') - 1) . '-' . '01 00:00:00';
    $last_month = DateTime::createFromFormat('Y-n-d H:i:s', $date_from);
    $date_to = $last_month_year . '-' . $date->format('n') - 1 . '-' . $last_month->format('t') . ' 23:59:59';
    $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()) . ' AND created BETWEEN ' . db_input($date_from) . ' AND ' . db_input($date_to);
    $res = db_query($sql);
    if (db_num_rows($res)) {
        $tickets = db_assoc_array($res, true);
        $E = 0;
        $U = 0;
        $M = $last_month->format('t') * 24;
        foreach ($tickets as $ticket) {
            if ( $tt = new Ticket($ticket['ticket_id']) ) {
                $U += $tt->get_outage_duration() / 60;
                $E += $tt->get_service_duration()/60;
            }
        }
        $sla = ((($M - $E - $U) / ($M - $E)) * 100).' %';
        
    } else {
        $sla = 'No tickets';
    }
    $data[$last_month->format('M-Y')] = $sla;
    $sla_title = 'SLA for last month: '.$last_month->format('M Y');
} elseif ($thistab == 'current_month') {
    $date_from = $date->format('Y-m-') . '01'.' 00:00:00';
    $date_to = $date->format('Y-m-') . str_pad(($date->format('d')), 2, '0', STR_PAD_LEFT) . ' 23:59:59';
    $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()) . ' AND created BETWEEN ' . db_input($date_from) . ' AND ' . db_input($date_to);
    $res = db_query($sql);
    $tickets = db_assoc_array($res, true);
    $E = 0;
    $U = 0;
    $M = $date->format('t') * 24;
    if ( count($tickets) ) {
        foreach ($tickets as $ticket) {
            if ( $tt = new Ticket($ticket['ticket_id']) ) {
                $U += $tt->get_outage_duration() / 60;
                $E += $tt->get_service_duration()/60;
            }
        }
        $sla = number_format(((($M - $E - $U) / ($M - $E)) * 100), 2).' %';
    } else {
        $sla = 'no tickets';
    }
    $data[$date->format('M-Y')] = $sla;
    $sla_title = 'SLA for current month: '.$date->format('M Y');
} elseif ($thistab == 'custom_date') {
    if ($_GET && $_GET['startDate'] && $_GET['endDate']) {
        $date_from = DateTime::createFromFormat('n/j/Y H:i:s', $_GET['startDate'].' 00:00:00');
        $date_from_sel = $date_from;
        $date_to = DateTime::createFromFormat('n/j/Y', $_GET['endDate']);
        $date_to_sel = $date_to;
        $months_f = ((DateTime::createFromFormat('n/j/Y', $_GET['startDate'])->diff($date_to)->format('%a') / 365) * 12);
        $months = ceil($months_f);
        $data = array();
        if ($months) {
            for ($i = 1; $i <= $months; $i++) {
                if ($i > 1) {
                    $date_t = DateTime::createFromFormat('Y-m-d H:i:s', $date_from->format('Y-m-') . $date_from->format('t') . ' 00:00:00');
                    $date_from = $date_t->add(new DateInterval('P1D')); //next month first date
                }
                $date_to = DateTime::createFromFormat('Y-m-d H:i:s', $date_from->format('Y-m-') . $date_from->format('t') . ' 23:59:59');
                if (($i == $months) && ($months != $months_f)) {
                    $date_to = DateTime::createFromFormat('Y-m-d H:i:s', $date_to_sel->format('Y-m-d ') . '23:59:29');
                }
                
                $sql = 'SELECT * FROM ' . TICKET_TABLE . ' WHERE client_id=' . db_input($thisuser->getId()) . ' AND created BETWEEN ' . db_input($date_from->format('Y-m-d H:i:s')) . ' AND ' . db_input($date_to->format('Y-m-d H:i:s'));
                $res = db_query($sql);
                $tickets = db_assoc_array($res);

                if ($tickets && !empty($tickets)) {
                    $E = 0;
                    $U = 0;
                    //$tdf = $date_from;
                    //$tdt = DateTime::createFromFormat('Y-m-d H:i:s', $date_to->format('Y-m-').$date_to->format('t').' 23:59:59');
                    $M = $date_to->format('t')*24;
                    foreach ($tickets as $ticket) {
                        if ( $tt = new Ticket($ticket['ticket_id']) ) {
                            $U += $tt->get_outage_duration() / 60;
                            $E += $tt->get_service_duration()/60;
                        }
                    }
                    //echo sprintf('U:%s E:%s', $U, $E);
                    $sla = number_format(((($M - $E - $U) / ($M - $E)) * 100), 2) . ' %';
                } else {
                    $sla = 'no tickets found';
                }
                $data[$date_from->format('M-Y')] = $sla;
                $sla_title = 'SLA between date '.$date_from_sel->format('D, d-M-Y').' and '.$date_to_sel->format('D, d-M-Y');
            }
        }
    } else {
        
    }
}
// ========================
require_once(CLIENTINC_DIR . 'header.inc.php');
require(TEMPLATE_DIR . 'sla.tpl.php');
include_once(CLIENTINC_DIR . 'footer.inc.php');
?>