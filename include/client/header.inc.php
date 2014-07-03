<?php
$title = ($cfg && is_object($cfg)) ? $cfg->getTitle() : 'Support';
//header("Content-Type: text/html; charset=UTF-8\r\n");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title><?php   echo   Format::htmlchars($title) ?></title>
        <link rel="shortcut icon" href="./images/favicon.ico">
        <!--
        <link rel="stylesheet" href="./styles/main.css" media="screen">
        <link rel="stylesheet" href="./styles/transmission.css" media="screen">
        <link rel="stylesheet" href="./styles/index.css" media="screen">
        <link rel="stylesheet" href="./styles/nav.client.css" media="screen">
        <link rel="stylesheet" href="./styles/colors.css" media="screen">
        <link rel="stylesheet" href="./styles/service_order.css" media="screen">
        <link rel="stylesheet" href="./styles/frame.css" media="screen">
        
        <link rel="stylesheet" href="./styles/ticket.css" media="screen">

        <link rel="stylesheet" href="./styles/users.css" media="screen">
        <link rel="stylesheet" href="./styles/style.css" media="screen">
        -->
        <link rel="stylesheet" href="./styles/nav.client.css" media="screen">
        <link rel="stylesheet" href="<?php   echo   ROOT_PATH; ?>css/osticket.css" media="screen">
        <link rel="stylesheet" href="<?php   echo   ASSETS_PATH; ?>css/theme.css" media="screen">
        <link rel="stylesheet" href="<?php   echo   ASSETS_PATH; ?>css/print.css" media="print">
        <link rel="stylesheet" href="<?php   echo   ROOT_PATH; ?>scp/css/typeahead.css"
            media="screen" />
        <link type="text/css" href="<?php   echo   ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css"
        rel="stylesheet" media="screen" />
        <link rel="stylesheet" href="<?php   echo   ROOT_PATH; ?>css/thread.css" media="screen">
        <link rel="stylesheet" href="<?php   echo   ROOT_PATH; ?>css/redactor.css" media="screen">
        <link type="text/css" rel="stylesheet" href="<?php   echo   ROOT_PATH; ?>css/font-awesome.min.css">

        <script type="text/javascript" src="./js/jquery-1.11.1.min.js"></script>
        <link rel="stylesheet" href="./styles/jquery-ui.css" />
        <script type="text/javascript" src="./js/jquery-ui.min.js"></script>
        
        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript" src="js/calendar.js"></script>
    </head>
    
    <body>
        <div id="container" class="wrapper">
        <noscript id="js_warning">
            <div>
            Please, enable javascript for best experience
            </div>
        </noscript>
            <?php if ($thisuser && is_object($thisuser) && $thisuser->isValid()) { ?>
                <div id="header">
                    <a id="logo" href="index.php" title="Support Center"><img src="./images/logo2.png" border=0 alt="Support Center"></a>
                    
                    
                    <div id="cssmenu" class="cssmenu">
                        <ul id="nav">
<!--                             <li><a class="home" href="index.php">Home</a></li> -->
                            <li><a class="new_ticket" href="open.php">New Ticket</a></li>

                            <li class="has-sub"><a class="my_tickets" href="tickets.php">My Tickets</a>
                                <ul>
                                    <li><a href="view.php?status=open">Open Tickets</a></li>
                                    <li><a href="view.php?status=closed">Closed Tickets</a></li>
                                    <li><a href="view.php?status=sla">SLA Claim Ticket</a></li>
                                    <li><a href="view.php?status=no_sla">SLA Non Claim Ticket</a></li>
                                </ul>
                            </li>

                                <li><a class="new_order" href="order.create.php">New Order</a></li>

                                <li class="has-sub"><a class="my_orders" href="#">My Orders</a>
                                    <ul>
                                        <li><a href="order.create.php">New Order</a></li>
                                        <li><a href="orders.php">Order Status</a></li>
                                        <li><a href="order_updown.php">Upgrade/Downgrade</a></li>
                                        <li><a href="orders.php?status=accepted">Accepted Order</a></li>
                                        <li><a href="orders.php?status=rejected">Rejected Order</a></li>
                                        <li><a href="orders.php?status=cancel">Cancel Order</a></li>
                                    </ul>
                                </li>

                                <li class="has-sub"><a href="orders.php">Order Status</a>
                                    <ul>
                                        <li><a href="orders.php?status=accepted">Accepted Order</a></li>
                                        <li><a href="orders.php?status=rejected">Rejected Order</a></li>
                                        <li><a href="orders.php?status=cancel">Cancel Order</a></li>
                                    </ul>
                                </li>

                                <li class="has-sub"><a class="monitoring" href="#">Monitoring</a>
                                    <ul>
                                        <li><a href="monitoring.php?p=lg">Looking Glass</a></li>
                                        <!--<li><a href="monitoring.php?p=graph">MRTG</a></li> -->
                                        <li><a href="monitoring.php?p=spt">Speed Test</a></li>
                                        <li><a href="transmission.php">Transmission Path</a></li>
                                        <li><a href="pending_feature.php">SLA Achivement</a></li>
                                    </ul>
                                </li>

                                <li class="has-sub"><a class="accounts" href="#">Accounts</a>
                                    <ul>
                                        <li><a href="pending_feature.php">Invoices</a></li>
                                        <li><a href="pending_feature.php">Statements</a></li>
                                    </ul>
                                </li>

                                <li class="has-sub"><a class="sla" href="sla.php">SLA</a>
                                    <ul>
                                        <li><a href="sla.php?page=last_year">Last Year</a></li>
                                        <li><a href="sla.php?page=current_year">Current Year</a></li>
                                        <li><a href="sla.php?page=last_month">Last Month</a></li>
                                        <li><a href="sla.php?page=current_month">Current Month</a></li>
                                        <li><a href="pending_feature.php">Customize Date</a></li>
                                    </ul>
                                </li>
                                
                                <li><a class="add_staff" href="admin.php?t=client">User Info</a></li>
                                
                            <li><a class="log_out" href="logout.php">Log Out</a></li>
                        </ul>
                    </div>
                    
                </div>
            <?php } ?>
        <div id="content">