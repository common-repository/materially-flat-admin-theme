<?php 
    /**
    * Uninstallation procedure for Materially Flat Admin Theme
    */
    
	//protect uninstall file.
		if ( ! defined( 'ABSPATH' ) ) exit;
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

    // Let's get some credentials
		if(false === ( $creds = request_filesystem_credentials(site_url(). '/wp-admin/', '', false, false, array()))){
			return true;
		}
		// Inititialize the API
		if(!WP_Filesystem($creds)){
			request_filesystem_credentials(site_url(). '/wp-admin/', '', true);
			return false;
		} 
		global $wp_filesystem;
	
        //delete all files and folder related to this plugin from the upload directory of wordpress.
		$wp_filesystem->delete( MFAT_CUSTOM_DIR, true );
		
        //remove all options related to this plugin
        delete_option( 'mfat_theme_options');
		delete_option( 'mfat_login_options');
		delete_option( 'mfat_settings_options');
			