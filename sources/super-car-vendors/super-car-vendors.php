<<<<<<< HEAD
<?php
/*
Plugin Name: Super Car Vendors
Plugin URI: http://cms.web-medias.com/supercarvendors
Description: This plugin create a new custom post_type « SuperCarVendors » and allows to manipulate brands and models of cars.
Version: 1.0
Company: ##############
Author: Sébastien Brémond
Author URI: http://cms.web-medias.com/supercarvendors/authors
License: GPL2
Text Domain: supercarvendors
Domain Path: /languages
*/


// Exit if accessed directly.
if ( ! function_exists('add_action') || ! defined( 'ABSPATH' ) )
	exit();




/* ***************** *******************************************************************
 * WordPress Plugin | SuperCarVendors.
 * ***************** *******************************************************************
 * @author Sebastien Bremond (IncludE)
 * @studio ##############
 * @client <active-project>
 * 
 * Plugin Class declaration based on the WordPress Plugin API and WP mechanisms.
 * Controls the plugin, as well as activation, and deactivation
 */

class SuperCarVendors {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'Super Car Vendors';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'supercarvendors';

	/**
	 * Unique plugin post type identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_post_type = 'supercarvendors';

	/**
	 * Plugin textdomain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $domain = 'supercarvendors';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;


	static $stack;



	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		

		// Fires a hook before the class is set up.
		do_action( $this->plugin_slug .'_pre_init' );


		// Loads the plugin textdomain first!
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 2 );


		// Runs hook once Plugin has been initialized.
		// Adds post type and taxonomy first to be initialized before adding meta box...
		add_action( 'init', array( $this, 'supercarvendors_plugin_post_type' ), 1 );


		// Removes the 'add media' button for this custom post type.
		add_action( 'admin_head', array( $this, 'adapt_plugin_post_type' ), 1 );


		// Builds the meta box system and defines the backup method.
		add_action( 'add_meta_boxes', array( $this, 'supercarvendors_plugin_add_meta_box') );
		add_action( 'save_post', array( $this, 'supercarvendors_plugin_save_meta_box_data') );


		// Adds shortcode to embed into posts, pages,...
		add_shortcode( $this->plugin_post_type , array(&$this, 'supercarvendors_plugin_add_shortcode'));

		// Loads the plugin back-end behavior (Javascript files).
		add_action('admin_enqueue_scripts', array(&$this, 'supercarvendors_plugin_admin_head'));

		// Loads the needs for front-end.
		//add_action ('wp_enqueue_scripts', array(&$this, 'supercarvendors_plugin_frontend_head'));


		// Changes the default placeholder text of the global Post input title area.
		add_filter('enter_title_here', array(&$this, 'supercarvendors_plugin_change_title_placeholder'));

		// Adds a smallest box just after the title.
		add_filter('edit_form_after_title', array(&$this, 'supercarvendors_plugin_add_content_after_editor'));


		// Allows to custom the WP List table by adding custom column in the UI View and add some informations.
		add_filter('manage_'.$this->plugin_post_type.'_posts_columns', array(&$this, 'supercarvendors_plugin_columns_head'),  10);


	}





	/**
	 * Loads the plugin textdomain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->domain;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}





	/**
	 * Adapts the custom post type and other stuffs arround it to fit properly as the needs.
	 *
	 * @since 1.0.0
	 */
	public function adapt_plugin_post_type() {

		$screen = get_current_screen();

		// Removes the 'Add media' button for the WP Editor.
		if( $this->plugin_post_type == $screen->post_type ){
			remove_action( 'media_buttons', 'media_buttons' );
		}

		// No excerpt, nor comments needed in the plugin admin UI.
		//remove_post_type_support( $this->plugin_post_type, 'editor');
		remove_post_type_support( $this->plugin_post_type, 'excerpt' );
		remove_post_type_support( $this->plugin_post_type, 'comments' );
		remove_post_type_support( $this->plugin_post_type, 'custom-fields');

	}





	/**
	 * Creates the related custom post type for the plugin
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_post_type() {

		//register_taxonomy_for_object_type('category', $this->plugin_post_type ); // Register Taxonomies for Category

		//register_taxonomy_for_object_type('post_tag', $this->plugin_post_type ); // Register Taxonomies for Post tag

		register_post_type( $this->plugin_post_type , // Register Custom Post Type
			array(
			'labels'		=> array(
				'name'				  => __('Super Cars Vendors', $this->domain), // Rename these to suit
				'singular_name'		 => __('Super Car Vendors', $this->domain),
				'add_new'			   => __('Nouveau constructeur', $this->domain),
				'add_new_item'		  => __('Ajout d\'un nouveau contructeur', $this->domain),
				'edit'				  => __('Modifier', $this->domain),
				'edit_item'			 => __('Modifier un constructeur', $this->domain),
				'new_item'			  => __('Nouveau constructeur', $this->domain),
				'view'				  => __('Voir le constructeur', $this->domain),
				'view_item'			 => __('Afficher', $this->domain),
				'search_items'		  => __('Rechercher un constructeur', $this->domain),
				'not_found'			 => __('Aucun constructeur trouvé', $this->domain),
				'not_found_in_trash'	=> __('Aucun constructeur trouvé dans la corbeille', $this->domain)
			),
			'public'		=> true,
			'hierarchical'  => true, // Allows your posts to behave like Hierarchy Pages
			'has_archive'   => true,
			'supports'	  => array(
				'editor'		/**/
			   ,'title'		 /**/
			   ,'thumbnail'	 /**/
			/* ,'excerpt'	   /**/
			/* ,'custom-fields' /**/
			/* ,'comments'	  /**/
			),
			'can_export'	=> true, // Allows export in Tools > Export
			'menu_icon'	 => 'dashicons-businessman',
			'menu_position' => 100,
			'taxonomies'	=> array(
			/*
				'post_tag',
				'category',
				'supercarvendors_taxonomy'
			/**/
			) // Add Category and Post Tags support
		));



	}





	/**
	 * Loads the plugin behaviors.
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_admin_head() {

		// Enqueue jQuery UI CSS
		//
		// UI Style (prefered CDN vendors - Note the SSL negociation)
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
		wp_enqueue_script( 'jquery-ui-style', '//code.jquery.com/ui/1.11.4/jquery-ui.js' ); // Distant git from jQuery CDN


		wp_enqueue_script( 'plugin-supercarvendors-runtime-apps', plugins_url('/core/js/plugin-runtime.js', __FILE__), array(), null, false);

	}





	/**
	 * Customs the title placeholder into the admin UI
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_change_title_placeholder($title) {

		$screen = get_current_screen();
		
		if( $this->plugin_post_type == $screen->post_type ){
			$title = __('Marque du constructeur', $this->domain);
		}

		return $title;

	}





	/**
	 * Adds a custom column header to the posttype table list.
	 */
	public function supercarvendors_plugin_columns_head($defaults) {
		
		$defaults['title'] = __( 'Marque du constructeur', $this->domain );
		
		return $defaults;

	}





	/**
	 * Adds a postbox after app title / before content editor.
	 */
	public function supercarvendors_plugin_add_content_after_editor() {

		$screen = get_current_screen();

		// Shows this panel only on the admin page of custom post defined by the <plugin_post_type>
		if( $this->plugin_post_type == $screen->post_type ){

            /* Start : HTML Output */ ?>
			<a name="wp_content_area"></a>
			<div class="postbox" style="margin-top:20px; margin-bottom:5px;">
				<h2 style="color:#0c7cb6;"><?php _e( 'Liste de modèles', $this->domain ); ?></h2>
				<div class="inside" style="padding-bottom:5px;">
					<p>
						<span class="dashicons-before dashicons-arrow-down"></span>
						<?php _e( 'Saisir les modèles appartenant à ce contructeur', $this->domain ); ?>
					</p>
					<em><?php _e( 'Un modèle par ligne', $this->domain ); ?></em>
				</div>
			</div>

            <?php /* End : HTML Output */
		}
		
	}





	/**
	 * Adds a box to the main column on the Post and Page edit screens.
	 */
	public function supercarvendors_plugin_add_meta_box($postType) {

		$screens = array( $this->plugin_post_type ); // Allows to run with this screen.

		foreach ( $screens as $screen ) {

		/*  // Memo !
		 *	add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
		 *	@param $screen ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type-slug')
		 *  @param $context ('normal', 'advanced', or 'side')
		 *  @param $priority ('high', 'core', 'default' or 'low')
		 */

			// Builds the MetaBox container.
			$meta_box_container_title = '<strong style="display:block; color:#0c7cb6; font-size:23px;">'. $this->plugin_name .'</strong>'.
			 							__( 'Gestion des constructeurs de véhicules « Super Cars Vendors »', $this->domain );
			
			add_meta_box(
				'supercarvendors_plugin_sectionid', 
				$meta_box_container_title,
				array( $this, 'supercarvendors_plugin_meta_box_callback' ),
				$screen, 
				'advanced', 
				'core'
			);

		}
	}





	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function supercarvendors_plugin_meta_box_callback( $post ) {

		// Adds a nonce field so we can check for it later.
		wp_nonce_field( 'supercarvendors_plugin_meta_box', 'supercarvendors_plugin_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */



		// Notify if something missing !
		//
		$REQUIRED_CLASS = 'SuperCar'; // Define the Class dependence.

		if ( !class_exists( $REQUIRED_CLASS )) { ?>

		<div class="error">
			<p>
				<span class="spinner"></span> <strong><?php _e( 'Module absent !', $this->domain ); ?></strong> 
				<?php _e( 'La classe', $this->domain ); ?> « <u><?php echo $REQUIRED_CLASS; ?> »</u> 
				<?php _e( "d'un plugin dépendant est absente.", $this->domain ); ?><br />
				<?php _e( "Le plugin n'a pas été correctement installé, un module ou une dépendance est deffectueux ou bien une erreur d'installation est survenue.", $this->domain ); ?><br />
				<?php _e( "Merci de prévenir votre administrateur et lui remonter le code d'erreur", $this->domain ); ?> 
				<code>(<?php echo $this->plugin_name; ?>) <?php echo $REQUIRED_CLASS; ?>::PHP_Class_Error [class_exists=false]</code>.
			</p>
		</div>
		
		<?php } ?>



		<?php
		// End: Meta box printing

	}






	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function supercarvendors_plugin_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Checks if our nonce is set.
		if ( ! isset( $_POST['supercarvendors_plugin_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['supercarvendors_plugin_meta_box_nonce'], 'supercarvendors_plugin_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Checks the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'post' == $_POST['post_type'] )  {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		}

		/* OK, it's safe for us to save the data now. */
		// No saved meta fields here, just consider this to set up properly the WP Plugin/Addon mechanism ! 


	}





	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The SuperCarVendors object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SuperCarVendors ) ) {
			self::$instance = new SuperCarVendors();
		}

		return self::$instance;

	}

}

// Loads the main plugin class.
$SuperCarVendors = SuperCarVendors::get_instance();
=======
<?php
/*
Plugin Name: Super Car Vendors
Plugin URI: http://cms.web-medias.com/supercarvendors
Description: This plugin create a new custom post_type « SuperCarVendors » and allows to manipulate brands and models of cars.
Version: 1.0
Company: ##############
Author: Sébastien Brémond
Author URI: http://cms.web-medias.com/supercarvendors/authors
License: GPL2
Text Domain: supercarvendors
Domain Path: /languages
*/


// Exit if accessed directly.
if ( ! function_exists('add_action') || ! defined( 'ABSPATH' ) )
	exit();




/* ***************** *******************************************************************
 * WordPress Plugin | SuperCarVendors.
 * ***************** *******************************************************************
 * @author Sebastien Bremond (IncludE)
 * @studio ##############
 * @client <active-project>
 * 
 * Plugin Class declaration based on the WordPress Plugin API and WP mechanisms.
 * Controls the plugin, as well as activation, and deactivation
 */

class SuperCarVendors {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'Super Car Vendors';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'supercarvendors';

	/**
	 * Unique plugin post type identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_post_type = 'supercarvendors';

	/**
	 * Plugin textdomain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $domain = 'supercarvendors';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;


	static $stack;



	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		

		// Fires a hook before the class is set up.
		do_action( $this->plugin_slug .'_pre_init' );


		// Loads the plugin textdomain first!
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 2 );


		// Runs hook once Plugin has been initialized.
		// Adds post type and taxonomy first to be initialized before adding meta box...
		add_action( 'init', array( $this, 'supercarvendors_plugin_post_type' ), 1 );


		// Removes the 'add media' button for this custom post type.
		add_action( 'admin_head', array( $this, 'adapt_plugin_post_type' ), 1 );


		// Builds the meta box system and defines the backup method.
		add_action( 'add_meta_boxes', array( $this, 'supercarvendors_plugin_add_meta_box') );
		add_action( 'save_post', array( $this, 'supercarvendors_plugin_save_meta_box_data') );


		// Adds shortcode to embed into posts, pages,...
		add_shortcode( $this->plugin_post_type , array(&$this, 'supercarvendors_plugin_add_shortcode'));

		// Loads the plugin back-end behavior (Javascript files).
		add_action('admin_enqueue_scripts', array(&$this, 'supercarvendors_plugin_admin_head'));

		// Loads the needs for front-end.
		//add_action ('wp_enqueue_scripts', array(&$this, 'supercarvendors_plugin_frontend_head'));


		// Changes the default placeholder text of the global Post input title area.
		add_filter('enter_title_here', array(&$this, 'supercarvendors_plugin_change_title_placeholder'));

		// Adds a smallest box just after the title.
		add_filter('edit_form_after_title', array(&$this, 'supercarvendors_plugin_add_content_after_editor'));


		// Allows to custom the WP List table by adding custom column in the UI View and add some informations.
		add_filter('manage_'.$this->plugin_post_type.'_posts_columns', array(&$this, 'supercarvendors_plugin_columns_head'),  10);


	}





	/**
	 * Loads the plugin textdomain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->domain;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}





	/**
	 * Adapts the custom post type and other stuffs arround it to fit properly as the needs.
	 *
	 * @since 1.0.0
	 */
	public function adapt_plugin_post_type() {

		$screen = get_current_screen();

		// Removes the 'Add media' button for the WP Editor.
		if( $this->plugin_post_type == $screen->post_type ){
			remove_action( 'media_buttons', 'media_buttons' );
		}

		// No excerpt, nor comments needed in the plugin admin UI.
		//remove_post_type_support( $this->plugin_post_type, 'editor');
		remove_post_type_support( $this->plugin_post_type, 'excerpt' );
		remove_post_type_support( $this->plugin_post_type, 'comments' );
		remove_post_type_support( $this->plugin_post_type, 'custom-fields');

	}





	/**
	 * Creates the related custom post type for the plugin
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_post_type() {

		//register_taxonomy_for_object_type('category', $this->plugin_post_type ); // Register Taxonomies for Category

		//register_taxonomy_for_object_type('post_tag', $this->plugin_post_type ); // Register Taxonomies for Post tag

		register_post_type( $this->plugin_post_type , // Register Custom Post Type
			array(
			'labels'		=> array(
				'name'				  => __('Super Cars Vendors', $this->domain), // Rename these to suit
				'singular_name'		 => __('Super Car Vendors', $this->domain),
				'add_new'			   => __('Nouveau constructeur', $this->domain),
				'add_new_item'		  => __('Ajout d\'un nouveau contructeur', $this->domain),
				'edit'				  => __('Modifier', $this->domain),
				'edit_item'			 => __('Modifier un constructeur', $this->domain),
				'new_item'			  => __('Nouveau constructeur', $this->domain),
				'view'				  => __('Voir le constructeur', $this->domain),
				'view_item'			 => __('Afficher', $this->domain),
				'search_items'		  => __('Rechercher un constructeur', $this->domain),
				'not_found'			 => __('Aucun constructeur trouvé', $this->domain),
				'not_found_in_trash'	=> __('Aucun constructeur trouvé dans la corbeille', $this->domain)
			),
			'public'		=> true,
			'hierarchical'  => true, // Allows your posts to behave like Hierarchy Pages
			'has_archive'   => true,
			'supports'	  => array(
				'editor'		/**/
			   ,'title'		 /**/
			   ,'thumbnail'	 /**/
			/* ,'excerpt'	   /**/
			/* ,'custom-fields' /**/
			/* ,'comments'	  /**/
			),
			'can_export'	=> true, // Allows export in Tools > Export
			'menu_icon'	 => 'dashicons-businessman',
			'menu_position' => 100,
			'taxonomies'	=> array(
			/*
				'post_tag',
				'category',
				'supercarvendors_taxonomy'
			/**/
			) // Add Category and Post Tags support
		));



	}





	/**
	 * Loads the plugin behaviors.
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_admin_head() {

		// Enqueue jQuery UI CSS
		//
		// UI Style (prefered CDN vendors - Note the SSL negociation)
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
		wp_enqueue_script( 'jquery-ui-style', '//code.jquery.com/ui/1.11.4/jquery-ui.js' ); // Distant git from jQuery CDN


		wp_enqueue_script( 'plugin-supercarvendors-runtime-apps', plugins_url('/core/js/plugin-runtime.js', __FILE__), array(), null, false);

	}





	/**
	 * Customs the title placeholder into the admin UI
	 *
	 * @since 1.0.0
	 */
	public function supercarvendors_plugin_change_title_placeholder($title) {

		$screen = get_current_screen();
		
		if( $this->plugin_post_type == $screen->post_type ){
			$title = __('Marque du constructeur', $this->domain);
		}

		return $title;

	}





	/**
	 * Adds a custom column header to the posttype table list.
	 */
	public function supercarvendors_plugin_columns_head($defaults) {
		
		$defaults['title'] = __( 'Marque du constructeur', $this->domain );
		
		return $defaults;

	}





	/**
	 * Adds a postbox after app title / before content editor.
	 */
	public function supercarvendors_plugin_add_content_after_editor() {

		$screen = get_current_screen();

		// Shows this panel only on the admin page of custom post defined by the <plugin_post_type>
		if( $this->plugin_post_type == $screen->post_type ){

            /* Start : HTML Output */ ?>
			<a name="wp_content_area"></a>
			<div class="postbox" style="margin-top:20px; margin-bottom:5px;">
				<h2 style="color:#0c7cb6;"><?php _e( 'Liste de modèles', $this->domain ); ?></h2>
				<div class="inside" style="padding-bottom:5px;">
					<p>
						<span class="dashicons-before dashicons-arrow-down"></span>
						<?php _e( 'Saisir les modèles appartenant à ce contructeur', $this->domain ); ?>
					</p>
					<em><?php _e( 'Un modèle par ligne', $this->domain ); ?></em>
				</div>
			</div>

            <?php /* End : HTML Output */
		}
		
	}





	/**
	 * Adds a box to the main column on the Post and Page edit screens.
	 */
	public function supercarvendors_plugin_add_meta_box($postType) {

		$screens = array( $this->plugin_post_type ); // Allows to run with this screen.

		foreach ( $screens as $screen ) {

		/*  // Memo !
		 *	add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
		 *	@param $screen ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type-slug')
		 *  @param $context ('normal', 'advanced', or 'side')
		 *  @param $priority ('high', 'core', 'default' or 'low')
		 */

			// Builds the MetaBox container.
			$meta_box_container_title = '<strong style="display:block; color:#0c7cb6; font-size:23px;">'. $this->plugin_name .'</strong>'.
			 							__( 'Gestion des constructeurs de véhicules « Super Cars Vendors »', $this->domain );
			
			add_meta_box(
				'supercarvendors_plugin_sectionid', 
				$meta_box_container_title,
				array( $this, 'supercarvendors_plugin_meta_box_callback' ),
				$screen, 
				'advanced', 
				'core'
			);

		}
	}





	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function supercarvendors_plugin_meta_box_callback( $post ) {

		// Adds a nonce field so we can check for it later.
		wp_nonce_field( 'supercarvendors_plugin_meta_box', 'supercarvendors_plugin_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */



		// Notify if something missing !
		//
		$REQUIRED_CLASS = 'SuperCar'; // Define the Class dependence.

		if ( !class_exists( $REQUIRED_CLASS )) { ?>

		<div class="error">
			<p>
				<span class="spinner"></span> <strong><?php _e( 'Module absent !', $this->domain ); ?></strong> 
				<?php _e( 'La classe', $this->domain ); ?> « <u><?php echo $REQUIRED_CLASS; ?> »</u> 
				<?php _e( "d'un plugin dépendant est absente.", $this->domain ); ?><br />
				<?php _e( "Le plugin n'a pas été correctement installé, un module ou une dépendance est deffectueux ou bien une erreur d'installation est survenue.", $this->domain ); ?><br />
				<?php _e( "Merci de prévenir votre administrateur et lui remonter le code d'erreur", $this->domain ); ?> 
				<code>(<?php echo $this->plugin_name; ?>) <?php echo $REQUIRED_CLASS; ?>::PHP_Class_Error [class_exists=false]</code>.
			</p>
		</div>
		
		<?php } ?>



		<?php
		// End: Meta box printing

	}






	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function supercarvendors_plugin_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Checks if our nonce is set.
		if ( ! isset( $_POST['supercarvendors_plugin_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['supercarvendors_plugin_meta_box_nonce'], 'supercarvendors_plugin_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Checks the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'post' == $_POST['post_type'] )  {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		}

		/* OK, it's safe for us to save the data now. */
		// No saved meta fields here, just consider this to set up properly the WP Plugin/Addon mechanism ! 


	}





	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The SuperCarVendors object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SuperCarVendors ) ) {
			self::$instance = new SuperCarVendors();
		}

		return self::$instance;

	}

}

// Loads the main plugin class.
$SuperCarVendors = SuperCarVendors::get_instance();
>>>>>>> 69d800751f0fdd3e6c15326a85c1175d8e48ca65
