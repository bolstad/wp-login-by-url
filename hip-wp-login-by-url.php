<?php

/*
Plugin Name: Hippies Login by URL
Plugin URI: http://hippies.se/plugins/hip-wp-login-by-url/
Description: Let selected users login without credentials with a secret url 
Version: 0.1
Author: Christian Bolstad
Author URI: http://christianbolstad.com/
*/

/**
 * Copyright (c) `date "+%Y"` Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */


$hip_redirect_to = '/mina-sidor/';


function hip_auto_login() 
{
    global $hip_redirect_to;

    $hip_loginname = wp_kses($_GET['username'],array());
    $hip_checksum  = wp_kses($_GET['plux'],array());

    if (!is_user_logged_in() && !empty($_GET['plux'])) 
    {

        // get sha256 checksum and salt it with AUTH_KEY constant defined in wp-config.php    	
        $real_checksum = hash('sha256',$hip_loginname . AUTH_KEY);

       if (!empty($hip_loginname) && $real_checksum == $hip_checksum )
            {
                //login
                $user = get_user_by('login', $hip_loginname);
                $userobject =  wp_set_current_user($user->ID, $hip_loginname);
                wp_set_auth_cookie($userobject->ID);
                do_action('wp_login', $hip_loginname);
                header('Location: ' . $hip_redirect_to);
                die;
            }    
            else
            {
                echo "fail fail";
                die;
            }
    }
}

function hip_auto_login_url($userid = null)
{

    if ($userid == null) 
          $userid = get_current_user_id();

    $user = get_user_by('id', $userid);
    $url = get_site_url() . '/?username' . $user->user_login . '&plux=' . hash('sha256',$user->user_login . AUTH_KEY); 

    return $url; 

}

add_action('init', 'hip_auto_login');
