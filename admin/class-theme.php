<?php

	if ( ! defined( 'ABSPATH' ) ) exit;
	/**
	* This class is responsible for Admin Dashboard Theme
	*/
	
	if(!class_exists('MFAT_Theme')){
		class MFAT_Theme{
			private $option = '';
			private $optionname = 'mfat_theme_options';
			private $settings_option = '';
			public function __construct(){
				$this->option = get_option($this->optionname);
				//get either the user's theme settings or the general theme settings
				add_action('admin_enqueue_scripts', array($this, 'admin_theme')); // add theme to admin pages
                add_action('admin_enqueue_scripts', array($this, 'admin_theme_script')); // add script to admin page
				add_action('admin_enqueue_scripts', array($this, 'custom_style'));
				add_action('admin_enqueue_scripts', array($this, 'font_style'));
			}
            
            /**
            * Function for adding the required javascript files
            * to all the admin page.        
            */
            
            public function admin_theme_script(){
                wp_enqueue_script('rippleScript', plugins_url('js/ripples.js', __FILE__),
                                'jquery', '', true);
                wp_enqueue_script('materialScript', plugins_url('js/material.js', __FILE__),
                                'jquery', '', true);
            }
            
			/**
			* This function is responsible for loading an external stylesheet depending on the chosen theme.
			* If a background image is to be loaded, then the function also loads in an
			* extra external stylesheet that is responsible for reducing the background
			* opacity of various components of the admin dashboard
			*/
			
			public function admin_theme(){
				$option = $this->option;
				$theme = $option['theme'];
				$include_img = $option['include_img'];
				$background_img = $option['background_img'];
				//boolean to include image.
				$include_image = false;
                //include material design stylesheet
                wp_enqueue_style('mfat-material', plugins_url('css/material-design.css', __FILE__));
				//check to see if image can be included and the url of image is really an image
				if(($include_img == 'yes') && ($background_img != '') && (getimagesize($background_img)))
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
					break;
				}//end switch
			}
                	
				/**
				* This function is responsible for the font style of both ordinary text and
				* links in the admin dashboard. It adds the google font stylesheet for it.
				*/
				
				public function font_style(){
					$font_style = $this->option['font_style'];
					$output = '';
					//check to see if the chosen font style is not the default one
					if($font_style != "Default"){
						$url = 'http' . ( is_ssl() ? 's' : '' ).'://fonts.googleapis.com/css?family='.
						urlencode($font_style);
						wp_enqueue_style('mfat-google-font', $url);
					}
				}
				
				/**
				* This function is responsible for adding the external stylesheet containing 
				* the custom variables like the font... etc to the admin page.
				*/
				
				public function custom_style(){
					if(file_exists(MFAT_CUSTOM_DIR . '/admin.css')){
						$file_url = MFAT_CUSTOM_URL . '/admin.css';
						wp_enqueue_style('mfat-custom', $file_url);
					}
				}
				
		}//end class
	}//end if
$mfat_theme = new MFAT_Theme();