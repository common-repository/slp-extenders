<?php
if ( ! class_exists( 'SLP_Extenders_Admin_UserManaged' ) ) {

	/**
	 * The things that modify the Admin / User Managed Locations.
	 *
	 * @package StoreLocatorPlus\SLP_Extenders\Admin\UserManaged
	 * @author DeBAAT <slp-extenders@de-baat.nl>
	 * @copyright 2022 De B.A.A.T. - Charleston Software Associates, LLC
	 *
	 * Text Domain: slp-extenders
	 *
	 * @property        SLP_Extenders               $addon
	 * @property        SLP_Extenders_Admin         $admin
	 * @property        SLP_Extenders_User_Managed  $addon_user_managed         The addon object for User Managed.
	 *
	 */
	class SLP_Extenders_Admin_User_Managed  extends SLPlus_BaseClass_Object {

		public  $addon;
		public  $admin;
		public  $addon_user_managed;
		public  $current_user;

        /**
         * Things we do at the start.
         */
        public function initialize() {
			$this->debugMP('msg', __FUNCTION__ . ' started!');
            $this->add_hooks_and_filters();
			//$this->addon_user_managed = $this->addon->addon_user_managed;

			$this->current_user = wp_get_current_user();

        }

        /**
         * WP and SLP hooks and filters.
         */
        private function add_hooks_and_filters() {
			$this->debugMP('msg', __FUNCTION__ . ' started!');
			// $this->debugMP('pr',__FUNCTION__.' started with _REQUEST: ', $_REQUEST );

			// Only proceed if ext_user_managed_enabled.
			if ( ! $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
				return;
			}

			// Manage Locations UI
			//
			add_filter('slp_locations_manage_bulkactions',       array($this,'filter_slp_locations_manage_bulkactions_uml'      )           );
			// add_filter('slp_locations_manage_filters',           array($this,'filter_slp_locations_manage_filters_uml'          )           );

			// Manage Locations Processing
			//
			add_action('slp_manage_locations_action',            array($this,'action_slp_manage_locations_action_uml'           )           );

			// Manage Locations
			//
			add_action('slp_manage_location_where',              array($this,'filter_slp_manage_location_where_uml'             ), 110      );

			// SLP Action Hooks
			//
			// slp_location_added : update extendo data when adding a location
			// slp_location_saved : update extendo data when changing a location
			//
			add_action('slp_location_added',                     array($this,'action_slp_location_saved_uml'                    ), 110      );
			add_action('slp_location_saved',                     array($this,'action_slp_location_saved_uml'                    ), 110      );
			add_action('slp_deletelocation_starting',            array($this,'action_slp_deletelocation_starting_uml'           ), 110      );

			// SLP Filters
			//
			// slp_edit_location_right_column : add extendo fields to location add/edit form
			// slp_manage_location_columns : show extendo fields on the locations list table
			// slp_column_data : manipulate per-location data when it is rendered in the locations list table
			//
			// NOTE: Make sure these are executed AFTER the filters of Super Extendo!!!
			//
			add_filter('slp_manage_location_columns',            array($this,'filter_slp_manage_location_columns_uml'           )           );
//			add_filter('slp_edit_location_right_column',         array($this,'filter_AddExtendedDataToEditFormUML'              ),06        );
			add_filter('slp_column_data',                        array($this,'filter_slp_column_data_uml'                       ),85    ,3  );
			add_filter('slp_edit_location_change_extended_data_info', array($this,'filter_slp_edit_location_change_extended_data_info_uml'  ), 90   );

			// Pro Pack Filters
			//
			// if ($this->slplus->is_AddOnActive('slp-pro')) {
				// add_filter('slp_csv_locationdata'       , array( $this, 'filter_CheckForPreExistingIdentifierUML'  ) );
			// }

			// WordPress Admin Actions and Filters
			add_action('user_register'                          ,array($this,'action_user_register'               )           );

			add_filter('user_row_actions',                       array($this,'filter_user_row_actions'            ), 10,  2   );
			add_filter('bulk_actions-users',                     array($this,'filter_bulk_actions_users'          )           );

			add_filter('manage_users_columns'                   ,array($this,'filter_manage_users_columns'        )           );
			add_action('manage_users_custom_column'             ,array($this,'filter_manage_users_custom_column'  ), 10,  3   );

        }


		//-------------------------------------------------------------
		// METHODS :: THE STUFF THAT MAKES THIS ADD-ON UNIQUE
		//-------------------------------------------------------------

		/**
		 * Delete the extended data.
		 */
		function action_slp_deletelocation_starting_uml() {
			$this->debugMP('msg', __FUNCTION__ . ' started.');
			// This functionality is already handled by SLP Super Extendo hooks
		}

		/**
		 * Save the extended data after adding new or updating existing.
		 */
		function action_slp_location_saved_uml() {
			$this->debugMP('msg', __FUNCTION__ . ' started.');

			// Remove the lat/long if user is store editor and noAdmin and publish rights are revoked
			//
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user(true) ) {
				if ( ! $this->slplus->SmartOptions->ext_uml_publish_location->is_true ) {
					$this->debugMP('msg', __FUNCTION__ . ' slp_uml_is_user() but user has no rights to publish immediately so remove lat/long.');
					$this->slplus->currentLocation->latitude = '';
					$this->slplus->currentLocation->longitude = '';
					$this->slplus->currentLocation->MakePersistent();
				} else {
					$this->debugMP('msg', __FUNCTION__ . ' slp_uml_is_user() and user has the rights to publish immediately.');
				}
			} else {
				$this->debugMP('msg', __FUNCTION__ . ' slp_uml_is_user() returned false so lat/long are not touched.');
			}

			// Check our extended columns and see if there is a matching property in exdata in currentLocation
			$newValues = array();

			// Store the current user login in the store_user field if user is store editor and noAdmin
			//
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user(true) ) {
				$current_user = wp_get_current_user();
				$newValues[ SLP_UML_STORE_USER_SLUG ] = $current_user->user_login;
			} else {
				$this->debugMP('msg', __FUNCTION__ . ' slp_uml_is_user() returned false so store_user is not overwritten.');
			}

			// New values?  Write them to disk...
			if ( count($newValues) > 0 ){
				$this->debugMP('pr',__FUNCTION__ . ' update for newValues= ', $newValues);
				$this->slplus->database->extension->update_data($this->slplus->currentLocation->id, $newValues);
			} else {
				$this->debugMP('msg',__FUNCTION__ . ' No extended data overwritten.');
			}
		}

		/**
		 * Allows editing of the extendo data for the location
		 * 
		 * @param $thedata  - option value field
		 * @param $thefield - The name of the field
		 * @param $thelabel - The column label
		 */
		function filter_slp_column_data_uml( $thedata, $thefield, $thelabel ) {
			// $this->debugMP('pr',__FUNCTION__ . ' started for thefield ' . $thefield . ', thelabel ' . $thelabel . ' and theData:', $thedata);

			// This functionality is already handled by SLP Super Extendo hooks

			return $thedata;
		}

		/**
		 * Remove the UML columns from the manage locations form.
		 *
		 * SLP Filter: slp_edit_location_remove_extended_columns
		 *
		 * @param mixed[] $currentCols column name + column label for existing items
		 * @return mixed[] column name + column labels, extended with our extra fields data
		 */
		function filter_slp_edit_location_change_extended_data_info_uml( $extendedCols ) {
			// $this->debugMP('pr',__FUNCTION__ . ' === extendedCols BEFORE ===>', $extendedCols);

			// Check whether this user is allowed to see the UML fields
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user(true) ) {
				// Remove extended UML data
				$newCols = array();
				foreach ($extendedCols as $curCol) {
					if ( $curCol->slug != SLP_UML_STORE_USER_SLUG ) {
						$newCols[$curCol->slug] = $curCol;
					}
					// $this->debugMP('msg',__FUNCTION__ . ' === REMOVE extendedCols curCol->slug = ' . $curCol->slug);
				}
			} else {
				// Copy extended UML data
				$newCols = array();
				foreach ($extendedCols as $curCol) {
					$newCols[$curCol->slug] = $curCol;
					// $this->debugMP('msg',__FUNCTION__ . ' === COPY extendedCols curCol->slug = ' . $curCol->slug);
				}
			}
			// $this->debugMP('pr',__FUNCTION__ . ' === extendedCols AFTER ===>', $newCols);
			return $newCols;
		}


		/**
		 * Add more actions to the Bulk Action drop down on the admin Locations/Manage Locations interface.
		 *
		 * @param mixed[] $BulkActions
		 */
		function filter_slp_locations_manage_bulkactions_uml( $items ) {
			$this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG.');
			// $this->debugMP('pr',__FUNCTION__.' started with SLP_UML_ STORE_USER _SLUG for items: ', $items);

			// Add bulk actions if user is Store Admin
			//
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) {
				// Store Admin so add bulk actions.
				$return_items = 
					array_merge(
						$items,
						array(
							array(
								'label'     =>  __('Assign Store User','slp-extenders'),
								'value'     => SLP_EXT_ACTION_ASSIGN_STORE_USER,
								'extras'    =>
									'<div id="extra_' . SLP_EXT_ACTION_ASSIGN_STORE_USER . '" class="bulk_extras">'.
										'<label for="sl_' . SLP_EXT_ACTION_ASSIGN_STORE_USER . '">' . __('Define the store user to assign:','slp-extenders') . '</label>' . ' ' .
										'<input name="sl_' . SLP_EXT_ACTION_ASSIGN_STORE_USER . '">'.
									'</div>'
							),
						),
						array(
							array(
								'label'     =>  __('Remove Store User','slp-extenders'),
								'value'     => SLP_EXT_ACTION_REMOVE_STORE_USER,
							),
						)
					);
			} else {
				// No Store Admin so don't add anything.
				$return_items = $items;
			}
			$this->debugMP('pr', __FUNCTION__ . ' returns with SLP_UML_ STORE_USER _SLUG with return_items: ', $return_items);
			return $return_items;
		}

		/**
		 * Add more filters to the Filter drop down on the admin Locations/Manage Locations interface.
		 *
		 * @param mixed[] $items
		*/
		function filter_slp_locations_manage_filters_uml( $items ) {
			$this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG.');

			// Only add this filter if the current_user slp_uml_is_admin
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) { 
				$return_items = 
					array_merge(
						$items,
						array(
							array(
								'label'     =>  __('Filter User','slp-extenders'),
								'value'     => SLP_EXT_ACTION_FILTER_STORE_USER,
								'extras'    =>
									'<div id="extra_' . SLP_EXT_ACTION_FILTER_STORE_USER . '" class="filter_extras">'.
										'<label for="sl_' . SLP_EXT_ACTION_FILTER_STORE_USER . '">' . __('Enter the store user to filter on:','slp-extenders') . '</label>' . ' ' .
										'<input name="sl_' . SLP_EXT_ACTION_FILTER_STORE_USER . '" onkeypress="if (event.keyCode == 13) document.getElementById(\'doaction_filterType\').click();">'.
									'</div>'
							)
						)
					);
			} else {
				// No Store Admin so don't add anything.
				$return_items = $items;
			}
			$this->debugMP('pr', __FUNCTION__ . ' returns with SLP_UML_ STORE_USER _SLUG with return_items: ', $return_items);
			return $return_items;
		}

		/**
		 * Additional location processing on manage locations admin page.
		 *
		 */
		function action_slp_manage_locations_action_uml() {
			$this->debugMP('msg',__FUNCTION__.' started with SLP_UML_ STORE_USER _SLUG. ');
			$this->debugMP('pr',__FUNCTION__.' started with SLP_UML_ STORE_USER _SLUG and _REQUEST: ', $_REQUEST );

			// If user is no Store Admin, don't do anything
			//
			if ( ! SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) {
				return;
			}
			
			// If user is Store Admin, process the actions
			switch ($_REQUEST['act']) {

				// Add store_user setting to locations
				case SLP_EXT_ACTION_ASSIGN_STORE_USER:
					if (isset($_REQUEST[ 'sl_' . SLP_EXT_ACTION_ASSIGN_STORE_USER ])) {
						$sl_store_user = sanitize_text_field($_REQUEST[ 'sl_' . SLP_EXT_ACTION_ASSIGN_STORE_USER ]);
						$this->debugMP('msg', __FUNCTION__ . ' should ASSIGN store_user value: ' . $sl_store_user);
						$LocationIDs = $this->admin->get_ids_from_array( $_REQUEST, 'sl_id' );
						$this->uml_LocationsBulkActionSetStoreUser( $LocationIDs, $sl_store_user );
					}
					break;

				// Remove store_user setting from locations
				case SLP_EXT_ACTION_REMOVE_STORE_USER:
					$this->debugMP('msg', __FUNCTION__ . ' should REMOVE store_user value! ');
					$LocationIDs = $this->admin->get_ids_from_array( $_REQUEST, 'sl_id' );
					$this->uml_LocationsBulkActionSetStoreUser( $LocationIDs, '' );
					break;

				default:
					$this->debugMP('msg',__FUNCTION__.' should process unknown action ' . esc_html($_REQUEST['act']) . ' for store user value! ');
					break;
			}
		}

		/**
		 * Setup the Filter User filter for manage locations.
		 * Filter the locations on the store_user entered
		 *
		 * @param string $where
		 * @return string
		 */
		function filter_slp_manage_location_where_uml( $where ) {

			// $this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG with clause: ', $where);
			$operator = empty($where) ? '' : " AND ";
			$newClause = '';

			// Use different filter for admins and non-admins
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) {
				// Check filter button parameter for admins

				// Check some values to validate the filter action
				if (!isset($_REQUEST['filter']))                                 { return $where; }
				if ($_REQUEST['filter'] != SLP_EXT_ACTION_FILTER_STORE_USER)     { return $where; }
				if (!isset($_REQUEST['sl_' . SLP_EXT_ACTION_FILTER_STORE_USER])) { return $where; }
				if ($_REQUEST['sl_' . SLP_EXT_ACTION_FILTER_STORE_USER] == '')   { return $where; }

				// If request valid, create additional filter where clause
				$newClause = $this->slp_uml_where_filter_store_user( sanitize_text_field($_REQUEST['sl_' . SLP_EXT_ACTION_FILTER_STORE_USER] ));
			} else {
				// Filter on user for non-admins
				$newClause = $this->slp_uml_get_where_current_user();
			}

			// Append new clause and original clause
			$whereClause = $where . $operator . $newClause;

			$this->debugMP('msg',__FUNCTION__ . ' CONTINUED with clause: ', $whereClause);
            return $whereClause;
		}

		/**
		 * Tag a location
		 *
		 * @param string $action = add or remove
		 */
		function uml_LocationsBulkActionSetStoreUser($location_IDs = '', $newStoreUserValue = '') {
			global $wpdb;

			$this->debugMP('pr',__FUNCTION__.' started with SLP_UML_ STORE_USER _SLUG for location_IDs: ', $location_IDs);

			//assigning or removing newStoreUserValue for specified locations
			//
			// Make an array of locationIDs
			$theLocations = (!is_array($location_IDs)) ? array($location_IDs) : $theLocations = $location_IDs;
			
			// Define the new value to use
			$newValues = array();
			$newValues[ SLP_UML_STORE_USER_SLUG ] = $newStoreUserValue;

			// Process locationIDs Array
			//
			foreach ($theLocations as $locationID) {
				$this->slplus->currentLocation->set_PropertiesViaDB($locationID);
				$this->slplus->database->extension->update_data($this->slplus->currentLocation->id, $newValues);
			}
			$this->slplus->notifications->display();

		}

		/**
		 * Get the value to use in searching SQL for User Managed Locations.
		 *
		 * @return string
		 */
		function slp_uml_where_filter_store_user( $theStoreUser = '' ) {

			// Use theStoreUser as filter
			$addedWhereStatement = " (" . SLP_UML_STORE_USER_SLUG . " = '" . $theStoreUser . "') ";
			$this->debugMP('pr',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG where ' . $addedWhereStatement . ' used for theStoreUser=' . $theStoreUser);
			return $addedWhereStatement;
		}

		/**
		 * Removes the User Managed Locations columns for a User Managed Locations
		 * Filter: slp_manage_location_columns : show extendo fields on the locations list table
		 * @param $current_cols array The current columns
		 */
		function filter_slp_manage_location_columns_uml( $current_cols ) {
			// $this->debugMP('pr',__FUNCTION__.' started for current_cols:', $current_cols );

			// Remove User Managed Locations columns if allowed user
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user(true) ) {
				$new_cols = $this->remove_UserManagedLocations_Fields( $current_cols );
				// $this->debugMP('pr', __FUNCTION__ . ' REMOVED SED columns, new_cols= ', $new_cols);
			} else {
				// No changes
				$new_cols = $this->add_UserManagedLocations_Fields( $current_cols );
				// $this->debugMP('pr', __FUNCTION__ . ' ADDED SED columns, new_cols= ', $new_cols);
			}

			return $new_cols;
		}

		/**
		 * Create the data interface object.
		 */
		function add_UserManagedLocations_Fields( $currentCols = array() ) {
			$this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG.');

			return array_merge( $currentCols,
				array(
					SLP_UML_STORE_USER_SLUG => __( 'Store User', 'slp-extenders' ),
				)
			);
		}

		/**
		 * Create the data interface object.
		 */
		function remove_UserManagedLocations_Fields( $sedColumns = array() ) {
			$this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG.');

			// Remove the User Managed Locations fields from the set of columns
			unset($sedColumns[ SLP_UML_STORE_USER_SLUG ]);

			return $sedColumns;
		}

		/**
		 * Applies the default allowed setting to newly registered users.
		 *
		 * @param int $user_id User ID.
		 */
		public function action_user_register( $user_id ) {
			$this->debugMP('msg',__FUNCTION__.' started for user_id: ' . $user_id);

			// Validate access and parameters
			if ( ! SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) { return false; }

			// Get a valid user object from the input
			$curUser = get_user_by( 'id', $user_id );
			if ( $curUser ) {
				$this->debugMP('pr',__FUNCTION__ . ': get_user_by(id,' . $user_id . ') found: ',$curUser);

				// Apply ext_uml_default_user_allowed setting
				if ( $this->slplus->SmartOptions->ext_uml_default_user_allowed->is_true ) {
					$this->slp_uml_user_allow( $user_id );
				} else {
					$this->slp_uml_user_disallow( $user_id );
				}
			}

		}

		/**
		 * Add the value for the new column to the Users list table
		 *
		 * @param string $output Custom column output. Default empty.
		 * @param string $column_name Column name.
		 * @param int $user_id ID of the currently-listed user.
		 */
		function filter_user_row_actions( $actions, $user_object ) {
			$this->debugMP('pr', __FUNCTION__ . ' started for actions :', $actions );

			// Create query_args and urls
			$query_args = array();
			$query_args[ 'wp_http_referer' ]       = urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			$query_args[ SLP_EXT_STORE_USER_SLUG ] = $user_object->ID;
			$query_args[ SLP_EXT_ACTION_REQUEST  ] = SLP_EXT_ACTION_USER_ALLOW;
			$action_link_allow                     = wp_nonce_url( add_query_arg( $query_args, admin_url( 'users.php' ) ) );
			$query_args[ SLP_EXT_ACTION_REQUEST  ] = SLP_EXT_ACTION_USER_DISALLOW;
			$action_link_disallow                  = wp_nonce_url( add_query_arg( $query_args, admin_url( 'users.php' ) ) );

			$action_template = '<a href="%s">%s</a>';

			$actions[ SLP_EXT_ACTION_USER_ALLOW ]    = sprintf( $action_template, $action_link_allow,    __( 'Allow',    'slp-extenders' ));
			$actions[ SLP_EXT_ACTION_USER_DISALLOW ] = sprintf( $action_template, $action_link_disallow, __( 'Disallow', 'slp-extenders' ));

			return $actions;
		}

		/**
		 * Add new bulk_actions to the Users list table
		 *
		 * @param WP_User $columns The current WP_User object.
		 */
		function filter_bulk_actions_users( $actions ) {
			$this->debugMP('msg',__FUNCTION__ . ' started.');

			$actions[SLP_EXT_ACTION_USER_ALLOW]    =  __( 'Allow',    'slp-extenders' );
			$actions[SLP_EXT_ACTION_USER_DISALLOW] =  __( 'Disallow', 'slp-extenders' );
			return $actions;
		}

		/**
		 * Add a new column to the Users list table
		 *
		 * @param WP_User $columns The current WP_User object.
		 */
		function filter_manage_users_columns( $columns ) {
			$this->debugMP('msg',__FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG.');

			// $columns[SLP_UML_STORE_USER_SLUG_COL] =  __( '#Locations', 'slp-extenders' );
			$columns[SLP_EXT_STORE_USER_COL_ALLOWED]   =  __( 'Manage Stores', 'slp-extenders' );
			$columns[SLP_EXT_STORE_USER_COL_LOCATIONS] =  __( 'Locations',     'slp-extenders' );
			return $columns;
		}

		/**
		 * Add the value for the new column to the Users list table
		 *
		 * @param string $output Custom column output. Default empty.
		 * @param string $column_name Column name.
		 * @param int $user_id ID of the currently-listed user.
		 */
		function filter_manage_users_custom_column( $value, $column_name, $user_id ) {
			$this->debugMP('msg', __FUNCTION__ . ' started for column_name :' . $column_name . ' .');

			switch ( $column_name ) {

				case SLP_EXT_STORE_USER_COL_ALLOWED:
					$user_allowed  = __( 'Disallowed','slp-extenders' );
					$curUser = get_user_by( 'id', $user_id );
					if ( $curUser ) {
						 if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user_allowed( $curUser->user_login ) ) {
							$user_allowed = __( 'Allowed','slp-extenders' );
						 }
					}
					return $user_allowed;
					break;

				case SLP_EXT_STORE_USER_COL_LOCATIONS:
					// Get a value from a valid user object from the input
					$user_locations = '<span class="dashicons dashicons-no"></span>';
					$curUser = get_user_by( 'id', $user_id );
					if ( $curUser ) {
						$user_allowed = SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user_allowed( $curUser->user_login );
						if ( $user_allowed ) {
							$user_locations = $this->slp_count_filtered_locations( $curUser->user_login );
						}
					}
					return $user_locations;
					break;
				default:
					return $value;
					break;
			}
			return $value;
		}

		/**
		 * Get the value to use in searching SQL for User Managed Locations.
		 *
		 * @return string
		 */
		function slp_uml_get_where_current_user($userLogin = '') {
			$this->debugMP('msg', __FUNCTION__ . ' started with SLP_UML_ STORE_USER _SLUG for userLogin :' . $userLogin . ' .');

			$addedWhereStatement = " 1=1 ";

			// Find nothing if user not logged in
			if ( ! is_user_logged_in() ) {
				return $addedWhereStatement;
			}

			// Find all if current_user_can manage_slp
			if ( ( current_user_can(SLP_UML_CAP_MANAGE_SLP_ADMIN) ) && (( $userLogin == $this->current_user->user_login ) || ( $userLogin == '' ) ) ) {
			//if ( current_user_can(SLP_UML_CAP_MANAGE_SLP_ADMIN) ) {
				return $addedWhereStatement;
			}

			// Filter if user allowed as store editor
			//if ($this->addon_user_managed->slp_uml_is_user()) {
			if ( SLP_Extenders_User_Managed::get_instance()->slp_uml_is_user() ) {
				if ($userLogin == '') {
					$currentUser = wp_get_current_user();
					$currentUserLogin = $currentUser->user_login;
				} else {
					$currentUserLogin = $userLogin;
				}
				$addedWhereStatement = " (" . SLP_UML_STORE_USER_SLUG . " = '" . $currentUserLogin . "') ";
				$this->debugMP('pr',__FUNCTION__ . ' where ' . $addedWhereStatement . ' used for currentUser=' . $currentUserLogin);
				return $addedWhereStatement;
			}

			return $addedWhereStatement;
		}

		/**
		 * Allow the user for User Managed Locations
		 *
		 * @params string $uml_user_id the login of the user to allow User Managed Locations
		 * @return boolean true when success
		 */
		function slp_uml_user_allow( $uml_user_id ) {
			$this->debugMP('msg', __FUNCTION__ . ' started for user :' . $uml_user_id . ' .');

			// Validate access and parameters
			//if (!$this->addon_user_managed->slp_uml_is_admin()) { return false; }
			if ( ! SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) { return false; }
			if ( $uml_user_id == '' ) { return false; }

			$user = get_user_by( 'ID', $uml_user_id );
			if ( $user ) {
				$user->add_cap( SLP_UML_CAP_MANAGE_SLP );
				$user->add_cap( SLP_UML_CAP_MANAGE_SLP_USER );
			}
			$this->debugMP('pr',__FUNCTION__ . ' user = ',$user);

			return true;
		}

		/**
		 * Disallow the user for User Managed Locations
		 *
		 * @params string $uml_user_id the login of the user to disallow User Managed Locations
		 * @return boolean true when success
		 */
		function slp_uml_user_disallow( $uml_user_id ) {
			$this->debugMP('msg', __FUNCTION__ . ' started for user :' . $uml_user_id . ' .');

			// Validate access and parameters
			//if (!$this->addon_user_managed->slp_uml_is_admin()) { return false; }
			if ( ! SLP_Extenders_User_Managed::get_instance()->slp_uml_is_admin() ) { return false; }
			if ( $uml_user_id == '' ) { return false; }

			$user = get_user_by( 'ID', $uml_user_id );
			if ( $user ) {
				$user->remove_cap( SLP_UML_CAP_MANAGE_SLP );
				$user->remove_cap( SLP_UML_CAP_MANAGE_SLP_USER );
			}
			$this->debugMP('pr',__FUNCTION__ . ' user = ',$user);

			return true;
		}

		/**
		 * Count all locations related to the requested user, count all locations when slp_uml_is_admin 
		 *
		 * @params string $sqlStatement the existing SQL command for Select All
		 * @return integer
		 */
		function slp_count_filtered_locations($umlUserLogin) {

			$debugMsg = '';
			$sqlStatement  = "SELECT * FROM ".$this->slplus->db->prefix."store_locator ";
			$sqlStatement .= $this->slplus->database->extension->filter_ExtendedDataQueries('join_extendo');
			$sqlStatement .= " WHERE " . $this->slp_uml_get_where_current_user( $umlUserLogin );

			$locationsFound = $this->slplus->db->get_results( $sqlStatement );
			$totalLocations = count($locationsFound);
			$this->debugMP('msg',__FUNCTION__.' found ' . $totalLocations . ' locations for user ' . $umlUserLogin . ' .');
//			$this->debugMP('pr',__FUNCTION__.' locationsFound=',$locationsFound);

			return $totalLocations;
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
}