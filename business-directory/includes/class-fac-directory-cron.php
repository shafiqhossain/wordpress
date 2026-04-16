<?php

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Fac_Directory_Cron {
	/**
	 * Class constructor
	 */
	public function __construct() {

	}

	/**
	* Fetch groups and profiles data from the YM site
	*
	* @param string $since
	*
	* @return mixed
	*/
	public static function fetchProfiles( $since ) {
	  global $wpdb;

      $options = array(
        'api_key' => get_option('fac_directory_api_key', ''),
        'api_passcode' => get_option('fac_directory_api_passcode', ''),
        'api_version' => get_option('fac_directory_api_version', ''),
      );

      // if any of the API parameter is empty, return
      if (empty($options['api_key']) || empty($options['api_passcode']) || empty($options['api_version'])) {
        // log message
        return false;
      }

      /*
      $ids = self::getMemberIds($since, $options);
      foreach($ids as $id) {
         self::fetchAndSaveMember($id, $options);
      }
      */

	  return true;
	}

    public static function getMemberIds($since, $options) {
        // Get the XML for API call
        $xml = self::getMemberIdsXML($since, $options);

        //$data = $this->generatexml->generateXML($this->query->ApiCall($xml));

        //return $this->extract($data, $UpdatedIDs);

	  return [];
    }

    public static function fetchAndSaveMember($id, $options) {

    }

    /**
     * @param $xml
     * @return \SimpleXMLElement
     */
    public function generateXML($xml) {
        $data = new \SimpleXMLElement($xml);
        return $data;
    }

    /**
     * Call YM API
     *
     * @return array
     */
    public static function apiCall($xml) {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        try {
            $response = wp_remote_post('https://api.yourmembership.com/', array(
                'headers'=> $headers,
                'body' => $xml
            ));
        }
        catch (\Exception $ex) {
            $response = wp_remote_post('https://api.yourmembership.com/', array(
                'headers'=> $headers,
                'body' => $xml
            ));
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        if ($http_code == 200) {
            return json_decode(wp_remote_retrieve_body($response));
        }
        else {
            return array();
        }
    }

    /**
     * Get the XML to getch all the member ids
     *
     * @param string $time
     * @param array $options
     * @return string
     */
    public static function getMemberIdsXML($time = '', $options = array()) {
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
           <YourMembership>
               <Version>'.$options['api_version'].'</Version>
               <ApiKey>'.$options['api_key'].'</ApiKey>
               <CallID>004</CallID>
               <SaPasscode>'.$options['api_passcode'].'</SaPasscode>
              <Call Method="Sa.People.All.GetIDs">';
        if (!empty($time)) {
            $xml .= "<Timestamp>{$time}</Timestamp>";
        }

        $xml .= '   </Call>
           </YourMembership>';
        return $xml;
    }

}





