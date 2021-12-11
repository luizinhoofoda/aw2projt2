<?php
	// include config file
	require_once 'config.php';

	//a PHP Super Global variable which used to collect data after submitting it from the form
	// Sanitize fist the values of this variable
	$request = sanitize($_REQUEST);

	// Validate the data
	$validation = validate($request, [
		'voucher-code' => 'required|max:25'
	]);

	// Defined $result as array
	$result = [];

	// Check if no validation errors
	if(!count($validation)):

		// Connect to database
		$db = connectDB();

		// Set the INSERT SQL data
		$sql = "SELECT * FROM vouchers WHERE name='".$request['voucher-code']."' AND status='1'";

		// Process the query
		$results = $db->query($sql);

		// Fetch Associative array
		$row = $results->fetch_assoc();

		// Check if voucher code still active
		if(!is_null($row)):
			// Set the UPDATE SQL data for voucher code to inactive after using it
			$sql = "UPDATE vouchers SET status='0' WHERE id='".$row['id']."'";

			// Process the query
			if ($db->query($sql)) {
			  	$result['response'] = "voucher code succesfully redeemed.";
			} else {
				$result['response'] = "Error: " . $sql . "<br>" . $db->error;
			}
		else:
			$result['has_error'] = 1;
			$result['errors']['voucher-code'] = [
				"used" => "voucher code is already in used."
			];
		endif;

		// Close the connection after using it
		$db->close();
	else:
		$result['has_error'] = 1;
	   	$result['errors'] = $validation;
	endif;

	// Encode array into json format
	echo json_encode($result);


?>