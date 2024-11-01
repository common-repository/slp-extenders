<?php

defined( 'ABSPATH' ) || exit;
require_once SLPLUS_PLUGINDIR . '/include/base_class.ajax.php';
/**
 * Holds the ajax-only code.
 *
 * This allows the main plugin to only include this file in AJAX mode
 * via the slp_init when DOING_AJAX is true.
 *
 * @property        SLP_Extenders                     $addon
 * @property        SLP_Extenders_AJAX_Social_Media   $ajax_social_media         The AJAX object for Social Media.
 * @property        SLP_Extenders_AJAX_User_Managed   $ajax_user_managed         The AJAX object for User Managed.
 * @property        SLP_Extenders_AJAX_Events         $ajax_events               The AJAX object for Events.
 *
 */
class SLP_Extenders_AJAX extends SLP_BaseClass_AJAX
{
    private  $ajax_social_media ;
    private  $ajax_user_managed ;
    private  $ajax_events ;
    public  $query_params_valid = array( 'options' ) ;
    private  $upload_transient = 'slp_uploads_path' ;
    public  $valid_actions = array(
        'csl_ajax_onload',
        'csl_ajax_search',
        'slp_background_location_download',
        'slp_change_option',
        'slp_clear_import_messages',
        'slp_create_page',
        'slp_download_report_csv',
        'slp_download_locations_csv',
        'slp_get_country_list',
        'slp_get_state_list'
    ) ;
    /**
     * Set up our environment.
     *
     */
    final function initialize()
    {
        $this->addon = SLP_Extenders_Get_Instance();
        parent::initialize();
    }
    
    /**
     * Add our specific AJAX filters.
     *
     */
    function add_ajax_hooks()
    {
        // Start the AJAX for Social_Media if enabled
        if ( $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            $this->add_ajax_hooks_for_user_managed();
        }
        $this->add_global_hooks();
    }
    
    /**
     * Global hooks, exposed possibly even outside AJAX.
     */
    public function add_global_hooks()
    {
        //		add_filter( 'slp_results_marker_data' , array( $this , 'modify_ajax_markers' ) );
    }
    
    /**
     * Attach the ajax objects.
     */
    private function add_ajax_hooks_for_user_managed()
    {
        if ( !isset( $this->ajax_user_managed ) ) {
            $this->ajax_user_managed = new SLP_Extenders_AJAX_User_Managed();
        }
        $this->ajax_user_managed->add_ajax_hooks();
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