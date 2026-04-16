<?php

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Fac_Directory_Fetch_data {
    private $client_id;
    private $api_key;
    private $api_password;
    private $api_endpoint = 'https://ws.yourmembership.com';

    private $allowedMemberTypes = [
        'County Staff',
        'Commissioner',
        'Constitutionals'
    ];

	/**
	 * Class constructor
	 */
	public function __construct($client_id, $api_key, $api_password) {
      $this->client_id = $client_id;
      $this->api_key = $api_key;
      $this->api_password = $api_password;
	}

    public function fetchMemberData($since = '', $is_batch = 0, $only_missing_profiles = 0) {
        $status = array(
            'status' => 0,
            'message' => '',
        );

        // Authenticate
        $status = $this->authenticate();
        if ($status['status'] == 0) {
          fac_directory_write_log('YM membership cron: Authentication failed.');
          return $status;
        }
        $cookies = isset($status['cookies']) ? $status['cookies'] : false;

        // Fetch MemberIDs
        $this->memberIDs( $since, $cookies, $is_batch, $only_missing_profiles);

        return $status;
    }

    /**
     * Authenticate for API access
     *
     * @return array
     */
	public function authenticate( ) {
	    $status = array(
	      'status' => 0,
          'message' => '',
        );

        $response = wp_remote_post( $this->api_endpoint . '/Ams/Authenticate',
            array(
                'body' => array(
                  'usertype' => 'Admin',
                  'ClientId' => $this->client_id,
                  'username' => $this->api_key,
                  'password' => $this->api_password
                ),
                'timeout'     => 60,
                'redirection' => 5,
                'blocking'    => true,
                'httpversion' => '1.0',
                'sslverify'   => false,
                'data_format' => 'body',
            )
        );

        if ( is_wp_error( $response ) ) {
          $error_message = $response->get_error_message();
          $status = array(
            'status' => 0,
            'message' => $error_message,
          );
          return $status;
        }

        try {
          $data_response = $response['response'];
          $data_cookies = $response['cookies'];
          $cookies = array();
          foreach ($data_cookies as $obj) {
            $this->setCookies($obj);
            $cookies[] = new WP_Http_Cookie( array(
                'name' => $obj->name,
                'value' => $obj->value,
                'expires' => $obj->expires,
                'path' => $obj->path,
                'domain' => $obj->domain,
                'port' => $obj->port,
                'host_only' => $obj->host_only
            ));
          }

          return array(
            'status' => 1,
            'message' => 'Successfully authenticated.',
            'cookies' => $cookies,
          );
        }
        catch ( Exception $ex ) {
          $status = array(
            'status' => 0,
            'message' => 'Failed to decode the response.',
          );

          return $status;
        }
	}

    /**
     * Fetch and Extract Member Profile IDs
     *
     * @param string $since
     * @return false|void
     */
    public function memberIDs( $since = '', $ck = array(), $is_batch = 0, $only_missing_profiles = 0) {
        $url = $this->api_endpoint . '/Ams/'.$this->client_id.'/PeopleIDs';

        if (empty($since)) {
          $timestamp = get_option('fac_directory_profiles_since', '1970-01-01T00:00:00');
        }
        else {
          $timestamp = $since;
        }

        if (empty($ck)) {
          $cookies = $this->getCookies();
        }
        else {
          $cookies = $ck;
        }

        $PageSize = 100;  // number of profile in each page
        $PageNumber = get_option('fac_directory_current_page_number', 1);
        $max_profiles = get_option('fac_directory_profile_fetch_batch', 1000);
        $max_profiles = reset($max_profiles);

        $profiles_data = [];
        while(true) {
            $response = wp_remote_get($url,
                array(
                    'headers'   => [
                        'content-type' => 'application/json',
                    ],
                    'body' => array(
                        'Timestamp' => $timestamp,
                        'UserType' => 'Member',
                        'PageSize' => $PageSize,
                        'PageNumber' => $PageNumber
                    ),
                    'cookies' => $cookies
                )
            );

            try {
              if ( !is_wp_error( $response ) ) {
                  $jsonData = json_decode( $response['body'], true);
                  $IDList = isset($jsonData['IDList']) ? $jsonData['IDList'] : array();
                  $fetch_count = count($IDList);
                  ++$PageNumber;
                  update_option( 'fac_directory_current_page_number', $PageNumber );

                  // filter list, if only check the missing profiles
                  if ($only_missing_profiles) {
                    $IDList = $this->getMissingProfiles( $IDList );
                  }

                  if (!empty($IDList)) {
                    $profiles_data = array_merge($profiles_data, $IDList);
                  }

                  // If the page profile counts is less that PageSize, that means, no more profiles to fetch.
                  if (empty($fetch_count) || ($fetch_count < $PageSize)) {
                      // Reset to beginning
                      update_option( 'fac_directory_current_page_number', 1 );

                      $timestamp = date('Y-m-d\TH:i:s', time());
                      update_option('fac_directory_profiles_since', $timestamp);

                      fac_directory_write_log('YM membership cron: Profile fetch complete, resetting.');
                      break;
                  }
              }
              else {
                  fac_directory_write_log('YM membership cron failed. Due to Response Error.');
                  break;
              }
            }
            catch ( Exception $ex ) {
                fac_directory_write_log('YM membership cron failed. Error: ' . $ex->getMessage());
                break;
            }
        }

        // Log the list, only for debug
        // fac_directory_write_log(print_r($profiles_data,true));

        // For debug only
        //$profiles_data = [];
        //$profiles_data[] = 36916397;

        if ($is_batch) {
          // Session data for batch process
          $_SESSION['ym_profiles'] = $profiles_data;
          $_SESSION['ym_cookies'] = $cookies;
          // update previous batch process
          delete_option( 'batch_ym_process_profiles_processed');

          $url = '/wp-admin/admin.php?page=dg-batches&action=view&id=ym_process_profiles';
          if ( wp_redirect( $url ) ) {
            exit;
          }
        }
        else {
          $this->fetchProfiles( $profiles_data, $cookies );
          $url = '/wp-admin/admin.php?page=fac-directory/profiles';
          if ( wp_redirect( $url ) ) {
            exit;
          }
        }
    }

    /**
     * Fetch profiles
     *
     * @param $IDList
     * @param $cookies
     * @return void
     */
    public function fetchProfiles( $IDList, $cookies ) {
        if (is_array($IDList) && !empty($IDList)) {
            foreach ($IDList as $profileID) {
                $memberData = $this->member($profileID, $cookies);
                $memberGroupData = $this->memberGroups($profileID, $cookies);

                // Filter
                if (!empty($memberData) && !empty($memberGroupData)) {
                    // Save Profile Info
                    $this->saveMemberData($memberData);

                    // Save Group Info
                    foreach ($memberGroupData as $memberGroup) {
                        $this->saveMemberGroupData($memberGroup);
                        $this->saveGroupData($memberGroup);
                    }
                }
            }
        }
    }

    /**
     * Fetch profile
     *
     * @param $profileID
     * @param $cookies
     * @return void
     */
    public function fetchProfile( $profileID, $cookies ) {
      $memberData = $this->member($profileID, $cookies);
      $memberGroupData = $this->memberGroups($profileID, $cookies);

      // Filter
      if (!empty($memberData) && !empty($memberGroupData)) {
        // Save Profile Info
        $this->saveMemberData($memberData);

        // Save Group Info
        foreach ($memberGroupData as $memberGroup) {
          $this->saveMemberGroupData($memberGroup);
          $this->saveGroupData($memberGroup);
        }
      }
    }

    /**
     * Fetch and Extract Member Profile Info
     *
     * @param $profileID
     * @return bool|array
     */
    public function member( $profileID, $ck = array() ) {
        $url = $this->api_endpoint . '/Ams/'.$this->client_id.'/People';

        if (empty($ck)) {
          $cookies = $this->getCookies();
        }
        else {
          $cookies = $ck;
        }

        $response = wp_remote_get($url,
            array(
                'headers'   => [
                  'content-type' => 'application/json',
                ],
                'body' => array(
                  'ProfileID' => $profileID,
                ),
                'cookies'  => $cookies
            )
        );

        $row = [];

        try {
            if (!is_wp_error( $response ) ) {
                $jsonData = json_decode( $response['body'], true);
                if ($profileID == 36916397) {
                  fac_directory_write_log(print_r($jsonData,true));
                }
                if (empty($jsonData['ResponseStatus']['Errors']) && !empty($jsonData['ProfileID'])) {
                    $row['ProfileID'] = isset($jsonData['ProfileID']) ? $jsonData['ProfileID'] : '';
                    $row['IsMember'] = isset($jsonData['IsMember']) && $jsonData['IsMember'] ? 1 : 0;
                    $row['ImportID'] = isset($jsonData['MemberAccountInfo']['ImportID']) ? $jsonData['MemberAccountInfo']['ImportID'] : '';
                    $row['Approved'] = isset($jsonData['MemberAccountInfo']['Approved']) && $jsonData['MemberAccountInfo']['Approved'] ? 1 : 0;
                    $row['Suspended'] = isset($jsonData['MemberAccountInfo']['Suspended']) && $jsonData['MemberAccountInfo']['Suspended'] ? 1 : 0;
                    $row['Featured'] = isset($jsonData['MemberAccountInfo']['Featured']) && $jsonData['MemberAccountInfo']['Featured'] ? 1 : 0;
                    $row['ApprovalDate'] = isset($jsonData['MemberAccountInfo']['ApprovalDate']) ? $jsonData['MemberAccountInfo']['ApprovalDate'] : '';
                    $row['LastModifiedDate'] = isset($jsonData['MemberAccountInfo']['LastModifiedDate']) ? $jsonData['MemberAccountInfo']['LastModifiedDate'] : '';
                    $row['MembershipExpires'] = isset($jsonData['MemberAccountInfo']['MembershipExpires']) ? $jsonData['MemberAccountInfo']['MembershipExpires'] : '';
                    $row['MembershipExpiresDate'] = isset($jsonData['MemberAccountInfo']['MembershipExpiresDate']) ? $jsonData['MemberAccountInfo']['MembershipExpiresDate'] : '';
                    $row['ConstituentID'] = isset($jsonData['MemberAccountInfo']['ConstituentID']) ? $jsonData['MemberAccountInfo']['ConstituentID'] : '';
                    $row['MemberTypeCode'] = isset($jsonData['MemberAccountInfo']['MemberTypeCode']) ? $jsonData['MemberAccountInfo']['MemberTypeCode'] : '';
                    $row['Registered'] = isset($jsonData['MemberAccountInfo']['Registered']) ? $jsonData['MemberAccountInfo']['Registered'] : '';
                    $row['MemberProfilePublic'] = isset($jsonData['MemberAccountInfo']['MemberProfilePublic']) && $jsonData['MemberAccountInfo']['MemberProfilePublic'] ? 1 : 0;
                    $row['LastUpdated'] = isset($jsonData['MemberAccountInfo']['LastUpdated']) ? $jsonData['MemberAccountInfo']['LastUpdated'] : '';
                    $row['Gender'] = isset($jsonData['MemberPersonalInfo']['Gender']) ? $jsonData['MemberPersonalInfo']['Gender'] : '';
                    $row['Prefix'] = isset($jsonData['MemberPersonalInfo']['Prefix']) ? $jsonData['MemberPersonalInfo']['Prefix'] : '';
                    $row['FirstName'] = isset($jsonData['MemberPersonalInfo']['FirstName']) ? $jsonData['MemberPersonalInfo']['FirstName'] : '';
                    $row['MiddleName'] = isset($jsonData['MemberPersonalInfo']['MiddleName']) ? $jsonData['MemberPersonalInfo']['MiddleName'] : '';
                    $row['LastName'] = isset($jsonData['MemberPersonalInfo']['LastName']) ? $jsonData['MemberPersonalInfo']['LastName'] : '';
                    $row['Suffix'] = isset($jsonData['MemberPersonalInfo']['Suffix']) ? $jsonData['MemberPersonalInfo']['Suffix'] : '';
                    $row['NickName'] = isset($jsonData['MemberPersonalInfo']['NickName']) ? $jsonData['MemberPersonalInfo']['NickName'] : '';
                    $row['MaidenName'] = isset($jsonData['MemberPersonalInfo']['MaidenName']) ? $jsonData['MemberPersonalInfo']['MaidenName'] : '';
                    $row['HeadshotImageURI'] = isset($jsonData['MemberPersonalInfo']['HeadshotImageURI']) ? $jsonData['MemberPersonalInfo']['HeadshotImageURI'] : '';
                    $row['PersonalEmailAddress'] = isset($jsonData['MemberPersonalInfo']['Email']) ? $jsonData['MemberPersonalInfo']['Email'] : '';

                    $row['EmployerName'] = isset($jsonData['MemberProfessionalInfo']['EmployerName']) ? $jsonData['MemberProfessionalInfo']['EmployerName'] : '';
                    $row['SelfEmployed'] = isset($jsonData['MemberProfessionalInfo']['SelfEmployed']) && $jsonData['MemberProfessionalInfo']['SelfEmployed'] ? 1 : 0;
                    $row['WorkTitle'] = isset($jsonData['MemberProfessionalInfo']['WorkTitle']) ? $jsonData['MemberProfessionalInfo']['WorkTitle'] : '';
                    $row['WorkType'] = isset($jsonData['MemberProfessionalInfo']['WorkType']) ? $jsonData['MemberProfessionalInfo']['WorkType'] : '';
                    $row['WorkUrl'] = isset($jsonData['MemberProfessionalInfo']['WorkUrl']) ? $jsonData['MemberProfessionalInfo']['WorkUrl'] : '';
                    $row['WorkAddressLine1'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressLine1']) ? $jsonData['MemberProfessionalInfo']['WorkAddressLine1'] : '';
                    $row['WorkAddressLine2'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressLine2']) ? $jsonData['MemberProfessionalInfo']['WorkAddressLine2'] : '';
                    $row['WorkAddressCity'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressCity']) ? $jsonData['MemberProfessionalInfo']['WorkAddressCity'] : '';
                    $row['WorkAddressLocation'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressLocation']) ? $jsonData['MemberProfessionalInfo']['WorkAddressLocation'] : '';
                    $row['WorkAddressPostalCode'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressPostalCode']) ? $jsonData['MemberProfessionalInfo']['WorkAddressPostalCode'] : '';
                    $row['WorkAddressCountry'] = isset($jsonData['MemberProfessionalInfo']['WorkAddressCountry']) ? $jsonData['MemberProfessionalInfo']['WorkAddressCountry'] : '';
                    $row['WorkPhoneNumber'] = isset($jsonData['MemberProfessionalInfo']['WorkPhoneNumber']) ? $jsonData['MemberProfessionalInfo']['WorkPhoneNumber'] : '';
                    $row['WorkPhoneAreaCode'] = isset($jsonData['MemberProfessionalInfo']['WorkPhoneAreaCode']) ? $jsonData['MemberProfessionalInfo']['WorkPhoneAreaCode'] : '';
                    $row['WorkFaxNumber'] = isset($jsonData['MemberProfessionalInfo']['WorkFaxNumber']) ? $jsonData['MemberProfessionalInfo']['WorkFaxNumber'] : '';
                    $row['WorkFaxAreaCode'] = isset($jsonData['MemberProfessionalInfo']['WorkFaxAreaCode']) ? $jsonData['MemberProfessionalInfo']['WorkFaxAreaCode'] : '';

                    $row['Party'] = '';
                    $row['Occupation'] = '';
                    $row['Committees'] = '';
                    $row['County'] = '';
                    $row['Department'] = '';
                    $row['Bio'] = '';
                    $row['Race'] = '';
                    $row['WorkEmailAddress'] = '';

                    $CustomFields = isset($jsonData['MemberCustomFieldResponses']) ? $jsonData['MemberCustomFieldResponses'] : [];
                    foreach($CustomFields as $key => $CustomField) {
                        $values = isset($CustomField['Values']) ? $CustomField['Values'] : [];
                        $field_value = '';
                        foreach($values as $value) {
                            if (!empty($value['Value'])) {
                                if (!empty($field_value)) $field_value .= ',';
                                $field_value .= $value['Value'];
                            }
                        }

                        if ($CustomField['FieldCode'] == 'Party') {
                            $row['Party'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'Occupation') {
                            $row['Occupation'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'Committees') {
                            $row['Committees'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'County') {
                            $row['County'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'Department') {
                            $row['Department'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'Bio') {
                            $row['Bio'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'Race') {
                            $row['Race'] = $field_value;
                        }
                        elseif ($CustomField['FieldCode'] == 'WorkEmail') {
                            $row['WorkEmailAddress'] = $field_value;
                        }
                    }
                }
            }
        }
        catch ( Exception $ex ) {
            $jsonData = null;
            return false;
        }

        if (isset($row['MemberTypeCode']) && in_array($row['MemberTypeCode'], $this->allowedMemberTypes)) {
          return $row;
        }

        return false;
    }

    /**
     * Fetch and Extract Profile Groups Info
     *
     * @param $profileID
     * @return bool|array
     */
    public function memberGroups( $profileID, $ck = array() ) {
        $url = $this->api_endpoint . '/Ams/'.$this->client_id.'/PeopleGroups';

        if (empty($ck)) {
            $cookies = $this->getCookies();
        }
        else {
            $cookies = $ck;
        }

        $response = wp_remote_get($url,
            array(
                'headers'   => [
                    'content-type' => 'application/json',
                ],
                'body' => array(
                    'ID' => $profileID,
                ),
                'cookies'  => $cookies
            )
        );

        $memberGroups = [];

        try {
            if (!is_wp_error( $response ) ) {
                $jsonData = json_decode( $response['body'], true );
                if ($profileID == 36916397) {
                    fac_directory_write_log(print_r($jsonData,true));
                }
                if (empty($jsonData['ResponseStatus']['Errors']) && !empty($jsonData['Relationships'])) {
                    $row = [];
                    $Relationships = isset($jsonData['Relationships']) ? $jsonData['Relationships'] : [];
                    foreach($Relationships as $key => $Relationship) {
                        if (!empty($Relationship['RelationshipTag']) && $Relationship['RelationshipTag'] == 'Member') {
                            $Groups = isset($Relationship['Groups']) ? $Relationship['Groups'] : [];
                            foreach($Groups as $key2 => $Group) {
                                $GroupID = $Group['GroupID'];
                                $Code = isset($Group['Code']) ? $Group['Code'] : '';
                                $Name = $Group['Name'];

                                $code_part_1 = substr($Code, 0, 3);
                                $code_part_2 = substr($Code, 0, 4);
                                if ($code_part_1 == 'CC-' || $code_part_2 == 'CCO-') {
                                    $Name = str_ireplace('County Contacts - Core: ', '', $Name);
                                    $Name = str_ireplace('County Contacts - Other: ', '', $Name);

                                    $memberGroups[] = [
                                        'ProfileID' => $profileID,
                                        'GroupID' => $GroupID,
                                        'GroupName' => $Name,
                                        'GroupCode' => $Code,
                                    ];
                                }
                            }
                        }

                    }
                }
            }
        }
        catch ( Exception $ex ) {
            return false;
        }

        return $memberGroups;
    }

    /**
     * Save the Member Profile Info
     *
     * @param $row
     */
    public function saveMemberData( $row ) {
	    global $wpdb;
        $table_name = $wpdb->prefix . "fd_member_profiles";
        $ProfileID = $row['ProfileID'];
        $Created = date('Y-m-d H:i:s');
        $Updated = date('Y-m-d H:i:s');

        $pID = $wpdb->get_col($wpdb->prepare("SELECT ProfileID FROM {$table_name} WHERE ProfileID=%s", $ProfileID));
        if (empty($pID)) {
            $row['Created'] = $Created;
            $row['Updated'] = $Updated;
            $wpdb->insert($table_name, $row);
        }
        else {
            unset($row['ProfileID']);
            $row['Updated'] = $Updated;
            $wpdb->update($table_name, $row, array('ProfileID' => $ProfileID));
        }
    }

    /**
     * Save the member Group Info
     *
     * @param $ProfileID
     * @param $GroupID
     * @param $Code
     * @param $Name
     */
    public function saveMemberGroupData($memberGroup ) {
        global $wpdb;
        $table_name = $wpdb->prefix . "fd_member_groups";

        $ProfileID = isset($memberGroup['ProfileID']) ? $memberGroup['ProfileID'] : '';
        $GroupID = isset($memberGroup['GroupID']) ? $memberGroup['GroupID'] : '';
        $Code = isset($memberGroup['GroupCode']) ? $memberGroup['GroupCode'] : '';
        $Name = isset($memberGroup['GroupName']) ? $memberGroup['GroupName'] : '';

        $pID = $wpdb->get_col($wpdb->prepare("SELECT ProfileID FROM {$table_name} WHERE ProfileID=%s AND GroupID=%s", $ProfileID, $GroupID));
        if (empty($pID)) {
            $row = array(
              'ProfileID' => $ProfileID,
              'GroupID' => $GroupID,
              'GroupName' => $Name,
              'GroupCode' => $Code,
            );
            $wpdb->insert($table_name, $row);
        }
        else {
            $row = array(
              'GroupName' => $Name,
              'GroupCode' => $Code,
            );
            $wpdb->update($table_name, $row, array('ProfileID' => $ProfileID, 'GroupID' => $GroupID));
        }
    }

    /**
     * Save The Group Info
     *
     * @param $GroupID
     * @param $Code
     * @param $Name
     */
    public function saveGroupData( $memberGroup, $flag = 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . "fd_groups";

        $GroupID = isset($memberGroup['GroupID']) ? $memberGroup['GroupID'] : '';
        $Code = isset($memberGroup['GroupCode']) ? $memberGroup['GroupCode'] : '';
        $Name = isset($memberGroup['GroupName']) ? $memberGroup['GroupName'] : '';
        if ($flag) {
            $Created = isset($memberGroup['Created']) ? $memberGroup['Created'] : date('Y-m-d H:i:s');
            $Updated = isset($memberGroup['Updated']) ? $memberGroup['Updated'] : date('Y-m-d H:i:s');
        }
        else {
            $Created = date('Y-m-d H:i:s');
            $Updated = date('Y-m-d H:i:s');
        }

        $gID = $wpdb->get_col($wpdb->prepare("SELECT GroupID FROM {$table_name} WHERE GroupID=%s", $GroupID));
        if (empty($gID)) {
            $row = array(
                'GroupID' => $GroupID,
                'GroupName' => $Name,
                'GroupCode' => $Code,
                'Created' => $Created,
                'Updated' => $Updated,
            );
            $wpdb->insert($table_name, $row);
        }
        else {
            $row = array(
                'GroupName' => $Name,
                'GroupCode' => $Code,
                'Updated' => $Updated,
            );
            $wpdb->update($table_name, $row, array('GroupID' => $GroupID));
        }
    }

    public function setCookies($obj) {
      $expires = time() + 3600;
      setcookie($obj->name, $obj->value, $expires, $obj->path, $obj->domain, true, false);
    }

    public function getCookies() {
      $cookies = array();
      foreach ( $_COOKIE as $name => $value ) {
        if ($name == 'ss-pid' || $name == 'ss-id' || $name == 'X-UAId') {
          $cookies[] = new WP_Http_Cookie(array('name' => $name, 'value' => $value));
        }
      }

      return $cookies;
    }

    public function deleteCookies() {
      if (isset($_COOKIE['ss-pid'])) {
        unset($_COOKIE['ss-pid']);
        setcookie('ss-pid', '', time() - 3600, '/');
      }

      if (isset($_COOKIE['ss-id'])) {
        unset($_COOKIE['ss-id']);
        setcookie('ss-id', '', time() - 3600, '/');
      }

      if (isset($_COOKIE['X-UAId'])) {
        unset($_COOKIE['X-UAId']);
        setcookie('X-UAId', '', time() - 3600, '/');
      }
    }

    public function migrateProfileData() {
        global $wpdb;
        $table_name_new = $wpdb->prefix . "fd_member_profiles";
        $table_name_old = $wpdb->prefix . "fac_dir_profiles";

        $results = $wpdb->get_results("SELECT * FROM {$table_name_old}");
        $count = 0;
        foreach($results as $r) {
            $old_id = $r->websiteid;
            $pID = $wpdb->get_col($wpdb->prepare("SELECT ProfileID FROM {$table_name_new} WHERE ProfileID=%s", $old_id));
            if (!empty($pID)) {
                $pID = reset($pID);
            }

            if (empty($pID)) {
                $row = array(
                    'ProfileID' => $r->websiteid,
                    'IsMember' => $r->ismember,
                    'ImportID' => '',
                    'Approved' => $r->approved,
                    'Suspended' => $r->suspended,
                    'Featured' => '',
                    'ApprovalDate' => '',
                    'LastModifiedDate' => '',
                    'MembershipExpires' => '',
                    'MembershipExpiresDate' => '',
                    'ConstituentID' => '',
                    'MemberTypeCode' => $r->membertypecode,
                    'Registered' => $r->Registered,
                    'MemberProfilePublic' => '',
                    'LastUpdated' => $r->LastUpdated,
                    'Gender' => '',
                    'Prefix' => $r->nameprefix,
                    'FirstName' => $r->firstname,
                    'MiddleName' => $r->middlename,
                    'LastName' => $r->lastname,
                    'Suffix' => $r->namesuffix,
                    'NickName' => '',
                    'MaidenName' => '',
                    'PersonalEmailAddress' => '',
                    'HeadshotImageURI' => $r->headshotimageuri,
                    'EmployerName' => $r->employer,
                    'SelfEmployed' => '',
                    'WorkTitle' => $r->title,
                    'WorkType' => '',
                    'WorkUrl' => '',
                    'WorkAddressLine1' => $r->empaddrlines,
                    'WorkAddressLine2' => '',
                    'WorkAddressCity' => $r->empcity,
                    'WorkAddressLocation' => $r->emplocation,
                    'WorkAddressPostalCode' => $r->emppostalcode,
                    'WorkAddressCountry' => '',
                    'WorkPhoneNumber' => $r->empphone,
                    'WorkPhoneAreaCode' => '',
                    'WorkFaxNumber' => '',
                    'WorkFaxAreaCode' => '',
                    'WorkEmailAddress' => $r->emailaddr,
                    'Party' => '',
                    'Occupation' => '',
                    'Committees' => '',
                    'County' => '',
                    'Department' => '',
                    'Bio' => '',
                    'Race' => '',
                    'Created' => $r->created_at,
                    'Updated' => $r->updated_at,
                );
                $wpdb->insert($table_name_new, $row);
            }
            else {
                $row = array(
                    'IsMember' => $r->ismember,
                    'Approved' => $r->approved,
                    'Suspended' => $r->suspended,
                    'MemberTypeCode' => $r->membertypecode,
                    'Registered' => $r->Registered,
                    'LastUpdated' => $r->LastUpdated,
                    'Prefix' => $r->nameprefix,
                    'FirstName' => $r->firstname,
                    'MiddleName' => $r->middlename,
                    'LastName' => $r->lastname,
                    'Suffix' => $r->namesuffix,
                    'HeadshotImageURI' => $r->headshotimageuri,
                    'EmployerName' => $r->employer,
                    'WorkTitle' => $r->title,
                    'WorkAddressLine1' => $r->empaddrlines,
                    'WorkAddressCity' => $r->empcity,
                    'WorkAddressLocation' => $r->emplocation,
                    'WorkAddressPostalCode' => $r->emppostalcode,
                    'WorkPhoneNumber' => $r->empphone,
                    'WorkEmailAddress' => $r->emailaddr,
                    'Created' => $r->created_at,
                    'Updated' => $r->updated_at,
                );
                $wpdb->update($table_name_new, $row, array('ProfileID' => $r->websiteid));
            }
        }
    }

    public function migrateGroupData() {
        global $wpdb;
        $table_name_old1 = $wpdb->prefix . "fac_dir_group_profile";
        $table_name_old2 = $wpdb->prefix . "fac_dir_groups";
        $table_name_old3 = $wpdb->prefix . "fac_dir_profiles";

        $results_group_profiles = $wpdb->get_results("SELECT * FROM {$table_name_old1}");
        $count = 0;
        $a_count = 0;
        foreach($results_group_profiles as $r1) {
            $group_id = $r1->group_id;
            $profile_id = $r1->profile_id;

            $ProfileID = '';
            $GroupName = '';
            $GroupCode = '';
            $Created = date('Y-m-d H:i:s');
            $Updated = date('Y-m-d H:i:s');

            $results_group = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name_old2} WHERE id=%d", $group_id));
            if ($results_group) {
                $GroupName = $results_group->name;
                $GroupCode = $results_group->code;
                $Created = $results_group->created_at;
                $Updated = $results_group->updated_at;
            }

            $result_profile = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name_old3} WHERE id=%s", $profile_id));
            if ($result_profile) {
                $ProfileID = $result_profile->websiteid;
            }
            ++$count;

            if (!empty($ProfileID)) {
                ++$a_count;
                $memberGroup = array(
                  'ProfileID' => $ProfileID,
                  'GroupID' => $group_id,
                  'GroupCode' => $GroupCode,
                  'GroupName' => $GroupName,
                  'Created' => $Created,
                  'Updated' => $Updated,
                );

                $this->saveMemberGroupData($memberGroup);
                $this->saveGroupData( $memberGroup, 1);
            }
        }

    }

    function getMissingProfiles($ids = []) {
        global $wpdb;
        $table_name = $wpdb->prefix . "fd_member_profiles";

        $data = [];
        foreach($ids as $id) {
            $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE ProfileID=%d", $id));
            if (!empty($result->ProfileID)) {
              $data[] = $id;
            }
        }

        return $data;
    }

}
