<?php

defined( 'ABSPATH' ) || exit;
require_once SLPLUS_PLUGINDIR . 'include/base_class.addon.php';
// Define some constants for use by this add-on
slp_ext_maybe_define_constant( 'SLP_EXT_SECTION_SLUG_EXT', 'ext_section_extenders' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SECTION_SLUG_UML', 'ext_section_user_managed' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_OPTION_NAME', 'slp-extenders-options' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SHORT_SLUG_UML', 'slp-user-managed-locations' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SELECTED_NAV_ELEMENT_KEY', 'ext_selected_nav_element_key' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SELECTED_NAV_ELEMENT_PREFIX', '#wpcsl-option-' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SELECTED_NAV_ELEMENT_EXT', SLP_EXT_SECTION_SLUG_EXT );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SELECTED_NAV_ELEMENT_UML', SLP_EXT_SECTION_SLUG_UML );
//
slp_ext_maybe_define_constant( 'SLP_EXT_GROUP_SLUG_UML_TABLE', 'ext_group_user_managed' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_GROUP_SLUG_SETTINGS_EXT', 'ext_settings_general' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_GROUP_SLUG_SETTINGS_UML', 'ext_settings_user_managed' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_GROUP_SLUG_SETTINGS_SAVE', 'ext_settings_save' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_ASSIGN_STORE_USER', 'ext_assign_store_user' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_REMOVE_STORE_USER', 'ext_remove_store_user' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_FILTER_STORE_USER', 'ext_filter_store_user' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SUPPORT_URL', 'https://www.de-baat.nl/' . SLP_EXT_SHORT_SLUG );
// The URL link to the documentation support page
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION', 'ext_action' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_SAVE', 'ext_action_save' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_REQUEST', 'ext_action_request' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_USER_ALLOW', 'ext_action_user_allow' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ACTION_USER_DISALLOW', 'ext_action_user_disallow' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_NOTICE_SUCCESS', '10' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_NOTICE_INFO', '6' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_NOTICE_WARNING', '1' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_NOTICE_ERROR', '1' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_CSL_SEPARATOR', '--' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_STORE_USER_SLUG', 'users' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_STORE_USER_COL_ALLOWED', 'slp_user_col_allowed' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_STORE_USER_COL_LOCATIONS', 'slp_user_col_locations' );
//
slp_ext_maybe_define_constant( 'SLP_UML_STORE_USER_SLUG', 'store_user' );
//
slp_ext_maybe_define_constant( 'SLP_UML_STORE_USER_SLUG_COL', 'store_user_col' );
//
slp_ext_maybe_define_constant( 'SLP_UML_CAP_MANAGE_SLP', 'manage_slp' );
//
slp_ext_maybe_define_constant( 'SLP_UML_CAP_MANAGE_SLP_ADMIN', 'manage_slp_admin' );
//
slp_ext_maybe_define_constant( 'SLP_UML_CAP_MANAGE_SLP_USER', 'manage_slp_user' );
//
// SLP_Extenders Plugin Dir and
slp_ext_maybe_define_constant( 'SLPLUS_PLUGINDIR_EXT', plugin_dir_path( SLP_EXT_FILE ) );
slp_ext_maybe_define_constant( 'SLPLUS_PLUGINURL_EXT', plugins_url( '', SLP_EXT_FILE ) );
/**
 * Class SLP_Extenders
 *
 * @property        SLP_Extenders_Admin         $admin
 * @property        SLP_Extenders               $instance
 * @property        SLP_Extenders_Options       $options_defaults           Defaults for settable options for this plugin.
 * @property        SLP_Extenders_Social_Media  $addon_social_media         The addon object for Social Media.
 * @property        SLP_Extenders_User_Managed  $addon_user_managed         The addon object for User Managed.
 * @property        SLP_Extenders_Events        $addon_events               The addon object for Events.
 *
 */
class SLP_Extenders extends SLP_BaseClass_Addon
{
    protected  $class_prefix = SLP_EXT_CLASS_PREFIX ;
    /**
     * Settable options for this plugin.
     *
     * @var mixed[] $options
     */
    public  $options = array(
        'installed_version' => SLP_EXT_NO_INSTALLED_VERSION,
    ) ;
    /**
     * Defaults for settable options for this plugin.
     *
     * @var mixed[] $options_defaults
     */
    // public        $options_defaults             = array(
    // // General Extenders Options
    // 'installed_version'                     => '',
    // 'ext_social_media_enabled'              => '1',
    // 'ext_user_managed_enabled'              => '1',
    // 'ext_events_enabled'                    => '1',
    // 'ext_default_section'                   => SLP_EXT_SECTION_SLUG_EXT,
    // // Social Media Options
    // 'ext_social_icon_location'              => '',
    // 'ext_social_per_page'                   => '20',
    // 'ext_sme_label_social_name'             => 'Social Media: ',
    // 'ext_sme_label_social_slug'             => 'Social List: ',
    // 'ext_sme_show_option_all_name'          => '',
    // 'ext_sme_show_option_all_slug'          => 'Any Social Media',
    // 'ext_sme_show_social_name_on_search'    => '0',
    // 'ext_sme_show_social_slug_on_search'    => '0',
    // 'ext_sme_default_icons'                 => '',
    // 'ext_sme_show_icon_array'               => '1',
    // 'ext_sme_show_legend_block'             => '0',
    // 'ext_sme_show_legend_text'              => '0',
    // 'ext_sme_hide_empty'                    => '1',
    // // User Managed Options
    // 'ext_uml_publish_location'              => '1',
    // 'ext_uml_default_user_allowed'          => '1',
    // 'ext_uml_show_uml_buttons'              => '0',
    // // Event Location Options
    // 'ext_event_icon_location'               => '',
    // 'ext_event_per_page'                    => '20',
    // 'ext_elm_label_event_name'              => 'Event Name: ',
    // 'ext_elm_label_event_slug'              => 'Event List: ',
    // 'ext_elm_label_event_category'          => 'Event Category: ',
    // 'ext_elm_label_event_status'            => 'Event Status: ',
    // 'ext_elm_show_option_all_name'          => '',
    // 'ext_elm_show_option_all_slug'          => 'Any Event',
    // 'ext_elm_show_option_all_category'      => 'Any Category',
    // 'ext_elm_show_option_all_status'        => 'Any Status',
    // 'ext_elm_show_event_name_on_search'     => '0',
    // 'ext_elm_show_event_slug_on_search'     => '0',
    // 'ext_elm_show_event_category_on_search' => '0',
    // 'ext_elm_show_event_status_on_search'   => '0',
    // 'ext_elm_default_icons'                 => '',
    // 'ext_elm_show_icon_array'               => '1',
    // 'ext_elm_show_legend_block'             => '0',
    // 'ext_elm_show_legend_text'              => '0',
    // 'ext_elm_hide_empty'                    => '1',
    // );
    public  $admin ;
    public static  $instance ;
    public  $ext_date_format ;
    public  $ext_time_format ;
    public  $ext_datetime_format = null ;
    public  $addon_social_media ;
    public  $addon_user_managed ;
    public  $addon_events ;
    public  $remote_version = '' ;
    /**
     * Initialize a singleton of this object.
     *
     * @return SLP_Extenders
     */
    public static function init()
    {
        static  $instance = false ;
        
        if ( !$instance ) {
            load_plugin_textdomain( 'slp-extenders', false, SLP_EXT_REL_DIR . '/languages/' );
            $instance = new SLP_Extenders( array(
                'version'                  => SLP_EXT_VERSION,
                'min_slp_version'          => SLP_EXT_MIN_SLP,
                'name'                     => __( 'Extenders', 'slp-extenders' ),
                'option_name'              => SLP_EXT_OPTION_NAME,
                'file'                     => SLP_EXT_FILE,
                'activation_class_name'    => 'SLP_Extenders_Activation',
                'admin_class_name'         => 'SLP_Extenders_Admin',
                'ajax_class_name'          => 'SLP_Extenders_AJAX',
                'userinterface_class_name' => 'SLP_Extenders_UI',
            ) );
        }
        
        return $instance;
    }
    
    /**
     * Run these things during invocation. (called from base object in __construct)
     */
    protected function initialize()
    {
        $this->slplus->min_add_on_versions[SLP_EXT_SHORT_SLUG] = SLP_EXT_VERSION;
        // Get date and time formats
        $this->get_ext_datetime_format();
        $this->debugMP( 'msg', __FUNCTION__ . ' this->ext_datetime_format =!' . $this->ext_datetime_format . '!' );
        parent::initialize();
    }
    
    /**
     * Run these things during invocation. (called from base object in __construct)
     */
    function get_ext_datetime_format()
    {
        if ( $this->ext_datetime_format != null ) {
            return $this->ext_datetime_format;
        }
        // Get date and time formats
        $this->ext_date_format = get_option( 'date_format' );
        if ( empty($this->ext_date_format) ) {
            $this->ext_date_format = 'dd-mm-yyyy';
        }
        $this->ext_time_format = get_option( 'time_format' );
        if ( empty($this->ext_time_format) ) {
            $this->ext_time_format = 'H:i';
        }
        $this->ext_datetime_format = $this->ext_date_format . ' ' . $this->ext_time_format;
        $this->debugMP( 'msg', __FUNCTION__ . ' this->ext_datetime_format =!' . $this->ext_datetime_format . '!' );
        return $this->ext_datetime_format;
    }
    
    /**
     * Add cross-element hooks & filters.
     *
     * Haven't yet moved all items to the AJAX and UI classes.
     */
    function add_hooks_and_filters()
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        // Get date and time formats
        $this->get_ext_datetime_format();
        $this->debugMP( 'msg', __FUNCTION__ . ' this->ext_datetime_format =!' . $this->ext_datetime_format . '!' );
        // Add Icons
        //		add_filter( 'slp_icon_directories', array( $this, 'add_icon_directory' ), 10 );
        //		add_filter( 'wp_title', array( $this, 'modify_page_title' ), 20, 3 );
    }
    
    /**
     * Check whether the current version of this Add On works with the latest version of the SLP base plugin.
     * This is already checked against the SLP_ELM_MIN_SLP version in the loader
     *
     * @return boolean
     */
    private function check_my_version_compatibility()
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started but not needed for version=' . $this->version );
        return true;
    }
    
    /**
     * Get the latest version of this Add On from Freemius.
     *
     * @return string
     */
    function get_latest_version_from_freemius()
    {
        // Get the Freemius object for this plugin
        $fs = slp_ext_freemius_get_freemius();
        //$this->debugMP('pr', __CLASS__.'::'.__FUNCTION__ . ' found fs=', $fs );
        // Get the _storage object of this FS Freemius object
        $_slug = $fs->get_slug();
        $_module_type = $fs->get_module_type();
        $_storage = FS_Storage::instance( $_module_type, $_slug );
        //$this->debugMP('pr', __CLASS__.'::'.__FUNCTION__ . ' found _storage=', $_storage );
        $this->remote_version = $_storage->plugin_last_version;
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' found remote_version=' . $this->remote_version . ' for _module_type=' . $_module_type . ' and _slug=' . $_slug );
        return $this->remote_version;
    }
    
    /**
     * Creates updates object AND checks for updates for this add-on.
     * Not needed as this is handled by Freemius
     *
     * @param boolean $force
     */
    function create_object_Updates( $force )
    {
        $latest_version = $this->get_latest_version_from_freemius();
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' found version=' . $this->version . ' and latest_version=' . $latest_version );
    }
    
    /**
     * Add our icon directory to the list used by SLP.
     *
     * @param mixed[] $directories - array of directories.
     *
     * @return mixed[]
     */
    function add_icon_directory( $directories )
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        return $directories;
    }
    
    /**
     * Things we do at the start.
     */
    protected function at_startup()
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        // Create the addon objects
        $this->create_object_addon_objects();
    }
    
    /**
     * Set the admin menu items.
     *
     * @param mixed[] $menuItems
     *
     * @return mixed[]
     */
    public function filter_AddMenuItems( $menuItems )
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        $this->createobject_Admin();
        // Create admin_settings objects
        $this->admin->create_object_extenders_admin_settings();
        $this->admin_menu_entries = array();
        $this->admin_menu_entries[] = array(
            'label'    => __( 'Extenders', 'slp-extenders' ),
            'slug'     => 'slp_extenders',
            'class'    => $this->admin->extenders_admin_settings,
            'function' => 'render_extenders_admin_settings',
        );
        return parent::filter_AddMenuItems( $menuItems );
    }
    
    /**
     * Initialize the options properties from the WordPress database.
     */
    function init_options()
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        // Set the defaults for first-run
        // Especially useful for gettext stuff you cannot put in the property definitions.
        //
        $this->option_defaults = $this->options;
        //$this->option_defaults['first_entry_for_city_selector'      ] = __( 'All Cities...', 'slp-extenders' );
        parent::init_options();
    }
    
    /**
     * Create and attach the addon info objects.
     */
    private function create_object_addon_objects()
    {
        $this->debugMP( 'msg', __CLASS__ . '::' . __FUNCTION__ . ' started.' );
        // $this->addon_user_managed = new SLP_Extenders_User_Managed( array( 'addon' => $this->addon ) );
        if ( !isset( $this->addon_user_managed ) ) {
            $this->addon_user_managed = new SLP_Extenders_User_Managed( array(
                'addon' => $this,
            ) );
        }
    }
    
    /**
     * Create a timestamp for the current time
     *
     * @return timestamp
     */
    static function create_timestamp_now( $timezone_format = '' )
    {
        if ( $timezone_format == '' ) {
            $timezone_format = _x( 'Y-m-d G:i:s', 'timezone date format', 'slp-extenders' );
        }
        return date_i18n( $timezone_format );
    }
    
    /**
     * Create a Map Settings Debug My Plugin panel.
     *
     * @return null
     */
    static function create_DMPPanels()
    {
        if ( !isset( $GLOBALS['DebugMyPlugin'] ) ) {
            return;
        }
        if ( class_exists( 'DMPPanelSLPEXT' ) == false ) {
            require_once 'class.dmppanels.php';
        }
        $GLOBALS['DebugMyPlugin']->panels['slp.ext'] = new DMPPanelSLPEXT();
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