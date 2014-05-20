<?php
/**
 * client side include
 * staff management template
 *
 *
 *
 */
if (!defined('OSTCLIENTINC'))
    die('Access Denied');
$rep=null;
$newuser=true;
if($staff && $_REQUEST['a']!='new'){
    $rep=$staff->getInfo();
    $title='Update: '.$rep['firstname'].' '.$rep['lastname'];
    $action='update';
    $pwdinfo='To reset the password enter a new one below';
    $newuser=false;
}else {
    $title='New Staff Member';
    $pwdinfo='Temp password required';
    $action='create';
    $rep['resetpasswd']=isset($rep['resetpasswd'])?$rep['resetpasswd']:1;
    $rep['isactive']=isset($rep['isactive'])?$rep['isactive']:1;
    $rep['dept_id']=$rep['dept_id']?$rep['dept_id']:$_GET['dept'];
    $rep['isvisible']=isset($rep['isvisible'])?$rep['isvisible']:1;
}
$rep=($errors && $_POST)?Format::input($_POST):Format::htmlchars($rep);

//get the goodies.
$groups=db_query('SELECT group_id,group_name FROM '.CLIENT_GROUP_TABLE);
//$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE);

 ?>
<div class="msg"><?php   echo   $title;  ?></div>
<div id="add_staff_form">
    <form name="client_staff_form" action="staff.php" method="get">
        <input type="hidden" name="do" value="<?php   echo   $action;  ?>">
        <input type="hidden" name="a" value="<?php   echo   Format::htmlchars($_REQUEST['a']);  ?>">
        <input type="hidden" name="t" value="client">
        <input type="hidden" name="client_id" value="<?php   echo   $rep['client_id'];  ?>">

        <label>Username:</label><input type="text" name="username" value="<?php   echo   $rep['username'];  ?>">
        &nbsp;<span class="error">*&nbsp;<?php   echo   $errors['username'];  ?></span>
        <br />
        <label>Permission</label>
        <select name="group_id">
            <option value=0>Select Group</option>
            <?php
            while (list($id,$name) = db_fetch_row($groups)){
                $selected = ($rep['group_id']==$id)?'selected':'';  ?>
                <option value="<?php   echo  $id ?>"<?php   echo  $selected ?>><?php   echo  $name ?></option>
            <?php
            } ?>
            </select>&nbsp;<span class="error">*&nbsp;<?php   echo  $errors['group'] ?></span>
        <label>Name:</label>
        <input type="text" name="name" value="<?php   echo   $rep['firstname'];  ?>">&nbsp;<span class="error">*</span>
        <br />
        <label>Email:</label>
        <input type="text" name="email" size=25 value="<?php   echo   $rep['email'];  ?>">
        &nbsp;<span class="error">*&nbsp;<?php   echo   $errors['email'];  ?></span>
        <br />
        <label>Phone</label>
        <input type="text" name="phone" value="<?php   echo   $rep['phone'];  ?>">
        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['phone'];  ?></span>
        <label>Extension</label>
        <input type="text" name="phone_ext" value="<?php   echo   $rep['phone_ext'];  ?>">
        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['phone_ext'];  ?></span>
        <br />
        <label>Mobile Phone:</label>
        <input type="text" name="mobile" value="<?php   echo   $rep['mobile'];  ?>" >
        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['mobile'];  ?></span>
        <br />
        <label>Password:</label>
        <i><?php   echo   $pwdinfo;  ?></i>&nbsp;&nbsp;&nbsp;<span class="error">&nbsp;<?php   echo   $errors['npassword'];  ?></span> <br/>
        <input type="password" name="password" AUTOCOMPLETE=OFF >
        <label>Password(Confirm):</label>
        <input type="password" name="again_password" AUTOCOMPLETE=OFF >
        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['vpassword'];  ?></span>
        <br />
        <div id="add_staff_submit">
            <input class="button" type="submit" name="submit" value="Submit">
            <input class="button" type="reset" name="reset" value="Reset">
            <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="index.php?t=staff"'>
        </div>
    </form>
</div>