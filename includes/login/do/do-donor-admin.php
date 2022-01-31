<?php
/*
 * Donor administration
 * @since 2017-05-12
 * @version 2017-05-13 first working version
 * @version 2017-05-15 added modify function
 * 
 *  Copyright (C) 2022  <Klaus D. Goepel>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
include '../../config.php';

$title="BPMSG Donations";
$version = substr('$LastChangedDate$',18,10);
$rev = trim('$Rev$', "$");

$para = array(
	'trNo'	=>	0,
	'trId' 	=> 	"",
	'trAmnt'=>	0.,
	'trFee'	=>	0.,
	'trFx'	=>	1.,
	'trDate' => "",
	'trName' => "",
	'trEmail' => "",
	'trCmnt' => "",
	'trUser' => "",
	'trUid' => 1,
	'trUemail' =>""
);

$donors = array();
$trNumbers = array();
$trNoDonor = array();
$storedUsers = array();
$formToken="";

$trDate = date('Y-m-d', time());
$err = "";
$msg = "";
$year = date("Y");
$yr = 1; // by default actual year for donor list

/* Functions */
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/* 
 * Validation of form data. This function can serve as a template for other forms
 * @version 2017-05-17
 * @para array $trD defines default values for empty form
 * @return array $para clean array of form input data
 */
function validateFormData($trD = array()){
	global $err;
	global $validateDate;
	// strip all tags (FILTER_SANITIZE_STRING)

	$fData = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
	// [^\p{L}\p{N}\-\&] except letters, numeric chars, hyphen and ampersand, ~u UTF-8 ?
  if( isset($fData['formToken']) && $fData['formToken'] != $_SESSION['formToken']){
 		$err .= "<span class='err'>Form input error. </span>";
 	}
//	if(filter_has_var(INPUT_POST, 'trName'))
		$fData['trName'] = mb_substr(preg_replace('~[^\p{L}\p{N}\-\&]++~u',' ', $fData['trName']),0,64);
//	if(filter_has_var(INPUT_POST, 'trUser'))
		$fData['trUser'] = mb_substr(preg_replace('~[^\p{L}\p{N}\-\&]++~u',' ', $fData['trUser']),0,64);
//	if(filter_has_var(INPUT_POST, 'trId'))
		$fData['trId'] =   mb_substr(preg_replace('~[^\p{L}\p{N}\-\&]++~u',' ', $fData['trId']),0,30);
//	if(filter_has_var(INPUT_POST, 'trCmnt'))
		$fData['trCmnt'] = mb_substr(preg_replace('~[^\p{L}\p{N}\-\&\?\.\!\,\:]++~u',' ', $fData['trCmnt']),0,255);
	$fData['trAmnt'] = mb_substr(filter_input(INPUT_POST,'trAmnt', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),0,10);
	$fData['trFee'] =  mb_substr(filter_input(INPUT_POST, 'trFee', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),0,10);
	$fData['trFx'] =   mb_substr(filter_input(INPUT_POST,  'trFx', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),0,10);
	$fData['trEmail']= mb_substr(filter_input(INPUT_POST,'trEmail', FILTER_SANITIZE_EMAIL),0,64);

// check empty, ranges and set defaults
	if(empty($fData['trNo']))
		$fData['trNo'] = 0;

	// modify existing receord: fill empty fields with defaults from $trD
	if ($fData['trNo'] != 0 && ! empty($trD) ) {
		if($fData['trId'] == "")    $fData['trId'] = $trD['trId'];
		if(!isset($fData['trAmnt']) || $fData['trAmnt'] == "")  $fData['trAmnt'] = $trD['trAmnt'];
		if(!isset($fData['trFee'] ) || $fData['trFee'] == "")   $fData['trFee']  = $trD['trFee'];
		//		if($fData['trDate'] == date('Y-m-d'))
		//			$fData['trDate'] = $trD['trDate'];
		if($fData['trName'] == "")	 $fData['trName'] = $trD['trName'];
		if($fData['trEmail'] == "") $fData['trEmail'] = $trD['trEmail'];
		if($fData['trCmnt'] == "" )  $fData['trCmnt'] = $trD['trCmnt'];
		$fData['trUid'] = $trD['trUid'];
	}
	// check mandatory fields and validity of inputs
	if($fData['trAmnt'] == "")
		$err .= "<span class='err'>Amount required. </span>";
	elseif ( $fData['trAmnt']<0 || $fData['trAmnt']>1000)
		$err .= "<span class='err'>Invalid value for amount. </span>";
	if($fData['trFee'] < 0 || $fData['trFee'] > $fData['trAmnt'])
		$err .= "<span class='err'>Invalid value for fee. </span>";
		if(isset($fData['trName']) && $fData['trName'] =="")
		$err .= "<span class='err'>Empty Name field. </span>";
	if($fData['trEmail'] == "")
		$err .= "<span class='err'>E-mail required. </span>";
	if($fData['trFx'] == "" || $fData['trFx'] <= 0)
			$fData['trFx'] = 1.0;
	if(isset($fData['trDate']) && $fData['trDate'] == "")
		$fData['trDate'] = date('Y-m-d');
	elseif (isset($fData['trDate']) && !validateDate($fData['trDate'], 'Y-m-d'))
		$err .= "<span class='err'>Invalid Date Format. </span>";
	return $fData;
}

	$login = new Login();
	$ahpAdmin = new AhpAdmin();

	if ($login->isUserLoggedIn() && in_array($_SESSION['user_id'], $admin))
		$loggedIn = true;

	// --- MENU SWITCH
	if (isset($_POST['EXIT'])){
		header('HTTP/1.0 200 ok');
		header("Location: " . "do-user-admin.php" );
	}
	// --- REFR --- refresh after selecting year period
	if(isset($_POST['REFR'])){
		// year selection
		if($_POST['trYr'] == -1){
			$year -= 1; $yr = -1;
		} elseif ($_POST['trYr'] == 2) {
			$year .= ", " . ($year-1); $yr = 2;
		} elseif ($_POST['trYr'] == 0) {
			$year = "all"; $yr = 0;
		}
	} else {
		if ( isset($_POST['trNo']) && $_POST['trNo'] != 0  && empty($_POST['CLR']) ) {
			// get donor details
			$trNoDonor = $ahpAdmin->getDonorDetails($_POST['trNo']);
		}	
		// --- Cleanup and check input
		if(isset($_POST)){
			$para = validateFormData($trNoDonor);
			$para['formToken'] =  $formToken;
		}
		// --- CLR --- clear form fields
		if(isset($_POST['CLR'])){
			$para['trName'] = $para['trEmail'] = $para['trId'] = $para['trCmnt'] = $para['trFx'] =
			$para['trAmnt'] = $para['trFee'] = $para['trUser'] = $para['trUid'] = $para['trUemail'] = "";
			$para['trNo'] = 0;
			$para['trDate'] = date('Y-m-d');
		}
		if ( ( isset($_POST['CHKID']) || isset($_POST['UPDT']) || isset($_POST['MOD']) || isset($_POST['CLR']))  ) {
		// get user id from $_POST['trUser']
			if( isset($para['trUser']) && $para['trUser'] != "unknown"){
				// get AHP user details
				$user = $ahpAdmin->getUserDetails($para['trUser']);
				$para['trUid'] = $user[0];
				$para['trUemail'] = $user[2];
			} elseif ( !empty($trNoDonor) && $para['trUser'] == "unknown" ) {
				// update AHP user fields
				$para['trUid'] = $trNoDonor['trUid'];
				$para['trUser'] = $trNoDonor['user_name'];
				$para['trUemail'] = $trNoDonor['user_email'];
			} else	{
				// empty AHP user id and email
				$para['trUid'] = "";
				$para['trUemail'] ="";
			}
			// check for donation
			$donor = $ahpAdmin->getDonor($para['trUid'] );
			if(count($donor) > 0) {
				$msg = "<span class='hl'>We have " . count($donor) . " donation(s) from this user already.</span>";
				if ($para['trName'] == "")
					$para['trName'] = $donor[0]['trName'];
				if ($para['trEmail'] =="")
					$para['trEmail'] = $donor[0]['trEmail'];
			}
		}
		// --- UPDT --- Insert data into donations table
		if(isset($_POST['UPDT']) && count($ahpAdmin->errors)==0 && $err == ""){
			$flag = $ahpAdmin->writeUserDonation($para['trDate'], $para['trId'], $para['trAmnt'], $para['trFee'], $para['trName'],
		 	$para['trEmail'], $para['trCmnt'], $para['trUid']);
		if($flag == true){
			$msg .= "<span class='msg'><br>New Donation inserted into donation table. </span>";
		} else
			$err .= "<span class='err'>Data could not be written. </span>";
		} elseif(isset($_POST['UPDT']) && $err != "") {
			$err .= "<span class='err'>Data were not inserted. </span>";
		}
		// --- MOD --- MODIFY existing data in donations table
		if(isset($_POST['MOD']) && count($ahpAdmin->errors)==0 && $err == ""){
			$flag = $ahpAdmin->modifyUserDonation($para);
			if($flag == true){
				$msg .= "<span class='msg'><br>Donation was successfully updated. </span>";
			} else
				$err .= "<span class='err'>Data could not be updated. </span>";
			} elseif(isset($_POST['MOD']) && $err != "") {
				$err .= "<span class='err'>Data could not be modified. </span>";
			}
			// selection list trNo 
			$trNumbers = array( "0" => "0");
			$trNumbers = array_merge( $trNumbers, $ahpAdmin->getAllTrNos());
			// selection list Users
			$storedUsers = array( "9999" => "unknown");
			$storedUsers = array_merge( $storedUsers, $ahpAdmin->getUserNames());
	}

// --- MAIN ---
$webHtml = new WebHtml($title);
	include('../form.login-hl.php');
echo "<h1>$title</h1>";
if ( $loggedIn === true && in_array($_SESSION['user_id'], $admin )) {
	if(DONATIONS){
		echo "<p>",$msg . $ahpAdmin->getErrors() . "</p>";
		echo "<form method='POST' action='$urlAct'>";
		// Insert new donation/Update existing
		if ( isset($_POST['INS']) || isset($_POST['CHKID']) || isset($_POST['CLR']) || (isset($_POST['UPDT']) && $err !="")){
			$formToken = $_SESSION['formToken'] = uniqid();
			// show donation insert form
			echo "<h3>Donation</h3>";
			include '../form.newdon.html' ;
			echo "<p>" . $err . "</p>";
		} else {
			echo '<h3>Donations ' . $year . '</h3>';
			$donors = $ahpAdmin->getAllDonors($yr);
			$ahpAdmin->displayDonorTable($donors);
			echo "<h3>Menu</h3>";
			include '../form.donations.html' ;
		}
		echo "</form>";
	} else {
		echo "<p>",$msg . $ahpAdmin->getErrors() . "</p>";
		echo '<p>This feature is disabled in the config file.</p>';
	}
} else {
	echo '<h2>Please login</h2>';
	echo '<p>You need to be registered to access this website.</p>';
}
//--- END
$webHtml->webHtmlFooter($version);
