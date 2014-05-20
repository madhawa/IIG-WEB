<?php
//TODO: show all staffs under a client as tree
//  echo   "now i'm in clientmemebers.inc.php";
//to be used in admin page and engineering page(provisioning) to generate all clients and add services
/*
if (!defined('ENGINEER_INC') || !$thisuser->isEngineer())
    die('Access Denied');
*/

//List all staff members...not pagenating...
$sql = 'SELECT client_id, client_name, client_type, client_org_designation' .
        ',client_org_department,DATE(created) as created,DATE(lastlogin) as lastlogin ' .
        ' FROM ' . CLIENT_TABLE .
        ' WHERE client_id=boss_id';

if ( $criteria['what'] && $criteria['type'] ) { //for client search query
    switch( $criteria['type'] ) {
        case 'id':
            $criteria['type'] = 'client_id';
            break;
        case 'name':
            $criteria['type'] = 'client_name';
            break;
        case 'type':
            $criteria['type'] = 'client_type';
            break;
    }
    $sql .= ' AND '.db_input($criteria['type'], false).' LIKE "%'.db_input($criteria['what'], false).'%"';
}

$users = db_query($sql . ' ORDER BY client_name');

$showing = ($num = db_num_rows($users)) ? "Clients" : "No clients found. <a href='admin.php?t=client&a=new'>Add New User</a>.";
?>
<div id="search_client_div">
    <form id="search_client" method="post" action="">
        <input type="hidden" name="t" value="client">
        <input type="hidden" name="do" value="search">
        client field:
        <input type="text" name="search_client" required="required">
        search type:
        <select name="search_type" required="required">
            <option value="id">client id</option>
            <option value="name">client name</option>
            <option value="type">client type</option>
        </select>
        <input type="submit" name="search_button" value="search">
    </form>
</div>
<h2 align="center">View all clients</h2>
<div id="all_client">
	<form action="" method="post">
		<table border="1" cellspacing=0 cellpadding=2 class="dtable" align="center">
			<tr>
				<th>Name</th>
				<th>Client Type</th>
				<?php if ($target_page != 'services') { ?> <!-- if for services page -->
					<th>Designation</th>
					<th>Department</th>
					<!--
					<th>Created</th>
					<th>Last Login</th>
					-->
					<th>Delete the client</th>
				<?php } ?>
			</tr>
			<?php
			$class = 'row1';
			$total = 0;
			$uids = ($errors && is_array($_POST['uids'])) ? $_POST['uids'] : null;
			if ($users && db_num_rows($users)) {
				//  echo   db_num_rows($users);
				while ($row = db_fetch_array($users)) {
					$sel = false;
					if (($uids && in_array($row['client_id'], $uids)) or ($uID && $uID == $row['client_id'])) {
						$class = "$class highlight";
						$sel = true;
					}
					$name = $row['client_name'];

					$sql_cin = 'SELECT client_id, service_type FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($row['client_id']);
					$type_data = db_query($sql_cin);
					$services = '';
					while ($cin_row = db_fetch_array($type_data)) {
                        $services = $services . ',' . $cin_row['service_type'];
					}
					$services = trim($services, ',');
					?>
					<tr class="<?php   echo   $class ?>" id="<?php   echo   $row['client_id'] ?>">

                        <td><a href="client.php?do=view&amp;id=<?php   echo   $row['client_id'] ?>"><?php   echo   Format::htmlchars($name) ?></a>&nbsp;</td>

						<td>
                            <?php
                                if ($row['client_type']) {
                                      echo   $row['client_type'];
                                }else {
                                      echo   $services;
                                }
                            ?>
                        </td>
						<?php if ($target_page != 'services') { ?>
							<td><?php   echo   $row['client_org_designation'] ?></td>
							<td><?php   echo   $row['client_org_department'] ?></td>
							<!--
							<td><?php   echo   Format::db_date($row['created']) ?></td>
							<td><?php   echo   Format::db_datetime($row['lastlogin']) ?>&nbsp;</td>
							-->
						<td><input type="checkbox" name="delete_client[]" value="<?php   echo   $row['client_id'] ?>"></td>
						<?php } ?>
					</tr>
					<?php
					$class = ($class == 'row2') ? 'row1' : 'row2';
				} //end of while.
				?>
				<?php if ($target_page != 'services') { ?>
				<tr>
					<td colspan="4"></td>
					<td>
						<input class="button" type="submit" name="mass_process_clients" value="apply">
					</td>
				</tr>
				<?php } ?>
				<?php
			} else {
				?>
				<tr class="<?php   echo   $class ?>"><td colspan=8><b>Query returned 0 results</b></td></tr>
			<?php } ?>
		</table>
	<br>
	<br>
	</form>
</div>

<script type="text/javascript">
    $('th, td').css('padding', '20px');

//to invoke search form and set param
    $('a[href=#search_client]').click(function() {
        $('div#search_client_div').show();
    });
    $('div#all_client').click(function(event) {
        event.stopPropagation();
        $('div#search_client_div').hide();
    });
</script>