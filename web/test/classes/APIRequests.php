<?php
/*
This file is part of the DQSEGDB WUI.

This file was written by Gary Hemming <gary.hemming@ego-gw.it>.

DMS-WUI uses the following open source software:
- jQuery JavaScript Library v1.12.4, available under the MIT licence - http://jquery.org/license - Copyright jQuery Foundation and other contributors.
- W3.CSS 2.79 by Jan Egil and Borge Refsnes.
- Font Awesome by Dave Gandy - http://fontawesome.io.
*/

require_once 'DAO.php';
require_once 'Logger.php';

class APIRequests {
	
    /* Set AJAX request timeouts. */
    private function set_ajax_timeout($t) {
        if($t == 'short') { $to = 3; }
        elseif($t == 'medium') { $to = 6; }
        elseif($t == 'long') { $to = 10; }
        ini_set('default_socket_timeout', $to);
    }

    /* Check if a host connection is possible. */
    public function host_connection_available() {
        // Init.
        $r = FALSE;
        // Instantiate.
        $dao = new DAO();
        // Set timout.
        $this->set_ajax_timeout('short');
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        $a = json_decode(file_get_contents($a[0]['host_ip'].'/dq'), true);
        // If not empty.
        if(!empty($a) && is_array($a)) {
            $r = TRUE;
        }
        // Return.
        return $r;
    }

    /* Get the payload provided by an already-built URI. */
    public function get_uri($uri) {
        // Instantiate.
        $dao = new DAO();
        // Set timout.
        $this->set_ajax_timeout('short');
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        $a = json_decode(file_get_contents($a[0]['host_ip'].$uri), true);
        // Return.
        return $a;
    }
    
    /* Get an array of available IFO. */
    public function get_ifo_array() {
        // Instantiate.
        $dao = new DAO();
        // Set timout.
        $this->set_ajax_timeout('short');
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        $a = json_decode(file_get_contents($a[0]['host_ip'].'/dq'), true);
        // Return.
        return $a;
    }

    /* Get an array of all available flags. */
    public function get_all_flags() {
        // Instantiate.
        $dao = new DAO();
        // Set timout.
        $this->set_ajax_timeout('medium');
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        $a = json_decode(file_get_contents($a[0]['host_ip'].'/report/flags'), true);
        // Return.
        return $a;
    }

    /* Get an array of flags related to a specific IFO. */
    public function get_ifo_flags() {
        // Instantiate.
        $dao = new DAO();
        // Set timout.
        $this->set_ajax_timeout('medium');
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        $a = json_decode(file_get_contents($a[0]['host_ip'].'/dq/'.$_SESSION['ifo']), true);
        // Return.
        return $a;
    }
    
    /* Get segments from the server. */
    public function get_segments($s, $e, $history) {
        // Init.
        $r = NULL;
        // Instantiate.
        $dao = new DAO();
        $log = new Logger();
        // Set the arguments to be passed.
        $args = $this->get_uri_args($s, $e);
        if($history == 0) {
            $args = $args.'&include=metadata,active,known';
        }
        // Get details for this host.
        $a = $dao->get_host_details($_SESSION['host_id']);
        // Get file contents.
        //$a = json_decode(file_get_contents($a[0]['host_ip'].'/dq/'.$_SESSION['ifo']), true);
        // Loop through each flag.
        foreach($_SESSION['uri_selected'] as $i => $uri) {
            $log->write_to_log_file(0, $uri);
            // Get resultant array.
            $r .= file_get_contents($a[0]['host_ip'].$uri.$args);
        }
        // Return.
        return $r;
    }

    /* Get URI args. */
    public function get_uri_args($s, $e) {
        // Init.
        $args = NULL;
        // If start GPS passed.
        if(isset($s)) {
            $args .= '&s='.$s;
            $_SESSION['gps_start'] = $s;
        }
        // If stop GPS passed.
        if(isset($e)) {
            $args .= '&e='.$e;
            $_SESSION['default_gps_stop'] = $e;
        }
        // If args have been passed.
        if(!empty($args)) {
            $args = substr($args, 1);
            $args = '?'.$args;
        }
        // Return.
        return $args;
    }
}

?>