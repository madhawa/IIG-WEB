<?php

if (!defined('OSTADMININC') || !$thisuser->isadmin())
    die('Access Denied');
$topbar_email = '
<div>
    <a href="admin.php?t=email">Email Addresses</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=email&a=new">Add new email</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=templates">Email Templates</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=banlist">Banned email</a>
    <hr>
</div> ';
$topbar_staff = '
<div>
    <a href="admin.php?t=staff">Staff members</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=staff&a=new">Add new staff</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=groups">User groups</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=groups&a=new">Add new group</a>
    <hr>
</div> ';
$topbar_client = '<a href="admin.php?t=client">All clients</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="admin.php?t=client&a=new">Add new client</a>';
$topbar_depts = '<a href="admin.php?t=depts">Departments</a>
    &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?t=depts&a=new">Add new department</a>';
switch ($thistab):
    case 'email':
    case 'templates':
    case 'banlist':
          echo   $topbar_email;
        break;
    case 'grp':
    case 'groups':
    case 'staff':
          echo   $topbar_staff;
        break;
    case 'client':
          echo   $topbar_client;
        break;
    case 'dept':
    case 'depts':
          echo   $topbar_depts;
        break;
endswitch;
?>