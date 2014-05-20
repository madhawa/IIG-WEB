<?php
require('client.inc.php');
require(CLIENTINC_DIR . 'header.inc.php');
?>
<br>
<div id="order_updown">
    <h2>Upgrade/Downgrade Form</h2>
    <br>
    <br>
    <br>
    <div id="order_updown_technical_info">
        <form target="" method="POST">
            <input type="checkbox" name="UPGRADE" value="upgrade"> UPGRADE
            <br>
            <br>
            <input type="checkbox" name="DOWNGRADE" value="downgrade"> DOWNGRADE
            <br>
            <br>
            <br>
            <br>


            <table>
                <tr>
                    <td>EXISTING CIRCUIT NAME</td>
                    <td><input type="text" name="existing_ckt_name"></td>
                </tr>
                <tr>
                    <td>PREVIOUS BW</td>
                    <td><input type="text" name="prev_bw"></td>
                </tr>
                <tr>
                    <td>BW AFTER CHANGE</td>
                    <td><input type="text" name="bw_after_change"></td>
                </tr>
                <tr>
                    <td>IN EFFECT FROM</td>
                    <td><input type="text" name="in_effect_from"></td>
                </tr>
                <tr>
                    <td>BILLING DATE FROM</td>
                    <td><input type="text" name="billing_date_from"></td>
                </tr>
            </table>
    </div>


    <div id="order_client_info">
        REQUEST FROM CLIENT:
        <br>
        <br>
        <br>
        <table>
            <tr>
                <td>NAME:</td>
                <td><input type="text" name="order_updown_client_name"></td>
            </tr>
            <tr>
                <td>Designation:</td>
                <td><input type="text" name="order_updown_client_designation"></td>
            </tr>
            <tr>
                <td>Company:</td>
                <td><input type="text" name="order_updown_client_designation"></td>
            </tr>
            <tr>
                <td>Date:</td>
                <td><input type="text" name="order_updown_client_date"></td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <input type="checkbox" name="perm_upgrade"> Permanenent Upgrade
        <br>
        <br>
        <input type="checkbox" name="time_frame"> Time Frame: From in effect date to ........... 
        <br>
        <br>
        <input type="checkbox" name="until_further_notice"> Until Further Notice
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        signature and Date
    </div>
    <br>
    <br>
    <br>

    <div class="order_updown_signature">
        ACCOUNT CLEARANCE
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        ..........................................
        <br>
        Signature/Date
    </div>
    <div class="order_updown_signature">
        MARKETING CLEARANCE
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        ..........................................
        <br>
        Signature/Date
    </div>
    <div class="order_updown_signature">
        APPROVED BY
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        ...........................................
        <br>
        Signature/Date
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <input class="button" type="submit" name="submit" value="Change Order">
    </form>
</div>

<?php
require(CLIENTINC_DIR . 'footer.inc.php');
?>