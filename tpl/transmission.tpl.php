<form action="" method="POST">


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

    <div>
    
        <div class="transmission">
            <?php if ( $rep['select_client'] ) { ?>
                <input type="hidden" name="do" value="update">
            <?php } else { ?>
                <input type="hidden" name="do" value="create">
            <?php } ?>
            <input type="hidden" name="scp_staff" value="<?php   echo   $thisuser->getId(); ?>">
            
            <h3>Select a client</h3>
            <select name="select_client">
                <option value="">Select</option>
                <?php foreach($client_data as $i=>$data) { ?>
                    <option value="<?php   echo   $data['client_id']; ?>" <?php if ( $rep && ($rep['select_client']==$data['client_id']) ) {   echo   'selected'; } ?> ><?php   echo   $data['client_name']; ?></option>
                <?php } ?>
            </select>
            <input type="hidden" name="client_name" value="<?php if ( $rep ) {   echo   $rep['client_name']; } ?>">
            <input type="hidden" name="client_id" value="<?php if ( $rep ) {   echo   $rep['select_client']; } ?>">
            
            <script type="text/javascript">
                $('[name="select_client"]').change(function(event) {
                    $('[name="client_name"]').val($(this).children(':selected').text());
                });
            </script>
            
        </div>
        
        <div class="transmission">
            <h3>Service Type</h3>
            <input type="text" name="service_type" value="<?php   echo   $rep['service_type']; ?>">
            <br>
            <br>
            visible to client : <input type="checkbox" name="service_type_visibility" value="1" <?php if ( $rep['service_type_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        
        <div class="transmission">
            <h3>Service Level</h3>
            <select name="service_level">
                <option value="">Select</option>
                <option value="VC12" <?php if ( $rep && ( $rep['service_level']=='VC12' ) ) {
                   echo   'selected'; } ?> >VC12</option>
                <option value="VC3" <?php if ( $rep && ( $rep['service_level']=='VC3' ) ) {
                   echo   'selected'; } ?> >VC3</option>
                <option value="3xAU3" <?php if ( $rep && ( $rep['service_level']=='3xAU3' ) ) {
                   echo   'selected'; } ?> >3xAU3</option>
                <option value="VC4" <?php if ( $rep && ( $rep['service_level']=='VC4' ) ) {
                   echo   'selected'; } ?> >VC4</option>
                <option value="VC4-4C" <?php if ( $rep && ( $rep['service_level']=='VC4-4C' ) ) {
                   echo   'selected'; } ?> >VC4-4C</option>
                <option value="VC4-8C" <?php if ( $rep && ( $rep['service_level']=='VC4-8C' ) ) {
                   echo   'selected'; } ?> >VC4-8C</option>
                <option value="VC4-16C" <?php if ( $rep && ( $rep['service_level']=='VC4-16C' ) ) {
                   echo   'selected'; } ?> >VC4-16C</option>
                <option value="VC4-64C" <?php if ( $rep && ( $rep['service_level']=='VC4-64C' ) ) {
                   echo   'selected'; } ?> >VC4-64C</option>
                 <option value="VC4-64C" <?php if ( $rep && ( $rep['service_level']=='N/A' ) ) {
                   echo   'selected'; } ?> >N/A</option>
            </select>
            <br>
            <br>
            visible to client : <input type="checkbox" name="service_level_visibility" value="1" <?php if ( $rep['service_level_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        
        <div class="transmission">
                <h3>Protection status</h3>
                Dhaka Banapole : <input type="checkbox" name="protection_status_db" value="Dhaka-Banapole" <?php if ( $rep['protection_status_db'] ) {   echo   'checked'; } ?> >
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                <input type="radio" name="protection_status_db_confirm" value="yes" <?php if ( $rep['protection_status_db_confirm']=='yes' ) {   echo   'checked="checked"'; } ?> >Yes
                <input type="radio" name="protection_status_db_confirm" value="no" <?php if ( $rep['protection_status_db_confirm']=='no' ) {   echo   'checked="checked"'; } ?> >No
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="protection_status_db_type" value="auto" <?php if ( $rep['protection_status_db_type']=='auto' ) {   echo   'checked="checked"'; } ?> >Automatic
                <input type="radio" name="protection_status_db_type" value="manual" <?php if ( $rep['protection_status_db_type']=='manual' ) {   echo   'checked="checked"'; } ?> >manual
                
                <br>
                
                1Asia SWR-Client : <input type="checkbox" name="protection_status_1asia" value="1Asia SWR-Client" <?php if ( $rep['protection_status_1asia'] ) {   echo   'checked'; } ?> >
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                <input type="radio" name="protection_status_1asia_confirm" value="yes" <?php if ( $rep['protection_status_1asia_confirm']=='yes' ) {   echo   'checked="checked"'; } ?> >Yes
                <input type="radio" name="protection_status_1asia_confirm" value="no" <?php if ( $rep['protection_status_1asia_confirm']=='no' ) {   echo   'checked="checked"'; } ?> >No
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="protection_status_1asia_type" value="auto" <?php if ( $rep['protection_status_1asia_type']=='auto' ) {   echo   'checked="checked"'; } ?> >Automatic
                <input type="radio" name="protection_status_1asia_type" value="manual" <?php if ( $rep['protection_status_1asia_type']=='manual' ) {   echo   'checked="checked"'; } ?> >manual
                
                <br>
                <br>
                
                <button type="button" name="add_new_pro_stat">add new</button>
                <span id="new_protection_status_data"></span>
                
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
                                    <input type="text" name="added_protection_status_name[]" value="<?php   echo   $name ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="added_protection_status_confirm[]" value="yes" <?php if ( $confirm=='yes' )   echo   'checked'; ?>>Yes
                                    <input type="radio" name="added_protection_status_confirm[]" value="no" <?php if ( $confirm=='no' )   echo   'checked'; ?>>No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="added_protection_status_type[]" value="auto" <?php if ( $type=='auto' )   echo   'checked'; ?>>Automatic
                                    <input type="radio" name="added_protection_status_type[]" value="manual" <?php if ( $type=='manual' )   echo   'checked'; ?>>manual
                                    <br><br>
                                            
                                <?php
                            }
                        }
                    
                    ?>
                </div>
                
                <br>
                
                <script type="text/javascript">
                    var input_field = '<br><br><input type="text" name="added_protection_status_name[]">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                        <input type="radio" name="added_protection_status_confirm[]" value="yes">Yes\
                                        <input type="radio" name="added_protection_status_confirm[]" value="no" >No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                        <input type="radio" name="added_protection_status_type[]" value="auto" >Automatic\
                                        <input type="radio" name="added_protection_status_type[]" value="manual" >manual<br><br>\
                                        ';
                    $('[name="add_new_pro_stat"]').click(function(event) {
                        $('div#added_protection_status').append(input_field);
                    });
                    
                    //write code to populate fields
                </script>
            
            <br>
            visible to client : <input type="checkbox" name="transmission_protection_visibility" value="1" <?php if ( $rep['transmission_protection_visibility']=='1' )   echo   'checked'; ?>>
            
        </div>
        
        <div class="transmission">
            <h3>Link Capacity</h3>
            <input type="text" name="link_cap" value="<?php if ( $rep['link_cap'] ) {   echo   $rep['link_cap']; } ?>">
            <br>
            <br>
            visible to client : <input type="checkbox" name="link_cap_visibility" value="1" <?php if ( $rep['link_cap_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        
        <div class="transmission">
            <h3>SPF Information</h3>
            
            Transmission mode : 
            <input type="radio" name="spf_info_mode" value="single" <?php if ( $rep && ($rep['spf_info_mode']=='single') ) {   echo   'checked="checked"';
                } ?> >Single
            <input type="radio" name="spf_info_mode" value="dual" <?php if ( $rep && ($rep['spf_info_mode']=='dual') ) {   echo   'checked="checked"';
                } ?> >Dual
            <br>
            Transmission wavelength: <input type="text" name="spf_info_wavelength" value="<?php   echo   $rep['spf_info_wavelength'] ?>">
            <br>
            Transmission distance: <input type="text" name="spf_info_distance" value="<?php   echo   $rep['spf_info_distance'] ?>">
            <br>
            <br>
            visible to client : <input type="checkbox" name="spf_info_visibility" value="1" <?php if ( $rep['spf_info_visibility']=='1' )   echo   'checked'; ?>>
            
        </div>
        
        <div class="transmission">
            <table>
                <tr>
                    <td colspan="2"><h3>NTTN Information</h3></td>
                </tr>
                <tr>
                    <td>Name of NTTN</td>
                    <td><input type="text" name="name_of_nttn" value="<?php if ( $rep && ( $rep['name_of_nttn'] ) ) {   echo   $rep['name_of_nttn']; } ?>"></td>
                </tr>
                <tr>
                    <td>Path distance</td>
                    <td><input type="text" name="path_distance" value="<?php if ( $rep && ( $rep['path_distance'] ) ) {   echo   $rep['path_distance']; } ?>"></td>
                </tr>
                <tr>
                    <td>
                        Path Loss
                    </td>
                    <td>
                        <input type="text" name="path_loss" value="<?php if ( $rep && ( $rep['path_loss'] ) ) {   echo   $rep['path_loss']; } ?>">
                    </td>
                </tr>
            </table>
            
            <div id="new_nttn" class="added">
                <button type="button" name="add_new_nttn">add new</button>
                <br>
                <?php
                    $num_added_nttn = count($rep['added_nttn_name']);//new nttn fields added
                    for ( $i=0; $i<$num_added_nttn; $i++ ) {//loop over them
                        $name = $rep['added_nttn_name'][$i];
                        $pd = $rep['added_nttn_path_distance'][$i];
                        $pv = $rep['added_nttn_path_value'][$i];
                        ?>
                            <br><br>
                            name : <input type="text" name="added_nttn_name[]" value="<?php   echo   $name; ?>">
                            <br>
                            path distance : <input type="text" name="added_nttn_path_distance[]" value="<?php   echo   $pd; ?>">
                            <br>
                            path value : <input type="text" name="added_nttn_path_value[]" value="<?php   echo   $pv; ?>">
                            <br><br>
                        <?php
                    }
                    
                ?>
            </div>
            
            <script type="text/javascript">
                var nttn = '<br><br>name : <input type="text" name="added_nttn_name[]" value="">\
                            <br>\
                            path distance : <input type="text" name="added_nttn_path_distance[]" value="">\
                            <br>\
                            path value : <input type="text" name="added_nttn_path_value[]" value=""><br><br>';
                $('[name="add_new_nttn"]').click(function(event) {
                    $(event.target).parent('div').append(nttn);
                });
            </script>
            
            <table>
                <tr>
                    <td colspan="2"><h3>POC in Information</h3></td>
                </tr>
                <tr>
                    <td>Start</td>
                    <td><input type="text" name="poc_start" value="<?php if ( $rep && ( $rep['poc_start'] ) ) {   echo   $rep['poc_start']; } ?>"></td>
                </tr>
                <tr>
                    <td>End</td>
                    <td><input type="text" name="poc_end" value="<?php if ( $rep && ( $rep['poc_end'] ) ) {   echo   $rep['poc_end']; } ?>"></td>
                </tr>
            </table>
            <br>
            <br>
            visible to client : <input type="checkbox" name="nttn_info_visibility" value="1" <?php if ( $rep['nttn_info_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        

        <div class="transmission">
            <h3>Device and ODF port information</h3>
            <textarea name="odf_port_info">
                <?php if ( $rep && ( $rep['odf_port_info'] ) ) {   echo   $rep['odf_port_info']; } ?>
            </textarea>
            <br>
            <br>
            visible to client : <input type="checkbox" name="odf_info_visibility" value="1" <?php if ( $rep['odf_info_visibility']=='1' )   echo   'checked'; ?>>
        </div>

        
        <div class="transmission">
            <h3>Transmit and Recieve Power</h3>
            Client Transmit power<input type="text" name="client_end_tx" value="<?php if ( $rep && ( $rep['client_end_tx'] ) ) {   echo   $rep['client_end_tx']; } ?>">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Client Receive power<input type="text" name="client_end_rx" value="<?php if ( $rep && ( $rep['client_end_rx'] ) ) {   echo   $rep['client_end_rx']; } ?>">
            <br>
            1Asia Transmit power<input type="text" name="1asia_end_tx" value="<?php if ( $rep && ( $rep['1asia_end_tx'] ) ) {   echo   $rep['1asia_end_tx']; } ?>">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            1Asia Receive power<input type="text" name="1asia_end_rx" value="<?php if ( $rep && ( $rep['1asia_end_rx'] ) ) {   echo   $rep['1asia_end_rx']; } ?>">
            <br>
            <br>
            visible to client : <input type="checkbox" name="tx_rx_info_visibility" value="1" <?php if ( $rep['tx_rx_info_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        
        <div class="transmission">
            <h3>Current status of link </h3>
            <input type="radio" name="link_status" value="up" <?php if ( $rep && ($rep['link_status']=='up') ) {   echo   'checked="checked"';
                    } ?> >Up
            <input type="radio" name="link_status" value="down" <?php if ( $rep && ($rep['link_status']=='down') ) {   echo   'checked="checked"';
                    } ?> >Down
                    
            <br>
            <br>
            visible to client : <input type="checkbox" name="link_status_info_visibility" value="1" <?php if ( $rep['link_status_info_visibility']=='1' )   echo   'checked'; ?>>
        </div>
        

        <div class="transmission">
            <h3>Remarks</h3>
            <textarea name="remarks">
                <?php   echo   $rep['remarks']; ?>
            </textarea>
            <br>
            <br>
            visible to client : <input type="checkbox" name="remarks_visibility" value="1" <?php if ( $rep['remarks_visibility']=='1' )   echo   'checked'; ?>>
        </div>

    

        <div class="transmission">
            <input type="submit" name="submit" value="save">
        </div>

        
    </div>
    
</form>

<script type="text/javascript">
    $('div.transmission_protection').not('div#protection_status_type').css('display','inline-block');
    
    $('[name="protection_status_confirm"]').click(function(event) {
        if ( $('[name="protection_status"]').val() ) {
            if ( $(event.target).val() == "yes" ) {
                $('div.transmission_protection').css('display','inline-block');
                $('div#protection_status_type').show();
            } else {
                $('div#protection_status_type').hide();
            }
        }
    });
    
    if ( $('[name="protection_status_confirm"]').val()=='yes' ) {
        $('div.transmission_protection').css('display','inline-block');
        $('div#protection_status_type').show();
    }
    
    $('[name="spf_info"]').change(function(event) {
        if ( $(event.target).val() == "mode" ) {
            $('div#transmission_spf_mode').show();
            $('div.transmission_spf').css('display', 'inline-block');
        } else {
            $('div#transmission_spf_mode').hide();
        }
    });
    
    if ( $('[name="spf_info"]').val() ) {
        $('div#transmission_spf_mode').show();
        $('div.transmission_spf').css('display', 'inline-block');
    }
</script>