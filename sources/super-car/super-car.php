<<<<<<< HEAD
<?php
/*
Plugin Name: Super Car
Plugin URI: http://cms.web-medias.com/supercar
Description: This plugin create a new custom post_type « SuperCar » and allows to manipulate entries as cars.
Version: 1.0
Company: ##############
Author: Sébastien Brémond
Author URI: http://cms.web-medias.com/supercar/authors
License: GPL2
Text Domain: supercar
Domain Path: /languages
*/


// Exit if accessed directly.
if ( ! function_exists('add_action') || ! defined( 'ABSPATH' ) )
    exit();




/* ***************** *******************************************************************
 * WordPress Plugin | SuperCar.
 * ***************** *******************************************************************
 * @author Sebastien Bremond (IncludE)
 * @studio ##############
 * @client <active-project>
 * 
 * Plugin Class declaration based on the WordPress Plugin API and WP mechanisms.
 * Controls the plugin, as well as activation, and deactivation
 */

class SuperCar {

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
    public $plugin_name = 'Super Car';

    /**
     * The taxonomy name of the plugin.
     *
     * @since 1.0.0
     *
     * @var array of string
     */
    public $plugin_name_taxonomy = array(
        'plural'   => "",
        'singular' => ""
    );

    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'supercar';

    /**
     * Unique plugin post type identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_post_type = 'supercar';

    /**
     * Plugin textdomain.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $domain = 'supercar';

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
        add_action( 'init', array( $this, 'supercar_plugin_post_type' ), 1 );


        // Removes the 'add media' button for this custom post type.
        add_action( 'admin_head', array( $this, 'adapt_plugin_post_type' ), 1 );


        // Builds the meta box system and defines the backup method.

        do_action( $this->plugin_slug .'_before_metabox' ); // Fires a hook before make the meta box.
        add_action( 'add_meta_boxes', array( $this, 'supercar_plugin_add_meta_box') );
        do_action( $this->plugin_slug .'_after_metabox' ); // Fires a hook after make the meta box.
        add_action( 'save_post', array( $this, 'supercar_plugin_save_meta_box_data') );

        // Adds shortcode to embed into posts, pages,...
        add_shortcode( $this->plugin_post_type , array(&$this, 'supercar_plugin_add_shortcode'));

        // Loads the plugin back-end behavior (Javascript files).
        add_action('admin_enqueue_scripts', array(&$this, 'supercar_plugin_admin_head'));

        // Loads the needs for front-end.
        //add_action ('wp_enqueue_scripts', array(&$this, 'supercar_plugin_frontend_head'));


        // Changes the default placeholder text of the global Post input title area.
        add_filter('enter_title_here', array(&$this, 'supercar_plugin_change_title_placeholder'));

        // Adds a smallest box just after the title.
        add_filter('edit_form_after_title', array(&$this, 'supercar_plugin_add_content_after_editor'));


        // Allows to custom the WP List table by adding custom column in the UI View and add some informations.
        add_filter('manage_'.$this->plugin_post_type.'_posts_columns', array(&$this, 'supercar_plugin_columns_head'),  10);
        add_action('manage_'.$this->plugin_post_type.'_posts_custom_column', array(&$this, 'supercar_plugin_columns_content'), 10, 2);

        // Fires a hook when the WP Plugin MetaBox was made.
        do_action( $this->plugin_slug .'_when_ready' );

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

        // Allows to manage supports for any use of excerpt.
        do_action( $this->plugin_slug .'_after_supports' );

    }





    /**
     * Creates the related custom post type for the plugin
     *
     * @since 1.0.0
     */
    public function supercar_plugin_post_type() {

        //register_taxonomy_for_object_type('category', $this->plugin_post_type ); // Register Taxonomies for Category

        //register_taxonomy_for_object_type('post_tag', $this->plugin_post_type ); // Register Taxonomies for Post tag

        register_post_type( $this->plugin_post_type , // Register Custom Post Type
            array(
            'labels'        => array(
                'name'                  => __('Super Cars', $this->domain),
                'singular_name'         => __('Super Car', $this->domain),
                'add_new'               => __('Nouvelle', $this->domain),
                'add_new_item'          => __('Ajout d\'une nouvelle Super Car', $this->domain),
                'edit'                  => __('Modifier', $this->domain),
                'edit_item'             => __('Modifier une Super Car', $this->domain),
                'new_item'              => __('Nouvelle Super Car', $this->domain),
                'view'                  => __('Voir la Super Car', $this->domain),
                'view_item'             => __('Afficher', $this->domain),
                'search_items'          => __('Rechercher une Super Car', $this->domain),
                'not_found'             => __('Aucune Super Car trouvée', $this->domain),
                'not_found_in_trash'    => __('Aucune Super Car trouvée dans la corbeille', $this->domain)
            ),
            'public'        => true,
            'hierarchical'  => false, // Allows your posts to behave like Hierarchy Pages
            'has_archive'   => false,
            'supports'      => array(
                'editor'        /**/
               ,'title'      /**/
               ,'thumbnail'  /**/
            /* ,'excerpt'      /**/
            /* ,'custom-fields' /**/
            /* ,'comments'    /**/
            ),
            'can_export'    => true, // Allows export in Tools > Export
            'menu_icon'     => 'dashicons-performance',
            'menu_position' => 100,
            'rewrite' => array('slug' => 'vehicule','with_front' => true),
            'taxonomies'    => array(
            /*
                'post_tag',
                'category',
                'supercar_taxonomy'
            /**/
            ) // Add Category and Post Tags support
        ));




    /*
        // Add admin side bar menu taxonomy
        $labels_taxonomy = array(
            'name'              => _x( $this->plugin_name_taxonomy['plural'], $this->domain ),
            'singular_name'     => _x( $this->plugin_name_taxonomy['singular'], $this->domain ),
            'search_items'      => __( 'Rechercher', $this->domain ),
            'all_items'         => __( 'Toutes', $this->domain ),
            'parent_item'       => __( 'Parent', $this->domain ),
            'parent_item_colon' => __( 'Parent :', $this->domain ),
            'edit_item'         => __( 'Edition', $this->domain ),
            'update_item'       => __( 'Mettre à jour', $this->domain ),
            'add_new_item'      => __( 'Nouvelle', $this->domain ),
            'new_item_name'     => __( 'Nouveau nom', $this->domain ),
            'menu_name'         => __( $this->plugin_name_taxonomy['plural'] ),
        );

        $args_taxonomy = array(
            'hierarchical'      => true,
            'labels'            => $labels_taxonomy,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'supercar_taxonomy' ),
        );

        //register_taxonomy( 'supercar_taxonomy', array( $this->plugin_post_type ), $args_taxonomy );
    /**/

    }





    /**
     * Loads the plugin behavior.
     *
     * @since 1.0.0
     */
    public function supercar_plugin_admin_head() {

        // Enqueue jQuery UI CSS
        //
        // UI Style (prefered CDN vendors - Note the SSL negociation)
        wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
        wp_enqueue_script( 'jquery-ui-style', '//code.jquery.com/ui/1.11.4/jquery-ui.js' ); // Distant git from jQuery CDN

        // Enqueue Datepicker
        //
        // Datepicker widget (finally the entries have no calendar date)
        //wp_enqueue_script( 'jquery-ui-datepicker', plugins_url('/core/js/jquery-datepicker.js', __FILE__), false, null); // Local available

        // Internationalisaton | FR options & Elements scripting behaviors (attaching datepicker to needed IDs if used)
        wp_enqueue_script( 'plugin-supercar-runtime-apps', plugins_url('/core/js/plugin-runtime.js', __FILE__), array(), null, false);
        wp_enqueue_style( 'plugin-supercar-runtime-apps', plugins_url('/core/css/plugin-runtime.css', __FILE__), array(), null );

    }





    /**
     * Customs the title placeholder into the admin UI
     *
     * @since 1.0.0
     */
    public function supercar_plugin_change_title_placeholder($title) {

        $screen = get_current_screen();
        
        if( $this->plugin_post_type == $screen->post_type ){
            $title = __('Libellé de ce véhicule...', $this->domain);
        }

        return $title;

    }





    /**
     * Adds a custom column header to the posttype table list.
     */
    public function supercar_plugin_columns_head($defaults) {
        
        $defaults['title'] = __( 'Libellé du véhicule', $this->domain );
        $defaults['entry_enabled'] = __( 'État', $this->domain );
        
        return $defaults;

    }





    /**
     * Adds (and fill) a custom column cell to the posttype table list.
     */
    public function supercar_plugin_columns_content($column_name, $post_ID) {

        
        // Negotiates the current processed column...
        if ($column_name == 'entry_enabled') {

            // Fetches the meta value, and shows status.

            // We want to show if this entry is part of the selections.
            $_supercar__selection = get_post_meta( $post_ID, '_supercar__selection', true );

            if( 1 == intval($_supercar__selection) ){
                echo '<span class="dashicons-before dashicons-yes" style="color:#0688A2;"></span>'. __( 'Entrée mise en avant', $this->domain );
            }else{
                echo '<span class="dashicons-before dashicons-no-alt" style="color:#A23D06;"></span> <em>'.__( '(non affectée)', $this->domain ).'</em>';
            }

        }
    }





    /**
     * Adds a postbox after app title / before content editor.
     */
    public function supercar_plugin_add_content_after_editor() {

        $screen = get_current_screen();

        // Shows this panel only on the admin page of custom post defined by the <plugin_post_type>
        if( $this->plugin_post_type == $screen->post_type ){ 

            /* Start : HTML Output (inline style embedded) */ ?>

            <a name="wp_content_area"></a>

            <div class="postbox closed" class="meta-box-sortables" behaviours="togglable" style="margin-top:20px; margin-bottom:5px;">            

                <button type="button" class="handlediv" aria-expanded="true">
                    <span class="screen-reader-text"><strong style="display:block; color:#0c7cb6; font-size:23px;"></strong></span>
                    <span class="toggle-indicator" aria-hidden="true">&#9670;</span>
                </button>
                <h2 class="hndle ui-sortable-handle">
                    <span>
                        <strong style="display:block; color:#0c7cb6; font-size:23px;">
                            <?php echo $this->plugin_name .' » '. __( 'Véhicule', $this->domain ); ?>
                        </strong>
                        <?php _e( 'Informations générales', $this->domain ); ?>
                    </span>
                </h2>

                <div class="inside" style="padding-bottom:5px;">
                    <p>
                        <span class="dashicons-before dashicons-arrow-down"></span>
                        <?php _e( 'La zone ci-dessous permet de détailler les informations ou caractéristiques génarales d\'un véhicule', $this->domain ); ?>
                    </p>
                </div>
            </div>

            <?php /* End : HTML Output */

        }
        
    }





    /**
     * Adds a box to the main column on the Post and Page edit screens.
     * 
     * @param WP Plugin current postType.
     */
    public function supercar_plugin_add_meta_box($postType) {

        $screens = array( $this->plugin_post_type ); // Allows to run with this

        foreach ( $screens as $screen ) {

        /*  // Memo !
         *  add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
         *  @param $screen ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type-slug')
         *  @param $context ('normal', 'advanced', or 'side')
         *  @param $priority ('high', 'core', 'default' or 'low')
         */

            // Builds the MetaBox container.
            $meta_box_container_title = '<strong style="display:block; color:#0c7cb6; font-size:23px;">'. $this->plugin_name .'</strong>'.
                                        __( 'Définition des caractéristiques principales de ce véhicule', $this->domain );
            
            add_meta_box(
                'supercar_plugin_sectionid', 
                $meta_box_container_title,
                array( $this, 'supercar_plugin_meta_box_callback' ),
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
    public function supercar_plugin_meta_box_callback( $post ) {

        // Adds a nonce field so we can check for it later.
        wp_nonce_field( 'supercar_plugin_meta_box', 'supercar_plugin_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        ?>

        <?php
        // Creates a dedicated and unique entry id
        //

            $_supercar__unique_id =  get_post_meta( $post->ID, '_supercar__unique_id', true );
            $_supercar__unique_id = ( !! empty( $_supercar__unique_id ) || "" == $_supercar__unique_id )? $this->get_random_anid() : $_supercar__unique_id;

        ?>
        <input type="hidden" name="_supercar__unique_id" value="<?php echo $_supercar__unique_id; ?>" />




        <?php
        // Notify if something missing !
        //
        $REQUIRED_CLASS = 'SuperCar'; // Defines the Class dependence.

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
        /*
         * Ok, lets start to build the plugin sections that contains all the fields needed
         */
        ?>



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-nametag"><?php _e( 'Identification', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner les caractéristiques d\'identification du véhicule.', $this->domain); ?></label>
            <br class="clear" />
            <em class="inline_label">
                <?php _e('&#10097; Marque &#10093; Modèle', $this->domain); ?>
            </em>
            <br class="clear" />


            <?php
            // Related field | Brands | Models | Ranges
            //  _supercar__brand_collection
            //  _supercar__model_collection
            //  _supercar__brandmodel_version
            //
            // Retreives the Brands/Models/Range colection.
            $brandmodel_collection = $this->get_supercars_brandmodel_posttype_storedin();


            $_supercar__brand_collection   = get_post_meta( $post->ID, '_supercar__brand_collection', true );
            $_supercar__brand_collection   = empty($_supercar__brand_collection)? 'none' : $_supercar__brand_collection;

            $_supercar__model_collection   = get_post_meta( $post->ID, '_supercar__model_collection', true );
            $_supercar__model_collection   = empty($_supercar__model_collection)? '' : $_supercar__model_collection;
            
            $_supercar__brandmodel_version =  get_post_meta( $post->ID, '_supercar__brandmodel_version', true );
            $_supercar__brandmodel_version = ( !! empty( $_supercar__brandmodel_version ) || "" == $_supercar__brandmodel_version )? "" : $_supercar__brandmodel_version;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Marque', $this->domain); ?> : </label>
                        <select name="_supercar__brand_collection">
                            
                            <option value="none"><?php _e( '-- Aucune marque sélectionnée --', $this->domain ); ?></option>

                            <?php
                                $u_brand_list = ( array_unique( array_column($brandmodel_collection, 's_brandmodel__brand', 't_brandmodel__brand') , SORT_REGULAR ) );

                                foreach( (array) $u_brand_list as $brand => $s_brand ) {
                                    $s_brand = strtolower( $s_brand );
                                    $s_brand = preg_replace("/(\s)+/", "_", $s_brand);
                                ?>
                                <option <?php echo $this->is_item_selected( $s_brand, $_supercar__brand_collection, false ); ?> value="<?php echo $s_brand; ?>" data-brand="<?php echo $s_brand; ?>"><?php echo $brand; ?></option>
                                <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Modèle', $this->domain); ?> : </label>
                        <select name="_supercar__model_collection">
                            <option value=""><?php _e( '-- Aucun modèle sélectionnée --', $this->domain ); ?></option>

                            <?php
                                $u_model_list = array_column($brandmodel_collection, 's_brandmodel__brand', 't_brandmodel__model' );
                                foreach( (array) $u_model_list as $model => $s_brand ) {
                                    $s_model = preg_replace("/(\s)+/", "_", strtolower( $model ));
                                ?>
                                <option <?php echo $this->is_item_selected( $s_model, $_supercar__model_collection, false ); ?> value="<?php echo $s_model; ?>" data-brand="<?php echo $s_brand; ?>" data-model="<?php echo $s_model; ?>"><?php echo $model; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Version', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__brandmodel_version" placeholder="<?php _e('(facultatif)', $this->domain); ?>" style="margin: 0px; width: 150px;" value="<?php echo $_supercar__brandmodel_version; ?>" />
                    </th>

                </tr>

                <tr valign="top">

                    <td scope="row" class="non-stackable-row">
                        <em class="inline_label">Appliquer cette sélection comme titre de la fiche véhicule :</em>
                        <button id="selected_brand_model_btn">
                            <strong class="dashicons-before dashicons-admin-post"><label class="inline_label" id="selected_brand_model"></label></strong>
                        </button>
                    </td>
                </tr>

            </tbody></table>


            <hr class="separator_data" />


            <?php
            // Related field | Year
            //  _supercar__dateyear
            //
            // Retreive the meta value of it.
            $_supercar__dateyear = intval( get_post_meta( $post->ID, '_supercar__dateyear', true ) );
            $_supercar__dateyear = ( !! empty( $_supercar__dateyear ) || $_supercar__dateyear==0 )? date('Y') : $_supercar__dateyear;

            $_supercar__km = intval( get_post_meta( $post->ID, '_supercar__km', true ) );
            $_supercar__km = ( !! empty( $_supercar__km ) || $_supercar__km==0 )? 0 : $_supercar__km;
            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Année du véhicule', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__dateyear" class="subtitle" style="margin: 0px; width: 60px;" value="<?php echo $_supercar__dateyear; ?>" />
                        <br class="clear">
                        <em class="block_label">
                            <?php _e('(Année de mise en circulation)', $this->domain); ?>
                        </em>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Kilométrage du véhicule', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__km" style="margin: 0px; width: 70px;" value="<?php echo $_supercar__km; ?>" />
                        <br class="clear">
                        <em class="block_label">
                            <?php _e('(Km. réels au compteurs)', $this->domain); ?>
                        </em>
                    </th>

                </tr>

            </tbody></table>


        </fieldset>
        <br class="clear" />





        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-editor-kitchensink"><?php _e( 'Caractéristiques', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Saisir les informations au sujet du véhicule.', $this->domain); ?></label>
            <br class="clear" />


            <?php
            // Related field | Energies
            //  _supercar__system_energy
            //
            // Retreive the meta value of it.
            $energy_list = $this->get_supercars_energy_posttype_storedin();

            $_supercar__system_energy = get_post_meta( $post->ID, '_supercar__system_energy', true );
            ?>


            <?php
            // Related field | Gearboxes
            //  _supercar__gear_box
            //
            // Retreive the gearbox type of it.
            $gearbox_list = $this->get_supercars_gearbox_posttype_storedin();

            $_supercar__gear_box = get_post_meta( $post->ID, '_supercar__gear_box', true );
            ?>


            <?php
            // Related field | Power Horse
            //  _supercar__power_horse
            //
            // Retreive the power of it.
            $_supercar__power_horse = get_post_meta( $post->ID, '_supercar__power_horse', true );
            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Énergie', $this->domain); ?> : </label>
                        <select name="_supercar__system_energy">
                            <option value="none"><?php _e( '-- Aucune energie sélectionnée --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $energy_list as $energy ) { ?>
                            <option <?php echo $this->is_item_selected( $energy['guid'], $_supercar__system_energy, false ); ?> value="<?php echo $energy['guid']; ?>" ><?php echo $energy['post_title']; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Boite à vitesses', $this->domain); ?> : </label>
                        <select name="_supercar__gear_box">
                            <option value="none"><?php _e( '-- Aucune boite sélectionnée --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $gearbox_list as $gearbox ) { ?>
                            <option <?php echo $this->is_item_selected( $gearbox['guid'], $_supercar__gear_box, false ); ?> value="<?php echo $gearbox['guid']; ?>" ><?php echo $gearbox['post_title']; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Puissance', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__power_horse" style="margin: 0px; width: 50px;" value="<?php echo $_supercar__power_horse; ?>" />
                        <em class="inline_label">
                            <?php _e('(Indication Chevaux Fiscaux)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>


        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-admin-customizer"><?php _e( 'Finition', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Définir le style de finition du véhicule', $this->domain); ?></label>
            <br class="clear" />

            <br class="clear" />

            <?php
            // Related field | Doors
            //  _supercar__doors
            //
            // Retreive the meta value of it.
            $_supercar__doors = intval( get_post_meta( $post->ID, '_supercar__doors', true ) );
            ?>

            <label class="inline_label"><?php _e('Ouverture', $this->domain); ?> : </label>
            <select name="_supercar__doors">
                <option value="none"><?php _e( '-- Faire un choix --', $this->domain ); ?></option>
                
                <option <?php echo $this->is_item_selected( 3, $_supercar__doors, false ); ?> value="3" ><?php _e( '3 portes', $this->domain ); ?></option>
                <option <?php echo $this->is_item_selected( 5, $_supercar__doors, false ); ?> value="5" ><?php _e( '5 portes', $this->domain ); ?></option>

            </select>


            <hr class="separator_data" />


            <?php
            // Related field | Color
            //  _supercar__color
            //
            // Retreive the meta value of it.
            $color_list = $this->get_supercars_color_posttype_storedin();

            $_supercar__color = get_post_meta( $post->ID, '_supercar__color', true );
            $_supercar__color = ($_supercar__color=='')? '#FFFFFF':$_supercar__color;
            ?>

            <label class="inline_label"><?php _e('Couleur du véhicule', $this->domain); ?> : </label>

                            
            <?php foreach( (array) $color_list as $color ) { ?>
            
                <?php if( isset( $color['nblf'] ) ) { ?>
                    <br class="clear" />
                <?php }else{ ?>
                    <span class="color-selector-wrapper">
                        <input type="radio" id="color_<?php echo $color['name']; ?>" name="_supercar__color" value="<?php echo $color['hexcode']; ?>" <?php echo $this->is_check( $color['hexcode'], $_supercar__color, false ); ?> />
                        <label for="color_<?php echo $color['name']; ?>" class="color-selector" style="background-color:<?php echo $color['hexcode']; ?>;" data-color="<?php echo $color['name']; ?>"></label>
                    </span>
                <?php } ?>

            <?php } ?>
            <br class="clear" />


            <hr class="separator_data" />


            <?php
            // Related field | Finition
            //  _supercar__finish
            //
            // Retreive the meta value of it.
            $_supercar__finish =  get_post_meta( $post->ID, '_supercar__finish', true );
            $_supercar__finish = ( !! empty( $_supercar__finish ) || $_supercar__finish=="" )? "(nc)" : $_supercar__finish;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Aspect/finition', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__finish" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__finish; ?>" />
                        <em class="inline_label">
                            <?php _e('(Facultatif - à compléter si spécifique)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-awards"><?php _e( 'Client HelloMotors', $this->domain ); ?></strong></legend>

            <?php
            // Related field | Client HelloMotors
            //  _supercar__client_hello
            //
            // Retreive the client of it.
            $_supercar__client_hello = intval( get_post_meta( $post->ID, '_supercar__client_hello', true ) );
            $_supercar__client_hello = ( !! empty( $_supercar__client_hello ) || $_supercar__client_hello=="" )? 0 : $_supercar__client_hello;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <td scope="row" class="non-stackable-row">
                        <label class="inline_label" for="_supercar__client_hello"><?php _e('Ce véhicule appartient-il à un client HelloMotors ?', $this->domain); ?> </label>
                        <input type="checkbox" name="_supercar__client_hello" value="1" <?php echo $this->is_check( 1, $_supercar__client_hello, false ); ?> >
                    </td>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-clipboard"><?php _e( 'Rapport d\'expertise', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner le document PDF&trade; servant de rapport d\'expertise pour le véhicule', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Expert Report
            //  _supercar__report
            //
            // Retreive the year of it.
            $_supercar__report = get_post_meta( $post->ID, '_supercar__report', true );
            $_supercar__report = ($_supercar__report == 'none')? '' : $_supercar__report;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Rapport d\'expertise', $this->domain); ?> : </label>
                        <select name="_supercar__report">
                            <option value="none"><?php _e( 'Pas encore de rapport d\'expertise à ce jour', $this->domain ); ?></option>
                            
                            <?php
                            $doc_collection = $this->get_documents_from_media_library();
                            foreach( (array) $doc_collection as $document ) { 
                            ?>
                            <option <?php echo $this->is_item_selected( $document['guid'], $_supercar__report, false ); ?> value="<?php echo $document['guid']; ?>" ><?php echo $document['post_title']; ?></option>
                            <?php } /**/ ?>
                        </select>


                    </th>

                </tr>

                <tr valign="top">
                    <td scope="row" class="non-stackable-row">
                        <em class="block_label">Ou bien, téléverser un rapport d'expertise et l'associer</em>
                        <button id="upload_select_pdf_btn">
                            <strong class="dashicons-before dashicons-upload"><label class="inline_label">Téléverser maintenant !</label></strong>
                        </button>
                    </td>
                </tr>


            </tbody></table>

            <input type="hidden" name="_supercar__report_url" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__report; ?>" />


            <em class="inline_label">
                <?php _e('La sélection d\'un document implique que le véhicule soit marqué publiquement comme vérifié !', $this->domain); ?>
            </em>

        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-location-alt"><?php _e( 'Localité du vendeur', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Préciser la localité géographique du vendeur', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Country address
            //  _supercar__address_country
            //
            // Retreive the year of it.

            $countries_list = $this->get_prefered_world_countries();
            $worldcountries_list = $this->get_other_world_countries();

            $_supercar__address_country = get_post_meta( $post->ID, '_supercar__address_country', true );
            $_supercar__address_country = ( !! empty( $_supercar__address_country ) || $_supercar__address_country=="" )? "(nc)" : $_supercar__address_country;

            ?>

            <?php
            // Related field | City address
            //  _supercar__address_city
            //
            // Retreive the year of it.
            $_supercar__address_city = get_post_meta( $post->ID, '_supercar__address_city', true );
            $_supercar__address_city = ( !! empty( $_supercar__address_city ) || $_supercar__address_city=="" )? "" : $_supercar__address_city;

            ?>

            <?php
            // Related field | City Zip Code
            //  _supercar__address_zipcode
            //
            // Retreive the year of it.
            $_supercar__address_zipcode = get_post_meta( $post->ID, '_supercar__address_zipcode', true );
            $_supercar__address_zipcode = ( !! empty( $_supercar__address_zipcode ) || $_supercar__address_zipcode=="" )? "" : $_supercar__address_zipcode;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Pays', $this->domain); ?> : </label>

                        <select name="_supercar__address_country">
                            <option value="none"><?php _e( '-- Aucun pays sélectionné --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $countries_list as $country ) { ?>
                            <option <?php echo $this->is_item_selected( $country['caption'], $_supercar__address_country, false ); ?> value="<?php echo $country['caption']; ?>" ><?php echo $country['caption']; ?></option>
                            <?php } ?>

                            <optgroup label="Tous les pays">>
                                <?php foreach( (array) $worldcountries_list as $worldcountry ) { ?>
                                <option <?php echo $this->is_item_selected( $worldcountry['caption'], $_supercar__address_country, false ); ?> value="<?php echo $worldcountry['caption']; ?>" ><?php echo $worldcountry['caption']; ?></option>
                                <?php } ?>
                            </optgroup>

                        </select>

                    </th>

                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Ville', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__address_city" style="margin: 0px; width: 99%;" placeholder="(nc)" value="<?php echo $_supercar__address_city; ?>" />
                    </th>

                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Code Postal', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__address_zipcode" style="margin: 0px; width: 90px;" placeholder="(nc)" value="<?php echo $_supercar__address_zipcode; ?>" />
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-money"><?php _e( 'Prix de vente', $this->domain ); ?></strong></legend>



            <?php
            // Related field | Price
            //  _supercar__price
            //
            // Retreive the year of it.
            $_supercar__price = intval( get_post_meta( $post->ID, '_supercar__price', true ) );
            $_supercar__price = ( !! empty( $_supercar__price ) || $_supercar__price==0 )? 0 : $_supercar__price;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Prix de vente affiché', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__price" class="subtitle" style="margin: 0px; width: 80px; text-align: center;" value="<?php echo $_supercar__price; ?>" />
                        <em class="inline_label">
                            <?php _e('(€ - euros)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />




        <?php
        // Custom fieldset data elements | #6 (Entry highlight)
        //
        ?>
        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-yes"><?php _e( 'Mettre en avant', $this->domain ); ?></strong></legend>

            <?php
            // Related field | Post enabling
            //  _supercar__selection
            $_supercar__selection = get_post_meta( $post->ID, '_supercar__selection', true );
            ?>            
            <label class="inline_label"><input type="checkbox" <?php echo $this->is_check( $_supercar__selection, '1' ); ?> name="_supercar__selection" value="1" />
                <?php _e('Activer cette entrée afin de toujours la proposer sur le site dans la sélection de véhicules ?', $this->domain); ?>
            </label>
            <br class="clear" />

        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-format-gallery"><?php _e( 'Galerie photo', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner les visuels associés à ce véhicule pour faire une galerie photos', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Expert Report
            //  _supercar__gallery_ids
            //
            // Retreive the ID of it.
            $_supercar__gallery_ids = trim( get_post_meta( $post->ID, '_supercar__gallery_ids', true ) );

            ?>

            <input type="hidden" name="_supercar__gallery_ids" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__gallery_ids; ?>" />

            <button id="make_select_img_btn">
                <strong class="dashicons-before dashicons-images-alt2"><label class="inline_label">Composer une galerie...</label></strong>
            </button>

            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="inline-row" role="gallery_wrapper">


                    </th>

                </tr>

            </tbody></table>

            <em class="inline_label">
                <?php _e('La photo à la une sera utilisée comme visuel d\'amorçage de la galerie, et n\'est pas affiché ci-dessus', $this->domain); ?>
            </em>

        </fieldset>
        <br class="clear" />



        <?php
        // End print meta box

    }




    /**
     * Check if reference equals to the value, and return selected if verified, empty otherwise.
     *
     * @param reference, string
     * @param value, string
     * @param default, boolean
     */
    public function is_item_selected( $reference, $value, $default ) {
        $verified = $default || ($value==$reference);
        return ( ($verified)? ' selected="selected" style="background-color:lightgray;" ':'' );
    }





    /**
     * Check if reference equals to the value, and return checked if verified, empty otherwise.
     *
     * @param cible, string reference
     * @param test, string to test
     */
    public function is_check($cible,$test){
        $verified = ($cible==$test);
        return ( ($verified)? ' checked="checked" ':'' );
    }





    /**
     * Prepare a simple random slug key.
     *
     * @param none
     */
    public function get_random_anid(){
        return chr(rand(65,90)) . chr(rand(65,90)) . rand(0,9) . rand(0,9) . rand(0,9) . chr(rand(65,90));
    }








    /**
     * Get all Brand/Models/Ranges stored and returns a colection.
     *
     * @param none
     */
    public function get_supercars_brandmodel_posttype_storedin() {

        $_brandmodel_collection = array();

        // Ensures that the SuperCarVendors (dependency) classe has been properly installed.
        if ( class_exists('SuperCarVendors')) {

            // Retreives all SuperCarVenvors posts, and fetches each content...
            $supercars_brandmodel = SuperCarVendors::get_instance();
            $plugin_post_type = $supercars_brandmodel->plugin_post_type;

            $args = array( 
                'post_type'      => array( $plugin_post_type ), 
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC'
            );
            $loop_brandmodel = new WP_Query( $args );

            foreach ( $loop_brandmodel->posts as $brandmodel) {
                
                $content = $brandmodel->post_content;
                $content = strip_tags( $content );

                $brandmodels = explode("\n", $content);
                foreach ( $brandmodels as $model) {

                    $t_brand = trim( $brandmodel->post_title );
                    $t_model = trim( $model );

                    $s_brand = preg_replace("/(\s)+/", "_", strtolower( trim( $t_brand ) ));
                    $s_model = preg_replace("/(\s)+/", "_", strtolower( trim( $t_model ) ));

                    if( $s_brand != "" && $s_model != "" ){

                        // Makes parts as wellformed slugs (used into the backend UI behaviors to sync selection)
                        $_brandmodel_collection[] = array(
                            'ID'          => null,
                            'guid'        => $s_brand ."__". $s_model,
                            'post_title'  => $t_brand ." ". $t_model,
                            't_brandmodel__brand'  => $t_brand,
                            't_brandmodel__model'  => $t_model,
                            's_brandmodel__brand'  => $s_brand,
                            's_brandmodel__model'  => $s_model
                        );

                    }

                }

            }

        }else{

            /*
             * Warning, if we have no SuperCarVendors classe found !
             * So in this case, we inject an unused row to enrich the combo select with a notified item.
             *
             * This is a way to detect in the backend ui if the selection is correct (JS) or at once saving !
             */
            $_brandmodel_collection[] = array(
                'ID'          => null,
                'guid'        =>'empty__null',
                'post_title'  =>'Empty Null',
                '_brandmodel__brand'=>'Empty',
                '_brandmodel__model'=>'Null'
            );

        }

        return $_brandmodel_collection;
    }







    /**
     * Gets all Energies (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_energy_posttype_storedin() {

        $_energy_collection = array();

        // TODO : The right way may a setting page to fill these properties !
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_energy_collection[] = array('guid'=>'diesel',     'post_title'=>'Diesel');
        $_energy_collection[] = array('guid'=>'electrique', 'post_title'=>'Electrique');
        $_energy_collection[] = array('guid'=>'essence',    'post_title'=>'Essence');
        $_energy_collection[] = array('guid'=>'gpl',        'post_title'=>'GPL');
        $_energy_collection[] = array('guid'=>'hybride',    'post_title'=>'Hybride');

        return $_energy_collection;
    }







    /**
     * Gets all Gearboxes (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_gearbox_posttype_storedin() {

        $_gearbox_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_gearbox_collection[] = array('guid'=>'manuelle',     'post_title'=>'Boîte de vitesses manuelle');
        $_gearbox_collection[] = array('guid'=>'sequentielle', 'post_title'=>'Boîte de vitesses séquentielle');
        $_gearbox_collection[] = array('guid'=>'robotisee',    'post_title'=>'Boîte de vitesses robotisée');
        $_gearbox_collection[] = array('guid'=>'double',       'post_title'=>'Boîte de vitesses à double embrayage');
        $_gearbox_collection[] = array('guid'=>'automatique',  'post_title'=>'Boîte de vitesses automatique');
        $_gearbox_collection[] = array('guid'=>'continue',     'post_title'=>'Transmission à variation continue');
        $_gearbox_collection[] = array('guid'=>'hybrides',     'post_title'=>'Véhicules hybrides et électriques');

        return $_gearbox_collection;
    }







    /**
     * Gets all Colors (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_color_posttype_storedin() {

        $_color_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_color_collection[]=array('name'=>'noir',      'hexcode'=>'#070707');
        $_color_collection[]=array('name'=>'anthracite','hexcode'=>'#484648');
        $_color_collection[]=array('name'=>'gris',      'hexcode'=>'#cecece');
        $_color_collection[]=array('name'=>'blanc',     'hexcode'=>'#fafafa');
        $_color_collection[]=array('name'=>'beige',     'hexcode'=>'#cdc8b4');
        $_color_collection[]=array('name'=>'rouge',     'hexcode'=>'#a30f0f');

        $_color_collection[]=array('nblf'=>true);

        $_color_collection[]=array('name'=>'orange',    'hexcode'=>'#da630e');
        $_color_collection[]=array('name'=>'jaune',     'hexcode'=>'#e6da00');
        $_color_collection[]=array('name'=>'vert',      'hexcode'=>'#7ab125');
        $_color_collection[]=array('name'=>'bleu',      'hexcode'=>'#258ab1');
        $_color_collection[]=array('name'=>'violet',    'hexcode'=>'#673ab7');
        $_color_collection[]=array('name'=>'rose',      'hexcode'=>'#c54dab');
        $_color_collection[]=array('name'=>'marron',    'hexcode'=>'#56433c');
        $_color_collection[]=array('name'=>'autre',     'hexcode'=>'transparent');

        $_color_collection[]=array('nblf'=>true);


        return $_color_collection;
    }



    /**
     * Gets the all needed world countries (hardwrited) and return a colection.
     *
     * @param none
     */
    public function get_prefered_world_countries() {

        $_country_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // An entire list of world countries in a selectable list, and a needed list to use...
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_country_collection[]=array('caption'=>"France");
        $_country_collection[]=array('caption'=>"Espagne");
        $_country_collection[]=array('caption'=>"Italie");
        $_country_collection[]=array('caption'=>"Pays-Bas");
        $_country_collection[]=array('caption'=>"Royaume-Uni");

        return $_country_collection;
    }

    public function get_other_world_countries() {

        $_country_collection = array();

        $_country_collection[]=array('caption'=>"Belgique");
        $_country_collection[]=array('caption'=>"Estonie");
        $_country_collection[]=array('caption'=>"Allemagne");
        $_country_collection[]=array('caption'=>"Irlande");
        $_country_collection[]=array('caption'=>"Luxembourg");
        $_country_collection[]=array('caption'=>"Monaco");
        $_country_collection[]=array('caption'=>"Roumanie");
        $_country_collection[]=array('caption'=>"Suisse");
        $_country_collection[]=array('caption'=>"Ukraine");

        return $_country_collection;
    }







    /**
     * Returns a collection of elements/entries depending on a data provider.
     *
     * @param provider, string method name of this Classe
     * @return data_result, array based on the method Classe return.
     */
    public function get_data_provider_collection($provider){
        
        $data_result = array();
        $provider_method = $provider.'__data_provider';
        $className   = SuperCar::get_instance();
        $data_result = $className->{$provider_method}();

        return $data_result;

    }



    /**
     * Gets all PDF/ATTACHMENT from media library.
     * Work with data provider {get_documents_from_media_library} or standalone.
     *
     * @param none
     * @return array of pdf documents « guid » and « post_title ».
     */
    public function get_documents_from_media_library__data_provider() {

        $args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/pdf',
            'post_status'    => 'inherit',
            'posts_per_page' => -1
        );

        $query_documents = new WP_Query( $args );
        
        $document_coll = array();
        foreach ( $query_documents->posts as $document) {
            $document_coll[] = array(
                'guid'       => $document->guid, 
                'post_title' => $document->post_title
            );
        }

        return $document_coll;
    }


    /**
     * Gets all WP PAGES from WP Theme.
     * Work with data provider {get_pages_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of wp pages « guid » and « post_title ».
     */
    
    public function get_pages_from_wp_theme__data_provider() {

        // Do not pick the pages having these templates :
        // - <launch.php>, ...
        // TODO, builds an admin panel to configure them from WP_Admin ;)
        $rejected_templates = array( '_launch.php', 'system.php', '_video-content.php' );

        $args = array(
            'sort_order'   => 'asc',
            'sort_column'  => 'post_title',
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'meta_key'     => '',
            'meta_value'   => '',
            'authors'      => '',
            'child_of'     => 0,
            'parent'       => -1,
            'exclude_tree' => '',
            'number'       => '',
            'offset'       => 0,
            'post_type'    => 'page',
            'post_status'  => 'publish'
        );

        $query_documents = new WP_Query( $args );

        $document_coll = array();
        foreach ( $query_documents->posts as $document) {

            // Picks this page only if template name is not in haystack.
            if( !in_array( get_post_meta( $document->ID, '_wp_page_template', TRUE ) , $rejected_templates ) ){

                $document_coll[] = array(
                    'guid'       => $document->ID, 
                    'post_title' => $document->post_title 
                );

            }

        }

        return $document_coll;
    }


    /**
     * Gets all WP POSTS from WP Theme.
     * Work with data provider {get_posts_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of wp posts « guid » and « post_title ».
     */
    
    public function get_posts_from_wp_theme__data_provider() {

        $args = array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'post',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'author'           => '',
            'post_status'      => 'publish',
            'suppress_filters' => true 
        );

        $query_documents = new WP_Query( $args );

        $document_coll = array();
        foreach ( $query_documents->posts as $document) {
            $document_coll[] = array(
                'guid'       => $document->ID, 
                'post_title' => $document->post_title
            );
        }

        return $document_coll;
    }


    /**
     * Gets all VIDEOS from WP Theme.
     * Work with data provider {get_video_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of video attachment « guid » and « post_title ».
     */
    
    public function get_video_from_wp_theme__data_provider() {

        $args = array(
          'post_type'      => 'attachment',
          'numberposts'    => -1,
          'post_status'    => null,
          'post_parent'    => null, // any parent
          'post_mime_type' => 'video'
        ); 

        $query_documents = get_posts( $args );

        $document_coll = array();
        if ( $query_documents ) {
            foreach ( $query_documents as $document) {
                $document_coll[] = array(
                    'guid'       => $document->ID, 
                    'post_title' => $document->post_title
                );
            }
        }

        return $document_coll;
    }






    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function supercar_plugin_save_meta_box_data( $post_id ) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['supercar_plugin_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['supercar_plugin_meta_box_nonce'], 'supercar_plugin_meta_box' ) ) {
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



    /*
        // Note : There is only one entry defined as demo in the collection !
        //
        // Ensure that there is no other stored entry that is marked as demo 
        // if the current one has been defined « demo » / _supercar__demoplay set to 1 !
        if( sanitize_text_field( $_POST['_supercar__demoplay'] ) == '1' ){
            
            $args = array( 'post_type' => array( $this->plugin_post_type ) );
            $loop = new WP_Query( $args );
            
            while ( $loop->have_posts() ) : $loop->the_post();
                
                global $post; setup_postdata($post);
                update_post_meta($post->ID, '_supercar__demoplay', '');

            endwhile;

        }
    /**/




        /**
         * Now, we can save into the WPDB Post all of our meta custom fields, sanitized ;)
         *
         */
        $save_dbarray = array(

            '_supercar__brand_collection'   => sanitize_text_field( $_POST['_supercar__brand_collection'] ),
            '_supercar__model_collection'   => sanitize_text_field( $_POST['_supercar__model_collection'] ),
            '_supercar__brandmodel_version' => sanitize_text_field( $_POST['_supercar__brandmodel_version'] ),
            '_supercar__dateyear'           => sanitize_text_field( $_POST['_supercar__dateyear'] ),
            '_supercar__km'                 => sanitize_text_field( $_POST['_supercar__km'] ),
            '_supercar__system_energy'      => sanitize_text_field( $_POST['_supercar__system_energy'] ),
            '_supercar__gear_box'           => sanitize_text_field( $_POST['_supercar__gear_box'] ),
            '_supercar__power_horse'        => sanitize_text_field( $_POST['_supercar__power_horse'] ),
            '_supercar__doors'              => sanitize_text_field( $_POST['_supercar__doors'] ),
            '_supercar__color'              => sanitize_text_field( $_POST['_supercar__color'] ),
            '_supercar__finish'             => sanitize_text_field( $_POST['_supercar__finish'] ),
            '_supercar__client_hello'       => sanitize_text_field( $_POST['_supercar__client_hello'] ),
            '_supercar__report'             => sanitize_text_field( $_POST['_supercar__report'] ),
            '_supercar__address_country'    => sanitize_text_field( $_POST['_supercar__address_country'] ),
            '_supercar__address_city'       => sanitize_text_field( $_POST['_supercar__address_city'] ),
            '_supercar__address_zipcode'    => sanitize_text_field( $_POST['_supercar__address_zipcode'] ),
            '_supercar__price'              => sanitize_text_field( $_POST['_supercar__price'] ),
            '_supercar__selection'          => sanitize_text_field( $_POST['_supercar__selection'] ),
            '_supercar__gallery_ids'        => sanitize_text_field( $_POST['_supercar__gallery_ids'] ),
            '_supercar__unique_id'          => sanitize_text_field( $_POST['_supercar__unique_id'] )

        );

        // Saves values from created array into db...
        // Updates the meta fields in the database (deleting and updating is a clear way to strore properly).
        foreach($save_dbarray as $meta_key=>$meta_value) {
        
            delete_post_meta($post_id, $meta_key);
            update_post_meta($post_id, $meta_key, $meta_value);

        }

        // We can delete depreciated, unsued and old metas here to maintains uptodate the wordpress plugin mechanism.
        delete_post_meta($post_id, '_supercar__enabled');


        // Removes a meta key only if its value is defined to '0' (zero)
        // Keep only meta sets to '1'
        if( intval( $_POST['_supercar__selection'] ) == 0 || empty( $_POST['_supercar__selection'] ) || !isset( $_POST['_supercar__selection'] ) ) {
            delete_post_meta($post_id, '_supercar__selection');
        }
    
    }





    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The SuperCar object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SuperCar ) ) {
            self::$instance = new SuperCar();
        }

        return self::$instance;

    }

}

// Loads the main plugin class.
$Active__SuperCar = SuperCar::get_instance();
=======
<?php
/*
Plugin Name: Super Car
Plugin URI: http://cms.web-medias.com/supercar
Description: This plugin create a new custom post_type « SuperCar » and allows to manipulate entries as cars.
Version: 1.0
Company: ##############
Author: Sébastien Brémond
Author URI: http://cms.web-medias.com/supercar/authors
License: GPL2
Text Domain: supercar
Domain Path: /languages
*/


// Exit if accessed directly.
if ( ! function_exists('add_action') || ! defined( 'ABSPATH' ) )
    exit();




/* ***************** *******************************************************************
 * WordPress Plugin | SuperCar.
 * ***************** *******************************************************************
 * @author Sebastien Bremond (IncludE)
 * @studio ##############
 * @client <active-project>
 * 
 * Plugin Class declaration based on the WordPress Plugin API and WP mechanisms.
 * Controls the plugin, as well as activation, and deactivation
 */

class SuperCar {

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
    public $plugin_name = 'Super Car';

    /**
     * The taxonomy name of the plugin.
     *
     * @since 1.0.0
     *
     * @var array of string
     */
    public $plugin_name_taxonomy = array(
        'plural'   => "",
        'singular' => ""
    );

    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'supercar';

    /**
     * Unique plugin post type identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_post_type = 'supercar';

    /**
     * Plugin textdomain.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $domain = 'supercar';

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
        add_action( 'init', array( $this, 'supercar_plugin_post_type' ), 1 );


        // Removes the 'add media' button for this custom post type.
        add_action( 'admin_head', array( $this, 'adapt_plugin_post_type' ), 1 );


        // Builds the meta box system and defines the backup method.

        do_action( $this->plugin_slug .'_before_metabox' ); // Fires a hook before make the meta box.
        add_action( 'add_meta_boxes', array( $this, 'supercar_plugin_add_meta_box') );
        do_action( $this->plugin_slug .'_after_metabox' ); // Fires a hook after make the meta box.
        add_action( 'save_post', array( $this, 'supercar_plugin_save_meta_box_data') );

        // Adds shortcode to embed into posts, pages,...
        add_shortcode( $this->plugin_post_type , array(&$this, 'supercar_plugin_add_shortcode'));

        // Loads the plugin back-end behavior (Javascript files).
        add_action('admin_enqueue_scripts', array(&$this, 'supercar_plugin_admin_head'));

        // Loads the needs for front-end.
        //add_action ('wp_enqueue_scripts', array(&$this, 'supercar_plugin_frontend_head'));


        // Changes the default placeholder text of the global Post input title area.
        add_filter('enter_title_here', array(&$this, 'supercar_plugin_change_title_placeholder'));

        // Adds a smallest box just after the title.
        add_filter('edit_form_after_title', array(&$this, 'supercar_plugin_add_content_after_editor'));


        // Allows to custom the WP List table by adding custom column in the UI View and add some informations.
        add_filter('manage_'.$this->plugin_post_type.'_posts_columns', array(&$this, 'supercar_plugin_columns_head'),  10);
        add_action('manage_'.$this->plugin_post_type.'_posts_custom_column', array(&$this, 'supercar_plugin_columns_content'), 10, 2);

        // Fires a hook when the WP Plugin MetaBox was made.
        do_action( $this->plugin_slug .'_when_ready' );

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

        // Allows to manage supports for any use of excerpt.
        do_action( $this->plugin_slug .'_after_supports' );

    }





    /**
     * Creates the related custom post type for the plugin
     *
     * @since 1.0.0
     */
    public function supercar_plugin_post_type() {

        //register_taxonomy_for_object_type('category', $this->plugin_post_type ); // Register Taxonomies for Category

        //register_taxonomy_for_object_type('post_tag', $this->plugin_post_type ); // Register Taxonomies for Post tag

        register_post_type( $this->plugin_post_type , // Register Custom Post Type
            array(
            'labels'        => array(
                'name'                  => __('Super Cars', $this->domain),
                'singular_name'         => __('Super Car', $this->domain),
                'add_new'               => __('Nouvelle', $this->domain),
                'add_new_item'          => __('Ajout d\'une nouvelle Super Car', $this->domain),
                'edit'                  => __('Modifier', $this->domain),
                'edit_item'             => __('Modifier une Super Car', $this->domain),
                'new_item'              => __('Nouvelle Super Car', $this->domain),
                'view'                  => __('Voir la Super Car', $this->domain),
                'view_item'             => __('Afficher', $this->domain),
                'search_items'          => __('Rechercher une Super Car', $this->domain),
                'not_found'             => __('Aucune Super Car trouvée', $this->domain),
                'not_found_in_trash'    => __('Aucune Super Car trouvée dans la corbeille', $this->domain)
            ),
            'public'        => true,
            'hierarchical'  => false, // Allows your posts to behave like Hierarchy Pages
            'has_archive'   => false,
            'supports'      => array(
                'editor'        /**/
               ,'title'      /**/
               ,'thumbnail'  /**/
            /* ,'excerpt'      /**/
            /* ,'custom-fields' /**/
            /* ,'comments'    /**/
            ),
            'can_export'    => true, // Allows export in Tools > Export
            'menu_icon'     => 'dashicons-performance',
            'menu_position' => 100,
            'rewrite' => array('slug' => 'vehicule','with_front' => true),
            'taxonomies'    => array(
            /*
                'post_tag',
                'category',
                'supercar_taxonomy'
            /**/
            ) // Add Category and Post Tags support
        ));




    /*
        // Add admin side bar menu taxonomy
        $labels_taxonomy = array(
            'name'              => _x( $this->plugin_name_taxonomy['plural'], $this->domain ),
            'singular_name'     => _x( $this->plugin_name_taxonomy['singular'], $this->domain ),
            'search_items'      => __( 'Rechercher', $this->domain ),
            'all_items'         => __( 'Toutes', $this->domain ),
            'parent_item'       => __( 'Parent', $this->domain ),
            'parent_item_colon' => __( 'Parent :', $this->domain ),
            'edit_item'         => __( 'Edition', $this->domain ),
            'update_item'       => __( 'Mettre à jour', $this->domain ),
            'add_new_item'      => __( 'Nouvelle', $this->domain ),
            'new_item_name'     => __( 'Nouveau nom', $this->domain ),
            'menu_name'         => __( $this->plugin_name_taxonomy['plural'] ),
        );

        $args_taxonomy = array(
            'hierarchical'      => true,
            'labels'            => $labels_taxonomy,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'supercar_taxonomy' ),
        );

        //register_taxonomy( 'supercar_taxonomy', array( $this->plugin_post_type ), $args_taxonomy );
    /**/

    }





    /**
     * Loads the plugin behavior.
     *
     * @since 1.0.0
     */
    public function supercar_plugin_admin_head() {

        // Enqueue jQuery UI CSS
        //
        // UI Style (prefered CDN vendors - Note the SSL negociation)
        wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
        wp_enqueue_script( 'jquery-ui-style', '//code.jquery.com/ui/1.11.4/jquery-ui.js' ); // Distant git from jQuery CDN

        // Enqueue Datepicker
        //
        // Datepicker widget (finally the entries have no calendar date)
        //wp_enqueue_script( 'jquery-ui-datepicker', plugins_url('/core/js/jquery-datepicker.js', __FILE__), false, null); // Local available

        // Internationalisaton | FR options & Elements scripting behaviors (attaching datepicker to needed IDs if used)
        wp_enqueue_script( 'plugin-supercar-runtime-apps', plugins_url('/core/js/plugin-runtime.js', __FILE__), array(), null, false);
        wp_enqueue_style( 'plugin-supercar-runtime-apps', plugins_url('/core/css/plugin-runtime.css', __FILE__), array(), null );

    }





    /**
     * Customs the title placeholder into the admin UI
     *
     * @since 1.0.0
     */
    public function supercar_plugin_change_title_placeholder($title) {

        $screen = get_current_screen();
        
        if( $this->plugin_post_type == $screen->post_type ){
            $title = __('Libellé de ce véhicule...', $this->domain);
        }

        return $title;

    }





    /**
     * Adds a custom column header to the posttype table list.
     */
    public function supercar_plugin_columns_head($defaults) {
        
        $defaults['title'] = __( 'Libellé du véhicule', $this->domain );
        $defaults['entry_enabled'] = __( 'État', $this->domain );
        
        return $defaults;

    }





    /**
     * Adds (and fill) a custom column cell to the posttype table list.
     */
    public function supercar_plugin_columns_content($column_name, $post_ID) {

        
        // Negotiates the current processed column...
        if ($column_name == 'entry_enabled') {

            // Fetches the meta value, and shows status.

            // We want to show if this entry is part of the selections.
            $_supercar__selection = get_post_meta( $post_ID, '_supercar__selection', true );

            if( 1 == intval($_supercar__selection) ){
                echo '<span class="dashicons-before dashicons-yes" style="color:#0688A2;"></span>'. __( 'Entrée mise en avant', $this->domain );
            }else{
                echo '<span class="dashicons-before dashicons-no-alt" style="color:#A23D06;"></span> <em>'.__( '(non affectée)', $this->domain ).'</em>';
            }

        }
    }





    /**
     * Adds a postbox after app title / before content editor.
     */
    public function supercar_plugin_add_content_after_editor() {

        $screen = get_current_screen();

        // Shows this panel only on the admin page of custom post defined by the <plugin_post_type>
        if( $this->plugin_post_type == $screen->post_type ){ 

            /* Start : HTML Output (inline style embedded) */ ?>

            <a name="wp_content_area"></a>

            <div class="postbox closed" class="meta-box-sortables" behaviours="togglable" style="margin-top:20px; margin-bottom:5px;">            

                <button type="button" class="handlediv" aria-expanded="true">
                    <span class="screen-reader-text"><strong style="display:block; color:#0c7cb6; font-size:23px;"></strong></span>
                    <span class="toggle-indicator" aria-hidden="true">&#9670;</span>
                </button>
                <h2 class="hndle ui-sortable-handle">
                    <span>
                        <strong style="display:block; color:#0c7cb6; font-size:23px;">
                            <?php echo $this->plugin_name .' » '. __( 'Véhicule', $this->domain ); ?>
                        </strong>
                        <?php _e( 'Informations générales', $this->domain ); ?>
                    </span>
                </h2>

                <div class="inside" style="padding-bottom:5px;">
                    <p>
                        <span class="dashicons-before dashicons-arrow-down"></span>
                        <?php _e( 'La zone ci-dessous permet de détailler les informations ou caractéristiques génarales d\'un véhicule', $this->domain ); ?>
                    </p>
                </div>
            </div>

            <?php /* End : HTML Output */

        }
        
    }





    /**
     * Adds a box to the main column on the Post and Page edit screens.
     * 
     * @param WP Plugin current postType.
     */
    public function supercar_plugin_add_meta_box($postType) {

        $screens = array( $this->plugin_post_type ); // Allows to run with this

        foreach ( $screens as $screen ) {

        /*  // Memo !
         *  add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
         *  @param $screen ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type-slug')
         *  @param $context ('normal', 'advanced', or 'side')
         *  @param $priority ('high', 'core', 'default' or 'low')
         */

            // Builds the MetaBox container.
            $meta_box_container_title = '<strong style="display:block; color:#0c7cb6; font-size:23px;">'. $this->plugin_name .'</strong>'.
                                        __( 'Définition des caractéristiques principales de ce véhicule', $this->domain );
            
            add_meta_box(
                'supercar_plugin_sectionid', 
                $meta_box_container_title,
                array( $this, 'supercar_plugin_meta_box_callback' ),
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
    public function supercar_plugin_meta_box_callback( $post ) {

        // Adds a nonce field so we can check for it later.
        wp_nonce_field( 'supercar_plugin_meta_box', 'supercar_plugin_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        ?>

        <?php
        // Creates a dedicated and unique entry id
        //

            $_supercar__unique_id =  get_post_meta( $post->ID, '_supercar__unique_id', true );
            $_supercar__unique_id = ( !! empty( $_supercar__unique_id ) || "" == $_supercar__unique_id )? $this->get_random_anid() : $_supercar__unique_id;

        ?>
        <input type="hidden" name="_supercar__unique_id" value="<?php echo $_supercar__unique_id; ?>" />




        <?php
        // Notify if something missing !
        //
        $REQUIRED_CLASS = 'SuperCar'; // Defines the Class dependence.

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
        /*
         * Ok, lets start to build the plugin sections that contains all the fields needed
         */
        ?>



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-nametag"><?php _e( 'Identification', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner les caractéristiques d\'identification du véhicule.', $this->domain); ?></label>
            <br class="clear" />
            <em class="inline_label">
                <?php _e('&#10097; Marque &#10093; Modèle', $this->domain); ?>
            </em>
            <br class="clear" />


            <?php
            // Related field | Brands | Models | Ranges
            //  _supercar__brand_collection
            //  _supercar__model_collection
            //  _supercar__brandmodel_version
            //
            // Retreives the Brands/Models/Range colection.
            $brandmodel_collection = $this->get_supercars_brandmodel_posttype_storedin();


            $_supercar__brand_collection   = get_post_meta( $post->ID, '_supercar__brand_collection', true );
            $_supercar__brand_collection   = empty($_supercar__brand_collection)? 'none' : $_supercar__brand_collection;

            $_supercar__model_collection   = get_post_meta( $post->ID, '_supercar__model_collection', true );
            $_supercar__model_collection   = empty($_supercar__model_collection)? '' : $_supercar__model_collection;
            
            $_supercar__brandmodel_version =  get_post_meta( $post->ID, '_supercar__brandmodel_version', true );
            $_supercar__brandmodel_version = ( !! empty( $_supercar__brandmodel_version ) || "" == $_supercar__brandmodel_version )? "" : $_supercar__brandmodel_version;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Marque', $this->domain); ?> : </label>
                        <select name="_supercar__brand_collection">
                            
                            <option value="none"><?php _e( '-- Aucune marque sélectionnée --', $this->domain ); ?></option>

                            <?php
                                $u_brand_list = ( array_unique( array_column($brandmodel_collection, 's_brandmodel__brand', 't_brandmodel__brand') , SORT_REGULAR ) );

                                foreach( (array) $u_brand_list as $brand => $s_brand ) {
                                    $s_brand = strtolower( $s_brand );
                                    $s_brand = preg_replace("/(\s)+/", "_", $s_brand);
                                ?>
                                <option <?php echo $this->is_item_selected( $s_brand, $_supercar__brand_collection, false ); ?> value="<?php echo $s_brand; ?>" data-brand="<?php echo $s_brand; ?>"><?php echo $brand; ?></option>
                                <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Modèle', $this->domain); ?> : </label>
                        <select name="_supercar__model_collection">
                            <option value=""><?php _e( '-- Aucun modèle sélectionnée --', $this->domain ); ?></option>

                            <?php
                                $u_model_list = array_column($brandmodel_collection, 's_brandmodel__brand', 't_brandmodel__model' );
                                foreach( (array) $u_model_list as $model => $s_brand ) {
                                    $s_model = preg_replace("/(\s)+/", "_", strtolower( $model ));
                                ?>
                                <option <?php echo $this->is_item_selected( $s_model, $_supercar__model_collection, false ); ?> value="<?php echo $s_model; ?>" data-brand="<?php echo $s_brand; ?>" data-model="<?php echo $s_model; ?>"><?php echo $model; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Version', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__brandmodel_version" placeholder="<?php _e('(facultatif)', $this->domain); ?>" style="margin: 0px; width: 150px;" value="<?php echo $_supercar__brandmodel_version; ?>" />
                    </th>

                </tr>

                <tr valign="top">

                    <td scope="row" class="non-stackable-row">
                        <em class="inline_label">Appliquer cette sélection comme titre de la fiche véhicule :</em>
                        <button id="selected_brand_model_btn">
                            <strong class="dashicons-before dashicons-admin-post"><label class="inline_label" id="selected_brand_model"></label></strong>
                        </button>
                    </td>
                </tr>

            </tbody></table>


            <hr class="separator_data" />


            <?php
            // Related field | Year
            //  _supercar__dateyear
            //
            // Retreive the meta value of it.
            $_supercar__dateyear = intval( get_post_meta( $post->ID, '_supercar__dateyear', true ) );
            $_supercar__dateyear = ( !! empty( $_supercar__dateyear ) || $_supercar__dateyear==0 )? date('Y') : $_supercar__dateyear;

            $_supercar__km = intval( get_post_meta( $post->ID, '_supercar__km', true ) );
            $_supercar__km = ( !! empty( $_supercar__km ) || $_supercar__km==0 )? 0 : $_supercar__km;
            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Année du véhicule', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__dateyear" class="subtitle" style="margin: 0px; width: 60px;" value="<?php echo $_supercar__dateyear; ?>" />
                        <br class="clear">
                        <em class="block_label">
                            <?php _e('(Année de mise en circulation)', $this->domain); ?>
                        </em>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Kilométrage du véhicule', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__km" style="margin: 0px; width: 70px;" value="<?php echo $_supercar__km; ?>" />
                        <br class="clear">
                        <em class="block_label">
                            <?php _e('(Km. réels au compteurs)', $this->domain); ?>
                        </em>
                    </th>

                </tr>

            </tbody></table>


        </fieldset>
        <br class="clear" />





        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-editor-kitchensink"><?php _e( 'Caractéristiques', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Saisir les informations au sujet du véhicule.', $this->domain); ?></label>
            <br class="clear" />


            <?php
            // Related field | Energies
            //  _supercar__system_energy
            //
            // Retreive the meta value of it.
            $energy_list = $this->get_supercars_energy_posttype_storedin();

            $_supercar__system_energy = get_post_meta( $post->ID, '_supercar__system_energy', true );
            ?>


            <?php
            // Related field | Gearboxes
            //  _supercar__gear_box
            //
            // Retreive the gearbox type of it.
            $gearbox_list = $this->get_supercars_gearbox_posttype_storedin();

            $_supercar__gear_box = get_post_meta( $post->ID, '_supercar__gear_box', true );
            ?>


            <?php
            // Related field | Power Horse
            //  _supercar__power_horse
            //
            // Retreive the power of it.
            $_supercar__power_horse = get_post_meta( $post->ID, '_supercar__power_horse', true );
            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Énergie', $this->domain); ?> : </label>
                        <select name="_supercar__system_energy">
                            <option value="none"><?php _e( '-- Aucune energie sélectionnée --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $energy_list as $energy ) { ?>
                            <option <?php echo $this->is_item_selected( $energy['guid'], $_supercar__system_energy, false ); ?> value="<?php echo $energy['guid']; ?>" ><?php echo $energy['post_title']; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Boite à vitesses', $this->domain); ?> : </label>
                        <select name="_supercar__gear_box">
                            <option value="none"><?php _e( '-- Aucune boite sélectionnée --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $gearbox_list as $gearbox ) { ?>
                            <option <?php echo $this->is_item_selected( $gearbox['guid'], $_supercar__gear_box, false ); ?> value="<?php echo $gearbox['guid']; ?>" ><?php echo $gearbox['post_title']; ?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Puissance', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__power_horse" style="margin: 0px; width: 50px;" value="<?php echo $_supercar__power_horse; ?>" />
                        <em class="inline_label">
                            <?php _e('(Indication Chevaux Fiscaux)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>


        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-admin-customizer"><?php _e( 'Finition', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Définir le style de finition du véhicule', $this->domain); ?></label>
            <br class="clear" />

            <br class="clear" />

            <?php
            // Related field | Doors
            //  _supercar__doors
            //
            // Retreive the meta value of it.
            $_supercar__doors = intval( get_post_meta( $post->ID, '_supercar__doors', true ) );
            ?>

            <label class="inline_label"><?php _e('Ouverture', $this->domain); ?> : </label>
            <select name="_supercar__doors">
                <option value="none"><?php _e( '-- Faire un choix --', $this->domain ); ?></option>
                
                <option <?php echo $this->is_item_selected( 3, $_supercar__doors, false ); ?> value="3" ><?php _e( '3 portes', $this->domain ); ?></option>
                <option <?php echo $this->is_item_selected( 5, $_supercar__doors, false ); ?> value="5" ><?php _e( '5 portes', $this->domain ); ?></option>

            </select>


            <hr class="separator_data" />


            <?php
            // Related field | Color
            //  _supercar__color
            //
            // Retreive the meta value of it.
            $color_list = $this->get_supercars_color_posttype_storedin();

            $_supercar__color = get_post_meta( $post->ID, '_supercar__color', true );
            $_supercar__color = ($_supercar__color=='')? '#FFFFFF':$_supercar__color;
            ?>

            <label class="inline_label"><?php _e('Couleur du véhicule', $this->domain); ?> : </label>

                            
            <?php foreach( (array) $color_list as $color ) { ?>
            
                <?php if( isset( $color['nblf'] ) ) { ?>
                    <br class="clear" />
                <?php }else{ ?>
                    <span class="color-selector-wrapper">
                        <input type="radio" id="color_<?php echo $color['name']; ?>" name="_supercar__color" value="<?php echo $color['hexcode']; ?>" <?php echo $this->is_check( $color['hexcode'], $_supercar__color, false ); ?> />
                        <label for="color_<?php echo $color['name']; ?>" class="color-selector" style="background-color:<?php echo $color['hexcode']; ?>;" data-color="<?php echo $color['name']; ?>"></label>
                    </span>
                <?php } ?>

            <?php } ?>
            <br class="clear" />


            <hr class="separator_data" />


            <?php
            // Related field | Finition
            //  _supercar__finish
            //
            // Retreive the meta value of it.
            $_supercar__finish =  get_post_meta( $post->ID, '_supercar__finish', true );
            $_supercar__finish = ( !! empty( $_supercar__finish ) || $_supercar__finish=="" )? "(nc)" : $_supercar__finish;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Aspect/finition', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__finish" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__finish; ?>" />
                        <em class="inline_label">
                            <?php _e('(Facultatif - à compléter si spécifique)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-awards"><?php _e( 'Client HelloMotors', $this->domain ); ?></strong></legend>

            <?php
            // Related field | Client HelloMotors
            //  _supercar__client_hello
            //
            // Retreive the client of it.
            $_supercar__client_hello = intval( get_post_meta( $post->ID, '_supercar__client_hello', true ) );
            $_supercar__client_hello = ( !! empty( $_supercar__client_hello ) || $_supercar__client_hello=="" )? 0 : $_supercar__client_hello;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <td scope="row" class="non-stackable-row">
                        <label class="inline_label" for="_supercar__client_hello"><?php _e('Ce véhicule appartient-il à un client HelloMotors ?', $this->domain); ?> </label>
                        <input type="checkbox" name="_supercar__client_hello" value="1" <?php echo $this->is_check( 1, $_supercar__client_hello, false ); ?> >
                    </td>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-clipboard"><?php _e( 'Rapport d\'expertise', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner le document PDF&trade; servant de rapport d\'expertise pour le véhicule', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Expert Report
            //  _supercar__report
            //
            // Retreive the year of it.
            $_supercar__report = get_post_meta( $post->ID, '_supercar__report', true );
            $_supercar__report = ($_supercar__report == 'none')? '' : $_supercar__report;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Rapport d\'expertise', $this->domain); ?> : </label>
                        <select name="_supercar__report">
                            <option value="none"><?php _e( 'Pas encore de rapport d\'expertise à ce jour', $this->domain ); ?></option>
                            
                            <?php
                            $doc_collection = $this->get_documents_from_media_library();
                            foreach( (array) $doc_collection as $document ) { 
                            ?>
                            <option <?php echo $this->is_item_selected( $document['guid'], $_supercar__report, false ); ?> value="<?php echo $document['guid']; ?>" ><?php echo $document['post_title']; ?></option>
                            <?php } /**/ ?>
                        </select>


                    </th>

                </tr>

                <tr valign="top">
                    <td scope="row" class="non-stackable-row">
                        <em class="block_label">Ou bien, téléverser un rapport d'expertise et l'associer</em>
                        <button id="upload_select_pdf_btn">
                            <strong class="dashicons-before dashicons-upload"><label class="inline_label">Téléverser maintenant !</label></strong>
                        </button>
                    </td>
                </tr>


            </tbody></table>

            <input type="hidden" name="_supercar__report_url" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__report; ?>" />


            <em class="inline_label">
                <?php _e('La sélection d\'un document implique que le véhicule soit marqué publiquement comme vérifié !', $this->domain); ?>
            </em>

        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-location-alt"><?php _e( 'Localité du vendeur', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Préciser la localité géographique du vendeur', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Country address
            //  _supercar__address_country
            //
            // Retreive the year of it.

            $countries_list = $this->get_prefered_world_countries();
            $worldcountries_list = $this->get_other_world_countries();

            $_supercar__address_country = get_post_meta( $post->ID, '_supercar__address_country', true );
            $_supercar__address_country = ( !! empty( $_supercar__address_country ) || $_supercar__address_country=="" )? "(nc)" : $_supercar__address_country;

            ?>

            <?php
            // Related field | City address
            //  _supercar__address_city
            //
            // Retreive the year of it.
            $_supercar__address_city = get_post_meta( $post->ID, '_supercar__address_city', true );
            $_supercar__address_city = ( !! empty( $_supercar__address_city ) || $_supercar__address_city=="" )? "" : $_supercar__address_city;

            ?>

            <?php
            // Related field | City Zip Code
            //  _supercar__address_zipcode
            //
            // Retreive the year of it.
            $_supercar__address_zipcode = get_post_meta( $post->ID, '_supercar__address_zipcode', true );
            $_supercar__address_zipcode = ( !! empty( $_supercar__address_zipcode ) || $_supercar__address_zipcode=="" )? "" : $_supercar__address_zipcode;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Pays', $this->domain); ?> : </label>

                        <select name="_supercar__address_country">
                            <option value="none"><?php _e( '-- Aucun pays sélectionné --', $this->domain ); ?></option>
                            
                            <?php foreach( (array) $countries_list as $country ) { ?>
                            <option <?php echo $this->is_item_selected( $country['caption'], $_supercar__address_country, false ); ?> value="<?php echo $country['caption']; ?>" ><?php echo $country['caption']; ?></option>
                            <?php } ?>

                            <optgroup label="Tous les pays">>
                                <?php foreach( (array) $worldcountries_list as $worldcountry ) { ?>
                                <option <?php echo $this->is_item_selected( $worldcountry['caption'], $_supercar__address_country, false ); ?> value="<?php echo $worldcountry['caption']; ?>" ><?php echo $worldcountry['caption']; ?></option>
                                <?php } ?>
                            </optgroup>

                        </select>

                    </th>

                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Ville', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__address_city" style="margin: 0px; width: 99%;" placeholder="(nc)" value="<?php echo $_supercar__address_city; ?>" />
                    </th>

                    <th scope="row" class="stackable-row">
                        <label class="block_label"><?php _e('Code Postal', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__address_zipcode" style="margin: 0px; width: 90px;" placeholder="(nc)" value="<?php echo $_supercar__address_zipcode; ?>" />
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-money"><?php _e( 'Prix de vente', $this->domain ); ?></strong></legend>



            <?php
            // Related field | Price
            //  _supercar__price
            //
            // Retreive the year of it.
            $_supercar__price = intval( get_post_meta( $post->ID, '_supercar__price', true ) );
            $_supercar__price = ( !! empty( $_supercar__price ) || $_supercar__price==0 )? 0 : $_supercar__price;

            ?>


            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="stackable-row">
                        <label class="inline_label"><?php _e('Prix de vente affiché', $this->domain); ?> : </label>
                        <input type="text" name="_supercar__price" class="subtitle" style="margin: 0px; width: 80px; text-align: center;" value="<?php echo $_supercar__price; ?>" />
                        <em class="inline_label">
                            <?php _e('(€ - euros)', $this->domain); ?>
                        </em>
                    </th>
                </tr>

            </tbody></table>



        </fieldset>
        <br class="clear" />




        <?php
        // Custom fieldset data elements | #6 (Entry highlight)
        //
        ?>
        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-yes"><?php _e( 'Mettre en avant', $this->domain ); ?></strong></legend>

            <?php
            // Related field | Post enabling
            //  _supercar__selection
            $_supercar__selection = get_post_meta( $post->ID, '_supercar__selection', true );
            ?>            
            <label class="inline_label"><input type="checkbox" <?php echo $this->is_check( $_supercar__selection, '1' ); ?> name="_supercar__selection" value="1" />
                <?php _e('Activer cette entrée afin de toujours la proposer sur le site dans la sélection de véhicules ?', $this->domain); ?>
            </label>
            <br class="clear" />

        </fieldset>
        <br class="clear" />



        <fieldset class="tmg">
            <legend><strong class="dashicons-before dashicons-format-gallery"><?php _e( 'Galerie photo', $this->domain ); ?></strong></legend>

            <label class="inline_text"><?php _e('Sélectionner les visuels associés à ce véhicule pour faire une galerie photos', $this->domain); ?></label>
            <br class="clear" />

            <?php
            // Related field | Expert Report
            //  _supercar__gallery_ids
            //
            // Retreive the ID of it.
            $_supercar__gallery_ids = trim( get_post_meta( $post->ID, '_supercar__gallery_ids', true ) );

            ?>

            <input type="hidden" name="_supercar__gallery_ids" style="margin: 0px; width: 99%;" value="<?php echo $_supercar__gallery_ids; ?>" />

            <button id="make_select_img_btn">
                <strong class="dashicons-before dashicons-images-alt2"><label class="inline_label">Composer une galerie...</label></strong>
            </button>

            <table class="form-table wpex-custom-admin-login-table"><tbody>

                <tr valign="top">
                    <th scope="row" class="inline-row" role="gallery_wrapper">


                    </th>

                </tr>

            </tbody></table>

            <em class="inline_label">
                <?php _e('La photo à la une sera utilisée comme visuel d\'amorçage de la galerie, et n\'est pas affiché ci-dessus', $this->domain); ?>
            </em>

        </fieldset>
        <br class="clear" />



        <?php
        // End print meta box

    }




    /**
     * Check if reference equals to the value, and return selected if verified, empty otherwise.
     *
     * @param reference, string
     * @param value, string
     * @param default, boolean
     */
    public function is_item_selected( $reference, $value, $default ) {
        $verified = $default || ($value==$reference);
        return ( ($verified)? ' selected="selected" style="background-color:lightgray;" ':'' );
    }





    /**
     * Check if reference equals to the value, and return checked if verified, empty otherwise.
     *
     * @param cible, string reference
     * @param test, string to test
     */
    public function is_check($cible,$test){
        $verified = ($cible==$test);
        return ( ($verified)? ' checked="checked" ':'' );
    }





    /**
     * Prepare a simple random slug key.
     *
     * @param none
     */
    public function get_random_anid(){
        return chr(rand(65,90)) . chr(rand(65,90)) . rand(0,9) . rand(0,9) . rand(0,9) . chr(rand(65,90));
    }








    /**
     * Get all Brand/Models/Ranges stored and returns a colection.
     *
     * @param none
     */
    public function get_supercars_brandmodel_posttype_storedin() {

        $_brandmodel_collection = array();

        // Ensures that the SuperCarVendors (dependency) classe has been properly installed.
        if ( class_exists('SuperCarVendors')) {

            // Retreives all SuperCarVenvors posts, and fetches each content...
            $supercars_brandmodel = SuperCarVendors::get_instance();
            $plugin_post_type = $supercars_brandmodel->plugin_post_type;

            $args = array( 
                'post_type'      => array( $plugin_post_type ), 
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC'
            );
            $loop_brandmodel = new WP_Query( $args );

            foreach ( $loop_brandmodel->posts as $brandmodel) {
                
                $content = $brandmodel->post_content;
                $content = strip_tags( $content );

                $brandmodels = explode("\n", $content);
                foreach ( $brandmodels as $model) {

                    $t_brand = trim( $brandmodel->post_title );
                    $t_model = trim( $model );

                    $s_brand = preg_replace("/(\s)+/", "_", strtolower( trim( $t_brand ) ));
                    $s_model = preg_replace("/(\s)+/", "_", strtolower( trim( $t_model ) ));

                    if( $s_brand != "" && $s_model != "" ){

                        // Makes parts as wellformed slugs (used into the backend UI behaviors to sync selection)
                        $_brandmodel_collection[] = array(
                            'ID'          => null,
                            'guid'        => $s_brand ."__". $s_model,
                            'post_title'  => $t_brand ." ". $t_model,
                            't_brandmodel__brand'  => $t_brand,
                            't_brandmodel__model'  => $t_model,
                            's_brandmodel__brand'  => $s_brand,
                            's_brandmodel__model'  => $s_model
                        );

                    }

                }

            }

        }else{

            /*
             * Warning, if we have no SuperCarVendors classe found !
             * So in this case, we inject an unused row to enrich the combo select with a notified item.
             *
             * This is a way to detect in the backend ui if the selection is correct (JS) or at once saving !
             */
            $_brandmodel_collection[] = array(
                'ID'          => null,
                'guid'        =>'empty__null',
                'post_title'  =>'Empty Null',
                '_brandmodel__brand'=>'Empty',
                '_brandmodel__model'=>'Null'
            );

        }

        return $_brandmodel_collection;
    }







    /**
     * Gets all Energies (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_energy_posttype_storedin() {

        $_energy_collection = array();

        // TODO : The right way may a setting page to fill these properties !
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_energy_collection[] = array('guid'=>'diesel',     'post_title'=>'Diesel');
        $_energy_collection[] = array('guid'=>'electrique', 'post_title'=>'Electrique');
        $_energy_collection[] = array('guid'=>'essence',    'post_title'=>'Essence');
        $_energy_collection[] = array('guid'=>'gpl',        'post_title'=>'GPL');
        $_energy_collection[] = array('guid'=>'hybride',    'post_title'=>'Hybride');

        return $_energy_collection;
    }







    /**
     * Gets all Gearboxes (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_gearbox_posttype_storedin() {

        $_gearbox_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_gearbox_collection[] = array('guid'=>'manuelle',     'post_title'=>'Boîte de vitesses manuelle');
        $_gearbox_collection[] = array('guid'=>'sequentielle', 'post_title'=>'Boîte de vitesses séquentielle');
        $_gearbox_collection[] = array('guid'=>'robotisee',    'post_title'=>'Boîte de vitesses robotisée');
        $_gearbox_collection[] = array('guid'=>'double',       'post_title'=>'Boîte de vitesses à double embrayage');
        $_gearbox_collection[] = array('guid'=>'automatique',  'post_title'=>'Boîte de vitesses automatique');
        $_gearbox_collection[] = array('guid'=>'continue',     'post_title'=>'Transmission à variation continue');
        $_gearbox_collection[] = array('guid'=>'hybrides',     'post_title'=>'Véhicules hybrides et électriques');

        return $_gearbox_collection;
    }







    /**
     * Gets all Colors (hardwrited) and return a collection.
     *
     * @param none
     */
    public function get_supercars_color_posttype_storedin() {

        $_color_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_color_collection[]=array('name'=>'noir',      'hexcode'=>'#070707');
        $_color_collection[]=array('name'=>'anthracite','hexcode'=>'#484648');
        $_color_collection[]=array('name'=>'gris',      'hexcode'=>'#cecece');
        $_color_collection[]=array('name'=>'blanc',     'hexcode'=>'#fafafa');
        $_color_collection[]=array('name'=>'beige',     'hexcode'=>'#cdc8b4');
        $_color_collection[]=array('name'=>'rouge',     'hexcode'=>'#a30f0f');

        $_color_collection[]=array('nblf'=>true);

        $_color_collection[]=array('name'=>'orange',    'hexcode'=>'#da630e');
        $_color_collection[]=array('name'=>'jaune',     'hexcode'=>'#e6da00');
        $_color_collection[]=array('name'=>'vert',      'hexcode'=>'#7ab125');
        $_color_collection[]=array('name'=>'bleu',      'hexcode'=>'#258ab1');
        $_color_collection[]=array('name'=>'violet',    'hexcode'=>'#673ab7');
        $_color_collection[]=array('name'=>'rose',      'hexcode'=>'#c54dab');
        $_color_collection[]=array('name'=>'marron',    'hexcode'=>'#56433c');
        $_color_collection[]=array('name'=>'autre',     'hexcode'=>'transparent');

        $_color_collection[]=array('nblf'=>true);


        return $_color_collection;
    }



    /**
     * Gets the all needed world countries (hardwrited) and return a colection.
     *
     * @param none
     */
    public function get_prefered_world_countries() {

        $_country_collection = array();

        // TODO: The right path should be a configuration page to fill these properties!
        // An entire list of world countries in a selectable list, and a needed list to use...
        // A settings section could be attacked from the frontend by an Options way to fetches availables items.
        $_country_collection[]=array('caption'=>"France");
        $_country_collection[]=array('caption'=>"Espagne");
        $_country_collection[]=array('caption'=>"Italie");
        $_country_collection[]=array('caption'=>"Pays-Bas");
        $_country_collection[]=array('caption'=>"Royaume-Uni");

        return $_country_collection;
    }

    public function get_other_world_countries() {

        $_country_collection = array();

        $_country_collection[]=array('caption'=>"Belgique");
        $_country_collection[]=array('caption'=>"Estonie");
        $_country_collection[]=array('caption'=>"Allemagne");
        $_country_collection[]=array('caption'=>"Irlande");
        $_country_collection[]=array('caption'=>"Luxembourg");
        $_country_collection[]=array('caption'=>"Monaco");
        $_country_collection[]=array('caption'=>"Roumanie");
        $_country_collection[]=array('caption'=>"Suisse");
        $_country_collection[]=array('caption'=>"Ukraine");

        return $_country_collection;
    }







    /**
     * Returns a collection of elements/entries depending on a data provider.
     *
     * @param provider, string method name of this Classe
     * @return data_result, array based on the method Classe return.
     */
    public function get_data_provider_collection($provider){
        
        $data_result = array();
        $provider_method = $provider.'__data_provider';
        $className   = SuperCar::get_instance();
        $data_result = $className->{$provider_method}();

        return $data_result;

    }



    /**
     * Gets all PDF/ATTACHMENT from media library.
     * Work with data provider {get_documents_from_media_library} or standalone.
     *
     * @param none
     * @return array of pdf documents « guid » and « post_title ».
     */
    public function get_documents_from_media_library__data_provider() {

        $args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/pdf',
            'post_status'    => 'inherit',
            'posts_per_page' => -1
        );

        $query_documents = new WP_Query( $args );
        
        $document_coll = array();
        foreach ( $query_documents->posts as $document) {
            $document_coll[] = array(
                'guid'       => $document->guid, 
                'post_title' => $document->post_title
            );
        }

        return $document_coll;
    }


    /**
     * Gets all WP PAGES from WP Theme.
     * Work with data provider {get_pages_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of wp pages « guid » and « post_title ».
     */
    
    public function get_pages_from_wp_theme__data_provider() {

        // Do not pick the pages having these templates :
        // - <launch.php>, ...
        // TODO, builds an admin panel to configure them from WP_Admin ;)
        $rejected_templates = array( '_launch.php', 'system.php', '_video-content.php' );

        $args = array(
            'sort_order'   => 'asc',
            'sort_column'  => 'post_title',
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'meta_key'     => '',
            'meta_value'   => '',
            'authors'      => '',
            'child_of'     => 0,
            'parent'       => -1,
            'exclude_tree' => '',
            'number'       => '',
            'offset'       => 0,
            'post_type'    => 'page',
            'post_status'  => 'publish'
        );

        $query_documents = new WP_Query( $args );

        $document_coll = array();
        foreach ( $query_documents->posts as $document) {

            // Picks this page only if template name is not in haystack.
            if( !in_array( get_post_meta( $document->ID, '_wp_page_template', TRUE ) , $rejected_templates ) ){

                $document_coll[] = array(
                    'guid'       => $document->ID, 
                    'post_title' => $document->post_title 
                );

            }

        }

        return $document_coll;
    }


    /**
     * Gets all WP POSTS from WP Theme.
     * Work with data provider {get_posts_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of wp posts « guid » and « post_title ».
     */
    
    public function get_posts_from_wp_theme__data_provider() {

        $args = array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'post',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'author'           => '',
            'post_status'      => 'publish',
            'suppress_filters' => true 
        );

        $query_documents = new WP_Query( $args );

        $document_coll = array();
        foreach ( $query_documents->posts as $document) {
            $document_coll[] = array(
                'guid'       => $document->ID, 
                'post_title' => $document->post_title
            );
        }

        return $document_coll;
    }


    /**
     * Gets all VIDEOS from WP Theme.
     * Work with data provider {get_video_from_wp_theme} or standalone.
     *
     * @param none
     * @return array of video attachment « guid » and « post_title ».
     */
    
    public function get_video_from_wp_theme__data_provider() {

        $args = array(
          'post_type'      => 'attachment',
          'numberposts'    => -1,
          'post_status'    => null,
          'post_parent'    => null, // any parent
          'post_mime_type' => 'video'
        ); 

        $query_documents = get_posts( $args );

        $document_coll = array();
        if ( $query_documents ) {
            foreach ( $query_documents as $document) {
                $document_coll[] = array(
                    'guid'       => $document->ID, 
                    'post_title' => $document->post_title
                );
            }
        }

        return $document_coll;
    }






    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function supercar_plugin_save_meta_box_data( $post_id ) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['supercar_plugin_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['supercar_plugin_meta_box_nonce'], 'supercar_plugin_meta_box' ) ) {
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



    /*
        // Note : There is only one entry defined as demo in the collection !
        //
        // Ensure that there is no other stored entry that is marked as demo 
        // if the current one has been defined « demo » / _supercar__demoplay set to 1 !
        if( sanitize_text_field( $_POST['_supercar__demoplay'] ) == '1' ){
            
            $args = array( 'post_type' => array( $this->plugin_post_type ) );
            $loop = new WP_Query( $args );
            
            while ( $loop->have_posts() ) : $loop->the_post();
                
                global $post; setup_postdata($post);
                update_post_meta($post->ID, '_supercar__demoplay', '');

            endwhile;

        }
    /**/




        /**
         * Now, we can save into the WPDB Post all of our meta custom fields, sanitized ;)
         *
         */
        $save_dbarray = array(

            '_supercar__brand_collection'   => sanitize_text_field( $_POST['_supercar__brand_collection'] ),
            '_supercar__model_collection'   => sanitize_text_field( $_POST['_supercar__model_collection'] ),
            '_supercar__brandmodel_version' => sanitize_text_field( $_POST['_supercar__brandmodel_version'] ),
            '_supercar__dateyear'           => sanitize_text_field( $_POST['_supercar__dateyear'] ),
            '_supercar__km'                 => sanitize_text_field( $_POST['_supercar__km'] ),
            '_supercar__system_energy'      => sanitize_text_field( $_POST['_supercar__system_energy'] ),
            '_supercar__gear_box'           => sanitize_text_field( $_POST['_supercar__gear_box'] ),
            '_supercar__power_horse'        => sanitize_text_field( $_POST['_supercar__power_horse'] ),
            '_supercar__doors'              => sanitize_text_field( $_POST['_supercar__doors'] ),
            '_supercar__color'              => sanitize_text_field( $_POST['_supercar__color'] ),
            '_supercar__finish'             => sanitize_text_field( $_POST['_supercar__finish'] ),
            '_supercar__client_hello'       => sanitize_text_field( $_POST['_supercar__client_hello'] ),
            '_supercar__report'             => sanitize_text_field( $_POST['_supercar__report'] ),
            '_supercar__address_country'    => sanitize_text_field( $_POST['_supercar__address_country'] ),
            '_supercar__address_city'       => sanitize_text_field( $_POST['_supercar__address_city'] ),
            '_supercar__address_zipcode'    => sanitize_text_field( $_POST['_supercar__address_zipcode'] ),
            '_supercar__price'              => sanitize_text_field( $_POST['_supercar__price'] ),
            '_supercar__selection'          => sanitize_text_field( $_POST['_supercar__selection'] ),
            '_supercar__gallery_ids'        => sanitize_text_field( $_POST['_supercar__gallery_ids'] ),
            '_supercar__unique_id'          => sanitize_text_field( $_POST['_supercar__unique_id'] )

        );

        // Saves values from created array into db...
        // Updates the meta fields in the database (deleting and updating is a clear way to strore properly).
        foreach($save_dbarray as $meta_key=>$meta_value) {
        
            delete_post_meta($post_id, $meta_key);
            update_post_meta($post_id, $meta_key, $meta_value);

        }

        // We can delete depreciated, unsued and old metas here to maintains uptodate the wordpress plugin mechanism.
        delete_post_meta($post_id, '_supercar__enabled');


        // Removes a meta key only if its value is defined to '0' (zero)
        // Keep only meta sets to '1'
        if( intval( $_POST['_supercar__selection'] ) == 0 || empty( $_POST['_supercar__selection'] ) || !isset( $_POST['_supercar__selection'] ) ) {
            delete_post_meta($post_id, '_supercar__selection');
        }
    
    }





    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The SuperCar object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SuperCar ) ) {
            self::$instance = new SuperCar();
        }

        return self::$instance;

    }

}

// Loads the main plugin class.
$Active__SuperCar = SuperCar::get_instance();
>>>>>>> 69d800751f0fdd3e6c15326a85c1175d8e48ca65
