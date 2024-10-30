<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	/**
	* Class responsible for Login Page Theme.
	*/
	
	if(!class_exists('MFAT_Login_Theme')){
		class MFAT_Login_Theme{
			private $option = '';
			private $optionname = 'mfat_login_options';
			public function __construct(){
				$this->option = get_option($this->optionname);
                add_action('login_enqueue_scripts', array($this, 'login_theme'));
				add_action('login_enqueue_scripts', array($this, 'font_style'));
				add_action('login_enqueue_scripts', array($this, 'custom_style'));
                add_action('login_enqueue_scripts', array($this, 'login_theme_script'));
			}
            
            /**
            * Function for adding the required javascript files
            * to the login page.        
            */
            
            public function login_theme_script(){
				wp_enqueue_script('jquery');
				wp_enqueue_script('rippleScript', plugins_url('js/ripples.js', __FILE__),
                                '', '', true);
                wp_enqueue_script('materialScript', plugins_url('js/material.js', __FILE__),
                                '', '', true);
            }
			
            
			/*
			* This function is responsible for loading the external login
			* page stylesheet depending on the user choice
			*/
			
			public function login_theme(){
				$theme = $this->option['theme'];
				$include_img = $this->option['include_img'];
				$background_img = $this->option['background_img'];
				//boolean to include image.
				$include_image = false;
				//include material stylesheet.
				wp_enqueue_style('mfat-material', plugins_url('css/material-design.css', __FILE__));
				//check to see if image can be included and the url of image is really an image
				if(($include_img == 'on') && ($background_img != '') && (getimagesize($background_img)))
					$include_image = true;
				else
					$include_image = false;
				switch($theme){
					case 'Blue':
						if(is_rtl())
							wp_enqueue_style('mfat', plugins_url('css/blue-rtl.css', __FILE__));
						else
							wp_enqueue_style('mfat', plugins_url('css/blue.css', __FILE__));
						if($include_image){
							if(is_rtl())
								wp_enqueue_style('mfat-img', plugins_url('css/blue-img-rtl.css', __FILE__));
							else
								wp_enqueue_style('mfat-img', plugins_url('css/blue-img.css', __FILE__));
						}
					break;
			
					case 'Red':
						if(is_rtl())
							wp_enqueue_style('mfat', plugins_url('css/red-rtl.css', __FILE__));
						else
							wp_enqueue_style('mfat', plugins_url('css/red.css', __FILE__));
						if($include_image){
							if(is_rtl())
								wp_enqueue_style('mfat-img', plugins_url('css/red-img-rtl.css', __FILE__));
							else
								wp_enqueue_style('mfat-img', plugins_url('css/red-img.css', __FILE__));
						}
				}//end switch
			}
			
       		/**
			* This function is responsible for the font style
			* of all texts and links in the login page. It outputs
			* the adds the google font stylesheet for this.
			*/
			
			public function font_style(){
				$font_style = $this->option['font_style'];
				$output = '';
				if($font_style != 'Default'){	
					$url = 'http' . ( is_ssl() ? 's' : '' ).'://fonts.googleapis.com/css?family='
        	        . urlencode($font_style);
					wp_enqueue_style('mfat', $url);
				}
			}
			
			/**
			* This function is responsible for adding the external stylesheet containing 
			* the custom variables like the font... etc to the login page.
			*/
			
			public function custom_style(){
				if(file_exists(MFAT_CUSTOM_DIR . '/login.css')){
					$file_url = MFAT_CUSTOM_URL . '/login.css';
					wp_enqueue_style('mfat-custom', $file_url);
				}
			}
		}//end class
	}//end if
	
	$mfat_login_theme = new MFAT_Login_Theme();