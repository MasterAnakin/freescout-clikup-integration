<?php
/**
 * Freescout Clikup integration. Creating task.
 *
 * Logic for creating tasks in Clikup.
 *
 * @package clikup-fs-api
 * @version 1.0.0
 * @author  Milos Milosevic
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link https://mmilosevic.com
 */

header( 'Access-Control-Allow-Origin: your_site' );
// Get task data from FreeScout.
$post_email           = isset( $_POST['customerEmail'] ) ? strip_tags( $_POST['customerEmail'] ) : null;
$conversation_subject = isset( $_POST['conversationSubject'] ) ? strip_tags( $_POST['conversationSubject'] ) : null;
$task_assignee        = isset( $_POST['taskAssignee'] ) ? strip_tags( $_POST['taskAssignee'] ) : null;
$client_folder_id     = isset( $_POST['clientId'] ) ? strip_tags( $_POST['clientId'] ) : null;
$time_now             = isset( $_POST['timeNow'] ) ? strip_tags( $_POST['timeNow'] ) : null;
$due_date             = isset( $_POST['dueDate'] ) ? strip_tags( $_POST['dueDate'] ) : null;
$description          = isset( $_POST['taskDescription'] ) ? htmlspecialchars( $_POST['taskDescription'] ) : null;
$task_url             = isset( $_POST['taskUrl'] ) ? strip_tags( $_POST['taskUrl'] ) : null;




$query = array(
	'archived' => 'false',
);

$curl = curl_init();

curl_setopt_array(
	$curl,
	array(
		CURLOPT_HTTPHEADER     => array(
			'Authorization: add_key',
		),
		CURLOPT_URL            => 'https://api.clickup.com/api/v2/folder/' . $client_folder_id . '/list?' . http_build_query( $query ),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST  => 'GET',
	)
);

$response = curl_exec( $curl );
$error    = curl_error( $curl );

$decode_response_lists = json_decode( $response, true );

$i = 0;
foreach ( $decode_response_lists as $first_array ) {

	foreach ( $first_array as $single_client_value ) {
		if ( $i == 0 ) {
			$single_client_id = $single_client_value['id'];
		} else {
			break;
		}
		++$i;
	}
}



// Set team ID.
$query = array(
	'custom_task_ids' => 'true',
	'team_id'         => 'id_number',
);

$curl = curl_init();
// Team members IDs
$ass_arr = array(
	'Milos'    => add_team_id_number,
);
if ( array_key_exists( $task_assignee, $ass_arr ) ) {
	$task_assignee_idd = $ass_arr[ $task_assignee ];
}
// Populate payload for Clikup.
$payload = array(
	'name'                         => $conversation_subject,
	'description'                  => $description,
	'assignees'                    => array(
		$task_assignee_idd,
	),
	'tags'                         => array(
		'support',
	),
	'status'                       => 'to do',
	'priority'                     => 3,
	'due_date'                     => $due_date,
	'due_date_time'                => false,
	'time_estimate'                => 3600000,
	'start_date'                   => $time_now,
	'start_date_time'              => false,
	'notify_all'                   => true,
	'parent'                       => null,
	'links_to'                     => null,
	'check_required_custom_fields' => true,
	'custom_fields'                => array(
		array(
			'id'    => 'be0143f9-79c9-48b6-af12-5ab88f02f313',
			'value' => 'fac9912c-8d77-4f2b-9cb0-1ca28718edf9',
		),
		array(
			'id'    => '123f746b-174c-4417-a472-7eeecaabbca5',
			'value' => $task_url,
		),
	),
);

curl_setopt_array(
	$curl,
	array(
		CURLOPT_HTTPHEADER     => array(
			'Authorization: add_key',
			'Content-Type: application/json',
		),
		CURLOPT_POSTFIELDS     => json_encode( $payload ),
		CURLOPT_URL            => 'https://api.clickup.com/api/v2/list/' . $single_client_id . '/task?' . http_build_query( $query ),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST  => 'POST',
	)
);

$response = curl_exec( $curl );
$error    = curl_error( $curl );

curl_close( $curl );

if ( $error ) {
	echo 'cURL Error #:' . esc_html( $error );
} else {
	$decode_response = json_decode( $response, true );
	echo esc_html( $decode_response['id'] );
}
