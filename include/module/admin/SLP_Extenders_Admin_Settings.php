<?php

defined( 'ABSPATH' ) || exit;
/**
 * Admin Settings for Extenders
 *
 * @property        SLP_Extenders                              $addon
 * @property        SLP_Extenders_Admin                        $admin                              The admin object for this addon.
 * @property-read   SLP_Settings                               $settings
 * @property-read   SLP_Extenders_AdminUI_SocialMedia_AddEdit  $socialmedia_addedit
 * @property-read   SLP_Extenders_AdminUI_SocialMedia_Table    $socialmedia_table
 * @property-read   SLP_Extenders_AdminUI_UserManager_Table    $usermanager_table
 * @property-read   SLP_Extenders_AdminUI_EventManager_AddEdit $eventmanager_addedit
 * @property-read   SLP_Extenders_AdminUI_EventManager_Table   $eventmanager_table
 *
 */
class SLP_Extenders_Admin_Settings extends SLPlus_BaseClass_Object
{
    public  $addon ;
    public  $admin ;
    public  $settings ;
    public  $slp_menu_only = false ;
    private  $socialmedia_addedit ;
    private  $socialmedia_table ;
    // private $usermanager_table;
    private  $eventmanager_addedit ;
    private  $eventmanager_table ;
    /**
     * Things we do at the start.
     */
    function initialize()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started 1.' );
        if ( $this->slp_menu_only ) {
            return;
        }
        $this->addon = SLP_Extenders_Get_Instance();
        //$this->admin = $this->addon->admin;
        $this->debugMP( 'msg', __FUNCTION__ . ' started 2.' );
        $this->settings = new SLP_Settings( array(
            'name'        => SLPLUS_NAME . __( ' - Extenders', 'slp-extenders' ),
            'form_action' => '',
            'save_text'   => __( 'Save Extenders Settings', 'slp-extenders' ),
        ) );
    }
    
    /**
     * Add the standard NavBar to the Extenders page.
     */
    function add_NavBarToTab()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->settings->add_section( array(
            'name'        => 'Navigation',
            'div_id'      => 'navbar_wrapper',
            'innerdiv'    => false,
            'is_topmenu'  => true,
            'auto'        => false,
            'description' => SLP_Admin_UI::get_instance()->create_Navbar(),
        ) );
        $panel_name = __( 'Save Setttings', 'slp-extenders' );
    }
    
    /**
     * Add the menu to show different sections.
     */
    function add_group_section( $section_slug = SLP_EXT_SECTION_SLUG_EXT )
    {
        $first_section_slug = $this->slplus->SmartOptions->ext_default_section->value;
        $this->debugMP( 'msg', __FUNCTION__ . ' started with section_slug = ' . $section_slug . ', first_section_slug = ' . $first_section_slug );
        $this->settings->add_section( array(
            'section_slug' => $section_slug,
            'first'        => $section_slug == $first_section_slug,
        ) );
    }
    
    /**
     * Add the Extenders General Settings Section.
     */
    private function add_ExtendersSettingsSection()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $section_name = __( 'General Settings', 'slp-extenders' );
        $section_slug = SLP_EXT_SECTION_SLUG_EXT;
        $group_slug = SLP_EXT_GROUP_SLUG_SETTINGS_EXT;
        $panel_description = __( 'Enable separate functionalities like Social Media, User Managed Locations, Events.', 'slp-extenders' );
        $panel_description .= ' ';
        $panel_description .= __( 'Use the button below to save the settings.', 'slp-extenders' );
        $this->settings->add_section( array(
            'slug'        => $section_slug,
            'group'       => $group_slug,
            'innerdiv'    => true,
            'auto'        => true,
            'first'       => true,
            'description' => $panel_description,
        ) );
        // Create the add edit group
        $panel_name = '';
        $group_slug = SLP_EXT_GROUP_SLUG_SETTINGS_EXT;
        $group_params = array();
        $group_params['section_slug'] = $section_slug;
        $group_params['group_slug'] = $group_slug;
        $group_params['plugin'] = $this->addon;
        $group_params['header'] = $panel_name;
        $this->settings->add_group( $group_params );
        $this->debugMP( 'msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug'] );
        // Create the settings panels
        $this->add_ExtendersSettingsSection_SaveSettings( $section_slug );
        $this->add_ExtendersSettingsSection_ExtendersPanel( $section_slug );
        $this->add_ExtendersSettingsSection_UserManagedPanel( $section_slug );
    }
    
    /**
     * Add the Save Settings button to the Extenders Settings Section
     *
     * @param $section_slug
     */
    function add_ExtendersSettingsSection_SaveSettings( $section_slug )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' section_slug = ' . $section_slug );
        // Set some text
        $panel_name = __( 'Save Setttings', 'slp-extenders' );
        $panel_description = __( 'Use the button below to save the settings.', 'slp-extenders' );
        $group_slug = SLP_EXT_GROUP_SLUG_SETTINGS_SAVE;
        $group_params = array();
        $group_params['section_slug'] = $section_slug;
        $group_params['group_slug'] = $group_slug;
        $group_params['plugin'] = $this->addon;
        $group_params['header'] = $panel_name;
        $group_params['intro'] = __( 'These settings impact the general working of the Extenders add-on.', 'slp-extenders' );
        $this->settings->add_group( $group_params );
        $this->debugMP( 'msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug'] );
        $onClick = "AdminUI.doAction('save','','locationForm' );";
        $save_settings_value = SLP_EXT_ACTION_SAVE;
        $save_settings_text = __( 'Save Extenders Settings', 'slp-extenders' );
        $save_settings_action = 'update';
        $save_settings_id = '';
        $buttonContent = "";
        $buttonContent .= "<div id='slp_form_buttons' style='padding-left:188px;'>";
        $buttonContent .= "<input type='hidden' name='" . SLP_EXT_ACTION_SAVE . "' value='" . SLP_EXT_ACTION_SAVE . "' />";
        $buttonContent .= "<input type='submit' class='button-primary' style='width:150px;margin:3px;' ";
        $buttonContent .= 'value="' . $save_settings_text . '" ';
        $buttonContent .= 'onClick="' . $onClick . '" ';
        $buttonContent .= "' alt='" . $save_settings_text . "' title='" . $save_settings_text . "'";
        $buttonContent .= ">";
        $buttonContent .= "</div>";
        $this->settings->add_ItemToGroup( array(
            'section'    => $section_slug,
            'group'      => $group_slug,
            'setting'    => $this->admin->create_SettingsSetting( 'save_buttons', $save_settings_action, $save_settings_id ),
            'type'       => 'custom',
            'show_label' => false,
            'custom'     => $buttonContent,
            'label'      => __( 'Save Buttons', 'slp-extenders' ),
        ) );
    }
    
    /**
     * Add the Enabling Panel to the Extenders Settings Section
     *
     * @param $section_slug
     */
    function add_ExtendersSettingsSection_ExtendersPanel( $section_slug )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' section_slug = ' . $section_slug );
        $panel_name = __( 'Enabling functionalities', 'slp-extenders' );
        $group_slug = SLP_EXT_GROUP_SLUG_SETTINGS_EXT;
        $group_params = array();
        $group_params['section_slug'] = $section_slug;
        $group_params['group_slug'] = $group_slug;
        $group_params['plugin'] = $this->addon;
        $group_params['header'] = $panel_name;
        $group_params['intro'] = __( 'These settings impact the general working of the Extenders add-on.', 'slp-extenders' );
        $this->settings->add_group( $group_params );
        $this->debugMP( 'msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug'] );
    }
    
    /**
     * Add the User Managed Locations Settings Section to the Setting Panel
     *
     * @param string $section_slug
     */
    function add_ExtendersSettingsSection_UserManagedPanel( $section_slug )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' SmartOptions->ext_user_managed_enabled = ' . $this->slplus->SmartOptions->ext_user_managed_enabled->is_true );
        // Only render UserManagedPanel when user_managed_enabled
        if ( !$this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            return;
        }
        $panel_name = __( 'User Managed Settings', 'slp-extenders' );
        $group_slug = SLP_EXT_GROUP_SLUG_SETTINGS_UML;
        $group_params = array();
        $group_params['section_slug'] = $section_slug;
        $group_params['group_slug'] = $group_slug;
        $group_params['plugin'] = $this->addon;
        $group_params['header'] = $panel_name;
        $group_params['intro'] = __( 'These settings impact the general working of User Managed Locations.', 'slp-extenders' );
        $this->settings->add_group( $group_params );
        $this->debugMP( 'msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug'] );
    }
    
    /**
     * Add the User Managed Section.
     */
    function add_UserManagedSection()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' section_slug = ' . SLP_EXT_SECTION_SLUG_UML );
        // Generate the html for the group_section_menu
        $this->add_group_section( SLP_EXT_SECTION_SLUG_UML );
        // Render the UserManager_Section
        // $this->usermanager_table->render_UserManager_Table_Section();
        $this->render_UserManager_Section();
    }
    
    /**
     * Build the reports tab content.
     */
    function render_extenders_admin_settings()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'pr', __FUNCTION__ . ' started with _GET =', $_GET );
        $this->debugMP( 'pr', __FUNCTION__ . ' started with _POST =', $_POST );
        // $this->debugMP('pr', __FUNCTION__ . ' started with _REQUEST =', $_REQUEST );
        $this->debugMP( 'pr', __FUNCTION__ . ' started with slplus->clean =', $this->slplus->clean );
        // Check default page
        $this->admin->set_selected_nav_element( $this->slplus->SmartOptions->ext_default_section->value );
        $this->save_Settings();
        $this->add_NavBarToTab();
        // Show Notices
        $this->slplus->notifications->display();
        // Add the UserManagedSection if ext_user_managed_enabled.
        $this->debugMP( 'msg', __FUNCTION__ . ' started with ext_user_managed_enabled ' . $this->slplus->SmartOptions->ext_user_managed_enabled->is_true );
        if ( $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            $this->add_UserManagedSection();
        }
        // Add the General Settings section always
        $this->add_ExtendersSettingsSection();
        $this->settings->render_settings_page();
    }
    
    /**
     * Handle the slp_ext_action_request.
     */
    function check_slp_extenders_page( $slp_ext_page_to_check = false )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        if ( $slp_ext_page_to_check === false ) {
            return false;
        }
        if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == $slp_ext_page_to_check ) {
            return true;
        }
        return false;
    }
    
    /**
     * Handle the slp_ext_action_request.
     */
    function handle_slp_ext_action_request( $slp_ext_action_request = '' )
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started with slp_ext_action_request = ' . $slp_ext_action_request );
        switch ( $slp_ext_action_request ) {
            case SLP_EXT_ACTION_SAVE:
                $this->save_extenders_Settings();
                break;
            default:
                break;
        }
    }
    
    /**
     * Save settings when appropriate.
     */
    function save_extenders_Settings()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'pr', __FUNCTION__ . ' ---> this->addon->options for ' . $this->addon->option_name . ' = ', $this->addon->options );
        $this->debugMP( 'pr', __FUNCTION__ . ' ---> this->settings->current_admin_page = ', $this->settings->current_admin_page );
        $this->debugMP( 'pr', __FUNCTION__ . ' ---> this->SmartOptions->smart_properties = ', $this->slplus->SmartOptions->smart_properties );
        $this->debugMP( 'pr', __FUNCTION__ . ' ---> this->SmartOptions->current_checkboxes = ', $this->slplus->SmartOptions->current_checkboxes );
        $this->slplus->SmartOptions->set_checkboxes( 'slp_extenders_settings' );
        array_walk( $_REQUEST, array( $this->slplus->SmartOptions, 'set_valid_options' ) );
        $this->slplus->WPOption_Manager->update_wp_option( $this->addon->option_name, $this->addon->options );
        // Serialized Options Setting for stuff NOT going to slp.js.
        // This should be used for ALL new options not going to slp.js.
        //
        array_walk( $_REQUEST, array( $this->slplus, 'set_ValidOptionsNoJS' ) );
        if ( isset( $_REQUEST['options_nojs'] ) ) {
            array_walk( $_REQUEST['options_nojs'], array( $this->slplus, 'set_ValidOptionsNoJS' ) );
        }
        SLP_SmartOptions::get_instance()->save();
        $this->slplus->SmartOptions->execute_change_callbacks();
        // Anything changed?  Execute their callbacks.
        $this->slplus->WPOption_Manager->update_wp_option( $this->addon->option_name, $this->addon->options );
    }
    
    /**
     * Save settings when appropriate.
     */
    function save_Settings()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        // Check whether there is an action to perform
        
        if ( !empty($_REQUEST[SLP_EXT_ACTION_REQUEST]) ) {
            $this->handle_slp_ext_action_request( $_REQUEST[SLP_EXT_ACTION_REQUEST] );
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[ ' . SLP_EXT_ACTION_REQUEST . ' ] = ' . esc_html( $_REQUEST[SLP_EXT_ACTION_REQUEST] ) );
            return;
        }
        
        // Check whether there is an action to perform
        
        if ( !empty($_REQUEST[SLP_EXT_ACTION_SAVE]) ) {
            $this->handle_slp_ext_action_request( $_REQUEST[SLP_EXT_ACTION_SAVE] );
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[ ' . SLP_EXT_ACTION_SAVE . ' ] = ' . esc_html( $_REQUEST[SLP_EXT_ACTION_SAVE] ) );
            return;
        }
        
        
        if ( empty($_REQUEST['action']) ) {
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[action] = empty.' );
            return;
        }
        
        
        if ( $_REQUEST['action'] !== 'update' ) {
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[action] !== update.' );
            //	    	return;
        }
        
        
        if ( empty($_REQUEST['_wp_http_referer']) ) {
            // error_log( __CLASS__ . '::' . __FUNCTION__ . ' : RETURNED 1 for _SERVER = ' . print_r( $_SERVER, true ) );
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[_wp_http_referer] is empty.' );
            return;
        }
        
        
        if ( substr( $_REQUEST['_wp_http_referer'], -strlen( 'page=' . SLP_EXT_ADMIN_PAGE_SLUG ) ) !== 'page=' . SLP_EXT_ADMIN_PAGE_SLUG ) {
            // error_log( __CLASS__ . '::' . __FUNCTION__ . ' : RETURNED 2 for _SERVER = ' . print_r( $_SERVER, true ) );
            $this->debugMP( 'msg', __FUNCTION__ . ' returned because _REQUEST[_wp_http_referer] !== ' . SLP_EXT_ADMIN_PAGE_SLUG . '.' );
            return;
        }
        
        // error_log( __FILE__ . '::' . __LINE__ . ' : wp_redirect for _REQUEST[_wp_http_referer] = ' . esc_html($_REQUEST['_wp_http_referer']) );
        // handle the actual save_Settings
        $this->save_extenders_Settings();
    }
    
    /**
     * Add the User Managed Section.
     */
    function render_UserManager_Section()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        $this->debugMP( 'msg', __FUNCTION__ . ' started with ext_user_managed_enabled = ' . $this->slplus->SmartOptions->ext_user_managed_enabled->is_true );
        // Only render UserManager_Table when ext_user_managed_enabled
        if ( !$this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            return;
        }
        // Set some text
        $section_slug = SLP_EXT_SECTION_SLUG_UML;
        $group_slug = SLP_EXT_GROUP_SLUG_UML_TABLE;
        $panel_name = __( 'Manage Users', 'slp-extenders' );
        $group_params = array();
        $group_params['section_slug'] = $section_slug;
        $group_params['group_slug'] = $group_slug;
        $group_params['plugin'] = $this->addon;
        $group_params['header'] = $panel_name;
        $group_params['intro'] = sprintf( __( 'The functionality for managing users is moved to the <a href="%s">admin Users page</a>.', 'slp-extenders' ), admin_url( 'users.php' ) );
        $this->settings->add_group( $group_params );
        $this->debugMP( 'msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug'] );
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