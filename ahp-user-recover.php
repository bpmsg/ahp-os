<?php
/*  
 * 	Restore user from the backup database
 *  User will still be deactivated when restored.
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
 *  Revision: $Rev: 86 $
 * 
 * TODO: move all functions to class AhpDb.php
 * 
 */

include 'includes/config.php';

/* 
 * Backup database has to be same type as active database! 
 */
$backupDb = 'ahp-os_bck';

$storedUsers = array();  // users in active database
$deletedUsers = array(); // users in backup db but not in active db
$errMsg = "";
$userName= "";
$ahpUser = array();

$pageTitle ='AHP recover';
$title="AHP-OS User Recovery";
$subTitle = "User recovery from bachup database";
$version = substr('$LastChangedDate: 2022-02-01 17:30:06 +0800 (Tue, 01 Feb 2022) $',18,10);
$rev = trim('$Rev: 86 $', "$");

$login = new Login();
$ahpAdm = new AhpAdmin();

// backup database
$ahpAdmBck = new AhpAdmin($backupDb);
$ahpDbBck =  new AhpDb($backupDb);

// reset in case back from edit form
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);


// get user account data for $user_name
function getAccountData($userName){
	global $ahpAdmBck;
	if($ahpAdmBck->dataBaseConnection()){
		$query = $ahpAdmBck->db_connection->prepare(
			"SELECT * FROM `users` WHERE `user_name` = :user_name"
		);
		$query->bindValue(':user_name', $userName, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch(PDO::FETCH_ASSOC);
	}
}


// get array of all projects from $user
function readAllProjects($userName){
	global $ahpDbBck;
	if($ahpDbBck->databaseConnection()){
		$query = $ahpDbBck->db_connection->prepare(
			"SELECT * FROM `projects` WHERE `project_author` = :user_name"
		);
		$query->bindValue(':user_name', $userName, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
}


// get all pairwise comparisons for project with session code $sc
function readAllPwc($sc){
	global $ahpDbBck;
	$query = $ahpDbBck->db_connection->prepare(
		"SELECT * FROM `pwc` WHERE `project_sc` = :sc"
	);
	$query->bindValue(':sc', $sc, PDO::PARAM_STR);
	$query->execute();
	return $query->fetchAll(PDO::FETCH_ASSOC);
}


// get all alternatives
function readAllAlt($sc){
	global $ahpDbBck;
	$query = $ahpDbBck->db_connection->prepare(
		"SELECT * FROM `alternatives` WHERE `project_sc` = :sc"
	);
	$query->bindValue(':sc', $sc, PDO::PARAM_STR);
	$query->execute();
	return $query->fetchAll(PDO::FETCH_ASSOC);
}


/* 
 * Get complete user data from $userName and return as $ahpUser array 
 */
function getUser($userName){
	global $ahpDbBck;
	$ahpUser = array();

	if($ahpDbBck->databaseConnection()){
		$ahpUser['user'] = getAccountData($userName);
		
		//$ahpUser['projects'] = readAllProjects($userName);
		$query = $ahpDbBck->db_connection->prepare(
			"SELECT * FROM `projects` WHERE `project_author` = :user_name"
		);
		$query->bindValue(':user_name', $userName, PDO::PARAM_STR);
		$query->execute();
		$ahpUser['projects'] = $query->fetchAll(PDO::FETCH_ASSOC);
		
		$pwc = array();
		$a = array();
		foreach($ahpUser['projects'] as $p){
			
			$r = readAllPwc($p['project_sc']);
			if(!empty($r)){
				foreach($r as $row)
					$pwc[]= $row;
			}
			
			$al = readAllAlt($p['project_sc']);
			if (!empty($al)){
				foreach($al as $row)
					$a[] = $row;
			}
		}
		$ahpUser['pwc'] = $pwc;		
		$ahpUser['a'] = $a;
		return $ahpUser;
	}
}


/* 
 * Restore user
 */
function restoreUser($user, $projects, $pwc, $alt=array()){
	global $ahpDb;
	global $err;
	$insState = true;
	if($ahpDb->databaseConnection()){

		$ahpDb->db_connection->exec( "SET autocommit=0");
		$ahpDb->db_connection->exec( (DB_TYPE == 'sqlite' ? "BEGIN TRANSACTION;" : "START TRANSACTION;") );

		// write user data
		try {
			$sql = "INSERT INTO `users` 
			(`user_name`, `user_password_hash`, `user_email`, `user_active`, 
			 `user_registration_ip`, `user_registration_datetime`, `user_last_login`) 
			VALUES( :user_name, :user_password_hash, :user_email, :user_active,
			 :user_registration_ip, :user_registration_datetime, :user_last_login);";	
			$queryIns = $ahpDb->db_connection->prepare($sql);
		} catch (PDOException $e){
			$err[] = MESSAGE_DATABASE_ERROR . $e;
		}
		if(is_object($queryIns)){
		   $queryIns->bindValue(':user_name', $user['user_name'], PDO::PARAM_STR);
		   $queryIns->bindValue(':user_password_hash', $user['user_password_hash'], PDO::PARAM_STR);
		   $queryIns->bindValue(':user_email', $user['user_email'], PDO::PARAM_STR);
		   $queryIns->bindValue(':user_active', 0, PDO::PARAM_INT);
		   $queryIns->bindValue(':user_registration_ip', $user['user_registration_ip'], PDO::PARAM_STR);
		   $queryIns->bindValue(':user_registration_datetime', $user['user_registration_datetime'], PDO::PARAM_STR);
		   $queryIns->bindValue(':user_last_login', $user['user_last_login'], PDO::PARAM_STR);
		   $insState = $queryIns->execute();
		}
		if($insState != true){
			$err[] = "<p>User insert failed</p>";
			$ahpDb->db_connection->exec( "ROLLBACK;");
			return false;
		}
		$user_id = $ahpDb->db_connection->lastInsertId();
		// write projects        
		if(is_array($projects) && count($projects)>0){
			try {
				$sql = "INSERT INTO `projects` 
				(`project_sc`, `project_name`, `project_description`, 
				 `project_hText`, `project_datetime`, `project_author`) 
				VALUES ( :project_sc, :project_name, :project_description, 
				 :project_hText, :project_datetime, :project_author);";
				$queryInsert = $ahpDb->db_connection->prepare($sql);
			} catch (PDOException $e){
					$err[] = DB_PROJECT_WRITE_ERROR . $e;
			}
			if(is_object($queryInsert)){
				foreach($projects as $p){
				// write project data
				$queryInsert->bindValue(':project_sc', $p['project_sc'], PDO::PARAM_STR);
					$queryInsert->bindValue(':project_name', $p['project_name'], PDO::PARAM_STR);
					$queryInsert->bindValue(':project_description', $p['project_description'], PDO::PARAM_STR);
					$queryInsert->bindValue(':project_hText', $p['project_hText'], PDO::PARAM_STR);
					$queryInsert->bindValue(':project_datetime', $p['project_datetime'], PDO::PARAM_STR);
					$queryInsert->bindValue(':project_author', $p['project_author'], PDO::PARAM_STR);
					$insState &= $queryInsert->execute();
					if (!$insState){
						$err[] = DB_PROJECT_WRITE_ERROR;
					}
				}
			} 
		}
		if(!$insState){
			$ahpDb->db_connection->exec( "ROLLBACK;");
			echo "<p>project insert failed</p>";
			return false;
		}
		// write pwc
		if(is_array($pwc) && count($pwc)>0){
			try {
				$sql = "INSERT INTO pwc ( project_sc, pwc_part, pwc_timestamp, pwc_node, pwc_ab, pwc_intense ) 
						VALUES ( :project_sc, :pwc_part, :pwc_timestamp, :pwc_node, :pwc_ab, :pwc_intense );";
				$queryIns = $ahpDb->db_connection->prepare($sql);
			} catch (PDOException $e){
				$err[] = DB_PROJECT_WRITE_ERROR . $e;
			}
			foreach($pwc as $c){
				$queryIns->bindValue(':project_sc', $c['project_sc'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_part', $c['pwc_part'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_timestamp', $c['pwc_timestamp'], PDO::PARAM_INT);
				$queryIns->bindValue(':pwc_node', $c['pwc_node'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_ab', $c['pwc_ab'], PDO::PARAM_STR);
				$queryIns->bindValue(':pwc_intense', $c['pwc_intense'], PDO::PARAM_STR);
				$insState &= $queryIns->execute();
			}
			if(!$insState){
				$ahpDb->db_connection->exec( "ROLLBACK;");
				echo "<p>pwc insert failed</p>";
				return false;
			}
		}
		// write Alternatives
		if(is_array($alt) && count($alt)>0){
			try {
				$ahpDb->db_connection->exec( "PRAGMA foreign_keys = ON;" );
				$sql = "INSERT INTO alternatives ( project_sc, alt) 
						VALUES ( :project_sc, :alt);";
				$queryIns = $ahpDb->db_connection->prepare($sql);
			} catch (PDOException $e){
				$err[] = DB_PROJECT_WRITE_ERROR . $e;
			}
			foreach ($alt as $a){
				$queryIns->bindValue(':project_sc', $a['project_sc'], PDO::PARAM_STR);
				$queryIns->bindValue(':alt', $a['alt'], PDO::PARAM_STR);
				$insState &= $queryIns->execute();
			}
			if(!$insState){
				$ahpDb->db_connection->exec( "ROLLBACK;");
				echo "<p>alt insert failed</p>";
				return false;
			}
		}
		// write audit table
		try {
			$sql = "INSERT INTO audit ( a_trg, a_uid, a_un, a_act) 
					VALUES ( 'R', :a_uid, :a_un, 'Backup recovery' );";
			$queryIns = $ahpDb->db_connection->prepare($sql);
		} catch (PDOException $e){
			$err[] = DB_PROJECT_WRITE_ERROR . $e;
		}
		$queryIns->bindValue(':a_uid',$user_id, PDO::PARAM_INT);
		$queryIns->bindValue(':a_un', $user['user_name'], PDO::PARAM_STR);
		$insState &= $queryIns->execute();
		if(!$insState){
			$ahpDb->databaseConnection->exec( "ROLLBACK;");
			echo "<p>Audit table insert failed</p>";
			return false;
		}
		// --- COMMIT all inserts 
		$ahpDb->db_connection->exec( "COMMIT;");
		$ahpDb->db_connection->exec( "SET autocommit=0");
		return true;
	}
	$errMsg = "<p class='err'>Restore error for $userName, no database connection</p>";
	return false;
}

/* --- MENU ACTIONS --- */

if (isset($_POST['EXIT']) || !$login->isUserLoggedIn()){
	header('HTTP/1.0 200 ok');
	header("Location: " . "includes/login/do/do-user-admin.php");
}
if ( isset($_POST['DEL']) || isset($_POST['OPEN']) || isset($_POST['REACT']) ) {
	$para = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
	if ( filter_has_var(INPUT_POST, 'userName') ){
		$userName = mb_substr(preg_replace('~[^\p{L}\p{N}]++~u','', $para['userName']),0,64);
	}
	// reactivate user
	$ahpDb = new AhpDb(); // productive database

	// TODO: add menu point to download $ahpUser JSON to
	if (isset($_POST['REACT'])){
		$ahpUser = getUser($userName);
		// JSON encoded 
		$ahpJs = json_encode($ahpUser);
		
		// Restore all data to active database
		if (restoreUser($ahpUser['user'], $ahpUser['projects'], $ahpUser['pwc'], $ahpUser['a'])){
			$errMsg = "<p class='msg'><br>Account of $userName was successfully 
				restored with " . count($ahpUser['projects']) . " projects</p>";
		} else {
			$errMsg = "<p class='err'><br>Restore error for $userName ";
			$errMsg .= $ahpDb->getErrors() . "</p>";
		}

		$sessionName = "";
	} else {
		// view 
	}

} elseif (isset($_POST['CLOSE'])){
		$userName = "";
}

$storedUsers  = $ahpAdm->getUserNames();
$deletedUsers = $ahpAdmBck->getUserNames();
$deletedUsers = array_diff($deletedUsers, $storedUsers);
$storedUsers = array_values($deletedUsers);

if($userName !=""){
	$i = array_search($userName,$storedUsers);
	if($i >0){
		$storedUsers[$i] = $storedUsers[0];
		$storedUsers[0] = $userName;
	}
}


/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml($pageTitle);
	include 'includes/login/form.login-hl.php';
	if (!empty($login->errors)) 
		echo $login->getErrors();
	echo "<h1>$title</h1>";
	echo "<h2>$subTitle</h2>";

	echo "<h2>Deleted Users</h2>";
	echo "<p>Prod. database: <span class='var'>" . DBNAME . "</span>";
	echo "<br>Backup database: <span class='var'>$backupDb</span>. <br><span class='var'>",
	count($storedUsers), "</span> Users can be recovered.</p>";
	echo "<h2>AHP User Recovery Menu</h2>";
		include 'views/ahpUserRecoverMenu.html';
	echo $errMsg;
	// --- TODO: add a menu point to export users as JSON file
	//if(!empty($ahpJs))
	//	echo "<p>$ahpJs</p>";

	echo "<h2>User Details</h2>";
	if($userName !=""){
		echo "<p>User details for user <span class='hl'>$userName</span></p>";
		echo "<h3>E-mail, registration and last login</h3>";
		$userDetails = $ahpAdmBck->getUserDetails($userName);
		$ahpAdm->displayUserTable(array($userDetails));

		echo "<h3>Projects</h3>";
		$projects = readAllProjects($userName);
			if(is_array($projects))
				$pcnt = count($projects);
			else
				$pcnt = 0;
		echo "<p class='msg'>User has $pcnt project(s)</p>";
		$ahpDb->displaySessionTable($userName);
	} else
		echo "<p>No data</p>";

$webHtml->webHtmlFooter($version);
