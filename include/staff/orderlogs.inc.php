<?php
if(!defined('OSTSCPINC') || !@$thisuser->isStaff()) die('Access Denied');

$qstr='&t=orderlogs'; //Query string collector
if($_REQUEST['type']) { 
    $qstr.='&amp;type='.urlencode($_REQUEST['type']);
}

$type=null;

switch(strtolower($_REQUEST['type'])){
    case 'new':
        $title='New Order';
        $type='created';
        break;
    case 'accepted':
        $title='Accepted Orders';
        $type='accepted';
        break;
    case 'rejected':
        $title='Rejected Orders';
        $type='rejected';
        break;
    case 'cancelled':
        $title = 'Cancelled Orders';
        $type = 'cancelled';
        break;
    case 'updated':
        $title = 'Updated Orders';
        $type = 'updated';
        break;
    default:
        $type=null;
        $title='All logs';
}

$qwhere =' WHERE 1';
//$qwhere ='';

//Type
if($type){
    $qwhere.=' AND log_type='.db_input($type);    
}

//dates
$startTime  =($_REQUEST['startDate'] && (strlen($_REQUEST['startDate'])>=8))?strtotime($_REQUEST['startDate']):0;
$endTime    =($_REQUEST['endDate'] && (strlen($_REQUEST['endDate'])>=8))?strtotime($_REQUEST['endDate']):0;
if( ($startTime && $startTime>time()) or ($startTime>$endTime && $endTime>0)){
    $errors['err']='Entered date span is invalid. Selection ignored.';
    $startTime=$endTime=0;    
}else{
    
    //Have fun with dates.
    
    if($startTime){
    
        $qwhere.=' AND log_date>=FROM_UNIXTIME('.$startTime.')';
            
                
        $qstr.='&startDate='.urlencode($_REQUEST['startDate']);
                        
        
        
    }
    
    if($endTime){
    
        $qwhere.=' AND log_date<=FROM_UNIXTIME('.$endTime.')';
        
        $qstr.='&endDate='.urlencode($_REQUEST['endDate']);
        
    }
}

$qselect = 'SELECT log.* ';
$qfrom=' FROM '.ORDER_LOG_TABLE.' log ';
//get log count based on the query so far..
$total=db_count("SELECT count(*) $qfrom $qwhere");
$pagelimit=30;
$page = ($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
//pagenate
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('admin.php',$qstr);
$query="$qselect $qfrom $qwhere ORDER BY log.log_date DESC LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
  echo   $query . '<br />';
mysql_query($query) or die(mysql_error());
$result = db_query($query);
$showing=db_num_rows($resp)?$pageNav->showing():"";
 ?>
<div class="msg">Service order logs Logs</div>
<div id='filter' >
 <form action="admin.php?t=orderlogs" method="get">
    <input type="hidden" name="t" value="orderlogs" />
    <div style="padding-left:15px;">
        Date Span:
        &nbsp;From&nbsp;<input id="sd" size=15 name="startDate" value="<?php   echo   Format::htmlchars($_REQUEST['startDate']) ?>" 
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF>
            <a href="#" onclick="event.cancelBubble=true;calendar(getObj('sd')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp; to &nbsp;&nbsp;
            <input id="ed" size=15 name="endDate" value="<?php   echo   Format::htmlchars($_REQUEST['endDate']) ?>" 
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF >
                <a href="#" onclick="event.cancelBubble=true;calendar(getObj('ed')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp;
            &nbsp;Type:
            <select name='type'>
                <option value="" selected>All</option>
                <option value="new" <?php   echo   ($type=='new')?'selected="selected"':'' ?>>New Orders</option>
                <option value="accepted" <?php   echo   ($type=='accepted')?'selected="selected"':'' ?>>Accepted Orders</option>
                <option value="rejected" <?php   echo   ($type=='rejected')?'selected="selected"':'' ?>>Rejected Orders</option>
                <option value="cancelled" <?php   echo   ($type=='cancelled')?'selected="selected"':'' ?>>Cancelled Orders</option>
                <option value="updated" <?php   echo   ($type=='updated')?'selected="selected"':'' ?>>Updated Orders</option>
            </select>
            &nbsp;&nbsp;
            <input type="submit" Value="View" />
    </div>
 </form>
</div>
<div style="margin-bottom:20px; padding-top:0px;">
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="tickets.php" method="POST" name='tickets' onSubmit="return checkbox_checker(this,1,0);">
    <input type="hidden" name="a" value="mass_process" >
    <input type="hidden" name="status" value="<?php   echo   $statusss ?>" >
    <tr><td>
       <table width="100%" border="0" cellspacing=0 cellpadding=2 class="logs" align="center">
        <tr><th><?php   echo   $title ?></th></tr>
        <?php
        $class = "row1";
        $total=0;
        if($result && ($num=db_num_rows($result))):
            $icons=array('accepted'=>'debugLog','rejected'=>'alertLog','cancelled'=>'errorLog','created'=>'debugLog','updated'=>'debugLog');
            while ($row = db_fetch_array($result)) {
                $icon=isset($icons[$row['log_type']])?$icons[$row['log_type']]:'debugLog';
                 ?>
            <tr class="<?php   echo   $class ?> " id="<?php   echo   $row['id'] ?>">
                <td>
                  <a href="javascript:toggleMessage('<?php   echo   $row['id'] ?>');">
                  <img border="0" align="left" id="img_<?php   echo   $row['id'] ?>" src="images/plus.gif">
                  <span style="color:000; float: left; width:190px;"><?php   echo   Format::db_daydatetime($row['log_date']) ?></span>
                  &nbsp;&nbsp;
                  <span class="Icon <?php   echo   $icon ?>"><?php   echo  Format::htmlchars($row['title']) ?></span></a>
                    <div id="msg_<?php   echo   $row['id'] ?>" class="hide">
                        <hr>
                        <?php   echo   'order : <a href="orders.php?id='.Format::display($row['order_id']).'">'.Format::display($row['order_id']) . '</a> '.Format::display($row['log_type']).'    by userid:: '.Format::display($row['user_id']).'    at ' . Format::display($row['log_date']) . '    from ip '.Format::display($row['ip']) ?>
                        <span style="text-align:right;float:right;"><i><?php   echo   Format::htmlchars($row['ip']) ?>&nbsp;&nbsp;</i></span>
                    </div>

                </td>
            </tr>
            <?php
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //not tickets found!!  ?> 
            <tr class="<?php   echo   $class ?>"><td><b>Query returned 0 results.</b></td></tr>
        <?php
        endif;  ?>
       </table>
    </td></tr>
    <?php
    if($num>0){ 
     ?>
        <tr><td style="padding-left:20px">page:<?php   echo   $pageNav->getPageLinks() ?></td></tr>
    <?php }  ?>
    </form>
 </table>
</div>
<?php
