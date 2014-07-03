<?php 
//client side login template
if(!defined('OSTCLIENTINC')) die('Kwaheri');

$e=Format::input($_POST['login_field']?$_POST['login_field']:$_GET['e']);
$t=Format::input($_POST['passwd']?$_POST['lticket']:$_GET['t']);
 ?>

<!--
<p style="float: right"><a href="./scp">go to 1asia-ahl executive login</a></p>
<div class="clear"></div>
-->
<h1 align="center">1Asia Alliance Support Center</h1>
<br>
<br>

<!--
<div>
    <?php  if($loginmsg) { ?>
        <p align="center" id="errormessage"><?php    echo  $loginmsg ?></p>
    <?php  }elseif($warn) { ?>
        <p class="warnmessage"><?php    echo  $warn ?></p>
    <?php } ?>
</div>
-->

<div id="loginBox">
    <?php  if($loginmsg) { ?>
        <p style="color: #990033; font-weight: bold; text-align: center"><?php    echo   $loginmsg  ?></p>
        <br>
    <?php  }  ?>
	<form action="" method="post">
        <input type="hidden" name="do" value="clientlogin" />
        <table border=0 align="center">
            <tr>
                <!-- TODO: if name is different then login_field then form is not submitted -->
                <td>
                    <p style="font-weight: bold">Username</p>
                    <input type="text" name="login_field" id="name" value="" >
                </td>
            </tr>
            <tr>
                <td>
                    <p style="font-weight: bold; margin-top: 10px;">Password</p>
                    <input type="password" name="password" id="pass">
                </td>
            </tr>
            <tr>
                <td>
                    <input style="margin-top: 30px; padding: 10px 20px" class="save" type="submit" name="submit" value="Login">
                </td>
            </tr>
        </table>
    </form>
    <span id="login_report"></span>
</div>
<script type="text/javascript">
    $('body').css({ 'background': '' });
    $('body').css({ 'background-image': 'linear-gradient(#0594BE, #023149)' });
    //$('#container').css({ 'background-image': 'linear-gradient(#0594BE, #023149)' });
    $('div#loginBox').css({
        'border': '1px solid',
        'padding': '10px 40px 50px 40px',
        'display': 'table',
        'margin-left': 'auto',
        'margin-right': 'auto',
        'background-color': '#0594BE'
        });
</script>