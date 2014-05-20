<?php
//some helper functions
function get_staffs() {
    $staffs = array();
    $sql = 'SELECT * FROM ' . STAFF_TABLE;
    if ( ($res=db_query($sql)) && db_num_rows($res) ) {
        while ( $row = db_fetch_array($res) ) {
            $staffs[] = $row;
        }
        return $staffs;
    } else {
        return false;
    }
}

//$users: a single user if or array of user id
function add_users_to_dept($users, $dept_id) {
    $error = '';
    if ( is_array($users) ) {
        foreach( $users as $user_id ) {
            if ( !($staff = new Staff($user_id)) && !$staff->setDeptId($dept_id) ) {
                $error .= $user_id . ', ';
            }
        }
        if ( $error ) {
            return 'failed to insert user ' . $error;
        } else {
            return true;
        }
    } else {
        if ( !($staff = new Staff($users)) && !$staff->setDeptId($dept_id) ) {
            $error .= $users;
        }
        if ( $error ) {
            return 'failed to insert user ' . $error;
        } else {
            return true;
        }
    }
}

?>