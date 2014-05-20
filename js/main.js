$(document).ready(function() {
    /**
     * Protect window.console method calls, e.g. console is not defined on IE
     * unless dev tools are open, and IE doesn't define console.debug
     */
    (function() {
        if (!window.console) {
            window.console = {};
        }
        // union of Chrome, FF, IE, and Safari console methods
        var m = [
            "log", "info", "warn", "error", "debug", "trace", "dir", "group",
            "groupCollapsed", "groupEnd", "time", "timeEnd", "profile", "profileEnd",
            "dirxml", "assert", "count", "markTimeline", "timeStamp", "clear"
        ];
        // define undefined methods as noops to prevent errors
        for (var i = 0; i < m.length; i++) {
            if (!window.console[m[i]]) {
                window.console[m[i]] = function() {
                };
            }
        }
    })();
    console.log('*****************************************************************');
    console.log('*****************************************************************');
    console.info('this complete system(front-end, backend) developer is Minhajul Anwar.');
    console.info('developer contact # email:[ polarglow06@gmail.com, minhaj@vimmaniac.com ] skype:[ minhaj_vimmaniac, minu273 ]');
    console.info('phone: 01719910365, 01534303074');
    console.log('*****************************************************************');
    console.log('*****************************************************************');
    console.log('*****************************************************************');
    
    
    $('noscript').hide();
    
    
    //notification manager
    var notf_man = {
        notfs: {},
        container: 'div#notification'
    };
    notf_man.notf_container_show = function() {
        $(this.container).css('display', 'block');
    };
    notf_man.notf_container_hide = function() {
        $(this.container).hide(1000);
    };
    notf_man.empty_notf_container = function() {
        $(this.container).empty();
    };
    notf_man.push = function(thing) {
        notf_man.notf_container_show();
        $(this.container).append(thing);
    };
    notf_man.remove = function(id_or_class) {
        var class_selector = '.'+id_or_class;
        var selected_class = $(this.container).find(class_selector);
        
        var id_selector = '#'+id_or_class;
        var selected_id = $(this.container).find(id_selector);
        
        if ( selected_class.length > 0 ) {
            selected_class.remove();
        }
        if ( selected_id.length > 0 ) {
            selected_id.remove();
        }
    };
    //notification hide on click
    $('div#notification').click(function() {
        $(this).hide(1000);
    });
    setTimeout(function() {
        if ($('div#notification').length === 0)
            $('div#notification').hide(1000);
    }, 4000);
    //END notification manager
    
    
    //login page
    $('div#loginBox input[type="submit"]').click(function(event) {
        var uid = $('div#loginBox input[name="login_field"]').val();
        var pass = $('div#loginBox input[type="password"]').val();
        if ( !uid || !pass ) {
            return false;
        }
    });
 
 
    if ( !$('input[type="password"]').val() ) {
        $('button.show_pass_buton').hide();
    }
    $('input[name="client_password"]').change(function(event) {
        if ( $(event.target).val() ) {
            $('button.show_pass_buton').show();
        } else {
            $('button.show_pass_buton').hide();
        }
    });
    
    
    var current_user = $('div#current_user [name="client_id"]').val();//boss
    //$('div#add_staff_for_this_client input[name="do"]').val('create');
    
    
    $('button[name="add_staff_for_client"]').click(function(event) {
        $('button[name="add_staff_for_client"]').hide();
        
        if ( $('div.each_client_staff_div').is(':visible') ) {
            $('span#view_all_client_staff_spanbutton').text('view all');
            $('span#view_all_client_staff_spanbutton').css('color', 'green');
            $('span#view_all_client_staff_spanbutton').css('background-image', 'url("../../images/expand.png")');
            $('div.each_client_staff_div').hide('slow');
        }
        
        $('div#add_staff_for_this_client').show('slow');
        $('button[name="hide_add_staff_form"]').show('slow');
        //$('div#add_staff_for_this_client input[name="do"]').val('create');
        //$('div#add_staff_for_this_client input[name="boss_id"]').val(client_id);
        if ( $('div#add_staff_for_this_client [name="client_staff_id"]').val() ) {
            $('div#client_staff_account_info').show();
        }
    });
    
    $('button[name="hide_add_staff_form"]').click(function(event) {
        $(event.target).hide();
        $('div#add_staff_for_this_client').hide('slow');
        $('button[name="add_staff_for_client"]').show();
    });
    
    
    
    
    if ( !$('div.each_client_staff_div').length ) {
        $('span#view_all_client_staff_spanbutton').hide();
    }
    $('span#view_all_client_staff_spanbutton').click(function(event) {
        if ( !$('div.each_client_staff_div').is(':visible') ) {
            $(event.target).text('hide all');
            $(event.target).css('color', 'red');
            $(event.target).css('background-image', 'url("../../images/contract.png")');
            $('div.each_client_staff_div').show('slow');
        } else {
            $(event.target).text('view all');
            $(event.target).css('color', 'green');
            $(event.target).css('background-image', 'url("../../images/expand.png")');
            $('div.each_client_staff_div').hide('slow');
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
    });
    
    
    
    $('#cssmenu .has-sub').hover(function() {
        $(this).children('ul').css({'background':'#F1F1F1', 'color': '#ffffff', 'z-index':'100'});
        $(this).children('ul').stop(true, true).slideDown('slow');
    }, function() {
        $(this).children('ul').stop(true, true).slideUp('slow');
    });
});