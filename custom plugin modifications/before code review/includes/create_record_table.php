<?php



function create_quiz_records_table() {
	global $wpdb;


	$table_name = $wpdb->prefix . 'quiz_records_table';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		quiz_id int(11) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		user_id mediumint(9) NOT NULL,
		score mediumint(9) NOT NULL,
        questions mediumint(9) NOT NULL,
        quiz_type varchar(20) NOT NULL,
		PRIMARY KEY  (quiz_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function create_parent_child_record_table() {
	global $wpdb;


	$table_name = $wpdb->prefix . 'parent_child_record_table';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		parent_user_id mediumint(9) NOT NULL,
		child_user_id mediumint(9) NOT NULL,
		CONSTRAINT link PRIMARY KEY  (parent_user_id, child_user_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function create_mta_flag_table() {
	global $wpdb;


	$table_name = $wpdb->prefix . 'mta_flag_table';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_id mediumint(9) NOT NULL,
		PRIMARY KEY  (user_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}


