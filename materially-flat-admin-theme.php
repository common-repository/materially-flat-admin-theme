<?php
/*
		Plugin Name: Materially Flat Admin Theme
		Plugin URI: http://wordpress.org/materially-flat-admin-theme/
		Description: Change the look and appearance of your admin and login pages.
		Version: 1.0.0
		Author: Duru Goodness
        Author URI: 
        License: GPLv2 or later
        License URI: http://www.gnu.org/licenses/gpl-2.0.html
   */
	if ( ! defined( 'ABSPATH' ) ) exit;
   
    // If this file is called directly, abort.
    if ( ! defined( 'WPINC' ) ) {
        die;
    }
	define("MFAT_URL", plugin_dir_url(__FILE__));
	define("MFAT_DIR", dirname(__FILE__));
	define("MFAT_VERSION", '1.0.0');
	define("MFAT_CUSTOM_URL", trailingslashit(WP_CONTENT_URL). 'materially-flat-admin-theme');
	define("MFAT_CUSTOM_DIR", trailingslashit(WP_CONTENT_DIR). 'materially-flat-admin-theme');
    register_activation_hook(__FILE__, 'mfat_install');
    
		/**
		* Function for installing Materially Flat Admin Theme and filling default options for the plugin
		*/
		function mfat_install(){
			global $wp_version;
			
			if(version_compare($wp_version, '3.8', '<')){
				wp_die('This plugin requires wordpress version 3.8 and above');
			}
			
			//transient for Materially Flat Admin Theme Welcome Screen on activation
			set_transient('mfat_welcome_screen_activation_redirect', true, 30);
			$mfat_theme_options = array(
                'theme' => 'Blue',
                'include_img' => 'no',
                'background_img' => '',
				'font_color' => '#444',
				'link_color' => '#0073aa',
				'hover_link_color' => '#0096dd',
                'font_style' => 'Default'
			);
			$mfat_settings_options = array(
				'google_api_key' => 'AIzaSyCkoGQOWbd8r3lMWXPZxsYurjUXr_tJia4'
			);
			$mfat_login_options = array(
				'theme' => 'Blue',
                'include_img' => 'no',
                'background_img' => '',
				'font_color' => '#444',
				'link_color' => '#999',
				'hover_link_color' => '#00a0d2',
				'font_style' => 'Default'
			);
			
			//check to see if plugin has been activated before to avoid overwriting already saved values
				if(!get_option('mfat_theme_options')){
					mfat_copy_files();
					update_option('mfat_settings_options', $mfat_settings_options);
					update_option('mfat_theme_options', $mfat_theme_options);
					update_option('mfat_login_options', $mfat_login_options);
				}
		}
	
	/* action for initializing plugin **************************************************/
	add_action('init', 'mfat_init');
	function mfat_init(){
		global $pagenow;
		$option = get_option('mfat_theme_options');
		$loginoption = get_option('mfat_login_options');
		//load the various classes for the admin dashboard
		if(is_admin()){
			require_once('admin/class-mfat-settings.php');
			require_once('admin/class-theme.php');
		}
		//load the various classes for the admin bar in the main site
		else if((!is_admin()) && (is_admin_bar_showing()) &&(is_user_logged_in())){
			if(array_key_exists('theme', $option))//check to see if theme has been set
				require_once('adminbar/class-theme.php');
		}
		//load the various classes for the login page
		if($pagenow == 'wp-login.php'){
			if(array_key_exists('theme', $loginoption))//check to see if login theme has been set
				require_once('login/class-theme.php');
		}
	}
	
	/**
	* This function is responsible for copying the sass files to the upload directory
	* of wordpress
	*/
	
	function mfat_copy_files(){
		$url = admin_url('plugins.php');
		$access_type = get_filesystem_method();
		
		//if filesystem method is direct then go on smoothly.
		if($access_type === 'direct'){
		
			// Let's get some credentials
			$creds = request_filesystem_credentials($url, '', false, false, array());
			// Inititialize the API
			if(!WP_Filesystem($creds))
				return true;
				
			global $wp_filesystem;
			
			if(!$wp_filesystem->exists( MFAT_CUSTOM_DIR )){
				$wp_filesystem->mkdir( MFAT_CUSTOM_DIR );
				$scss_files = array("admin.scss", "admin-variables.scss", "adminbar.scss", "login.scss", "login-variables.scss");
				$scss_directory = MFAT_DIR. '/templates/';
				foreach ( $scss_files as $file ) {
					if ( ! $wp_filesystem->exists( MFAT_CUSTOM_DIR . "/{$file}" ) ) {
						if ( ! $wp_filesystem->put_contents( MFAT_CUSTOM_DIR . "/{$file}", $wp_filesystem->get_contents( $scss_directory . $file, FS_CHMOD_FILE) ) )
							wp_die( "<pre>Could not copy a file {$file}.</pre>" );
					}
				}
			}
		}
		
		else{
			add_action('admin_notice', 'mfat_admin_notice');
		}		
	}
	
	//Function responsible for adding notice when filesystem method is not direct
	function mfat_admin_notice(){
		$url = admin_url('plugins.php');
		$link = wp_nonce_url($url, 'mfat_ask_cred', 'mfat_ask');
		$message = __("Materially would like you to type in your username and password. Just click <a href='$url'>here</a>", "mfat");
		echo '<div class="notice">';
		echo $message;
		echo "</div>";
	}
	
	add_action('admin_init', 'mfat_request_cred_not_direct');
	//function for requesting filesystem credential when filesystem method is not direct	
	function mfat_request_cred_not_direct(){
		if(isset($_GET['mfat_ask']) && wp_verify_nonce($_GET['mfat_ask'], 'mfat_ask_cred')){
			$url = admin_url('plugins.php');
			$in = true;
			if (false === ($creds = request_filesystem_credentials($url, '' ) ) ) {
                $in = false;
            }
            if ($in && ! WP_Filesystem($creds) ) {
                // our credentials were no good, ask the user for them again
                request_filesystem_credentials($url, '', true);
                $in = false;
            }
            if($in)
            {
				global $wp_filesystem;
				//redefine the custom directory for storing the css files.
				define("MFAT_CUSTOM_DIR", trailingslashit($wp_filesystem->wp_content_dir()). 'materially-flat-admin-theme');
    
				if(!$wp_filesystem->exists( MFAT_CUSTOM_DIR )){
					$wp_filesystem->mkdir( MFAT_CUSTOM_DIR );
					$scss_files = array("admin.scss", "admin-variables.scss", "adminbar.scss", "login.scss", "login-variables.scss");
					$scss_directory = MFAT_DIR. '/templates/';
					foreach ( $scss_files as $file ) {
						if ( ! $wp_filesystem->exists( MFAT_CUSTOM_DIR . "/{$file}" ) ) {
							if ( ! $wp_filesystem->put_contents( MFAT_CUSTOM_DIR . "/{$file}", $wp_filesystem->get_contents( $scss_directory . $file, FS_CHMOD_FILE) ) )
								wp_die( "<pre>Could not copy a file {$file}.</pre>" );
						}
					}
				}
			}
		}			
	}
	