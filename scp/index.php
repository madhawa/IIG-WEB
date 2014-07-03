<?php
/*********************************************************************
    index.php
    
    Future site for helpdesk summary aka Dashboard.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
require_once('staff.inc.php');
//Nothing for now...simply redirect to tickets page.
if ( $thisuser->isNocStaff() ) {
    require('tickets.php');
} elseif ( $thisuser->isManagementStaff() ) {
    require('orders.php');
} elseif ( $thisuser->isProvisioningStaff() ) {
    require('services.php');
} elseif ( $thisuser->isProvisioningStaff() ) {
    require('services.php');
} elseif ( $thisuser->isSalesStaff() ) {
    require('orders.php');
} elseif ( $thisuser->isSuperAdmin() ) {
    require('dashboard.php');
} else {
    require('profile.php');
}
?>