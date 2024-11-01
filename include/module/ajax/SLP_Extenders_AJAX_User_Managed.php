<?php
defined( 'ABSPATH' ) || exit;
require_once( SLPLUS_PLUGINDIR . '/include/base_class.ajax.php' );


/**
 * Holds the ajax-only code.
 *
 * This allows the main plugin to only include this file in AJAX mode
 * via the slp_init when DOING_AJAX is true.
 *
 * @property        SLP_Extenders                     $addon
 * @property        SLP_Extenders_AJAX                $ajax_extenders             The AJAX object for this addon.
 * @property        SLP_Extenders_User_Managed        $addon_user_managed         The addon object for User_Managed.
 *
 */
class SLP_Extenders_AJAX_User_Managed extends SLP_BaseClass_AJAX {

	private $ajax_extenders;
	public  $addon_user_managed;

	/**
	 * Set up our environment.
	 *
	 * @uses \SLP_Power_AJAX::modify_formdata via SLP Filter slp_modify_ajax_formdata
	 */
	final function initialize() {
		$this->addon = SLP_Extenders_Get_Instance();
		$this->ajax_extenders = $this->addon->ajax;
		$this->addon_user_managed = $this->addon->addon_user_managed;

		parent::initialize();
	}

	/**
	 * Add our specific AJAX filters.
     *
     * @uses \SLP_Power_AJAX::filter_JSONP_SearchByCategory
	 */
	function add_ajax_hooks() {

		add_filter( 'slp_results_marker_data'                   , array( $this , 'filter_slp_results_marker_data_uml'  ) );

	}

	//-------------------------------------
	// Methods : Custom
	//-------------------------------------

	/**
	 * Add uml_buttons to the marker data.
	 * 
	 * @param   array       $marker     The map marker data.
	 * 
	 * @return  array                   The modified map marker.
	 */
	public function filter_slp_results_marker_data_uml( $marker ) {

		// Only allow when option is set
		if ( $this->slplus->SmartOptions->ext_uml_show_uml_buttons->is_true) {

			// Only allow valid users allowed to manage this location
			if ( isset($marker['id']) ) {
				if ( $this->addon_user_managed->slp_uml_is_user_location( $marker['id'] ) ) {
					$marker['uml_buttons'] = $this->createstring_uml_buttons( $marker['id'] );
				}
			}
		}

		return $marker;
	}

	/**
	 * Build the action buttons HTML string on the first column of the manage locations panel.
	 *
	 * Applies the slp_manage_locations_uml_buttons filter.
	 *
	 * @return string
	 */
	private function createstring_uml_buttons( $location_id = '' ) {
		$buttons_HTML  = '';
		$buttons_URL   = admin_url() . 'admin.php?page=slp_manage_locations';

		// Add edit_button
		$edit_URL   = add_query_arg( array( 'act' => 'edit', 'id' => $location_id ), $buttons_URL);

		$buttons_HTML .=
			sprintf(
				'<a class="dashicons dashicons-welcome-write-blog slp-no-box-shadow uml-buttons" style="width:20px;color:#3b8dbd;" alt="%s" title="%s" data-action="edit" href="%s#" target="_blank" data-id="%s"></a>',
				__( 'edit location', 'slp-extenders' ),
				__( 'edit location', 'slp-extenders' ),
				$edit_URL,
				$location_id
			);

		/**
		 * Filter to Build UML action buttons
		 *
		 * @filter      slp_manage_locations_uml_buttons
		 *
		 * @params      string  current HTML
		 * @params      string  current location_id
		 */

		return apply_filters( 'slp_manage_locations_uml_buttons', $buttons_HTML, (array) $location_id );

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