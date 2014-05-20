<?php
if (!defined('OSTSCPINC') || !@$thisuser->isStaff())
    die('Access Denied');
$qstr = '&'; //Query string collector
if ($_REQUEST['status']) { //Query string status has nothing to do with the real status used below; gets overloaded.
    $qstr.='status=' . urlencode($_REQUEST['status']);
}
if ($_REQUEST['order']) {
    $order = $orderWays[$_REQUEST['order']];
}
if ($_GET['limit']) {
    $qstr.='&limit=' . urlencode($_GET['limit']);
}

$negorder = $order == 'DESC' ? 'ASC' : 'DESC';



$loggedin_staff_dept_id = (string)($thisuser->getDeptId());
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$pagelimit = $_GET['limit'] ? $_GET['limit'] : $thisuser->getPageLimit();
$pagelimit = $pagelimit ? $pagelimit : PAGE_LIMIT; //true default...if all fails.
$total = db_count("SELECT count(DISTINCT order_id) FROM " . ORDER_TABLE . " WHERE dept_id=" . $loggedin_staff_dept_id);

$pageNav = new Pagenate($total, $page, $pagelimit);
$pageNav->setURL('orders.php', $qstr . '&sort=' . urlencode($_REQUEST['sort']) . '&order=' . urlencode($_REQUEST['order']));
$showing = '';

$sql = '';
$sql = "SELECT order_id,status,created_date,ip_order_created_from,created_by,client_id,customer_name,ip_order_created_from,assigned_staff_id FROM " . ORDER_TABLE . " WHERE dept_id=" . db_input($loggedin_staff_dept_id) ;

//when order search form is submitted
if ( $_POST['order_search_by'] == 'client_id' ) {
    $field_name = 'client_id';
    $field_value = $_POST['search_by_client'];
    
    $sql = "SELECT order_id,status,created_date,ip_order_created_from,created_by,client_id,customer_name,ip_order_created_from,assigned_staff_id FROM " . ORDER_TABLE . " WHERE ".$field_name . "=" . db_input($field_value) ;
    
} elseif ( $_POST['order_search_by'] == 'order_id' ) {
    $field_name = 'order_id';
    $field_value = $_POST['search_by_order_id'];
    
    $sql = "SELECT order_id,status,created_date,ip_order_created_from,created_by,client_id,customer_name,ip_order_created_from,assigned_staff_id FROM " . ORDER_TABLE . " WHERE " . $field_name . "=" . db_input($field_value) ;
}

//TODO: make the pagination work
//$sql = $sql . $pageNav->getStart() . "," . $pageNav->getLimit();
//  echo   $sql . '<br />';

//mysql_query($sql) or die(mysql_error());
if ($orders_res = db_query($sql)) {
$showing = db_num_rows($orders_res) ? $pageNav->showing() : "";
}
else {
    $errors['err'] = 'no orders found';
}
//$row = db_fetch_array($orders_res);
?>

<?php if ($errors['err']) { ?>
    <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
<?php } ?>
<?php if ($msg) { ?>
    <p align="center" id="infomessage"><?php   echo   $msg ?></p>
<?php } ?>
<?php if ($warn) { ?>
    <p id="warnmessage"><?php   echo   $warn ?></p>
<?php } ?>
<br>
<br>
<br>
<div id="search_order">
    <?php
        $sql = 'SELECT client_id, client_name FROM ' . CLIENT_TABLE ;
        $client_stack = db_query($sql);    
    ?>
    <form action="" method="post">
        <input type="hidden" name="do" value="search_order">
        <div id="search_by">
            search by
            <select name="order_search_by">
                <option value="">Slelect</option>
                <option value="client_id">Client Name</option>
                <option value="order_id">Order Id</option>
            </select>
        </div>

        
        <div id="search_by_order_id">
            order id: <input type="text" name="search_by_order_id" value="">
        </div>
        <div id="search_by_client">
            select client :
            <select name="search_by_client">
                <?php
                    while ( $row = db_fetch_array($client_stack) ) {
                        ?>
                        <option value="<?php   echo   $row['client_id']; ?>"><?php   echo   $row['client_name']; ?></option>
                        <?php
                    }
                ?>
            </select>
        </div>
        <br>
        <br>
        <input type="submit" name="search_the_order" value="search for order">
    </form>
    
    
    <script type="text/javascript">
        $('div#search_by_client').hide();
        $('div#search_by_order_id').hide();
        
        $('select[name="order_search_by"]').change(function(event) {
            var search_type = $(event.target).val();
            if ( search_type == 'client_id' ) {
                $('div#search_by_client').show();
                $('div#search_by_order_id').hide();
                $('div#search_by, div#search_by_client').css('display', 'inline-block');
            } else if ( search_type == 'order_id' ) {
                $('div#search_by_client').hide();
                $('div#search_by_order_id').show();
                $('div#search_by, div#search_by_order_id').css('display', 'inline-block');
            }
        });
    </script>
    
</div>

<div id="orders_list">
    <table>
        <tr>
            <td class="msg" >&nbsp;<b><?php   echo   $showing ?>&nbsp;&nbsp;&nbsp;<?php   echo   $results_type ?></b></td>
            <td>
                <a href=""><img src="images/refresh.gif" alt="Refresh" border=0></a>
            </td>
        </tr>
    </table>
    <!--
    <div class="color_code">
        <div class="col_code_inline" id="pending_col_code">
        </div>
        <span class="col_code_inline">
            pending
        </span>
        <br>
        
        <div class="col_code_inline" id="accepted_col_code">
        </div>
        <span class="col_code_inline">
            accepted
        </span>
        <br>
        
        <div class="col_code_inline" id="rejected_col_code">
        </div>
        <span class="col_code_inline">
            rejected
        </span>
        <br>
        
        <div class="col_code_inline" id="cancelled_col_code">
        </div>
        <span class="col_code_inline">
            cancelled
        </span>
        <br>
        
        <div class="col_code_inline" id="scp_col_code">
        </div>
        <span class="col_code_inline">
            created by asiaahl staff
        </span>
    </div>
    -->

        <form action="orders.php" method="POST" name='orders'>
            <input type="hidden" name="a" value="mass_process" >
                    <table width="100%" border="0" class="dtable" align="center">
                        <tr>
                            <th width="20%" >Order Id</th>
                            <th width="20%">
                                <a href="orders.php?sort=date&order=<?php   echo   $negorder ?><?php   echo   $qstr ?>" title="Sort By Date <?php   echo   $negorder ?>">Date</a></th>
                            <th width="15%">Status</th>
                            <th width="20%">Client name</th>
                            <th width="20%">Delete</th>
                        </tr>
                        <?php
                        $class = "row1";
                        $total = 0;
                        if ($orders_res && ($num = db_num_rows($orders_res))) {
                            while ($row = db_fetch_array($orders_res)) {
                                $row['source']='web';
                                $tag = $row['assigned_staff_id'] ? 'assigned' : 'pending';
                                $flag = null;
                                //if($row['lock_id']) $flag='locked';
                                if($row['assigned_staff_id']) $flag = 'assigned';
                                //elseif($row['isoverdue']) $flag='reject';

                                $order_id = $row['order_id'];
                                $status = $row['status'];
                                if (!strcasecmp($row['status'], 'pending')) {
                                    $order_id = sprintf('<b>%s</b>', $order_id);
                                    //$status=sprintf('<b>%s</b>',Format::truncate($row['status'],40)); // Making the status bold is too much for the eye
                                }
                                if (!strcasecmp($row['status'], 'accepted')) {
                                    $class = 'accepted_orders';
                                }
                                if (!strcasecmp($row['status'], 'rejected')) {
                                    $class = 'rejected_orders';
                                }
                                if (!strcasecmp($row['status'], 'cancelled')) {
                                    $class = 'calcelled_orders';
                                }
                                if (!strcasecmp($row['status'], 'pending')) {
                                    $class = 'pending_orders';
                                }
                                if ( !$row['client_id'] ) {
                                    $class = 'created_in_scp';
                                }
                                ?>
                                <!--<tr class="<?php   echo   $class ?> " id="<?php   echo   $row['order_id'] ?>"> --> <!-- disable color codes -->
                                <tr id="<?php   echo   $row['order_id'] ?>">
                                    <td align="center" title="<?php   echo   $row['client_email'] ?>" nowrap>
                                        <a class="Icon <?php   echo   strtolower($row['source']) ?>ticket" title="<?php   echo   'IP:'; ?> : <?php   echo   $row['ip_order_created_from'] ?>" 
                                           href="orders.php?id=<?php   echo   $row['order_id'] ?>"><?php   echo   $order_id ?></a></td>
                                    <td align="center" nowrap><?php   echo   Format::db_date($row['created_date']) ?></td>
                                    <td nowrap class="nohover" ><?php   echo   $row['status'] ?></td>
                                    <td nowrap><?php   echo   Format::truncate($row['customer_name'], 22, strpos($row['customer_name'], '@')) ?>&nbsp;</td>
                                    <td nowrap><input type="checkbox" name="delete_orders[]" value="<?php   echo   $row['order_id']; ?>">delete</td>
                                </tr>
                                <?php
                                $class = ($class == 'row2') ? 'row1' : 'row2';
                            } ?>
                            <tr>
                                <td colspan="4"></td>
                                <td><input type="submit" name="delete" value="delete"></td>
                            </tr>
                                <script type="text/javascript">
                                    $('[name="delete"]').click(function(event) {
                                        var r = window.confirm("are you suuuuuureeeeeeee?????");
                                        if ( r != true ) {
                                            event.preventDefault();
                                        }
                                    });
                                </script>
                        <?php } else { //not orders found!! 
                            ?>

                            <tr class="<?php   echo   $class ?>"><td colspan="5"><b>Query returned 0 results.</b></td></tr>
                        <?php } ?>
                    </table>
        </form>
<br>
<br>
page:<?php   echo   $pageNav->getPageLinks() ?>

<br>
<br>
</div>