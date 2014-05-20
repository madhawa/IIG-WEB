<?php
if (!defined('CLIENTSOFINC'))
    die('Access Denied');

//TODO: log permission denied issues, access violations
if ($thisuser->isClientAdmin() || $thisuser->onlyView()) {
    $qstr = '&amp;'; //Query string collector
    if ($_REQUEST['status']) { //Query string status has nothing to do with the real status used below; gets overloaded.
        $qstr.='status=' . urlencode($_REQUEST['status']);
    }
    if ($_REQUEST['order']) {
        $order = $orderWays[$_REQUEST['order']];
    }
    if ($_GET['limit']) {
        $qstr.='&amp;limit=' . urlencode($_GET['limit']);
    }

    $negorder = $order == 'DESC' ? 'ASC' : 'DESC';

    $loggedin_staff_dept_id = (string) ($thisuser->getDeptId());
    $page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
    $pagelimit = $_GET['limit'] ? $_GET['limit'] : $thisuser->getPageLimit();
    $pagelimit = $pagelimit ? $pagelimit : PAGE_LIMIT; //true default...if all fails.
    $total = db_count("SELECT count(DISTINCT order_id) FROM " . ORDER_TABLE . " WHERE client_id=" . db_input($thisuser->getId()));

    $pageNav = new Pagenate($total, $page, $pagelimit);
    $pageNav->setURL('orders.php', $qstr . '&amp;sort=' . urlencode($_REQUEST['sort']) . '&amp;order=' . urlencode($_REQUEST['order']));
    $showing = '';

    //TODO: currently it checks user id of currently logged in user, which means a staff can only view orders he created
    $sql = '';
    $sql = "SELECT order_id,status,created_date,ip_order_created_from,client_id,customer_name,ip_order_created_from,assigned_staff_id,dept_id,client_cancelled FROM " . ORDER_TABLE . " WHERE client_cancelled=0 AND client_id=" . db_input($thisuser->getId());

    $notfound_string = '';
    //$asked_status is set in orders.php
    if ($asked_status) {
        switch ($asked_status) {
            case 'accepted':
                $notfound_string = '0 accepted orders';
                $sql = $sql . ' AND status=' . db_input('accepted');
                break;
            case 'rejected':
                $notfound_string = '0 rejected orders';
                $sql = $sql . ' AND status=' . db_input('rejected');
                break;
            case 'cancel':
                $notfound_string = '0 cancelable orders';
                require_once(INCLUDE_DIR . 'class.dept.php');
                $sql = $sql . ' AND dept_id<>' . Dept::getIdByName('provisioning');
                break;
            default:
                $errors[err] = 'unknown action';
        }
    }

    //TODO: make the pagination work
    //$sql = $sql . $pageNav->getStart() . "," . $pageNav->getLimit();
    //  echo   $sql . '<br />';
    //mysql_query($sql) or die(mysql_error());
    if ($orders_res = db_query($sql)) {
        if (!db_num_rows($orders_res)) $errors['err'] = $notfound_string;
        else $showing = db_num_rows($orders_res) ? $pageNav->showing() : "";
    } else {
        $errors['err'] = 'error in database query';
    }
} else
    $errors['err'] = 'access denied, you have no permission for this action';
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

<?php if (($thisuser->isClientAdmin() || $thisuser->onlyView())) { ?>
    <div id="grouped_orders">
        <table>
            <tr>
                <td width="80%" class="msg" >&nbsp;<b><?php   echo   $showing ?>&nbsp;&nbsp;&nbsp;<?php   echo   $results_type ?></b></td>
                <td nowrap style="text-align:right;padding-right:20px;">
                    <a href=""><img src="images/refresh.gif" alt="Refresh" border=0></a>
                </td>
            </tr>
        </table>
        <table>
            <input type="hidden" name="a" value="mass_process" >
            <input type="hidden" name="status" value="<?php   echo   $statusss ?>" >
            <tr><td>
                    <table class="dtable">
                        <tr>
                            <th width="20%" >Order Id</th>
                            <th width="20%">
                                <a href="orders.php?sort=date&amp;order=<?php   echo   $negorder ?><?php   echo   $qstr ?>" title="Sort By Date <?php   echo   $negorder ?>">Created Date</a></th>
                            <th >Status</th>
                        </tr>
                        <?php
                        $class = "row1";
                        $total = 0;
                        if ($orders_res && ($num = db_num_rows($orders_res))):
                            while ($row = db_fetch_array($orders_res)) {
                                $row['source'] = 'web';
                                $tag = $row['assigned_staff_id'] ? 'assigned' : 'pending';
                                $flag = null;
                                //if($row['lock_id']) $flag='locked';
                                if ($row['assigned_staff_id'])
                                    $flag = 'assigned';
                                //elseif($row['isoverdue']) $flag='reject';

                                $order_id = $row['order_id'];
                                $status = $row['status'];
                                if (!strcasecmp($row['status'], 'pending')) {
                                    $order_id = sprintf('<b>%s</b>', $order_id);
                                    //$status=sprintf('<b>%s</b>',Format::truncate($row['status'],40)); // Making the status bold is too much for the eye
                                }
                                ?>
                                <tr class="<?php   echo   $class ?> " id="<?php   echo   $row['order_id'] ?>">
                                    <td title="<?php   echo   $row['client_email'] ?>" nowrap>
                                        <a class="Icon <?php   echo   strtolower($row['source']) ?>ticket" title="<?php   echo   'IP:'; ?> : <?php   echo   $row['ip_order_created_from'] ?>" 
                                           href="orders.php?id=<?php   echo   $row['order_id'] ?>"><?php   echo   $order_id ?></a></td>
                                    <td nowrap><?php   echo  $row['created_date'] ?></td>
                                    <td nowrap class="nohover" ><?php
                    $status = strtolower($row['status']) == 'accepted' ? 'service delivered in ' . $row['updated_date'] : $row['status'];
                      echo   $status;
                                ?></td>
                                </tr>
                                <?php
                                $class = ($class == 'row2') ? 'row1' : 'row2';
                            } //end of while.
                        else: //not orders found!! 
                            ?> 
                            <tr class="<?php   echo   $class ?>"><td colspan=8><b>Query returned 0 results.</b></td></tr>
                        <?php endif; ?>
                    </table>
                </td></tr>
        </table>
    </div>
<?php } ?>