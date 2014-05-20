//search client
//oh baby, edit database loaded data
$('div#loaded').on('click', '[name="edit_service"]', function(event) {
    $(event.target).hide();
    $(event.target).parent('div.each_other_type_div').find('div.service_info').hide();
    $(event.target).parent('div.each_other_type_div').find('div.service_input').show();
});
//baby! now remove each data div box
$('div#other_type_container_div').on('click', '[name="delete_service"]', function(event) {
    $(event.target).parent('div.each_other_type_div').remove();
});
var data = 'service type<br><select name="service_type[]" required>\
                                <option value="">Please Select</option>\
                                                        <option value="only IP Transit" >only IP Transit</option>\
                                                                                <option value="IP Bandwidth" >IP Bandwidth</option>\
                                                                                                        <option value="IP transit + IPLC[Full Circuit]" >IP transit + IPLC[Full Circuit]</option>\
                                                                                                                                <option value="P transit + IPLC[half Circuit]" >IP transit + IPLC[half Circuit]</option>\
                                                                                                                                                        <option value="IPLC[Half Circuit]" >IPLC[Half Circuit]</option>\
                                                                                                                                                                                <option value="IPLC[Full Circuit]" >IPLC[Full Circuit]</option>\
                                                                                                                                                                                                        <option value="Global MPLS" >Global MPLS</option>\
                                                                                                                                                                                                                                <option value="Internartional Ethernet" >Internartional Ethernet</option>\
                                                                                                                                                                                                                                                    </select>';
data = data + '<br>Circuit type<br><select name="circuit_type[]" required>\
                        <option value="">Please Select</option>\
                                                    <option value="Half-Circuit" >Half-Circuit</option>\
                                                                                <option value="Full-Circuit" >Full-Circuit</option>\
                                                                                                            <option value="OSS">OSS</option>\
                                                                                                                                        <option value="Partial" >Partial</option>\
                                                                                                                                                                </select>';
data = data + '<br>Circuit ID (CIN):<br><input class="other_type" type="text" name="cin_no[]" value="" required>';
data = data + '<br>Circuit DIagram:<br><input class="other_type" type="file" name="ckt_diag[]" value="">';
data = data + '<br>From:<br><input type="text" name="from[]" value="">';
data = data + '<br>To:<br><input type="text" name="to[]" value="">';
data = data + '<br>Link details:<br><input type="text" name="link_details[]" value="">';
data = data + '<br>bandwidth speed(CIR):<br><input type="text" name="bw_speed_cir[]" value=""> &nbsp; Mbps &nbsp;&nbsp;<br>Max Burstable Limit:<br><input type="text" name="max_burstable_limit" value="">&nbsp;Mbps';
data = '<div class="each_other_type_div"><button class="save button" type="button" name="delete_service">delete</button><br>' + data + '</div>';

$('button[name="add_other_type"]').click(function() {
    $('div#other_type_container_div').append(data);
});
$('button[name="remove_other_type"]').on('click', function(event) {
    $('[name="remove_cin"]').val('remove_cin');
    $('div#other_type_container_div div:last-child').remove();
});



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



$('[name="show_pass_buton"]').click(function() {
    var p = $('[name="password"]').val();
    var p_a = $('[name="password_again"]').val();
    alert('password: "' + p + '" password again: "' + p_a + '"');
});
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


// -------------------------------- CSS ----------------------------------

$('table.tform input:not([type="submit"])').css('width', '300px');
$('table.tform select').css('width', '300px');
$('table.tform th').css('font-size', '1.3em');
$('table.tform th').css('font-weight', 'bold');
$('table.tform').css('border', '0');
$('table.tform th').css('border', '0');
$('table.tform th').css('background-color', 'inherit');
$('table.tform td').css('border', '0');

function go_there(url) {
    window.location.href = url;
}