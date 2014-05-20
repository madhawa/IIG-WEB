<?php if ( $rep['id'] ) { ?>   
    <?php if( $rep['service_type_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Service Type</h3>
            <span><?php   echo   $rep['service_type']; ?></span>
        </div>
    <?php } ?>
    
    <?php if( $rep['service_level_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Service Level</h3>
            <span><?php   echo   $rep['service_level']; ?></span>
        </div>
    <?php } ?>
    
    <?php if ( $rep['transmission_protection_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Protection status</h3>        
            <?php
            if ( $rep['protection_status_db'] ) {
                  echo   '<span>'.$rep['protection_status_db'].'</span>'.$spaces;
                if ( $rep['protection_status_db_confirm']=='yes' ) {
                      echo   '<pan>'.'YES'.'</span>'.$spaces;
                } else {
                      echo   '<pan>'.'NO'.'</span>'.$spaces;
                }
                if ( $rep['protection_status_db_type']=='auto' ) {
                      echo   '<span>'.'AUTO'.'</span>'.$spaces;
                } else {
                      echo   '<span>'.'MANUAL'.'</span>'.$spaces;
                }
            }
            
            if ( $rep['protection_status_1asia'] ) {
                  echo   '<br>';
                
                  echo   '<span>'.$rep['protection_status_1asia'].'</span>'.$spaces;
                if ( $rep['protection_status_1asia_confirm']=='yes' ) {
                      echo   '<pan>'.'YES'.'</span>'.$spaces;
                } else {
                      echo   '<pan>'.'NO'.'</span>'.$spaces;
                }
                if ( $rep['protection_status_1asia_type']=='auto' ) {
                      echo   '<span>'.'AUTO'.'</span>'.$spaces;
                } else {
                      echo   '<span>'.'MANUAL'.'</span>'.$spaces;
                }
            }
            ?>
            
            <br>
            <br>
            
            <div id="added_protection_status" class="added">
                <?php
                    $num_added_prot_fields = count($rep['added_protection_status_name']);
                    //print_r($rep);
                    
                    if ( $num_added_prot_fields ) {//if new protection fields added
                        for ( $i=0; $i<$num_added_prot_fields; $i++ ) { // loop over them
                            $name = $rep['added_protection_status_name'][$i];
                            $confirm = $rep['added_protection_status_confirm'][$i];
                            $type = $rep['added_protection_status_type'][$i];
                            ?>
                                <br><br>
                                name: <span><?php   echo   $name ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php   echo   $confirm; ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php   echo   $type; ?>
                                <br><br>
                            <?php
                        }
                    }
                
                ?>
            </div>
            
        </div>
    <?php } ?>
    
    <?php if ( $rep['link_cap_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Link Capacity</h3>
            <span><?php   echo   $rep['link_cap']; ?></span>
        </div>
    <?php } ?>
    
    <?php if ( $rep['spf_info_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>SPF Information</h3>
            
            Transmission mode : 
            <?php   echo   '<span>'.$rep['spf_info_mode'].'</span>'; ?>
            <br>
            Transmission wavelength:
            <?php   echo   '<span>'.$rep['spf_info_wavelength'].'</span>'; ?>
            <br>
            Transmission distance:
            <?php   echo   '<span>'.$rep['spf_info_distance'].'</span>'; ?>        
        </div>
    <?php } ?>
    
    <?php if ( $rep['nttn_info_visibility']=='1' ) { ?>
        <div class="transmission">
            <table>
                <tr>
                    <td colspan="2"><h3>NTTN Information</h3></td>
                </tr>
                <tr>
                    <td>Name of NTTN</td>
                    <td><span><?php   echo   $rep['name_of_nttn']; ?></span></td>
                </tr>
                <tr>
                    <td>Path distance</td>
                    <td><span><?php   echo   $rep['path_distance']; ?></span></td>
                </tr>
                <tr>
                    <td>Path Loss</td>
                    <td><span><?php   echo   $rep['path_loss']; ?></span></td>
                </tr>
            </table>
            
            <div id="new_nttn" class="added">
                <?php
                    $num_added_nttn = count($rep['added_nttn_name']);//new nttn fields added
                    for ( $i=0; $i<$num_added_nttn; $i++ ) {//loop over them
                        $name = $rep['added_nttn_name'][$i];
                        $pd = $rep['added_nttn_path_distance'][$i];
                        $pv = $rep['added_nttn_path_value'][$i];
                        ?>
                            <br><br>
                            name of NTTN : <span><?php   echo   $name; ?></span>
                            <br>
                            path distance : <span><?php   echo   $pd; ?></span>
                            <br>
                            path value : <span><?php   echo   $pv; ?></span>
                            <br><br>
                        <?php
                    }
                    
                ?>
            </div>
            
            <table>
                <tr>
                    <td colspan="2"><h3>POC in Information</h3></td>
                </tr>
                <tr>
                    <td>Start</td>
                    <td><span><?php   echo   $rep['poc_start']; ?></span></td>
                </tr>
                <tr>
                    <td>End</td>
                    <td><span><?php   echo   $rep['poc_end']; ?></span></td>
                </tr>
            </table>
        </div>
    <?php } ?>
    
    <?php if ( $rep['odf_info_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Device and ODF port information</h3>
            <p><?php   echo   $rep['odf_port_info']; ?></p>
        </div>
    <?php } ?>

    <?php if ( $rep['tx_rx_info_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Transmit and Recieve Power</h3>
            Client Transmit power<span><?php   echo   $rep['client_end_tx']; ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Client Receive power<span><?php   echo   $rep['client_end_rx']; ?></span>
            <br>
            
            1Asia Transmit power<span><?php   echo   $rep['1asia_end_tx']; ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            1Asia Receive power<span><?php   echo   $rep['1asia_end_rx']; ?></span>
        </div>
    <?php } ?>
    
    <?php if ( $rep['link_status_info_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Current status of link </h3>
            <span><?php   echo   $rep['link_status']; ?></span>
        </div>
    <?php } ?>
    
    <?php if ( $rep['remarks_visibility']=='1' ) { ?>
        <div class="transmission">
            <h3>Remarks</h3>
            <p><?php   echo   $rep['remarks']; ?></p>
        </div>
    <?php } ?>
    
<?php } else { ?>
<h1 align='center'>no data for you</h1>
<?php } ?>
    

<script type="text/javascript">
    $('div.transmission_protection').not('div#protection_status_type').css('display','inline-block');
    $('div.transmission_protection').css('display','inline-block');
    $('div#protection_status_type').show();
    $('div#transmission_spf_mode').show();
    $('div.transmission_spf').css('display', 'inline-block');
</script>