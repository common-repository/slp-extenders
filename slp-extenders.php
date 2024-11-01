<?php

/*
 * Plugin Name:  Store Locator Plus® | Extenders
 * Plugin URI:   https://www.de-baat.nl/slp-extenders/
 * Description:  Adds power user features like managing location based events, social media information and locations managed by other logged in users to Store Locator Plus®.
 * Author:       DeBAAT
 * Author URI:   https://www.de-baat.nl/slp/
 * License:      GPL3
 * Tested up to: 6.1.1
 * Version:      6.1.1
 *
 * Text Domain:  slp-extenders
 * Domain Path:  /languages/
 * 
 * 
 * Copyright 2022 De B.A.A.T. (slp-extenders@de-baat.nl)
 */
defined( 'ABSPATH' ) || exit;
// Define some constants for use by this add-on
slp_ext_maybe_define_constant( 'SLP_EXT_FREEMIUS_ID', '3300' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_SHORT_SLUG', 'slp-extenders' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_PREMIUM_SLUG', 'slp-extenders-premium' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_CLASS_PREFIX', 'SLP_Extenders_' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ADMIN_PAGE_SLUG', 'slp_extenders' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_ADMIN_PAGE_SLUG_FRE', 'slp_extenders-pricing' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_MIN_SLP', '5.5.0' );
//
slp_ext_maybe_define_constant( 'SLP_EXT_FILE', __FILE__ );
//
slp_ext_maybe_define_constant( 'SLP_EXT_REL_DIR', plugin_dir_path( SLP_EXT_FILE ) );
//
slp_ext_maybe_define_constant( 'WP_DEBUG_LOG_SLP_EXT', false );
//
slp_ext_maybe_define_constant( 'SLP_EXT_NO_INSTALLED_VERSION', '0.0.0' );
//
/**
 * Define a constant if it is not already defined.
 *
 * @param string $name  Constant name.
 * @param string $value Value.
 *
 * @since  1.0.0
 */
function slp_ext_maybe_define_constant( $name, $value )
{
    if ( !defined( $name ) ) {
        define( $name, $value );
    }
}

// Include Freemius SDK integration

if ( function_exists( 'slp_ext_freemius' ) ) {
    slp_ext_freemius()->set_basename( true, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'slp_ext_freemius' ) ) {
        // Create a helper function for easy SDK access.
        function slp_ext_freemius()
        {
            global  $slp_ext_freemius ;
            
            if ( !isset( $slp_ext_freemius ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $slp_ext_freemius = fs_dynamic_init( array(
                    'id'               => SLP_EXT_FREEMIUS_ID,
                    'slug'             => SLP_EXT_SHORT_SLUG,
                    'premium_slug'     => SLP_EXT_PREMIUM_SLUG,
                    'type'             => 'plugin',
                    'public_key'       => 'pk_defe79e6b8496c708359b5269248e',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Premium',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => true,
                    'trial'            => array(
                    'days'               => 30,
                    'is_require_payment' => false,
                ),
                    'menu'             => array(
                    'slug'    => SLP_EXT_ADMIN_PAGE_SLUG,
                    'account' => false,
                    'contact' => false,
                    'support' => false,
                    'parent'  => array(
                    'slug' => 'csl-slplus',
                ),
                ),
                    'is_live'          => true,
                ) );
            }
            
            return $slp_ext_freemius;
        }
        
        // Init Freemius.
        slp_ext_freemius();
        // Signal that SDK was initiated.
        do_action( 'slp_ext_freemius_loaded' );
        function slp_ext_freemius_settings_url()
        {
            return admin_url( 'admin.php?page=' . SLP_EXT_ADMIN_PAGE_SLUG );
        }
        
        slp_ext_freemius()->add_filter( 'connect_url', 'slp_ext_freemius_settings_url' );
        slp_ext_freemius()->add_filter( 'after_skip_url', 'slp_ext_freemius_settings_url' );
        slp_ext_freemius()->add_filter( 'after_connect_url', 'slp_ext_freemius_settings_url' );
        slp_ext_freemius()->add_filter( 'after_pending_connect_url', 'slp_ext_freemius_settings_url' );
    }
    
    /**
     * Get the Freemius object.
     *
     * @return string
     */
    function slp_ext_freemius_get_freemius()
    {
        return freemius( SLP_EXT_FREEMIUS_ID );
    }
    
    
    if ( function_exists( 'slp_ext_freemius' ) ) {
        slp_ext_freemius()->set_basename( false, __FILE__ );
        //	return;
    }
    
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && !empty($_POST['action']) && $_POST['action'] === 'heartbeat' ) {
        return;
    }
    function SLP_Extenders_loader()
    {
        require_once 'include/base/loader.php';
    }
    
    add_action( 'plugins_loaded', 'SLP_Extenders_loader' );
    function SLP_Extenders_Get_Instance()
    {
        global  $slplus ;
        return SLP_Extenders::get_instance();
        //	return $slplus->AddOns->get( 'slp-extenders', 'instance' );
    }
    
    function SLP_Extenders_admin_menu()
    {
        global  $_registered_pages ;
        $_registered_pages['admin_page_' . SLP_EXT_ADMIN_PAGE_SLUG] = true;
        $_registered_pages['admin_page_' . SLP_EXT_ADMIN_PAGE_SLUG_FRE] = true;
        $_registered_pages[SLP_EXT_ADMIN_PAGE_SLUG_FRE] = true;
        // error_log( __CLASS__ . '::' . __FUNCTION__ . ' : _registered_pages = ' . esc_html(print_r( $_registered_pages, true )) );
        global  $plugin_page ;
        // if ( $plugin_page === SLP_EXT_ADMIN_PAGE_SLUG ) {
        // $plugin_page = SLP_EXT_ADMIN_PAGE_SLUG_EXT;
        // }
    }
    
    function SLP_Extenders_admin_init()
    {
        global  $plugin_page ;
        // error_log( __CLASS__ . '::' . __FUNCTION__ . ' : plugin_page = ' . $plugin_page );
        //error_log( __CLASS__ . '::' . __FUNCTION__ . ' : _SERVER = ' . esc_html(print_r( $_SERVER, true )) );
        //error_log( __CLASS__ . '::' . __FUNCTION__ . ' : _REQUEST = ' . esc_html(print_r( $_REQUEST, true )) );
        // if ( substr($plugin_page, 0, strlen(SLP_EXT_ADMIN_PAGE_SLUG)) === SLP_EXT_ADMIN_PAGE_SLUG ) {
        // $plugin_page = SLP_EXT_ADMIN_PAGE_SLUG;
        // }
    }
    
    /**
     * Translate the slug for an add_on.
     *
     * @param object $this_addon this object for the addon
     * @param string $addon_slug slug for the addon
     *
     * @return object reference to this addon
     */
    function filter_ext_slp_get_addon( $this_addon, $addon_slug )
    {
        
        if ( strtolower( $addon_slug ) == 'extenders' ) {
            $this_ext_addon = SLP_Extenders_Get_Instance();
            return $this_ext_addon;
            return SLP_Extenders_Get_Instance();
            global  $slplus ;
            return $slplus->AddOns->get( 'slp-extenders', 'instance' );
        }
        
        return $this_addon;
    }
    
    /**
     * Auto-loads classes whenever new ClassName() is called.
     *
     * Loads them from the module/<submodule> directory for the add on.  <submodule> is the part after the class prefix before an _ or .
     * For example SLP_Power_Admin would load the include/module/admin/SLP_Power_Admin.php file.
     *
     * @param $class_name
     */
    function SLP_Extenders_auto_load( $class_name )
    {
        if ( strpos( $class_name, SLP_EXT_CLASS_PREFIX ) !== 0 ) {
            return;
        }
        // Set submodule and file name.
        //
        $prefix = SLP_EXT_CLASS_PREFIX;
        preg_match( "/{$prefix}([a-zA-Z]*)/", $class_name, $matches );
        $file_name = SLP_EXT_REL_DIR . 'include/module/' . (( isset( $matches[1] ) ? strtolower( $matches[1] ) . '/' : '' )) . $class_name . '.php';
        // If the include/module/submodule/class.php file exists, load it.
        //
        if ( is_readable( $file_name ) ) {
            require_once $file_name;
        }
    }
    
    // Register the local SLP_Extenders_auto_load
    spl_autoload_register( 'SLP_Extenders_auto_load' );
    /**
     * Create an SLP_Extenders Debug My Plugin panel.
     *
     * @return null
     */
    // function SLP_Extenders_create_DMPPanels() {
    // if (!isset($GLOBALS['DebugMyPlugin'])) {
    // return;
    // }
    // if (class_exists('DMPPanelSLPEXT') == false) {
    // require_once('include/class.dmppanels.php');
    // }
    // $GLOBALS['DebugMyPlugin']->panels['slp.ext'] = new DMPPanelSLPEXT();
    // }
    /**
     * Simplify the plugin debugMP interface.
     *
     * @param string $type
     * @param string $hdr
     * @param string $msg
     */
    function SLP_Extenders_debugMP(
        $type = 'msg',
        $header = '',
        $message = '',
        $file = null,
        $line = null,
        $notime = true
    )
    {
        $panel = 'slp.ext';
        if ( WP_DEBUG_LOG_SLP_EXT ) {
            switch ( strtolower( $type ) ) {
                case 'pr':
                    error_log( 'HDR: ' . $header . ' PR is no MSG ' . print_r( $message, true ) );
                    break;
                default:
                    error_log( 'HDR: ' . $header . ' MSG: ' . $message );
                    break;
            }
        }
        // Panel not setup yet?  Return and do nothing.
        //
        if ( !isset( $GLOBALS['DebugMyPlugin'] ) || !isset( $GLOBALS['DebugMyPlugin']->panels[$panel] ) ) {
            return;
        }
        // Do normal real-time message output.
        //
        switch ( strtolower( $type ) ) {
            case 'pr':
                $GLOBALS['DebugMyPlugin']->panels[$panel]->addPR(
                    $header,
                    $message,
                    $file,
                    $line,
                    $notime
                );
                break;
            default:
                $GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage(
                    $header,
                    $message,
                    $file,
                    $line,
                    $notime
                );
                break;
        }
    }
    
    // Register the additional admin pages!!!
    add_action( 'admin_init', 'SLP_Extenders_admin_init', 25 );
    add_action( 'admin_menu', 'SLP_Extenders_admin_menu' );
    // ADMIN
    add_action( 'user_admin_menu', 'SLP_Extenders_admin_menu' );
    // ADMIN
    // Addon slug translation
    add_filter(
        'slp_get_addon',
        'filter_ext_slp_get_addon',
        10,
        2
    );
    add_action( 'dmp_addpanel', array( 'SLP_Extenders', 'create_DMPPanels' ) );
    //add_action( 'dmp_addpanel',     'SLP_Extenders_create_DMPPanels'                  );
}
