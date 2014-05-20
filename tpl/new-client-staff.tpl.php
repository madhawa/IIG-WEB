<?php    echo   $bc_text;  ?>
<div class="space"></div>
<div width="100%">
    <?php if($errors['err']) { ?>
        <p align="center" class="errormessage"><?php    echo  $errors['err'] ?></p>
    <?php }elseif($msg) { ?>
        <p align="center" class="infomessage"><?php    echo  $msg ?></p>
    <?php }elseif($warn) { ?>
        <p class="warnmessage"><?php    echo  $warn ?></p>
    <?php } ?>
</div>

<h2 align="center"><?php    echo   $title;  ?></h2>

<form action="" method="post">
<input type="hidden" name="do" value="save_staff">
<input type="hidden" name="client_id" value="<?php    echo   $client_id;  ?>">
<input type="hidden" name="staff_id" value="<?php    echo   $rep['id'];  ?>">
<table border="0" cellspacing=0 cellpadding=2 class="tform" align="center">
    <tr>
        <th>Employee Name:</th>
        <td><input type="text" name="staff_name" value="<?php    echo   $rep['staff_name'];  ?>" required></td>
    </tr>
    <tr>
        <th>Phone number:</th>
        <td><input type="text" name="phone" placeholder="" value="<?php    echo   $rep['phone'];  ?>" required></td>
    </tr>
    <tr>
        <th>email:</th>
        <td><input type="text" name="email" placeholder="" value="<?php    echo   $rep['email'];  ?>" required></td>
    </tr>
    <tr>
        <th>Designation</th>
        <td>
            <select name="designation" required>
                <option value=''>Please select</option>
                <option value='Manager' <?php  if ($rep['designation'] == 'Manager')   echo   'selected';  ?> >Manager</option>
                <option value='MD' <?php  if ($rep['designation'] == 'MD')   echo   'selected';  ?> >MD</option>
                <option value='Director' <?php  if ($rep['designation'] == 'Director')   echo   'selected';  ?> >Director</option>
                <option value='CTO' <?php  if ($rep['designation'] == 'CTO')   echo   'selected';  ?> >CTO</option>
                <option value='Assistant manager' <?php  if ($rep['designation'] == 'Assistant manager')   echo   'selected';  ?> >Assistant manager</option>
                <option value='Deputy manager' <?php  if ($rep['designation'] == 'Deputy manager')   echo   'selected';  ?> >Deputy manager</option>
                <option value='Senior_manager' <?php  if ($rep['designation'] == 'Senior manager')   echo   'selected';  ?> >Senior Manager</option>
                <option value='NOC Engineer' <?php  if ($rep['designation'] == 'NOC Engineer')   echo   'selected';  ?> >NOC Engineer</option>
        </td>
    </tr>
    <tr>
        <th>Department</th>
        <td>
            <select name="department" required>
                <option value=''>Please select</option>
                <option value='Management' <?php  if ($rep['department'] == 'Management')   echo   'selected';  ?> >Management</option>
                <option value='Accounts' <?php  if ($rep['department'] == 'Accounts')   echo   'selected';  ?> >Accounts</option>
                <option value='Transmission' <?php  if ($rep['department'] == 'Transmission')   echo   'selected';  ?> >Transmission</option>
                <option value='Datacom' <?php  if ($rep['department'] == 'Datacom')   echo   'selected';  ?> >Datacom</option>
                <option value='Power' <?php  if ($rep['department'] == 'Power')   echo   'selected';  ?> >Power</option>
                <option value='NOC' <?php  if ($rep['department'] == 'NOC')   echo   'selected';  ?> >NOC</option>
        </td>
    </tr>
    <tr>
        <td colspan="2"><button class="save" type="submit">Save</button></td>
    </tr>
</table>
</form>