<?php require_once('staff.inc.php');

require_once(CLASS_DIR . 'class.service.php');
require_once(CLASS_DIR . 'class.client.php');

$tpl = '';
$nav->setTabActive('capacity');



//handling form post
if ( $_POST['action']=='capacity-add' ) {
    if ( Service::save($_POST, $errors) ) {
        $msg = 'save success';
    } else {
        $errors['err'] = 'save failure';
    }
} elseif ( ( $_POST['action']=='discontinue' ) ) {
    $sid = $_POST['sid'];
    if (Service::discontinue($sid)) {
        $msg="success";
    } else {
        $errors['err'] = 'failure';
    }
}

//routing to different pages
$page=  $_GET['page'];
$tpl = '';
switch($page) {
    case 'cap_add':
        $tpl = 'scp.cap_add.tpl.php';
        break;
    case 'view_active':
        $services = Service::get_inhouse_services(true);
        if ( !$services || !count($services) ) {
            $title = 'no active services found';
            $num = 0;
        } else {
            $title = count($services) . ' services found';
            $num = count($services);
        }

        $tpl = 'scp.inhouse_services.tpl.php';
        break;
    case 'view_discontinued':
        $services = Service::get_inhouse_services(false);
        if ( !$services || !count($services) ) {
            $title = 'no inactive services found';
            $num = 0;
        } else {
            $title = count($services) . ' services found';
            $num = count($services);
        }

        $tpl = 'scp.inhouse_services.tpl.php';
        break;
}


?>

<?php require_once(STAFFINC_DIR . 'header.inc.php'); ?>
<?php require_once(STAFFINC_DIR . 'capacity.inc.php'); ?>
<?php require_once(STAFFINC_DIR . 'footer.inc.php'); ?>