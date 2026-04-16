<?php
/**
 * MSG LMS Migrate Functions
 *
 * @author 		Shafiq Hossain
 * @category 	Core
 * @package 	MSG Lms/Functions
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Initialize Drupal 7 database connection
 */
add_action('init', 'msg_initialize_migratedb');
function msg_initialize_migratedb() {
  global $migratedb;
  //$migratedb = new WPDB( 'root', 'emon', 'msg7', 'localhost');
  $migratedb = new WPDB( 'thetaman_portal', 'xbBIQa@viWS#', 'thetaman_portal', 'localhost');
}

/**
 * Migrate
 */
add_action( 'wp_router_generate_routes', 'migrate_portal_add_routes', 20 );
function migrate_portal_add_routes( $router ) {
    $route_args = array(
		'path' => 'migrate/portal',
		'query_vars' => array( ),
		'page_callback' => 'migrate_portal_route_callback',
		'page_arguments' => array(),
		'access_callback' => true,
		'title' => __( 'Migrate Portal' ),
		'template' => false
    );
    $router->add_route( 'migrate-portal', $route_args );
    //write_log('Location: migrate_portal_add_routes');
}

function migrate_portal_route_callback( ) {
    //write_log('Location: migrate_portal_route_callback');
	//get current user
	$current_user_id = get_current_user_id();

	//anonymous user not allowed
	if(empty($current_user_id)) return;

	if($current_user_id != 1) {
      set_transient( get_current_user_id() . '_message_notification',
        __( 'Access denied.', 'talemy' )
      );

	  $post_id = 26470;  //home page
	  $url = get_post_permalink($post_id);
	  wp_redirect( $url, 302 );
	  exit;
	}
    ?>


	<form name="migrate_confirmation_form" method="POST" onsubmit="return migrate_form_validation()" action="/migrate-portal-process">
		User Name: <input id="user_name" name="user_name" type="text" />
		Password: <input id="user_pass" name="user_pass" type="password" />
  		<fieldset id="group2">
  		  <legend>Migration Type</legend>
		  <input type="radio" name="migration_type" value="users"> 1. Users<br>
		  <input type="radio" name="migration_type" value="courses"> 2. Courses<br>
		  <input type="radio" name="migration_type" value="memberships"> 3. Memberships<br>
		  <input type="radio" name="migration_type" value="enrollments"> 4. Enrollments<br>
		  <input type="radio" name="migration_type" value="groups"> 5. Groups<br>
		  <input type="radio" name="migration_type" value="messages"> 6. Messages<br>
		  <input type="radio" name="migration_type" value="friends"> 7. Friends<br>
		  <input type="radio" name="migration_type" value="walls"> 8. Wall Posts<br>
		  <input type="radio" name="migration_type" value="orders"> 9. Orders<br>
		  <input type="radio" name="migration_type" value="repair-courses"> 10. Repair Courses<br>
		  <input type="radio" name="migration_type" value="membership-update"> 11. Memberships Update<br>
		  <input type="radio" name="migration_type" value="friends-createtime-fix"> 12. Friends Createtime Update<br>
		  <input type="radio" name="migration_type" value="none" checked='checked' > None<br>
		</fieldset>
		<br>
		<input type="submit" value="Submit" />
	</form>

	<script type="text/javascript">
	  function migrate_form_validation() {
		var user_name = document.forms['migrate_confirmation_form']['user_name'].value;
		if (user_name == '' || user_name == null) {
		  alert('Please enter the user name.');
		  return false;
		}

		var user_pass = document.forms['migrate_confirmation_form']['user_pass'].value;
		if (user_pass == '' || user_pass == null) {
		  alert('Please enter the user password.');
		  return false;
		}
	  }
	</script>
    <?php
}


add_action( 'wp_router_generate_routes', 'migrate_portal_process_add_routes', 30 );
function migrate_portal_process_add_routes( $router ) {
    //write_log('Location: migrate_portal_process_add_routes');
    $route_args = array(
		'path' => 'migrate-portal-process',
		'query_vars' => array( ),
		'page_callback' => 'migrate_portal_process_route_callback',
		'page_arguments' => array(),
		'access_callback' => true,
		'title' => __( 'Migrate Portal Process' ),
		'template' => false
    );
    $router->add_route( 'migrate-portal-process', $route_args );
}

function migrate_portal_process_route_callback( ) {
    //write_log('Location: migrate_portal_process_route_callback');
	//get current user
	$current_user_id = get_current_user_id();

	//anonymous user not allowed
	if(empty($current_user_id)) return;

	if($current_user_id != 1) {
      set_transient( get_current_user_id() . '_message_notification',
        __( 'Access denied.', 'talemy' )
      );

	  $post_id = 26470;  //home page
	  $url = get_post_permalink($post_id);
	  wp_redirect( $url, 302 );
	  exit;
	}

	//get the form submit
    $user_name = $_POST['user_name'];
    $user_pass = $_POST['user_pass'];

    $migration_type = $_POST['migration_type'];
    if(empty($migration_type)) $migration_type = 'users';

    //validation
    if($user_name != 'msg-webmaster' && $user_pass != 'msg-webmaster') {
      set_transient( get_current_user_id() . '_message_notification',
        __( 'Access denied.', 'talemy' )
      );

	  $post_id = 26470;  //home page
	  $url = get_post_permalink($post_id);
	  wp_redirect( $url, 302 );
	  exit;
    }

	//change max execution time
	$max_execution_time = ini_get('max_execution_time');
	set_time_limit(0);

	$lock = new WP_Lock( 'migrate_portal' );
	if ( $lock->acquire( WP_Lock::WRITE ) ) {
  		$migratedb = new WPDB( 'thetaman_portal', 'xbBIQa@viWS#', 'thetaman_portal', 'localhost');
  		$migrate_moodledb = new WPDB( 'thetaman_portal', 'xbBIQa@viWS#', 'thetaman_portal', 'localhost');
  	    //$migrate_file_path = 'C:/My Work2/src/UpWork/HimanshuJuneja/portal';
  	    $migrate_file_path = '/home/thetamanagement/portal';

		if($migration_type == 'users') {
		  //migrate users
		  migrate_users($migratedb, $migrate_file_path);
		}
		else if($migration_type == 'courses') {
		  //migrate courses
		  migrate_courses($migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Course Complete -------------------');
		}
		else if($migration_type == 'memberships') {
		  //migrate user membership
		  migrate_users_memberships($migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Users Membership Complete -------------------');
		}
		else if($migration_type == 'enrollments') {
		  //migrate user enrolled courses
		  migrate_users_course_enrollments($migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Users Course Enrollment Complete -------------------');
		}
		else if($migration_type == 'groups') {
		  //migrate user groups and group wall post
		  do_action('msg_migrate_bb_groups_action', $migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Group Complete -------------------');
		}
		else if($migration_type == 'messages') {
		  //migrate private messages
		  do_action('msg_migrate_bb_messages_action', $migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Private Message Complete -------------------');
		}
		else if($migration_type == 'friends') {
		  //migrate user friends
		  do_action('msg_migrate_bb_friends_action', $migratedb, $migrate_file_path);
		  write_log('-------------- Migrate User Friends Complete -------------------');
		}
		else if($migration_type == 'walls') {
		  //migrate user wall posts
		  do_action('migrate_user_wall_posts', $migratedb, $migrate_file_path);
		  write_log('-------------- Migrate User Wall Post Complete -------------------');
		}
		else if($migration_type == 'orders') {
		  //migrate orders / purchase
		  do_action('msg_migrate_woocommerce_orders_action', $migratedb, $migrate_file_path);
		  write_log('-------------- Migrate Orders Complete -------------------');
		}
		else if($migration_type == 'repair-courses') {
		  //Set course ratings data
		  courses_set_rating_data($migratedb, $migrate_moodledb);
		  write_log('-------------- Set Courses Rating Complete -------------------');
		}
		else if($migration_type == 'membership-update') {
		  //Update membership data
		  migrate_users_memberships_update($migratedb, $migrate_file_path);
		  write_log('-------------- Set Membership Update Complete -------------------');
		}
		else if($migration_type == 'friends-createtime-fix') {
		  //Update friends createtime
		  migrate_user_friends_update_createtime($migratedb, $migrate_file_path);
		  write_log('-------------- Update Friends Createtime Complete -------------------');
		}

		//release the lock
		$lock->release();

        set_transient( get_current_user_id() . '_message_notification',
          __( 'Migration is complete!', 'talemy' )
        );
	}
	else {
      set_transient( get_current_user_id() . '_message_notification',
        __( 'System busy.', 'talemy' )
      );
	}

	//change back max execution time
	set_time_limit($max_execution_time);


	$post_id = 26470;  //home page
	$url = get_post_permalink($post_id);
	wp_redirect( $url, 302 );
	exit;
}

function migrate_users($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_users');

  //get the woocommerce country object
  $countries_obj = new WC_Countries();
  $countries = $countries_obj->__get('countries');

  $sql  = 'SELECT a.*, ';
  $sql .= 'b.idnumbers, c.field_first_name_value, d.field_last_name_value, e.field_gender_value, f.field_date_of_birth_value, ';
  $sql .= 'h.field_contact_number_value, i.field_area_of_specialization_value, ';
  $sql .= 'k.street, k.additional, k.city, k.province, k.postal_code, k.country, ';
  $sql .= 'l.field_terms_and_conditions_value, m.field_type_of_account_value, n.field_web_site_value, ';
  $sql .= 'o.field_short_description_value, p.field_subuser_limit_value, q.filename, q.uri, ';
  $sql .= 'a.created, a.access, a.login, a.timezone ';
  $sql .= 'FROM users a ';
  $sql .= 'LEFT JOIN users_idnumber b ON a.uid = b.uid ';
  $sql .= "LEFT JOIN field_data_field_first_name c ON a.uid = c.entity_id AND c.entity_type = 'user' AND c.bundle = 'user' AND c.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_last_name d ON a.uid = d.entity_id AND d.entity_type = 'user' AND d.bundle = 'user' AND d.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_gender e ON a.uid = e.entity_id AND e.entity_type = 'user' AND e.bundle = 'user' AND e.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_date_of_birth f ON a.uid = f.entity_id AND f.entity_type = 'user' AND f.bundle = 'user' AND f.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_contact_number h ON a.uid = h.entity_id AND h.entity_type = 'user' AND h.bundle = 'user' AND h.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_area_of_specialization i ON a.uid = i.entity_id AND i.entity_type = 'user' AND i.bundle = 'user' AND i.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_address j ON a.uid = j.entity_id AND j.entity_type = 'user' AND j.bundle = 'user' AND j.deleted = 0 ";
  $sql .= "LEFT JOIN location k ON j.field_address_lid = k.lid ";
  $sql .= "LEFT JOIN field_data_field_terms_and_conditions l ON a.uid = l.entity_id AND l.entity_type = 'user' AND l.bundle = 'user' AND l.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_type_of_account m ON a.uid = m.entity_id AND m.entity_type = 'user' AND m.bundle = 'user' AND m.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_web_site n ON a.uid = n.entity_id AND n.entity_type = 'user' AND n.bundle = 'user' AND n.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_short_description o ON a.uid = o.entity_id AND o.entity_type = 'user' AND o.bundle = 'user' AND o.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_subuser_limit p ON a.uid = p.entity_id AND p.entity_type = 'user' AND p.bundle = 'user' AND p.deleted = 0 ";
  $sql .= "LEFT JOIN file_managed q ON a.picture = q.fid ";
  $sql .= "WHERE a.uid > 0 ";
  $sql .= "AND a.is_migrated = 0 ";
  $sql .= "LIMIT 25000 ";

  $results = $migratedb->get_results($sql);
  //write_log($results);

  foreach($results as $row) {
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_users_mapping a ';
	$sql0 .= 'WHERE a.uid = '.$row->uid." ";
	$sql0 .= 'AND a.is_migrated = 1 ';
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	//if found, continue;
	if(isset($results0->uid) && $results0->uid > 0) continue;

	//get the values
	$uid = $row->uid;
	$name = $row->name;
	$fullname = $row->fullname;
	$email = $row->mail;
	$status = $row->status;

	$picture_filename = !empty($row->filename) ? $row->filename : '';
	$picture_uri = !empty($row->uri) ? $row->uri : '';

	$user_login = msg_get_username($name);

	$nick_name = strtolower($fullname);
	$nick_name = str_ireplace(' ', '-', $nick_name);

    $idnumbers = (!empty($row->idnumbers) ? $row->idnumbers : '');
    $first_name = (!empty($row->field_first_name_value) ? $row->field_first_name_value : '');
    $last_name = (!empty($row->field_last_name_value) ? $row->field_last_name_value : '');
    $gender = (!empty($row->field_gender_value) ? $row->field_gender_value : '');
    $date_of_birth = (!empty($row->field_date_of_birth_value) ? $row->field_date_of_birth_value : '');
    $contact_number = (!empty($row->field_contact_number_value) ? $row->field_contact_number_value : '');
    $area_of_specialization = (!empty($row->field_area_of_specialization_value) ? $row->field_area_of_specialization_value : '');
    $street = (!empty($row->street) ? $row->street : '');
    $additional = (!empty($row->additional) ? $row->additional : '');
    $city = (!empty($row->city) ? $row->city : '');
    $province = (!empty($row->province) ? $row->province : '');
    $postal_code = (!empty($row->postal_code) ? $row->postal_code : '');
    $country = (!empty($row->country) ? $row->country : '');
    $terms_and_conditions = (!empty($row->field_terms_and_conditions_value) ? $row->field_terms_and_conditions_value : '');
    $type_of_account_value = (!empty($row->field_type_of_account_value) ? $row->field_type_of_account_value : '');
    $web_site_value = (!empty($row->field_web_site_value) ? $row->field_web_site_value : '');
    $short_description_value = (!empty($row->field_short_description_value) ? $row->field_short_description_value : '');
    $subuser_limit_value = (!empty($row->field_subuser_limit_value) ? $row->field_subuser_limit_value : 0);
    $picture = (!empty($row->field_subuser_limit_value) ? $row->field_subuser_limit_value : 0);

    $created = (!empty($row->created) ? $row->created : 0);
    $access = (!empty($row->access) ? $row->access : 0);
    $login = (!empty($row->login) ? $row->login : 0);
    $timezone = (!empty($row->timezone) ? $row->timezone : '');

    $country_name = '';
    if(!empty($country)) {
      $country_name = $countries[strtoupper($country)];
    }

    $old_user = get_user_by('email', $email );
    if( ! $old_user ) {
	  $user_data = array(
	    'user_pass' => wp_generate_password(12, false),
	    'user_login' => $user_login,
	    'user_nicename' => $nick_name,
	    'user_url' => $web_site_value,
	    'user_email' => $email,
	    'display_name' => $fullname,
	    'first_name' => $first_name,
	    'last_name' => $last_name,
	    'description' => $short_description_value,
	    'user_registered' => date('Y-m-d H:i:s', $created),
        'user_status' => 0,  //ham
	  );

	  $user_meta = array(
		'type_of_account' => msg_get_type_of_accounts($type_of_account_value),
		'paying_customer' => 1,
		'description' => $short_description_value,
		'nickname' => $user_login,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'subuser_limit' => $subuser_limit_value,
		'wp_user_avatar' => '',
		'sf_user_avatar' => '',
	  );

	  $user_id = wp_insert_user( $user_data );
	  if ( is_wp_error( $user_id ) ) {
	     echo 'ERROR: ' . $user_id->get_error_message();
	     continue;
	  }
	  else {
	    $user = get_user_by('id', $user_id );
	  }

	  if(!$user) continue;

	  $user_avatar_id = '';
	  $user_avatar_url = '';
	  if(!empty($picture_uri)) {
        $user_avatar = msg_upload_profile_image($migrate_file_path, $picture_filename, $picture_uri);

        $user_avatar_id = isset($user_avatar['attach_id']) ? $user_avatar['attach_id'] : '';
        $user_avatar_url = isset($user_avatar['file_url']) ? $user_avatar['file_url'] : '';
      }
	  $user_meta['wp_user_avatar'] = $user_avatar_id;
	  $user_meta['sf_user_avatar'] = $user_avatar_url;

	  //update user meta
      foreach( $user_meta as $key => $val ) {
        update_user_meta( $user_id, $key, $val );
      }

	  //roles
	  $sql3  = 'SELECT a.uid, ';
	  $sql3 .= 'c.rid, c.name ';
	  $sql3 .= 'FROM users a ';
	  $sql3 .= "LEFT JOIN users_roles b ON a.uid = b.uid ";
	  $sql3 .= "LEFT JOIN role c ON b.rid = c.rid ";
	  $sql3 .= 'WHERE a.uid = '.$row->uid." ";
	  $results3 = $migratedb->get_results($sql3);

      foreach($results3 as $row3) {
        $rid = $row3->rid;
        $wp_roles = msg_get_role($rid);
        foreach($wp_roles as $wp_role) {
          $user->add_role( $wp_role );
        }
      }

	  //interest
	  $sql2  = 'SELECT a.uid, ';
	  $sql2 .= 'g.field_interest_value ';
	  $sql2 .= 'FROM users a ';
	  $sql2 .= "LEFT JOIN field_data_field_interest g ON a.uid = g.entity_id AND g.entity_type = 'user' AND g.bundle = 'user' AND g.deleted = 0 ";
	  $sql2 .= 'WHERE a.uid = '.$row->uid." ";
	  $results2 = $migratedb->get_results($sql2);

	  $interests = array();
      foreach($results2 as $row2) {
        if(!empty($row2->field_interest_value)) {
	      $interests[] = msg_get_interests($row2->field_interest_value);
	    }
      }

	  $address  = $street.PHP_EOL;
	  $address .= $additional.PHP_EOL;
	  $address .= $city.' '.$province.' '.$postal_code;

      $bb_meta = array(
		'gender' => ucfirst($gender),
		'date_of_birth' => !empty($date_of_birth) ? date('Y-m-d 00:00:00', strtotime($date_of_birth)) : '',
		'contact_number' => $contact_number,
		'area_of_specialization' => msg_get_area_of_specialization($area_of_specialization),
	    'interests' => $interests,
	    'address' => $address,
	    'country' => $country_name,
      );

	  //buddypress fields update
	  do_action('msg_migrate_bb_profile_action', $user, $bb_meta);

	  //add the uid into migrate mapping table
      $sql5 = "INSERT INTO wp_migrate_users_mapping (uid, new_uid, is_migrated) VALUES ($uid, $user->ID, 1) ON DUPLICATE KEY UPDATE new_uid = VALUES(new_uid), is_migrated = 1";
      $wpdb->query($sql5);

	  //flag drupal user record
      $sql6 = "UPDATE users SET is_migrated=1 WHERE uid = ".$uid;
      $migratedb->query($sql6);
    }
  }

}

function msg_migrate_bb_profile_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_bb_profile_action');
}
add_action( 'bp_ready', 'msg_migrate_bb_profile_action', 10);

function msg_migrate_bb_profile($user = NULL, $bb_meta = NULL) {
  //write_log('Location: msg_migrate_bb_profile');
  if($user == false) return;
  if(empty($bb_meta)) return;

  $user_id = $user->ID;

  //Gender
  if(isset($bb_meta['gender']) && !empty($bb_meta['gender'])) {
    xprofile_set_field_data(203, $user_id, $bb_meta['gender']);
  }

  //Contact Number
  if(isset($bb_meta['contact_number']) && !empty($bb_meta['contact_number'])) {
    xprofile_set_field_data(206, $user_id, $bb_meta['contact_number']);
  }

  //Address
  if(isset($bb_meta['address']) && !empty($bb_meta['address'])) {
    xprofile_set_field_data(424, $user_id, $bb_meta['address']);
  }

  //Area of Specialization
  if(isset($bb_meta['area_of_specialization']) && !empty($bb_meta['area_of_specialization'])) {
    xprofile_set_field_data(214, $user_id, $bb_meta['area_of_specialization']);
  }

  //Country
  if(isset($bb_meta['country']) && !empty($bb_meta['country'])) {
    xprofile_set_field_data(230, $user_id, $bb_meta['country']);
  }

  //Date of Birth
  if(isset($bb_meta['date_of_birth']) && !empty($bb_meta['date_of_birth'])) {
    xprofile_set_field_data(202, $user_id, $bb_meta['date_of_birth']);
  }

  //Interest
  if(isset($bb_meta['interests']) && !empty($bb_meta['interests'])) {
    xprofile_set_field_data(207, $user_id, $bb_meta['interests']);
  }

}
add_action( 'msg_migrate_bb_profile_action', 'msg_migrate_bb_profile', 10, 2);


function msg_get_username($user_name = '') {
  //write_log('Location: msg_get_username');
  if(empty($user_name)) {
    $length = 10;
    $user_name = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )), 1, $length);
  }

  $count = 1;
  $user_name_temp = $user_name;

  while(true) {
    $user_id = username_exists( $user_name );
    if ( !$user_id ) break;

	$user_name = $user_name_temp.$count;
	++$count;
  }

  return $user_name;
}

function msg_get_role($rid = 0) {
  //write_log('Location: msg_get_role');
  $new_roles = array();

  $roles = array(
	1 => 'subscriber', 				//anonymous user
	2 => 'subscriber', 				//authenticated user
	3 => 'administrator',			//administrator
	4 => 'subscriber', 				//remote-service
	43 => 'subscriber', 			//paid user
	44 => 'subscriber',				//Airtel
	45 => 'subscriber',				//corporate admin user
	46 => 'subscriber',				//corporate paid user
	47 => 'subscriber',				//corporate mohd.tariq
	49 => 'subscriber',				//corporate stephaniekemp788
	50 => 'subscriber',				//corporate bigcreekgroup
  );

  if(isset($roles[$rid]) && !empty($roles[$rid])) {
    $new_roles[] = $roles[$rid];
  }

  if($rid == 43) {  //paid user
    $new_roles[] = 'member';
    $new_roles[] = 'bbp_participant';
  }

  if($rid == 46) {  //corporate paid user
    $new_roles[] = 'corporate';
    $new_roles[] = 'bbp_participant';
  }

  if($rid == 45) {  //corporate admin user
    $new_roles[] = 'corporate_admin';
  }

  if(in_array($rid, array(44,47,49,50))) {  //Airtel, corporate mohd.tariq, corporate stephaniekemp788, corporate bigcreekgroup
    $new_roles[] = 'corporate';
  }

  //unique
  $new_roles = array_unique($new_roles);

  return $new_roles;
}

/**
  * Type of accounts mapping
  */
function msg_get_type_of_accounts($type_of_account = '') {
  //write_log('Location: msg_get_type_of_accounts');
  if(empty($type_of_account)) return 'Other';

  $types = array(
    'student' => 'Student',
    'working_professional' => 'Working Professional',
    'faculty_member' => 'Faculty Member',
  );

  if(isset($types[$type_of_account]) && !empty($types[$type_of_account])) {
    return $types[$type_of_account];
  }
  else {
    return 'Other';
  }
}


/**
  * Interests mapping
  */
function msg_get_interests($interest = '') {
  //write_log('Location: msg_get_interests');
  if(empty($interest)) return '';

  $interest_types = array(
    'politics' => 'Politics',
    'news' => 'News',
    'education' => 'Education',
    'sports' => 'Sports',
    'music' => 'Music',
    'travel' => 'Travel'
  );

  if(isset($interest_types[$interest]) && !empty($interest_types[$interest])) {
    return $interest_types[$interest];
  }
  else {
    return '';
  }
}


/**
  * Area of Specialization
  */
function msg_get_area_of_specialization($area = '') {
  //write_log('Location: msg_get_area_of_specialization');
  if(empty($area)) return '';

  $area_types = array(
    'marketing' => 'Marketing',
    'human_resource_management' => 'Human Resource Management',
    'finance' => 'Finance',
    'communication' => 'Communication',
    'entrepreneurship' => 'Entrepreneurship',
    'information_technology' => 'Information Technology',
    'operations' => 'Operations',
    'healthcare_management' => 'Healthcare Management',
    'advertising' => 'Advertising',
    'travel_and_tourism' => 'Travel and Tourism',
    'retail_management' => 'Retail Management',
    'international_business' => 'International Business',
    'supply_chain_management' => 'Supply Chain Management',
    'general_management' => 'General Management',
    'others' => 'Others',
  );

  if(isset($area_types[$area]) && !empty($area_types[$area])) {
    return $area_types[$area];
  }
  else {
    return '';
  }
}

function msg_upload_profile_image($migrate_file_path, $filename, $uri) {
  //write_log('Location: msg_upload_profile_image');
  $filename = str_ireplace("/", '_', $filename);
  $filename = str_ireplace(" ", '_', $filename);

  $uri = str_ireplace('public://','',$uri);
  $image_url = $migrate_file_path . '/sites/default/files/'.$uri;
  $image_data = file_get_contents( $image_url );

  $upload_dir = wp_upload_dir();
  if ( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
  }
  else {
    $file = $upload_dir['basedir'] . '/' . $filename;
  }

  file_put_contents( $file, $image_data );
  $wp_filetype = wp_check_filetype( $filename, null );

  $attachment = array(
    'guid' => $file,
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => sanitize_file_name( $filename ),
    'post_content' => '',
    'post_status' => 'inherit'
  );

  $attach_id = wp_insert_attachment( $attachment, $file );
  $file_url = '';
  if (!is_wp_error($attach_id)) {
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    $wp_attached_file_url = get_post_meta($attach_id, '_wp_attached_file', true);
    $file_url = $upload_dir['baseurl'] . '/' . $wp_attached_file_url;
  }

  return array('file_url' => $file_url, 'attach_id' => $attach_id);
}



function migrate_courses($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_courses');

  $sql  = 'SELECT a.nid, a.title, a.created, a.changed, ';
  $sql .= 'b.field_category_tid, c.field_course_short_name_value, d.field_course_summary_value, e.field_no_of_slides_value, f.field_created_from_value, ';
  $sql .= 'h.field_course_type_value, j.field_course_url_moodle_value, ';
  $sql .= 'k.field_course_id_moodle_value, l.field_published_date_value, ';
  $sql .= 'q.filename, q.uri ';
  $sql .= 'FROM node a ';
  $sql .= "LEFT JOIN field_data_field_category b ON a.nid = b.entity_id AND b.entity_type = 'node' AND b.bundle = 'course_builder' AND b.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_short_name c ON a.nid = c.entity_id AND c.entity_type = 'node' AND c.bundle = 'course_builder' AND c.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_summary d ON a.nid = d.entity_id AND d.entity_type = 'node' AND d.bundle = 'course_builder' AND d.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_no_of_slides e ON a.nid = e.entity_id AND e.entity_type = 'node' AND e.bundle = 'course_builder' AND e.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_created_from f ON a.nid = f.entity_id AND f.entity_type = 'node' AND f.bundle = 'course_builder' AND f.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_type h ON a.nid = h.entity_id AND h.entity_type = 'node' AND h.bundle = 'course_builder' AND h.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_image i ON a.nid = i.entity_id AND i.entity_type = 'node' AND i.bundle = 'course_builder' AND i.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_url_moodle j ON a.nid = j.entity_id AND j.entity_type = 'node' AND j.bundle = 'course_builder' AND j.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_course_id_moodle k ON a.nid = k.entity_id AND k.entity_type = 'node' AND k.bundle = 'course_builder' AND k.deleted = 0 ";
  $sql .= "LEFT JOIN field_data_field_published_date l ON a.nid = l.entity_id AND l.entity_type = 'node' AND l.bundle = 'course_builder' AND l.deleted = 0 ";
  $sql .= "LEFT JOIN file_managed q ON i.field_course_image_fid = q.fid ";
  $sql .= "WHERE a.type = 'course_builder' ";
  $sql .= "AND a.status = 1 ";
  $sql .= "AND a.is_migrated = 0 ";
  //$sql .= "LIMIT 100 ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_nodes_mapping a ';
	$sql0 .= 'WHERE a.nid = '.$row->nid." ";
	$sql0 .= 'AND a.is_migrated = 1 ';
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	//if found, continue;
	if(isset($results0->nid) && $results0->nid > 0) continue;

	$product_pid = 0;
	$course_pid = 0;

	$nid = $row->nid;
	$title = $row->title;
	$created = $row->created;
	$changed = $row->changed;

    $post_name = strtolower($title);
    $post_name = trim($post_name);
    $post_name = str_ireplace(' ','-', $post_name);

    $category_tid = !empty($row->field_category_tid) ? $row->field_category_tid : 0;
    $course_short_name = !empty($row->field_course_short_name_value) ? $row->field_course_short_name_value : '';
    $course_summary = !empty($row->field_course_summary_value) ? $row->field_course_summary_value : '';
    $no_of_slides = !empty($row->field_no_of_slides_value) ? $row->field_no_of_slides_value : '';
    $created_from = !empty($row->field_created_from_value) ? $row->field_created_from_value : '';
    $course_type = !empty($row->field_course_type_value) ? $row->field_course_type_value : '';
    $course_url_moodle = !empty($row->field_course_url_moodle_value) ? $row->field_course_url_moodle_value : '';
    $course_id_moodle = !empty($row->field_course_id_moodle_value) ? $row->field_course_id_moodle_value : '';
    $published_date = !empty($row->field_published_date_value) ? $row->field_published_date_value : '';
    $course_image_filename = !empty($row->filename) ? $row->filename : '';
    $course_image_uri = !empty($row->uri) ? $row->uri : '';

    $attach_id = 0;
    if(!empty($course_image_uri)) {
      $attach_id = msg_upload_media_image($migrate_file_path, $course_image_filename, $course_image_uri);
    }

	if($course_type == 'paid') {
	  //product category
      $product_cat_term = msg_get_product_category($category_tid);
      $product_cat = isset($product_cat_term['term_id']) ? $product_cat_term['term_id'] : 0;
      if(empty($product_cat)) {
        $product_cats = array();
      }
      else {
        $product_cats = array($product_cat);
      }

	  //create product
	  $product_data = array(
        'post_title'    => $title,
        'post_content'  => $course_summary,
        'post_excerpt'  => $course_short_name,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'	    => 'product',
        'post_date' 	=> date( 'Y-m-d H:i:s', $created ),
        'post_date_gmt' => date( 'Y-m-d H:i:s', $created ),
        'post_modified' => date( 'Y-m-d H:i:s', $changed ),
        'post_modified_gmt' => date( 'Y-m-d H:i:s', $changed ),
        'post_name'     => $post_name,
        'comment_status'=> 'closed',
        'comment_count' => 0,
        'ping_status'   => 'closed',
        'post_parent'   => 0,
        'filter' 	      => 'raw',
        'tax_input'    => array(
          'product_type'  => array(326),  //course
          'product_cat' => $product_cats,
        ),
	    'meta_input'   => array(
	      '_thumbnail_id' => $attach_id,
	      '_regular_price'   => 29,
	      'total_sales' => 0,
	      '_tax_status' => 'taxable',
	      '_tax_class' => '',
	      '_manage_stock' => 'no',
	      '_backorders' => 'no',
	      '_sold_individually' => 'yes',
	      '_virtual' => 'yes',
	      '_downloadable' => 'no',
	      '_download_limit' => -1,
	      '_download_expiry' => -1,
	      '_stock' => '',
	      '_stock_status' => 'instock',
	      '_wc_average_rating' => 0,
	      '_wc_review_count' => 0,
	      '_product_version' => '3.8.1',
	      '_price' => 29,
	      '_related_course' => [],  		//update later
	      'slide_template' => 'default',
	    ),
	  );

	  $product_pid = wp_insert_post( $product_data );
	  if ( is_wp_error( $product_pid ) ) {
	    echo 'ERROR: ' . $product_pid->get_error_message();
	    continue;
	  }
	  else {
	    $product = get_post( $product_pid );
	  }
	}


	//get the course category
	$ld_course_category_term = msg_get_course_category($category_tid);
	$ld_course_category = isset($ld_course_category_term['term_id']) ? $ld_course_category_term['term_id'] : 0;

    if(empty($ld_course_category)) {
      $ld_course_categories = array();
    }
    else {
      $ld_course_categories = array($ld_course_category);
    }

	//create course
	$course_data = array(
      'post_title'    => $title,
      'post_content'  => $course_summary,
      'post_excerpt'  => $course_short_name,
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'	  => 'sfwd-courses',
      'post_date' 	  => date( 'Y-m-d H:i:s', $created ),
      'post_date_gmt' => date( 'Y-m-d H:i:s', $created ),
      'post_modified' => date( 'Y-m-d H:i:s', $changed ),
      'post_modified_gmt' => date( 'Y-m-d H:i:s', $changed ),
      'post_name'         => $post_name,
      'comment_status'=> 'closed',
      'comment_count' => 0,
      'ping_status'   => 'closed',
      'post_parent'   => 0,
      'filter' 	      => 'raw',
      'tax_input'    => array(
        'ld_course_category' => $ld_course_categories,
        'elementor_library_type' => array(164),			//page
        'ld_course_tag' => array(397),					//Free
      ),
	  'meta_input'   => array(
	    '_elementor_edit_mode' => 'builder',
	    '_elementor_template_type' => 'page',
	    '_elementor_controls_usage' => '',
	    '_elementor_version' => '2.8.2',
	    '_thumbnail_id' => $attach_id,
	    '_wp_page_template' => 'default',
	    '_elementor_page_settings' => '',
	    '_elementor_data' => '',
	    '_wp_old_slug' => 'course-content',
	    '_wp_old_date' => date('Y-m-d', $created),
	    '_ldcr_review_after' => 0,
	    '_sf_demo_content' => 1,
	    '_ldcr_enable' => 1,
	    '_learndash_course_grid_short_description' => '',
	    '_learndash_course_grid_enable_video_preview' => 1,
	    '_learndash_course_grid_video_embed_code' => '',
	    '_learndash_course_grid_custom_button_text' => 'View Demo',
	    '_learndash_course_grid_custom_ribbon_text' => 'Course Demo',
	    'slide_template' => 'default',
	    'course_price_billing_p3' => '',
	    'course_price_billing_t3' => 'D',
	    'course_sections' => [],
	    'ld_course_steps' => '',
	    '_wc_memberships_force_public' => 'no',
	    '_wc_memberships_use_custom_content_restricted_message' => 'no',

	    '_ld_custom_meta' => array(
	      'duration' => '',
	      'content_type' => 'fas fa-play-circle',
	      'embed_code' => '',
	      'short_desc' => $course_short_name,
	      'level' => ($course_type == 'paid' || $course_type == 'promotional' ? 'All Levels' : 'Beginner'),
	      'language' => 'English',
	      'enrolled' => 200,
	      'lessons' => 0,
	      'related_product' => (!empty($product_pid) && ($course_type == 'paid' || $course_type == 'promotional') ? $product_pid : ''),
	    ),

	    '_sfwd-courses' => array(
		  'sfwd-courses_course_price_type' => (($course_type == 'free' || $course_type == 'promotional') ? 'free' : 'closed'),
		  'sfwd-courses_course_price' => (($course_type == 'free' || $course_type == 'promotional') ? '' : 29),
		  'sfwd-courses_certificate' => 26549,
		  'sfwd-courses_course_materials_enabled' => '',
		  'sfwd-courses_course_materials' => '',
		  'sfwd-courses_course_disable_content_table' => '',
		  'sfwd-courses_course_lesson_per_page' => '',
		  'sfwd-courses_course_lesson_per_page_custom' => '',
		  'sfwd-courses_course_topic_per_page_custom' => '',
		  'sfwd-courses_course_lesson_order_enabled' => '',
		  'sfwd-courses_course_lesson_orderby' => '',
		  'sfwd-courses_course_lesson_order' => '',
		  'sfwd-courses_course_prerequisite_enabled' => '',
		  'sfwd-courses_course_prerequisite' => '',
		  'sfwd-courses_course_prerequisite_compare' => 'ANY',
		  'sfwd-courses_course_points_enabled' => '',
		  'sfwd-courses_course_points' => '',
		  'sfwd-courses_course_points_access' => '',
		  'sfwd-courses_expire_access' => '',
		  'sfwd-courses_expire_access_days' => 0,
		  'sfwd-courses_expire_access_delete_progress' => '',
		  'sfwd-courses_custom_button_url' => (($course_type == 'free' || $course_type == 'promotional') ? '' : '/plan-and-pricing/'),
		  'sfwd-courses_course_price_billing_p3' => '',
		  'sfwd-courses_course_price_billing_t3' => '',
		  'sfwd-courses_course_disable_lesson_progression' => '',
	    ),

	  ),
	);

	$course_pid = wp_insert_post( $course_data );
	if ( is_wp_error( $course_pid ) ) {
	  echo 'ERROR: ' . $course_pid->get_error_message();
	  continue;
	}
	else {
	  $course = get_post( $course_pid );
	}

	if(!$course) continue;

	if(isset($product->ID) && !empty($product->ID)) {
	  $related_course = array();
	  $related_course[] = $course->ID;
	  update_post_meta( $product->ID, '_related_course', $related_course);
	}

	//add the nid into migrate mapping table
    $sql5 = "INSERT INTO wp_migrate_nodes_mapping (nid, learndash_pid, woo_pid, is_migrated) VALUES ($nid, $course_pid, $product_pid, 1) ON DUPLICATE KEY UPDATE learndash_pid = VALUES(learndash_pid), woo_pid = VALUES(woo_pid), is_migrated = 1";
    $wpdb->query($sql5);

	//flag drupal node record
    $sql6 = "UPDATE node SET is_migrated=1 WHERE nid = ".$nid;
    $migratedb->query($sql6);
  }
}



function msg_upload_media_image($migrate_file_path, $filename, $uri) {
  //write_log('Location: msg_upload_media_image');

  $filename = str_ireplace("/", '_', $filename);
  $filename = str_ireplace(" ", '_', $filename);

  $uri = str_ireplace('public://','',$uri);
  $image_url = $migrate_file_path . '/sites/default/files/'.$uri;
  $image_data = file_get_contents( $image_url );
  //write_log('image_url: '.$image_url);

  $upload_dir = wp_upload_dir();
  if ( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
  }
  else {
    $file = $upload_dir['basedir'] . '/' . $filename;
  }

  file_put_contents( $file, $image_data );
  $wp_filetype = wp_check_filetype( $filename, null );

  $attachment = array(
    'guid' => $file,
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => sanitize_file_name( $filename ),
    'post_content' => '',
    'post_status' => 'inherit'
  );

  $attach_id = wp_insert_attachment( $attachment, $file );
  if (!is_wp_error($attach_id)) {
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
  }

  return $attach_id;
}


/**
  * Course category mapping
  */
function msg_get_course_category($category_id = 0) {
  //write_log('Location: msg_get_course_category');
  $categories = array(
    97 => array('term_id' => 137, 'name' => 'Management Basics', 'slug' => 'management-basics'),
    96 => array('term_id' => 138, 'name' => 'Organizational Behavior', 'slug' => 'organizational-behavior'),
    94 => array('term_id' => 139, 'name' => 'People Management', 'slug' => 'people-management'),
    95 => array('term_id' => 140, 'name' => 'Marketing', 'slug' => 'marketing'),
    98 => array('term_id' => 141, 'name' => 'Operations', 'slug' => 'operations'),
    99 => array('term_id' => 142, 'name' => 'Finance', 'slug' => 'finance'),
    93 => array('term_id' => 143, 'name' => 'Skills Development', 'slug' => 'skills-development'),
    101 => array('term_id' => 144, 'name' => 'MSG Videos', 'slug' => 'msg-videos'),
  );

  if(isset($categories[$category_id])) {
    return $categories[$category_id];
  }
  else {
    return array();
  }
}

/**
  * Product category mapping
  */
function msg_get_product_category($category_id = 0) {
  //write_log('Location: msg_get_product_category');
  $categories = array(
    97 => array('term_id' => 327, 'name' => 'Management Basics', 'slug' => 'management-basics'),
    96 => array('term_id' => 331, 'name' => 'Organizational Behavior', 'slug' => 'organizational-behavior'),
    94 => array('term_id' => 332, 'name' => 'People Management', 'slug' => 'people-management'),
    95 => array('term_id' => 328, 'name' => 'Marketing', 'slug' => 'marketing'),
    98 => array('term_id' => 330, 'name' => 'Operations', 'slug' => 'operations'),
    99 => array('term_id' => 116, 'name' => 'Finance', 'slug' => 'finance'),
    93 => array('term_id' => 333, 'name' => 'Skills Development', 'slug' => 'skills-development'),
    101 => array('term_id' => 329, 'name' => 'MSG Videos', 'slug' => 'msg-videos'),
  );

  if(isset($categories[$category_id])) {
    return $categories[$category_id];
  }
  else {
    return array();
  }
}


/**
  * User Memberships
  */
function migrate_users_memberships($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_users_memberships');

  $sql  = 'SELECT a.id, a.uid, a.plan_id, a.plan_vid, a.course_opted, a.first_purchased_date, a.expired_at, ';
  $sql .= 'c.name as plan_name, c.price, c.number_of_courses, c.download_lessons, ';
  $sql .= 'c.duration, c.msg_videos ';
  $sql .= 'FROM msg_user_plan a ';
  $sql .= "INNER JOIN system_user_plans b ON a.plan_id = b.plan_id ";
  $sql .= "INNER JOIN system_user_plans_revision c ON a.plan_vid = c.plan_vid ";
  $sql .= "WHERE a.is_expired = 0 ";
  $sql .= "AND ((a.expired_at IS NULL OR a.expired_at = '') OR (DATE(a.expired_at) > CURDATE())) ";
  $sql .= "AND a.is_migrated = 0 ";
  $sql .= "ORDER BY a.uid ASC ";


  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_users_mapping a ';
	$sql0 .= 'WHERE a.uid = '.$row->uid." ";
	$sql0 .= 'AND a.is_plan_migrated = 0 ';
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	//get migrated uid
	$wp_uid = 0;
	if(isset($results0->uid) && $results0->uid > 0) {
	  $wp_uid = $results0->new_uid;
	}

	//if NOT found, continue;
	if(empty($wp_uid)) continue;

	$uid = $row->uid;
	$plan_id = $row->plan_id;
	$plan_vid = $row->plan_vid;

	$title = !empty($row->plan_name) ? $row->plan_name : '';
	$price = !empty($row->price) ? $row->price : 0;
	$course_opted = !empty($row->course_opted) ? $row->course_opted : 'all';
	$number_of_courses = !empty($row->number_of_courses) ? $row->number_of_courses : '';
	$duration = $row->duration;
	$download_lessons = $row->download_lessons;
	$msg_videos = $row->msg_videos;
	$created = strtotime($row->first_purchased_date);
	$start_date = !empty($row->first_purchased_date) ? date('Y-m-d H:i:s', strtotime($row->first_purchased_date)) : '';
	$expire_date = !empty($row->expired_at) ? date('Y-m-d H:i:s', strtotime($row->expired_at)) : '';

	if($duration == 0 && $number_of_courses == 'all') {
	  $wc_membership_plan = 27063;  //Ultimate
	}
	else if($duration > 0 && $number_of_courses == 'all') {
	  $wc_membership_plan = 27062;  //Startup
	}
	else {
	  $wc_membership_plan = 27061;  //Basic
	}

	$membership_buy_course = get_post_meta($wc_membership_plan, 'ld_membership_buy_course', true);
	if(empty($membership_buy_course)) $membership_buy_course = 0;

    $post_name = strtolower($title);
    $post_name = trim($post_name);
    $post_name = str_ireplace(' ','-', $post_name);
    $post_name = $post_name . '-'. $plan_id . '-' . $plan_vid;


	//create user membership plan
	$user_membership_plan_data = array(
      'post_title'    => $title,
      'post_content'  => '',
      'post_excerpt'  => '',
      'post_status'   => 'wcm-active',
      'post_author'   => $wp_uid,
      'post_type'	  => 'wc_user_membership',
      'post_date' 	  => date( 'Y-m-d H:i:s', $created ),
      'post_date_gmt' => date( 'Y-m-d H:i:s', $created ),
      'post_modified' => date( 'Y-m-d H:i:s', $created ),
      'post_modified_gmt' => date( 'Y-m-d H:i:s', $created ),
      'post_name'     => $post_name,
      'comment_status'=> 'closed',
      'comment_count' => 0,
      'ping_status'   => 'closed',
      'post_parent'   => $wc_membership_plan,
      'filter' 	      => 'raw',
	  'meta_input'   => array(
	    '_product_id' => 27179,					//Woocomerce subscription product
	    '_order_id' => '',
	    '_start_date' => $start_date,
	    '_end_date' => $expire_date,
	    'user_membership_course_limit' => ($number_of_courses == 'all' ? 0 : intval($number_of_courses)),
	    'user_membership_buy_course' => $membership_buy_course,
	    'user_membership_lesson_downloads' => $download_lessons,
	    'user_membership_watch_video' => $msg_videos,
	    '_subscription_id' => '',
	    '_has_installment_plan' => '',
	  ),
	);


	$user_membership_plan_pid = wp_insert_post( $user_membership_plan_data );
	if ( is_wp_error( $user_membership_plan_pid ) ) {
	  echo 'ERROR: ' . $user_membership_plan_pid->get_error_message();
	  continue;
	}
	else {
	  $user_membership_plan = get_post( $user_membership_plan_pid );
	}

	if(!$user_membership_plan) continue;

	//add the membership_plan_id into migrate mapping table
    $sql5 = "UPDATE wp_migrate_users_mapping SET membership_plan_id = $user_membership_plan_pid, is_plan_migrated = 1 WHERE uid = $uid ";
    $wpdb->query($sql5);

	//flag drupal node record
    $sql6 = "UPDATE msg_user_plan SET is_migrated=1 WHERE id = ".$row->id;
    $migratedb->query($sql6);
  }

}


/**
  * User Memberships update
  */
function migrate_users_memberships_update($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_users_memberships_update');

  $sql  = 'SELECT a.id, a.uid, a.plan_id, a.plan_vid, a.course_opted, a.first_purchased_date, a.expired_at, ';
  $sql .= 'c.name as plan_name, c.price, c.number_of_courses, c.download_lessons, ';
  $sql .= 'c.duration, c.msg_videos ';
  $sql .= 'FROM msg_user_plan a ';
  $sql .= "INNER JOIN system_user_plans b ON a.plan_id = b.plan_id ";
  $sql .= "INNER JOIN system_user_plans_revision c ON a.plan_vid = c.plan_vid ";
  $sql .= "WHERE a.is_expired = 0 ";
  $sql .= "AND ((a.expired_at IS NULL OR a.expired_at = '') OR (DATE(a.expired_at) > CURDATE())) ";
  $sql .= "AND a.is_migrated = 1 ";
  $sql .= "ORDER BY a.uid ASC ";


  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_users_mapping a ';
	$sql0 .= 'WHERE a.uid = '.$row->uid." ";
	$sql0 .= 'AND a.is_plan_migrated = 1 ';
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	//get migrated uid
	$wp_uid = 0;
	if(isset($results0->uid) && $results0->uid > 0) {
	  $wp_uid = $results0->new_uid;
	}

	//if NOT found, continue;
	if(empty($wp_uid)) continue;

	$migrated_membership_plan_id = 0;
	if(isset($results0->membership_plan_id) && $results0->membership_plan_id > 0) {
	  $migrated_membership_plan_id = $results0->membership_plan_id;
	}

	//if NOT found, continue;
	if(empty($migrated_membership_plan_id)) continue;

	$uid = $row->uid;
	$plan_id = $row->plan_id;
	$plan_vid = $row->plan_vid;

	$title = !empty($row->plan_name) ? $row->plan_name : '';
	$price = !empty($row->price) ? $row->price : 0;
	$course_opted = !empty($row->course_opted) ? $row->course_opted : 'all';
	$number_of_courses = !empty($row->number_of_courses) ? $row->number_of_courses : '';
	$duration = $row->duration;
	$download_lessons = $row->download_lessons;
	$msg_videos = $row->msg_videos;
	$created = strtotime($row->first_purchased_date);
	$start_date = !empty($row->first_purchased_date) ? date('Y-m-d H:i:s', strtotime($row->first_purchased_date)) : '';
	$expire_date = !empty($row->expired_at) ? date('Y-m-d H:i:s', strtotime($row->expired_at)) : '';

	if($duration == 0 && $number_of_courses == 'all') {
	  $wc_membership_plan = 27063;  //Ultimate
	}
	else if($duration > 0 && $number_of_courses == 'all') {
	  $wc_membership_plan = 27062;  //Startup
	}
	else {
	  $wc_membership_plan = 27061;  //Basic
	}

	$membership_buy_course = get_post_meta($wc_membership_plan, 'ld_membership_buy_course', true);
	if(empty($membership_buy_course)) $membership_buy_course = 0;

    update_post_meta( $migrated_membership_plan_id, '_product_id', 27179);
    update_post_meta( $migrated_membership_plan_id, '_order_id', '');
    update_post_meta( $migrated_membership_plan_id, '_start_date', $start_date);
    update_post_meta( $migrated_membership_plan_id, '_end_date', $expire_date);
    update_post_meta( $migrated_membership_plan_id, 'user_membership_course_limit', ($number_of_courses == 'all' ? 0 : intval($number_of_courses)) );
    update_post_meta( $migrated_membership_plan_id, 'user_membership_buy_course', $membership_buy_course);
    update_post_meta( $migrated_membership_plan_id, 'user_membership_lesson_downloads', $download_lessons);
    update_post_meta( $migrated_membership_plan_id, 'user_membership_watch_video', $msg_videos);
    update_post_meta( $migrated_membership_plan_id, '_subscription_id', '');
    update_post_meta( $migrated_membership_plan_id, '_has_installment_plan', '');
  }

  //update wordpress users who has uid: 1 to 26
  $sql  = 'SELECT * FROM wp_users ';
  $sql .= "WHERE ID >= 1 ";
  $sql .= "AND ID <= 26 ";

  $results = $wpdb->get_results($sql);

  foreach($results as $row) {
    $membership = user_current_membership( $row->ID );

    if($membership != false) {
      $membership_id = $membership->id;
      $membership_plan_id = $membership->plan_id;

      $membership_plan = $membership->plan;
      $membership_post = $membership->post;

      if($membership_plan_id == 27063) {  //Ultimate
	    $user_membership_course_limit = 0;
	    $user_membership_buy_course = 1;
	    $user_membership_lesson_downloads = 1;
	    $user_membership_watch_video = 1;
      }
      else if($membership_plan_id == 27062) {  //Startup
	    $user_membership_course_limit = 0;
	    $user_membership_buy_course = 1;
	    $user_membership_lesson_downloads = 1;
	    $user_membership_watch_video = 1;
      }
      else if($membership_plan_id == 27061) {  //Basic
	    $user_membership_course_limit = 5;
	    $user_membership_buy_course = 1;
	    $user_membership_lesson_downloads = 1;
	    $user_membership_watch_video = 1;
      }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', '_product_id')) {
    	update_post_meta( $membership_id, '_product_id', 27179);
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', '_order_id')) {
    	update_post_meta( $membership_id, '_order_id', '');
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', 'user_membership_course_limit')) {
    	update_post_meta( $migrated_membership_plan_id, 'user_membership_course_limit', $user_membership_course_limit);
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', 'user_membership_buy_course')) {
    	update_post_meta( $membership_id, 'user_membership_buy_course', $user_membership_buy_course);
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', 'user_membership_lesson_downloads')) {
    	update_post_meta( $membership_id, 'user_membership_lesson_downloads', $user_membership_lesson_downloads);
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', 'user_membership_watch_video')) {
    	update_post_meta( $membership_id, 'user_membership_watch_video', $user_membership_watch_video);
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', '_subscription_id')) {
    	update_post_meta( $membership_id, '_subscription_id', '');
	  }

	  if ( !check_meta_data_exists($membership_id, 'wc_user_membership', '_has_installment_plan')) {
    	update_post_meta( $membership_id, '_has_installment_plan', '');
	  }
    }
  }

}


/**
  * Users Course Enrollments
  */
function migrate_users_course_enrollments($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_users_course_enrollments');

  //migrate user enrolled courses
  $sql  = 'SELECT a.id, a.uid, a.course_id, enrollment_date ';
  $sql .= 'FROM msg_user_courses_as_per_plan a ';
  $sql .= "WHERE a.is_migrated = 0 ";
  $sql .= "LIMIT 25000 ";

  $results = $migratedb->get_results($sql);
  $processed_users = array();

  foreach($results as $row) {
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_users_mapping a ';
	$sql0 .= 'WHERE a.uid = '.$row->uid." ";
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	//old user
	$uid = $row->uid;

	//get migrated uid
	$wp_uid = 0;
	if(isset($results0->uid) && $results0->uid > 0) {
	  $wp_uid = $results0->new_uid;
	}

	//if NOT found, continue;
	if(empty($wp_uid)) continue;

	$sql1  = 'SELECT * ';
	$sql1 .= 'FROM wp_migrate_nodes_mapping a ';
	$sql1 .= 'WHERE a.nid = '.$row->course_id." ";
	$sql1 .= 'LIMIT 1 ';
	$results1 = $wpdb->get_results($sql1);
	if($results1) $results1 = reset($results1);

	//get migrated post id
	$learndash_pid = 0;
	if(isset($results1->learndash_pid) && $results1->learndash_pid > 0) {
	  $learndash_pid = $results1->learndash_pid;
	}

	//if NOT found, continue;
	if(empty($learndash_pid)) continue;

	//get existing enrolled courses
	$user_courses = learndash_user_get_enrolled_courses($wp_uid);

	//add the new one
	$user_courses[] = $learndash_pid;

	//save
	learndash_user_set_enrolled_courses( $wp_uid, $user_courses);

	//update enrollment date
	$enrollment_date = strtotime($row->enrollment_date);
	$course_meta_key = 'course_'.$learndash_pid.'_access_from';
	update_user_meta($wp_uid, $course_meta_key, $enrollment_date);

	//flag drupal node record
    $sql6 = "UPDATE msg_user_courses_as_per_plan SET is_migrated=1 WHERE id = ".$row->id;
    $migratedb->query($sql6);

    $sql5 = "UPDATE wp_migrate_users_mapping SET is_enrollment_migrated = is_enrollment_migrated + 1 WHERE uid = $uid ";
    $wpdb->query($sql5);
  }
}


function msg_migrate_bb_messages_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_bb_messages_action');
}
add_action( 'bp_ready', 'msg_migrate_bb_messages_action', 10);

/**
  * User private messages
  */
function migrate_user_private_messages($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_user_private_messages');

  $sql  = 'SELECT mid, author, subject, body, timestamp ';
  $sql .= 'FROM pm_message ';
  $sql .= 'WHERE is_migrated = 0 ';
  $sql .= "ORDER BY mid ASC ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$message_id = $row->mid;
	$user_id = $row->author;
	$subject = !empty($row->subject) ? $row->subject : '';
	$body = !empty($row->body) ? $row->body : '';
	$timestamp = $row->timestamp;

	$wp_uid = msg_get_mapped_user_id($user_id);
	if(empty($wp_uid)) continue;

    $sql1 = 'SELECT mid, thread_id, recipient, is_new, deleted ';
    $sql1 .= 'FROM pm_index ';
    $sql1 .= "WHERE type = 'user' ";
    $sql1 .= "AND mid = ".$row->mid." ";
    $sql1 .= "ORDER BY mid ASC ";

	$recipients = array();
    $results1 = $migratedb->get_results($sql1);
    foreach($results1 as $row1) {
	  $wpr_uid = msg_get_mapped_user_id($row1->recipient);
	  if(empty($wpr_uid)) continue;

	  $recipients[] = $wpr_uid;
    }

	$args = array(
		'sender_id'  => $wp_uid,
		'thread_id'  => false,
		'recipients' => $recipients,
		'subject'    => $subject,
		'content'    => $body,
		'date_sent'  => date('Y-m-d H:i:s', $timestamp),
	);
	messages_new_message($args);


	//flag drupal node record
    $sql6 = "UPDATE pm_message SET is_migrated=1 WHERE mid = ".$row->mid;
    $migratedb->query($sql6);
  }

}
add_action( 'msg_migrate_bb_messages_action', 'migrate_user_private_messages', 10, 2);



function msg_migrate_bb_groups_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_bb_groups_action');
}
add_action( 'bp_ready', 'msg_migrate_bb_groups_action', 10);

/**
  * User group and group wall post
  */
function migrate_user_groups_and_wall_post($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_user_groups_and_wall_post');

  $sql  = 'SELECT group_id, group_name, uid, is_private ';
  $sql .= 'FROM msg_user_groups ';
  $sql .= 'WHERE is_migrated = 0 ';
  $sql .= "ORDER BY group_id ASC ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$id = $row->group_id;
	$user_id = $row->uid;
	$name = !empty($row->group_name) ? $row->group_name : '';
	$status = $row->is_private == 0 ? 'public' : 'private';
	$date_created = date('Y-m-d H:i;s');
	$enable_forum = 0;
	$parent_id = 0;
	$slug = strtolower(str_ireplace(' ','-',$name));

	$wp_uid = msg_get_mapped_user_id($user_id);
	if(empty($wp_uid)) continue;

	$args = array(
	  'group_id'     => $id,
	  'creator_id'   => $wp_uid,
	  'name'         => $name,
	  'description'  => '',
	  'slug'         => $slug,
	  'status'       => $status,
	  'parent_id'    => $parent_id,
	  'enable_forum' => $enable_forum,
	  'date_created' => $date_created
	);

    $group_id = groups_create_group($args);

    if($group_id) {
      $sql1 = 'SELECT group_id, member_uid ';
      $sql1 .= 'FROM msg_user_group_members ';
      $sql1 .= "WHERE group_id = ".$row->group_id." ";
      $sql1 .= "ORDER BY group_id ASC ";

      $results1 = $migratedb->get_results($sql1);
      foreach($results1 as $row1) {
	    $wp_member_uid = msg_get_mapped_user_id($row1->member_uid);
	    if(empty($wp_member_uid)) continue;

  	    groups_join_group( $group_id, $wp_member_uid );
	    if($row1->member_uid == $row->uid) {
  	      groups_promote_member( $wp_member_uid, $group_id, 'admin' );
  	    }
  	    //groups_ban_member( $user_id, $wp_member_uid );
      }

      groups_update_last_activity( $group_id);
      $forum_id = groups_get_groupmeta( $group_id, 'forum_id', true );

      //if forum is created, add the group wall post
      if($forum_id) {
        $sql2 = 'SELECT post_id, uid, status, created_at ';
        $sql2 .= 'FROM msg_user_group_wall_post ';
        $sql2 .= "WHERE group_id = ".$row->group_id." ";
        $sql2 .= "AND uid > 0 ";
        $sql2 .= "AND uid != 75522 ";
        $sql2 .= "AND status NOT LIKE 'Lorem Ipsum%' ";
        $sql2 .= "AND status != '' ";
        $sql2 .= "ORDER BY post_id ASC ";

        $results2 = $migratedb->get_results($sql2);
        foreach($results2 as $row2) {
	      $wp_topic_uid = msg_get_mapped_user_id($row2->uid);
	      if(empty($wp_topic_uid)) continue;

		  $post_title = substr($row2->status,0,255);
		  $post_content = (strlen($row2->status) > 255 ? $row2->status : '');

		  $topic_data = array(
		    'post_parent'    => $forum_id, // forum ID
			'post_status'    => 'publish',
			'post_type'      => 'topic',
			'post_author'    => $wp_topic_uid,
			'post_password'  => '',
			'post_content'   => $post_content,
			'post_title'     => $post_title,
			'comment_status' => 'open',
			'menu_order'     => 0
		  );

		  $topic_meta = array(
		    'forum_id'           => $forum_id,
		    'voice_count'        => 1,
		    'reply_count'        => 0,
		    'reply_count_hidden' => 0,
		    'last_reply_id'      => 0,
		    'last_active_time'   => date('Y-m-d H:i:s', strtotime($row2->created_at))
		  );
		  $topic_id = bbp_insert_topic( $topic_data, $topic_meta);

		  if($topic_id) {
		    $sql3 = 'SELECT reply_id, post_id, uid, comment, created_at ';
			$sql3 .= 'FROM msg_user_group_wall_post_reply ';
			$sql3 .= "WHERE group_id = ".$row2->post_id." ";
			$sql3 .= "AND uid > 0 ";
			$sql3 .= "AND comment != '' ";
			$sql3 .= "ORDER BY reply_id ASC ";

			$results3 = $migratedb->get_results($sql3);
			foreach($results3 as $row3) {
			  $wp_reply_uid = msg_get_mapped_user_id($row3->uid);
			  if(empty($wp_reply_uid)) continue;

			  $post_title = substr($row3->comment,0,255);
			  $post_content = (strlen($row3->comment) > 255 ? $row3->comment : '');

		      $reply_data = array(
			    'post_parent'    => $topic_id, // topic ID
			    'post_type'      => 'reply',
			    'post_author'    => $wp_reply_uid,
			    'post_password'  => '',
			    'post_content'   => $post_content,
			    'post_title'     => $post_title,
			    'menu_order'     => bbp_get_topic_reply_count( $topic_id, true ) + 1,
			    'comment_status' => 'open'
		      );

		      $reply_meta = array(
			    'forum_id'  => $forum_id,
			    'topic_id'  => $topic_id,
		      );

		      $reply_id = bbp_insert_reply( $reply_data, $reply_meta);
		      if($reply_id) {
			    bbp_approve_reply($reply_id);
			  }

			} //foreach-$results3
		  }  //if($topic_id)

		} //foreach-results2
      } //if($forum_id)

	}

	//flag drupal node record
    $sql6 = "UPDATE msg_user_groups SET is_migrated=1 WHERE group_id = ".$row->group_id;
    $migratedb->query($sql6);

  }

}
add_action( 'msg_migrate_bb_groups_action', 'migrate_user_groups_and_wall_post', 10, 2);


function msg_migrate_bb_friends_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_bb_friends_action');
}
add_action( 'bp_ready', 'msg_migrate_bb_friends_action', 10);

/**
  * User friends
  */
function migrate_user_friends($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_user_friends');

  $sql  = 'SELECT id, uid, friend_uid ';
  $sql .= 'FROM msg_user_friends_request_queue ';
  $sql .= 'WHERE is_migrated = 0 ';
  $sql .= 'AND is_discard = 0 ';
  $sql .= 'AND uid > 0 ';
  $sql .= "ORDER BY uid ASC ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$wp_uid = msg_get_mapped_user_id($row->uid);
	if(empty($wp_uid)) continue;

	$wp_friend_uid = msg_get_mapped_user_id($row->friend_uid);
	if(empty($wp_friend_uid)) continue;

	$created_at = $row->created_at;
	if(empty($created_at)) continue;

    if(!friends_check_friendship( $wp_uid, $wp_friend_uid )) {
	  friends_add_friend( $wp_uid, $wp_friend_uid, true );

	  //update the created date
      $sql2 = "UPDATE wp_bp_friends SET date_created='".$created_at."' WHERE initiator_user_id = ".$wp_uid." AND friend_user_id=".$wp_friend_uid;
      $wpdb->query($sql2);
	}

	//flag drupal node record
    $sql6 = "UPDATE msg_user_friends_request_queue SET is_migrated=1 WHERE id = ".$row->id;
    $migratedb->query($sql6);
  }


  //update last activity, to view the users as member
  $members =  get_users( 'fields=ID' );
  // $members =  get_users( 'fields=ID&role=subscriber' );
  foreach ( $members as $user_id ) {
    bp_update_user_last_activity( $user_id, bp_core_current_time() );
  }
}
add_action( 'msg_migrate_bb_friends_action', 'migrate_user_friends', 10, 2);



/**
  * Update User friends createtime
  */
function migrate_user_friends_update_createtime($migratedb, $migrate_file_path) {
  global $wpdb;

  $sql  = 'SELECT id, uid, friend_uid, created_at ';
  $sql .= 'FROM msg_user_friends_request_queue ';
  $sql .= 'WHERE uid > 0 ';
  $sql .= 'AND is_discard = 0 ';
  $sql .= "ORDER BY uid ASC ";

  $results = $migratedb->get_results($sql);

  $response = array();

  foreach($results as $row) {
    $r = array('uid' => $row->uid, 'friend_uid' => $row->friend_uid);

	$wp_uid = msg_get_mapped_user_id($row->uid);
	if(empty($wp_uid)) continue;

	$wp_friend_uid = msg_get_mapped_user_id($row->friend_uid);
	if(empty($wp_friend_uid)) continue;

	$created_at = $row->created_at;
	if(empty($created_at)) continue;

    $r['wp_uid'] = $wp_uid;
    $r['wp_friend_uid'] = $wp_friend_uid;
    $r['created_at'] = $created_at;
    $response[] = $r;

    $sql2 = "UPDATE wp_bp_friends SET date_created='".$created_at."' WHERE initiator_user_id = ".$wp_uid." AND friend_user_id=".$wp_friend_uid;
    $wpdb->query($sql2);
  }
  //write_log($response);
}




function msg_migrate_bb_wall_posts_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_bb_wall_posts_action');
}
add_action( 'bp_ready', 'msg_migrate_bb_wall_posts_action', 10);

/**
  * User wall posts
  */
function migrate_user_wall_posts($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_user_wall_posts');

  $sql  = 'SELECT post_id, uid, post_to_uid, status, created_at ';
  $sql .= 'FROM msg_user_wall_post ';
  $sql .= "WHERE uid > 0 ";
  $sql .= "AND status != '' ";
  $sql .= "AND status NOT LIKE 'Lorem Ipsum%' ";
  $sql .= "AND is_migrated = 0 ";
  $sql .= "ORDER BY post_id ASC ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$wp_uid = msg_get_mapped_user_id($row->uid);
	if(empty($wp_uid)) continue;

	$wp_mentionname_uid = msg_get_mapped_user_id($row->post_to_uid);
	if(!empty($row->post_to_uid) && empty($wp_mentionname_uid)) continue;
	$mentionname = '';
	if(!empty($wp_mentionname_uid)) {
	  $mentionname = bp_activity_get_user_mentionname( $wp_mentionname_uid );
	}
	$mentionname = '@'.$mentionname;
	$content = $mentionname.' '.$row->status;

	$user = get_user_by( 'id', $wp_uid );
	$name = $mentionname;
	if($user) {
	  $name = $user->first_name . ' ' . $user->last_name;
	}

	$args = array(
		'action'            => $name.' posted an update',
		'content'           => $content,
		'component'         => 'activity',
		'type'              => 'activity_update',
		'primary_link'      => '',
		'user_id'           => $wp_uid,
		'item_id'           => false,
		'secondary_item_id' => false,
		'recorded_time'     => date('Y-m-d H:i:s', strtotime($row->created_at)),
		'hide_sitewide'     => false,
		'is_spam'           => false,
	);
    $activity_id = bp_activity_add( $args);

    if($activity_id) {
      //like
	  $sql2 = 'SELECT uid, post_id, is_like ';
	  $sql2 .= 'FROM msg_user_wall_post_like ';
	  $sql2 .= "WHERE uid > 0 ";
	  $sql2 .= "AND post_id = ".$row->post_id." ";
	  $sql2 .= "AND is_like = 1 ";
	  $sql2 .= "ORDER BY uid ASC ";

	  $results2 = $migratedb->get_results($sql2);

	  foreach($results2 as $row2) {
		$wp_like_uid = msg_get_mapped_user_id($row2->uid);
		if(empty($wp_like_uid)) continue;

		bp_activity_add_user_favorite( $activity_id, $wp_like_uid);
	  } //foreach-$results2

	  //reply
	  $sql3  = 'SELECT reply_id, post_id, uid, comment, created_at ';
	  $sql3 .= 'FROM msg_user_wall_post_reply ';
	  $sql3 .= "WHERE uid > 0 ";
	  $sql3 .= "AND post_id = ".$row->post_id." ";
	  $sql3 .= "AND comment != '' ";
	  $sql3 .= "AND comment NOT LIKE 'Lorem Ipsum%' ";
	  $sql3 .= "ORDER BY reply_id ASC ";

	  $results3 = $migratedb->get_results($sql3);

	  foreach($results3 as $row3) {
		$wp_reply_uid = msg_get_mapped_user_id($row3->uid);
		if(empty($wp_reply_uid)) continue;

		$args = array(
		  'content'           => $row3->comment,
		  'user_id'           => $wp_reply_uid,
		  'activity_id'       => $activity_id,
		  'parent_id'         => $activity_id,
		  'primary_link'      => '',
		  'skip_notification' => true
		);
		$reply_id = bp_activity_new_comment( $args );
		if($reply_id) {

		  //like
		  $sql4 = 'SELECT uid, reply_id, is_like ';
		  $sql4 .= 'FROM msg_user_wall_post_reply_like ';
		  $sql4 .= "WHERE uid > 0 ";
		  $sql4 .= "AND reply_id = ".$row3->reply_id." ";
		  $sql4 .= "AND is_like = 1 ";
		  $sql4 .= "ORDER BY uid ASC ";

		  $results4 = $migratedb->get_results($sql4);

		  foreach($results4 as $row4) {
			$wp_reply_like_uid = msg_get_mapped_user_id($row4->uid);
			if(empty($wp_reply_like_uid)) continue;

			bp_activity_add_user_favorite( $reply_id, $wp_reply_like_uid);
		  } //foreach-$results4
		} //if($reply_id)

	  }//foreach-$results3

    } //if($activity_id)

	//flag drupal node record
    $sql6 = "UPDATE msg_user_wall_post SET is_migrated=1 WHERE post_id = ".$row->post_id;
    $migratedb->query($sql6);

  }

}
add_action( 'migrate_user_wall_posts', 'migrate_user_wall_posts', 10, 2);


function msg_migrate_woocommerce_orders_action() {
  //nothing to do here
  //write_log('Location: msg_migrate_woocommerce_orders_action');
}
add_action( 'woocommerce_checkout_process', 'msg_migrate_woocommerce_orders_action', 10);

/**
  * Transaction Order
  */
function migrate_user_transaction_orders($migratedb, $migrate_file_path) {
  global $wpdb;
  //write_log('Location: migrate_user_transaction_orders');

  $sql  = 'SELECT a.txn_id, a.uid, a.plan_id, a.plan_vid, a.order_number, a.invoice_id, a.plan_name, a.plan_price, ';
  $sql .= 'a.pay_method, a.currency_code, a.created_at,a.action,a.credit_card_processed,a.txn_keys, ';
  $sql .= 'a.first_name, a.last_name, a.country, a.state, a.city, a.zip_code, a.street_address, a.street_address2, ';
  $sql .= 'a.ip_country, a.card_holder_name, b.name as plan_name_original, ';
  $sql .= 'b.number_of_courses, b.duration ';
  $sql .= 'FROM msg_user_2checkout_transactions a ';
  $sql .= "INNER JOIN system_user_plans_revision b ON a.plan_id = b.plan_id AND a.plan_vid = b.plan_vid ";
  $sql .= "WHERE a.txn_id > 0 ";
  $sql .= "AND a.is_migrated = 0 ";
  $sql .= "ORDER BY a.txn_id ASC ";

  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$wp_uid = msg_get_mapped_user_id($row->uid);
	if(empty($wp_uid)) continue;

    $wp_user = get_user_by('id', $wp_uid );

    $address = array(
	  'first_name' => !empty($row->first_name) ? $row->first_name : '',
	  'last_name'  => !empty($row->last_name) ? $row->last_name : '',
	  'company'    => '',
	  'email'      => $wp_user->user_email,
	  'phone'      => '',
	  'address_1'  => !empty($row->street_address) ? $row->street_address : '',
	  'address_2'  => !empty($row->street_address2) ? $row->street_address2 : '',
	  'city'       => !empty($row->city) ? $row->city : '',
	  'state'      => !empty($row->state) ? $row->state : '',
	  'postcode'   => !empty($row->zip_code) ? $row->zip_code : '',
	  'country'    => msg_get_country($row->country),
    );

    $args = array(
      'customer_id'   => $wp_uid,
      'created_via'   => 'Migrate',
      'add_order_note' => 'Created via migration from previous system. Previous order number was '.$row->order_number.' and invoice number was '.$row->invoice_id
    );

    // create the order
    $order = wc_create_order($args);

    $order->set_address( $address, 'billing' );
	$order->set_currency( 'USD' );
	$order->set_payment_method( 'twocheckout');
	$order->set_customer_note( (!empty($row->action) ? $row->action : '') );
	$order->set_date_created( date('Y-m-d H:i:s', strtotime($row->created_at)) );
	$order->set_date_completed( date('Y-m-d H:i:s', strtotime($row->created_at)) );

    $order_id = $order->get_id();

	$payment_method_title = '';
    if(!empty($row->pay_method) && $row->pay_method == 'CC') {
	  $payment_method_title = 'Credit Card';
    }
    else if(!empty($row->pay_method) && $row->pay_method == 'Visa/MasterCard') {
	  $payment_method_title = 'Credit Card';
    }
    else if(!empty($row->pay_method) && $row->pay_method == 'PPI') {
	  $payment_method_title = 'PPI';
    }
    else if(!empty($row->pay_method) && $row->pay_method == 'AL') {
	  $payment_method_title = 'AL';
    }

	//subscription plans
	$subscription_product_id = 27175;
	if($row->duration == 0 && $row->number_of_courses == 'all') {
	  $subscription_product_variation_id = 27181;  //Ultimate
	  $subscription_product_variation_name = 'Ultimate';
	}
	else if($row->duration > 0 && $row->number_of_courses == 'all') {
	  $subscription_product_variation_id = 27180;  //Startup
	  $subscription_product_variation_name = 'Startup';
	}
	else {
	  $subscription_product_variation_id = 27179;  //Basic
	  $subscription_product_variation_name = 'Basic';
	}

    // add a bunch of meta data
    update_post_meta($order_id, '_customer_ip_address', '', true);
    update_post_meta($order_id, 'transaction_id', $row->txn_keys, true);
    update_post_meta($order_id, '_payment_method_title', $payment_method_title, true);
    update_post_meta($order_id, '_customer_user', $wp_uid, true);
    update_post_meta($order_id, '_completed_date', date('Y-m-d H:i:s', strtotime($row->created_at)), true);
    update_post_meta($order_id, '_order_currency', 'USD', true);
    update_post_meta($order_id, '_paid_date', date('Y-m-d H:i:s', strtotime($row->created_at)), true);
    update_post_meta($order_id, '_date_paid', strtotime($row->created_at), true);

    update_post_meta($order_id, '_download_permissions_granted', 'yes', true);
    update_post_meta($order_id, '_recorded_sales', 'yes', true);
    update_post_meta($order_id, '_recorded_coupon_usage_counts', 'yes', true);
    update_post_meta($order_id, '_order_stock_reduced', 'yes', true);
    update_post_meta($order_id, '_wishlist_analytics_processed', 1, true);
    update_post_meta($order_id, 'is_vat_exempt', 'no', true);
    update_post_meta($order_id, '_prices_include_tax', 'no', true);

	//get the user membership plan
	$sql0  = 'SELECT * ';
	$sql0 .= 'FROM wp_migrate_users_mapping a ';
	$sql0 .= 'WHERE a.uid = '.$row->uid." ";
	$sql0 .= 'LIMIT 1 ';
	$results0 = $wpdb->get_results($sql0);
	if($results0) $results0 = reset($results0);

	$membership_plan_id = (isset($results0->membership_plan_id) && !empty($results0->membership_plan_id) ? $results0->membership_plan_id : 0);
	if($membership_plan_id) {
	  $memberships_access_granted = array();
	  $memberships_access_granted[$membership_plan_id] = array('already_granted' => 'yes', 'granting_order_status' => 'processing');
      add_post_meta($order_id, '_wc_memberships_access_granted', $memberships_access_granted, true);
    }

    update_post_meta($order_id, '_order_total', $row->plan_price, true);
    update_post_meta($order_id, '_order_tax', 0, true);
    update_post_meta($order_id, '_order_shipping_tax', 0, true);
    update_post_meta($order_id, '_order_shipping', 0.00, true);
    update_post_meta($order_id, '_cart_discount_tax', 0, true);
    update_post_meta($order_id, '_cart_discount', 0, true);

	// add item
	$plan_name = !empty($row->plan_name) ? $row->plan_name : 'Unknown-'.$row->plan_name_original.'-'.$row->action;
	$item_id = wc_add_order_item( $order_id, array(
	  'order_item_name' => $plan_name,
	  'order_item_type' => 'line_item'
	));

	// add item meta data
	if ( $item_id ) {
	  wc_add_order_item_meta( $item_id, '_qty', 1 );
	  wc_add_order_item_meta( $item_id, '_tax_class', '' );
	  wc_add_order_item_meta( $item_id, '_product_id', $subscription_product_id );
	  wc_add_order_item_meta( $item_id, '_variation_id', $subscription_product_variation_id );
	  wc_add_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $row->plan_price ) );
	  wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( $row->plan_price ) );
	  wc_add_order_item_meta( $item_id, '_line_tax', wc_format_decimal( 0 ) );
	  wc_add_order_item_meta( $item_id, '_line_subtotal_tax', wc_format_decimal( 0 ) );
	  wc_add_order_item_meta( $item_id, 'subscription-plan', $subscription_product_variation_name );
	}

	//$order->set_total( $total );
    $order->calculate_totals();
    $order->update_status('Completed', 'Imported order', TRUE);

	//flag drupal node record
    $sql6 = "UPDATE msg_user_2checkout_transactions SET is_migrated=1 WHERE txn_id = ".$row->txn_id;
    $migratedb->query($sql6);

  }

}
add_action( 'msg_migrate_woocommerce_orders_action', 'migrate_user_transaction_orders', 10, 2);


/**
  * Get the Drupal 7 to Wordpress migrated
  * mapped user id
  */
function msg_get_mapped_user_id($uid=0) {
  global $wpdb;
  //write_log('Location: msg_get_mapped_user_id');

  $sql  = 'SELECT * ';
  $sql .= 'FROM wp_migrate_users_mapping a ';
  $sql .= 'WHERE a.uid = '.$uid." ";
  $sql .= 'LIMIT 1 ';
  $results = $wpdb->get_results($sql);
  if($results) $results = reset($results);

  $wp_uid = 0;
  if(isset($results->uid) && $results->uid > 0) {
	$wp_uid = $results->new_uid;
  }

  return $wp_uid;
}


/**
  * Country mapping
  */
function msg_get_country($country_code = '') {
  //write_log('Location: msg_get_country');
  if(empty($country_code)) return 'US';

  $countries = array(
	'USA' => 'US',
	'GBR' => 'GB',
	'BRB' => 'BB',
	'AUS' => 'AU',
	'ZAF' => 'ZA',
	'NGA' => 'NG',
	'IND' => 'IN',
	'SAU' => 'SA',
	'ARE' => 'AE',
	'LBN' => 'LB',
	'PER' => 'PE',
	'PHL' => 'PH',
	'SGP' => 'SG',
	'CAN' => 'CA',
	'ROU' => 'RO',
	'EGY' => 'EG',
	'GRC' => 'GR',
	'KWT' => 'KW',
	'PAK' => 'PK',
	'BGD' => 'BD',
	'HRV' => 'HR',
	'SYC' => 'SC',
	'MMR' => 'MM',
	'IRL' => 'IE',
	'TUR' => 'TR',
	'OMN' => 'OM',
	'BRA' => 'BR',
	'ZWE' => 'ZW',
	'BGR' => 'BG',
	'CMR' => 'CM',
	'SVK' => 'SK',
	'MDV' => 'MV',
	'LSO' => 'LS',
	'SOM' => 'SO',
	'KEN' => 'KE',
	'TTO' => 'TT',
	'CHN' => 'CN',
	'VIR' => 'VI',
	'ITA' => 'IT',
	'PRT' => 'PT',
	'ZMB' => 'ZM',
	'ISR' => 'IL',
	'IDN' => 'ID',
	'MYS' => 'MY',
	'NOR' => 'NO',
	'PRI' => 'PR',
	'DEU' => 'DE',
	'POL' => 'PL',
	'SWE' => 'SE',
	'MUS' => 'MU',
	'HKG' => 'HK',
	'JAM' => 'JM',
	'LCA' => 'LC',
	'QAT' => 'QA',
	'JPN' => 'JP',
	'LUX' => 'LU',
	'GHA' => 'GH',
	'TZA' => 'TZ',
	'FRA' => 'FR',
	'BHR' => 'BH',
	'VNM' => 'VN',
	'JOR' => 'JO',
	'KNA' => 'KN',
	'UKR' => 'UA',
	'LTU' => 'LT',
	'BTN' => 'BT',
	'SLE' => 'SL',
	'KOR' => 'KR',
	'GUY' => 'GY',
	'AFG' => 'AF',
	'NLD' => 'NL',
	'VEN' => 'VE',
	'AUT' => 'AT',
	'RUS' => 'RU',
	'CRI' => 'CR',
	'UGA' => 'UG',
	'MNG' => 'MN',
	'NZL' => 'NZ',
	'KHM' => 'KH',
	'TUN' => 'TN',
	'AGO' => 'AO',
	'CHL' => 'CL',
	'DNK' => 'DK',
	'MOZ' => 'MZ',
	'ESP' => 'ES',
	'MWI' => 'MW',
	'CHE' => 'CH',
	'ANT' => 'CW',
	'HUN' => 'HU',
	'MKD' => 'MK',
	'AZE' => 'AZ',
	'RWA' => 'RW',
	'ATG' => 'AQ',
	'SUR' => 'SR',
	'ARM' => 'AM',
	'BHS' => 'BS',
	'MLT' => 'MT',
	'LKA' => 'LK',
	'NAM' => 'NA',
	'BEL' => 'BE',
	'DJI' => 'DJ',
	'IRQ' => 'IQ',
	'MAR' => 'MA',
	'SEN' => 'SN',
	'CZE' => 'CZ',
	'SRB' => 'RS',
	'SVN' => 'SI',
	'TWN' => 'TW',
	'BIH' => 'BA',
	'MLI' => 'ML',
	'PSE' => 'PS',
	'SWZ' => 'SZ',
	'BWA' => 'BW',
	'GTM' => 'GT',
	'ALB' => 'AL',
	'BFA' => 'BF',
	'CYP' => 'CY',
	'THA' => 'TH',
	'MEX' => 'MX',
	'CYM' => '',
	'VGB' => 'VG',
	'BDI' => 'BI',
	'COL' => 'CO',
	'India' => 'IN',
	'FJI' => 'FJ',
	'TGO' => 'TG',
	'CUW' => 'CW',
  );

  if(isset($countries[$country_code]) && !empty($interest_types[$country_code])) {
    return $countries[$country_code];
  }
  else {
    return 'US';
  }
}


/**
  * Set course ratings
  */
function courses_set_rating_data($migratedb, $migrate_moodledb) {
  global $wpdb;
  //write_log('Location: courses_set_rating_data');

  //get the drupal vs moodle course list mapping
  $sql = 'SELECT * FROM course_idnumber';
  $results = $migratedb->get_results($sql);

  foreach($results as $row) {
	$drupal_course_id = $row->cid;
	$moodle_course_id = $row->idnumbers;
	$is_migrated = $row->is_migrated;

	//get the drupat to wordpress course id
    $sql1 = 'SELECT * FROM wp_migrate_nodes_mapping WHERE nid = '.$drupal_course_id;
    $results1 = $wpdb->get_results($sql1);
    $learndash_pid = 0;
    foreach($results1 as $row1) {
	  $learndash_pid = $row1->learndash_pid;
	}
	if(empty($learndash_pid)) continue;

	if($is_migrated == 0 || empty($is_migrated)) {
	  //get the like users list using moodle course id
      $sql2 = 'SELECT * FROM mdl_course_like_status WHERE status = 1 AND course = '.$moodle_course_id;
      $results2 = $migrate_moodledb->get_results($sql2);

      foreach($results2 as $row2) {
	    $moodle_user_id = $row2->user;
	    $moodle_timecreated = $row2->timecreated;

	    //get the drupal uid from moodle uid
        $sql3 = 'SELECT * FROM users_idnumber WHERE idnumbers = '.$moodle_user_id;
        $results3 = $migratedb->get_results($sql3);
        $drupal_user_id = 0;
        foreach($results3 as $row3) {
	      $drupal_user_id = $row3->uid;
        }
        if(empty($drupal_user_id)) continue;

	    //get the drupat to wordpress user id
	    $wp_uid = msg_get_mapped_user_id($drupal_user_id);
	    if(empty($wp_uid)) continue;


	    //create learndash course review
	    $course_review_data = array(
          'post_title'    => 'Very Nice Course',
          'post_content'  => '',
          'post_excerpt'  => '',
          'post_status'   => 'publish',
          'post_author'   => $wp_uid,
          'post_type'	    => 'ldcr_review',
          'post_date' 	=> date( 'Y-m-d H:i:s', $moodle_timecreated ),
          'post_date_gmt' => date( 'Y-m-d H:i:s', $moodle_timecreated ),
          'post_modified' => date( 'Y-m-d H:i:s', $moodle_timecreated ),
          'post_modified_gmt' => date( 'Y-m-d H:i:s', $moodle_timecreated ),
          'post_name'     => '',
          'comment_status'=> 'closed',
          'comment_count' => 0,
          'ping_status'   => 'closed',
          'post_parent'   => 0,
          'filter' 	      => 'raw',
	      'meta_input'   => array(
	        '_ldcr_course_id' => $learndash_pid,
	        '_ldcr_rating' => 5,
	        '_ldcr_upvotes' => 0,
	      ),
	    );

	    $course_review_id = wp_insert_post( $course_review_data );
	    if ( is_wp_error( $course_review_id ) ) {
	      echo 'ERROR: ' . $course_review_id->get_error_message();
	      continue;
	    }

	    //update meta for course
        $sql4  = "SELECT COUNT(*) as total_reviews ";
        $sql4 .= "FROM wp_postmeta a ";
        $sql4 .= "INNER JOIN wp_posts b ON a.post_id = b.ID ";
        $sql4 .= "WHERE a.meta_key = '_ldcr_course_id' ";
        $sql4 .= "AND a.meta_value = '".$learndash_pid."' ";
        $sql4 .= "AND b.post_type = 'ldcr_review' ";
        $results4 = $wpdb->get_results($sql4);
        $total_reviews = 0;
        foreach($results4 as $row4) {
          $total_reviews = $row4->total_reviews;
        }
		update_post_meta($learndash_pid, '_ldcr_rating', 5);
		update_post_meta($learndash_pid, '_ldcr_total_rating', $total_reviews);
		update_post_meta($learndash_pid, '_ldcr_enable', 1);
		update_post_meta($learndash_pid, '_ldcr_review_after', 0);

	    //flag drupal node record
        $sql5 = "UPDATE course_idnumber SET is_migrated=1 WHERE cid = ".$drupal_course_id;
        $migratedb->query($sql5);

	  }  //foreach-$results2
	}
	else {  //is_migrated == 1
	  //update meta for course
      $sql4  = "SELECT COUNT(*) as total_reviews ";
      $sql4 .= "FROM wp_postmeta a ";
      $sql4 .= "INNER JOIN wp_posts b ON a.post_id = b.ID ";
      $sql4 .= "WHERE a.meta_key = '_ldcr_course_id' ";
      $sql4 .= "AND a.meta_value = '".$learndash_pid."' ";
      $sql4 .= "AND b.post_type = 'ldcr_review' ";
      $results4 = $wpdb->get_results($sql4);
      $total_reviews = 0;
      foreach($results4 as $row4) {
        $total_reviews = $row4->total_reviews;
      }
	  update_post_meta($learndash_pid, '_ldcr_rating', 5);
	  update_post_meta($learndash_pid, '_ldcr_total_rating', $total_reviews);
	  update_post_meta($learndash_pid, '_ldcr_enable', 1);
	  update_post_meta($learndash_pid, '_ldcr_review_after', 0);
	}

  }  //foreach-$results


  //get the courses which are migrated
  $sql = 'SELECT * FROM wp_migrate_nodes_mapping WHERE is_migrated = 1 AND woo_pid > 0 ';
  $results = $wpdb->get_results($sql);

  foreach($results as $row) {
	$learndash_course_id = $row->learndash_pid;
	$woocommerce_product_id = $row->woo_pid;

	$related_course = array();
	$related_course[] = $learndash_course_id;
	update_post_meta( $woocommerce_product_id, '_related_course', $related_course);
  }

}




