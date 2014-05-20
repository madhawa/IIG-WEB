$('th, td').css('padding', '20px');

$('span#odf_port_mapping_spanbutton').css('padding', '10px');
//$('span#odf_port_mapping_spanbutton').draggable();

$('span#odf_port_mapping_spanbutton').click(function(event) {
    console.info('showing odf port mapping modal window');
    $('div#odf_mapping').empty();
    $('div#odf_mapping').css('background-color', '#FF99FF');
    
    var target_div_id = add_odf('odf_mapping'); //pushing html to the specified container
    var div_selector = 'div#'+target_div_id;
    var odf_name = $(div_selector).find('select.odf_name').val();
    if ( odf_name ) {
        apply_db_odf_to_target(odf_name, target_div_id);
    }
    
    
    $('span.odf_tray_ports').css({'background-color': 'black'}); // tray ports block
    //$('span.odf_tray_ports span.ports_span').css();
    $('span.tray_span').css({'border': '1px solid', 'padding': '2 px'}); // tray
    
    $('span.ports_span').css('border', '0'); // ports container
    $('span.ports_span>span').css( {'border': '1px solid', 'padding': '2px', 'margin': '5px'} ); // each port box
    
    $('span#tray_a span').not('span.ports_span').css('background-color', '#6699FF');
    $('span#tray_b span').not('span.ports_span').css('background-color', '#009999');
    $('span#tray_c span').not('span.ports_span').css('background-color', '#99CCFF');
    $('span#tray_d span').not('span.ports_span').css('background-color', '#CC9900');
    
    //$('span.ports_span>span').draggable();
    
    
    $('div#odf_mapping').dialog('open');
});