<?php
/**
 * Freescout Clikup integration. File that calls AJAX.
 *
 * Logic for calling AJAX requests.
 *
 * @package fs-clikup
 * @version 1.0.0
 * @author  Milos Milosevic
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link https://valet.io
 */

header( 'Access-Control-Allow-Origin: add_url' );
// List all Clikup folders to pull clients from.

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
		CURLOPT_URL            => 'https://api.clickup.com/api/v2/space/32263954/folder?' . http_build_query( $query ),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST  => 'GET',
	)
);

$folders[] = curl_exec( $curl );
$error     = curl_error( $curl );



if ( $error ) {
	echo 'cURL Error #:' . htmlspecialchars( $error );
} else {

	$all_clients = array();
	foreach ( $folders as $single_response ) {

		$decode_response = json_decode( $single_response, true );

		foreach ( $decode_response as $first_array ) {

			foreach ( $first_array as $single_client_value ) {
				?>
				<?php $all_clients[] = ( $single_client_value['name'] . $single_client_value['id'] ); ?>
				<?php
			}
		}
	}
	echo '<select id="clients-list" title="Select Client" style="max-width: 200px;">';
	echo '<option value="">Select Client</option>';
	sort( $all_clients );
	foreach ( $all_clients as $single_client ) {
			$single_client_id   = preg_replace( '/[^0-9]/', '', $single_client );
			$single_client_name = preg_replace( '/[0-9]+/', '', $single_client );

		?>
			<option value="<?php echo intval( $single_client_id ); ?>"><?php echo htmlspecialchars( $single_client_name ); ?></option>

		<?php
	}

	echo '</select><div id="list-tasks"></div>';

}



	$time_now           = time() * 1000;
	$due_date           = time() + 72 * 60 * 60;
	$due_date_13_digits = $due_date * 1000;
if ( isset( $_POST['customerEmail'] ) ) {
	$customer_email = $_POST['customerEmail'];
}
if ( isset( $_POST['conversationSubject'] ) ) {
	$conversation_subject = $_POST['conversationSubject'];
}


echo '<div class="fs-values">
		<div id="time-now" style="display:none;">' . esc_html( $time_now ) . '</div>
		<div id="due-date" style="display:none;">' . esc_html( $due_date ) . '</div>
		<div id="due-date-13" style="display:none;">' . esc_html( $due_date_13_digits ) . '</div>
		<div id="customer-email" style="display:none;">' . esc_html( $customer_email ) . '</div>
		<div id="conversation-subject" style="display:none;">' . esc_html( $conversation_subject ) . '</div>
		</div>';


echo '<button type="button" id="button-clikup" style="margin-top: 10px;">Create Task</button>';
?>

<script src="https://dev-valet.pantheonsite.io/clikup-fs-api/ajax.js"></script>
