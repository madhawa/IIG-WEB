<h2 align="center">Edit Ticket <a href="<?php echo SCP_URL . '/tickets.php?id=' . $ticket_info['ticket_id'] ?>">&lt;back to ticket&gt;</a></h2>
<div>
    <?php if ($errors['err'] && $_POST['a'] == 'process') { ?>
        <p align="center" class="errormessage"><?php echo $errors['err'] ?></p>
    <?php } elseif ($msg) { ?>
        <p align="center" class="infomessage"><?php echo $msg ?></p>
    <?php } elseif ($warn) { ?>
        <p id="warnmessage"><?php echo $warn ?></p>
    <?php } ?>
</div>
<form action="" method="post">
    <input type="hidden" name="action" value="edit_ticket">
    <input type="hidden" name="ticket_id" value="<?php echo $ticket_info['ticket_id'] ?>">
    <table align="center" cellspacing="2" cellpadding="5" border=1>
        <tr>
            <td colspan="2">
                <div><h3>Ticket Info</h3></div>
                <hr>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Status</strong>
            </td>
            <td>
                <?php echo (($ticket->getStatus() == 'closed') && $ticket->getSLAClaim()) ? $ticket->getStatus() . ' & sla:' . $ticket->getSLAClaim() . '%' : $ticket->getStatus() ?></td>
        </tr>
        <tr>
            <td><strong>Subject</strong></td>
            <td>
                <div class="current_value">
                    <?php echo Format::htmlchars($ticket->getSubject()) ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <input type="text" name="subject" value="<?php echo Format::htmlchars($ticket->getSubject()) ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Root cause</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['root_cause'] ? Format::htmlchars($ticket_info['root_cause']) : 'N/A' ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <input type="text" name="root_cause" value="<?php echo Format::htmlchars($ticket->getSubject()) ?>">
                </div>
            </td>
        </tr>
        <?php if (!$ticket_info['raiser_name']) { ?>
            <tr>

                <td>
                    <strong>Email:</strong>
                </td>
                <td>
                    <?php
                    echo $ticket->getEmail();
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Phone</strong></td>
                <td><?php echo Format::phone($ticket->getPhoneNumber()) ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong>Cc email address</strong></td>
            <td>
                <div class="current_value">
                    <?php echo $ticket->getAltEmail(); ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <input type="text" name="alt_email" value="<?php echo $ticket->getAltEmail(); ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div><h3>Site Info</h3></div>
                <hr>
            </td>
        </tr>
        <?php if ($ticket_info['site_visited_by']) { ?>
            <tr>
                <td><strong>Site visited by</strong></td>
                <td><?php echo $ticket_info['site_visited_by']; ?></td>
            </tr>
            <tr>
                <td><strong>Site visited date</strong></td>
                <td>
                <div class="current_value">
                <?php echo $ticket_info['site_visited_date']; ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                <input class="normal ticket_datetimepicker" type="text" name="site_visited_date" autocomplete=OFF placeholder="select date from calender">
                </div>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2">
                <div><h3>Link Info</h3><button type="button" name="edit_link_info">change</button></div>
                <hr>
            </td>
        </tr>
        <tr class="link_info">
            <td><strong>Link down date</strong></td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['link_down_date'] ? $ticket_info['link_down_date'] : 'N/A'; ?>
                </div>
                <div class="change_value">
                    <input class="normal ticket_datetimepicker" type="text" name="link_down_date" autocomplete=OFF>
                </div>
            </td>
        </tr>

        <tr class="link_info">
            <td>
                <strong>Restoration date</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['restoration_date']; ?>
                </div>
                <div class="change_value">
                    <input class="normal ticket_datetimepicker" type="text" name="restoration_date" autocomplete=OFF>
                </div>
            </td>
        </tr>
        <tr class="link_info">
            <td>
                <strong>Downtime duration</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['downtime_duration'].' minutes'; ?>
                </div>
                <div class="change_value">
                    <input class="normal" type="text" name="downtime_duration_front">
                    <input type="hidden" name="downtime_duration"> <!-- duration in minutes -->
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Restoration done by</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['restoration_done_by']; ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <input class="normal" type="text" name="restoration_done_by" value="<?php echo $ticket_info['restoration_done_by']; ?>">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Restoration confirmed by</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php echo $ticket_info['restoration_confirmed_by']; ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <input class="normal" type="text" name="restoration_confirmed_by" value="<?php echo $ticket_info['restoration_confirmed_by']; ?>">
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <h3>SLA Info</h3>
                <hr>
            </td>
            <td>
                <hr>
            </td>
        </tr>

        <tr>
            <td>
                <strong>SLA claim</strong>
            </td>
            <td>
                <div class="current_value">
                    <?php if ($ticket_info['sla_claim_duration']) { ?>
                        <strong>SLA claim cause:</strong><?php echo $ticket_info['sla_claim_cause']; ?>
                        <br>
                        <strong>SLA claim duration:</strong><?php echo $ticket_info['sla_claim_duration'].' minutes'; ?>
                    <?php } ?>
                </div>
                <button type="button" name="edit_value">change</button>
                <div class="change_value">
                    <strong>SLA claim</strong>
                    <select class="normal" name="sla_claim" style="width: 150px" required>
                        <option value=""></option>
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                    <div class="sla_data">
                        <strong>Duration(E) in minutes</strong>
                        <input class="normal" type="text" name="sla_claim_duration_front">
                        <input type="hidden" name="sla_claim_duration">
                        <br>
                        <strong>Cause</strong>
                        <input class="normal" type="text" name="sla_claim_cause">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Note</strong>
            </td>
            <td>
                <textarea style="width: 400px;" name="note">
                </textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit">Save</button>
            </td>
        </tr>
    </table>
    <script type="text/javascript">

        //css
        $('button').css('float', 'right');
        $('.current_value').css('float', 'left');
        $('p.title').css({
            'font-weight': 'bold',
            'font-size': '1.2em'
        });
        $('dlddl').css({
            'font-size': '1.2em'
        });

        //initializations
        $('div.change_value').hide();
        $('.sla_data').hide();

        //update
        $('table').on('click', 'button[name="edit_value"]', function(event) {
            $(event.target).closest('tr').find('div.current_value').hide();
            $(event.target).closest('tr').find('div.change_value').show();
            $(event.target).hide();
        });

        //for link info
        $('button[name="edit_link_info"]').on('click', function(event) {
            $('.link_info .current_value').hide();
            $('.link_info .change_value').show();
            $(event.target).hide();
        });


        $('.ticket_datetimepicker').datetimepicker({
            controlType: 'select',
            dateFormat: "dd-mm-yy",
            timeFormat: 'hh:mm tt'
        });

        $('[name="downtime_duration_front"]').on('click', function(event) { //TODO: currently its users duty to select valid range, before and after validation
            var link_down_date = $('[name="link_down_date"]').datetimepicker('getDate');
            var link_restoration_date = $('[name="restoration_date"]').datetimepicker('getDate');
            if (link_down_date && link_restoration_date) {
                var link_down_duration = (link_restoration_date - link_down_date) / 60000; //in minutes
                if (link_down_duration <= 0) {
                    alert('select valid dates to calculate duration');
                    $('[name="link_down_date"]').val('');
                    $('[name="restoration_date"]').val('');
                } else {
                    $('[name="downtime_duration"]').val(link_down_duration);
                    $('[name="downtime_duration_front"]').val(link_down_duration + ' minutes');
                }
            }
        });

        $('[name="sla_claim_duration_front"]').blur(function(event) {

            var duration = parseInt($(event.target).val());
            if (duration) {
                $('[name="sla_claim_duration"]').val(duration);
                $('[name="sla_claim_duration_front"]').val(duration + ' minutes');
            } else {
                $('[name="sla_claim_duration"]').val('');
                $('[name="sla_claim_duration_front"]').val('');
            }
        });




        $('select[name="sla_claim"]').change(function(event) {
            var sla_claim = $(event.target).val();
            if (sla_claim == 'yes') {
                $('.sla_data').show();
            } else if (sla_claim == 'no') {
                $('.sla_data').hide();
                $('.sla_claim_tr').hide();
            }

            if (!sla_claim) {
                $('div#sla_data').hide();
            }
        });



    </script>