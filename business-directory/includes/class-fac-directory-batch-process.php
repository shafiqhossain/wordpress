<?php
if ( class_exists( 'WP_Batch' ) ) {

    /**
     * Class YM_Process_Profiles
     */
    class YM_Process_Profiles extends WP_Batch {

        /**
         * Unique identifier of each batch
         * @var string
         */
        public $id = 'ym_process_profiles';

        /**
         * Describe the batch
         * @var string
         */
        public $title = 'YM Member Profile Fetch';

        /**
         * Fetch data class
         * @var object
         */
        private $fdFetchData;

        /**
         * To setup the batch data use the push() method to add WP_Batch_Item instances to the queue.
         *
         * Note: If the operation of obtaining data is expensive, cache it to avoid slowdowns.
         *
         * @return void
         */
        public function setup() {
            /**
             * The class responsible for fetch the member profiles from remote server
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fac-directory-fetch-data.php';

            $client_id = get_option('fac_directory_client_id', '');
            $api_key = get_option('fac_directory_api_key', '');
            $api_password = get_option('fac_directory_api_password', '');
            $this->fdFetchData = new Fac_Directory_Fetch_data($client_id, $api_key, $api_password);

            $profiles_data = isset($_SESSION['ym_profiles']) ? $_SESSION['ym_profiles'] : [];
            $cookies = isset($_SESSION['ym_cookies']) ? $_SESSION['ym_cookies'] : [];

            foreach ( $profiles_data as $id ) {
              $this->push( new WP_Batch_Item( $id, array( 'profile_id' => $id, 'cookies' => $cookies ) ) );
            }

            //unset($_SESSION['ym_profiles']);
            //unset($_SESSION['ym_cookies']);
        }

        /**
         * Handles processing of batch item. One at a time.
         *
         * In order to work it correctly you must return values as follows:
         *
         * - TRUE - If the item was processed successfully.
         * - WP_Error instance - If there was an error. Add message to display it in the admin area.
         *
         * @param WP_Batch_Item $item
         *
         * @return bool|\WP_Error
         */
        public function process( $item ) {
            // Retrieve values
            $profile_id = $item->get_value( 'profile_id' );
            $cookies = $item->get_value( 'cookies' );

            $this->fdFetchData->fetchProfile( $profile_id, $cookies );

            // Return true if the item processing is successful.
            return true;
        }

        /**
         * Called when specific process is finished (all items were processed).
         * This method can be overriden in the process class.
         * @return void
         */
        public function finish() {
            unset($_SESSION['ym_profiles']);
            unset($_SESSION['ym_cookies']);
            unset($this->items);

            $url = '/wp-admin/admin.php?page=fac-directory/profiles';
            if ( wp_redirect( $url ) ) {
                exit;
            }

            // Do something after process is finished.
            // You have $this->items, or other data you can set.
        }

    }
}