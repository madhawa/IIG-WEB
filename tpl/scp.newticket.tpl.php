<form action="tickets.php" method="post" enctype="multipart/form-data">
<input type='hidden' name='a' value='select_client'>
<tr>
    <td align="left" ><b>Select a client:</b></td>
    <?php 
        $sql = 'SELECT client_id, client_name FROM ' . CLIENT_TABLE ;
        $client_stack = db_query($sql);
        /*
        $sql_cin = 'SELECT cin, service_type, client_name FROM ' . SERVICE_CIN_TABLE ;
        $cin_stack = db_query($sql_cin);
        $cin = array();
        while ($cin_row = db_fetch_array($cin_stack)) {
            $cin[$cin_row['client_name']] = $cin_row;
              echo   '';
        }
        */
    ?>
    <td>
        <select name="client_id">
            <?php while ( $each_client = db_fetch_array($client_stack) ) { ?>
                <option value="<?php   echo   $each_client['client_id']; ?>" name="<?php   echo   $each_client['client_name']; ?>" ><?php   echo   $each_client['client_name']; ?></option>
            <?php } ?>
        </select>
    </td>
</tr>
<tr height=2px><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <td></td>
        <td>
            <input class="button" type="submit" name="submit_x" value="Submit Ticket">
            <input class="button" type="reset" value="Reset">
            <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="tickets.php"'>    
        </td>
    </tr>


</form>