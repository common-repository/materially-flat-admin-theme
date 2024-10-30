<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;
	//include utility class
	require_once(MFAT_DIR.'/inc/library-class.php');
	//include class for login theme page
	require_once('class-mfat-login-settings.php');
	//include class for Materially Flat Admin Theme settings page
	require_once('class-settings.php');
	class MFAT_Theme_Settings{
		//Constant for making sense of the admin page structure
		const settings_group_name = 'mfat-theme';
		const settings_form_id = 'admin-look';
		const settings_page = 'admin-theme-page';
		const menu_slug = 'mfat-main';
		const language_slug = 'mfat';
		const loginmenu_slug = 'mfat-login';
        const settingsmenu_slug ='mfat-settings';
		const settings_theme_section = 'admin-theme-section';
		const settings_font_section = 'admin-font-section';
		private $library_class = '';
		private $google_fonts = array();
		private $google_api_key = '';
		private $cache_name = '';
		private $api_url = '';
		private $pagetitle = 'Materially Flat Admin Theme';
		private $menuname = 'Material Theme';
		private $submenuname = 'Admin Theme Settings';
        private $loginpagelongname = 'Materially Flat Admin Theme Login Settings';
		private $loginpagetitle = 'Materially Flat Admin Theme Login Settings';
		private $settingspagelongname = 'Settings';
		private $settingspagetitle = 'Settings';
		private $option = '';
		private $optionname = 'mfat_theme_options';
		private $settings_optionname = 'mfat_settings_options';
		private $admin_notice = '';
		private $fontarray = array();
		private $mfat_login_page = '';
		private $mfat_settings_page = '';
		
		public function __construct(){
			$this->option = get_option($this->optionname);
			$settings_option = get_option($this->settings_optionname);
			//Google API key for accessing the font list from google font
			$this->google_api_key = $settings_option['google_api_key'];
			// The name of the cache for the the fonts.
    		$this->mfat_cache_name = 'mfat_cache';
			// The Google API URL we will fetch the font list from.
    		$this->api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';
			//initialize Materially Flat Admin Theme utility class
			$this->library_class = new MFAT_Library();
			//initialize login theme class
			$this->mfat_login_page = new MFAT_Login_Theme_Settings();
			//initialize settings class
			$this->mfat_settings_page = new MFAT_Settings();
			//add the admin menu
			add_action('admin_menu', array($this, 'admin_menu'));
			
			//Register Settings
			add_action('admin_init', array($this, 'register_settings'));
			add_action('admin_init', array($this->mfat_login_page, 'register_settings'));
			add_action('admin_init', array($this->mfat_settings_page, 'register_settings'));
			add_action('admin_init', array($this->mfat_settings_page, 'process_settings_export'));
			add_action('admin_init', array($this->mfat_settings_page, 'process_settings_import'));
		}
		
		/**
		* This function is responsible for adding this settings page stylesheet
		*/
		
		public function page_style(){
			if ( is_rtl() ) {
				$css = 'css/admin-theme-settings-rtl.css';
			}
			else {
				$css = 'css/admin-theme-settings.css';
			}
			wp_enqueue_style('mfatStylesheet', plugins_url($css, __FILE__));
			wp_enqueue_style('wp-color-picker');
		}
		
		/**
		* This function is responsible for adding the various scripts for this page
		*/
		
		public function page_script(){
			wp_register_script('tipTipScript', plugins_url('js/jquery-tiptip/jquery.tipTip.min.js', __FILE__),
							'jquery', '', true);
			wp_enqueue_script('mfatScript', plugins_url('js/admin-theme-settings.js', __FILE__),
							array('jquery', 'media-upload', 'media-views', 'wp-color-picker',
							'jquery-touch-punch', 'tipTipScript'), '', true);
			wp_enqueue_media();
		}
		
		/**
		* This function is responsible for adding the Materially Flat Admin Theme Menu
		* in the admin dashboard.
		*/
		public function admin_menu(){
			$pagetitle = $this->pagetitle;
			$menuslug = self::menu_slug;
			$languageslug = self::language_slug;
			$menuname = $this->menuname;
			$submenuname = $this->submenuname;
			$mfat_login_page = $this->mfat_login_page;
			$loginpagelongname = $this->loginpagelongname;
			$loginpagetitle = $this->loginpagetitle;
			$loginmenuslug = self::loginmenu_slug;
			$mfat_settings_page = $this->mfat_settings_page;
			$settingspagelongname = $this->settingspagelongname;
			$settingspagetitle = $this->settingspagetitle;
			$settingsmenuslug = self::settingsmenu_slug;
			
			$page = add_menu_page(
				__($pagetitle, $languageslug),
				__($menuname, $languageslug), 
				'manage_options', $menuslug, 
				array($this, 'settings_page')
			);
		
			//get login theme submenu page without adding to admin menu section
			
			$loginsubmenupage = add_submenu_page(
				'options.php',
				__($loginpagelongname, $languageslug),
				__($loginpagetitle, $languageslug),
				'manage_options',
				$loginmenuslug,
				array($mfat_login_page, 'settings_page')
			);
			//add settings submenu page
			
			$settingsubmenupage = add_submenu_page(
				'options.php',
				__($settingspagelongname, $languageslug),
				__($settingspagetitle, $languageslug),
				'manage_options',
				$settingsmenuslug,
				array($mfat_settings_page, 'settings_page')
			);
			//add styles and scripts to menu and submenu pages
			add_action('admin_print_styles-'.$page, array($this, 'page_style'));
			add_action('admin_print_scripts-'.$page, array($this, 'page_script'));
			add_action('admin_print_styles-'.$loginsubmenupage, array($this, 'page_style'));
			add_action('admin_print_scripts-'.$loginsubmenupage, array($mfat_login_page, 'page_script'));
			add_action('admin_print_styles-'.$settingsubmenupage, array($mfat_settings_page, 'page_style'));
			add_action('admin_print_scripts-'.$settingsubmenupage, array($mfat_settings_page, 'page_script'));
			
		}
		
		/**
		* This function responsible for outputting the Admin Theme
		* settings page.
		*/
		
		public function settings_page(){
			$languageslug = self::language_slug;
			//Materially Flat Admin Theme library class
			$library_class = $this->library_class;
			// Get the current data from Google.
        	$this->google_fonts = $library_class->get_google_font_data($this->api_url, $this->google_api_key);
            //Get the various links of the various sections
            $themelink = esc_url(admin_url(add_query_arg(array('page' => self::menu_slug),'admin.php')));
            $loginlink = esc_url(admin_url(add_query_arg( array( 'page' => self::loginmenu_slug ), 'admin.php')) );
            $settingslink = esc_url(admin_url(add_query_arg(array('page' => self::settingsmenu_slug), 'admin.php')));
			echo '<div class="wrap">';
            echo '<h2 class="nav-tab-wrapper">';
            echo '<a href="'.$themelink.'" class = "nav-tab nav-tab-active">'.__('Admin Theme Settings', $languageslug).'</a>';
            echo '<a href="'.$loginlink.'" class = "nav-tab">'.__('Login Theme Settings', $languageslug).'</a>';
            echo '<a href="'.$settingslink.'" class = "nav-tab">'.__('Settings', $languageslug).'</a>';
            echo '</h2>';
			echo '<h1>'. __('Admin Theme Options', $languageslug). '</h1>';
			if ( empty($this->google_fonts) ) {
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
			
			echo '<p> <a href="#" id="preview-change" class="button hide-if-no-js">'.__('Preview Changes', $languageslug).'</a>'; 
			submit_button();
			
			echo '</form>';
			
			echo '</div>';
		}
        
		
		/**
		* Function for registering all parts of the settings page.
		* and also getting the options that applies to a user.
		*/
		
		public function register_settings(){
			$languageslug = self::language_slug;
			
			register_setting(
				self::settings_group_name,
				$this->optionname,
				array($this, 'validate_settings')
			);
			
			add_settings_section(
				self::settings_theme_section,
				__('Admin Theme Section', $languageslug),
				array($this, 'admin_theme_section'),
				self::settings_page
			);
			
			add_settings_field(
				'admin-theme',
				__('Admin Theme', $languageslug),
				array($this, 'admin_theme'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'admin-theme')
			);
			
			add_settings_field(
				'include-background-image',
				__('Include Background Image', $languageslug),
				array($this, 'include_image_field'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'include-background-image hide-if-no-js')
			);
			
			add_settings_field(
				'background-image-url',
				__('Background Image URL', $languageslug),
				array($this, 'background_image_URL'),
				self::settings_page,
				self::settings_theme_section,
				array('class' => 'background-image-url hide-if-no-js')
			);
			
			add_settings_section(
				self::settings_font_section,
				__('Admin Font Section', $languageslug),
				array($this, 'admin_font_section'),
				self::settings_page
			);
			
			add_settings_field(
				'general-font-color',
				__('General Font Color', $languageslug),
				array($this, 'general_font_color'),
				self::settings_page,
				self::settings_font_section,
				array('class' => 'general-font-color')
			);
			
			add_settings_field(
				'link-color',
				__('Link Color', $languageslug),
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
		
		public function admin_theme_section(){
			$languageslug = self::language_slug;
			_e('This Section is concerned with the admin theme and background image options', $languageslug);
		}
		
		
		
		/**
		* This function is responsible for outputting the admin theme option
		*/
		
		public function admin_theme(){
			$option = $this->option['theme'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
            echo '<select name="'.$name.'[theme]">';
            echo '<option value="Blue"'.selected($option, 'Blue', false).'> Blue </option>';
            echo '<option value="Red"'.selected($option, 'Red', false).'> Red </option>';
            echo '</select>';
		}
        
		public function include_image_field(){
		    $option = $this->option['include_img'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This determines if a background image would be displayed in the admin dashboard.", 
            $languageslug).'">?</span>'.
				'<input id="include-background-image" name="'.$name.'[include_img]" type="checkbox" value="yes"'.checked($option, 'yes', false).'>'.
				'<p class="description hide-if-js">'.__("This determines if a background image would be displayed in the admin dashboard.", 
                $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the background image url option
		*/
		
		public function background_image_URL(){
			$option = $this->option['background_img'];
			$languageslug = self::language_slug;
			$name = $this->optionname;
			echo '<span class="helptip" data-tip="'.__("This option is for the url or the web address of the background image in the admin dashboard.", 
            $languageslug).'">?</span>'.
				 '<input id="background-image-url" name="'.$name.'[background_img]" type="text" value="'.esc_url($option).'">'.
				 '<a href="#" class="select-image dashicons dashicons-before dashicons-format-image"> </a>'.
				 '<p class="description hide-if-js">'.__("This option is for the url or the web address of the background image in the admin dashboard.", 
                 $languageslug).'</p>';
		}
		
		public function admin_font_section(){
			$languageslug = self::language_slug;
			_e('This section contains the font color, link color and hovered link color, font style for different areas of the admin dashboard', 
            $languageslug);
		}
		
		/**
		* This function is responsible for outputting the general font color option.
		* including hidden field for storing color value
		*/
		
		public function general_font_color(){
			$option = $this->option['font_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option is for the color of all texts in the admin dashboard except ".
            "for those in the admin menu and admin bar.", $languageslug).'">?</span>'.
				 '<input id="general-font-color" name="'.$name.'[font_color]" type="text" value="'.esc_attr($option).'">'.
				 '<p class="description hide-if-js">'.__("This option is for the color of all texts in the admin dashboard except ".
                 "for those in the admin menu and admin bar.", $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the link color option
		*/
		
		public function link_color(){
			$option = $this->option['link_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the color of links in the admin dashboard.", $languageslug).
            '">?</span>'.
				'<input id="link-color" name="'.$name.'[link_color]" type="text" value="'.esc_attr($option).'">'.
				'<span id="preview-link-color" class="hide-if-no-js" style="color:'.esc_attr($option).'"> Preview Link Color </span>'.
				'<p class="description hide-if-js">'.__("This option determines the color of links in the admin dashboard.", $languageslug).'</p>';
		}
		
		/**
		* This function is responsible for outputting the hoverred link color option
		*/
		
		public function hover_link_color(){
			$option = $this->option['hover_link_color'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the color of a link when it is interacted with in the admin dashboard.",
            $languageslug).'">?</span>'.
				'<input id="hover-link-color" name="'.$name.'[hover_link_color]" type="text" value="'.esc_attr($option).'">'.
				'<span id="preview-hover-color" class="hide-if-no-js" style="color:'.esc_attr($option).'">Preview Link Interaction Color </span>'.
				'<p class="description hide-if-js">'.__("This option determines the color of a link when it is interacted with in the admin dashboard.",
                $languageslug).'</p>';
		}
        
		/**
		* This function is responsible for outputting the  font style option
		*/
		
		public function font_style(){
			$option = $this->option['font_style'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This option determines the font style of all texts and links in the admin dashboard."
            ,$languageslug).'">?</span>'.
				'<select  id="font-style" name= "'.$name.'[font_style]">'.
                    '<option value="Default"'.selected($option, "Default", false).'>'.
					__("Default", $languageslug).'</option>';
			foreach($this->fontarray as $fonts){
				  echo '<option value="'.$fonts['name'].'"'.selected($option, $fonts['name'], false).'>'.
				  $fonts['name'].'</option>';
			}
            echo '</select>';
			echo '<p class="description hide-if-js">'.__("This option determines the font style of all texts and links in the admin dashboard.",
            $languageslug).'</p>';
		}
        
		/**
		* This function is responsible for validating options saved by the
		* user and compiling the sass file created with the options to css 
		* before saving it to the database.
		*/
		
		public function validate_settings($input){
			$admin_array = array();
			$library_class = $this->library_class;
			$input['theme'] = sanitize_text_field($input['theme']);
			$input['include_img'] = ($input['include_img'] == 'yes') ? 'yes' : 'no';
			$input['background_img'] = sanitize_text_field($input['background_img']);
			$input['font_color'] = substr(sanitize_text_field($input['font_color']), 0, 7);
			$input['link_color'] = substr(sanitize_text_field($input['link_color']), 0, 7);
			$input['hover_link_color'] = substr(sanitize_text_field($input['hover_link_color']), 0, 7);
			$input['font_style'] = sanitize_text_field($input['font_style']);
				
			if($input['include_img'] == 'yes') $admin_array['background-image'] = "'$input[background_img]'";
			else $admin_array['background-image'] = "";
			
			if(strpos($input['font_style'], " ") > -1) $admin_array['font-style'] = "'$input[font_style]'";
			else $admin_array['font-style'] = $input['font_style'];
			
			//check to see if font chosen is open sans.
			if(strcasecmp('default', $admin_array['font-style']) == 0)
				$admin_array['font-style'] = "'Open Sans'";
			$admin_array['font-color'] = $input['font_color'];
			$admin_array['link-color'] = $input['link_color'];
			$admin_array['hover-link-color'] = $input['hover_link_color'];
			
			$library_class->compile_admin_sass($admin_array);
			
			return $input;
		}
		 /**
   		 * This function is responsible for displaying an admin notice if there is one.
     	 */

    	public function display_admin_notice($message = null) {
			$notice = $this->admin_notice;
        	if ( ! isset($message) ) {
				 $message = $notice;
			}
        	if ( $message == '' ) { 
				return;
			}
	
    	    // Wrap in paragraphs if no tags found in the message.
    	    if ( strpos('<', $message) === false ) {
				 $message = '<p>' . $message . '</p>';
			}
	
    	    echo '<div class="updated">';
        	echo $message;
        	echo '</div>';
    	}
	}//end class
	
	$mfat_theme_settings = new MFAT_Theme_Settings();