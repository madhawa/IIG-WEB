<?php  if(!defined('OSTSCPINC') || !is_object($thisuser) || !$thisuser->isStaff() || !is_object($nav)) die('Access Denied');  ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="developer" content="Minhaj; email=polarglow06@gmail.com">
<meta name="portal-developer" content="Minhaj; email=polarglow06@gmail.com">
<meta name="crm-developer" content="Minhaj; email=polarglow06@gmail.com">
<meta name="developer-email" content="polarglow06@gmail.com">
<?php 
if(defined('AUTO_REFRESH') && is_numeric(AUTO_REFRESH_RATE) && AUTO_REFRESH_RATE>0){ //Refresh rate
  echo   '<meta http-equiv="refresh" content="'.AUTO_REFRESH_RATE.'" />';
}
 ?>
<title>1Asia-ahl support center Control Panel</title>
<link rel="shortcut icon" href="../images/favicon.ico">
<link rel="stylesheet" href="css/main.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="css/tabs.css" type="text/css" media="screen"/>

<link rel="stylesheet" href="../styles/jquery-ui.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="./chat/js/jScrollPane/jScrollPane.css" />
<link rel="stylesheet" type="text/css" href="./chat/css/page.css" />
<link rel="stylesheet" type="text/css" href="./chat/css/chat.css" />

<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js" ></script>
<script type="text/javascript" src="../js/json2.js" ></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/scp.js"></script>
<script type="text/javascript" src="js/tabber.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>


<script type="text/javascript" src="js/basic.js"></script>
<script type="text/javascript" src="js/dynamics.js"></script>

<?php 
if($cfg && $cfg->getLockTime()) { //autoLocking enabled. ?>
<script type="text/javascript" src="js/autolock.js" charset="utf-8"></script>
<?php } ?>
</head>
<body>
<?php 
if($sysnotice){ ?>
<div id="system_notice"><?php    echo   $sysnotice;  ?></div>
<?php  
}
$img_name = '';
$current_dept = $thisuser->getDeptName();
switch(strtolower($current_dept)) {
    case 'sales':
        $img_name = 'images/sale_dept_name.png';
        break;
    case 'billing':
        $img_name = 'images/billing_dept_name.png';
        break;
    case 'provisioning':
        $img_name = 'images/provisioning_dept_name.png';
        break;
}
 ?>
<div id="container">
<noscript>
<div align="center">
    <h1 style="color:red"> enable javascript </h1>
</div>
</noscript>
    <div id="header">
        <?php  if ($img_name) {  ?>
        <img src="<?php    echo   $img_name;  ?>" alt="department name">
        <?php  }  ?>
        <p id="info">Welcome back, <strong><?php    echo  $thisuser->getUsername() ?></strong> 
           <?php 
            if($thisuser->isAdmin() && !defined('ADMINPAGE')) {  ?>
            | <a href="admin.php">Admin Panel</a> 
            <?php }else{ ?>
            | <a href="index.php">Staff Panel</a>
            <?php } ?>
            | <a href="profile.php?t=pref">My Preference</a> | <a href="logout.php">Log Out</a></p>
    </div>
    <div id="nav">
        <ul id="main_nav" <?php    echo  !defined('ADMINPAGE')?'class="dist"':'' ?>>
            <?php 
            if(($tabs=$nav->getTabs()) && is_array($tabs)){
             foreach($tabs as $tab) {  ?>
                <li><a <?php    echo  $tab['active']?'class="active"':'' ?> href="<?php    echo  $tab['href'] ?>" title="<?php    echo  $tab['title'] ?>"><?php    echo  $tab['desc'] ?></a></li>
            <?php }
            }else{ //??  ?>
                <li><a href="profile.php" title="My Preference">My Account</a></li>
            <?php } ?>
        </ul>
        <ul id="sub_nav">
            <?php 
            if(($subnav=$nav->getSubMenu()) && is_array($subnav)){
              foreach($subnav as $item) {  ?>
                <li><a class="<?php    echo  $item['iconclass'] ?>" href="<?php    echo  $item['href'] ?>" title="<?php    echo  $item['title'] ?>"><?php    echo  $item['desc'] ?></a></li>
              <?php }
            } ?>
        </ul>
    </div>
    <div class="clear"></div>
    <div id="content" width="100%">
    
        <div id="sticky_top_bar">
            <div id="show_notf_button">
                notifications
                <span>
                </span>
            </div>
            <div id="show_port_mapping">
                odf port mapping
            </div>
        </div>