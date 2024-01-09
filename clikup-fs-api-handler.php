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

header( 'Access-Control-Allow-Origin: https://inbox.valet.io' );
// List all Clikup folders to pull clients from.

$query = array(
  "archived" => "false"
);

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_HTTPHEADER => [
    "Authorization: pk_57096564_GTODYU5KD1U0D88DUOM38H5NDTFI8DBA"
  ],
  CURLOPT_URL => "https://api.clickup.com/api/v2/space/32263954/folder?" . http_build_query($query),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "GET",
]);

$folders[] = curl_exec($curl);
$error = curl_error($curl);



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

	<?php }

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


echo 	'<div class="fs-values">
		<div id="time-now" style="display:none;">'. $time_now .'</div>
		<div id="due-date" style="display:none;">'. $due_date .'</div>
		<div id="due-date-13" style="display:none;">'. $due_date_13_digits .'</div>
		<div id="customer-email" style="display:none;">'. $customer_email .'</div>
		<div id="conversation-subject" style="display:none;">'. $conversation_subject .'</div>
		</div>';


echo '<button type="button" id="button-clikup" style="margin-top: 10px;">Create Task</button>';
?>

<script src="https://dev-valet.pantheonsite.io/clikup-fs-api/ajax.js"></script>

<?php
/*
echo '<script>
    let customerEmail = "' . htmlspecialchars( $customer_email ) . '";
    let conversationSubject = "' . htmlspecialchars( $conversation_subject ) . '";
    let timeNow = "' . intval( $time_now ) . '";
    let dueDate = "' . intval( $due_date_13_digits ) . '";
    let taskAssignee = jQuery(".nav-user").text();
    let taskDescription_n = jQuery(".thread-type-customer:last-child .thread-body").text();
    let taskDescription = taskDescription_n.replace(/\s\s+/g, " ");
    let taskUrl = window.location.href; 
    jQuery("#button-clikup").click(function(){
    let clientId = jQuery("#clients-list").find(":selected").val();
    $.ajax({
      url: "https://dev-valet.pantheonsite.io/clikup-fs-api/clickup-api-create-task.php",
      type: "POST",
      data: { customerEmail: customerEmail, conversationSubject: conversationSubject, taskAssignee: taskAssignee, clientId: clientId, timeNow: timeNow, dueDate: dueDate, taskDescription: taskDescription, taskUrl: taskUrl },
      success: function(decode_response) {
        alert("Task created! New task link is: https://app.clickup.com/t/" + decode_response);        
      }
    });
  })</script>';

*/

?>
