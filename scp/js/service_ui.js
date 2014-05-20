$('li#iig').click(function(event) {
    $('div#iig_div, div#itc_div, div#inhouse_div, div#co-loc_div').hide();
    $('div#iig_div').show();
});

$('li#itc').click(function(event) {
    $('div#iig_div, div#itc_div, div#inhouse_div, div#co-loc_div').hide();
    $('div#itc_div').show();
});

$('li#inhouse').click(function(event) {
    $('div#iig_div, div#itc_div, div#inhouse_div, div#co-loc_div').hide();
    $('div#inhouse_div').show();
});

$('li#co-loc').click(function(event) {
    $('div#iig_div, div#itc_div, div#inhouse_div, div#co-loc_div').hide();
    $('div#co-loc_div').show();
});



$('a#cap_add_a').click(function(event) {
    $('div#inhouse_cap_add_div, div#inhouse_active_div, div#inhouse_disc_div').hide();
    $('div#inhouse_cap_add_div').show();
});
$('a#active_a').click(function(event) {
    $('div#inhouse_cap_add_div, div#inhouse_active_div, div#inhouse_disc_div').hide();
    $('div#inhouse_active_div').show();
});
$('a#discontinued_a').click(function(event) {
    $('div#inhouse_cap_add_div, div#inhouse_active_div, div#inhouse_disc_div').hide();
    $('div#inhouse_disc_div').show();
});