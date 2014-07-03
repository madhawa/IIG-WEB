<?php
/**
 * TODO: make this form at first to validate in browser by js or html5
 * body.order.tpl.php, body template for create order page
 * @author Minhaj <polarglow06@gmail.com>
 * @copyright (c) 2013, Minhaj
 * @package HelpDeskConnected
 */
//TODO: make this form responsive: regain field data from previous failed submission
$readonly = '';
$form_action = 'order.create.php';
if ($do == 'staff_view' || $do == 'client_view') {
    $readonly = 'readonly';
    $form_action = '';
}
if ( $do == 'new_by_scp_staff' ) {
    $form_action = './orders.php?do=new_order';
}
?>
required fields are marked by a star
<br>
<br>
<div id="order_full_form">
    <script type="text/javascript">
        $(function() {
            $(".datepicker").datepicker();
        });
    </script>
    <?php if ( !$thisuser->isClient() ) { ?>
        <button type="button" name="edit_order">edit this order</button>
    <?php } ?>
    <h1 align="center">SERVICE ORDER FORM</h1>
    <br>
    <?php if ($do) { ?>

        <?php if ($do == 'create_by_client') { ?>
            <form name="service_order_form" action="<?php   echo   $form_action; ?>" method="POST" enctype="multipart/form-data" >
        <?php } ?>
        <?php if ($do == 'new_by_scp_staff') { ?>
            <form name="service_order_form" action="<?php   echo   ''; ?>" method="POST" enctype="multipart/form-data" >
        <?php } ?>

        <?php if (($do == 'staff_view') && (is_object($order) && $order->Pending())) { ?>
            <form name="service_order_form" action="<?php   echo   $form_action; ?>" method="POST" enctype="multipart/form-data" >
        <?php } ?>

        <?php if (($do == 'client_view') && is_object($order) && $order->client_can_cancel()) { ?>
            <form name="service_order_form" action="<?php   echo   $form_action; ?>" method="POST" enctype="multipart/form-data" >
        <?php } ?>

    <?php } ?>
                    <div id="service_order_form_head">
                        <div id="service_order_form_head_fields">
                            <input type="hidden" name="do" value="<?php   echo   $do; ?>">
                            <input  type="hidden" name="a" value="">
                            <input type="hidden" name="t" value="">
                            <input type="hidden" name="client_id" value="<?php if ($thisuser->isClient())   echo   $thisuser->getId(); ?>">
                            <input type="hidden" name="order_id" value="<?php   echo   $rep['order_id'] ? $rep['order_id'] : $order_id; ?>">
                            <table width="auto">
                                <tr>
                                    <td>Customer Relationship No:</td><td><input  type="text" value="<?php   echo   $rep['customer_rel_no']; ?>" name="customer_rel_no" required>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['customer_rel_no']; ?></span></td>
                                    <td width="60%" id="company_logo" rowspan="6"><img align="right" src="./images/company_logo.png" alt="logo"></td>
                                </tr>
                                <tr>
                                    <td>Customer Name:</td><td><input  type="text" name="customer_name" value="<?php
                                    if ($do == 'create_by_client') {
                                          echo   $thisuser->getName();
                                    } else {
                                          echo   $rep['customer_name'];
                                    }
                                    ?>" required>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['customer_name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Customer Email ID:</td><td><input  type="email" name="customer_email" value="<?php 
                                    if ($do == 'create_by_client') {
                                          echo   $thisuser->getEmail();
                                    } else {
                                          echo   $rep['customer_email']; 
                                    }
                                    ?>" required>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['customer_email']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Customer Type:</td>
                                    <td><select name="customer_type" required>
                                            <option value="">Please Select</option>
                                            <option value="Enterprise Customer" <?php if ($rep['customer_type'] == 'Enterprise Customer')   echo   'selected'; ?> >Enterprise Customer</option>
                                            <option value="ISP/BWAs" <?php if ($rep['customer_type'] == 'ISP/BWAs')   echo   'selected'; ?> >ISP/BWAs</option>
                                            <option value="IIG" <?php if ($rep['customer_type'] == 'IIG')   echo   'selected'; ?> >IIG</option>
                                            <option value="IGW" <?php if ($rep['customer_type'] == 'IGW')   echo   'selected'; ?> >IGW</option>
                                            <option value="ANS" <?php if ($rep['customer_type'] == 'ANS')   echo   'selected'; ?> >ANS</option>
                                            <option value="Call Center/BPO" <?php if ($rep['customer_type'] == 'Call Center/BPO')   echo   'selected'; ?> >Call Center/BPO</option>
                                            <option value="NGO/Banks" <?php if ($rep['customer_type'] == 'NGO/Banks')   echo   'selected'; ?> >NGO/Banks</option>
                                            <option value="Government" <?php if ($rep['customer_type'] == 'Government')   echo   'selected'; ?> >Government</option>
                                            <option value="Others" <?php if ($rep['customer_type'] == 'Others')   echo   'selected'; ?> >Others</option>
                                        </select>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['customer_type']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Circuit Type:</td>
                                    <td><select name="circuit_type" required>
                                            <option value="">Please Select</option>
                                            <option value="Half-Circuit" <?php if ($rep['service_type'] == 'Half-Circuit')   echo   'selected'; ?> >Half-Circuit</option>
                                            <option value="Full-Circuit" <?php if ($rep['service_type'] == 'Full-Circuit')   echo   'selected'; ?> >Full-Circuit</option>
                                            <option value="OSS" <?php if ($rep['service_type'] == 'OSS')   echo   'selected'; ?> >OSS</option>
                                            <option value="Partial" <?php if ($rep['service_type'] == 'Partial')   echo   'selected'; ?> >Partial</option>
                                        </select>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['service_type']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Service Type:</td>
                                    <td><select name="service_type" required>
                                            <option value="">Please Select</option>
                                            <option value="only IP Transit" <?php if ($rep['circuit_type'] == 'only IP Transit')   echo   'selected'; ?> >only IP Transit</option>
                                            <option value="IP Bandwidth" <?php if ($rep['circuit_type'] == 'IP Bandwidth')   echo   'selected'; ?> >IP Bandwidth</option>
                                            <option value="IP transit + IPLC[Full Circuit]" <?php if ($rep['circuit_type'] == 'IP transit + IPLC[Full Circuit]')   echo   'selected'; ?> >IP transit + IPLC[Full Circuit]</option>
                                            <option value="P transit + IPLC[half Circuit]" <?php if ($rep['circuit_type'] == 'IP transit + IPLC[half Circuit]')   echo   'selected'; ?> >IP transit + IPLC[half Circuit]</option>
                                            <option value="IPLC[Half Circuit]" <?php if ($rep['circuit_type'] == 'IPLC[Half Circuit]')   echo   'selected'; ?> >IPLC[Half Circuit]</option>
                                            <option value="IPLC[Full Circuit]" <?php if ($rep['circuit_type'] == 'IPLC[Full Circuit]')   echo   'selected'; ?> >IPLC[Full Circuit]</option>
                                            <option value="Global MPLS" <?php if ($rep['circuit_type'] == 'Global MPLS')   echo   'selected'; ?> >Global MPLS</option>
                                            <option value="Internartional Ethernet" <?php if ($rep['circuit_type'] == 'Internartional Ethernet')   echo   'selected'; ?> >Internartional Ethernet</option>
                                        </select>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['circuit_type']; ?></span></td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div id="order_details">
                        <h1 allign="center">Order Details</h1>
                        <hr>

                        <div id="order_creator_info">
                            <br>
                            <p id="order_body_head_text">
                                We hereby order for Bandwidth connectivity for our Telecommunication services to 1Asia Alliance Communication Ltd, subject to terms and conditions as described in the order form, this service order form fully comply with MSA and SLA as agreed, We are furnishing the necessary details as follows:-
                            </p>
                            <table>
                                <tr>
                                    <td>Contact Person Name:</td><td colspan="5"><input  type="text" name="order_creator_name" value="<?php   echo   $rep['order_creator_name']; ?>" required>
                                        &nbsp;<span class="error">&nbsp;<?php   echo   $errors['order_creator_name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Designation:</td><td><input  type="text" name="order_creator_designation" value="<?php   echo   $rep['order_creator_designation']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_designation']; ?></span></td>
                                    <td width="20%">Department Name:</td><td colspan="3"><input  type="text" name="order_creator_dept_name" value="<?php   echo   $rep['order_creator_dept_name']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_dept_name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Address:</td><td colspan="5"><input  type="text" name="order_creator_address" value="<?php   echo   $rep['order_creator_address']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_address']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>City:</td><td><input  type="text" name="order_creator_city" value="<?php   echo   $rep['order_creator_city']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_city']; ?></span></td>
                                    <td>ZIP/PO Code:</td><td><input   type="text" name="order_creator_zip_or_po" value="<?php   echo   $rep['order_creator_zip_or_po']; ?>" placeholder="minimum 4 digit" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_zip_or_po']; ?></span></td>
                                    <td>Country:</td><td><input  type="text" name="order_creator_country" value="<?php   echo   $rep['order_creator_country']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_country']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Office Phone:</td><td><input  type="text" name="order_creator_office_phone" value="<?php   echo   $rep['order_creator_office_phone']; ?>" placeholder="01********" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_office_phone']; ?></span></td>
                                    <td>Fax No:</td><td><input  type="text" name="order_creator_fax" value="<?php   echo   $rep['order_creator_fax']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_fax']; ?></span></td>
                                    <td>Mobile:</td><td><input  type="text" name="order_creator_mobile" value="<?php   echo   $rep['order_creator_mobile']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_mobile']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Ready For Service Date:</td><td><input  type="text" name="order_creator_service_ready_date" class="datepicker" value="<?php   echo   $rep['order_creator_service_ready_date']; ?>" placeholder="click to select" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_creator_service_ready_date']; ?></span></td>
                                </tr>
                            </table>
                            <br>
                            <br>
                        </div> <!-- END id="order_creator_info" -->

                        <br>
                        <br>
                        <div id="customer_end_details">
                            <h1>Customer End Details</h1>
                            <hr>
                            <br>
                            <table>
                                <tr>
                                    <td>Contact Person Name:</td><td colspan="5"><input  type="text" name="order_customer_name" value="<?php   echo   $rep['order_customer_name']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Designation:</td><td><input  type="text" name="order_customer_designation" value="<?php   echo   $rep['order_customer_designation']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_designation']; ?></span></td>
                                    <td>Department Name:</td><td><input  type="text" name="order_customer_dept_name" value="<?php   echo   $rep['order_customer_dept_name']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_dept_name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Address:</td><td colspan="5"><input  type="text" name="order_customer_address" value="<?php   echo   $rep['order_customer_address']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_address']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>City:</td><td><input  type="text" name="order_customer_city" value="<?php   echo   $rep['order_customer_city']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_city']; ?></span></td>
                                    <td>ZIP/PO Code:</td><td><input  type="text" name="order_customer_zip_or_po" value="<?php   echo   $rep['order_customer_zip_or_po']; ?>" placeholder="minimum 4 digit" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_zip_or_po']; ?></span></td>
                                    <td>Country:</td><td><input  type="text" name="order_customer_country" value="<?php   echo   $rep['order_customer_country']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_country']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Office Phone:</td><td><input  type="text" name="order_customer_phone_office" value="<?php   echo   $rep['order_customer_phone_office']; ?>" placeholder="01********" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_pone_office']; ?></span></td>
                                    <td>Fax No:</td><td><input  type="text" name="order_customer_fax" value="<?php   echo   $rep['order_customer_fax']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_fax']; ?></span></td>
                                    <td>Mobile:</td><td><input  type="text" name="order_customer_mobile" value="<?php   echo   $rep['order_customer_mobile']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_mobile']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Backhaul Provider:</td>
                                    <td><select name="order_customer_backhaul_provider" required>
                                            <option value="">Please Select</option>
                                            <option value="NTTN-F@H" <?php if ($rep['order_customer_backhaul_provider'] == 'NTTN-F@H')   echo   'selected'; ?> >NTTN-F@H</option>
                                            <option value="NTTN-Other" <?php if ($rep['order_customer_backhaul_provider'] == 'NTTN-Other')   echo   'selected'; ?> >NTTN-Other</option>
                                            <option value="NTTN + INTL-1ASIA" <?php if ($rep['order_customer_backhaul_provider'] == 'NTTN + INTL-1ASIA')   echo   'selected'; ?> >NTTN + INTL-1ASIA</option>
                                            <option value="NTTN-SCL + INTL-1ASIA" <?php if ($rep['order_customer_backhaul_provider'] == 'NTTN-SCL + INTL-1ASIA')   echo   'selected'; ?> >NTTN-SCL + INTL-1ASIA</option>
                                            <option value="NTTN-F@H + INTL-1ASIA" <?php if ($rep['order_customer_backhaul_provider'] == 'NTTN-F@H + INTL-1ASIA')   echo   'selected'; ?> >NTTN-F@H + INTL-1ASIA</option>
                                            <option value="INTL-1ASIA" <?php if ($rep['order_customer_backhaul_provider'] == 'INTL-1ASIA')   echo   'selected'; ?> >INTL-1ASIA</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_backhaul_provider']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Backhaul Responsibility:</td>
                                    <td><select name="order_customer_backhaul_responsibility" required>
                                            <option value="">Please Select</option>
                                            <option value="Customer Responsibility" <?php if ($rep['order_customer_backhaul_responsibility'] == 'Customer Responsibility')   echo   'selected'; ?> >Customer Responsibility</option>
                                            <option value="1Asia-AHL Responsibility" <?php if ($rep['order_customer_backhaul_responsibility'] == '1Asia-AHL Responsibility')   echo   'selected'; ?> >1Asia-AHL Responsibility</option>
                                            <option value="Domestic-Customer" <?php if ($rep['order_customer_backhaul_responsibility'] == 'Domestic-Customer')   echo   'selected'; ?> >Domestic-Customer</option>
                                            <option value="International-1Asia-AHL" <?php if ($rep['order_customer_backhaul_responsibility'] == 'International-1Asia-AHL')   echo   'selected'; ?> >International-1Asia-AHL</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_backhaul_responsibility']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Equipment to be used:</td>
                                    <td><select name="order_customer_equipment_to_be_used" required>
                                            <option value="">Please Select</option>
                                            <option value="Router only" <?php if ($rep['order_customer_equipment_to_be_used'] == 'Router only')   echo   'selected'; ?> >Router only</option>
                                            <option value="MUX only" <?php if ($rep['order_customer_equipment_to_be_used'] == 'MUX only')   echo   'selected'; ?> >MUX only</option>
                                            <option value="Router + MUX" <?php if ($rep['order_customer_equipment_to_be_used'] == 'Router + MUX')   echo   'selected'; ?> >Router + MUX</option>
                                            <option value="Other" <?php if ($rep['order_customer_equipment_to_be_used'] == 'Other')   echo   'selected'; ?> >Other</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_equipment_to_be_used']; ?></span></td>
                                    <td align="right">If others:</td><td colspan="3"><input   type="text" name="order_customer_equipment_others" value="<?php   echo   $rep['order_customer_equipment_others']; ?>"></td>
                                </tr>
                                <tr>
                                    <td>Equipment Name:</td><td><input  type="text" name="order_customer_equipment_name" value="<?php   echo   $rep['order_customer_equipment_name']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_equipment_name']; ?></span></td>
                                    <td align="right">Model:</td><td><input   type="text" name="order_customer_equipment_model" value="<?php   echo   $rep['order_customer_equipment_model']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_equipment_model']; ?></span></td>
                                    <td>Vendor:</td><td><input  type="text" name="order_customer_equipment_vendor" value="<?php   echo   $rep['order_customer_equipment_vendor']; ?>" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_equipment_vendor']; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Connectivity Interface:</td>
                                    <td><select name="order_customer_connectivity_interface" required>
                                            <option value="">Please Select</option>
                                            <option value="Ethernet(10/100)" <?php if ($rep['order_customer_connectivity_interface'] == 'Ethernet(10/100)')   echo   'selected'; ?> >Ethernet(10/100)</option>
                                            <option value="Ethernet(10/100/1000)" <?php if ($rep['order_customer_connectivity_interface'] == 'Ethernet(10/100/1000)')   echo   'selected'; ?> >Ethernet(10/100/1000)</option>
                                            <option value="GigE-Optical-SM" <?php if ($rep['order_customer_connectivity_interface'] == 'GigE-Optical-SM')   echo   'selected'; ?> >GigE-Optical-SM</option>
                                            <option value="GigE-Optical-MM" <?php if ($rep['order_customer_connectivity_interface'] == 'GigE-Optical-MM')   echo   'selected'; ?> >GigE-Optical-MM</option>
                                            <option value="STM-1 POS" <?php if ($rep['order_customer_connectivity_interface'] == 'STM-1 POS')   echo   'selected'; ?> >STM-1 POS</option>
                                            <option value="STM-4 POS" <?php if ($rep['order_customer_connectivity_interface'] == 'STM-4 POS')   echo   'selected'; ?> >STM-4 POS</option>
                                            <option value="STM-16 POS" <?php if ($rep['order_customer_connectivity_interface'] == 'STM-16 POS')   echo   'selected'; ?> >STM-16 POS</option>
                                            <option value="STM-64 POS" <?php if ($rep['order_customer_connectivity_interface'] == 'STM-64 POS')   echo   'selected'; ?> >STM-64 POS</option>
                                            <option value="TENGE-XFP" <?php if ($rep['order_customer_connectivity_interface'] == 'TENGE-XFP')   echo   'selected'; ?> >TENGE-XFP</option>
                                            <option value="DS-3" <?php if ($rep['order_customer_connectivity_interface'] == 'DS-3')   echo   'selected'; ?> >DS-3</option>
                                            <option value="E1" <?php if ($rep['order_customer_connectivity_interface'] == 'E1')   echo   'selected'; ?> >E1</option>
                                            <option value="Other" <?php if ($rep['order_customer_connectivity_interface'] == 'Other')   echo   'selected'; ?> >Other</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_connectivity_interface']; ?></span></td>
                                    <td align="right">If Others:</td><td colspan="3"><input  type="text" name="order_customer_connectivity_interface_others" value="<?php   echo   $rep['order_customer_connectivity_interface_others']; ?>"></td>
                                </tr>
                                <tr>
                                    <td>Protocol to be used:</td>
                                    <td><select name="order_customer_protocol_to_be_used" required>
                                            <option value="">Please Select</option>
                                            <option value="IP" <?php if ($rep['order_customer_protocol_to_be_used'] == 'IP')   echo   'selected'; ?> >IP</option>
                                            <option value="Clear Channel" <?php if ($rep['order_customer_protocol_to_be_used'] == 'Clear Channel')   echo   'selected'; ?> >Clear Channel</option>
                                            <option value="Channelized" <?php if ($rep['order_customer_protocol_to_be_used'] == 'Channelized')   echo   'selected'; ?> >Channelized</option>
                                            <option value="Non Channelized" <?php if ($rep['order_customer_protocol_to_be_used'] == 'Non Channelized')   echo   'selected'; ?> >Non Channelized</option>
                                            <option value="Other" <?php if ($rep['order_customer_protocol_to_be_used'] == 'Other')   echo   'selected'; ?> >Other</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_protocol_to_be_used']; ?></span></td>
                                    <td align="right">If Others:</td><td colspan="3"><input  type="text" name="order_customer_protocol_others" value="<?php   echo   $rep['order_customer_protocol_others']; ?>"></td>
                                </tr>
                                <tr>
                                    <td>Connectivity Capacity:</td>
                                    <td><select name="order_customer_connectivity_capacity" required>
                                            <option value="">Please Select</option>
                                            <option value="10-mbps" <?php if ($rep['order_customer_connectivity_capacity'] == '10-mbps')   echo   'selected'; ?> >10 Mbps</option>
                                            <option value="100-mbps" <?php if ($rep['order_customer_connectivity_capacity'] == '100-mbps')   echo   'selected'; ?> >100 Mbps</option>
                                            <option value="1000-mbps" <?php if ($rep['order_customer_connectivity_capacity'] == '1000-mbps')   echo   'selected'; ?> >1000 Mbps</option>
                                            <option value="stm-1" <?php if ($rep['order_customer_connectivity_capacity'] == 'stm-1')   echo   'selected'; ?> >STM-1</option>
                                            <option value="stm-4" <?php if ($rep['order_customer_connectivity_capacity'] == 'stm-4')   echo   'selected'; ?> >STM-4</option>
                                            <option value="stm-16" <?php if ($rep['order_customer_connectivity_capacity'] == 'stm-16')   echo   'selected'; ?> >STM-16</option>
                                            <option value="stm-64" <?php if ($rep['order_customer_connectivity_capacity'] == 'stm-64')   echo   'selected'; ?> >STM-64</option>
                                            <option value="10G" <?php if ($rep['order_customer_connectivity_capacity'] == '10G')   echo   'selected'; ?> >10G</option>
                                            <option value="E1" <?php if ($rep['order_customer_connectivity_capacity'] == 'E1')   echo   'selected'; ?> >E1</option>
                                            <option value="DS3" <?php if ($rep['order_customer_connectivity_capacity'] == 'DS3')   echo   'selected'; ?> >DS3</option>
                                            <option value="Other" <?php if ($rep['order_customer_connectivity_capacity'] == 'Other')   echo   'selected'; ?> >Other</option>
                                        </select>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_customer_connectivity_capacity']; ?></span></td>
                                    <td align="right">If Others:</td><td colspan="3"><input  type="text" name="order_customer_connectivity_capacity_others" value="<?php   echo   $rep['order_customer_connectivity_capacity_others']; ?>"></td>
                                </tr>
                            </table>
                            <br>
                            Special Instructions If Any:
                            <br><textarea name="order_customer_special_ins" rows="4" cols="70" ><?php   echo   $rep['order_customer_special_ins']; ?></textarea>


                            <br>
                            <br>
                            <br>
                            <br>
                            <div id="create_order_technical_info">
                                <h1>Technical info</h1>
                                <hr>
                                <br>
                                <table>
                                    <tr>
                                        <td>Technical Contact Name:</td>
                                        <td><input   type="text" name="order_technical_contact_name" value="<?php   echo   $rep['order_technical_contact_name']; ?>" required>&nbsp;<span class="error"><?php   echo   $errors['order_technical_contact_name']; ?></span></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile No:</td>
                                        <td><input  type="text" name="order_technical_contact_mobile" value="<?php   echo   $rep['order_technical_contact_mobile']; ?>" required>
                                            &nbsp;<span class="error"><?php   echo   $errors['order_technical_contact_mobile']; ?></span></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Phone Number:</td>
                                        <td><input   type="text" name="order_technical_contact_phone" value="<?php   echo   $rep['order_technical_contact_phone']; ?>" placeholder="01********" required>
                                        &nbsp;<span class="error"><?php   echo   $errors['order_technical_contact_phone']; ?></span></td>
                                        <td>Email ID:</td>
                                        <td><input  type="email" name="order_technical_contact_email" value="<?php   echo   $rep['order_technical_contact_email']; ?>" required>
                                            &nbsp;<span class="error"><?php   echo   $errors['order_technical_contact_email']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Messenger IDs:</td>
                                        <td><input style="width: 200px" type="text" name="order_technical_contact_messengers" value="<?php   echo   $rep['order_technical_contact_messengers']; ?>" required placeholder="comma seperated"> &nbsp;<span class="error"><?php   echo   $errors['order_technical_contact_messengers']; ?></span></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Routing Type Required:</td>
                                        <td>
                                            <select name="order_routing_type" required>
                                                <option value="">Please Select</option>
                                                <option value="Static Routing" <?php if ($rep['order_routing_type'] == 'Static Routing')   echo   'selected'; ?> >Static Routing</option>
                                                <option value="BGP" <?php if ($rep['order_routing_type'] == 'BGP')   echo   'selected'; ?> >BGP</option>
                                                <option value="Other" <?php if ($rep['order_routing_type'] == 'Other')   echo   'selected'; ?> >Other</option>
                                            </select>
                                            &nbsp;<span class="error"><?php   echo   $errors['order_routing_type']; ?></span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Customer AS System Name:</td>
                                        <td><input  type="text" name="order_customer_as_sys_name" value="<?php   echo   $rep['order_customer_as_sys_name']; ?>" required> &nbsp;<span class="error"><?php   echo   $errors['order_customer_as_sys_name']; ?></span></td>
                                        <td>AS Number:</td>
                                        <td><input  type="text" name="order_customer_as_sys_num" value="<?php   echo   $rep['order_customer_as_sys_num']; ?>" placeholder="example: 10102" required>&nbsp;<span class="error"><?php   echo   $errors['order_customer_as_sys_num']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>AS SET Number:</td>
                                        <td><input  type="text" name="order_customer_as_set_num" value="<?php   echo   $rep['order_customer_as_set_num']; ?>" required>&nbsp;<span class="error"><?php   echo   $errors['order_customer_as_set_num']; ?></span></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>BGP Routing:</td>
                                        <td>
                                            <select name="order_bgp_routing" required>
                                                <option value="">Please Select...</option>
                                                <option value="Default Route" <?php if ($rep['order_bgp_routing'] == 'Default Route')   echo   'selected'; ?> >Default Route</option>
                                                <option value="Full Route Table" <?php if ($rep['order_bgp_routing'] == 'Full Route Table')   echo   'selected'; ?> >Full Route Table</option>
                                                <option value="Partial Route Table" <?php if ($rep['order_bgp_routing'] == 'Partial Route Table')   echo   'selected'; ?> >Partial Route Table</option>
                                                <option value="Other" <?php if ($rep['order_bgp_routing'] == 'Other')   echo   'selected'; ?> >Other</option>
                                            </select>&nbsp;<span class="error"><?php   echo   $errors['order_bgp_routing']; ?></span></td>
                                        <td>Router Name:</td>
                                        <td><input  type="text" name="order_router_name" value="<?php   echo   $rep['order_router_name']; ?>" required>&nbsp;<span class="error"><?php   echo   $errors['order_router_name']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>bandwidth speed(CIR):</td>
                                        <td style="width: auto"><input  type="text" name="order_bw_speed_cir" value="<?php   echo   $rep['order_bw_speed_cir']; ?>" placeholder="Mbps" required><span class="error"><?php   echo   $errors['order_bw_speed_cir']; ?></span></td>
                                        <td>Max Burstable Limit:</td>
                                        <td><input  type="text" name="order_max_burstable_limit" value="<?php   echo   $rep['order_max_burstable_limit']; ?>" placeholder="Mbps" required><span class="error"><?php   echo   $errors['order_max_burstable_limit']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Connectivity Interface:</td>
                                        <td><select name="connectivity_interface" required>
                                                <option value="">Please Select...</option>
                                                <option value="Ethernet(10/100)" <?php if ($rep['connectivity_interface'] == 'Ethernet(10/100)')   echo   'selected'; ?> >Ethernet(10/100)</option>
                                                <option value="Ethernet(10/100/1000)" <?php if ($rep['connectivity_interface'] == 'Ethernet(10/100/1000)')   echo   'selected'; ?> >Ethernet(10/100/1000)</option>
                                                <option value="GigE-Optical-SM" <?php if ($rep['connectivity_interface'] == 'GigE-Optical-SM')   echo   'selected'; ?> >GigE-Optical-SM</option>
                                                <option value="GigE-Optical-MM" <?php if ($rep['connectivity_interface'] == 'GigE-Optical-MM')   echo   'selected'; ?> >GigE-Optical-MM</option>
                                                <option value="STM-1 POS" <?php if ($rep['connectivity_interface'] == 'STM-1 POS')   echo   'selected'; ?> >STM-1 POS</option>
                                                <option value="STM-4 POS" <?php if ($rep['connectivity_interface'] == 'STM-4 POS')   echo   'selected'; ?> >STM-4 POS</option>
                                                <option value="STM-16 POS" <?php if ($rep['connectivity_interface'] == 'STM-16 POS')   echo   'selected'; ?> >STM-16 POS</option>
                                                <option value="STM-64 POS" <?php if ($rep['connectivity_interface'] == 'STM-64 POS')   echo   'selected'; ?> >STM-64 POS</option>
                                                <option value="TENGE-XFP" <?php if ($rep['connectivity_interface'] == 'TENGE-XFP')   echo   'selected'; ?> >TENGE-XFP</option>
                                                <option value="DS-3" <?php if ($rep['connectivity_interface'] == 'DS-3')   echo   'selected'; ?> >DS-3</option>
                                                <option value="E1" <?php if ($rep['connectivity_interface'] == 'E1')   echo   'selected'; ?> >E1</option>
                                                <option value="Other" <?php if ($rep['connectivity_interface'] == 'Other')   echo   'selected'; ?> >Other</option>
                                            </select>
                                            &nbsp;<span class="error"><?php   echo   $errors['connectivity_interface']; ?></span></td>
                                        <td>Fiber Type:</td>
                                        <td><input  type="text" name="order_fiber_type" value="<?php   echo   $rep['order_fiber_type']; ?>" required>
                                            &nbsp;<span class="error"><?php   echo   $errors['order_fiber_type']; ?></span></td>
                                    </tr>
                                </table>
                                <br>
                                <br>
                                IP Details For Global Announcement:
                                <br><textarea name="order_ip_details_for_global" rows="4" cols="70" required>
                                    <?php   echo   $rep['order_ip_details_for_global']; ?>
                                </textarea>
                                &nbsp;<span class="error"><?php   echo   $errors['order_ip_details_for_global']; ?></span>
                                <br>
                                <br>
                                Special Routing Comments:
                                <br><textarea name="order_special_routing_comments" rows="4" cols="70">
                                    <?php   echo   $rep['order_special_routing_comments']; ?>
                                </textarea>
                                <br>
                                <br>
                                <br>

                            </div> <!-- END id="create_order_technical_info" -->
                        </div> <!-- END id="customer_end_details" -->
            
                        <br>
                        <br>
                        <div id="order_billing_info">
                            <h1>BILLING INFORMATION</h1>
                            <hr>
                            <br>
                            <table id="billing_info">
                                <tr>
                                    <td>Total Non Recurring Charges:</td>
                                    <td><input  type="text" name="order_billing_total_non_recurring_charges" value="<?php   echo   $rep['order_billing_total_non_recurring_charges']; ?>" >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Monthly Recurring Charges:</td>
                                    <td><input  type="text" name="order_billing_total_monthly_recurring_charges" value="<?php   echo   $rep['order_billing_total_monthly_recurring_charges']; ?>" >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hardware Charges:</td>
                                    <td><input  type="text" name="order_billing_hw_charges" value="<?php   echo   $rep['order_billing_hw_charges']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Misc Charges:</td>
                                    <td><input  type="text" name="order_billing_misc_charges" value="<?php   echo   $rep['order_billing_misc_charges']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Special Discount:</td>
                                    <td><input  type="text" name="order_billing_special_discount" value="<?php   echo   $rep['order_billing_special_discount']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>VAT/TAX:</td>
                                    <td><input  type="text" name="order_billing_vat_or_tax" value="<?php   echo   $rep['order_billing_vat_or_tax']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Deposit:</td>
                                    <td><input  type="text" name="order_billing_deposit" value="<?php   echo   $rep['order_billing_deposit']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Payable with SOF:</td>
                                    <td><input  type="text" name="order_billing_total_payable_with_sof" value="<?php   echo   $rep['order_billing_total_payable_with_sof']; ?>">
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <br>
                            Special Requests(if any):
                            <br><textarea name="order_special_requests_if_any" rows="4" cols="70"><?php   echo   $rep['order_special_requests_if_any']; ?></textarea>
                            <br>
                        </div> <!-- END id="order_billing_info" -->
                        <br>

                    </div> <!-- END id="order_details" -->

                    <br>
                    <br>
                    <div id="terms_and_final">
                        <h3 allign="left">Remarks:</h3>
                        <br>
                        <ul>
                            <li>
                                All Charges must be payable in favor of 1Asia Alliance Communication Ltd through Cheque/ DD/TT/PO forms only
                            </li>

                            <li>
                                We hereby undertake that we will not use the IP Bandwidth  for further distribution for commercial purpose  other than our 
                                license scope  and in case of any violation or regulatory issue then  1ASIA  Alliance has the full rights  to decommission the Link 
                                including any other legal remedy
                            </li>

                            <li>
                                No encryption shall be allowed for the IP  Bandwidth use  within the bandwidth provided. In case encryption is required, details 
                                will have to be furnished and monitoring will have to be provided as per the prevailing licensing and regulatory conditions
                            </li>

                            <li>
                                The minimum period of this service will be one-year, calculated to commence on the date of Request for service in SOF
                            </li>

                            <li>
                                In any case if the supplied bandwidth used for illegal VoIP service then ordering party will be fully liable for any legal action 
                                taken against them and in any condition 1Asia Alliance is not responsible for illegal use of Bandwidth
                            </li>

                            <li>
                                During operation if any suspected IP address  blocked by our system admin or regulatory demand then 1Asia Alliance is not 
                                responsible for any business loss.  1Asia Alliance will not be  liable to restore the IP  Service  unless regulator  given the written 
                                consent to restore partial or full service for further reactivation.
                            </li>

                            <li>
                                Any upgrade/downgrade request must need to submit as per 1Asia Alliance standard upgrade/downgrade form through web-portal.
                            </li>

                            <li>
                                Any service abuse is fully responsible by customer itself.
                            </li>

                            <li>
                                No International VPN is allowed through IP Bandwidth unless customer have written permission from BTRC.  
                            </li>

                            <li>
                                Customer must supply the Telecommunication services  License along with this SOF,  Certificate of Incorporation  and VAT 
                                Registration Certificate. 
                            </li>
                        </ul>
                        <br>
                        <br>
                        <table width="100%">
                            <tr>
                                <td>Applicants Name:</td>
                                <td><input type="text" name="applicants_name" value="<?php   echo   $rep['applicants_name']; ?>" required>
                                    &nbsp;<span class="error"><?php   echo   $errors['applicants_name']; ?></span></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Designation:</td>
                                <td>
                                    <input type="text" name="applicants_designation" value="<?php   echo   $rep['applicants_designation']; ?>" required>
                                    &nbsp;<span class="error"><?php   echo   $errors['applicants_designation']; ?></span>
                                </td>
                                <td>
                                    Date: <input type="text" class="datepicker" name="application_date" value="<?php   echo   $rep['application_date']; ?>" placeholder="click to select" required>
                                    &nbsp;<span class="error"><?php   echo   $errors['application_date']; ?></span>
                                </td>
                                <td><span style="margin-left: auto">Signature with Company Seal</span></td>
                            </tr>
                        </table>

                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>

                    </div> <!-- END id="terms_and_final" -->

                    <?php if (($do == 'staff_view') && (is_object($order) && $order->Pending())) { ?>
                        <input class="button" type="submit" name="accept" value="accept" 
                               onClick=' return confirm("Are you sure you want to mark selected order accepted?");'>
                        <input class="button" type="submit" name="reject" value="reject" 
                               onClick=' return confirm("Are you sure you want to reject selected order?");'>
                        <button class="button" type="button" name="cancel" 
                               onClick='window.location.href="./orders.php"'>cancel</button>
                        
                        <!-- staff can update order without filling required fields -->
                        <script type="text/javascript">
                            $('div#order_full_form input, div#order_full_form select').prop('required', false);
                        </script>
                     <?php } ?>

                    <?php if ($do == 'client_view' && is_object($order) && $order->client_can_cancel()) { ?>
                        <button class="button" type="button" name="cancel" value="cancel" 
                               onClick=' return confirm("Are you sure you want to cancel selected order?");'>cancel</button>
                           <?php } ?>

                    <?php if ( ($do == 'create_by_client') || ($do == 'new_by_scp_staff') ) { ?>
                        <div id="order_form_submit">
                            <button class="button" type="submit" name="submit_order">Submit Order</button>
                            
                            <?php if ($do == 'new_by_scp_staff') { ?>
                                <script type="text/javascript">
                                    $('input, select').prop('required', false);
                                </script>
                                <button class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="./orders.php"'>cancel</button>
                            <?php } ?>
                            
                        </div>
                        <script type="text/javascript">
                            $('input.button').css({'width':'100px'});
                            $('input.button').not('[name="submit_order"]').css('margin-left','50px')
                        </script>
                    <?php } ?>
                </form>
                <!--
            <button type="button">DEBUG-autofill</button>
            <script type="text/javascript" src="./styles/test.js"></script>
                -->

                </div> <!-- END id="order_full_form"-->
                <?php if ( $thisuser->isClient() ) { ?>
                <script type="text/javascript" src="./js/order.js"></script>
                <?php } else { ?>
                <script type="text/javascript" src="../js/order.js"></script>
                <?php } ?>