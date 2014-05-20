<?php
require('client.inc.php');

$location = "";
$query_string = "";
if ($thisuser->isClientAdmin() || $thisuser->onlyMonitor() || $thisuser->onlyView()) {
    $query_string = $_GET['p'];
    switch ($query_string) {
        case "lg":
        $location = "http://lg.1asiacom.net";
        break;
        case "spt":
        $location = "http://speedtest.1asiacom.net";
        break;
    }
}

require(CLIENTINC_DIR . 'header.inc.php');
if ( $query_string != 'graph' ) {
    require(TEMPLATE_DIR.'iframe.tpl.php');
} else {
            $sql = 'SELECT mrtg_links FROM ' . CLIENT_TABLE . ' WHERE ' . 'client_id=' . db_input($thisuser->getId());
            if ($mrtg_stack = db_query($sql)) {
                $mrtg_links_from_db = db_fetch_array($mrtg_stack);
				//print_r($mrtg_links_from_db);
            } else {
                $mrtg_links_from_db['mrtg_links'] = '';
            }
            ?>
            <div class="mrtg_div">
                <h2 align="center"><b>MRTG links</b></h2>
                <br>
                <br>
                <?php
                $links_array = explode(',', $mrtg_links_from_db['mrtg_links']);
                foreach ($links_array as $key=>$value) {
                    if ( $value ) {
                          echo   '<iframe src="' . $value . '"></iframe>';
                          echo   '<br><br>';
                    }
                }
                ?>
            </div>


<?php
}
require(CLIENTINC_DIR . 'footer.inc.php');
?>