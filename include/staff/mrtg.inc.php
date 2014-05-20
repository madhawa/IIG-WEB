<?php
//populate client info versus mrtg data
$sql = 'SELECT client_id, client_name, mrtg_links FROM ' . CLIENT_TABLE;
$mrtg_stack = db_query($sql);

//populate client name and id list
$sql = 'SELECT client_id, client_name FROM ' . CLIENT_TABLE ;
$client_stack = db_query($sql);

//form subitted, now save
if ($_POST) {
    if ( $_POST['mrtg_link_input'] ) {
        $mrtg_links = trim($_POST['mrtg_link_input'], ',');
        
        if ( $_POST['do'] == 'edit_mrtg' ) {
            $sql = 'UPDATE ' . CLIENT_TABLE . ' SET mrtg_links=' . db_input($mrtg_links) . ' WHERE client_id=' . db_input( $_POST['mrtg_client_id'] );
        } else {
            $sql = 'UPDATE ' . CLIENT_TABLE . ' SET mrtg_links=concat(mrtg_links,' . db_input(','.$mrtg_links) . ') WHERE client_id=' . db_input( $_POST['mrtg_client_id'] );
        }
        if (db_query($sql)) {
            $msg = 'updated successfully';
        } else {
            $errors['err'] = 'failed to save';
        }
    }
}
?>
<br>
<div>
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php   echo   $errors['err'] ?></p>
    <?php } ?>
    
    <?php if ($msg) { ?>
        <p align="center" id="infomessage"><?php   echo   $msg ?></p>
    <?php } ?>
    
    <?php if ($warn) { ?>
        <p id="warnmessage"><?php   echo   $warn ?></p>
    <?php } ?>
</div>
<br>
<br>
<div class="mrtg_div">
	<h2>Save MRTG links</h2>
    <a>insert multiple links seperated by comma</a>
    <br>
    <br>
    <button class="button" type="button" name="insert_mrtg_link">insert mrtg link</button>

    <form name="save_mrtg" target="" method="post">
        <textarea rows="4" cols="70" class="mrtg" name="mrtg_link_input" required></textarea>
		<br>
		<br>
		<span class="mrtg">Select a client:</span>
		<br>
		<select class="mrtg" name="mrtg_client_id" required>
            <option value="">Select</option>
			<?php while ( $each_client = db_fetch_array($client_stack) ) { ?>
				<option value="<?php   echo   $each_client['client_id']; ?>"><?php   echo   $each_client['client_name']; ?></option>
			<?php } ?>
		</select>
		<br>
        <span class="mrtg">Comment</span>
        <br>
        <textarea class="mrtg" name="mrtg_comment">
        </textarea>
		<br>
		<br>
        <input class="mrtg" type="submit" name="save_mrtg_links" value="save">
    </form>
    <br>
</div>
<div class="mrtg_div">
    <h2>MRTG links in database</h2>
	<table class="dtable">
		<tr>
			<th>Client Name</th>
            <th>MRTG links</th>
			<th>edit</th>
		</tr>
		<?php while ( $mrtg_data = db_fetch_array($mrtg_stack) ) { ?>
		<tr>
		<form name="save_mrtg" target="" method="post">
			<td><?php   echo   $mrtg_data['client_name'] ?></td>
			<td><?php $links_array = explode(',', $mrtg_data['mrtg_links']);
				foreach ( $links_array as $key=>$value ) {
					  echo   '<a href="' . $value . '" target="_blank">' . $value . '</a><br>';
				}
				?>
			</td>
			<td>
                <?php if ( $mrtg_data['mrtg_links'] ) { ?>
                    <input type="hidden" name="mrtg_client_id" value="<?php   echo   $mrtg_data['client_id']; ?>">
                    <input type="hidden" name="do" value="edit_mrtg">
                    <textarea rows="4" cols="70" name="mrtg_link_input"><?php   echo   trim($mrtg_data['mrtg_links'], ','); ?></textarea>
                    <button class="button" type="button" name="edit_mrtg">edit</button>
                    <input class="button" type="submit" name="save_edited_mrtg_links" value="save">
                <?php } ?>
			</td>
        </form>
		</tr>
		<?php } ?>
	</table>
</div>

<script type="text/javascript">
    $('div.mrtg_div').css('border', '1px solid');
    $('div.mrtg_div').css('padding', '10px');
	$('div.mrtg_div table td').css('padding', '10px');
	
    $('.mrtg').hide();
    $('[name="insert_mrtg_link"]').click(function(event) {
        $('.mrtg').show();
		$(event.target).hide();
    });
    
    $('[name="save_edited_mrtg_links"]').hide();
    $('[name="mrtg_link_input"]').hide();
    $('button[name="edit_mrtg"]').click(function(event) {
        $(event.target).hide();
        $(event.target).prev('[name="mrtg_link_input"]').show();
        $(event.target).next('[name="save_edited_mrtg_links"]').show();
        //$('[name="mrtg_links_edited"]').show();
    });
    
</script>