<?php

class Session {

    function init() {
        //TODO: make this function
    }
    
    function get_session() {
        self::init();
        return $_SESSION;
    }
    
    function set_session_data($indice, $data) {
        self::init();
        $_SESSION[$indice] = $data;
    }
    
    function get_session_data($indice) {
        $session = self::get_session();
        
        if ( isset($session[$indice])) {
            return $session[$indice];
        } else {
            return null;
        }
    }
    
    function unset_element($indice) {
        unset($_SESSION[$indice]);
    }
}

//flashdata management
class SessionFlash extends Session {
    //whether the flashdata array exists
    function check_flash() {
        if ( parent::get_session_data('flash')===null ) {
            parent::set_session_data('flash', array());
        }
    }
    
    function get_flash() {
        self::check_flash();
        return parent::get_session_data('flash');
    }
    
    function get_flash_element($indice) {
        $flash = self::get_flash();
        return $flash[$indice];
    }
    
    function get_flash_message() {
        return self::get_flash_element('message');
    }
    
    function get_flash_data() {
        return self::get_flash_element('data');
    }
    
    
    function set_flash_element($indice, $data) {
        self::check_flash();
        $_SESSION['flash'][$indice] = $data;
    }
    
    function set_flash_message($msg) {
        self::set_flash_element('message', $msg);
    }
    
    function set_flash_data($data) {
        self::set_flash_element('data', $data);
    }
    
    function clear_flash() {
        parent::unset_element('flash');
    }
}



?>