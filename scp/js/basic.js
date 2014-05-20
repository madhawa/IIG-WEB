/**
this script contains some basic functions and objects needed for all pages especially service pages
*/


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
console.group('DEVELOPER INFO');
console.info('developer : Minhaj');
console.info('developer contact # email:[ polarglow06@gmail.com, minhaj@vimmaniac.com ] skype:[ minhaj_vimmaniac ]');
console.groupEnd();
console.log('*****************************************************************');
console.log('*****************************************************************');
console.log('*****************************************************************');



function is_object(obj) {
    if (typeof(obj) === "object") {
        return true;
    } else {
        return false;
    }
}
function is_string(thing) {
    if (typeof(thing) === "string") {
        return  true;
    } else {
        return false;
    }
}
function is_number(thing) {
    if (typeof(thing) === "number") {
        return true;
    } else {
        return false;
    }
}
function is_array(thing) {
    if ( typeof(thing) == "array" || is_object(thing) ) {
        return true;
    } else {
        return false;
    }
}
function uniqid(prefix) {
    // Math.random should be unique because of its seeding algorithm.
    // Convert it to base 36 (numbers + letters), and grab the first 9 characters
    // after the decimal.
    if (prefix && !is_string(prefix)) {
        prefix = 'uid_';
    } else if (!prefix) {
        prefix = 'uid_';
    }

    return prefix + Math.random().toString(36).substr(2, 9);
}

function gen_pass(len) {
    return Math.random().toString(36).substr(2, len);
}

function valid_email(email){
    var x=email.indexOf('@');
    var y=email.lastIndexOf('.');

    if(x===-1 || y===-1 || (x+2)>=y){
        return false;
    }
    else{
        return true;
    }
}





var s_style = {}; //special style
s_style.error_field_css = {
    'border': '2px solid red',
    'outline': 'none',
    'box-shadow': '0 0 10px red'
};
s_style.undo_error_field_css = {
    'border': '',
    'outline': '',
    'box-shadow': ''
};

s_style.this_client_port_css = {
    'outline': '2px solid green'
};
s_style.port_css_undo = {
    'outline': ''
};
s_style.other_client_port_css = {
    'outline': '2px solid #FF00FF'
};
s_style.error_port_css = {
    'outline': '5px solid red'
};
s_style.odf_cross_link_css =  {
	'outline': '3px solid'
}




var buttons = {};
buttons.notf_button_html =
        '<div id="show_notf_button">\
    notifications\
    <span>\
    </span>\
    </div>'
        ;

buttons.port_mapping_button_html =
        '<div id="show_port_mapping">\
    odf port mapping\
    </div>';
