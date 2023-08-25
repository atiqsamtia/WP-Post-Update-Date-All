<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function handleComments() {
	global $wpdb;
	$comments = $wpdb->get_results( "SELECT c.comment_ID, c.comment_post_ID, c.comment_date, p.post_date 
		FROM $wpdb->comments c 
	    JOIN $wpdb->posts p ON c.comment_post_ID = p.ID 
		WHERE c.comment_approved = 1 AND p.post_status = 'publish'" );

	list( $from, $to ) = getFromAndToDates();
	$total = 0;
	foreach ( $comments as $comment ) {
		$total ++;
		$post_date = strtotime( $comment->post_date );
		$_from     = $from;
		if ( $from < $post_date ) {
			$_from = $post_date;
		}
		$_to = $to;
		if ( $to < $post_date ) {
			$_to = $post_date + 60;
		}

		$time = rand( $_from, $_to );
		$time = date( "Y-m-d H:i:s", $time );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $wpdb->comments SET comment_date ='%s', comment_date_gmt='%s' WHERE comment_ID = %d",
				$time,
				get_gmt_from_date( $time ),
				$comment->comment_ID
			)
		);
	}

	return $total;
}

function getFromAndToDates() {

	$from = $_POST['distribute'];
	$to   = current_time( 'timestamp', 0 );
	$now  = current_time( 'timestamp', 0 );

	if ( $from == 0 ) {
		$range = explode( '-', $_POST['range'] );
		if ( count( $range ) == 2 ) {
			$from = strtotime( $range[0], $now );
			$to   = strtotime( $range[1], $now );
		} else {
			$from = strtotime( '-3 hours', $now );
		}
	}

	return [ $from, $to ];
}