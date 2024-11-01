<?php
if(!class_exists('WP_List_Table')){ require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ); }

/**
 * The data interface helper.
 *
 * @package StoreLocatorPlus\Extenders\AdminUI\UserManager
 * @author DeBAAT <slp-extenders@de-baat.nl>
 * @copyright 2022 De B.A.A.T. - Charleston Software Associates, LLC
 *
 * @property        SLP_Extenders                     $addon
 * @property        SLP_Extenders_Admin               $extenders_admin            The admin object for this addon.
 * @property        SLP_Extenders_Admin_User_Managed  $admin_user_managed         The admin object for User Managed.
 *
 */
class SLP_Extenders_AdminUI_UserManager_Table extends WP_List_Table {

	private $slplus;
	public  $addon;
	public  $admin;
	public  $extenders_admin;
	public  $admin_user_managed;
	public  $time_created = 123;
	public  $settings;

    //-------------------------------------
    // Methods : Base
    //-------------------------------------

    /**
     * Initialize the List Table
     *
     * @param mixed[] $params
     */
    public function __construct($params=null) {
        if (($params != null) && is_array($params)) {
            foreach ($params as $key=>$value) {
                $this->$key = $value;
            }
        }
		parent::__construct( array(
			'singular' => 'user',
			'plural'   => 'users',
			'ajax'     => false
		) );
		$this->debugMP('msg',__FUNCTION__.' started, BEFORE time_created = ' . $this->time_created );
		$this->time_created = $this->addon->create_timestamp_now("H:i:s");
		$this->debugMP('msg',__FUNCTION__.' started, AFTER time_created = ' . $this->time_created );

		$this->initialize();
    }

	/**
	 * Things we do at the start.
	 */
	public function initialize() {
		SLP_Extenders_Text::get_instance();
		$this->debugMP('msg',__FUNCTION__.' started.');
		$this->debugMP('msg',__FUNCTION__.' started, BEFORE time_created = ' . $this->time_created );
		$this->time_created = $this->addon->create_timestamp_now("H:i:s");
		$this->debugMP('msg',__FUNCTION__.' started, AFTER time_created = ' . $this->time_created );

		$this->extenders_admin    = $this->admin;
		//$this->admin_user_managed = $this->extenders_admin->admin_user_managed;
	}

    /**
     * Used to help set column labels and output structure.
     *
     * @return mixed[]
     */
	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'loginname'   => __( 'Login name', 'slp-extenders' ),
			'name'        => __( 'Name',       'slp-extenders' ),
			'storeuser'   => __( 'Store User', 'slp-extenders' ),
			'username'    => __( 'User name',  'slp-extenders' ),
			'email'       => __( 'E-mail',     'slp-extenders' ),
			'role'        => __( 'Role',       'slp-extenders' ),
			'locations'   => __( '#Locations', 'slp-extenders' ),
		);

		return $columns;
	}

    /**
     * Used to help set column meta data about which table columns should be hidden.
     *
     * @return mixed[]
     */
	function get_hidden_columns() {
        $hidden_columns = array(
        );
        return $hidden_columns;
	}

    /**
     * Used to help set column meta data about which table columns can be sorted.
     *
     * @return mixed[]
     */
	function get_sortable_columns() {
        $sortable_columns = array(
            'loginname'    => array('loginname',  true),     //true means it's already sorted
            'username'     => array('username',   false),
            'storeuser'    => array('storeuser',  false),
            'name'         => array('name',       false),
            'email'        => array('email',      false),
            'role'         => array('role',       false),
            'locations'    => array('locations',  false),
        );
        return $sortable_columns;
	}

    /**
     * Output the special checkbox column.
     * @param mixed[] $item
     * @return string
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("user")
            /*$2%s*/ $item['loginname']         //The value of the checkbox should be the record's id
            );
    }

    /**
     * Output the column showing the loginname.
     * @param mixed[] $item
     * @return string
     */
    function column_loginname($item){
        
        //Build row actions
        $actions = array(
            SLP_EXT_ACTION_USER_ALLOW    => sprintf('<a href="?page=%s&action=%s&user=%s">' . __( 'Allow'   ,'slp-extenders' ) . '</a>', esc_url($_REQUEST['page']), SLP_EXT_ACTION_USER_ALLOW,    $item['loginname']),
            SLP_EXT_ACTION_USER_DISALLOW => sprintf('<a href="?page=%s&action=%s&user=%s">' . __( 'Disallow','slp-extenders' ) . '</a>', esc_url($_REQUEST['page']), SLP_EXT_ACTION_USER_DISALLOW, $item['loginname']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['loginname'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    /**
     *
     * @param type $item
     * @param type $column_name
     */
    function column_default($item,$column_name) {
        switch ($column_name) {
            case 'ID':
            case 'username':
            case 'name':
            case 'email':
            case 'storeuser':
            case 'role':
            case 'locations':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            SLP_EXT_ACTION_USER_ALLOW    => __( 'Allow',    'slp-extenders' ),
            SLP_EXT_ACTION_USER_DISALLOW => __( 'Disallow', 'slp-extenders' ),
        );
        return $actions;
    }
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
		if (!isset($_REQUEST['user'])) { return false; }
		$userValues = array();
		if (is_array($_REQUEST['user'])) {
			foreach( $_REQUEST['user'] as $userValue ) {
				$userValues[] = sanitize_text_field($userValue);
			}
		} else {
			$userValues[] = sanitize_text_field($_REQUEST['user']);
		}

        //Detect when and which bulk action is being triggered...
		switch ($this->current_action()) {

			case SLP_EXT_ACTION_USER_ALLOW:
				$this->debugMP('pr',__FUNCTION__.' Users allowed as store editor:',$userValues);
				foreach ($userValues as $curUser) {
					$this->admin_user_managed->slp_uml_user_allow($curUser);
				}
				break;

			case SLP_EXT_ACTION_USER_DISALLOW:
				$this->debugMP('pr',__FUNCTION__.' Users disallowed as store editor:',$userValues);
				foreach ($userValues as $curUser) {
					$this->admin_user_managed->slp_uml_user_disallow($curUser);
				}
				break;

			case '':
				// Do nothing for an empty action
				$this->debugMP('msg',__FUNCTION__ . ' Empty action: ' . $this->current_action());
				break;

			default:
				$this->debugMP('msg',__FUNCTION__ . ' Unknown action: ' . $this->current_action());
				break;

		}

    }

    /**
     * Get the list from the output buffer of parent method.
     *
     * @return string
     */
    function display() {
        ob_start();
        //<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		echo '<form id="users-filter" method="get">';
			//<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			echo '<input type="hidden" name="page" value="' . esc_url($_REQUEST['page']) . '" />';
			//<!-- Now we can render the completed list table -->
			parent::display();
		// Close the form for the User Manager
		echo '</form>';
        return ob_get_clean();
    }

    /**
     * No extendo users found.
     */
	function no_items() {
		_e( 'No users were found.', 'slp-extenders');
	}

    /**
     * Fetch the users from the database.
     */
    function prepare_items() {

		// If there are searches added at a later date
        // this array can have where SQL commands added
        // to filter the return results
        //
		$this->debugMP('msg',__FUNCTION__.' started.');
		$this->debugMP('msg',__FUNCTION__.' started, BEFORE time_created = ' . $this->time_created );
		$this->time_created = $this->addon->create_timestamp_now("H:i:s");
		$this->debugMP('msg',__FUNCTION__.' started, AFTER time_created = ' . $this->time_created );

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        
		// Define the columns to use
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

		// Query the users as a copy from WP_Users_List_Table
		$args = array(
//			'number' => $users_per_page,
//			'offset' => ( $paged-1 ) * $users_per_page,
//			'role' => $role,
//			'search' => $usersearch,
			'fields' => 'all_with_meta'
		);
		$this->debugMP('pr', __FUNCTION__ . ': args= ', $args);
		$wp_user_search = new WP_User_Query( $args );
		$all_users = $wp_user_search->get_results();

		// Create the data to show in the table
		$table_data = array();
		$user_item = array();
		$curUser = new WP_User;
		foreach ($all_users as $key => $userdata) {
			$curUser->init( $userdata );
			$this->debugMP('msg', __FUNCTION__ . ': curUser->user_login = ' . $curUser->user_login);

			$user_item['ID']         = $curUser->ID;
			$user_item['loginname']  = $curUser->user_login;
			$user_item['username']   = $curUser->user_nicename;
			$user_item['name']       = $curUser->data->display_name;
			$user_item['email']      = $curUser->data->user_email;
			$user_item['storeuser']  = $curUser->has_cap(SLP_UML_CAP_MANAGE_SLP_USER) ? __( 'Allowed','slp-extenders' ) : __( 'Disallowed','slp-extenders' );
			$user_item['role']       = $curUser->roles[0];
			//$user_item['locations']  = $this->admin_user_managed->slp_count_filtered_locations($curUser->user_login);
			$user_item['locations']  = SLP_Extenders_Admin_User_Managed::get_instance()->slp_count_filtered_locations($curUser->user_login);

			$table_data[] = $user_item;
		}
		$this->debugMP('pr', __FUNCTION__ . ': tableData= ', $table_data);
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($table_data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $table_data = array_slice($table_data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $table_data;

		$this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
    }

    //-------------------------------------
    // Methods : Added
    //-------------------------------------

	/**
	 * Add the User Managed Section.
	 */
	function render_UserManager_Table_Section() {
		$this->debugMP('msg',__FUNCTION__ . ' started.');
		$this->debugMP('msg', __FUNCTION__ . ' started with ext_user_managed_enabled ' . $this->slplus->SmartOptions->ext_user_managed_enabled->is_true );

		// Only render UserManager_Table when ext_user_managed_enabled
		if ( ! $this->slplus->SmartOptions->ext_user_managed_enabled->is_true) {
			return;
		}

		// Create the manage table group
		$section_slug = SLP_EXT_SECTION_SLUG_UML;
		$group_slug   = SLP_EXT_GROUP_SLUG_UML_TABLE;
		$panel_name   = __('Manage Users', 'slp-extenders');

		$group_params                   = array();
		$group_params['section_slug']   = $section_slug;
		$group_params['group_slug']     = $group_slug;
		$group_params['plugin']         = $this->addon;
		$group_params['header']         = $panel_name;

		$this->settings->add_group( $group_params );
		$this->debugMP('msg', __FUNCTION__ . ' SETTINGS add_group: ' . $group_params['group_slug']);

		$this->prepare_items();

		$this->settings->add_ItemToGroup(
			array(
				'section'       => $section_slug,
				'group'         => $group_slug,
				'label'         => false,
				'type'          => 'subheader',
				'show_label'    => false,
				'description'   => $this->display()
				)
			);

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
