<?php
/* User administration for ahp project database
 * @version 2017-04-09 last version w/o SVN
 * @uses class.Login.php
 * @uses class.LoginAdmin.php
 * @uses form.UserAdminMenu/html
 * 
    Copyright (C) 2022  <Klaus D. Goepel>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */
include '../../config.php';
// --- SENDING MAIL WHEN USER IS DEACTVATED
require_once('../../PHPMailer/PHPMailer.php'); // Mailer
require_once('../../PHPMailer/SMTP.php'); // Mailer
require_once('../../PHPMailer/Exception.php'); // Mailer

session_start();

$title= "User Administration";
$version = substr('$LastChangedDate$',18,10);
$rev = trim('$Rev$', "$");

$lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");

$storedUsers = array();
$usersInactive = array();
$msg = "";
$userName= "";
$sel = array(); // selected inactive users to deactivate
$loggedIn = false;
$mail = REGISTRATION_EMAIL;

// specific: used for project session table link
$urlAhpH =     BASE . $urlAhp;
$urlGroupRes = BASE . $urlGroupRes;
$urlSessionAdmin = BASE . $urlSessionAdmin;

$login = new Login();
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);

$userDb = new LoginAdmin();
$ahpAdmin = new AhpAdmin();

if ($login->isUserLoggedIn() && in_array($_SESSION['user_id'], $admin )) {
	$loggedIn = true;
} elseif( !isset($_POST['user_edit_form_delete'])) {
    header("Location: " . SITE_URL);
    exit();
}

// Last login more than n days ago
$daysInactive = 90;
$usersInactive = $ahpAdmin->getInactiveUsers($daysInactive);

/* 
 * --- MENUE SWITCH --- 
 */
if (isset($_POST['EXIT'])){
	header('HTTP/1.0 200 ok');
	header("Location: " . SITE_URL );
}
if (isset($_POST['RECOV'])){
	header('HTTP/1.0 200 ok');
	header("Location: " . BASE . "ahp-user-recover.php" );
}

// For deactivation of users using checkbox
if( isset($_POST['chk'])){
			$sel = $_POST['chk'];
}

if ( isset($_POST['DEL']) || isset($_POST['DELALL']) || isset($_POST['OPEN']) || isset($_POST['DEACT']) || isset($_POST['REACT'])) {
	$para = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
	if ( filter_has_var(INPUT_POST, 'user_name') ){
		$userName = $para['user_name'];
	}
	// Delete selected user 
	if (isset($_POST['DEL'])){
		if( $userDb->deleteUser($userName) == false){
			$msg = "User could not be deleted. " . $userDb->getErrors();
		}
	}
	// Delete all inactive users 
	if (isset($_POST['DELALL'])){
		// users without projects no login more than 90 days
		if (isset($_POST['ACT'])){
			$days = 90;
			$users = $ahpAdmin->getInactiveUsers($days);
			foreach($users as $user){
				if ($user[3] == 0){ // no projects
					$flg = $userDb->deactivateUser($user[1], false);
					if( $userDb->deleteUser($user[1]) && $flg == true)
						$msg .= $user[1] . ", ";
				}
			}
			$msg = "Deleted inactive users without projects: " 
			. ( $msg == "" ? "<span class='err'>none" : "<span class='msg'>" . $msg) 
			. "</span>";
			$userDb->messages = array();
		// users deactivated more than 2 days
		$usersInactive = $ahpAdmin->getInactiveUsers($daysInactive); // update list
		} else {
			$days = 2;
			$users = $ahpAdmin->getInactivatedUsers($days);
			foreach($users as $user){
				if( $userDb->deleteUser($user[1]) == true){
					$msg  .= $user[1] . ", ";
				}
			}
			$msg = "Deleted inactivated users: " 
			. ( $msg == "" ? "<span class='err'>none" : "<span class='msg'>" . $msg) 
			. "</span>";
			$userDb->messages = array();
		}		
	}
	// Deactivate selected user
	if (isset($_POST['DEACT'])){
		// For testing purposes sending of deactivation email can be switched off
		$mail_flag = true;
		if(isset($_POST['chk'])){
			$msg = "<span class='msg'>User(s) "; $err  = "";
			foreach($sel as $i=>$on){
				if( $userDb->deactivateUser($usersInactive[$i][1],$mail_flag) == true){
					$msg .= $usersInactive[$i][1] . ", ";
				} else {
					$err .= $usersInactive[$i][1] . ", ";
				}
			}
			$sel = array();
			$msg .= "deactivated.";
			if ($err !="")
				$msg .= "<span class='err'>" . $err . "could not be deactivated.";
			
		} else {
				if( $userDb->deactivateUser($userName,$mail_flag) == true)
					$msg = "<span class='msg'>User " . $userName . " was deactivated.";
				else
					$err = "User " . $userName . " could not be deactivated.";
		}
		$msg .= ($mail_flag ? " Deactivation email(s) sent." : " No deactivation email sent.</span>");
		// get updated list of inactive users
		$usersInactive = $ahpAdmin->getInactiveUsers($daysInactive);
	}
	// Reactivate selected user
	if (isset($_POST['REACT'])){
		if( $userDb->reactivateUser($userName) == true){
			$msg = "Account of $userName was successfully reactivated";
		} else {
			$msg = "User could not be reactivated. " . $userDb->getErrors();	
		}
	}

} elseif (isset($_POST['CLOSE'])){
		$userName = "";
} elseif (isset($_POST['CLEAN'])){
		if ($userDb->cleanAuditTable())
			$msg = "Clean Audit table successful: " . $userDb->getErrors();	
		else
			$msg = "Error: " . $userDb->getErrors();	
}

	// request to delete comes from user edit - this form has formToken
	if (isset($_POST['user_edit_form_delete'])){
		$userName = $_SESSION['user_name'];
		if( isset($_SESSION['formToken']) && isset($_POST['formToken']) && ($_SESSION['formToken'] == $_POST['formToken'])){
			unset($_SESSION['formToken']);
			if( $userDb->deactivateUser($userName, true) == true){
				$msg = sprintf($login->lgTxt->msg['deact'], $userName);
				$msg .= ( $mail ? $login->lgTxt->msg['deactm'] : ".");
			} else {
				$msg = "Account could not be deleted. ";	
			}
		} else {
			$msg = "Form submission error. ";
		}
	}

// selection list 
$storedUsers = array( 9999 => "select");
$storedUsers = array_merge($storedUsers, $userDb->getUserNames());
if($userName !="select"){
	$i = array_search($userName,$storedUsers);
	if($i >0){
		$storedUsers[$i] = $storedUsers[0];
		$storedUsers[0] = $userName;
	}
}
/* 
 * --- MAIN ---
 */

$regUserCnt = $userDb->getActiveUserCnt();
$uCnt = count($userDb->getLatestUsers( LHRS ));

$webHtml = new WebHtml($title);
include('../../login/' . 'form.login-hl.php');
echo "<h1>$title</h1>";
if($loggedIn) {
	echo "<small><a href='do-log.php'>Log</a>&nbsp;&nbsp;<a href='dbIntegrity.php'>Database</a></small>";
	if(DONATIONS)
		echo "<small>&nbsp;&nbsp;<a href='do-donor-admin.php'>Donations</a></small>";
	echo "<h2>Registered Users</h2>";
	echo "<p class='msg'>AHP-OS has <span class='res'>$regUserCnt</span> registered users. ";
	echo "<span class='res'>$uCnt</span> active users in the last ", LHRS, " hours.</p>";

	// Active users in the last LHRS hours
	$users = $ahpAdmin->getLatestUsers( LHRS );
	$ahpAdmin->displayUserTable($users);

	// Latest user registrations
	$users = $ahpAdmin->getAllUsers( REGDAYS );
	echo "<p>", count($users), " user registrations in the last ", REGDAYS, " days</p>";
	$ahpAdmin->displayUserTable($users);

	// Inactive users in the last n days	
	echo "<h2>Inactive Users</h2>";
	$days = 2;
	$users = "";
	echo "<p>Registered but not activated more than $days days ago (delete)</p>";
	$users = $ahpAdmin->getInactivatedUsers($days);
	$ahpAdmin->displayUserTable($users);
	echo "<p>Last login more than $daysInactive days ago (check checkbox to deactivate)</p>";
	$ahpAdmin->displayUserTable($usersInactive, true, $sel);
	// --- admin menu
	echo "<h2>User Admin Menu</h2>";
	echo "<form id='ua' method='POST' action='$urlAct' name='user_admin_menu'>";
		include '../../login/' . 'form.UserAdminMenu.html';
	echo "</form>";
	// --- show messages and errors
	echo "<p>$msg" . $userDb->getErrors() . "</p>";

	echo "<h2>User Details</h2>";
	if( $userName != "select" && $userName != "" ){
		echo "<p>User details for user <span class='hl'>$userName</span></p>";
		echo "<h3>E-mail, registration and last login</h3>";
		$userDetails = $ahpAdmin->getUserDetails($userName);
		$ahpAdmin->displayUserTable( array($userDetails));
		if( is_numeric($userDetails[3])){
			$ahpDb = new AhpDb();
			echo "<h3>User Activity Level</h3>";
			$counts = $ahpDb->getActivityLevel($userName);
			$alv =  0.35 * (min(200,$counts['dmCnt'])/200);
			$alv += 0.30 * (min(10000,$counts['hCnt'])/10000);
			$alv += 0.20 * (min(20,$counts['pCnt'])/20);
			$alv += 0.15 * (min(300,$counts['aCnt'])/300);
			echo " Activity Index = <span class='res'>",round($alv*100,1), "% </span>: ";
			echo $counts['pCnt']," Projects, ",
			$counts['hCnt'], " Hierarchy Chars, ",
			$counts['dmCnt'], " Participants, ",
			$counts['aCnt']," Account entries" ;
			// Project session table
			echo "<h3>Projects</h3>";
			$ahpDb->displaySessionTable($userName);
		}
		echo "<h4>Activities</h4>";
		$userDb->displayLogTable($userDetails[0], 10);
	} else
		echo "<p>No data.</p>";
} else {
	echo "<p>",$msg," ", $userDb->getErrors(), "</p>";
	echo "<p class='ca' ><a href='" . SITE_URL . "' >Continue</a><p>";
}
$webHtml->webHtmlFooter($version);
