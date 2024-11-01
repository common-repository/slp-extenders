<?php
require_once(SLPLUS_PLUGINDIR.'/include/base_class.userinterface.php');

/**
 * Holds the UI-only code for User Managed Locations.
 *
 * @package StoreLocatorPlus\SLP_Extenders\UI\UserManaged
 * @author DeBAAT <slp-extenders@de-baat.nl>
 * @copyright 2022 De B.A.A.T. - Charleston Software Associates, LLC
 *
 * This allows the main plugin to only include this file in the front end
 * via the wp_enqueue_scripts call.   Reduces the back-end footprint.
 *
 * @property        SLP_Extenders                     $addon
 */
class SLP_Extenders_UI_User_Managed extends SLP_BaseClass_UI {

	public  $addon;

	public function __construct( $options = array() ) {

		$this->addon = SLP_Extenders_Get_Instance();

		parent::__construct( $options );

	}

	/**
	 * Add UI specific hooks and filters.
	 *
	 * Overrides the base class which is just a stub placeholder.
	 *
	 * @uses \SLP_Power_UI::add_category_selectors
	 */
	public function add_hooks_and_filters() {

		parent::add_hooks_and_filters();

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
	function debugMP($type,$hdr,$msg='') {
		if (($type === 'msg') && ($msg!=='')) {
			$msg = esc_html($msg);
		}
		if (($hdr!=='')) {   // Adding __CLASS__ to non-empty hdr
			$hdr = __CLASS__ . '::' . $hdr;
		}

		SLP_Extenders_debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}