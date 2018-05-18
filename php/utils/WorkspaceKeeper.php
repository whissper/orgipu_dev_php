<?php

namespace utils;

use dbcengine\DBEngine;

/**
 * WorkspaceKeeper class
 */
class WorkspaceKeeper {
    //constructor
    function __construct() {
        
    }

    /**
     * do Login
     * @param type $login
     * @param type $password
     * @return JSON string
     */
    public function doLogin($login, $password) {
        $dbEngine = new DBEngine();
        $userData = $dbEngine->selectLoginData($login, $password);

        if ($userData['error'] != '') {
            session_destroy();
            return $userData['error'];
        }

        if ($userData['id'] != '' && $userData['role'] != '') {
            $_SESSION['usr_id'] = $userData['id'];
            $_SESSION['usr_role'] = $userData['role'];
            return '{ "isvalid" : true, "userid" : "' . $_SESSION['usr_id'] . '", "userrole" : "' . $_SESSION['usr_role'] . '" }';
        } else {
            session_destroy();
            return '{ "isvalid" : false, "userid" : "", "userrole" : "" }';
        }
    }
    
    /**
     * do Logout
     */
    public function doLogout() {
        session_destroy();
    }
    
    /**
     * get type of loading workspace (session values check with passed parameters)
     * @param type $userID
     * @param type $userRole
     * @return string
     */
    public function loadWorkspace($userID, $userRole) {
        if (isset($_SESSION['usr_id']) &&
                isset($_SESSION['usr_role']) &&
                intval($_SESSION['usr_id']) == intval($userID) &&
                intval($_SESSION['usr_role']) == intval($userRole)) {
            if (intval($_SESSION['usr_role']) == 1) { //guest
                return '1';
            } else if (intval($_SESSION['usr_role']) == 2) { //user	
                return '2';
            } else if (intval($_SESSION['usr_role']) == 3) { //admin
                return '3';
            }
        } else {
            session_destroy();
            return '0';
        }
    }
    
    /**
     * get type of loading workspace according current session values (userID and userRole)
     * @return string
     */
    public function keepWorkspace() {
        if (isset($_SESSION['usr_id']) &&
                isset($_SESSION['usr_role'])) {
            if (intval($_SESSION['usr_role']) == 1) { //guest
                return '1';
            } else if (intval($_SESSION['usr_role']) == 2) { //user	
                return '2';
            } else if (intval($_SESSION['usr_role']) == 3) { //admin
                return '3';
            }
        } else {
            session_destroy();
            return '0';
        }
    }

}
