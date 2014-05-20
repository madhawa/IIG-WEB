<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
require('client.inc.php');
//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require('tickets.php');
    exit;
}


require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="index">

<div class="index_divs" style="float: left">
  <span class="section_title">Open A New Ticket</span>
  <br>
  <br>
  Open a new trouble ticket
  <br /><br />
  <a href="open.php"><button type="button" class="button">New Ticket</button></a>
</div>

<div class="index_divs" style="float: right">
    <span class="section_title">Track Tickets</span>
    <br>
    <br>
    Track your tickets
    <br>
    <br>
    <a href="tickets.php"><button type="button" class="button">My Tickets</button></a>
</div>

<div class="clear"></div>

<div class="index_divs" style="float: left">
    <span class="section_title">Order Your Required Services</span>
    <br>
    <br>
    Please insert required informations in the SOF.
    <br>
    <br>
    <a href="order.create.php"><button type="button" class="button">SOF</button></a>
</div>

<div class="index_divs" style="float: right">
    <span class="section_title">Manage Your Staffs</span>
    <br>
    <br>
    View SLA
    <br>
    <br>
    <a href="sla.php"><button type="button" class="button">SLA</button></a>
</div>

<script type="text/javascript">
    $('div.index_divs').css({
        'border': '2px solid green',
        'display': 'table',
        'width': '400px',
        'padding': '20px',
        'margin-bottom': '100px'
        });
    $('div#index').css({
        'padding': '50px'
        });
</script>

</div>


<br />
<?php require(CLIENTINC_DIR.'footer.inc.php'); ?>
