<?php
if (!defined('OSTSCPINC') || !@$thisuser->isStaff())
    die('Access Denied');
date_default_timezone_set('asia/dhaka');
define(ONE_DAY, 24 * 60 * 60);

class Chart {

    var $max_date;
    var $min_date;
    var $all_dates;
    var $total_tickets;
    var $total_open_tickets;
    var $total_pending_tickets;
    var $total_closed_tickets;
    var $total_answered_tickets;
    var $num_all_tickets;
    var $num_open_tickets;
    var $num_pending_tickets;
    var $num_closed_tickets;
    var $num_answered_tickets;

    function Chart($only_today=true) {
        $this->max_date = time();
        $this->min_date = date("Y-m-d", strtotime('-1 week'));
        $this->all_dates = array();

        $this->total_tickets = 0;
        $this->total_open_tickets = 0;
        $this->total_pending_tickets = 0;
        $this->total_closed_tickets = 0;
        $this->total_answered_tickets = 0;

        $this->num_all_tickets = array();
        $this->num_open_tickets = array();
        $this->num_pending_tickets = array();
        $this->num_closed_tickets = array();
        $this->num_answered_tickets = array();

        $current = $this->max_date;
        $i = 0;
        for ($i = 0; $i <= 6; $i++) {
            $sql_get = 'SELECT count(open.ticket_id) as open, count(answered.ticket_id) as answered ' .
                    ',count(overdue.ticket_id) as overdue, count(assigned.ticket_id) as assigned ' .
                    ' FROM ' . TICKET_TABLE . ' ticket ' .
                    'LEFT JOIN ' . TICKET_TABLE . ' open ON open.ticket_id=ticket.ticket_id AND open.status=\'open\' AND open.isanswered=0 ' .
                    'LEFT JOIN ' . TICKET_TABLE . ' answered ON answered.ticket_id=ticket.ticket_id AND answered.status=\'open\' AND answered.isanswered=1 ' .
                    'LEFT JOIN ' . TICKET_TABLE . ' overdue ON overdue.ticket_id=ticket.ticket_id AND overdue.status=\'open\' AND overdue.isoverdue=1 ' .
                    'LEFT JOIN ' . TICKET_TABLE . ' assigned ON assigned.ticket_id=ticket.ticket_id ' ;
            if ( $only_today == true ) {
                $sql_get .= ' WHERE DATE(ticket.created)=' . "'" . date('Y-m-d', $current) . "'";
            }
            
            $stats = db_fetch_array(db_query($sql_get));

            $this->total_tickets += $stats['assigned'];
            $this->total_open_tickets += $stats['open'];
            $this->total_answered_tickets += $stats['answered'];
            $this->total_pending_tickets += $stats['open'] + $stats['answered'];
            $this->total_closed_tickets += $stats['assigned'] - $stats['open'] - $stats['answered'];

            $this->num_all_tickets[] = $stats['assigned'];
            $this->num_answered_tickets[] = $stats['answered'];
            $this->num_open_tickets[] = $stats['open'];
            $this->num_pending_tickets[] = $stats['open'] + $stats['answered'];

            $this->num_closed_tickets[] = $stats['assigned'] - $stats['open'] - $stats['answered'];
            $this->all_dates[] = date('d', $current);
            //print_r($this->all_dates);
            $current = $current - ONE_DAY;
        }
    }

    function gen_all_tickets_series() {
        return join($this->num_all_tickets, ',');
    }

    function total_tickets() {
        return $this->total_tickets;
    }

    function gen_pending_tickets_series() {
        return join($this->num_pending_tickets, ',');
    }
    
    function pending_tickets() {
        return $this->total_pending_tickets;
    }

    function percent_pending_tickets() {
        return ($this->total_pending_tickets / $this->total_tickets) * 100;
    }

    function gen_closed_tickets_series() {
        return join($this->num_closed_tickets, ',');
    }

    function closed_tickets() {
        return $this->total_closed_tickets;
    }
    
    function percent_closed_tickets() {
        return ($this->total_closed_tickets / $this->total_tickets) * 100;
    }

    function gen_all_dates_series() {
        return join($this->all_dates, ',');
    }

}

$gen_chart = new Chart();
//  echo   $gen_chart->gen_all_dates_series().' '.$gen_chart->gen_all_tickets_series();
?>
<script type="text/javascript">
    $(function () {
        var line_chart;
        $(document).ready(function() {
            line_chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'line_chart',
                    type: 'line',
                    marginRight: 130,
                    marginBottom: 25
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                title: {
                    text: 'Tickets',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                xAxis: {
                    categories: [<?php   echo   $gen_chart->gen_all_dates_series(); ?>]
                },
                yAxis: {
                    tickInterval: 1,
                    min: 0,
                    title: {
                        text: 'number of tickets'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -150,
                    y: -5,
                    borderWidth: 0
                },
                series: [{
                        name: 'All Tickets',
                        data: [<?php   echo   $gen_chart->gen_all_tickets_series(); ?>]
                    }, {
                        name: 'Pending Tickets',
                        data: [<?php   echo   $gen_chart->gen_pending_tickets_series(); ?>],
                        dashStyle: 'longdash'
                    }]
            });
        });
    
    });
</script>

<script type="text/javascript">
    $(function () {
        var pie_chart;
    
        $(document).ready(function () {
    	
            // Build the chart
            pie_chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'pie_chart_status',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
                    percentageDecimals: 1
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                        type: 'pie',
                        name: 'Ticket status',
                        data: [
                            ['closed', <?php   echo   $gen_chart->percent_closed_tickets(); ?>],
                            {
                                name: 'pending',
                                y: <?php   echo   $gen_chart->percent_pending_tickets(); ?>,
                                sliced: true,
                                selected: true
                            }
                        ]
                    }]
            });
        });
    
    });
</script>

<script type="text/javascript" src="js/highcharts/highcharts.js"></script>