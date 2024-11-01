<?php

if ( !class_exists( 'SLP_Extenders_Admin_Info' ) ) {
    /**
     * The things that modify the Admin / General Tab.
     *
     * @package StoreLocatorPlus\SLP_Extenders\Admin\Info
     * @author DeBAAT <slp-extenders@de-baat.nl>
     * @copyright 2022 De B.A.A.T. - Charleston Software Associates, LLC
     *
     * Text Domain: slp-extenders
     *
     * @property        SLP_Extenders          $addon
     */
    class SLP_Extenders_Admin_Info extends SLPlus_BaseClass_Object
    {
        public  $addon ;
        /**
         * Things we do at the start.
         */
        public function initialize()
        {
            $this->add_hooks_and_filters();
        }
        
        /**
         * WP and SLP hooks and filters.
         */
        private function add_hooks_and_filters()
        {
            add_filter( 'slp_version_report_' . $this->addon->short_slug, array( $this, 'show_activated_modules' ) );
        }
        
        /**
         * Show activated modules.
         *
         * @param $version
         *
         * @return mixed
         */
        public function show_activated_modules( $version )
        {
            $active_modules = array();
            if ( $this->slplus->SmartOptions->ext_user_managed_enabled->is_true ) {
                $active_modules[] = '<em>User Managed Locations</em>';
            }
            
            if ( !empty($active_modules) ) {
                $active_module = '<br/><span class="label">+</span>' . join( ', ', $active_modules ) . ' ' . __( 'modules enabled.', 'slp-extenders' );
            } else {
                $active_module = '';
            }
            
            return $version . $active_module;
        }
    
    }
}