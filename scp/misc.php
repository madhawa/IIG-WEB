<?php

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


?>