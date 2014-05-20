<?php

/* * *******************************************************************
  class.nav.php

  Navigation helper classes. Pointless BUT helps keep navigation clean and free from errors.

  Peter Rotich <peter@osticket.com>
  Copyright (c)  2006-2010 osTicket
  http://www.osticket.com

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id: $
 * ******************************************************************** */

class StaffNav {

    var $tabs = array();
    var $submenu = array();
    var $activetab;
    var $ptype;

    function StaffNav($pagetype = 'staff') {
        global $thisuser;

        $this->ptype = $pagetype;
        $tabs = array();

            if ( $thisuser->isSuperAdmin() ) {
                $tabs['dashboard'] = array('desc' => 'Dashboard', 'href' => 'dashboard.php?t=dashboard', 'title' => 'Admin Dashboard');
                //$tabs['settings'] = array('desc' => 'Settings', 'href' => 'admin.php?t=settings', 'title' => 'System Settings');
                //$tabs['tickets'] = array('desc' => 'Tickets', 'href' => 'admin.php?t=tickets', 'title' => 'Ticket Queue');
                //$tabs['manage'] = array('desc' => 'Manage', 'href' => 'admin.php?t=manage', 'title' => 'Manage emails, users and departments');
                //$tabs['emails'] = array('desc' => 'Emails', 'href' => 'admin.php?t=email', 'title' => 'Email Settings');
                //$tabs['services'] = array('desc' => 'Services', 'href' => 'admin.php?t=services', 'title' => 'Services');
                //$tabs['topics'] = array('desc' => 'Help Topics', 'href' => 'admin.php?t=topics', 'title' => 'Help Topics');
                $tabs['staff'] = array('desc' => 'Executives', 'href' => 'executives.php?t=staff', 'title' => 'Executive Members');
                //$tabs['client'] = array('desc' => 'Client', 'href' => 'admin.php?t=client', 'title' => 'Client management');
                //$tabs['client'] = array('desc' => 'Client', 'href' => 'client.php', 'title' => 'Customer management');
                //$tabs['1asia-ahl.com'] = array('desc' => '1asia-ahl.com', 'href' => 'admin.php?t=1asiaahl', 'title' => 'Manage 1asia-ahl.com');
                $tabs['depts'] = array('desc' => 'Departments', 'href' => 'departments.php?t=depts', 'title' => 'Departments');
                $tabs['sla'] = array('desc' => 'SLA', 'href' => 'sla.php', 'title' => 'SLA Dashboard');
            }
            //$tabs['chat'] = array('desc' => 'Chat', 'href' => 'chat.php', 'title' => 'chat');
            if ( $thisuser->isSuperAdmin() || $thisuser->isNocStaff()) {
                $tabs[TICKET] = array('desc' => 'Tickets', 'href' => 'tickets.php', 'title' => 'Ticket Queue');
                $tabs['sla'] = array('desc' => 'SLA', 'href' => 'sla.php', 'title' => 'SLA Dashboard');
                $tabs[CLIENT] = array('desc' => 'Client', 'href' => 'client.php', 'title' => 'Customer management');
            }
            
            if ( $thisuser->isSuperAdmin() || $thisuser->isManagementStaff() ) {
                $tabs[ORDER] = array('desc' => 'Orders', 'href' => 'orders.php', 'title' => 'Order Queue');
                $tabs[TICKET] = array('desc' => 'Tickets', 'href' => 'tickets.php', 'title' => 'Ticket Queue');
                $tabs[CLIENT] = array('desc' => 'Client', 'href' => 'client.php', 'title' => 'Customer management');
            }
            if ( $thisuser->isSuperAdmin() || $thisuser->isProvisioningStaff() ) {
                $tabs[MRTG] = array('desc' => 'Mrtg', 'href' => 'mrtg.php', 'title' => 'Mrtg');
                //$tabs['transmission'] = array('desc' => 'Transmission', 'href' => 'transmission.php', 'title' => 'Transmission');
                //if ( $thisuser && $thisuser->isEngineer() ) {
                $tabs[SERVICE] = array('desc' => 'Services', 'href' => 'services.php', 'title' => 'Services');
                $tabs[CAPACITY] = array('desc' => 'Capacity', 'href' => 'capacity.php', 'title' => 'Capacity');
            }
            if ( $thisuser->isSuperAdmin() || $thisuser->isSalesStaff() ) {
                $tabs[ORDER] = array('desc' => 'Orders', 'href' => 'orders.php', 'title' => 'Order Queue');
                $tabs[TICKET] = array('desc' => 'Tickets', 'href' => 'tickets.php', 'title' => 'Ticket Queue');
            }
            
            //$tabs['1asia-ahl.com'] = array('desc' => '1asia-ahl.com', 'href' => 'admin.php?t=1asiaahl', 'title' => 'Manage 1asia-ahl.com');
            //}
            /*
            if ($thisuser && $thisuser->canManageKb()) {
                $tabs['kbase'] = array('desc' => 'Knowledge Base', 'href' => 'kb.php', 'title' => 'Knowledge Base: Premade');
            }
            */
            //$tabs['directory'] = array('desc' => 'Directory', 'href' => 'directory.php', 'title' => 'Staff Directory');
            $tabs['profile'] = array('desc' => 'My Account', 'href' => 'profile.php', 'title' => 'My Profile');
        $this->tabs = $tabs;
    }

    function setTabActive($tab) {

        if ($this->tabs[$tab]) {
            $this->tabs[$tab]['active'] = true;
            if ($this->activetab && $this->activetab != $tab && $this->tabs[$this->activetab])
                $this->tabs[$this->activetab]['active'] = false;
            $this->activetab = $tab;
            return true;
        }
        return false;
    }

    function addSubMenu($item, $tab = null) {

        $tab = $tab ? $tab : $this->activetab;
        $this->submenu[$tab][] = $item;
    }

    function getActiveTab() {
        return $this->activetab;
    }

    function getTabs() {
        return $this->tabs;
    }

    function getSubMenu($tab = null) {

        $tab = $tab ? $tab : $this->activetab;
        return $this->submenu[$tab];
    }

}

?>
