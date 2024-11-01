<?php

defined( 'ABSPATH' ) || exit;
require_once SLPLUS_PLUGINDIR . '/include/base/SLP_AddOn_Options.php';
/**
 * Class SLP_Extenders_Options
 */
class SLP_Extenders_Options extends SLP_AddOn_Options
{
    /**
     * Create our options.
     */
    protected function create_options()
    {
        global  $slplus ;
        //$this->addon  = SLP_Extenders_Get_Instance();
        $this->slplus = $slplus;
        // $this->debugMP('pr', __FUNCTION__ . ' started with _REQUEST: ', $_REQUEST );
        SLP_Extenders_Text::get_instance();
        // Get the page to create the options for
        $create_options_page = ( isset( $_REQUEST['page'] ) ? esc_url( $_REQUEST['page'] ) : SLP_EXT_SECTION_SLUG_EXT );
        // if ( $create_options_page !== SLP_EXT_SECTION_SLUG ) {
        // $create_options_page = SLP_EXT_SECTION_SLUG_EXT;
        // }
        // General Extenders options
        $this->augment_extenders_page_options( $create_options_page );
        $this->augment_extenders_general_options( $create_options_page );
        // User Managed options
        
        if ( $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            $this->augment_user_managed_options( $create_options_page );
            $this->augment_settings_results_appearance_user_managed();
            $this->augment_settings_search_appearance_user_managed();
            $this->augment_settings_view_appearance_user_managed();
            $this->augment_settings_map_markers_user_managed();
        }
        
        // General Extenders options
        //$this->extenders_settings( $create_options_page );
    }
    
    /**
     * General Extenders Options
     */
    private function augment_extenders_page_options( $attach_to_slp_page = SLP_EXT_SECTION_SLUG_EXT )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started with attach_to_slp_page: ' . $attach_to_slp_page );
        $page_options = array();
        $page_options[] = array(
            'label' => 'Settings Page',
            'value' => SLP_EXT_SELECTED_NAV_ELEMENT_EXT,
        );
        $page_options[] = array(
            'label' => 'User Managed Page',
            'value' => SLP_EXT_SELECTED_NAV_ELEMENT_UML,
        );
        $new_options['ext_default_section'] = array(
            'default' => SLP_EXT_SELECTED_NAV_ELEMENT_EXT,
            'type'    => 'dropdown',
            'options' => $page_options,
        );
        $this->attach_to_slp( $new_options, array(
            'page'    => SLP_EXT_ADMIN_PAGE_SLUG,
            'section' => SLP_EXT_SECTION_SLUG_EXT,
            'group'   => SLP_EXT_GROUP_SLUG_SETTINGS_EXT,
        ) );
    }
    
    /**
     * General Extenders Options
     */
    private function augment_extenders_general_options( $attach_to_slp_page = SLP_EXT_SECTION_SLUG_EXT )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started with attach_to_slp_page: ' . $attach_to_slp_page );
        $new_options['ext_user_managed_enabled'] = array(
            'type'    => 'checkbox',
            'default' => '1',
        );
        //		$new_options['show_cats_on_search']             = array( 'related_to' => $related_to , 'type' => 'dropdown' , 'default' => 'none' , 'get_items_callback' => array( $this , 'get_show_cats_on_search_items' ) );
        $this->attach_to_slp( $new_options, array(
            'page'              => SLP_EXT_ADMIN_PAGE_SLUG,
            'section'           => SLP_EXT_SECTION_SLUG_EXT,
            'group'             => SLP_EXT_GROUP_SLUG_SETTINGS_EXT,
            'use_in_javascript' => false,
        ) );
    }
    
    /**
     *  User Managed Options
     */
    private function augment_user_managed_options( $attach_to_slp_page = SLP_EXT_SECTION_SLUG_EXT )
    {
        $new_options['ext_uml_publish_location'] = array(
            'type'    => 'checkbox',
            'default' => '1',
        );
        $new_options['ext_uml_default_user_allowed'] = array(
            'type'    => 'checkbox',
            'default' => '1',
        );
        $new_options['ext_uml_show_uml_buttons'] = array(
            'type'    => 'checkbox',
            'default' => '0',
        );
        $this->attach_to_slp( $new_options, array(
            'page'              => SLP_EXT_ADMIN_PAGE_SLUG,
            'section'           => SLP_EXT_SECTION_SLUG_EXT,
            'group'             => SLP_EXT_GROUP_SLUG_SETTINGS_UML,
            'use_in_javascript' => false,
        ) );
    }
    
    /**
     * Settings / Search / Appearance / User Managed
     *
     */
    private function augment_settings_search_appearance_user_managed()
    {
    }
    
    /**
     * Settings / Map / Markers / User Managed
     *
     */
    private function augment_settings_map_markers_user_managed()
    {
    }
    
    /**
     * Settings / Results / Appearance / User Managed
     *
     */
    private function augment_settings_results_appearance_user_managed()
    {
    }
    
    /**
     * Settings / View / Appearance / User Managed
     *
     */
    private function augment_settings_view_appearance_user_managed()
    {
    }
    
    /**
     * Extenders > Settings
     */
    private function extenders_settings( $attach_to_slp_page = SLP_EXT_SECTION_SLUG_EXT )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started with attach_to_slp_page: ' . $attach_to_slp_page );
        $new_options['ext_extenders_enabled'] = array(
            'type' => 'checkbox',
        );
        $this->attach_to_slp( $new_options, array(
            'page'              => SLP_EXT_ADMIN_PAGE_SLUG,
            'section'           => SLP_EXT_SECTION_SLUG_EXT,
            'group'             => SLP_EXT_GROUP_SLUG_SETTINGS_EXT,
            'use_in_javascript' => false,
        ) );
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