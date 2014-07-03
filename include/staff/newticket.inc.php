<?php
if (!defined('OSTSCPINC') || !is_object($thisuser) || !$thisuser->isStaff())
    die('Access Denied');
$info = ($_POST && $errors) ? Format::input($_POST) : array(); //on error...use the post data
?>

<div width="100%">
    <?php if ($errors['err']) { ?>
        <p align="center" id="errormessage"><?php echo $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" class="infomessage"><?php echo $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p class="warnmessage"><?php echo $warn ?></p>
<?php } ?>
</div>
<div id="new_ticket">
    <h2 align="center" class="msg">Create a new ticket on behalf of client</h2>
    <table align="center" border="0" cellspacing=1 cellpadding=2>
        <form action="tickets.php" method="post" enctype="multipart/form-data">
            <input type='hidden' name='a' value='open'>
            <input type="hidden" name="name" value="">
            <input type="hidden" name="client_id" value="">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div><h3 class="msg">Client Info</h3></div>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Search for client:</strong>
                    </td>
                    <td>
                        <input class="normal" type="text" name="type_client" autocomplete="off" required>
                        <div id="search_sug" style="width: 300px; border: 1px solid; z-index: 100">

                        </div>
                        <!--
                        <select name="client_id" required>
                            <option value="">select client</option>
                        <?php foreach ($clients as $each_client) { ?>
                                    <option value="<?php echo $each_client['client_id']; ?>" id="<?php echo $each_client['email']; ?>" <?php if ($info['client_id'] == $each_client['client_id']) echo 'selected' ?> class="<?php echo $each_client['client_of']; ?>" required ><?php echo $each_client['client_name']; ?></option>
<?php } ?>
                        </select>
                        -->
                    </td>
                </tr>
            </tbody>
            <script type="text/javascript">
                $('div#search_sug').hide();
                var clients = JSON.parse(<?php echo "'" . $clients_json . "'"; ?>);
                $('[name="type_client"]').keyup(function(event) {
                    $('div#search_sug').empty();
                    $('div#search_sug').show();
                    var name = $(event.target).val().toLowerCase();
                    if (name) {
                        $.each(clients, function(index, element) {
                            var cl_name = element.client_name;
                            var cl_id = element.client_id;
                            if (cl_name.toLowerCase().indexOf(name) != -1) {
                                $('div#search_sug').append('<p style="padding: 10px; cursor: pointer; border: 1px grey;" id="' + cl_id + '">' + cl_name + '</p>');
                            }
                        });
                    } else {
                        $('div#search_sug').hide();
                    }
                });

                $('div#search_sug').on('click', 'p', function(event) {
                    var client_id = $(event.target).attr('id');
                    var client_name = $(event.target).text();
                    if (client_id && client_name) {
                        $('[name="client_id"]').val(client_id);
                        $('[name="type_client"]').val(client_name);
                        $('div#search_sug').hide();
                        //filtering cin
                        if (client_id) {
                            $('[name="cin"] option').each(function(index, element) {
                                if ($(element).attr('id') && ($(element).attr('id') != client_name)) {
                                    $(element).hide();
                                } else {
                                    $(element).show();
                                }
                            });
                        }
                    }
                });
            </script>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div>
                            <h3 class="msg">Ticket raiser info</h3>
                        </div>
                        <hr>
                        <input class="normal" type="checkbox" name="raiser_info_enable" value=1 <?php if ($info['raiser_info_enable']) echo 'selected'; ?>>enable/disable
                    </td>
                </tr>
                <tr class="ticket_raiser_info">
                    <td>
                        <strong>Name</strong>
                    </td>
                    <td>
                        <input class="normal" type="text" name="raiser_name" value="<?php echo $info['raiser_name'] ?>">
                    </td>
                </tr>
                <tr class="ticket_raiser_info">
                    <td>
                        <strong>Contact Number</strong>
                    </td>
                    <td>
                        <input class="normal" type="text" name="raiser_phone" value="<?php echo $info['raiser_phone'] ?>">
                    </td>
                </tr>
                <tr class="ticket_raiser_info">
                    <td><strong>Email address</strong></td>
                    <td>
                        <input class="normal" type="text" name="raiser_email" value="<?php echo $info['raiser_email'] ?>">
                    </td>
                </tr>
                <tr class="ticket_raiser_info">
                    <td><strong>Ticket raised from</strong></td>
                    <td>
                        <select name="raised_from">
                            <option value=""></option>
                            <option value="Email">Email</option>
                            <option value="Phone">Phone</option>
                        </select>
                    </td>
                </tr>
                <script type="text/javascript">
                    $('[name="raiser_info_enable"]').click(function(event) {
                        if ( $(event.target).prop('checked') ) {
                            $('.ticket_raiser_info').show();
                        } else {
                            $('.ticket_raiser_info').hide();
                            clear_raiser_fields();
                        }
                    });
                    if ( $('[name="raiser_info_enable"]').prop('checked') ) {
                        $('[name="raiser_info_enable"]').trigger('click');
                    } else {
                        $('.ticket_raiser_info').hide();
                        clear_raiser_fields();
                    }
                    
                    function clear_raiser_fields() {
                        $('.ticket_raiser_info input, .ticket_raiser_info select').val('');
                    }
                </script>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div><h3 class="msg">Ticket Info</h3></div>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Subject</strong>
                    </td>
                    <td>
                        <input type="text" name="subject" size="35" value="<?php echo $info['subject'] ?>" required>
                    </td>
                </tr>
                <tr>
                    <td><strong>Root cause:</strong></td>
                    <td>
                        <input type="text" name="root_cause" value="<?php echo $info['root_cause']; ?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Add to cc<br>(comma seperated)</strong>
                    </td>
                    <td>
                        <!-- <input type="text" name="alt_email" value="<?php echo $info['alt_email'] ?>"> -->
                        <br>
                        <textarea name="alt_email">
                            <?php echo $info['alt_email'] ?>
                        </textarea>
                    </td>
                </tr>
                <tr>
                    <td><strong>Select cin:</strong></td>
                    <td>
                        <select name="cin">
                            <option value="">no CIN</option>
                            <?php
                            $sql_cin = 'SELECT cin, service_type, client_name FROM ' . SERVICE_CIN_TABLE . ' ORDER BY client_name';
                            $type_data = db_query($sql_cin);
                            while ($cin_row = db_fetch_array($type_data)) {
                                if ($cin_row['client_name'] && $cin_row['service_type']) {
                                    ?>
                                    <option id="<?php echo $cin_row['client_name']; ?>" value="<?php echo $cin_row['cin']; ?>"><?php echo 'CIN: ' . $cin_row['service_type'] . ' - ' . $cin_row['cin'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Issue summary</strong></td>
                    <td>       
                        <i>Visible to client/customer.</i><font class="error"><b>*&nbsp;<?php echo $errors['issue'] ?></b></font><br/>
                        <textarea name="message" cols="55" rows="8" wrap="soft" required><?php echo $info['issue'] ?></textarea></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div><h3 class="msg">Site Info</h3></div>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Site visited ?</strong>
                    </td>
                    <td>
                        <select name="site_visited" class="normal" style="width: 150px" required>
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </td>
                </tr>
                <tr class="site_visited_yes">
                    <td><strong>Site Visited By</strong></td>
                    <td>
                        <input class="normal" type="text" name="site_visited_by">
                    </td>
                </tr>
                <tr class="site_visited_yes">
                    <td><strong>Site visited date/time</strong></td>
                    <td>
                        <input class="normal ticket_datetimepicker" type="text" name="site_visited_date" autocomplete=OFF placeholder="select date from calender">
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div><h3 class="msg">Link Info</h3></div>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Link down date/time: </strong>
                    </td>
                    <td>
                        <input class="normal ticket_datetimepicker" type="text" name="link_down_date" autocomplete=OFF>
                    </td>
                </tr>
                <tr>
                    <td><strong>Restoration date/time:</strong></td>
                    <td>
                        <input class="normal ticket_datetimepicker" type="text" name="restoration_date" autocomplete=OFF>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Restoration done by</strong>
                    </td>
                    <td>
                        <input class="normal" type="text" name="restoration_done_by">
                    </td>
                </tr>
                <tr>
                    <td><strong>Restoration confirmed by</strong></td>
                    <td>
                        <input class="normal" type="text" name="restoration_confirmed_by">
                    </td>
                </tr>
                <tr>
                    <td><strong>Down time duration(U)</strong></td>
                    <td>
                        <input class="normal" type="text" name="downtime_duration_front" autocomplete="off">
                        <input type="hidden" name="downtime_duration"> <!-- duration in minutes -->
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <h3 class="msg">SLA Info</h3>
                    </td>
                    <td>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td><strong>SLA claim</strong></td>
                    <td>
                        <select class="normal" name="sla_claim" style="width: 150px" required>
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </td>
                </tr>
                <tr class="sla_data">
                    <td><strong>Duration(E) in minutes</strong></td>
                    <td>
                        <input class="normal" type="text" name="sla_claim_duration_front" autocomplete="off">
                        <input type="hidden" name="sla_claim_duration">
                    </td>
                </tr>
                <tr class="sla_data">
                    <td><strong>Cause</strong></td>
                    <td>
                        <input class="normal" type="text" name="sla_claim_cause">
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <br>
                        <br>
                        <br>
                        <input class="button submit save" type="submit" name="submit_x" value="Submit Ticket">
                    </td>
                </tr>
            </tbody>
        </form>
    </table>
</div>

<script type="text/javascript">

    $('h3.msg').css({
        'margin-top': '50px',
        'background-color': '#F4F4FF',
        'padding': '10px'
    });
    $('table').css({
        'background-color': ''
    });

    $('.ticket_datetimepicker').datetimepicker({
        controlType: 'select',
        dateFormat: "dd-mm-yy",
        timeFormat: 'hh:mm tt'
    });
    
    $('[name="alt_email"]').blur(function(event) {
        
        var cc_emails = $(event.target).val().replace(/;/g, ',');
        var cc_emails = cc_emails.replace(/\n/g, '');
        if ( cc_emails ) {
            $(event.target).val(cc_emails);
        } else {
            $(event.target).val('');
        }
    });
    
    $('[name="downtime_duration_front"]').on('click', function(event) { //TODO: currently its users duty to select valid range, before and after validation
        var link_down_date = $('[name="link_down_date"]').datetimepicker('getDate');
        var link_restoration_date = $('[name="restoration_date"]').datetimepicker('getDate');
        if ( link_down_date && link_restoration_date ) {
            var link_down_duration = (link_restoration_date-link_down_date)/60000; //in minutes
            if ( link_down_duration <= 0 ) {
                alert('select valid dates to calculate duration');
                $('[name="link_down_date"]').val('');
                $('[name="restoration_date"]').val('');
            } else {
                $('[name="downtime_duration"]').val(link_down_duration);
                $('[name="downtime_duration_front"]').val(link_down_duration+' minutes');
            }
        }
    });
            
    $('[name="sla_claim_duration_front"]').blur(function(event) {
        
        var duration = parseInt($(event.target).val());
        if ( duration ) {
            $('[name="sla_claim_duration"]').val(duration);
            $('[name="sla_claim_duration_front"]').val(duration+' minutes');
        } else {
            $('[name="sla_claim_duration"]').val('');
            $('[name="sla_claim_duration_front"]').val('');
        }
    });

    $('p.title').css({
        'font-weight': 'bold',
        'font-size': '1.2em'
    });

    $('.site_visited_yes').hide();

    $('select[name="site_visited"]').change(function(event) {
        var done = $(event.target).val();
        if (done == 'yes') {
            $('.site_visited_yes').show();
        } else if (done == 'no') {
            $('.site_visited_yes').hide();
        }
    });

    //SLA data
    $('.sla_data').hide();

    $('select[name="sla_claim"]').change(function(event) {
        var sla_claim = $(event.target).val();
        if (sla_claim == 'yes') {
            $('.sla_data').show();
        } else if (sla_claim == 'no') {
            $('.sla_data').hide();
        }
    });

    $('[name="client_id"]').change(function(event) {
        var client_email = $('[name="client_id"] option:selected').attr('id');
        var client_name = $('[name="client_id"] option:selected').text();
        if (!client_name) {
            return false;
        }
        $('[name="email"]').val(client_email);
        $('[name="name"]').val(client_name);

        //filtering cin
        if (client_email) {
            $('[name="cin"] option').each(function(index, element) {
                if ($(element).attr('id') && ($(element).attr('id') != client_name)) {
                    $(element).hide();
                } else {
                    $(element).show();
                }
            });
        }
    });

    $('div#new_ticket input:not(.normal, [type="submit"],[type="reset"],[type="button"]), div#new_ticket textarea').css('width', '600px');
    $('div#new_ticket select:not(.normal)').css('width', '300px');
    $('div#new_ticket th').css('font-size', '1.5em');
    $('div#new_ticket td.submit').css('padding-top', '50px');
    
    $('[name="submit_x"]').click(function(event) {
        if (!$('[name="client_id"]').val()) {
            alert('please select a client');
            event.defaultPrevented();
        }
    });

    var options = {
        script: "ajax.php?api=tickets&f=searchbyemail&limit=10&",
        varname: "input",
        json: true,
        shownoresults: false,
        maxresults: 10,
        callback: function(obj) {
            document.getElementById('email').value = obj.id;
            document.getElementById('name').value = obj.info;
            return false;
        }
    };
    var autosug = new bsn.AutoSuggest('email', options);
</script>