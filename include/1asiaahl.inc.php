<?php
function asiaahl($data, &$errors) {
    define('PARENT_SITE_ROOT_DIR',str_replace('\\\\', '/', $_SERVER['DOCUMENT_ROOT']).'/'); #Get real path for root dir ---linux and windows
    
    $uploaddir = PARENT_SITE_ROOT_DIR . 'gallery_fls';
    $posted_texts = trim($_POST['parent_site_intro_text']);
    //  echo   'posted text:'.$posted_texts;
    $up_images = $_FILES['parent_site_images'];
    
    $sql = 'INSERT INTO '.PARENT_SITE_TABLE.' SET updated=NOW(), texts='.db_input(mysqli_real_escape_string($posted_texts));
    $sql_update = 'UPDATE '.PARENT_SITE_TABLE.' SET updated=NOW(), texts='.db_input(mysqli_real_escape_string($posted_texts)).' WHERE id=1';
    
    //mysql_query($sql) or die(mysql_error());
    //  echo   $sql_update.'<br>';
    if (!db_query($sql_update) || !db_affected_rows()) {
        $errors['err'] = 'text saving failure';
    } else {
        $count = 0;
        foreach ($up_images['name'] as $filename) {
            if (isset($up_images['tmp_name'][$count])) {
                $file_ext = pathinfo(basename($up_images['name'][$count]), PATHINFO_EXTENSION);
                //$new_filename = $client_id . '_' . $type . '_' . $date . '.' . $file_ext;
                $new_file_path = $uploaddir . '/' . basename($up_images['name'][$count]);

                if (!is_writable($uploaddir)) {
                    $errors['err'] = 'upload directory is not writable ';
                    Sys::log(LOG_WARNING, 'missing permissions', ' upload directory ' . $uploaddir . ' is not writable, fix permission');
                }

                if (!$errors['err'] && $up_images['error'][$count] != UPLOAD_ERR_OK) {
                    $errors['err'] = "error! try again ";
                }

                if (!$errors['err'] && $up_images['size'] > $max_size) {
                    $errors['err'] = " filesize exceeds allowed limit " . ($max_size / 1024) . " KiloBytes";
                }

                if (!$errors['err'] && !move_uploaded_file($up_images['tmp_name'][$count], $new_file_path)) {
                    $errors['err'] = "bad file upload!";
                    Sys::log(LOG_ALERT, 'bad file upload', 'bad file upload attempt by staff ' . $staff_id . ' from ip:' . $ip . ' ,more info::server tmp file name ' . $up_images['tmp_name'][$count] . ', client side file name ' . $up_images['name'][$count]);
                }
                
            }
            $count++;
        }
    }
    
    if ($errors['err']) {
        return false;
    } else {
        return true;
    }
}

?>