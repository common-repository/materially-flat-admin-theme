<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	//include utility class
	require_once(MFAT_DIR.'/inc/library-class.php');
	
	/**
	* This class is responsible for the login theme settings
	* page
	*/
	
	class MFAT_Login_Theme_Settings{
		const settings_group_name = 'mfat-login-theme';
		const settings_form_id = 'login-look';
		const settings_page = 'login-theme-page';
		const language_slug = 'mfat';
		const settings_theme_section = 'login-theme-section';
		const settings_font_section = 'login-font-section';
		private $google_fonts = array();
		private $option = '';
		private $optionname = 'mfat_login_options';
		private $admin_option = '';
		private $admin_optionname = 'mfat_settings_options';
		private $google_api_key = '';
		private $api_url = '';
		private $cache_name = '';
		private $admin_notice = '';
		private $fontarray = array();
		private $library_class = '';
		
		public function __construct(){
			$this->option = get_option($this->optionname);
			$this->admin_option = get_option($this->admin_optionname);
			//Google API key for accessing the font list from google font
			$this->google_api_key = $this->admin_option['google_api_key'];
			// The name of the cache for the the fonts.
    		$this->cache_name = 'mfat_cache';
			// The Google API URL we will fetch the font list from.
    		$this->api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';
			//initialize Materially Flat Admin Theme utility class
			$this->library_class = new MFAT_Library();
		}
		
		/**
		* This function is responsible for adding the various scripts for this page
		*/
		
		public function page_script(){
			wp_register_script('tipTipScript', plugins_url('js/jquery-tiptip/jquery.tipTip.min.js', __FILE__),
							'jquery');
			wp_enqueue_script('mfatScript', plugins_url('js/login-theme-settings.js', __FILE__),
							array('jquery', 'media-upload', 'media-views', 'wp-color-picker',
							'jquery-touch-punch', 'tipTipScript'));
			wp_enqueue_media();
		}
		
		/**
		* This function is responsible for outputting the 
		* login theme settings page
		*/
		
		public function settings_page(){
			$languageslug = self::language_slug;
			//Materially Flat Admin Theme library class
			$library_class = $this->library_class;
			// Get the current data from Google.
        	$this->google_fonts = $library_class->get_google_font_data($this->api_url, $this->google_api_key);
            //Get the various links of the various sections
            $themelink = esc_url(admin_url(add_query_arg(array('page' => 'mfat-main'),'admin.php')));
            $loginlink = esc_url(admin_url(add_query_arg( array( 'page' => 'mfat-login' ), 'admin.php')) );
            $settingslink = esc_url(admin_url(add_query_arg(array('page' => 'mfat-settings'), 'admin.php')));
			echo '<div class="wrap">';
             echo '<h2 class="nav-tab-wrapper">';
            echo '<a href="'.$themelink.'" class = "nav-tab">'.__('Admin Theme Settings', $languageslug).'</a>';
            echo '<a href="'.$loginlink.'" class = "nav-tab nav-tab-active">'.__('Login Theme Settings', $languageslug).'</a>';
            echo '<a href="'.$settingslink.'" class = "nav-tab">'.__('Settings', $languageslug).'</a>';
            echo '</h2>';
			echo '<h2>'. __('Login Theme Options', $languageslug). '</h2>';
			if (empty($this->google_fonts)) {
           	 	$this->google_fonts = $library_class->get_fallback_font_data();
	
    	        // Display the admin notive inline.
    	        $this->display_admin_notice(
        	        __(
            	        'Cannot access the Google Webfonts API.'
                	    . ' Check your API key.'
	                    . ' The fallback font list is being used instead.',
						$languageslug
    	            )
        	    );
	
    	        // Display additional diagnostics if available.
        	    if ( ! empty($this->admin_notice)) {
            	    $this->display_admin_notice(
    	                $this->admin_notice
    	            );
        	    }
        	}
			//get list of google fonts formatted in a right way
			$this->fontarray = $library_class->get_google_fonts($this->google_fonts, $this->cache_name);
			echo '<form method="post" action="options.php" id="'.self::settings_form_id.'">';
			echo '<table class="form-table">';
				settings_fields(self::settings_group_name);
				$library_class->do_settings_sections(self::settings_page);
			echo '</table>';
			
			submit_button();
			
			echo '</form>';
			
			echo '</div>';
		}
		public function register_settings(){
			$languageslug = self::language_slug;
			register_setting(
				self::settings_group_name,
				$this->optionname,
				array($this, 'validate_settings')
			);
			
			add_settings_section(
				self::settings_theme_section,
				__('Login Theme Section', $languageslug),
				array($this, 'login_theme_section'),
				self::settings_page
			);
			
			add_settings_field(
				'login-theme',
				__('Theme', $languageslug),
				array($this, 'login_theme'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'login-theme')
			);
            
			add_settings_field(
				'include-background-image',
				__('Include Background Image', $languageslug),
				array($this, 'include_image_field'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'hide-if-no-js include-background-image')
			);
			
			add_settings_field(
				'background-image-url',
				__('Background Image URL', $languageslug),
				array($this, 'background_image_URL'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'background-image-url')
			);
			
			add_settings_section(
				self::settings_font_section,
				__('Login Font Section', $languageslug),
				array($this, 'login_font_section'),
				self::settings_page
			);
			
			add_settings_field(
				'font-color',
				__('Font Color', $languageslug),
				array($this, 'font_color'),
				self::settings_page,
				self::settings_font_section,
				array('class' => 'font-color')
			);
			
			add_settings_field(
				'link-color',
				__('Link Font Color', $languageslug),
				array($this, 'link_color'),
				self::settings_page,
				self::settings_font_section,
				array('class' => 'link-color')
			);
			
			add_settings_field(
				'hovered-link-color',
				__('Link Interaction Font Color', $languageslug),
				array($this, 'hover_link_color'),
				self::settings_page,
				self::settings_font_section,
				array('class' => 'hovered-link-color')
			);
		
			add_settings_field(
				'font-style',
				__('Font Style', $languageslug),
				array($this, 'font_style'),
				self::settings_page,
				self::settings_font_section,
				array('class' => 'font-style')
			);
		}
		
		public function login_theme_section(){
			$languageslug = self::language_slug;
			_e('This section is concerned with the login theme and background image options', $languageslug);
		}
		
		public function login_theme(){
			$languageslug = self::language_slug;
			if(array_key_exists('theme', $this->option))
				$option = $this->option['theme'];
			else
				$option = 'Black';
			$name = $this->optionname;
            echo '<select name="'.$name.'[theme]">';
            echo '<option value="Blue"'.selected($option, 'Blue', false).'> Blue </option>';
            echo '<option value="Red"'.selected($option, 'Red', false).'> Red </option>';
            echo '</select>';
		}

		/**
		* This function is responsible for outputting the include
		* background image option
		*/
		
		public function include_image_field(){
			if(array_key_exists('include_img', $this->option))
				$option = $this->option['include_img'];
			else
				$option = 'off';
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This determines if a background image would be displayed in the login page.", 
            $languageslug).'">?</span>'.
				'<input id="include-background-image" name="'.$name.'[include_img]" type="checkbox" value="on"'.checked($option, 'on', false).'>'.
				'<p class="description hide-if-js">'.__("This determines if a background image would be displayed in the login page.", 
                $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the background image url option
		*/
		
		public function background_image_URL(){
			if(array_key_exists('background_img', $this->option))
				$option = $this->option['background_img'];
			else
				$option = '';
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option is for the url or the web address of the background image in the login page.", 
            $languageslug).'">?</span>'.
				 '<input id="background-image-url" name="'.$name.'[background_img]" type="text" value="'.esc_url($option).'">'.
				 '<a href="#" class="select-image dashicons dashicons-before dashicons-format-image"> </a>'.
				 '<p class="description">'.__("This option is for the url or the web address of the background image in the login page.", 
                 $languageslug).'</p>';
		}
		
		public function login_font_section(){
			$languageslug = self::language_slug;
			_e('This section contains the font color, link color and hovered link color, font style for the login page', $languageslug);
		}
		
		/**
		* This function is responsible for outputting the general font color option
		*/
		
		public function font_color(){
			$option = $this->option['font_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option is for the color of texts in the login page.", $languageslug).'">?</span>'.
				 '<input id="font-color" name="'.$name.'[font_color]" type="text" value="'.esc_attr($option).'">'.
				 '<p class="description hide-if-js">'.__("This option is for the color of texts in the ".
				"login page.", $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the link color option
		*/
		
		public function link_color(){
			$option = $this->option['link_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the color of links in the login page.", $languageslug).'">?</span>'.
				'<input id="link-color" name="'.$name.'[link_color]" type="text" value="'.esc_attr($option).'">'.
				'<span id="preview-link-color" class="hide-if-no-js" style="color:'.esc_attr($option).'">Preview Link Color </span>'.
				'<p class="description hide-if-js">'.__("This option determines the color of links in the login page.", $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the hoverred link color option
		*/
		
		public function hover_link_color(){
			$option = $this->option['hover_link_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the color of a link when it is interacted with in the login page."
            ,$languageslug).'">?</span>'.
				'<input id="hover-link-color" name="'.$name.'[hover_link_color]" type="text" value="'.esc_attr($option).'">'.
				'<span id="preview-hover-color" class="hide-if-no-js" style="color:'.esc_attr($option).'">Preview Link Interaction Color </span>'.
				'<p class="description hide-if-js">'.__("This option determines the color of a link when it is interacted with in the login page.",
                $languageslug).'</p>';
		}
        
		/**
		* This function is responsible for outputting the font style option
		*/
		
		public function font_style(){
			if(array_key_exists('font_style', $this->option))
				$option = $this->option['font_style'];
			else
				$option = 'Default';
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the font style of all texts and links in the login page.",
            $languageslug).'">?</span>'.
				'<select  id="font-style" name= "'.$name.'[font_style]">'.
                    '<option value="Default"'.selected($option, "Default", false).'>'. __("Default", $languageslug).'</option>';
			foreach($this->fontarray as $fonts){
				   echo '<option value="'.$fonts['name'].'"'.selected($option, $fonts['name'], false).'>'.$fonts['name'].'</option>';
			}
            echo '</select>';
			echo '<p class="description hide-if-js">'.__("This option determines the font style of all texts and links in the login page.",
            $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for validating options saved by the
		* user and compiling the sass file created with the options to css 
		* before saving it to the database.
		*/
				
		public function validate_settings($input){
			//array to hold the arguments to that would be compiled to the login css file.
			$login_array = array();
			$library_class = $this->library_class;
			$input['theme'] = sanitize_text_field($input['theme']);
			$input['include_img'] = ($input['include_img'] == 'on') ? 'on' : 'off';
			$input['background_img'] = sanitize_text_field($input['background_img']);
			$input['font_color'] = substr(sanitize_text_field($input['font_color']), 0, 7);
			$input['link_color'] = substr(sanitize_text_field($input['link_color']), 0, 7);
			$input['hover_link_color'] = substr(sanitize_text_field($input['hover_link_color']), 0, 7);
            $input['font_style'] = sanitize_text_field($input['font_style']);
			if($input['include_img'] == 'on') $login_array['background-image'] = "'$input[background_img]'";
			else $login_array['background-image'] = "";
			
			if(strpos($input['font_style'], " ") > -1) $login_array['font-style'] = "'$input[font_style]'";
			else $login_array['font-style'] = $input['font_style'];
			
			//check to see if font chosen is default.
			if(strcasecmp('default', $login_array['font-style']) == 0)
				$login_array['font-style'] = "'Open Sans'";
				
			$login_array['font-color'] = $input['font_color'];
			$login_array['link-color'] = $input['link_color'];
			$login_array['hover-link-color'] = $input['hover_link_color'];
			
			$library_class->compile_login_sass($login_array);
			return $input;
		}
		
		/**
   		 * Display an admin notice if there is one.
     	 */

    	public function display_admin_notice($message = null) {
			$notice = $this->admin_notice;
        	if ( ! isset($message)) $message = $notice;
        	if ($message == '') return;
	
    	    // Wrap in paragraphs if no tags found in the message.
    	    if (strpos('<', $message) === false) $message = '<p>' . $message . '</p>';
	
    	    echo '<div class="updated">';
        	echo $message;
        	echo '</div>';
    	}
	}//end class