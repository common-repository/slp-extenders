<?php

require_once SLPLUS_PLUGINDIR . '/include/base_class.userinterface.php';
/**
 * Holds the UI-only code.
 *
 * This allows the main plugin to only include this file in the front end
 * via the wp_enqueue_scripts call.   Reduces the back-end footprint.
 *
 * @property        SLP_Extenders                     $addon
 * @property        SLP_Extenders_UI_Social_Media     $ui_social_media            The UI object for Social Media.
 * @property        SLP_Extenders_UI_User_Managed     $ui_user_managed            The UI object for User Managed.
 * @property        SLP_Extenders_UI_Events           $ui_events                  The UI object for Events.
 */
class SLP_Extenders_UI extends SLP_BaseClass_UI
{
    public  $addon ;
    public  $ui_social_media ;
    public  $ui_user_managed ;
    public  $ui_events ;
    public  $js_requirements = array( 'google_maps' ) ;
    public  $js_settings ;
    private  $LegendWalker ;
    public  $location_property ;
    public  $property_shorthand ;
    public function __construct( $options = array() )
    {
        /** @var SLPlus $slplus_plugin */
        global  $slplus_plugin ;
        $this->addon = $slplus_plugin->addon( 'extenders' );
        parent::__construct( $options );
    }
    
    /**
     * Add UI specific hooks and filters.
     *
     * Overrides the base class which is just a stub placeholder.
     *
     * @uses \SLP_Power_UI::add_category_selectors
     */
    public function add_hooks_and_filters()
    {
        parent::add_hooks_and_filters();
        $this->create_object_ui_objects();
    }
    
    /**
     * Create and attach the admin objects.
     */
    private function create_object_ui_objects()
    {
        $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
        // Create the UserManaged objects if ext_user_managed_enabled.
        //
        if ( $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
            $this->ui_user_managed = new SLP_Extenders_UI_User_Managed( array(
                'addon' => $this->addon,
                'ui'    => $this,
            ) );
        }
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