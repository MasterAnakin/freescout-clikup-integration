<?php
/**
 * Freescout Clikup integration. File that calls all tasks for specific client.
 *
 * Logic for calling AJAX requests.
 *
 * @package clikup-fs-api
 * @version 1.0.0
 * @author  Milos Milosevic
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link https://valet.io
 */

header( 'Access-Control-Allow-Origin: add_url' );


if ( isset( $_GET['clientId'] ) && ! empty( $_GET['clientId'] ) ) {

	$client_id = intval( $_GET['clientId'] );

	$query = array(
		'include_closed' => 'true',
	);

	$curl = curl_init();

	curl_setopt_array(
		$curl,
		array(
			CURLOPT_HTTPHEADER     => array(
				'Authorization: add_key',
				'Content-Type: application/json',
			),
			CURLOPT_URL            => 'https://api.clickup.com/api/v2/list/' . $client_id . '/task?' . http_build_query( $query ),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST  => 'GET',
		)
	);



	$response = curl_exec( $curl );
	$error    = curl_error( $curl );

	curl_close( $curl );

	if ( $error ) {
		echo 'cURL Error #:' . $error;
	} else {
		$response_decode = json_decode( $response, true );
		$i               = 0;
		foreach ( $response_decode as $list_client_tasks ) {
			if ( is_array( $list_client_tasks ) || is_object( $list_client_tasks ) ) {

				foreach ( $list_client_tasks as $single_task ) {

					$custom_id         = $single_task['custom_id'];
					$clickup_task_url  = $single_task ['url'];
					$clickup_task_name = $single_task ['name'];

					echo '<a href="' . htmlspecialchars( $clickup_task_url ) . '" target="_blank">' . htmlspecialchars( $clickup_task_name ) . '</a><br>';
					// Show only last 12 tasks.
					if ( ++$i > 12 ) {
						break;
					}
				}
			}
		}
	}
}
