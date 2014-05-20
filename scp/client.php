<?php

require('staff.inc.php');
$nav->setTabActive('client');
//$nav->addSubMenu(array('desc' => 'All Clients', 'href' => 'client.php?do=view_all', 'iconclass' => 'users'));
//$nav->addSubMenu(array('desc' => 'Add New Client', 'href' => '?do=newclient', 'iconclass' => 'newuser'));
//$nav->addSubMenu(array('desc' => 'search a client', 'href' => '#search_client', 'iconclass' => 'users'));
require_once(CLASS_DIR . 'class.client.php');
require_once(CLASS_DIR . 'class.breadcrumbs.php');
require_once(SCP_DIR . 'misc.php');

$file_limit = get_ini_limit();

$all_clients_url = SCP_URL . '/client.php?do=view_all';
$add_client_url = SCP_URL . '/client.php?do=newclient';

$errors = array();


$tpl = 'clientmembers.tpl.php';

$do = $_REQUEST['do'];
$id = $_REQUEST['id'];
$client_id = $_REQUEST['client_id'];

if ( $_POST ) { // to save
    $services = array();
    $services['service_type'] = $_POST['service_type'];
    $services['circuit_type'] = $_POST['circuit_type'];
    $services['cin'] = $_POST['cin_no'];

    switch ( $do ) { //light camera action!!!
        case 'newclient':
            $services = Client::build_service_array($_POST);

            if ( !Client::is_uniq_name($_POST['username']) ) { //login name is not unique
                $errors['err'] = 'username already in use, choose another';
                $rep = $_POST;
                $rep['username'] = '';
            } else { // name unique
                if ( $id = Client::create($_POST, $errors)) {
                    //now save cin
                    $_POST['client_id'] = $id; //for saving cin
                    Client::save_cin($_POST, $errors);

                    $services = Client::get_all_cin($id);

                    $msg = 'client added successfully';
                    $rep = $_POST;
                } else {
                    $errors['err'] = 'Unable to add the user. ' . $errors['err'];
                    $rep = $_POST;
                }
            }
            break;
        case 'update_client':
            //template variables
            $services = Client::build_service_array($_POST);
            $client = new Client($_POST['client_id']);
            $client_name = $client->getName();
            $client_id = $client->getId();

            if ( $client && ($id = $client->getID()) ) {
                if ($client->update($_POST, $errors)) {
                    $msg = 'Client profile updated successfully';

                    //now save cin
                    Client::save_cin($_POST, $errors);

                    $services = Client::get_all_cin($id);

                    $rep = $client->getInfo();
                }elseif (!$errors['err']) {
                    $errors['err'] = 'Error updating the user';
                    $rep = $_POST;
                }
            }else { //submitted client id is wrong
                $errors['err'] = 'Internal error';
                $rep = $_POST;
            }
            break;
        case 'mass_delete': //delete clients
            $clients_to_delete = $_POST['delete_client'];
            if ( count( $clients_to_delete ) && Client::delete_client($clients_to_delete, $errors) ) {
                $msg = 'success';
            }
            break;
        case 'save_staff':
            if ($id = Client::save_staff($_POST, $errors)) {
                $msg = 'success';
            }
            $rep = $_POST;
            $rep['id'] = $id;
            break;
        case 'delete_staff':
            $client_id = $_POST['client_id'];
            $staffs = $_POST['staff_ids'];
            if ( Client::delete_staff($staffs, $errors) ) {
                $msg = 'success!';
            }
            break;
    }
}


//serving pages
switch ( $do ) {
    case 'newclient':

        //breadcrumbs
        $bc = new breadcrumbs();
        $bc->add('all clients', $all_clients_url);
        $bc->add('add client', '');
        $breadcrumbs = $bc->get_bcs();
        $bc_text = '';
        foreach( $breadcrumbs as $bc ) {
            $bc_text .= '&lt;&lt;&lt; '.$bc.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        //template vars
        $title = 'Add a client';

        //the template
        $tpl = 'new-client.tpl.php';
        break;

    case 'save_staff':
    case 'newstaff':

        //template vars
        $client_id = $_GET['client_id'];
        $client = new Client($client_id);
        $client_name = $client->getName();
        $title = 'New staff for ' . $client_name;

        //urls
        $client_url = SCP_URL . '/client.php?do=view&id=' . $client_id;
        $add_client_staff_url = SCP_URL . '/client.php?do=newstaff&client_id=' . $client_id;

        //breadcrumbs
        $bc = new breadcrumbs();
        $bc->add('all clients', $all_clients_url);
        $bc->add('view ' . $client_name, $client_url);
        $bc->add('add staff for ' . $client_name, '');
        $breadcrumbs = $bc->get_bcs();
        $bc_text = '';
        foreach( $breadcrumbs as $bc ) {
            $bc_text .= '&lt;&lt;&lt; '.$bc.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if ( $client->getId() ) {
            $tpl = 'new-client-staff.tpl.php';
        }

        break;

    case '':
    case 'mass_delete':
    case 'view_all':

        //template vars
        $title = 'viewing all clients';
        $clients = Client::get_all_clients();

        //breadcrumbs
        $bc = new breadcrumbs();
        $bc->add('all clients', '');
        $breadcrumbs = $bc->get_bcs();
        $bc_text = '';
        foreach( $breadcrumbs as $bc ) {
            $bc_text .= '&lt;&lt;&lt; '.$bc.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        $tpl = 'clientmembers.tpl.php';
        break;

    case 'update_client':
    case 'delete_staff':
    case 'view': //view a client

        if ($id || $client_id) {
            //template variables
            $client = new Client($id);
            if (!$client) {
                $client = new Client($client_id);
            }
            $client_name = $client->getName();
            $client_id = $client->getId();
            $title = 'View/update client ## ' . $client->getName();
            $rep = $client->getInfo();
            $services = Client::get_all_cin($id);
            $staffs = $client->get_all_staff();

            //urls
            $client_url = SCP_URL . '/client.php?do=view&id=' . $client_id;
            $add_client_staff_url = SCP_URL . '/client.php?do=newstaff&client_id=' . $client_id;

            //breadcrumbs
            $bc = new breadcrumbs();
            $bc->add('all clients', $all_clients_url);
            $bc->add('view ' . $client_name, '');
            $breadcrumbs = $bc->get_bcs();
            $bc_text = '';
                foreach( $breadcrumbs as $bc ) {
                    $bc_text .= '&lt;&lt;&lt; '.$bc.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }

            $tpl = 'new-client.tpl.php';
        }
        break;

    case 'save_staff':
    case 'view_staff':

        //template vars
        $client_id = $_REQUEST['client_id'];
        $staff_id = $_REQUEST['staff_id'];
        if ( $client = new Client($client_id) ) {
            if ( $staff = $client->get_staff($staff_id) ) {
            $rep = $rep? $rep : $staff;
            $rep['client_id'] = $client->getId();
            $client_name = $client->getName();
            $tpl = 'new-client-staff.tpl.php';
            } else {
                $errors['err'] = 'no staff, add a new';
                $tpl = 'new-client-staff.tpl.php';
            }
        }

        //urls
        $client_url = SCP_URL . '/client.php?do=view&id=' . $client_id;
        $add_client_staff_url = SCP_URL . '/client.php?do=newstaff&client_id=' . $client_id;

        //breadcrumbs
        $bc = new breadcrumbs();
        $bc->add('all clients', $all_clients_url);
        $bc->add('view ' . $client_name, $client_url);
        $bc->add('all staffs' . $client_name, '');
        $breadcrumbs = $bc->get_bcs();
        $bc_text = '';
        foreach( $breadcrumbs as $bc ) {
            $bc_text .= '&lt;&lt;&lt; '.$bc.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        break;

}
?>

<?php require_once(STAFFINC_DIR . 'header.inc.php'); ?>

<ul class="left_nav">
    <li><a href="client.php?do=view_all">View all clients</a></li>
    <li><a href="?do=newclient">Add client</a></li>
    <?php if ( $client ) { ?>
    <li><a href="<?php   echo   $add_client_staff_url ?>">Add staff for <?php   echo   $client->getName(); ?></a></li>
    <?php } ?>
</ul>

<?php require_once(TEMPLATE_DIR . $tpl); ?>

<script type="text/javascript" src="js/client.js"></script>

<?php require_once(STAFFINC_DIR . 'footer.inc.php'); ?>