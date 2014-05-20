$(document).ready(function() {
$('div#order_full_form input').each(function() {
    if ( $(this).prop('readonly') ) {
        $(this).css('border', '0');
    }
});

//if edit order buton clicked
$('button[name="edit_order"]').click(function(event) {
    $('div#order_full_form input').prop('readonly', false);
    $('div#order_full_form input').css('border', '');
    $('<input class="button" type="submit" name="submit_order" value="update order" />').insertBefore($('input[name="accept"]'));
    $('[name="submit_order"]').css('margin-right', '30px');
});
$('input[name="order_technical_contact_phone"]').parent('td').css('width','200px'); //dirty way to format
    var order = {
        field_error_css:
                {'border': '1px red', 'outline': 'none', 'box-shadow': '0 0 10px red'},
        field_error_css_undo:
                {'border': '', 'outline': '', 'box-shadow': ''},
        customer_rel_no:
                {type: 'text', required: true, html_attr: 'input'},
        customer_name:
                {type: 'name', required: true, html_attr: 'input'},
        customer_email:
                {type: 'email', required: true, html_attr: 'input'},
        customer_type:
                {type: 'text', required: true, html_attr: 'select'},
        service_type:
                {type: 'text', required: true, html_attr: 'select'},
        circuit_type:
                {type: 'text', required: true, html_attr: 'select'},
        order_creator_name:
                {type: 'name', required: true, html_attr: 'input'},
        order_creator_designation:
                {type: 'text', required: true, html_attr: 'input'},
        order_creator_dept_name:
                {type: 'text', required: true, html_attr: 'input'},
        order_creator_address:
                {type: 'address', required: true, html_attr: 'input'},
        order_creator_city:
                {type: 'text', required: true, html_attr: 'input'},
        order_creator_zip_or_po:
                {type: 'zip', required: true, html_attr: 'input'},
        order_creator_country:
                {type: 'text', required: true, html_attr: 'input'},
        order_creator_office_phone:
                {type: 'phone', required: true, html_attr: 'input'},
        order_creator_fax:
                {type: 'fax', required: true, html_attr: 'input'},
        order_creator_mobile:
                {type: 'mobile', required: true, html_attr: 'input'},
        order_creator_service_ready_date:
                {type: 'date', required: true, html_attr: 'input'},
        order_customer_name:
                {type: 'names', required: true, html_attr: 'input'},
        order_customer_designation:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_dept_name:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_address:
                {type: 'address', required: true, html_attr: 'input'},
        order_customer_city:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_zip_or_po:
                {type: 'zip', required: true, html_attr: 'input'},
        order_customer_country:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_phone_office:
                {type: 'phone', required: true, html_attr: 'input'},
        order_customer_fax:
                {type: 'fax', required: true, html_attr: 'input'},
        order_customer_mobile:
                {type: 'mobile', required: true, html_attr: 'input'},
        order_customer_backhaul_provider:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_backhaul_responsibility:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_equipment_to_be_used:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_equipment_others:
                {type: 'text', required: false, html_attr: 'input'},
        order_customer_equipment_name:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_equipment_model:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_equipment_vendor:
                {type: 'text', required: true, html_attr: 'input'},
        order_customer_connectivity_interface:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_connectivity_interface_others:
                {type: 'text', required: false, html_attr: 'input'},
        order_customer_protocol_to_be_used:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_protocol_others:
                {type: 'text', required: false, html_attr: 'input'},
        order_customer_connectivity_capacity:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_connectivity_capacity_others:
                {type: 'text', required: false, html_attr: 'input'},
        order_customer_special_ins:
                {type: 'text', required: false, html_attr: 'textarea'},
        order_technical_contact_name:
                {type: 'name', required: true, html_attr: 'input'},
        order_technical_contact_mobile:
                {type: 'mobile', required: true, html_attr: 'input'},
        order_technical_contact_phone:
                {type: 'phone', required: true, html_attr: 'input'},
        order_technical_contact_email:
                {type: 'email', required: true, html_attr: 'input'},
        order_technical_contact_messengers:
                {type: 'im', required: true, html_attr: 'input'},
        order_routing_type:
                {type: 'text', required: true, html_attr: 'select'},
        order_customer_as_sys_name:
                {type: 'name', required: true, html_attr: 'input'},
        order_customer_as_sys_num:
                {type: 'number', required: true, html_attr: 'input'},
        order_customer_as_set_num:
                {type: 'number', required: true, html_attr: 'input'},
        order_bgp_routing:
                {type: 'text', required: true, html_attr: 'select'},
        order_router_name:
                {type: 'text', required: true, html_attr: 'input'},
        order_bw_speed_cir:
                {type: 'float', required: true, html_attr: 'input'},
        order_max_burstable_limit:
                {type: 'float', required: true, html_attr: 'input'},
        connectivity_interface:
                {type: 'text', required: true, html_attr: 'select'},
        order_fiber_type:
                {type: 'text', required: true, html_attr: 'input'},
        order_ip_details_for_global:
                {type: 'text', required: true, html_attr: 'textarea'},
        order_special_routing_comments:
                {type: 'text', required: false, html_attr: 'textarea'},
        order_billing_total_non_recurring_charges:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_total_monthly_recurring_charges:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_hw_charges:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_misc_charges:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_special_discount:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_vat_or_tax:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_deposit:
                {type: 'bill', required: true, html_attr: 'input'},
        order_billing_total_payable_with_sof:
                {type: 'bill', required: true, html_attr: 'input'},
        order_special_requests_if_any:
                {type: 'text', required: false, html_attr: 'input'},
        applicants_name:
                {type: 'name', required: true, html_attr: 'input'},
        applicants_designation:
                {type: 'text', required: true, html_attr: 'input'},
        application_date:
                {type: 'date', required: true, html_attr: 'input'},
        applicants_sig:
                {type: 'text', required: false, html_attr: 'input'}
    };

    function validate_email(email){
        var x=email.indexOf('@');
        var y=email.lastIndexOf('.');

        if(x===-1 || y===-1 || (x+2)>=y){
            return false;
        }
        else{
            return true;
        }
    }

    $('div#order_full_form input,select,textarea').on('change', function(change_event) {
        var field_name = $(change_event.target).attr('name');
        var field_value = $(change_event.target).val();
        if ( order[field_name] && (field_value.length === 0) ) {
            //field empty
            var name_selector = order[field_name].html_attr + '[name="' + field_name + '"]';
            //$(name_selector).css(order.field_error_css);
        } else {
            //not empty
            if ( order[field_name] && field_value.length>0 ) {
                var name_selector = order[field_name].html_attr + '[name="' + field_name + '"]';
                //$(name_selector).css(order.field_error_css_undo);
                
                switch (order[field_name].type) {
                    case 'email':
                        if ( !validate_email(field_value) ) {
                            $(name_selector).css(order.field_error_css);
                            alert('email not valid');
                            //change_event.preventDefault();
                        }
                        break;
                    case 'zip':
                        if ( $.isNumeric(field_value) ) {
                            if ( field_value.length<4 ) {
                                $(name_selector).css(order.field_error_css);
                                alert('zip code should be at least four digit');
                            }
                        } else {
                            $(name_selector).css(order.field_error_css);
                            alert('zip code should be numeric');
                            change_event.preventDefault();
                        }
                        break;
                    case 'phone':
                    case 'fax':
                    case 'mobile':
                        if ( !$.isNumeric(field_value) ) {
                            $(name_selector).css(order.field_error_css);
                            alert('number not valid');
                        }
                        break;
                }
            }
        }
    });

/*
    $('input[name="submit_order"]').click(function(click_event) {
        var req_field_empty = true;
        var number_of_empty_req_fields = 0;
        console.group('order form validation');

        $('div#order_full_form input,select,textarea').each(function() {
            var field_name = $(this).attr('name');
            if (order[field_name] !== undefined) {
                var name_selector = order[field_name].html_attr + '[name="' + field_name + '"]';
                var val = $(this).val();

                console.group('now analyzing ' + field_name);
                if (order[field_name].required) {
                    if (!val) {
                        number_of_empty_req_fields++;
                        $(name_selector).css(order.field_error_css);
                        req_field_empty = true;
                    } else {
                        $(name_selector).css(order.field_error_css_undo);
                    }
                    val ? console.log('field value:' + val) : console.log('field is required but empty');
                }
                console.groupEnd();
            }
        });

        if (req_field_empty) {
            click_event.preventDefault();
            (number_of_empty_req_fields > 1) ? alert('Error: some of the required fields are empty') : alert('Error: required field empty');
        }
        console.groupEnd();
    });
*/
});
