/*
 developer: MInhajul Anwar
 email: polarglow06@gmail.com
 License info:
 as long as you include my info, you are free to copy any portion of this code
 */

 
    var service_id = $('input[name="service_id"]').val();
    var client_id = $('input[name="client_boss_id"]').val();
    var client_name = $('input[name="client_boss_name"]').val();
    var scp_staff_id = $('input[name="scp_staff_id"]').val();


$(document).ready(function() {

    $('div#add_staff_and_services input').not('[type="checkbox"]').css('height', '30px');
    $('div#add_staff_and_services input[type="checkbox"]').css('position', 'relative');
    $('div#add_staff_and_services input[type="checkbox"]').not('[name="client_type_other"]').css('top', '-10px');

    //to invoke search form and set param
    $('a[href=#search_client]').click(function() {
        $('form#search_client').prepend('<input type="hidden" name="do" value="search">');
        $('div#search_client_div').show();
    });
    $('div#add_staff_and_services').click(function(event) {
        event.stopPropagation();
        $('form#search_client input[name="do"]').remove();
        $('div#search_client_div').hide();
    });
    
    
    
    $('select[name="client_type"]').change(function(event) {
        if ($(event.target).val() == "other") {
            $('#client_type_other').show();
        } else {
            $('#client_type_other').hide();
        }
    });







//asn field and button
    if ($('input[name="client_org_asn"]').val()) {
        $('input[name="client_org_asn"]').show();
        $('button[name="add_asn_button"]').hide();
    } else {
        $('input[name="client_org_asn"]').hide();
        $('button[name="add_asn_button"]').show();
    }



//show password button
    if (!$('input[type="password"]').val()) {
        $('button.show_pass_buton').hide();
    }
    $('input[name="client_password"]').change(function(event) {
        if ($(event.target).val()) {
            $('button.show_pass_buton').show();
        } else {
            $('button.show_pass_buton').hide();
        }
    });
    $('button.show_pass_buton').click(function(event) {
        var pass_field = $('input[name="client_password"],input[name="client_password_again"]');
        var password = pass_field.val();
        if (password) {
            if (pass_field.attr('type') === 'password') {
                pass_field.attr('type', 'text');
                $(event.target).text('hide password');
            } else {
                pass_field.attr('type', 'password');
                $(event.target).text('show password');
            }
        }
    }); //END show password button



//client side staff add new
    if (client_id) {
        $('button[name="add_staff_for_client"]').css('display', 'block');
        $('button[name="add_staff_for_client"]').click(function(event) {
            //$(event.target).hide('slow');
            if ($('div.each_client_staff_div').is(':visible')) {
                $('span#view_all_client_staff_spanbutton').text('view all');
                $('span#view_all_client_staff_spanbutton').css('color', 'green');
                $('span#view_all_client_staff_spanbutton').css('background-image', 'url("./images/expand.png")');
                $('div.each_client_staff_div').hide('slow');
            }

            $('div#add_staff_for_this_client').show('slow');
            $('button[name="hide_add_staff_form"]').show('slow');
            //$('div#add_staff_for_this_client input[name="do"]').val('create');
            $('div#boss_client input[name="do"]').val('update');
            $('div#add_staff_for_this_client input[name="boss_id"]').val(client_id);
            if ($('div#add_staff_for_this_client [name="client_scp_staff_id"]').val()) {
                $('div#client_staff_account_info').show();
            }
        });
        $('button[name="hide_add_staff_form"]').click(function(event) {
            $(event.target).hide();
            $('div#add_staff_for_this_client').hide('slow');
            $('button[name="add_staff_for_client"]').show();
        });
    }



//all client side staffs
    if (!$('div.each_client_staff_div').length) {
        $('span#view_all_client_staff_spanbutton').hide();
    }
    $('span#view_all_client_staff_spanbutton').click(function(event) {
        console.log('showing all staffs');
        if (!$('div.each_client_staff_div').is(':visible')) {
            $(event.target).text('hide all');
            $(event.target).css('color', 'red');
            $(event.target).css('background-image', 'url("./images/contract.png")');
            $('div.each_client_staff_div').show('slow');
        } else {
            $(event.target).text('view all');
            $(event.target).css('color', 'green');
            $(event.target).css('background-image', 'url("./images/expand.png")');
            $('div.each_client_staff_div').hide('slow');
        }
    });

});
