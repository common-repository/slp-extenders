<?php

defined( 'ABSPATH' ) || exit;
/**
 * Augment the SLP text tables.
 *
 * @var array    text    array of our text modifications key => SLP text manager slug, value = our replacement text
 */
class SLP_Extenders_Text extends SLPlus_BaseClass_Object
{
    private  $text ;
    /**
     * Things we do at the start.
     */
    public function initialize()
    {
        add_filter(
            'slp_get_text_string',
            array( $this, 'augment_text_string' ),
            10,
            2
        );
        //$this->addon->debugMP('msg',__FUNCTION__.' started.');
    }
    
    /**
     * Replace the SLP Text Manager Strings at startup.
     *
     * @param string $text the original text
     * @param string $slug the slug being requested
     *
     * @return string            the new SLP text manager strings
     */
    public function augment_text_string( $text, $slug )
    {
        $this->init_text();
        if ( !is_array( $slug ) ) {
            $slug = array( 'general', $slug );
        }
        if ( isset( $this->text[$slug[0]] ) && isset( $this->text[$slug[0]][$slug[1]] ) ) {
            return $this->text[$slug[0]][$slug[1]];
        }
        return $text;
    }
    
    /**
     * Initialize our text modification array.
     */
    private function init_text()
    {
        if ( isset( $this->text ) ) {
            return;
        }
        $this->init_text_sections_and_groups();
        $this->init_text_extenders_section();
        $this->init_text_user_managed();
    }
    
    /**
     * Initialize our text modification array for sections and groups.
     */
    private function init_text_sections_and_groups()
    {
        // Sections
        $this->text['settings_section'][SLP_EXT_SECTION_SLUG_EXT] = __( 'Settings Page', 'slp-extenders' );
        $this->text['settings_section'][SLP_EXT_SECTION_SLUG_UML] = __( 'User Managed Page', 'slp-extenders' );
        // Groups
        $this->text['settings_group'][SLP_EXT_GROUP_SLUG_SETTINGS_EXT] = __( 'General Settings', 'slp-extenders' );
        $this->text['settings_group'][SLP_EXT_GROUP_SLUG_SETTINGS_UML] = __( 'User Managed Settings', 'slp-extenders' );
        $this->text['settings_group_header'] = $this->text['settings_group'];
    }
    
    /**
     * Initialize our text modification array.
     */
    private function init_text_extenders_section()
    {
        $this->text['description']['ext_user_managed_enabled'] = __( 'Enables the User Managed Locations functionality.', 'slp-extenders' );
        $this->text['description']['ext_default_section'] = __( 'Define the default settings page.', 'slp-extenders' );
        $this->text['label']['ext_user_managed_enabled'] = __( 'Enable User Managed Locations', 'slp-extenders' );
        $this->text['label']['ext_default_section'] = __( 'Default Settings Page', 'slp-extenders' );
    }
    
    /**
     * Initialize our text modification array.
     */
    private function init_text_user_managed()
    {
        $this->text['description']['ext_uml_publish_location'] = __( 'When enabled a newly entered location is published immediately.', 'slp-extenders' ) . ' ' . __( 'When disabled, the geocode of a newly entered location is removed to block publishing.', 'slp-extenders' ) . ' ' . sprintf( __( 'This needs the re-geocoding functionality of %s to publish blocked locations.', 'slp-extenders' ), 'SLP Power' );
        // sprintf(__('This needs the re-geocoding functionality of %s to publish blocked locations.','slp-extenders'), $this->slplus->add_ons->get_product_url( 'slp-power' ));
        $this->text['description']['ext_uml_default_user_allowed'] = __( 'When enabled a newly added user is allowed to manage locations immediately.', 'slp-extenders' );
        $this->text['description']['ext_uml_show_uml_buttons'] = __( 'When enabled an edit button is added to assist managing a users locations.', 'slp-extenders' );
        $this->text['label']['ext_uml_publish_location'] = __( 'Publish Location Immediately', 'slp-extenders' );
        $this->text['label']['ext_uml_default_user_allowed'] = __( 'Allow new user by default', 'slp-extenders' );
        $this->text['label']['ext_uml_show_uml_buttons'] = __( 'Show edit buttons', 'slp-extenders' );
        $this->text['option_default']['ext_label_user_managed'] = __( 'User Managed', 'slp-extenders' );
    }

}