<span class="button" id="odf_port_mapping_spanbutton">ODF port mapping</span>

<div id="notification">
    <div class="close_box"></div>
</div>
<div id="odf_mapping">
</div>
<div id="select_odf">
	
</div>

<br>
<br>
<h3 style="text-align:center">Click over a client name for service provisioning form</h3>
<br>
<br>
<?php
//currently we are just making it simple
$target_page = 'services';
require_once('clientmembers.inc.php');
?>

<script type="text/javascript" src="js/notification.js"></script>
<script type="text/javascript" src="js/services.js"></script>
<script type="text/javascript">
    console.group('first time refresh');
    odf_data.all_odf_from_db_reload( [odf_data.this_client_odf_names_reload, odf_data.realtime_odf_names_reload] );
    console.groupEnd();
</script>
<script type="text/javascript" src="js/port_mapping.js"></script>