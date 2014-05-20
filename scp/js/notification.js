/* contains the notification framework
    developer: Minhaj
    license: include this whole info and you are free to copy and edit any portion of code
 */

var notf_man = {
    notfs: {},
    parent: 'div#container',
    container: 'div#notification',
    highlighted: false, //highlighted doesn't mean shown, it means whether the container div is shown and every other things on the page are not shown
    loading_indicator: '<img id="loading_gif" src="../images/load.gif" alt="loading">',
    loading: false
};

$(notf_man.container).draggable();



notf_man.operation = function(mode) {
    switch (mode) {
        case 'loading':
        case 'progress':
            if (this.loading === false) {
                this.loading = true;
                this.show_loading(); //spinnig gif
                $(this.parent).css('cursor', 'wait');
                $(this.parent).find('div#connectivity_details button').prop('disabled', true);
                $(this.parent).find('div#connectivity_details input,div#connectivity_details select,div#connectivity_details button').css('cursor', 'not-allowed');
            }
            break;
        case 'reset':
            if (this.loading === true) {
                this.loading = false;
                this.hide_loading(); //spinning gif
                $(this.parent).css('cursor', 'auto');
                $(this.parent).find('div#connectivity_details button').prop('disabled', false);
                $(this.parent).find('div#connectivity_details input,div#connectivity_details select,div#connectivity_details button,div#connectivity_details textarea').css('cursor', 'auto');
            }
            break;
    }

};
notf_man.notf_container_show = function() {
    if (!$(this.container).is(':visible')) {
        $(this.container).show(1000);
    }
};
notf_man.notf_container_hide = function(force_empty_container) {
    if (force_empty_container === true) {
        notf_man.force_empty_notf_container();
    }
    if ($(this.container).is(':visible')) {
        $(this.container).hide('1000');
    }
};
notf_man.force_empty_notf_container = function() {
//$(this.container).empty();
    $(this.container).find('*').not('.close_box, span.notf_clear').remove(); //do not remove the close box!
};
notf_man.push = function(thing, force_empty_container, timeout) {
    /*
     arguments: 
     thing: html formatted string
     force_empty_container : whether to forcefully empty the container div before pushing
     highlight_container : whether to apply notf_man.highlight_notf()
     */
    if (force_empty_container === true) {
        notf_man.force_empty_notf_container();
    }
    notf_man.notf_container_show();
    $(this.container).append(thing);
};
notf_man.show_loading = function() {
    notf_man.push(this.loading_indicator);
}
notf_man.hide_loading = function() {
    $('div#notification #loading_gif').remove();
}
notf_man.remove = function(id_or_class) {
    var class_selector = '.' + id_or_class;
    var selected_class = $(this.container).find(class_selector);
    var id_selector = '#' + id_or_class;
    var selected_id = $(this.container).find(id_selector);
    if (selected_class.length > 0) {
        selected_class.remove();
    }
    if (selected_id.length > 0) {
        selected_id.remove();
    }
};
//notification hide on close box click
$('div#notification .close_box').click(function(event) {
    event.stopPropagation();
    console.info('notification close button clicked');
    if ($(notf_man.container).is(':visible')) {
        notf_man.notf_container_hide();
    }
});
$(notf_man.container).on('click', 'p, span', function(event) {
    event.stopPropagation();
    console.info('remove a notification text');
    $(event.target).hide();
    $(event.target).remove();
});