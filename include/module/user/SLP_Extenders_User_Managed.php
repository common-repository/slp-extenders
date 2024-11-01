<?php
defined( 'ABSPATH' ) || exit;

/**
 * Manage users for admin, UI, CRON, AJAX, REST.
 *
 * @property        SLP_Extenders               $addon
 * @property        SLP_Extenders_Category_Data $category_data          The category table database interface.
 * @property        array                   $location_categories    The current location category ID array.
 * @property        SLPlus                  $slplus
 * @property        WP_Term[]               $wp_categories
 * @property-read   int                     $wp                     -category-count      Count of active social media. Fetch with get_category_count()
 */
class SLP_Extenders_User_Managed extends SLPlus_BaseClass_Object {
	public  $addon;

	/**
	 * Things we do at the start.
	 */
	public function initialize() {
		$this->debugMP('msg', __FUNCTION__ . ' started !!! ' );

		// Only proceed if ext_user_managed_enabled.
		if ( ! $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
			return;
		}

		SLP_Extenders_Text::get_instance();
		//$this->addon = SLP_Extenders_Get_Instance();

		add_filter('wp_print_styles',  array($this, 'slp_uml_wp_print_styles'   ));

		// Some WordPress Admin Actions and Filters for non-admin users
		add_action('show_user_profile'                      ,array($this,'action_show_user_profile'           )           );
		add_action('edit_user_profile'                      ,array($this,'action_show_user_profile'           )           );

	}

	/**
	 * Initialize all our jquery goodness.
	 *
	 */
	public static function slp_uml_wp_print_styles() {

		SLP_Extenders_debugMP('msg',__FUNCTION__.' started for SLPLUS_PLUGINURL_EXT: ' . SLPLUS_PLUGINURL_EXT . '/css/slp-uml.css' );
		wp_enqueue_style( 'slp_uml_style', SLPLUS_PLUGINURL_EXT . '/css/slp-uml.css' );
	}

	/**
	 * Add single quotes to a string.
	 *
	 * @used-by \SLP_Extenders_User_Managed::delete_detached_categories
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function add_single_quotes( $string ) {
		return "'$string'";
	}

	/**
	 * Show the Store User settings of the requested user.
	 *
	 * @param WP_User $profileuser The current WP_User object.
	 */
	public function action_show_user_profile( $profileuser ) {

		if (!@is_object($profileuser)) {
			$profileuser = wp_get_current_user();
		}
		$this->debugMP('msg', __FUNCTION__ . ' started for profileuser->user_login: ' . $profileuser->user_login);

		// Prepare some variables
		$user_allowed = $this->slp_uml_is_user_allowed( $profileuser->user_login );
		if ( $user_allowed ) {
			$user_locations  = SLP_Extenders_Admin_User_Managed::get_instance()->slp_count_filtered_locations( $profileuser->user_login );
			$user_text       = __( 'User is allowed to manage locations.', 'slp-extenders' );
			$user_text      .= ' ';
			$user_text      .= sprintf(_n( 'Currently managing one location.', 'Currently managing %d locations.', $user_locations, 'slp-extenders' ), $user_locations );
			$this->debugMP('msg',__FUNCTION__ . ' continued with user_allowed: ' . $user_allowed);
		} else {
			$user_text       = __( 'User is not allowed to manage any location.', 'slp-extenders' );
		}

		// Generate the output to show
		?>
		<h3><?php _e( 'Store Locator Plus', 'slp-extenders' ); ?></h3>
		<table class="form-table">
			<tr class="show-admin-bar user-admin-bar-front-wrap">
				<th scope="row"><?php _e( 'User Managed Locations', 'slp-extenders' ); ?></th>
				<td>
					<?php if ( $user_allowed ) : ?>
						<span class="dashicons dashicons-yes" color="green"></span>
					<?php else: ?>
						<span class="dashicons dashicons-no" color="red"></span>
					<?php endif; ?>
					<?php echo $user_text; ?>
				</td>
			</tr>
		</table>
		<?php			

	}

	/**
	 * Check whether the current user is a Store Admin.
	 *
	 * @param boolean $noAdmin - whether to validate for non-admins only, default = false
	 * @return boolean
	 */
	public function slp_uml_is_admin($noAdmin = false) {
		$this->debugMP('msg',__FUNCTION__.' started.');

		// User must be logged in
		if (!is_user_logged_in()) { return false; }

		// User can be wordpress admin
		if ($noAdmin && current_user_can('manage_options')) { return true; }

		// Check what current_user_can manage
		if (current_user_can(SLP_UML_CAP_MANAGE_SLP_ADMIN)) { return true; }

		return false;
	}

	/**
	 * Check whether the current user is a User Managed Locations.
	 *
	 * @param boolean $noAdmin - whether to validate for non-admins only, default = false
	 * @return boolean
	 */
	public function slp_uml_is_user($noAdmin = false) {
		$this->debugMP('msg',__FUNCTION__.' started.');

		// User must be logged in
		if (!is_user_logged_in()) { return false; }

		// User may not be wordpress admin if explicitly excluded
		if ($noAdmin && current_user_can('manage_options')) { return false; }

		// Check what current_user_can manage
		if (current_user_can(SLP_UML_CAP_MANAGE_SLP_USER)) { return true; }

		return false;
	}

	/**
	 * Check whether this user is allowed to manage this location.
	 *
	 * @param id       $locationId - the ID of the location to check
	 * @param string   $userLogin  - the login name of the user to check
	 * @return boolean
	 */
	public function slp_uml_is_user_location( $locationId = '', $userLogin = '' ) {
		$this->debugMP('msg',__FUNCTION__.' started with SLP_UML_ STORE_USER _SLUG.');

		// Admin is always allowed
		if ($this->slp_uml_is_admin()) {
			return true;
		}

		// If no user provided, use current user
		if ($userLogin == '') {
			$curUser = wp_get_current_user();
			if ( ! $curUser  ) {
				return false;
			}
			$userLogin = $curUser->user_login;
		}

		// If no location provided, use current location
		if ($locationId == '') {
			$locationId = $this->slplus->currentLocation->id;
		}

		// Get the extended_data for this location
		//
		if ($this->slplus->database->has_extended_data()) {

			$extendedLocationData =
				((int)$locationId > 0)               ?
				$this->slplus->database->extension->get_data($locationId)   :
				null
				;

			// If data found then get SLP_UML_STORE_USER_SLUG
			if (($extendedLocationData !== null) && (isset($extendedLocationData[SLP_UML_STORE_USER_SLUG]))) {

				// Check whether this user is allowed to manage this location
				if ( $extendedLocationData[SLP_UML_STORE_USER_SLUG] == $userLogin ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check whether the user is allowed to manage locations.
	 *
	 * @param string $userLogin - the login name of the user to check
	 * @return boolean
	 */
	public function slp_uml_is_user_allowed($userLogin = '') {
		$this->debugMP('msg',__FUNCTION__.' started.');

		// User must be logged in
		if ($userLogin == '') { return false; }

		// Check requested user has SLP_UML_CAP_MANAGE_SLP_USER
		$curUser = get_user_by( 'login', $userLogin );
		if ( $curUser ) {
			//$this->debugMP('pr',__FUNCTION__ . ': get_user_by(login, ' . $userLogin . ' ) found: ',$curUser);
			return $curUser->has_cap(SLP_UML_CAP_MANAGE_SLP_USER);
		}

		return false;
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
