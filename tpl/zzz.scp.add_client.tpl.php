<!-- TODO: use urldecode for contructing urls -->
<div id="notification">
    <div class="close_box"></div>
</div>

<?php if ($rep['boss_id'] && ($target_tab == 'services')) { ?>
    <div id="odf_mapping">
    </div>

    <h3 id="page_loading" align="center">Please wait while this page is loading</h3>

    <div id="add_services_button" >
        <button class="button" type="button" name="add_service_button" >Add services to this client</button>
    </div>
<?php } ?>

<div id="search_client_div">
    <form id="search_client" method="post" action="">
        <input type="hidden" name="t" value="client">
        <input type="hidden" name="do" value="search">
        client field: 
        <input type="text" name="search_client" required="required">
        search type: 
        <select name="search_type" required="required">
            <option value="id">client id</option>
            <option value="name">client name</option>
            <option value="type">client type</option>
        </select>
        <input type="submit" name="search_button" value="search">
    </form>
</div>

<br />
<div id="add_staff_and_services">
    <script type="javascript">
        $('div#add_staff_and_services').css('opacity', '0.1');
    </script>

    <div id="show_gen_pass">
        <span></span>
    </div>

    <div id="client_management"> <!-- div id changed from add_staff_form -->

        <div id="boss_client">

            <h3 id="boss_client_title" class="msg">
                <?php
                if ($rep['client_name'])
                    $title = 'Client : ' . $rep['client_name'];
                  echo   $title;
                ?>
            </h3>
            <form name="client_staff_form" action="admin.php" method="post">
                <input type="hidden" name="do" value="<?php   echo   $action; ?>">
                <input type="hidden" name="a" value="<?php   echo   Format::htmlchars($_REQUEST['a']); ?>">
                <input type="hidden" name="t" value="client">
                <input type="hidden" name="client_id" value="<?php if (is_object($client)) {   echo   $client->getId(); } ?>">
                <input type="hidden" name="client_boss_id" value="<?php if (is_object($client)) {   echo   $client->getBossId(); } ?>">
                <input type="hidden" name="scp_staff_id" value="<?php   echo   $thisuser->getId(); ?>">
                <input type="hidden" name="service_id" value="<?php   echo   $services['id'] ?>">

                <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                    <tr>
                        <th>Client Name</th>
                        <td>
                            <input type="text" name="client_company_name" value="<?php   echo   $rep['client_company_name']; ?>" required>
                            <input type="hidden" name="client_boss_name" value="<?php   echo   $rep['client_name']; ?>" >
                            &nbsp;<span class="error"><?php   echo   $errors['name']; ?></span>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><span class="highlighted_text_span"> LOGIN</span>:</th>
                        <td>
                            <input type="text" name="client_name" value="<?php   echo   $rep['client_name']; ?>" required>
                            &nbsp;<span class="error"><?php   echo   $errors['name']; ?></span>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Client type</th>
                        <td>
                            <select name="client_type" required>
                                <option value="">Please Select</option>
                                <option value="Enterprise Customer" <?php if ($rep['client_type'] == 'Enterprise Customer')   echo   'selected'; ?> >Enterprise Customer</option>
                                <option value="ISP/BWAs" <?php if ($rep['client_type'] == 'ISP/BWAs')   echo   'selected'; ?> >ISP/BWAs</option>
                                <option value="IIG" <?php if ($rep['client_type'] == 'IIG')   echo   'selected'; ?> >IIG</option>
                                <option value="IGW" <?php if ($rep['client_type'] == 'IGW')   echo   'selected'; ?> >IGW</option>
                                <option value="ANS" <?php if ($rep['client_type'] == 'ANS')   echo   'selected'; ?> >ANS</option>
                                <option value="Call Center/BPO" <?php if ($rep['client_type'] == 'Call Center/BPO')   echo   'selected'; ?> >Call Center/BPO</option>
                                <option value="NGO/Banks" <?php if ($rep['client_type'] == 'NGO/Banks')   echo   'selected'; ?> >NGO/Banks</option>
                                <option value="Government" <?php if ($rep['client_type'] == 'Government')   echo   'selected'; ?> >Government</option>
                                <option value="Others" <?php if ($rep['client_type'] == 'Others')   echo   'selected'; ?> >Others</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Service Type</th>
                        <td>
                            <div id="other_type_container_div">
                                <button type="button" name="add_other_type">add new</button>
                                <button type="button" name="remove_other_type">remove</button>
                                <input type="hidden" name="remove_cin" value="">
                                <br>
                                <?php
                                    $sql_cin = 'SELECT cin, service_type FROM ' . SERVICE_CIN_TABLE . ' WHERE client_id=' . db_input($rep['client_id']);
                                    $type_data = db_query($sql_cin);
                                    while ($cin_row = db_fetch_array($type_data)) {
                                ?>
                                    <div class="each_other_type_div">
                                        Type:<input class="other_type" type="text" name="service_type[]" value="<?php   echo   $cin_row['service_type'] ?>">
                                        <br>
                                        CIN: <input class="other_type" type="text" name="cin_no[]" value="<?php   echo   $cin_row['cin'] ?>">
                                    </div>
                                <?php
                                    }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <script type="text/javascript">
                        var data = 
                            'name:<input class="other_type" type="text" name="client_type[]" value="">\
                            <br>\
                            CIN: <input class="other_type" type="text" name="cin_no[]" value="">';
                        data = '<div class="each_other_type_div">' + data + '</div>';
                        
                        $('button[name="add_other_type"]').click(function() {
                            $('div#other_type_container_div').append(data);
                        });
                        $('button[name="remove_other_type"]').on('click', function(event) {
                            $('[name="remove_cin"]').val('remove_cin');
                            $('div#other_type_container_div div:last-child').remove();
                        });
                    </script>
                    <tr>
                        <th>Single Point of Contact(email):</th>
                        <td>
                            <input type="email" name="single_point_email" value="<?php   echo   $rep['single_point_email']; ?>" required>
                            &nbsp;<span class="error"><?php   echo   $errors['single_point_email']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Single Point of Contact(phone):</th>
                        <td>
                            <input type="text" name="single_point_phone" value="<?php   echo   $rep['single_point_phone']; ?>" required>
                            &nbsp;<span class="error">&nbsp;<?php   echo   $errors['single_point_phone']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Client ASN: &nbsp;&nbsp; <button type="button" name="add_asn_button">add ASN</button></th>
                        <td><input type="text" name="client_org_asn" placeholder="" value="<?php   echo   $rep['client_org_asn']; ?>">
                            &nbsp;<span class="error"><?php   echo   $errors['client_org_asn']; ?></span></td>
                    </tr>
                    <script type="text/javascript">
                            $('button[name="add_asn_button"]').click(function(event) {
                            if (!$(event.target).closest('tr').find('input[name="client_org_asn"]').is(':visible')) {
                                $(event.target).closest('tr').find('input[name="client_org_asn"]').show();
                                $(event.target).text('remove ASN');
                                $(event.target).css('color', 'red');
                            } else {
                                $(event.target).text('add ASN');
                                $(event.target).closest('tr').find('input[name="client_org_asn"]').val('');
                                $(event.target).closest('tr').find('input[name="client_org_asn"]').hide();
                                $(event.target).css('color', '');
                            }
                        }); // END asn field and button
                    </script>
                    <tr>
                        <th>Password:</th>
                        <td><input type="password" name="client_password" AUTOCOMPLETE=OFF placeholder="client login password" <?php if (!$client) {   echo   'required'; } ?> >
                            &nbsp;&nbsp;<span class="error">&nbsp;<?php   echo   $errors['client_password']; ?></span>
                            <button type="button" class="show_pass_buton">show password</button>
                            <button type="button" name="gen_pass" class="button">generate password</button>
                            <script type="text/javascript">
                                $('div#show_gen_pass span').click(function(event) {
                                    $(event.target).hide();
                                });
                                $('button[name="gen_pass"]').click(function() {
                                    $('div#show_gen_pass span').show();
                                    $('.show_pass_buton').show();
                                    var pass = gen_pass(6);
                                    $('span#view_pass').text(pass);
                                    $('div#show_gen_pass span').text(pass);
                                    $('input[name="client_password"]').val(pass);
                                    $('input[name="client_password_again"]').val(pass);
                                });
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <th>Password(Confirm):</th>
                        <td>
                            <input type="password" name="client_password_again" AUTOCOMPLETE=OFF placeholder="confirm password" <?php if (!$client) {   echo   'required'; } ?> >
                            &nbsp;<span class="error">&nbsp;<?php   echo   $errors['client_password_again']; ?></span>
                        </td>
                    </tr>
                </table>
                <br />
                <div class="msg"><span>Client Organogram</span></div>

                <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                    <tr>
                        <th>Employee Name:</th>
                        <td><input type="text" name="client_staff_name" value="<?php   echo   $rep['client_org_name']; ?>" required></td>
                    </tr>
                    <tr>
                        <th>Phone number:</th>
                        <td><input type="text" name="client_staff_phone" placeholder="" value="<?php   echo   $rep['client_org_phone']; ?>" required></td>
                    </tr>
                    <tr>
                        <th>email:</th>
                        <td><input type="text" name="client_staff_email" placeholder="" value="<?php   echo   $rep['client_org_email']; ?>" required></td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td>
                            <select name="client_staff_designation" required>
                                <option value=''>Please select</option>
                                <option value='manager' <?php if ($rep['client_org_designation'] == 'manager')   echo   'selected'; ?> >Manager</option>
                                <option value='md' <?php if ($rep['client_org_designation'] == 'md')   echo   'selected'; ?> >MD</option>
                                <option value='director' <?php if ($rep['client_org_designation'] == 'director')   echo   'selected'; ?> >Director</option>
                                <option value='cto' <?php if ($rep['client_org_designation'] == 'cto')   echo   'selected'; ?> >CTO</option>
                                <option value='assistant_manager' <?php if ($rep['client_org_designation'] == 'assistant_manager')   echo   'selected'; ?> >Assistant manager</option>
                                <option value='deputy_manager' <?php if ($rep['client_org_designation'] == 'deputy_manager')   echo   'selected'; ?> >Deputy manager</option>
                                <option value='senior_manager' <?php if ($rep['client_org_designation'] == 'senior_manager')   echo   'selected'; ?> >Senior Manager</option>
                                <option value='noc_eng' <?php if ($rep['client_org_designation'] == 'noc_eng')   echo   'selected'; ?> >NOC Engineer</option>
                            </select>&nbsp;<span class="error"><?php   echo   $errors['client_designation'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>
                            <select name="client_staff_department" required>
                                <option value=''>Please select</option>
                                <option value='management' <?php if ($rep['client_org_department'] == 'management')   echo   'selected'; ?> >Management</option>
                                <option value='accounts' <?php if ($rep['client_org_department'] == 'accounts')   echo   'selected'; ?> >Accounts</option>
                                <option value='transmission' <?php if ($rep['client_org_department'] == 'transmission')   echo   'selected'; ?> >Transmission</option>
                                <option value='datacom' <?php if ($rep['client_org_department'] == 'datacom')   echo   'selected'; ?> >Datacom</option>
                                <option value='power' <?php if ($rep['client_org_department'] == 'power')   echo   'selected'; ?> >Power</option>
                                <option value='noc' <?php if ($rep['client_org_department'] == 'noc')   echo   'selected'; ?> >NOC</option>
                            </select>&nbsp;<span class="error"><?php   echo   $errors['client_Department'] ?></span>
                        </td>
                    </tr>
                </table>
                <!--
                <?php if ($rep['client_id']) { ?>
                    <br />
                    <div class="msg"><span>Other info</span></div>
                    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                        <tr>
                            <th>Client Id </th>
                            <td><?php   echo   $rep['client_id'] ?></td>
                        </tr>
                        <tr>
                            <th>Client Relationship No:</th>
                            <td><?php   echo   $rep['client_rel_no'] ?></td>
                        </tr>
                        <tr>
                            <th>Circuit Id:</th>
                            <td><?php   echo   $rep['circuit_id'] ?></td>
                        </tr>
                        <tr>
                            <th>Account Created:</th>
                            <td><?php   echo   Format::db_datetime($rep['created']) . ' from ip ' . $rep['reg_ip'] ?></td>
                        </tr>
                        <tr>
                            <th>Account Updated:</th>
                            <td><?php   echo   Format::db_datetime($rep['updated']) ?></td>
                        </tr>
                        <tr>
                            <th>Last Login:</th>
                            <td><?php   echo   Format::db_datetime($rep['lastlogin']) ?></td>
                        </tr>
                    </table>
                <?php } ?>
                -->

                <input id="add_staff_submit" class="button" type="submit" name="submit" value="Submit">


            </form>
        </div> <!-- END div#boss_client -->

        <script type="text/javascript" src="js/add_client.js"></script>



        <?php if ($rep['boss_id']==909099090) { ?>
            <!-- if only there is a client(boss), then load the next sections -->


            <?php if ($all_client_staff) { ?>
                <?php
                $total_true_staff = count($all_client_staff) - 1; //true staffs, excluding the boss
                $head_line = $total_true_staff > 1 ?
                        $total_true_staff . ' users under ' . $rep['client_name'] :
                        $total_true_staff . ' user under ' . $rep['client_name'];
                ?>
                <div id="all_client_staff_corner">
                    <span id="all_client_staff_headline_span" class="msg">
                        <?php   echo   $head_line; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span id="view_all_client_staff_spanbutton">view all</span>
                    </span>
                    <button type="button" name="add_staff_for_client">Add new Staff</button>

                    <?php
                    $staff_display_serial = 1;
                    for ($i = 0; $i < count($all_client_staff); $i++) {
                        $current_staff = $all_client_staff[$i];

                        if (($current_staff['boss_id'] == $rep['client_id']) && ($current_staff['client_id'] != $rep['client_id'])) {
                            //checking again: if current main client is the boss of these clients, and ignore the boss because he is already in the top of the page
                            ?>
                            <div class="each_client_staff_div">
                                <p class="msg"><?php   echo   'Staff ' . $staff_display_serial; ?></p>
                                <form name="client_staff_form" action="admin.php" method="post">
                                    <input type="hidden" name="do" value="update_staff">
                                    <input type="hidden" name="a" value="<?php   echo   Format::htmlchars($_REQUEST['a']); ?>">
                                    <input type="hidden" name="t" value="client">
                                    <input type="hidden" name="boss_id" value="<?php   echo   $current_staff['boss_id']; ?>">
                                    <input type="hidden" name="client_staff_id" value="<?php   echo   $current_staff['client_id']; ?>">

                                    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                                        <tr>
                                            <th>Staff Name:</th>
                                            <td><input type="text" name="staff_name" value="<?php   echo   $current_staff['staff_name']; ?>" ></td>
                                        </tr>
                                        <tr>
                                            <th>Staff email:</th>
                                            <td>
                                                <input type="email" name="staff_email" size=25 value="<?php   echo   $current_staff['staff_email']; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Staff phone:</th>
                                            <td>
                                                <input type="text" name="staff_phone" value="<?php   echo   $current_staff['staff_phone']; ?>">
                                            </td>
                                        </tr>
                                            <th>Designation</th>
                                            <td>
                                                <select name="client_org_designation" >
                                                    <option value=''>Please select</option>
                                                    <option value='manager' <?php if ($current_staff['staff_designation'] == 'manager')   echo   'selected'; ?> >Manager</option>
                                                    <option value='md' <?php if ($current_staff['staff_designation'] == 'md')   echo   'selected'; ?> >MD</option>
                                                    <option value='director' <?php if ($current_staff['staff_designation'] == 'director')   echo   'selected'; ?> >Director</option>
                                                    <option value='cto' <?php if ($current_staff['staff_designation'] == 'cto')   echo   'selected'; ?> >CTO</option>
                                                    <option value='assistant_manager' <?php if ($current_staff['staff_designation'] == 'assistant_manager')   echo   'selected'; ?> >Assistant manager</option>
                                                    <option value='deputy_manager' <?php if ($current_staff['staff_designation'] == 'deputy_manager')   echo   'selected'; ?> >Deputy manager</option>
                                                    <option value='senior_manager' <?php if ($current_staff['staff_designation'] == 'senior_manager')   echo   'selected'; ?> >Senior Manager</option>
                                                    <option value='noc_eng' <?php if ($current_staff['staff_designation'] == 'noc_eng')   echo   'selected'; ?> >NOC Engineer</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>
                                                <select name="client_org_department" >
                                                    <option value=''>Please select</option>
                                                    <option value='management' <?php if ($current_staff['staff_department'] == 'management')   echo   'selected'; ?> >Management</option>
                                                    <option value='accounts' <?php if ($current_staff['staff_department'] == 'accounts')   echo   'selected'; ?> >Accounts</option>
                                                    <option value='transmission' <?php if ($current_staff['staff_department'] == 'transmission')   echo   'selected'; ?> >Transmission</option>
                                                    <option value='datacom' <?php if ($current_staff['staff_department'] == 'datacom')   echo   'selected'; ?> >Datacom</option>
                                                    <option value='power' <?php if ($current_staff['staff_department'] == 'power')   echo   'selected'; ?> >Power</option>
                                                    <option value='noc' <?php if ($current_staff['staff_department'] == 'noc')   echo   'selected'; ?> >NOC</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    <div>
                                        <br />
                                        <div class="msg"><span>Other info</span></div>
                                        <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                                            <tr>
                                                <th>Account Created:</th>
                                                <td><?php   echo   Format::db_datetime($current_staff['created']) . ' from ip ' . $staff['reg_ip'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Account Updated:</th>
                                                <td><?php   echo   Format::db_datetime($current_staff['updated']) ?></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div id="add_staff_submit">
                                        <input class="button" type="submit" name="submit" value="Update">
                                    </div>
                                </form>
                            </div> <!-- end of each staff div -->

                            <?php
                            $staff_display_serial++;
                        }
                        ?> <!-- end of condition -->

                    <?php } ?> <!-- end of looping over all staffs -->

                </div> <!-- END of all staff container div -->
            <?php } ?> <!-- end of condition of whether staff exists for this client(boss) -->



            <br>
            <br>
            <div id="add_staff_for_this_client">
                <h3 class="msg" >Add New staff</h3>
                <form action="admin.php" method="post">
                    <input type="hidden" name="do" value="new_staff">
                    <input type="hidden" name="a" value="<?php   echo   Format::htmlchars($_REQUEST['a']); ?>">
                    <input type="hidden" name="t" value="client">
                    <input type="hidden" name="boss_id" value="">
                    <input type="hidden" name="client_staff_id" value="">

                    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                        <tr>
                            <th>Staff Name:</th>
                            <td><input type="text" name="staff_name" value="<?php   echo   $staff['staff_name']; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Phone number</th>
                            <td><input type="number" name="staff_phone" value="<?php   echo   $staff['staff_phone']; ?>" required></td>
                        </tr>
                        <tr>
                            <th>email:</th>
                            <td><input type="email" name="staff_email" placeholder="" value="<?php   echo   $staff['staff_email']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>Designation</th>
                            <td>
                                <select name="staff_designation" required>
                                    <option value=''>Please select</option>
                                    <option value='manager' <?php if ($staff['client_org_designation'] == 'manager')   echo   'selected'; ?> >Manager</option>
                                    <option value='md' <?php if ($staff['client_org_designation'] == 'md')   echo   'selected'; ?> >MD</option>
                                    <option value='director' <?php if ($staff['client_org_designation'] == 'director')   echo   'selected'; ?> >Director</option>
                                    <option value='cto' <?php if ($staff['client_org_designation'] == 'cto')   echo   'selected'; ?> >CTO</option>
                                    <option value='assistant_manager' <?php if ($staff['client_org_designation'] == 'assistant_manager')   echo   'selected'; ?> >Assistant manager</option>
                                    <option value='deputy_manager' <?php if ($staff['client_org_designation'] == 'deputy_manager')   echo   'selected'; ?> >Deputy manager</option>
                                    <option value='senior_manager' <?php if ($staff['client_org_designation'] == 'senior_manager')   echo   'selected'; ?> >Senior Manager</option>
                                    <option value='noc_eng' <?php if ($staff['client_org_designation'] == 'noc_eng')   echo   'selected'; ?> >NOC Engineer</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>
                                <select name="staff_department" required>
                                    <option value=''>Please select</option>
                                    <option value='management' <?php if ($staff['client_org_department'] == 'management')   echo   'selected'; ?> >Management</option>
                                    <option value='accounts' <?php if ($staff['client_org_department'] == 'accounts')   echo   'selected'; ?> >Accounts</option>
                                    <option value='transmission' <?php if ($staff['client_org_department'] == 'transmission')   echo   'selected'; ?> >Transmission</option>
                                    <option value='datacom' <?php if ($staff['client_org_department'] == 'datacom')   echo   'selected'; ?> >Datacom</option>
                                    <option value='power' <?php if ($staff['client_org_department'] == 'power')   echo   'selected'; ?> >Power</option>
                                    <option value='noc' <?php if ($staff['client_org_department'] == 'noc')   echo   'selected'; ?> >NOC</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div id="client_staff_account_info">
                        <br />
                        <div class="msg"><span>Other info</span></div>
                        <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
                            <tr>
                                <th>Account Created:</th>
                                <td><?php   echo   Format::db_datetime($staff['created']) . ' from ip ' . $staff['reg_ip'] ?></td>
                            </tr>
                            <tr>
                                <th>Account Updated:</th>
                                <td><?php   echo   Format::db_datetime($staff['updated']) ?></td>
                            </tr>
                            <tr>
                                <th>Last Login:</th>
                                <td><?php   echo   Format::db_datetime($staff['lastlogin']) ?></td>
                            </tr>
                        </table>
                    </div>

                    <div id="add_staff_submit">
                        <input class="button" type="submit" name="submit" value="Submit">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" class="button" name="hide_add_staff_form">cancel</button>
                    </div>
                </form>
            </div>
        </div> <!-- END div#client_management -->

        <!-- START add services form -->
        <div id="user_info_shortlist">
            <table border="1" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
                <tr>
                    <th>Name</th>
                    <th>Organogram Name</th>
                    <th>Client Type</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>ASN</th>
                </tr>
                <tr class="<?php   echo   'row1 highlight' ?>" id="<?php   echo   $rep['client_id'] ?>">
                    <td><a href="admin.php?t=client&amp;id=<?php   echo   $rep['client_id'] ?>"><?php   echo   $rep['client_name'] ?></a>&nbsp;</td>
                    <td><?php   echo   $rep['client_org_name'] ?></td>
                    <td><?php   echo   $rep['client_type'] ?></td>
                    <td><?php   echo   $rep['client_org_designation'] ?></td>
                    <td><?php   echo   $rep['client_org_department'] ?></td>
                    <td><?php   echo   $rep['client_org_asn'] ?></td>
                </tr>
            </table>
        </div>
        <br />

        <?php if ($target_tab == 'services') { ?>
            <div id="add_services_container">
                <h2 align="center" class="msg">Services For this Client</h2>
                <div id="add_services">
                    <br />
                    <br />
                    <h3 align="center">Add a Service</h3>
                    <div id="add_service_dropdown">
                        <span class="msg">Add a Service:</span> 
                        <select name="add_services" >
                            <option value="">Please select</option>
                            <option value="ip_bw" <?php if ($services['service_name'] == 'ip_bw')   echo   'selected'; ?> >IP Bandwidth</option>
                            <option value="ip_transit" <?php if ($services['service_name'] == 'ip_transit')   echo   'selected'; ?> >IP Transit</option>
                            <option value="iplc" <?php if ($services['service_name'] == 'iplc')   echo   'selected'; ?> >IPLC</option>
                            <option value="mpls" <?php if ($services['service_name'] == 'mpls')   echo   'selected'; ?> >MPLS</option>
                        </select>
                    </div>

                    <div id="ip_bw_fields">
                        <form id="ip_bw_data">
                            <br />
                            <div class="msg"><span>IP Bandwidth Fields</span></div>
                            <table border="1" cellspacing=0 cellpadding=2 id="ip_bw" class="dtable" align="center" width="100%">
                                <tr>
                                    <th>Amount</th>
                                    <th>1Asiaahl End IP</th>
                                    <th>Client End IP</th>
                                    <th>Remarks</th>
                                </tr>
                                <tr class="row1 highlight" >
                                    <td><input type="text" name="ip_bw_amount" value="<?php   echo   $services['ip_bw_amount'] ?>" />
                                        <select name="ip_bw_unit">
                                            <option value="">Please Select</option>
                                            <option value="kbps" <?php if ($services['ip_bw_unit'] == 'kbps')   echo   'selected'; ?> >KBPS</option>
                                            <option value="mbps" <?php if ($services['ip_bw_unit'] == 'mbps')   echo   'selected'; ?> >MBPS</option>
                                            <option value="gbps" <?php if ($services['ip_bw_unit'] == 'gbps')   echo   'selected'; ?> >GBPS</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="ip_bw_1asiaahl_end_ip" placeholder="example:192.168.70.100" value="<?php   echo   $services['ip_bw_1asiaahl_end_ip'] ?>" /></td>
                                    <td><input type="text" name="ip_bw_client_end_ip" placeholder="example:192.168.70.100" value="<?php   echo   $services['ip_bw_client_end_ip'] ?>" /></td>
                                    <td><input type="text" name="ip_bw_remarks" value="<?php   echo   $services['ip_bw_remarks'] ?>" ></td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div id="ip_transit_fields" >
                        <form id="ip_transit_data">
                            <br />
                            <div class="msg"><span>IP Transit Fields</span></div>
                            <table border="1" cellspacing=0 cellpadding=2 id="ip_transit" class="dtable" align="center" width="100%">
                                <tr>
                                    <th>Amount</th>
                                    <th>1Asiaahl End IP</th>
                                    <th>Client End IP</th>
                                    <th>Allowed Prefix</th>
                                </tr>
                                <tr class="row1 highlight" >
                                    <td><input type="text" name="ip_transit_amount" value="<?php   echo   $services['ip_transit_amount']; ?>" />
                                        <select name="ip_transit_amount_unit">
                                            <option value="">Please Select</option>
                                            <option value="kbps" <?php if ($services['ip_transit_amount_unit'] == 'kbps')   echo   'selected'; ?> >KBPS</option>
                                            <option value="mbps" <?php if ($services['ip_transit_amount_unit'] == 'mbps')   echo   'selected'; ?> >MBPS</option>
                                            <option value="gbps" <?php if ($services['ip_transit_amount_unit'] == 'gbps')   echo   'selected'; ?> >GBPS</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="ip_transit_1asiaahl_end_ip" placeholder="example:192.168.70.100" value="<?php   echo   $services['ip_transit_1asiaahl_end_ip'] ?>" /></td>
                                    <td><input type="text" name="ip_transit_client_end_ip" placeholder="example:192.168.70.100" value="<?php   echo   $services['ip_transit_client_end_ip'] ?>" /></td>
                                    <td><input type="text" name="ip_transit_prefix" value="<?php   echo   $services['ip_transit_prefix'] ?>" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div id="iplc_fields" >
                        <form id="iplc_data">
                            <br />
                            <div class="msg"><span>IPLC Fields</span></div>
                            <table border="1" cellspacing=0 cellpadding=2 id="iplc" class="dtable" align="center" width="100%">
                                <tr>
                                    <th> Amount &#215; Level </th>
                                    <th>Total</th>
                                    <th>Circuit Type</th>
                                    <th>Circuit Diagram</th>
                                </tr>
                                <tr id="iplc_tr" class="row1 highlight" >
                                    <td style="width: auto">
                                        <input class="service_amount" type="text" name="iplc_fields_amount" placeholder="amount" value="<?php   echo   $services['iplc_fields_amount'] ?>" />
                                        &#215;
                                        <select class="service_level" name="iplc_fields_level">
                                            <option value="">Please Select</option>
                                            <option value="e1" <?php if ($services['iplc_fields_level'] == 'e1')   echo   'selected'; ?> >E1</option>
                                            <option value="stm1" <?php if ($services['iplc_fields_level'] == 'stm1')   echo   'selected'; ?> >STM 1</option>
                                            <option value="stm4" <?php if ($services['iplc_fields_level'] == 'stm4')   echo   'selected'; ?> >STM 4</option>
                                            <option value="stm16" <?php if ($services['iplc_fields_level'] == 'stm16')   echo   'selected'; ?> >STM 16</option>
                                            <option value="stm64" <?php if ($services['iplc_fields_level'] == 'stm64')   echo   'selected'; ?> >STM 64</option>
                                            <option value="stm256" <?php if ($services['iplc_fields_level'] == 'stm256')   echo   'selected'; ?> >STM 256</option>
                                            <option value="gig_ethernet" <?php if ($services['iplc_fields_level'] == 'gig_ethernet')   echo   'selected'; ?> >Gig Ethernet</option>
                                        </select>                                
                                    </td>
                                    <td><input class="service_total" type="text" name="iplc_fields_total" placeholder=" amount &#215; level " value="<?php   echo   $services['iplc_fields_total'] ?>" /></td>
                                    <td><select name="iplc_fields_circuit_type">
                                            <option value="">Please Select</option>
                                            <option value="half_circuit" <?php if ($services['iplc_fields_circuit_type'] == 'half_circuit')   echo   'selected'; ?> >Half Circuit</option>
                                            <option value="full_circuit" <?php if ($services['iplc_fields_circuit_type'] == 'full_circuit')   echo   'selected'; ?> >Full Circuit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($services['iplc_fields_circuit_diagram']) { ?>
                                            <a href="<?php   echo   './upload/ckt_diag/' . $services['iplc_fields_circuit_diagram'] ?>" >download diagram file</a>
                                        <?php } ?>
                                        <iframe class="upload_frame" src="upload.php?field=iplc&amp;client_id=<?php   echo   $rep['client_id'] ?>" name="iplc_ckt_diag" >
                                        </iframe>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div id="mpls_fields" >
                        <form id="mpls_data">
                            <br />
                            <div class="msg"><span>MPLS Fields</span></div>
                            <table border="1" cellspacing=0 cellpadding=2 id="mpls" class="dtable" align="center" width="100%">
                                <tr>
                                    <th></th>
                                    <th> Amount &#215; Level </th>
                                    <th>Total</th>
                                    <th>Circuit Type</th>
                                    <th>Circuit Diagram</th>
                                </tr>
                                <tr id="mpls_primary_tr" class="row1 highlight" >
                                    <td>Primary Circuit</td>
                                    <td style="width: auto">
                                        <input class="service_amount" type="text" name="mpls_fields_primary_circuit_amount" placeholder="amount" value="<?php   echo   $services['mpls_fields_primary_circuit_amount'] ?>" />
                                        &#215;
                                        <select class="service_level" name="mpls_fields_primary_circuit_level">
                                            <option value="">Please Select</option>
                                            <option value="e1" <?php if ($services['mpls_fields_primary_circuit_level'] == 'e1')   echo   'selected'; ?> >E1</option>
                                            <option value="stm1" <?php if ($services['mpls_fields_primary_circuit_level'] == 'stm1')   echo   'selected'; ?> >STM 1</option>
                                            <option value="stm4" <?php if ($services['mpls_fields_primary_circuit_level'] == 'stm4')   echo   'selected'; ?> >STM 4</option>
                                            <option value="stm16" <?php if ($services['mpls_fields_primary_circuit_level'] == 'stm16')   echo   'selected'; ?> >STM 16</option>
                                            <option value="stm64" <?php if ($services['mpls_fields_primary_circuit_level'] == 'stm64')   echo   'selected'; ?> >STM 64</option>
                                            <option value="stm256" <?php if ($services['mpls_fields_primary_circuit_level'] == 'stm256')   echo   'selected'; ?> >STM 256</option>
                                            <option value="gig_ethernet" <?php if ($services['mpls_fields_primary_circuit_level'] == 'gig_ethernet')   echo   'selected'; ?> >Gig Ethernet</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="service_total" type="text" name="mpls_fields_primary_circuit_total" placeholder="total = amount &#215; level " />
                                    </td>
                                    <td><select name="mpls_fields_primary_circuit_type">
                                            <option value="">Please Select</option>
                                            <option value="half_circuit" <?php if ($services['mpls_fields_primary_circuit_type'] == 'half_circuit')   echo   'selected'; ?> >Half Circuit</option>
                                            <option value="full_circuit" <?php if ($services['mpls_fields_primary_circuit_type'] == 'full_circuit')   echo   'selected'; ?> >Full Circuit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($services['mpls_fields_primary_circuit_diagram']) { ?>
                                            <a href="<?php   echo   './upload/ckt_diag/' . $services['mpls_fields_primary_circuit_diagram'] ?>" >download diagram file</a>
                                        <?php } ?>
                                        <iframe class="upload_frame" src="upload.php?field=mpls_primary&amp;client_id=<?php   echo   $rep['client_id'] ?>" >
                                        </iframe>
                                    </td>
                                </tr>
                                <tr id="mpls_secondary_tr" class="row1 highlight" >
                                    <td>Secondary Circuit</td>
                                    <td style="width: auto">
                                        <input class="service_amount" type="text" name="mpls_fields_secondary_circuit_amount" placeholder="amount" value="<?php   echo   $services['mpls_fields_secondary_circuit_amount'] ?>" />
                                        &#215;
                                        <select class="service_level" name="mpls_fields_secondary_circuit_level">
                                            <option value="">Please Select</option>
                                            <option value="e1" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'e1')   echo   'selected'; ?> >E1</option>
                                            <option value="stm1" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'stm1')   echo   'selected'; ?> >STM 1</option>
                                            <option value="stm4" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'stm4')   echo   'selected'; ?> >STM 4</option>
                                            <option value="stm16" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'stm16')   echo   'selected'; ?> >STM 16</option>
                                            <option value="stm64" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'stm64')   echo   'selected'; ?> >STM 64</option>
                                            <option value="stm256" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'stm256')   echo   'selected'; ?> >STM 256</option>
                                            <option value="gig_ethernet" <?php if ($services['mpls_fields_secondary_circuit_level'] == 'gig_ethernet')   echo   'selected'; ?> >Gig Ethernet</option>
                                        </select>
                                    </td>
                                    <td><input class="service_total" type="text" name="mpls_fields_secondary_circuit_total" placeholder="total= amount &#215; level " /></td>
                                    <td><select name="mpls_fields_secondary_circuit_type">
                                            <option value="">Please Select</option>
                                            <option value="half_circuit" <?php if ($services['mpls_fields_secondary_circuit_type'] == 'half_circuit')   echo   'selected'; ?> >Half Circuit</option>
                                            <option value="full_circuit" <?php if ($services['mpls_fields_secondary_circuit_type'] == 'full_circuit')   echo   'selected'; ?> >Full Circuit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($services['mpls_fields_secondary_circuit_diagram']) { ?>
                                            <a href="<?php   echo   './upload/ckt_diag/' . $services['mpls_fields_secondary_circuit_diagram'] ?>" >download diagram file</a>
                                        <?php } ?>
                                        <iframe class="upload_frame" src="upload.php?field=mpls_secondary&amp;client_id=<?php   echo   $rep['client_id'] ?>" >
                                        </iframe>
                                    </td>
                                </tr>
                                <tr id="mpls_tertiary_tr" class="row1 highlight" >
                                    <td>Tertiary Circuit</td>
                                    <td style="width: auto">
                                        <input class="service_amount" type="text" name="mpls_fields_tertiary_circuit_amount" placeholder="amount" value="<?php   echo   $services['mpls_fields_tertiary_circuit_amount'] ?>" />
                                        &#215;
                                        <select class="service_level" name="mpls_fields_tertiary_circuit_level">
                                            <option value="">Please Select</option>
                                            <option value="e1" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'e1')   echo   'selected'; ?> >E1</option>
                                            <option value="stm1" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'stm1')   echo   'selected'; ?> >STM 1</option>
                                            <option value="stm4" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'stm4')   echo   'selected'; ?> >STM 4</option>
                                            <option value="stm16" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'stm16')   echo   'selected'; ?> >STM 16</option>
                                            <option value="stm64" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'stm64')   echo   'selected'; ?> >STM 64</option>
                                            <option value="stm256" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'stm256')   echo   'selected'; ?> >STM 256</option>
                                            <option value="gig_ethernet" <?php if ($services['mpls_fields_tertiary_circuit_level'] == 'gig_ethernet')   echo   'selected'; ?> >Gig Ethernet</option>
                                        </select>
                                    </td>
                                    <td><input class="service_total" type="text" name="mpls_fields_tertiary_circuit_total" placeholder="total =  amount &#215; level " /></td>
                                    <td><select name="mpls_fields_tertiary_circuit_type">
                                            <option value="">Please Select</option>
                                            <option value="half_circuit" <?php if ($services['mpls_fields_tertiary_circuit_type'] == 'half_circuit')   echo   'selected'; ?> >Half Circuit</option>
                                            <option value="full_circuit" <?php if ($services['mpls_fields_tertiary_circuit_type'] == 'full_circuit')   echo   'selected'; ?> >Full Circuit</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($services['mpls_fields_tertiary_circuit_diagram']) { ?>
                                            <a href="<?php   echo   './upload/ckt_diag/' . $services['mpls_fields_tertiary_circuit_diagram'] ?>" >download diagram file</a>
                                        <?php } ?>
                                        <iframe class="upload_frame" src="upload.php?field=mpls_tertiary&amp;client_id=<?php   echo   $rep['client_id'] ?>" >
                                        </iframe>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <!--START Requirements for ip address input field -->

                    <!--
                    <script src="js/jquery.caret.js" type="text/javascript"></script>
                    <script src="js/jquery.ipaddress.js" type="text/javascript"></script>
                    <script type="text/javascript">
                        $(function(){
                        $('input[name="ip_bw_1asiaahl_end_ip"]').ipaddress({cidr:true});
                        $('input[name="ip_bw_client_end_ip"]').ipaddress({cidr:true});
                        $('input[name="ip_transit_client_end_ip"]').ipaddress({cidr:true});
                        $('input[name="ip_transit_1asiaahl_end_ip"]').ipaddress({cidr:true});
                        });
                    </script> -->
                    <div id="add_service_submit" align="center">

                        <button type="button" name="add_service_submit" value="Save">Save</button>
                        <button type="button" name="add_service_submit_and_continue" value="Save and Continue to connectivity details" >Save and continue to connectivity details</button>

                    </div>
                </div> <!-- END div#add_services -->

                <br />
                <br />
                <br />
				
				<div id="select_odf">
				</div>

                <div id="connectivity_details">
                    <br>
                    <span id="odf_object"><?php   echo   $services['all_odf_json_string']; ?></span>
                    <form id="con_details_form">
                        <h3 align="center">Connectivity Details</h3>
                        <br />
                        <div id="con_details_local_loop">
                            <span class="msg">Local Loop:</span>
                            <select name="con_details_local_loop" >
                                <option value="">Please Select</option>
                                <option value="nttn" <?php if ($services['con_details_local_loop'] == 'nttn')   echo   'selected'; ?> >NTTN</option>
                                <option value="overhead" <?php if ($services['con_details_local_loop'] == 'over_head')   echo   'selected'; ?> >Over Head</option>
                                <option value="mixed" <?php if ($services['con_details_local_loop'] == 'mixed')   echo   'selected'; ?> >Mixed</option>
                            </select>

                            &nbsp;&nbsp;
                            <span id="con_details_local_loop_nttn_fields">
                                <select name="con_details_local_loop_nttn_fields_nttn" >
                                    <option value="">Select Local NTTN Loop</option>
                                    <option value="f@h" <?php if ($services['con_details_local_loop_nttn_fields_nttn'] == 'f@h')   echo   'selected'; ?> >F@H</option>
                                    <option value="summit" <?php if ($services['con_details_local_loop_nttn_fields_nttn'] == 'summit')   echo   'selected'; ?> >Summit</option>
                                </select>
                            </span>

                            <!--  MUST READ
                            Characteristics standard of any div
                            
                            ## All odfs closest parent div should have class 'odf_details'
                            ## All odfs should have <span class="hidden_id"> immediately after div.odf_details
                            
                            ## All odfs should maintain this structure at least
                            
                            odf table immediate container div should have a unique id(unique just among all such odf divs)
                            <tr class="odf_data_tr">
                                <td class="odf_name_td"><input class="odf_name" /></td>
                                <td class="odf_tray_td"><select class="odf_tray"></select></td>
                                <td class="odf_ports_td">port checkboxes with same name</td>
                            <tr>
                            
                            -->
                            <div class="odf_details" id="con_details_nttn_odf_details">
                                <span class="hidden_id">con_details_nttn_odf_details</span>
                                <span class="msg"></span>
                                <table>
                                    <tr>
                                        <th></th>
                                        <th>Tray and Ports</th>
                                        <th></th>
                                    </tr>
                                    <tr class="odf_data_tr">
                                        <td class="odf_name_td">
                                            <p>NTTN ODF NAME:</p>
                                            <input id="nttn_odf_name" class="odf_name" type="text" name="odf_name" readonly="readonly" value="<?php   echo   $services['con_details_local_loop_nttn_fields_nttn']; ?>" />
                                            <span id="nttn_ckt_type" class="block_display_span">
                                                <p>Circuit Type</p>
                                                <select name="con_details_nttn_odf_circuit_type">
                                                    <option value="">Please Select</option>
                                                    <option value="primary" <?php if ($services['con_details_nttn_odf_circuit_type'] == 'primary')   echo   'selected'; ?> >Primary</option>
                                                    <option value="secondary" <?php if ($services['con_details_nttn_odf_circuit_type'] == 'secondary')   echo   'selected'; ?> >Secondary</option>
                                                </select>
                                            </span>
                                        </td>
                                        <td class="odf_tray_ports_td" align="center" >
                                            <span id="tray_a" class="odf_tray_ports">
                                                <span class="tray_span">Tray A<input type="checkbox" name="tray_a" value="a">
                                                </span>
                                                <span class="ports_span">
                                                    <span>port 1<input type="checkbox" name="odf_port_a" value="1" /></span>
                                                    <span>port 2<input type="checkbox" name="odf_port_a" value="2" /></span>
                                                    <span>port 3<input type="checkbox" name="odf_port_a" value="3" /></span>
                                                    <span>port 4<input type="checkbox" name="odf_port_a" value="4" /></span>
                                                    <span>port 5<input type="checkbox" name="odf_port_a" value="5" /></span>
                                                    <span>port 6<input type="checkbox" name="odf_port_a" value="6" /></span>
                                                    <span>port 7<input type="checkbox" name="odf_port_a" value="7" /></span>
                                                    <span>port 8<input type="checkbox" name="odf_port_a" value="8" /></span>
                                                    <span>port 9<input type="checkbox" name="odf_port_a" value="9" /></span>
                                                    <span>port 10<input type="checkbox" name="odf_port_a" value="10" /></span>
                                                    <span>port 11<input type="checkbox" name="odf_port_a" value="11" /></span>
                                                    <span>port 12<input type="checkbox" name="odf_port_a" value="12" /></span>
                                                </span>
                                            </span>

                                            <span id="tray_b" class="odf_tray_ports">
                                                <span class="tray_span">Tray B<input type="checkbox" name="tray_b" value="b">
                                                </span>
                                                <span class="ports_span">
                                                    <span>port 1<input type="checkbox" name="odf_port_b" value="1" /></span>
                                                    <span>port 2<input type="checkbox" name="odf_port_b" value="2" /></span>
                                                    <span>port 3<input type="checkbox" name="odf_port_b" value="3" /></span>
                                                    <span>port 4<input type="checkbox" name="odf_port_b" value="4" /></span>
                                                    <span>port 5<input type="checkbox" name="odf_port_b" value="5" /></span>
                                                    <span>port 6<input type="checkbox" name="odf_port_b" value="6" /></span>
                                                    <span>port 7<input type="checkbox" name="odf_port_b" value="7" /></span>
                                                    <span>port 8<input type="checkbox" name="odf_port_b" value="8" /></span>
                                                    <span>port 9<input type="checkbox" name="odf_port_b" value="9" /></span>
                                                    <span>port 10<input type="checkbox" name="odf_port_b" value="10" /></span>
                                                    <span>port 11<input type="checkbox" name="odf_port_b" value="11" /></span>
                                                    <span>port 12<input type="checkbox" name="odf_port_b" value="12" /></span>
                                                </span>
                                            </span>
                                            <span id="tray_c" class="odf_tray_ports">
                                                <span class="tray_span">Tray C<input type="checkbox" name="tray_c" value="c">
                                                </span>
                                                <span class="ports_span">
                                                    <span>port 1<input type="checkbox" name="odf_port_c" value="1" /></span>
                                                    <span>port 2<input type="checkbox" name="odf_port_c" value="2" /></span>
                                                    <span>port 3<input type="checkbox" name="odf_port_c" value="3" /></span>
                                                    <span>port 4<input type="checkbox" name="odf_port_c" value="4" /></span>
                                                    <span>port 5<input type="checkbox" name="odf_port_c" value="5" /></span>
                                                    <span>port 6<input type="checkbox" name="odf_port_c" value="6" /></span>
                                                    <span>port 7<input type="checkbox" name="odf_port_c" value="7" /></span>
                                                    <span>port 8<input type="checkbox" name="odf_port_c" value="8" /></span>
                                                    <span>port 9<input type="checkbox" name="odf_port_c" value="9" /></span>
                                                    <span>port 10<input type="checkbox" name="odf_port_c" value="10" /></span>
                                                    <span>port 11<input type="checkbox" name="odf_port_c" value="11" /></span>
                                                    <span>port 12<input type="checkbox" name="odf_port_c" value="12" /></span>
                                                </span>
                                            </span>
                                            <span id="tray_d" class="odf_tray_ports">
                                                <span class="tray_span">Tray D<input type="checkbox" name="tray_d" value="d">
                                                </span>
                                                <span class="ports_span">
                                                    <span>port 1<input type="checkbox" name="odf_port_d" value="1" /></span>
                                                    <span>port 2<input type="checkbox" name="odf_port_d" value="2" /></span>
                                                    <span>port 3<input type="checkbox" name="odf_port_d" value="3" /></span>
                                                    <span>port 4<input type="checkbox" name="odf_port_d" value="4" /></span>
                                                    <span>port 5<input type="checkbox" name="odf_port_d" value="5" /></span>
                                                    <span>port 6<input type="checkbox" name="odf_port_d" value="6" /></span>
                                                    <span>port 7<input type="checkbox" name="odf_port_d" value="7" /></span>
                                                    <span>port 8<input type="checkbox" name="odf_port_d" value="8" /></span>
                                                    <span>port 9<input type="checkbox" name="odf_port_d" value="9" /></span>
                                                    <span>port 10<input type="checkbox" name="odf_port_d" value="10" /></span>
                                                    <span>port 11<input type="checkbox" name="odf_port_d" value="11" /></span>
                                                    <span>port 12<input type="checkbox" name="odf_port_d" value="12" /></span>
                                                </span>
                                            </span>
                                        </td>

                                    </tr>
                                </table>
                            </div>

                            <div id="con_details_local_loop_mixed_fields">
                                <table border="1" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
                                    <tr>
                                        <th>NTTN</th>
                                        <th>Point A</th>
                                        <th>Point B</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="con_details_local_loop_mixed_fields_nttn">
                                                <option value="">Please Select</option>
                                                <option value="f@h" <?php if ($services['con_details_local_loop_mixed_fields_nttn'] == 'f@h')   echo   'selected'; ?> >F@H</option>
                                                <option value="f@h" <?php if ($services['con_details_local_loop_mixed_fields_nttn'] == 'summit')   echo   'selected'; ?> >Summit</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="con_details_local_loop_mixed_fields_nttn_point_a" value="<?php   echo   $services['con_details_local_loop_mixed_fields_nttn_point_a'] ?>" /></td>
                                        <td><input type="text" name="con_details_local_loop_mixed_fields_nttn_point_b" value="<?php   echo   $services['con_details_local_loop_mixed_fields_nttn_point_b'] ?>" /></td>
                                    </tr>
                                    <tr>
                                        <th>Over Head</th>
                                        <th>Point A</th>
                                        <th>Point B</th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="con_details_local_loop_mixed_fields_overhead" value="<?php   echo   $services['con_details_local_loop_mixed_fields_overhead'] ?>" /></td>
                                        <td><input type="text" name="con_details_local_loop_mixed_fields_overhead_point_a" value="<?php   echo   $services['con_details_local_loop_mixed_fields_overhead_point_a'] ?>" /></td>
                                        <td><input type="text" name="con_details_local_loop_mixed_fields_overhead_point_b" value="<?php   echo   $services['con_details_local_loop_mixed_fields_overhead_point_b'] ?>" /></td>
                                    </tr>
                                </table>
                            </div> <!-- END div#con_details_local_loop_mixed_fields -->
                        </div> <!-- END div#con_details_local_loop -->


                        <br />
                        <br />

                        <div id="add_odf_container">
                            <button type="button" name="add_odf_button" >Add ODF</button>
                            &nbsp;&nbsp;
                            <button type="button" name="remove_odf_button" >Remove ODF</button>
                            
                            <span id="this_client_port_color">
                            current client port color
                            </span>
                            <span id="other_client_port_color">
                            other client port color
                            </span>
							<span id="cross_odf_port_color">
							odf to odf link
							</span>
                            <script type="text/javascript">
                                $('span#this_client_port_color').css({'color': 'green', 'margin-left': '30px', 'font-weight': 'bold'});
                                $('span#other_client_port_color').css({'color': '#FF00FF', 'margin-left': '30px', 'font-weight': 'bold'});
                                $('span#cross_odf_port_color').css({'color': 'black', 'margin-left': '30px', 'font-weight': 'bold'});
                            </script>

                            <div id="added_odf">
                            </div>
                        </div>

                        <div id="con_details_interface">
                            <br />
                            <br />
                            <span class="msg" >Connectivity Interface</span>

                            <table border="1" cellspacing=0 cellpadding=2 align="center" width="100%">
                                <tr>
                                    <td><input type="checkbox" id="interface_type_router" name="interface_type_router" value="router" <?php if ($services['interface_type_router'] == 'router')   echo   'checked'; ?> />Router</td>
                                    <td class="interface_router_fields">Router Name:</td>
                                    <td class="interface_router_fields"><input type="text" name="interface_router_name" value="<?php   echo   $services['interface_router_name'] ?>" /></td>
                                    <td class="interface_router_fields">Port: <input type="text" name="interface_router_port" value="<?php   echo   $services['interface_router_port'] ?>" /></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" id="interface_type_mux" name="interface_type_mux" value="mux" <?php if ($services['interface_type_mux'] == 'mux')   echo   'checked'; ?> />MUX</td>
                                    <td class="interface_mux_fields">MUX Name:</td>
                                    <td class="interface_mux_fields"><input type="text" name="interface_mux_name" value="<?php   echo   $services['interface_mux_name'] ?>" /></td>
                                    <td class="interface_mux_fields">Port: <input type="text" name="interface_mux_port" value="<?php   echo   $services['interface_mux_port'] ?>" /></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" id="interface_type_mix" name="interface_type_mix" value="mix" <?php if ($services['interface_type_mix'] == 'mix')   echo   'checked'; ?> />Mixed</td>
                                    <td class="interface_mixed_fields">Router Name:</td>
                                    <td class="interface_mixed_fields"><input type="text" name="interface_mixed_router_name" value="<?php   echo   $services['interface_mixed_router_name'] ?>" /></td>
                                    <td class="interface_mixed_fields">Port: <input type="text" name="interface_mixed_router_port" value="<?php   echo   $services['interface_mixed_router_port'] ?>" /></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="interface_mixed_fields">MUX Name:</td>
                                    <td class="interface_mixed_fields"><input type="text" name="interface_mixed_mux_name" value="<?php   echo   $services['interface_mixed_mux_name'] ?>" /></td>
                                    <td class="interface_mixed_fields">Port: <input type="text" name="interface_mixed_mux_port" value="<?php   echo   $services['interface_mixed_mux_port'] ?>" /></td>
                                </tr>
                            </table>

                            <br />
                            <div id="con_interface_info_submit" align="center">
                                <button type="button" name="con_details_submit">Save</button>
                                <button type="button" name="con_details_submit_and_continue" >Save and Continue...</button>
                            </div>

                        </div> <!-- END div#con_details_interface -->
                    </form>
                </div> <!-- END div#connectivity_details -->

                <br />
                <br /> 
                <br /> 
                <br /> 
                <div id="con_dates">
                    <form id="con_dates_form">
                        <table>
                            <tr>
                                <td><span class="msg">Link Activation Date: </span></td>
                                <td></td>
                                <td><input type="text" name="link_act_date" value="<?php   echo   $services['link_act_date'] ?>" onclick="event.cancelBubble = true;
                                                                calendar(this);" /></td>
                            </tr>
                            <tr>
                                <td><span class="msg">Test Allocation Date</span></td>
                                <td><span>From: </span></td>
                                <td><input type="text" name="test_alloc_from" value="<?php   echo   $services['test_alloc_from'] ?>" onclick="event.cancelBubble = true;
                                                                calendar(this);" /></td>
                                <td><span>To: </span><input type="text" name="test_alloc_to" value="<?php   echo   $services['test_alloc_to'] ?>" onclick="event.cancelBubble = true;
                                                                calendar(this);" /></td>
                            </tr>
                            <tr>
                                <td><span class="msg">Billing Statement Date: </span></td>
                                <td></td>
                                <td><input type="text" name="billing_statement_date" value="<?php   echo   $services['billing_statement_date'] ?>" onclick="event.cancelBubble = true;
                                                                calendar(this);" /></td>
                            </tr>
                        </table>
                        <br />
                        <span class="msg">Remarks: </span>
                        <br />
                        <textarea name="con_details_remarks" value="<?php   echo   $services['con_details_remarks'] ?>" rows="4">
                        </textarea>

                        <br />
                        <br />
                        <div id="con_details_submit" align="center">
                            <button type="button" name="con_dates_save" >Save</button>
                            <button type="button" name="con_dates_save_and_comission">Save and comission service</button>
                        </div>
                    </form>
                </div>
            </div>
            <script type="text/javascript" src="js/notification.js"></script>
            <script type="text/javascript" src="js/services.js"></script>
        <?php } ?>
    </div> <!-- END div#add_staff_and_services -->

<?php } ?>
