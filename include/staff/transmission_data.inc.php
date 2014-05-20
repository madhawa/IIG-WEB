<?php
/*
view all clients transmission data
*/
$trans_sql = 'SELECT * FROM ' . TRANSMISSION_TABLE . ' ORDER BY client_name';

?>
<div id="all_trans_data">
    <table class="dtable" width="30%" align="center">
        <tr>
            <th>
                Client Name
            </th>
        </tr>
        <?php
        if ( ($res = db_query($trans_sql)) && db_num_rows($res) ) {
            while ( $row = db_fetch_array($res) ) {
                ?>
                    <tr>
                        <td><a href="<?php   echo   './transmission.php?id='.$row['client_id']  ?>"><?php   echo   $row['client_name']; ?></a></td>
                    </tr>
                
                <?php
            }
        } else {
            ?>
                <tr>
                    <td>No data in database</td>
                </tr>
            <?php
        }
        ?>
    </table>
</div>