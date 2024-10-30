<?php

	if ( ! defined( 'ABSPATH' ) ) exit;
	/**
	* This class is responsible for the Admin Bar Theme
	* in the main site page
	*/
	
	if(!class_exists('MFAT_Admin_Bar_Theme')){
		class MFAT_Admin_Bar_Theme{
			private $option = '';
			private $optionname = 'mfat_theme_options';
			public function __construct(){
				$this->option = get_option($this->optionname);
                add_action('wp_enqueue_scripts', array($this, 'admin_theme_script')); //add javascript to frontend
				add_action('wp_enqueue_scripts', array($this, 'theme')); // add theme to adminbar on frontend
				add_action('wp_enqueue_scripts', array($this, 'custom_style'));
				add_action('wp_enqueue_scripts', array($this, 'font_style'));
			}
			
            /**
            * Function for adding the required javascript files for the admin bar on the site        
            */
            
            public function admin_theme_script(){
                wp_enqueue_script('rippleScript', plugins_url('js/ripples.js', __FILE__),
                                'jquery', '', true);
                wp_enqueue_script('materialScript', plugins_url('js/material.js', __FILE__),
                                'jquery', '', true);
            }
            
			/**
			* This function is responsible for loading an external stylesheet for the 
            * admin bar depending on the chosen theme.
			*/
			
			public function theme(){
				$option = $this->option;
				$theme = $option['theme'];
                //include material design stylesheet
                wp_enqueue_style('mfat-material', plugins_url('css/material-design.css', __FILE__));
				switch($theme){
					case 'Blue':
						if(is_rtl())
							wp_enqueue_style('mfat', plugins_url('css/blue-rtl.css', __FILE__));
						else
							wp_enqueue_style('mfat', plugins_url('css/blue.css', __FILE__));
					break;
                    
					case 'Red':
						if(is_rtl())
							wp_enqueue_style('mfat', plugins_url('css/red-rtl.css', __FILE__));
						else
							wp_enqueue_style('mfat', plugins_url('css/red.css', __FILE__));
					break;
				}//end switch
			}
			
			/**
			* This function is responsible for the font style of the text and
			* links in the admin bar section of the home page.
			* It outputs the internal stylesheet for this.
			*/
			
			public function font_style(){
				$font_style = $this->option['font_style'];
				$output = '';
				//check to see if the chosen admin bar font style is not default.
				if($font_style != "Default"){
					$url = 'http' . ( is_ssl() ? 's' : '' )
	        	   	   .'://fonts.googleapis.com/css?family='
    	        	   . urlencode($font_style);
					wp_enqueue_style('mfat-google-font', $url);
				}
			}
			
			/**
			* This function is responsible for adding the external stylesheet containing 
			* the custom variables like the font style... etc to the frontend adminbar.
			*/
			
			public function custom_style(){
				if(file_exists(MFAT_CUSTOM_DIR . '/adminbar.css')){
					$file_url = MFAT_CUSTOM_URL . '/adminbar.css';
					wp_enqueue_style('mfat-custom', $file_url);
				}
			}
		}//end class
	}//end if

	$mfat_admin_bar_theme = new MFAT_Admin_Bar_Theme();