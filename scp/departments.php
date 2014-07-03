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
        case 'dept':
            $do = strtolower($_REQUEST['do']);
            switch ($do) {
                case 'update':
                    $dept = new Dept($_POST['dept_id']);
                    if ($dept && $dept->getId()) {
                        if ($dept->update($_POST, $errors)) {
                            $msg = 'Dept updated successfully';
                        } else {
                            $errors['err'] .= 'Error updating the department';
                        }
                    } else {
                        $errors['err'] = 'wrong dept id';
                    }
                    break;
                case 'create':
                    if (($deptID = Dept::create($_POST, $errors))) {
                        $msg = Format::htmlchars($_POST['dept_name']) . ' added successfully';
                        header('Location: '.SCP_URL.'/departments.php?t=dept&id='.$deptID);
                    } else {
                        $errors['err'] .= ' Unable to add department ';
                    }
                    break;
                case 'delete':
                    if (Dept::delete($_POST['dept_id'], $errors)) {
                        $msg = 'delete success '.$errors['err'];
                        SessionFlash::set_flash_message($msg);
                        
                        redirect(SCP_URL.'/departments.php');
                    } else {
                        $error['err'] .= ' delete failure! ';
                    }
                    break;
                /*
                case 'mass_process':
                    if (!$_POST['ids'] || !is_array($_POST['ids'])) {
                        $errors['err'] = 'You must select at least one department';
                    } elseif (!$_POST['public'] && in_array($cfg->getDefaultDeptId(), $_POST['ids'])) {
                        $errors['err'] = 'You can not disable/delete a default department. Remove default Dept and try again.';
                    } else {
                        $count = count($_POST['ids']);
                        $ids = implode(',', $_POST['ids']);
                        if ($_POST['public']) {
                            $sql = 'UPDATE ' . DEPT_TABLE . ' SET ispublic=1 WHERE dept_id IN (' . $ids . ')';
                            if (db_query($sql) && ($num = db_affected_rows()))
                                $warn = "$num of $count selected departments made public";
                            else
                                $errors['err'] = 'Unable to make depts public.';
                        }elseif ($_POST['private']) {
                            $sql = 'UPDATE ' . DEPT_TABLE . ' SET ispublic=0 WHERE dept_id IN (' . $ids . ') AND dept_id!=' . db_input($cfg->getDefaultDeptId());
                            if (db_query($sql) && ($num = db_affected_rows())) {
                                $warn = "$num of $count selected departments made private";
                            }
                            else
                                $errors['err'] = 'Unable to make selected department(s) private. Possibly already private!';
                        }elseif ($_POST['delete']) {
                            //Deny all deletes if one of the selections has members in it.
                            $sql = 'SELECT count(staff_id) FROM ' . STAFF_TABLE . ' WHERE dept_id IN (' . $ids . ')';
                            list($members) = db_fetch_row(db_query($sql));
                            $sql = 'SELECT count(topic_id) FROM ' . TOPIC_TABLE . ' WHERE dept_id IN (' . $ids . ')';
                            list($topics) = db_fetch_row(db_query($sql));
                            if ($members) {
                                $errors['err'] = 'Can not delete Dept. with members. Move staff first.';
                            } elseif ($topic) {
                                $errors['err'] = 'Can not delete Dept. associated with a help topics. Remove association first.';
                            } else {
                                //We have to deal with individual selection because of associated tickets and users.
                                $i = 0;
                                foreach ($_POST['ids'] as $k => $v) {
                                    if ($v == $cfg->getDefaultDeptId())
                                        continue; //Don't delete default dept. Triple checking!!!!!
                                    if (Dept::delete($v))
                                        $i++;
                                }
                                if ($i > 0) {
                                    $warn = "$i of $count selected departments deleted";
                                } else {
                                    $errors['err'] = 'Unable to delete selected departments.';
                                }
                            }
                        }
                    }
                    break;
                    */
                default:
                    $errors['err'] = 'Unknown Dept action';
            }
            break;
    }
    
}
    
    
//Process requested tab.
$nav->setTabActive('depts');
$nav->addSubMenu(array('desc' => 'Departments', 'href' => 'departments.php?t=depts', 'iconclass' => 'departments'));
$nav->addSubMenu(array('desc' => 'Add New Dept.', 'href' => 'departments.php?t=depts&amp;a=new', 'iconclass' => 'newDepartment'));

$thistab = strtolower($_REQUEST['t'] ? $_REQUEST['t'] : 'dashboard');
$inc = $page = ''; //No outside crap please!
$page = $dept? 'dept.inc.php' : 'depts.inc.php';
$submenu = array();
switch ($thistab) {
    case 'dept': //lazy
    case 'depts':
        $dept = null;
        if (($id = $_REQUEST['id'] ? $_REQUEST['id'] : $_POST['dept_id']) && is_numeric($id)) {
            $dept = new Dept($id);
            if (!$dept || !$dept->getId()) {
                $dept = null;
                $errors['err'] = 'Unable to fetch info on Dept ID#' . $id;
            }
        }
        $page = $dept? 'dept.inc.php' : 'depts.inc.php';
        if ($_REQUEST['a'] == 'new') {
            $page = 'newdept.inc.php';
        }
        break;
}
    
    
$inc = ($page) ? STAFFINC_DIR . $page : '';
//Now lets render the page...
require(STAFFINC_DIR . 'header.inc.php');
?>
<div>
    <?php
        if ( SessionFlash::get_flash_message() ) {
            $msg = SessionFlash::get_flash_message();
            SessionFlash::clear_flash();
        }
    ?>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } ?>
    <?php if ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } ?>
    <?php if ($warn) { ?>
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