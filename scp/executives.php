<?php
require('staff.inc.php');

//Make sure the user is admin type LOCKDOWN BABY!
if (!$thisuser && !$thisuser->isSuperAdmin()) {
    header('Location: index.php');
    require('index.php'); // just in case!
    exit;
}

//Access checked out OK...lets do the do 
define('OSTADMININC', TRUE); //checked by admin include files
define('PROV_INC', true); //service provisioning pages
define('ENGINEER_INC', true); // for engineers
define('ADMINPAGE', TRUE);   //Used by the header to swap menus.
//Files we might need.
//TODO: Do on-demand require...save some mem.
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once(INCLUDE_DIR . 'class.dept.php');
require_once(INCLUDE_DIR . 'class.group.php');
require_once(INCLUDE_DIR . 'class.email.php');
require_once(INCLUDE_DIR . 'class.mailfetch.php');
require_once(CLASS_DIR . 'class.client.php');


//Handle a POST.
if ($_POST && $_REQUEST['t'] && !$errors) {
    //print_r($_POST);
    //WELCOME TO THE HOUSE OF PAIN.
    $errors = array(); //do it anyways.

    switch (strtolower($_REQUEST['t'])) {
        case 'staff':
            include_once(INCLUDE_DIR . 'class.staff.php');
            $do = strtolower($_POST['do']);
            switch ($do) {
                case 'update':
                    $staff = new Staff($_POST['staff_id']);
                    if ($staff && $staff->getId()) {
                        if ($staff->update($_POST, $errors))
                            $msg = 'Staff profile updated successfully';
                        elseif (!$errors['err'])
                            $errors['err'] = 'Error updating the user';
                    }else {
                        $errors['err'] = 'Internal error';
                    }
                    break;
                case 'create':
                    if (($uID = Staff::create($_POST, $errors)))
                        $msg = Format::htmlchars($_POST['firstname'] . ' ' . $_POST['lastname']) . ' added successfully';
                    elseif (!$errors['err'])
                        $errors['err'] = 'Unable to add the user. Internal error';
                    break;
                case 'mass_process':
                    //ok..at this point..look WMA.
                    if ($_POST['uids'] && is_array($_POST['uids'])) {
                        $ids = implode(',', $_POST['uids']);
                        $selected = count($_POST['uids']);
                        if (isset($_POST['enable'])) {
                            $sql = 'UPDATE ' . STAFF_TABLE . ' SET isactive=1,updated=NOW() WHERE isactive=0 AND staff_id IN(' . $ids . ')';
                            db_query($sql);
                            $msg = db_affected_rows() . " of  $selected selected users enabled";
                        } elseif (in_array($thisuser->getId(), $_POST['uids'])) {
                            //sucker...watch what you are doing...why don't you just DROP the DB?
                            $errors['err'] = 'You can not lock or delete yourself!';
                        } elseif (isset($_POST['disable'])) {
                            $sql = 'UPDATE ' . STAFF_TABLE . ' SET isactive=0, updated=NOW() ' .
                                    ' WHERE isactive=1 AND staff_id IN(' . $ids . ') AND staff_id!=' . $thisuser->getId();
                            db_query($sql);
                            $msg = db_affected_rows() . " of  $selected selected users locked";
                            //Release tickets assigned to the user?? NO? could be a temp thing 
                            // May be auto-release if not logged in for X days? 
                        } elseif (isset($_POST['delete'])) {
                            db_query('DELETE FROM ' . STAFF_TABLE . ' WHERE staff_id IN(' . $ids . ') AND staff_id!=' . $thisuser->getId());
                            $msg = db_affected_rows() . " of  $selected selected users deleted";
                            //Demote the user 
                            db_query('UPDATE ' . DEPT_TABLE . ' SET manager_id=0 WHERE manager_id IN(' . $ids . ') ');
                            db_query('UPDATE ' . TICKET_TABLE . ' SET staff_id=0 WHERE staff_id IN(' . $ids . ') ');
                        } else {
                            $errors['err'] = 'Uknown command!';
                        }
                    } else {
                        $errors['err'] = 'No users selected.';
                    }
                    break;
                default:
                    $errors['err'] = 'Uknown command!';
            }
            break;
    }
}
        
//Process requested tab.
$thistab = strtolower($_REQUEST['t'] ? $_REQUEST['t'] : 'dashboard');
$inc = $page = ''; //No outside crap please!
$submenu = array();
switch ($thistab) {
    case 'staff':
        $group = null;
        //Tab and Nav options.
        $nav->setTabActive('staff');
        $nav->addSubMenu(array('desc' => 'Executive Members', 'href' => 'executives.php?t=staff', 'iconclass' => 'users'));
        $nav->addSubMenu(array('desc' => 'Add New Executive', 'href' => 'executives.php?t=staff&amp;a=new', 'iconclass' => 'newuser'));
        /*
        $nav->addSubMenu(array('desc' => 'Executive User Groups', 'href' => 'admin.php?t=groups', 'iconclass' => 'groups'));
        $nav->addSubMenu(array('desc' => 'Add New Group', 'href' => 'admin.php?t=groups&amp;a=new', 'iconclass' => 'newgroup'));
        */
        $page = '';
        switch ($thistab) {
            case 'grp':
            case 'groups':
                if (($id = $_REQUEST['id'] ? $_REQUEST['id'] : $_POST['group_id']) && is_numeric($id)) {
                    $res = db_query('SELECT * FROM ' . GROUP_TABLE . ' WHERE group_id=' . db_input($id));
                    if (!$res or !db_num_rows($res) or !($group = db_fetch_array($res)))
                        $errors['err'] = 'Unable to fetch info on group ID#' . $id;
                }
                $page = ($group or ($_REQUEST['a'] == 'new' && !$gID)) ? 'group.inc.php' : 'groups.inc.php';
                break;
            case 'staff':
                $page = 'staffmembers.inc.php';
                if (($id = $_REQUEST['id'] ? $_REQUEST['id'] : $_POST['staff_id']) && is_numeric($id)) {
                    $staff = new Staff($id);
                    if (!$staff || !is_object($staff) || $staff->getId() != $id) {
                        $staff = null;
                        $errors['err'] = 'Unable to fetch info on rep ID#' . $id;
                    }
                }
                $page = ($staff or ($_REQUEST['a'] == 'new' && !$uID)) ? 'staff.inc.php' : 'staffmembers.inc.php';
                break;
            default:
                $page = 'staffmembers.inc.php';
        }
        break;
}
        

$inc = ($page) ? STAFFINC_DIR . $page : '';
//Now lets render the page...
require(STAFFINC_DIR . 'header.inc.php');
?>
<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p align="center" id="warnmessage"><?php   echo   $warn ?></p>
<?php } ?>
</div>

<div style="margin:0 5px 5px 5px;">
    <?php
    if ($inc && file_exists($inc)) {
        require($inc);
    } else {
        ?>
        <p align="center">
            <font class="error">Problems loading requested admin page. (<?php   echo   Format::htmlchars($thistab) ?>)</font>
            <br>Possibly access denied, if you believe this is in error please get technical support.
        </p>
<?php } ?>
</div>

<?php
include_once(STAFFINC_DIR . 'footer.inc.php');
?>