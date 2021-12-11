<?php

	
	function voucherCodeGenerator() 
	{
		$voucherGenerated = randomString();

		$attributes = [
			'name' => $voucherGenerated
		];

		
		if(isRecordUnique($attributes, 'name', 'vouchers')):
			
			
			$db = connectDB();

		
			$sql = "INSERT INTO vouchers (name)
			VALUES (
				'".$voucherGenerated."'
			)";

			
			if (!$db->query($sql)):
				echo "Error: " . $sql . "<br>" . $db->error;
			endif;

			
			$db->close();

			return $voucherGenerated;

		else:
			voucherCodeGenerator();
		endif;
	}


	function randomString($length = 10) {
		
		$chars='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		
		$totalChars = strlen($chars);

		$totalRepeat = ceil($length/$totalChars);

		
		$repeatString = str_repeat($chars, $totalRepeat);

		
		$shuffleString = str_shuffle($repeatString);

		
	    return substr($shuffleString,1,$length);
	}
	
	function connectDB() 
	{
		$db = new mysqli(SERVER_NAME, SERVER_UNAME, SERVER_UPASS, SERVER_DB);

		if ($db->connect_errno) {
		  echo "Failed to connect to MySQL: " . $db->connect_error;
		  exit();
		}

		return $db;
	} 

	function sanitize($input) 
	{
		if(is_array($input)):
			foreach($input as $key=>$value):
				$result[$key] = sanitize($value);
			endforeach;
		else:
			$result = htmlentities($input, ENT_QUOTES, 'UTF-8');
		endif;

		return $result;
	}

	function validate($input, $rules) 
	{
		$errors = [];

		if(is_array($input)):
			foreach($rules as $fieldName=>$value):
				$fieldRules = explode("|", $value);

				foreach($fieldRules as $rule):

					$ruleValue = _getRuleSuffix($rule);
					$rule = _removeRuleSuffix($rule);

					if($rule == "required" && isEmptyFieldRequired($input, $fieldName)):
						$errors[$fieldName]['required'] = _removeUnderscore(ucfirst($fieldName)) . " is required.";
					endif;

					if($rule == "email" && !isEmailValid($input, $fieldName)):
						$errors[$fieldName]['email'] = _removeUnderscore(ucfirst($fieldName)) . " is invalid.";
					endif;

					if($rule == "min" && isLessThanMin($input, $fieldName, $ruleValue) && !isEmptyFieldRequired($input, $fieldName)):
						$errors[$fieldName]['max'] = _removeUnderscore(ucfirst($fieldName)) . " value is too short.";
					endif;

					if($rule == "max" && isMoreThanMax($input, $fieldName, $ruleValue)):
						$errors[$fieldName]['max'] = _removeUnderscore(ucfirst($fieldName)) . " value is too long.";
					endif;

					if($rule == "unique" && !isRecordUnique($input, $fieldName, $ruleValue)):
						$errors[$fieldName]['unique'] = _removeUnderscore(ucfirst($fieldName)) . " is already exists.";
					endif;

				endforeach;
			endforeach;
		endif;

		return $errors;
		
	}

	function isEmptyFieldRequired($input, $fieldName) 
	{
		return $input[$fieldName] == "" || empty($input[$fieldName]);
	}

	function isLessThanMin($input, $fieldName, $value) 
	{
		return strlen($input[$fieldName]) < $value; 
	}

	function isMoreThanMax($input, $fieldName, $value) 
	{
		return strlen($input[$fieldName]) > $value;
	}

	function isRecordUnique($input, $fieldName, $tableName) 
	{	
		
		$db = connectDB();

		
		$sql = "SELECT * FROM ".$tableName." WHERE ".$fieldName."='".$input[$fieldName]."'";

		
		$results = $db->query($sql);

		
		$row = $results->fetch_assoc();

		
		$db->close();

		
		return !is_array($row);
	}

	function isEmailValid($input, $fieldName) 
	{
		$email = $input[$fieldName];

		if(!empty($email) || $email != ""):
			return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
		else:
			return TRUE;
		endif;
	}


	function _removeUnderscore($string) 
	{
		return str_replace("_", " ", $string);
	}

	function _removeRuleSuffix($string) 
	{
		$arr = explode(":", $string);

		return $arr[0];
	}

	function _getRuleSuffix($string) 
	{
		$arr = explode(":", $string);

		return isset($arr[1])?$arr[1]:null;
	}

?>