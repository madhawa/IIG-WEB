<?php

/*
require('staff.inc.php');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

require_once(CLASS_DIR . 'class.service.php');


$client_id = htmlspecialchars($_GET['client_id']);
$up_field_name = "";
switch ($_GET['field']) {
    case 'iplc':
        $up_field_name = 'iplc_ckt';
        break;
    case 'mpls_primary':
        $up_field_name = 'mpls_prim_ckt';
        break;
    case 'mpls_secondary':
        $up_field_name = 'mpls_sec_ckt';
        break;
    case 'mpls_tertiary':
        $up_field_name = 'mpls_tert_ckt';
        break;
}
*/

//we should not exceed php.ini max file size, return into bytes
function get_ini_limit() {
    $ini_maxsize = ini_get('upload_max_filesize');
    if (!is_numeric($ini_maxsize)) {
        if (strpos($ini_maxsize, 'M') !== false)
            $ini_maxsize = intval($ini_maxsize) * 1024 * 1024;
        elseif (strpos($ini_maxsize, 'K') !== false)
            $ini_maxsize = intval($ini_maxsize) * 1024;
        elseif (strpos($ini_maxsize, 'G') !== false)
            $ini_maxsize = intval($ini_maxsize) * 1024 * 1024 * 1024;
    }
    return $ini_maxsize;
}

//parameter is the input field name in the upload form
function upload($post_data) {
    $errors = '';
    global $thisuser;
    $date = date('Y-m-d@H-i-s');
    $uploaddir = UPLOAD_DIR . 'ckt_diag';
    $field = 'diag_file';
    $max_size = htmlspecialchars($post_data['maximum_size']);
    $type = htmlspecialchars($post_data['diag_type']);
    $client_id = htmlspecialchars($post_data['upload_client_id']);
    $staff_id = $thisuser->getId();
    $ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_FILES[$field]['tmp_name'])) {
        $file_ext = pathinfo(basename($_FILES[$field]['name']), PATHINFO_EXTENSION);
        $new_filename = $client_id . '_' . $type . '_' . $date . '.' . $file_ext;
        $new_file_path = $uploaddir . '/' . $new_filename;

        if (!is_writable($uploaddir)) {
            $errors = 'upload directory is not writable ';
            Sys::log(LOG_WARNING, 'missing permissions', ' upload directory ' . $uploaddir . ' is not writable, fix permission');
        }

        if (!$errors && $_FILES[$field]['error'] != UPLOAD_ERR_OK) {
            $errors = "error! try again ";
        }

        if (!$errors && $_FILES[$field]['size'] > $max_size) {
            $errors = " filesize exceeds allowed limit " . ($max_size / 1024) . " KiloBytes";
        }

        if (!$errors && !move_uploaded_file($_FILES[$field]['tmp_name'], $new_file_path)) {
            $errors = "bad file upload!";
            Sys::log(LOG_ALERT, 'bad file upload', 'bad file upload attempt by staff ' . $staff_id . ' from ip:' . $ip . ' ,more info::server tmp file name ' . $_FILES[$field]['tmp_name'] . ', client side file name ' . $_FILES[$field]['name']);
        }
        if (!$errors) {
            $sql = '';
            switch ($type) {
                case 'iplc_ckt':
                    $sql = ' SET updated=NOW()' .
                            ',service_name=' . db_input('iplc') .
                            ',iplc_fields_circuit_diagram=' . db_input($new_filename);
                    break;
                case 'mpls_prim_ckt':
                    $sql = ' SET updated=NOW()' .
                            ',service_name=' . db_input('mpls') .
                            ',mpls_fields_primary_circuit_diagram=' . db_input($new_filename);
                    break;
                case 'mpls_sec_ckt':
                    $sql = ' SET updated=NOW()' .
                            ',service_name=' . db_input('mpls') .
                            ',mpls_fields_secondary_circuit_diagram=' . db_input($new_filename);
                    break;
                case 'mpls_tert_ckt':
                    $sql = ' SET updated=NOW()' .
                            ',service_name=' . db_input('mpls') .
                            ',mpls_fields_tertiary_circuit_diagram=' . db_input($new_filename);
                    break;
            }

            if (($ser = new Services($client_id)) && $ser->getId()) {
                $sql = 'UPDATE ' . ADDED_SERVICES_TABLE . $sql . ' WHERE client_id=' . db_input($client_id);
                //  echo   $sql;
            }
            else
                $sql = 'INSERT INTO ' . ADDED_SERVICES_TABLE . $sql . ',client_id=' . db_input($client_id);

            if (!db_query($sql) || !db_affected_rows()) {
                $errors = 'error in db query';
                unlink($new_file_path);
            }
        }
    }

    if (!$errors)
        return 1;
    else
        return $errors;
}

if ($_POST) {
    $upload_result = upload($_POST);
    if ($upload_result == 1)
        $up_report = 'success';
    else
        $up_report = $upload_result;
}
?>


<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                var diag_type = $('input[name="diag_type"]').val();

                var notf_to_remove = 'div#notification p#' + diag_type;
                if (top.$(notf_to_remove).length > 0) {
                    top.$(notf_to_remove).append('<b><?php   echo   $up_report; ?></b>');
                }
                setTimeout(function() {
                    top.$(notf_to_remove).remove();
                }, 6000);

                var notf_str = '';
                switch (diag_type) {
                    case 'iplc_ckt':
                        notf_str = 'uploading iplc circuit diagram......';
                        break;
                    case 'mpls_prim_ckt':
                        notf_str = 'uploading mpls primary circuit diagram......';
                        break;
                    case 'mpls_sec_ckt':
                        notf_str = 'uploading mpls secondary circuit diagram......';
                        break;
                    case 'mpls_tert_ckt':
                        notf_str = 'uploading mpls tertiary circuit diagram......';
                        break;
                }


                $('form.diag_upload_form').submit(function() {
                    top.$('div#notification').css('display', 'block');
                    top.$('div#notification').append('<p id="' + diag_type + '" class="up_notf">' + notf_str + '</p>');
                    top.$('div#notification p.up_notf').css({
                        'font-size': '150%',
                        'border-style': 'solid',
                        'padding': '2px'
                    });
                });


                setTimeout("$('div#infomessage').hide(1000);", 6000);
                $('div#infomessage').click(function() {
                    $(this).css('display', 'none');
                });
            });
        </script>
        <link rel="stylesheet" href="css/upload.css" type="text/css" media="screen"/>
    </head>


    <body>
        <?php if ($up_report) { ?>
            <div id="infomessage">
                <p><?php   echo   $up_report; ?></p>
            </div>
        <?php } ?>


        <form class="diag_upload_form" enctype="multipart/form-data" action="upload.php?field=<?php   echo   htmlspecialchars($_GET['field']) . '&client_id=' . $client_id ?>" method="post">
            <input type="hidden" name="upload_client_id" value="<?php   echo   $client_id ?>" />
            <input type="hidden" name="maximum_size" value="<?php   echo   get_ini_limit(); ?>" />
            <input type="hidden" name="diag_type" value="<?php   echo   $up_field_name ?>" />
            <input type="file" name="diag_file" />
            <input type="submit" name="upload_ckt_diag" value="upload" />
        </form>
    </body>
</html>