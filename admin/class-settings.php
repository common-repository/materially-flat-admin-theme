<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	class MFAT_Settings{
		const settings_group_name = 'mfat-settings';
		const settings_form_id = 'save';
		const settings_page = 'mfat-settings-page';
		const settings_section = 'settings-section';
		const language_slug = 'mfat';
		private $optionname = 'mfat_settings_options';
		private $option = '';
		
		public function __construct(){
			$this->option = get_option($this->optionname);
		}
		
		/**
		* This function is responsible for adding the stylesheet for thispagee
		*/
		
		public function page_style(){
			wp_enqueue_style('mfatStylesheet', plugins_url('css/admin-theme-settings.css', __FILE__));
		}
		
		/**
		* This function is responsible for adding the scripts for this page
		*/
		
		public function page_script(){
			wp_register_script('tipTipScript', plugins_url('js/jquery-tiptip/jquery.tipTip.min.js', __FILE__),
							'jquery');
			wp_enqueue_script('mfatScript', plugins_url('js/admin-settings-min.js', __FILE__),
							array('jquery', 'tipTipScript'));
		}
		
		/**
		* This function is responsible for exporting the Materially Flat Admin Theme settings_page
		* to a .json file
		*/
		public function process_settings_export() {

			if( empty( $_POST['mfat_action'] ) || 'export_settings' != $_POST['mfat_action'] )
				return;

			if( ! wp_verify_nonce( $_POST['mfat_export_nonce'], 'mfat_export_nonce' ) )
				return;

			if( ! current_user_can( 'manage_options' ) )
				return;
			
			$admin_settings = get_option('mfat_theme_options');
			$login_settings = get_option('mfat_login_options');
			$mfat_settings = get_option('mfat_settings_options');
			$settings = array($admin_settings, $login_settings, $mfat_settings);

			ignore_user_abort( true );

			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=mfat-settings-export-' . date( 'm-d-Y' ) . '.json' );
			header( "Expires: 0" );

			echo json_encode( $settings );
			exit;
		}

		/**
		* Function responsible for importing Materially Flat Admin Theme settings from a json file
		* It also compiles the sass file created as a result of the importation to css.
		*/
		public function process_settings_import() {

			if( empty( $_POST['mfat_action'] ) || 'import_settings' != $_POST['mfat_action'] )
				return;

			if( ! wp_verify_nonce( $_POST['mfat_import_nonce'], 'mfat_import_nonce' ) )
				return;

			if( ! current_user_can( 'manage_options' ) )
				return;

			$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

			if( $extension != 'json' ) {
				wp_die( __( 'Please upload a valid .json file' ) );
			}

			$import_file = $_FILES['import_file']['tmp_name'];

			if( empty( $import_file ) ) {
				wp_die( __( 'Please upload a file to import' ) );
			}

			// Retrieve the settings from the file and convert the json object to an array.
			$settings = json_decode( file_get_contents( $import_file ), true );
			$admin_settings = $settings[0];
			$login_settings = $settings[1];
			$mfat_settings = $settings[2];
			
			$admin_array = array();
			$login_array = array();
			
			//get the mfat utility class.
			require_once(MFAT_DIR . '/inc/library-class.php');
			$library_class = new MFAT_Library();
			
			//add the admin settings to the admin array
			if($admin_settings['include_img'] == 'yes') 
				$admin_array['background-image'] = "'$admin_settings[background_img]'";
			else $admin_array['background-image'] = "";
			
			if(strpos($admin_settings['font_style'], " ") > -1) 
				$admin_array['font-style'] = "'$admin_settings[font_style]'";
			else $admin_array['font-style'] = $admin_settings['font_style'];
			
			//check to see if font chosen is default.
			if(strcasecmp('default', $admin_array['font-style']) == 0)
				$admin_array['font-style'] = "'Open Sans'";
				
			$admin_array['font-color'] = $admin_settings['font_color'];
			$admin_array['link-color'] = $admin_settings['link_color'];
			$admin_array['hover-link-color'] = $admin_settings['hover_link_color'];
			
			//add the login settings to the login array
			if($login_settings['include_img'] == 'on') 
				$login_array['background-image'] = "'$login_settings[background_img]'";
			else $login_array['background-image'] = "";
			
			if(strpos($login_settings['font_style'], " ") > -1) 
				$login_array['font-style'] = "'$login_settings[font_style]'";
			else $login_array['font-style'] = $login_settings['font_style'];
			
			//check to see if font chosen is default.
			if(strcasecmp('default', $login_array['font-style']) == 0)
				$login_array['font-style'] = "'Open Sans'";
			
			$login_array['font-color'] = $login_settings['font_color'];
			$login_array['link-color'] = $login_settings['link_color'];
			$login_array['hover-link-color'] = $login_settings['hover_link_color'];
			
			$library_class->compile_imported_sass($admin_array, $login_array);
			
			update_option( 'mfat_theme_options', $admin_settings);
			update_option( 'mfat_login_options', $login_settings);
			update_option( 'mfat_settings_options', $mfat_settings);
			
			$page_url = esc_url(admin_url(add_query_arg(array('page' => 'mfat-settings'), 'admin.php')));
			wp_safe_redirect($page_url); exit;

		}

		
		public function settings_page(){
			$languageslug = self::language_slug;
			//Get the various links of the various sections
            $themelink = esc_url(admin_url(add_query_arg(array('page' => 'mfat-main'),'admin.php')));
            $loginlink = esc_url(admin_url(add_query_arg( array( 'page' => 'mfat-login' ), 'admin.php')) );
            $settingslink = esc_url(admin_url(add_query_arg(array('page' => 'mfat-settings'), 'admin.php')));
		?>
			<div class="wrap">
             	<h2 class="nav-tab-wrapper">
            		<a href="<?php echo $themelink; ?>" class = "nav-tab"><?php _e('Admin Theme Settings', $languageslug) ?></a>
            		<a href="<?php echo $loginlink; ?>" class = "nav-tab"><?php _e('Login Theme Settings', $languageslug) ?></a>
            		<a href="<?php echo $settingslink; ?>" class = "nav-tab nav-tab-active"><?php _e('Settings', $languageslug) ?></a>
            	</h2>
				<h2><?php __('Settings', $languageslug); ?></h2>
				<form method="post" action="options.php" id="<?php echo self::settings_form_id; ?>">
				<table class="form-table">
					<?php 
						settings_fields(self::settings_group_name);
						do_settings_sections(self::settings_page);
					?>
				</table>
				<?php submit_button(); ?>
			
				</form>
				<div class="metabox-holder">
					<div class="postbox">
						<h3><span><?php _e( 'Export Settings' ); ?></span></h3>
						<div class="inside">
							<p><?php _e( 'Export your Materially Flat Admin Theme settings as a .json file. This allows you to easily import the your configuration into another site.' ); ?></p>
							<form method="post">
								<p><input type="hidden" name="mfat_action" value="export_settings" /></p>
								<p>
									<?php wp_nonce_field( 'mfat_export_nonce', 'mfat_export_nonce' ); ?>
									<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->

					<div class="postbox">
						<h3><span><?php _e( 'Import Settings' ); ?></span></h3>
						<div class="inside">
							<p><?php _e( 'Import your Materially Flat Admin Theme settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.' ); ?></p>
							<form method="post" enctype="multipart/form-data">
								<p>
									<input type="file" name="import_file"/>
								</p>
								<p>
									<input type="hidden" name="mfat_action" value="import_settings" />
									<?php wp_nonce_field( 'mfat_import_nonce', 'mfat_import_nonce' ); ?>
									<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->
				</div><!-- .metabox-holder -->
			</div>
	<?php
		}
		
		public function register_settings(){
			$languageslug = self::language_slug;
			
			register_setting(
				self::settings_group_name,
				$this->optionname,
				array($this, 'validate_settings')
			);
			
			add_settings_section(
				self::settings_section,
				'',
				array($this, 'section'),
				self::settings_page
			);
			
			
			add_settings_field(
				'google-api-key',
				__('Google API Key', $languageslug),
				array($this, 'google_key'),
				self::settings_page,
				self::settings_section
			);
		}
		
		public function section(){
		}
		
		/**
		* This function is responsible for outputting the google api key option
		*/
		
		public function google_key(){
			@$option = $this->option['google_api_key'];
			$name = $this->optionname;
			$languageslug = self::language_slug;
			echo '<span class="helptip" data-tip="'.__("This will be the API key for retrieving the list of google fonts.",$languageslug).'">?</span>'.
				'<input id="google-api-key" name="'.$name.'[google_api_key]" type="text" value="'.esc_attr($option).'">'.
				'<p class="description hide-if-js">'.__('This will be the API key for retrieving the list of google fonts.', $languageslug).'</p>';
		}
		
		public function validate_settings($input){
			$input['google_api_key'] = sanitize_text_field($input['google_api_key']);
			return $input;
		}
	}//end class