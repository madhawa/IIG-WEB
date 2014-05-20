<?php

class breadcrumbs {
    var $breadcrumbs;

    function __construct() {
        $this->breadcrumbs = array();
    }

    function get_bcs() {
        return $this->breadcrumbs;
    }

    function add($name, $url) {
        $this->breadcrumbs[] = '<a href="'. $url .'">' . $name . '</a>';
    }
}


?>