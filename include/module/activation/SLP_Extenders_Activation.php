<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'SLP_Extenders_Activation' ) ) {
    require_once SLPLUS_PLUGINDIR . '/include/base_class.activation.php';
    /**
     * Manage plugin activation.
     *
     * @property    SLP_Extenders                          $addon
     * @property    SLP_Extenders_Social_Media             $addon_social_media              The addon object for Social Media.
     * @property    SLP_Extenders_User_Managed             $addon_user_managed              The addon object for User Managed.
     * @property    SLP_Extenders_Events                   $addon_events                    The addon object for Events.
     */
    class SLP_Extenders_Activation extends SLP_BaseClass_Activation
    {
        public  $addon ;
        public  $addon_social_media ;
        public  $addon_user_managed ;
        public  $addon_events ;
        // protected $smart_options = array(
        // // General Extenders Options
        // 'ext_social_media_enabled',
        // 'ext_user_managed_enabled',
        // 'ext_events_enabled',
        // 'ext_default_section',
        // // Social Media Options
        // 'ext_social_icon_location',
        // 'ext_sme_label_social_name',
        // 'ext_sme_label_social_slug',
        // 'ext_sme_show_option_all_name',
        // 'ext_sme_show_option_all_slug',
        // 'ext_sme_show_social_name_on_search',
        // 'ext_sme_show_social_slug_on_search',
        // 'ext_sme_default_icons',
        // 'ext_sme_show_icon_array',
        // 'ext_sme_show_legend_block',
        // 'ext_sme_show_legend_text',
        // 'ext_sme_hide_empty',
        // // User Managed Options
        // 'ext_uml_publish_location',
        // 'ext_uml_default_user_allowed',
        // 'ext_uml_show_uml_buttons',
        // // Event Location Options
        // 'ext_event_icon_location',
        // 'ext_elm_label_event_name',
        // 'ext_elm_label_event_slug',
        // 'ext_elm_label_event_category',
        // 'ext_elm_label_event_status',
        // 'ext_elm_show_option_all_name',
        // 'ext_elm_show_option_all_slug',
        // 'ext_elm_show_option_all_category',
        // 'ext_elm_show_option_all_status',
        // 'ext_elm_show_event_name_on_search',
        // 'ext_elm_show_event_slug_on_search',
        // 'ext_elm_show_event_category_on_search',
        // 'ext_elm_show_event_status_on_search',
        // 'ext_elm_default_icons',
        // 'ext_elm_show_icon_array',
        // 'ext_elm_show_legend_block',
        // 'ext_elm_show_legend_text',
        // 'ext_elm_hide_empty',
        // );
        /**
         * Settable options for the old addons
         *
         * @var mixed[] $options
         */
        public  $option_name_social_media = 'slplus-social-media-extender-options' ;
        public  $option_name_user_managed = 'slplus-user-managed-locations-options' ;
        public  $option_name_events = 'slplus-event-location-manager-options' ;
        public  $options_social_media = array() ;
        public  $options_user_managed = array() ;
        public  $options_events = array() ;
        /**
         * Update legacy settings.
         */
        function update()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started with this->addon->version ' . $this->addon->version . '!' );
            $this->updating_from = ( isset( $this->addon->options['installed_version'] ) ? $this->addon->options['installed_version'] : SLP_EXT_NO_INSTALLED_VERSION );
            $this->debugMP( 'msg', __FUNCTION__ . ' started with updating_from ' . $this->updating_from . '!!!' );
            // Migrate extended_data option setting to this addon
            $this->migrate_extended_data_options();
            $this->check_extended_data_options_store_user();
            
            if ( version_compare( $this->updating_from, $this->addon->version, '=' ) ) {
                $this->debugMP( 'msg', __FUNCTION__ . ' returned because updating_from ' . $this->updating_from . ' == addon->version ' . $this->addon->version );
                return;
            }
            
            $this->debugMP( 'msg', __FUNCTION__ . ' TODO migrate options to the right place for updating_from ' . $this->updating_from . '!!!' );
            // Migrate old options on first use
            $this->migrate_options_on_first_use();
            // Migrate extended_data option setting to this addon
            $this->migrate_extended_data_options();
            parent::update();
            // Update the options.
            $this->addon->options['installed_version'] = $this->addon->version;
            update_option( $this->addon->option_name, $this->addon->options );
        }
        
        /*************************************
         * Check extended_data option setting to this addon.
         *
         */
        function check_extended_data_options_store_user()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // If it does not exist yet, then add the Extended Data fields for User Managed Locations
            if ( !$this->slplus->database->extension->has_field( SLP_UML_STORE_USER_SLUG ) ) {
                $this->slplus->database->extension->add_field(
                    __( 'Store User', 'slp-extenders' ),
                    'varchar',
                    array(
                    'slug'  => SLP_UML_STORE_USER_SLUG,
                    'addon' => $this->addon->short_slug,
                ),
                    'immediate'
                );
            }
        }
        
        /*************************************
         * Migrate extended_data option setting to this addon.
         *
         */
        function migrate_extended_data_options()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Check the version
            //
            
            if ( version_compare( $this->addon->version, '5.4.1', '<' ) ) {
                // Rename the addon_slug for all fields from old addons
                $extended_data = $this->slplus->database->extension->get_cols( true );
                //$this->debugMP( 'pr', __FUNCTION__ . ': count= ' . count( $extended_data ) . ', col_data= ', $extended_data );
                foreach ( $extended_data as $key => $cur_field ) {
                    $cur_field_option_values = maybe_unserialize( $cur_field->options );
                    
                    if ( isset( $cur_field_option_values['addon'] ) ) {
                        $cur_field_addon = $cur_field_option_values['addon'];
                        // $this->debugMP( 'pr', __FUNCTION__ . ': cur_field_addon= ' . $cur_field_addon . ', cur_field->slug= ' . $cur_field->slug . ', cur_field= ', $cur_field );
                        // Only update fields from old UML addon.
                        switch ( $cur_field_addon ) {
                            case SLP_EXT_SHORT_SLUG_UML:
                                //$cur_field_option_values['slug']  = $cur_field->slug;
                                $cur_field_option_values['addon'] = $this->addon->short_slug;
                                $this->debugMP( 'pr', __FUNCTION__ . ': update_field for slug= ' . $cur_field->slug . ', cur_field_option_values= ', $cur_field_option_values );
                                $this->slplus->database->extension->update_field( false, false, $cur_field_option_values );
                                break;
                        }
                    } else {
                        // $this->debugMP( 'pr', __FUNCTION__ . ': cur_field_addon= NOT SET!!!, cur_field->slug= ' . $cur_field->slug . ', cur_field= ', $cur_field );
                    }
                
                }
            }
        
        }
        
        /*************************************
         * Migrate old options on first use.
         *
         */
        function migrate_options_on_first_use()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started for updating_from = ' . $this->updating_from . '.' );
            
            if ( $this->updating_from === SLP_EXT_NO_INSTALLED_VERSION ) {
                // if ( true ) {
                $this->debugMP( 'msg', __FUNCTION__ . ' Migrate old options on first use for updating_from = ' . $this->updating_from . '!!!' );
                // Get some general addon objects
                $this->addon_user_managed = $this->addon->addon_user_managed;
                // Migrate the options of the old addons
                $this->migrate_options_for_user_managed();
                $this->debugMP( 'pr', __FUNCTION__ . ' found this->addon->options:', $this->addon->options );
                $this->debugMP( 'pr', __FUNCTION__ . ' found this->slplus->options:', $this->slplus->options );
                $this->debugMP( 'pr', __FUNCTION__ . ' found this->slplus->options_nojs:', $this->slplus->options_nojs );
            }
        
        }
        
        /**
         * Migrate the options of the old SocialMediaExtender addon.
         */
        private function migrate_options_for_user_managed()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $this->options_user_managed = get_option( $this->option_name_user_managed );
            // Return when there are no options to migrate
            if ( $this->options_user_managed === false ) {
                return;
            }
            $this->debugMP( 'pr', __FUNCTION__ . ' found options_user_managed:', $this->options_user_managed );
            // Migrate the options found
            $this->updating_from_user_managed = $this->options_user_managed['installed_version'];
            //$this->updating_from_user_managed = '4.5';
            // Only update if not processed yet
            
            if ( version_compare( $this->updating_from_user_managed, '99.99', '<=' ) ) {
                $this->addon->options['ext_uml_publish_location'] = $this->options_user_managed['uml_publish_location'];
                $this->addon->options['ext_uml_default_user_allowed'] = $this->options_user_managed['uml_default_user_allowed'];
                $this->addon->options['ext_uml_show_uml_buttons'] = $this->options_user_managed['uml_show_uml_buttons'];
            }
            
            // Update the options to indicate they have been processed.
            $this->options_user_managed['installed_version'] = '99.99.91';
            update_option( $this->option_name_user_managed, $this->options_user_managed );
        }
        
        /**
         * Copy non-empty, readable files to destination if they are newer than the destination file.
         * OR if the destination file does not exist.
         *
         * @param $source_file
         * @param $destination_file
         */
        public function copy_newer_files( $source_file, $destination_file )
        {
            if ( empty($source_file) ) {
                return;
            }
            if ( !is_readable( $source_file ) ) {
                return;
            }
            if ( !file_exists( $destination_file ) || file_exists( $destination_file ) && filemtime( $source_file ) > filemtime( $destination_file ) ) {
                copy( $source_file, $destination_file );
            }
        }
        
        /**
         * Recursively copy source directory (or file) into destination directory.
         *
         * @param string $source can be a file or a directory
         * @param string $dest   can be a file or a directory
         */
        private function copyr( $source, $dest )
        {
            if ( !file_exists( $source ) ) {
                return;
            }
            // Make destination directory if necessary
            //
            if ( !is_dir( $dest ) ) {
                wp_mkdir_p( $dest );
            }
            // Loop through the folder
            $dir = dir( $source );
            
            if ( is_a( $dir, 'Directory' ) ) {
                while ( false !== ($entry = $dir->read()) ) {
                    // Skip pointers
                    if ( $entry == '.' || $entry == '..' ) {
                        continue;
                    }
                    $source_file = "{$source}/{$entry}";
                    $dest_file = "{$dest}/{$entry}";
                    // Copy Files
                    //
                    if ( is_file( $source_file ) ) {
                        $this->copy_newer_files( $source_file, $dest_file );
                    }
                    // Copy Symlinks
                    //
                    if ( is_link( $source_file ) ) {
                        symlink( readlink( $source_file ), $dest_file );
                    }
                    // Directories, go deeper
                    //
                    if ( is_dir( $source_file ) ) {
                        $this->copyr( $source_file, $dest_file );
                    }
                }
                // Clean up
                $dir->close();
            }
        
        }
        
        /**
         * Update the data structures on new db versions.
         *
         * @global object $wpdb
         * @param string $sql
         * @param string $table_name
         * @return string
         */
        private function dbupdater( $sql, $table_name )
        {
            global  $wpdb ;
            $retval = ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ? 'new' : 'updated' );
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
            global  $EZSQL_ERROR ;
            $EZSQL_ERROR = array();
            return $retval;
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
}
