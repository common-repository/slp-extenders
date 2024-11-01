<?php

defined( 'ABSPATH' ) || exit;
require_once SLPLUS_PLUGINDIR . 'include/module/admin_tabs/SLP_BaseClass_Admin.php';
/**
 * Holds the admin-only code.
 *
 * This allows the main plugin to only include this file in admin mode
 * via the admin_menu call.   Reduces the front-end footprint.
 *
 * @property        SLP_Extenders                     $addon
 * @property        SLP_Extenders_Activation          $extenders_activation       The extenders_activation object.
 * @property        SLP_Extenders_Admin_Settings      $extenders_admin_settings   The extenders_settings tab.
 * @property        SLP_Extenders_Admin_Social_Media  $admin_social_media         The admin object for Social Media.
 * @property        SLP_Extenders_Admin_User_Managed  $admin_user_managed         The admin object for User Managed.
 * @property        SLP_Extenders_Admin_Events        $admin_events               The admin object for Events.
 */
class SLP_Extenders_Admin extends SLP_BaseClass_Admin
{
    protected  $class_prefix = 'SLP_Extenders_' ;
    public  $addon ;
    public  $extenders_activation ;
    public  $extenders_admin_settings ;
    public  $admin_social_media ;
    public  $admin_user_managed ;
    public  $admin_events ;
    public  $settings_pages = array(
        'slp_extenders'  => array(
        'ext_social_media_enabled',
        'ext_user_managed_enabled',
        'ext_events_enabled',
        'ext_social_icon_location',
        'ext_social_per_page',
        'ext_uml_publish_location',
        'ext_uml_default_user_allowed',
        'ext_uml_show_uml_buttons',
        'ext_event_icon_location',
        'ext_event_per_page'
    ),
        'slp_experience' => array(
        'ext_sme_show_social_name_on_search',
        'ext_sme_show_social_slug_on_search',
        'ext_sme_default_icons',
        'ext_sme_show_icon_array',
        'ext_sme_show_legend_block',
        'ext_sme_show_legend_text',
        'ext_sme_hide_empty',
        'ext_elm_show_event_name_on_search',
        'ext_elm_show_event_slug_on_search',
        'ext_elm_show_event_category_on_search',
        'ext_elm_show_event_status_on_search',
        'ext_elm_hide_empty',
        'ext_elm_default_icons',
        'ext_elm_show_icon_array',
        'ext_elm_show_legend_block',
        'ext_elm_show_legend_text'
    ),
    ) ;
    /**
     * A cache for the taxonomy objects.
     *
     * @var 
     */
    public  $taxonomyCache = array() ;
    public  $time_created = 123 ;
    /**
     * Add our SLP hooks and Filters for Admin Mode
     *
     * @uses \SLP_Extenders_Admin::add_slp_settings_to_wp_edit_category for filter stores_edit_form
     * @uses \SLP_Extenders_Admin::add_slp_settings_to_wp_add_category  for filter stores_add_form_fields
     * @uses \SLP_Extenders_Admin::add_managed_pages
     */
    public function add_hooks_and_filters()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        parent::add_hooks_and_filters();
        $this->debugMP( 'msg', __FUNCTION__ . ' started, BEFORE time_created = ' . $this->time_created );
        $this->time_created = $this->addon->create_timestamp_now( "H:i:s" );
        $this->debugMP( 'msg', __FUNCTION__ . ' started, AFTER time_created = ' . $this->time_created );
        $this->debugMP( 'pr', __FUNCTION__ . ' started with options:', $this->addon->options );
        // Create admin objects
        $this->create_object_admin_objects();
        $this->handle_slp_ext_action();
        // Load objects based on which admin page we are on.
        //
        //error_log( __CLASS__ . '::' . __FUNCTION__ . ' : _REQUEST = ' . esc_html(print_r( $_REQUEST, true )) );
        
        if ( isset( $_REQUEST['page'] ) ) {
            switch ( $_REQUEST['page'] ) {
                case SLP_EXT_ADMIN_PAGE_SLUG:
                    // Create admin_settings objects
                    $this->create_object_extenders_admin_settings();
                    add_action( 'admin_enqueue_scripts', array( $this, 'setup_extenders_scripts' ), 20 );
                    // add_action( 'finish_slp_specific_setup' , array( $this, 'slp_ext_finish_slp_specific_setup' ) );
                    break;
                case 'slp_info':
                    $this->create_object_info();
                    $this->create_object_extenders_admin_settings( true );
                    break;
                case 'slp_manage_locations':
                    // Create admin_settings objects
                    $this->create_object_extenders_admin_settings();
                    break;
            }
        } else {
            $this->create_object_extenders_admin_settings( true );
        }
    
    }
    
    /**
     * Add the required admin scripts.
     */
    public function setup_extenders_scripts()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        $min = '';
        wp_enqueue_style( 'datetimepicker', $this->addon->url . '/css/jquery.datetimepicker' . $min . '.css', false );
        // Include the jQuery DatePicker
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'datetimepicker_js', $this->addon->url . '/js/jquery.js' );
        wp_enqueue_script( 'slp_datetimepicker', $this->addon->url . '/js/jquery.datetimepicker.full.js' );
        wp_enqueue_script( 'slp_ext_script', $this->addon->url . '/js/slp-ext-admin.js' );
        // wp_enqueue_script( 'datetimepicker',  $this->addon->url . '/js/jquery.datetimepicker.js', array ( 'jquery' ), 1.1, true);
    }
    
    /**
     * Create and attach the admin objects.
     */
    private function create_object_admin_objects()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'msg', __FUNCTION__ . ' started with ext_user_managed_enabled ' . $this->slplus->SmartOptions->ext_user_managed_enabled->is_true );
        $this->debugMP( 'msg', __FUNCTION__ . ' started, BEFORE time_created = ' . $this->time_created );
        $this->time_created = $this->addon->create_timestamp_now( "H:i:s" );
        $this->debugMP( 'msg', __FUNCTION__ . ' started, AFTER time_created = ' . $this->time_created );
        // Create the User_Managed objects
        //
        $this->admin_user_managed = new SLP_Extenders_Admin_User_Managed( array(
            'addon' => $this->addon,
            'admin' => $this,
        ) );
        $this->debugMP( 'msg', __FUNCTION__ . ' SLP_Extenders_Admin_User_Managed created admin_user_managed.' );
    }
    
    /**
     * Handle the actions defined by WPSL_EXT_ACTION_REQUEST
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_slp_ext_action()
    {
        // $this->debugMP('msg',__FUNCTION__.' started.');
        // check whether action is set
        $cur_action = $this->get_slp_ext_action();
        // $this->debugMP('msg',__FUNCTION__.' cur_action: ' . $cur_action);
        if ( $cur_action ) {
            switch ( $cur_action ) {
                // Allow the users managing locations
                case SLP_EXT_ACTION_USER_ALLOW:
                    $userValues = $this->get_ids_from_array( $_REQUEST, SLP_EXT_STORE_USER_SLUG );
                    $this->debugMP( 'pr', __FUNCTION__ . ' Users allowed as store editor:', $userValues );
                    foreach ( $userValues as $curUser ) {
                        $this->admin_user_managed->slp_uml_user_allow( $curUser );
                    }
                    break;
                    // Disallow the users managing locations
                // Disallow the users managing locations
                case SLP_EXT_ACTION_USER_DISALLOW:
                    $userValues = $this->get_ids_from_array( $_REQUEST, SLP_EXT_STORE_USER_SLUG );
                    $this->debugMP( 'pr', __FUNCTION__ . ' Users disallowed as store editor:', $userValues );
                    foreach ( $userValues as $curUser ) {
                        $this->admin_user_managed->slp_uml_user_disallow( $curUser );
                    }
                    break;
                default:
                    break;
            }
        }
    }
    
    /**
     * Get the action defined by SLP_EXT_ACTION_REQUEST or action
     *
     * @since 1.0.0
     * @return void
     */
    public function get_slp_ext_action()
    {
        // $this->debugMP('msg',__FUNCTION__.' started.');
        if ( isset( $_REQUEST[SLP_EXT_ACTION_REQUEST] ) ) {
            return sanitize_key( $_REQUEST[SLP_EXT_ACTION_REQUEST] );
        }
        if ( isset( $_REQUEST['action'] ) ) {
            return sanitize_key( $_REQUEST['action'] );
        }
        if ( isset( $_REQUEST['action2'] ) ) {
            return sanitize_key( $_REQUEST['action2'] );
        }
        return false;
    }
    
    /**
     * Create and attach the admin info object.
     */
    private function create_object_info()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        if ( !isset( $this->info ) ) {
            //			require_once( SLP_EXT_REL_DIR . 'include/module/admin/SLP_Extenders_Admin_Info.php' );
            $this->info = new SLP_Extenders_Admin_Info( array(
                'addon' => $this->addon,
                'admin' => $this,
            ) );
        }
    }
    
    /**
     * Create the settings interface object and attach to this->extenders_admin_settings
     */
    function create_object_extenders_admin_settings( $slp_menu_only = false )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'msg', __FUNCTION__ . ' started, BEFORE time_created = ' . $this->time_created );
        $this->time_created = $this->addon->create_timestamp_now( "H:i:s" );
        $this->debugMP( 'msg', __FUNCTION__ . ' started, AFTER time_created = ' . $this->time_created );
        
        if ( !isset( $this->extenders_admin_settings ) ) {
            $this->extenders_admin_settings = new SLP_Extenders_Admin_Settings( array(
                'addon'         => $this->addon,
                'admin'         => $this,
                'slp_menu_only' => $slp_menu_only,
            ) );
            $this->debugMP( 'msg', __FUNCTION__ . ' SLP_Extenders_Admin_Settings created.' );
        }
    
    }
    
    /**
     * Create the activation interface object and attach to this->extenders_activation
     */
    function create_object_extenders_activation()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        
        if ( !isset( $this->extenders_activation ) ) {
            $this->extenders_activation = new SLP_Extenders_Activation( array(
                'addon' => $this->addon,
                'admin' => $this,
            ) );
            $this->debugMP( 'msg', __FUNCTION__ . ' SLP_Extenders_Activation created.' );
        }
    
    }
    
    /**
     * If there is a newer version get the link.
     *
     * @return string
     */
    public function get_newer_version()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'msg', __FUNCTION__ . ' TODO: Replace with Freemius function.' );
        return '';
        return 'get_newer_version TODO: Replace with Freemius function.';
    }
    
    /**
     * Deactivate any plugins that this add-on replaces.
     */
    private function deactivate_replaced_addons()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        $replaced_addons = array( 'slp-user-managed-locations' );
        foreach ( $replaced_addons as $addon_slug ) {
            
            if ( $this->slplus->AddOns->get( $addon_slug, 'active' ) ) {
                deactivate_plugins( $this->slplus->AddOns->instances[$addon_slug]->file );
                $this->slplus->Helper->add_wp_admin_notification( sprintf( __( 'The %s add-on deactivated the conflicting %s add-on. ', 'slp-extenders' ), $this->addon->name, $this->slplus->AddOns->instances[$addon_slug]->name ) );
            }
        
        }
    }
    
    /**
     * Execute some admin startup things for this add-on pack.
     */
    function do_admin_startup()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        parent::do_admin_startup();
    }
    
    /**
     * Add the JS settings for admin.
     */
    function enqueue_admin_javascript( $hook )
    {
        $this->js_pages = array( SLP_ADMIN_PAGEPRE . SLP_EXT_ADMIN_PAGE_SLUG, SLP_ADMIN_PAGEPRE . 'slp_manage_locations' );
        if ( !parent::ok_to_enqueue_admin_js( $hook ) ) {
            return;
        }
        parent::enqueue_admin_javascript( $hook );
    }
    
    /**
     * Add our admin pages to the valid admin page slugs.
     *
     * @used-by SLP_Extenders_Admin_UI->is_our_admin_page()
     *
     * @param string[] $slugs admin page slugs
     *
     * @return string[] modified list of admin page slugs
     */
    function filter_AddOurAdminSlug( $slugs )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $slugs = parent::filter_AddOurAdminSlug( $slugs );
        $slugs = array_merge( $slugs, array( SLP_EXT_ADMIN_PAGE_SLUG, SLP_ADMIN_PAGEPRE . SLP_EXT_ADMIN_PAGE_SLUG ) );
        //$this->debugMP('pr', __FUNCTION__ . ' returned slugs:', $slugs);
        return $slugs;
    }
    
    /**
     * Add meta links specific for this AddOn.
     *
     * @param string[] $links
     * @param string   $file
     *
     * @return string
     */
    function add_meta_links( $links, $file )
    {
        
        if ( $file == $this->addon->slug ) {
            // Add Documentation support_url link
            $link_text = __( 'Documentation', 'slp-extenders' );
            $links[] = sprintf(
                '<a href="%s" title="%s" target="store_locator_plus">%s</a>',
                SLP_EXT_SUPPORT_URL,
                $link_text,
                $link_text
            );
            // Add Settings link
            $link_text = __( 'Settings', 'slp-extenders' );
            $links[] = sprintf(
                '<a href="%s" title="%s">%s</a>',
                admin_url( 'admin.php?page=' . SLP_EXT_ADMIN_PAGE_SLUG ),
                $link_text,
                $link_text
            );
            // $newer_version = $this->get_newer_version();
            // if ( ! empty( $newer_version ) ) {
            // $links[] = '<strong>' . sprintf( __( 'Version %s in production ', 'slp-extenders' ), $newer_version ) . '</strong>';
            // }
        }
        
        return $links;
    }
    
    /**
     * Set valid options from the incoming REQUEST
     *
     * @param mixed  $val - the value of a form var
     * @param string $key - the key for that form var
     */
    function set_ValidOptions( $val, $key )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $simpleKey = str_replace( SLPLUS_PREFIX . '-', '', $key );
        if ( array_key_exists( $simpleKey, $this->addon->options ) ) {
            $_POST[$this->addon->option_name][$simpleKey] = stripslashes_deep( $val );
        }
    }
    
    /**
     * Deactivate the competing add-on packs.
     */
    function update_install_info()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        //		parent::update_install_info();
        $this->deactivate_replaced_addons();
        // Do a check on the activation update
        $this->create_object_extenders_activation();
        $this->extenders_activation->update();
    }
    
    /**
     * Set the selected_nav_element to set focus to the ext_default_section
     */
    function set_selected_nav_element( $ext_selected_nav_element = SLP_EXT_SECTION_SLUG_EXT )
    {
        $ext_default_section_slug = $this->slplus->SmartOptions->ext_default_section->value;
        $this->debugMP( 'msg', __FUNCTION__ . ' started with ext_selected_nav_element = ' . $ext_selected_nav_element . ' ext_default_section = ' . $ext_default_section_slug );
        // Only set selected_nav_element when not set before
        if ( isset( $_REQUEST['selected_nav_element'] ) ) {
            return;
        }
        // Process SLP_EXT_SELECTED_NAV_ELEMENT_KEY for ext pages
        if ( isset( $_REQUEST[SLP_EXT_SELECTED_NAV_ELEMENT_KEY] ) ) {
            $ext_selected_nav_element = sanitize_key( $_REQUEST[SLP_EXT_SELECTED_NAV_ELEMENT_KEY] );
        }
        // Set the variables to handle selected_nav_element
        global  $slplus ;
        $_REQUEST['selected_nav_element'] = SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX . $ext_selected_nav_element;
        $this->debugMP( 'msg', __FUNCTION__ . ' reset with selected_nav_element = ' . $ext_selected_nav_element );
        $slplus->clean['selected_nav_element'] = SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX . $ext_selected_nav_element;
        $_SERVER['REQUEST_URI'] .= SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX . $ext_selected_nav_element;
    }
    
    /**
     * Get the selected_nav_element to handle actions
     */
    function get_selected_nav_element()
    {
        // global $slplus;
        // $ext_default_section_slug = $this->slplus->SmartOptions->ext_default_section->value;
        // $this->debugMP('msg', __FUNCTION__ . ' started with ext_default_section = ' . $ext_default_section_slug );
        $this->debugMP( 'msg', __FUNCTION__ . ' found _SERVER[REQUEST_URI] ext_selected_nav_element = ' . $_SERVER['REQUEST_URI'] );
        // $this->debugMP('pr',  __FUNCTION__ . ' found ext_selected_nav_element _REQUEST = ', $_REQUEST );
        // $this->debugMP('pr',  __FUNCTION__ . ' found ext_selected_nav_element slplus->clean = ', $slplus->clean );
        $ext_selected_nav_element = sanitize_key( $_SERVER['REQUEST_URI'] );
        $this->debugMP( 'msg', __FUNCTION__ . ' found _SERVER[REQUEST_URI] ext_selected_nav_element = ' . $ext_selected_nav_element );
        // Process SLP_EXT_SELECTED_NAV_ELEMENT_KEY for ext pages
        
        if ( isset( $_REQUEST[SLP_EXT_SELECTED_NAV_ELEMENT_KEY] ) ) {
            $ext_selected_nav_element = sanitize_key( $_REQUEST[SLP_EXT_SELECTED_NAV_ELEMENT_KEY] );
            $this->debugMP( 'msg', __FUNCTION__ . ' found _REQUEST[SLP_EXT_SELECTED_NAV_ELEMENT_KEY] ext_selected_nav_element = ' . $ext_selected_nav_element );
        }
        
        // Test ext_selected_nav_element for SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX
        $this->debugMP( 'msg', __FUNCTION__ . ' looking for ext_selected_nav_element containing  ' . SLP_EXT_SELECTED_NAV_ELEMENT_SME );
        // if ( strpos( $ext_selected_nav_element , SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX . SLP_EXT_SELECTED_NAV_ELEMENT_SME ) !== false ) {
        if ( strpos( $ext_selected_nav_element, SLP_EXT_SELECTED_NAV_ELEMENT_SME ) !== false ) {
            return SLP_EXT_SELECTED_NAV_ELEMENT_SME;
        }
        $this->debugMP( 'msg', __FUNCTION__ . ' looking for ext_selected_nav_element containing  ' . SLP_EXT_SELECTED_NAV_ELEMENT_ELM );
        // if ( strpos( $ext_selected_nav_element , SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX . SLP_EXT_SELECTED_NAV_ELEMENT_ELM ) !== false ) {
        if ( strpos( $ext_selected_nav_element, SLP_EXT_SELECTED_NAV_ELEMENT_ELM ) !== false ) {
            return SLP_EXT_SELECTED_NAV_ELEMENT_ELM;
        }
        return false;
    }
    
    /**
     * Get all Taxonomy Objects from the database and put them in an indexed cache
     *
     * @param boolean $taxonomy
     * @return array taxonomyObjects
     */
    public function get_taxonomy_names( $allTaxonomies, $taxonomyIds = '', $nameGlue = ',' )
    {
        $searchIds = explode( ',', $taxonomyIds );
        $resultNames = array();
        foreach ( $searchIds as $taxID ) {
            if ( isset( $allTaxonomies[$taxID] ) ) {
                $resultNames[] = $allTaxonomies[$taxID]->name;
            }
        }
        $taxonomyNames = implode( $nameGlue, $resultNames );
        // $this->debugMP('msg',__FUNCTION__ . ' translated (' . $taxonomyIds . ') into ' . $taxonomyNames);
        return $taxonomyNames;
    }
    
    /**
     * Creates the string to use a name for the setting.
     *
     * @param bool $addform - true if rendering add socials form
     */
    function create_SettingsSetting( $settingName, $settingAction, $settingID = '' )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' settingName = ' . $settingName . ', settingID = ' . $settingID . '.' );
        return $settingAction . SLP_EXT_CSL_SEPARATOR . $settingName . SLP_EXT_CSL_SEPARATOR . $settingID;
    }
    
    /**
     * Get the string used as name for the setting.
     *
     * @param bool $addform - true if rendering add socials form
     */
    function get_SettingsSettingKey( $settingKey, $settingAction, $settingID = '' )
    {
        $this->debugMP( 'msg', __FUNCTION__, ' settingKey = ' . $settingKey . ', settingID = ' . $settingID . '.' );
        $keyPattern = '#^.*' . $settingAction . SLP_EXT_CSL_SEPARATOR . '(.*)' . SLP_EXT_CSL_SEPARATOR . '.*#';
        $keyReplacement = '\\1';
        $newSettingKey = preg_replace( $keyPattern, $keyReplacement, $settingKey );
        $this->debugMP( 'msg', '', ' keyPattern = ' . $keyPattern . ', keyReplacement = ' . $keyReplacement . '.' );
        $this->debugMP( 'msg', '', ' settingKey = ' . $settingKey . ', newSettingKey = ' . $newSettingKey . '.' );
        return $newSettingKey;
    }
    
    /**
     * Create the input string for a date_picker element.
     */
    function createstring_DateTimePicker(
        $settingName,
        $settingAction,
        $settingID,
        $settingValue,
        $splitTime = false
    )
    {
        $this->debugMP( 'msg', __FUNCTION__ );
        $this->debugMP( 'msg', __FUNCTION__ . ' theName = ' . $theName . ', settingValue = ' . $settingValue );
        $this->debugMP( 'msg', __FUNCTION__ . ' ext_date_format = ' . $this->addon->ext_date_format . ', ext_time_format = ' . $this->addon->ext_time_format );
        $this->debugMP( 'msg', __FUNCTION__ . ' ext_datetime_format = ' . $this->addon->ext_datetime_format );
        $theHTML = '';
        // Prepare some variables depending on the request
        $theID = $this->create_SettingsSetting( $settingName, $settingAction, '' );
        $theName = $this->create_SettingsSetting( $settingName, $settingAction, $settingID );
        // $theValue = $this->get_datepicker_datetime($settingValue, $this->addon->ext_date_format . ' ' . $this->addon->ext_time_format);
        $theValue = $this->get_datepicker_datetime( $settingValue, $this->addon->ext_datetime_format );
        // Create the html for the datetimepicker
        $theHTML .= '<input type="text" class="datetimepicker" id="';
        $theHTML .= $theID;
        $theHTML .= '" name="';
        $theHTML .= $theName;
        $theHTML .= '" value="';
        $theHTML .= $theValue;
        $theHTML .= '"/>';
        $theHTML .= '<span class="slp-ext-datetime-format">' . $this->addon->ext_datetime_format . '</span> ';
        $this->debugMP( 'pr', __FUNCTION__ . ' theName = ' . $theName . ' theValue = ' . $theValue . ', settingValue = ', $settingValue );
        return $theHTML;
    }
    
    /**
     * Create the input string for a date_picker element.
     */
    function createstring_DatePicker( $theName, $theValue, $splitTime = false )
    {
        $this->debugMP( 'msg', __FUNCTION__ );
        $this->debugMP( 'msg', __FUNCTION__ . ' theName = ' . $theName . ', theValue = ' . $theValue );
        $this->debugMP( 'msg', __FUNCTION__ . ' ext_date_format = ' . $this->addon->ext_date_format . ', ext_time_format = ' . $this->addon->ext_time_format );
        $theHTML = '';
        // Prepare some variables depending on the request
        
        if ( $splitTime ) {
            $nameDate = 'date_' . $theName;
            $nameTime = 'time_' . $theName;
            $valueDate = $this->get_datepicker_datetime( $theValue, $this->addon->ext_date_format );
            $valueTime = $this->get_datepicker_datetime( $theValue, $this->addon->ext_time_format );
        } else {
            $nameDate = $theName;
            $nameTime = $theName;
            $valueDate = $this->get_datepicker_datetime( $theValue, $this->addon->ext_date_format . ' ' . $this->addon->ext_time_format );
        }
        
        // Create the html for the datepicker
        $theHTML .= '<input type="text" class="slp_datepicker" id="';
        $theHTML .= $nameDate;
        $theHTML .= '" name="';
        $theHTML .= $nameDate;
        $theHTML .= '" value="';
        $theHTML .= $valueDate;
        $theHTML .= '"/>';
        // Create the time input box if required
        
        if ( $splitTime ) {
            $theHTML .= '&nbsp;&nbsp;<input type="text" class="slp_timepicker" id="';
            $theHTML .= $nameTime;
            $theHTML .= '" name="';
            $theHTML .= $nameTime;
            $theHTML .= '" size="6" value="';
            $theHTML .= $valueTime;
            $theHTML .= '"/>';
        }
        
        $this->debugMP( 'pr', __FUNCTION__ . ' theName = ' . $theName . ' nameDate = ' . $nameDate . ' valueDate = ' . $valueDate . ', theValue = ', $theValue );
        return $theHTML;
    }
    
    /**
     * Get a part of the value according to the format
     *
     * @param type $url
     * @return type
     */
    public function get_datepicker_datetime( $theValue, $format = null )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' theValue = ' . $theValue . ', format = ' . $format );
        
        if ( null === $format ) {
            // Get date and time formats
            $format = $this->addon->ext_date_format;
            if ( empty($format) ) {
                $format = 'Y-m-d';
            }
        }
        
        // $formatted_date = date_i18n( 'Y-m-d H:i:s', strtotime( $theValue ) );
        // if ( ! empty( $formatted_date ) ) {
        // $this->debugMP('msg',__FUNCTION__ . ' return mysql2date formatted_date = ' . mysql2date( $format, $formatted_date ) . ', format = ' . $format );
        // return mysql2date( $format, $formatted_date );
        // }
        
        if ( !empty($theValue) ) {
            $this->debugMP( 'msg', __FUNCTION__ . ' return mysql2date theValue = ' . mysql2date( $format, $theValue ) . ', format = ' . $format );
            return mysql2date( $format, $theValue );
        }
        
        return '';
    }
    
    /**
     * Get the ID values from the input array, e.g. _REQUEST
     *
     */
    public function get_ids_from_array( $input_array = null, $input_key = '' )
    {
        // $this->debugMP('msg',__FUNCTION__.' started.');
        $output_array = array();
        // Check input parameters
        if ( $input_array == null ) {
            return $output_array;
        }
        if ( $input_key == '' ) {
            return $output_array;
        }
        if ( !isset( $input_array[$input_key] ) ) {
            return $output_array;
        }
        // Get intvals for IDs
        
        if ( is_array( $input_array[$input_key] ) ) {
            foreach ( $input_array[$input_key] as $input_value ) {
                $output_array[] = intval( $input_value );
            }
        } else {
            $output_array[] = intval( $input_array[$input_key] );
        }
        
        return $output_array;
    }
    
    /**
     * Simplify the plugin debugMP interface.
     *
     * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
     *
     * @param string $type
     * @param string $hdr
     * @param string $msg
     */
    function debugMP( $type, $hdr, $msg = '' )
    {
        if ( $type === 'msg' && $msg !== '' ) {
            $msg = esc_html( $msg );
        }
        if ( $hdr !== '' ) {
            // Adding __CLASS__ to non-empty hdr
            $hdr = __CLASS__ . '::' . $hdr;
        }
        SLP_Extenders_debugMP(
            $type,
            $hdr,
            $msg,
            NULL,
            NULL,
            true
        );
    }

}