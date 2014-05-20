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

<div id="search_client">
    <select style="margin-left: 300px" name="select_by">
        <option value="">Search Client by</option>
        <option value="name">Client Name</option>
        <option value="client_of">Client Of</option>
        <option value="client_type">Client Type</option>
    </select>
    <input type="text" name="name" value="" placeholder="client name">
    <select name="client_of" required>
        <option value="">Please Select Client Of</option>
        <option value="IIG">IIG</option>
        <option value="ITC">ITC</option>
    </select>
    <select name="client_type" required>
        <option value="">Please Select Client Type</option>
        <option value="Enterprise Customer">Enterprise Customer</option>
        <option value="ISP/BWAs">ISP/BWAs</option>
        <option value="IIG">IIG</option>
        <option value="IGW">IGW</option>
        <option value="ANS">ANS</option>
        <option value="Call Center/BPO">Call Center/BPO</option>
        <option value="NGO/Banks">NGO/Banks</option>
        <option value="Government">Government</option>
        <option value="Others">Others</option>
    </select>
</div>

<h2 id="title" align="center"><?php    echo   $title;  ?></h2>

<div id="all_client">
	<form action="" method="post">
		<input type=hidden name='do' value='mass_delete'>
		<table  class="dtable" align="center">
			<tr id="header">
                <th></th>
				<th>Name</th>
                <th>Client Type</th>
				<th>Client Of</th>
				<th>Email</th>
				<th>Asn</th>
			</tr>
			<?php  if ( $clients && count($clients)) {
				//  echo   db_num_rows($users);
				foreach( $clients as $client ) {
					 ?>
					<tr>
                        <td><input type="checkbox" name="delete_client[]" value="<?php    echo   $client['client_id']  ?>"></td>
						<td><a href="client.php?do=view&amp;id=<?php    echo   $client['client_id']  ?>"><?php    echo   Format::htmlchars($client['client_name'])  ?></a></td>
                        <td class="client_type"><?php    echo   $client['client_type'];  ?></td>
						<td class="client_of"><?php    echo   $client['client_of'];  ?></td>
                        <td><?php    echo   $client['email']  ?></td>
                        <td><?php    echo   $client['client_asn']  ?></td>
					</tr>
                <?php  }	 ?>
                    <tr>
                        <td colspan="6"><button class="save" name="delete_clients" type="submit">Delete</button></td>
                    </tr>
            <?php  } else {  ?>
				<tr class="<?php    echo   $class  ?>"><td colspan=8><b>No clients</b></td></tr>
			<?php  }  ?>
		</table>
	<br>
	<br>
	</form>
</div>

<script type="text/javascript">

    var prev_title = $('h2#title').text();

    $('[name="name"], [name="client_of"], [name="client_type"]').hide();
    $('div#search_client [name="select_by"]').change(function(event) {
        $('div#all_client tr').each(function(index, element){
            $(element).show();
        });
        $('h2#title').text(prev_title);
        var search= $(event.target).val();
        if ( search && ( search == 'name' ) ) {
            $('[name="name"]').show();
            $('[name="client_of"], [name="client_type"]').hide();
        } else if ( search && ( search == 'client_of' ) ) {
            $('[name="client_of"]').show();
            $('[name="name"], [name="client_type"]').hide();
        } else if ( search && ( search == 'client_type' ) ) {
            $('[name="client_type"]').show();
            $('[name="name"], [name="client_of"]').hide();
        } else {
            $('[name="name"], [name="client_of"], [name="client_type"]').hide();
        }
    });

    //now searching by client name
    $('div#search_client [name="name"]').keyup(function(event) {
        var type_name = $(event.target).val();
        var found = 0;
        $('div#all_client tr:not(#header)').each(function(index, element){
            var client_name = $(element).find('td a').text();
            if ( client_name.toLowerCase().search(type_name.toLowerCase()) == -1 ) {
                $(element).hide();
            } else {
                found++;
                $(element).show();
            }
        });
        if ( !found ) {
            $('h2#title').text('no clients found');
        } else {
            $('h2#title').text(prev_title);
        }
        if ( !type_name ) {
            $('h2#title').text(prev_title);
        }
    });
    //searching client by client of
    $('[name="client_of"]').change(function(event) {
        var of = $(event.target).val();
        var found = 0;
        $('div#all_client tr:not(#header)').each(function(index, element){
            var client_of = $(element).find('td.client_of').text();
            if ( client_of.toLowerCase().search(of.toLowerCase()) == -1 ) {
                $(element).hide();
            } else {
                $(element).show();
                found++;
            }
        });
        if ( !found ) {
            $('h2#title').text('no clients found');
        } else {
            $('h2#title').text(prev_title);
        }
        if ( !of ) {
            $('h2#title').text(prev_title);
        }
    });
    //searching by client type
    $('[name="client_type"]').change(function(event) {
        var type = $(event.target).val();
        var found = 0;
        $('div#all_client tr:not(#header)').each(function(index, element){
            var client_type = $(element).find('td.client_type').text();
            if ( client_type.toLowerCase().search(type.toLowerCase()) == -1 ) {
                $(element).hide();
            } else {
                $(element).show();
                found++;
            }
        });
        if ( !found ) {
            $('h2#title').text('no clients found');
        } else {
            $('h2#title').text(prev_title);
        }
        if ( !type ) {
            $('h2#title').text(prev_title);
        }
    });


    $('th, td').css('padding', '20px');
    $('th, td').css('font-size', '1.5em');

    $('[name="delete_clients"]').hide();
    $('[name="delete_client[]"]').click(function() {
        $('[name="delete_client[]"]').each(function(index, element) {
            if ( $(element).is(':checked') ) {
                $('[name="delete_clients"]').show();
            }
        });
    });

    $('[name="delete_clients"]').click(function(event) {
        var r=confirm("Do you really want to delete clients ?");
        if (r!=true) {
            return false;
        }
    });
</script>