<?php
/* Replaces the standard admin class class.LoginAdmin.php 
 * for the AHP package. Methods are extended to show the 
 * users' AHP projects.
 * Methods:
 * public function getUserDetails($user)      Details of $user
 * public function getAllUsers($days)         User registrations of the last $days
 * public function getLatestUsers($hours)     Active users the last $hours
 * public function getInactivatedUsers($days) Inactivated longer than $days: delete them 
 * public function getInactiveUsers($days)    Inactive users for more than $days: deactivate
 * public function displayUserTable($users)   Output data in HTML table
 *
 * Donations -- todo: put in extra class
 * public function getDonor($uid)
 * public function checkDonation($name)
 * public function getDonorDetails($trNo)
 * public function getAllTrNos()
 * public function getAllDonors($yr=0)
 * public function writeUserDonation($trDate, $trId, $trAmnt, $trFee, $trName, $trEmail, $trCmnt, $trUid)
 * public function modifyUserDonation($para)
 * public function displayDonorTable($donors){
 *
 * @version 2015-11-18
 * @version 2017-09-27 last version w/o SVN
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

 */


class AhpAdmin extends LoginAdmin {

	/*** AHP USER ADMINISTRATION ***/

/* Get the user registrations of the last $days 
 * This function joins the number of AHP projects to the user account 
 * @return [0] user_id [1] user_name [2] user_email [3] projectcount
 * [4] reg date [5] last login (d)
 */
	public function getUserDetails($user){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, nullif(count(projects.project_sc),0), 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') - julianday( users.user_last_login ),1) || ' d',1,7) FROM users 
			LEFT JOIN projects 
			ON users.user_name = projects.project_author 
			WHERE users.user_name = :user";

			$mysql = "SELECT user_id, user_name, user_email, count(projects.project_sc),  
			date(users.user_registration_datetime), DATEDIFF(CURDATE(), user_last_login) 
			FROM users 
			LEFT JOIN projects 
			ON users.user_name = projects.project_author  
			WHERE users.user_name = :user;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':user', $user, PDO::PARAM_STR);
			$query->execute();		
			$user = $query->fetch(PDO::FETCH_NUM);
			return $user;
		}
	}


/* Get the user registrations of the last $days 
 * This function joins the number of AHP projects to the user account 
 * same as above for useres registered in the last $days
 */
	public function getAllUsers($days){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, nullif(count(projects.project_sc),0), 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') - julianday( users.user_last_login ),1) || ' d',1,7),
			donations.trUid  
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author 
			WHERE users.user_id != '" . ADMIN_ID . "' 
			AND users.user_active = 1 
			AND julianday( users.user_registration_datetime) > julianday('now', 'localtime', '" . -$days . " days')
			GROUP BY users.user_email ORDER BY julianday(users.user_registration_datetime) DESC;";

			$mysql = "SELECT user_id, user_name, user_email, count(projects.project_sc), users.user_registration_datetime, 
			(CONCAT(DATEDIFF(CURDATE(), user_registration_datetime), ' d')) as days,
			donations.trUid
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author 
			WHERE users.user_active = 1 AND users.user_id != '" . ADMIN_ID . "'
			 AND DATEDIFF(CURDATE(), user_registration_datetime) < " . $days . 
			" GROUP BY users.user_email DESC;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->query($sql);
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get user name, email and number of projects for active users logged in the last $hours hours */
	public function getLatestUsers($hours){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, nullif(count(projects.project_sc),0), 
				date(users.user_registration_datetime), 
				substr( round(24 * (julianday('now') - julianday( users.user_last_login)),1) || '   h',1,8),
				donations.trUid
				FROM users
				LEFT JOIN donations ON users.user_id = donations.trUid 
				LEFT JOIN projects ON users.user_name = projects.project_author 
				WHERE julianday( users.user_last_login) >= julianday('now', '" . -$hours . " hours')
				AND users.user_id != '" . ADMIN_ID ."'
				GROUP BY users.user_email ORDER BY julianday( users.user_last_login ) DESC;";

			$mysql = "SELECT users.user_id, users.user_name, users.user_email, nullif(count(projects.project_sc),0),
				DATE(users.user_registration_datetime), CONCAT(HOUR(TIMEDIFF(NOW(), a_ts)), ':', 
				MINUTE(TIMEDIFF(NOW(), a_ts)), ' h') AS 'last' FROM `audit` 
				LEFT JOIN `users` ON `a_uid` = users.user_id
                LEFT JOIN projects ON users.user_name = projects.project_author 
				WHERE `a_act` = 'User Login'
				AND HOUR(TIMEDIFF(NOW(), user_last_login)) <" . $hours . "
				AND users.user_id != '1' 
				GROUP BY users.user_email ORDER BY audit.a_ts DESC";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->query($sql);
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get users who registered but did not activate account the last $days days
 * This function joins the number of AHP projects to the user account
 */
	function getInactivatedUsers($days){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, nullif(count(projects.project_sc),0), 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') - julianday( users.user_registration_datetime ),1) || ' d',1,7),
			donations.trUid
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author 
			WHERE users.user_id != '" . ADMIN_ID . "' 
				AND users.user_active = 0 
				AND julianday(user_registration_datetime) + " . $days . " - julianday('now', 'localtime') < 0
			GROUP BY users.user_email ORDER BY julianday(users.user_registration_datetime) DESC;";

			$mysql = "SELECT user_id, user_name, user_email, count(projects.project_sc), 
			DATE(users.user_registration_datetime), 
			DATEDIFF(CURDATE(), user_registration_datetime),
			donations.trUid
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author
			WHERE users.user_id != '" . ADMIN_ID . "'  
				AND users.user_active = 0
				AND (DATEDIFF(CURDATE(), user_registration_datetime) >= " . $days .  ")
			GROUP BY users.user_email ORDER BY users.user_registration_datetime DESC;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->execute();
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get inactive users with last login more than $days days ago
 * This function joins the number of AHP projects to the user account 
 * Returns:
 * [0]: user_id
 * [1]: user_name
 * [2]: user_email
 * [3]: NULL or number of projects
 * [4]: user registration date_time
 * [5]: days since last login
 * [6]: NULL or donation uid
 */
	public function getInactiveUsers($days){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, nullif(count(projects.project_sc),0), 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') - julianday( users.user_registration_datetime ),1) || ' d',1,7),
			donations.trUid 
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author 
			WHERE user_id !=" . ADMIN_ID . " 
			 AND max(julianday( users.user_registration_datetime), julianday( users.user_last_login ))
			 < julianday('now', '" . -$days . " day', 'localtime') 
			 AND donations.trUid = NULL 
			 AND user_active != 0
			GROUP BY users.user_email ORDER BY julianday( users.user_last_login ) DESC;";

			$mysql = "SELECT user_id, user_name, user_email, count(projects.project_sc), 
			DATE(users.user_registration_datetime), 
			DATEDIFF(CURDATE(), user_last_login),
			donations.trUid 
			FROM users 
			LEFT JOIN donations ON users.user_id = donations.trUid 
			LEFT JOIN projects ON users.user_name = projects.project_author  
			WHERE user_id !=" . ADMIN_ID . " 
				AND (DATEDIFF(CURDATE(), users.user_last_login) >= " . $days . ")
				AND isnull(donations.trUid)
                AND user_active != 0
			GROUP BY users.user_email ORDER BY users.user_last_login DESC;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->execute();
			$users = $query->fetchAll(PDO::FETCH_NUM);
		return $users;
		}
	}


/* 
 * temp clean audit function: delete update audit entries of deleted users
 * in audit table, keep 'I' and 'D' entry
 */
public function cleanAuditEntries($lmt=100){
	if ($this->dataBaseConnection()){
		// aundv view: a_un entries to be deleted have a_trg = "D"
		$sqlite = "CREATE TEMP VIEW IF NOT EXISTS aundv AS
    		SELECT a_un, a_trg FROM audit WHERE a_trg = 'D' OR a_trg = 'I'
			GROUP BY audit.a_un ORDER BY audit.a_un;";
		$mysql = "CREATE VIEW IF NOT EXISTS aundv AS
    		SELECT a_un, a_trg FROM audit WHERE a_trg = 'D' OR a_trg = 'I'
			GROUP BY audit.a_un ORDER BY audit.a_un;";
		$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
		$query = $this->db_connection->prepare( $sql );
		$query->execute();

		$sql = "SELECT a_un FROM aundv WHERE a_trg = 'D' LIMIT " . $lmt . ";";
		$query = $this->db_connection->prepare( $sql );
		$query->execute();
		$un = $query->fetchall(PDO::FETCH_COLUMN);
		$cnt = count($un);

		if ($cnt>0) {
			$sql = "DELETE FROM audit WHERE a_un = :name AND (a_trg<>'D' AND a_trg<>'I');";
			$query = $this->db_connection->prepare( $sql );
			foreach($un as $user){
				$query->bindValue(':name', $user, PDO::PARAM_STR);
				$query->execute();
			}	
		}
		return $cnt;
	}
}


/* 
 * Checks database integrity - sqlite only
 * @return array of rows with database information
 */
public  function checkDbIntegrity(){
	$res = array();
	$tmp = array();
	$fileList = array();
	// for mariaDb all tables have to be checked
	$tables = array( "users","projects","pwc","alternatives","audit","donations");
	$res = array( "0" => array("Database type: " => $this->db_type));

	if ($this->db_type == 'sqlite'){
		// --- sqlite
		$res[] = array("Filename: " => DB_PATH . DBNAME . ".db");
		$res[] = array("Last access: " => date("M d Y H:i:s.", filemtime(DB_PATH)));
		$res[] = array("Size: " => filesize(DB_PATH . DBNAME . ".db")/1024);
		if ($this->dataBaseConnection()){
			$vers = $this->db_connection->getAttribute( PDO::ATTR_SERVER_VERSION);
			$res[] = array("DB Version: " => $vers);
			$query = $this->db_connection->prepare("PRAGMA integrity_check;");
			$query->execute();
			$res = array_merge($res, $query->fetchAll(PDO::FETCH_ASSOC));
		}	else
			$res[] = array("Integrity check: " => MESSAGE_DATABASE_ERROR);
	} elseif ($this->db_type == 'mysql') {
		// --- mariadb (mysql)
		if ($this->dataBaseConnection()){
			$vers = $this->db_connection->getAttribute( PDO::ATTR_SERVER_VERSION);
			$res[] = array("DB Version: " => $vers);
			$sql = "CHECK TABLE " . implode(",",$tables) . " EXTENDED;";
			$query = $this->db_connection->prepare( $sql );
			$query->execute();
			$tmp = $query->fetchall(PDO::FETCH_ASSOC);
			for($i=0; $i<count($tables); $i++){
				$res[] = array($tmp[$i]['Table'] . ": " => $tmp[$i]['Msg_text']);
			}
		} else
			$res[] = array("Integrity check: " => MESSAGE_DATABASE_ERROR);
	} else {
			$res[] = array("Error: " => $this->db_type_INVALID);
	}
	return $res;
}


/* Displays User Data in a table (called from user-admin.php) 
 * HTML output of users table
 * [0] Id
 * [1] User
 * [2] Email
 * [3] Projects
 * [4] Registration
 * [5] Last
 * [6] trUid (donor)
 * @return void
 */
	public function displayUserTable($users, $sel=false, $marked=array()){
		if(!empty($users)){
			echo "\n<div class='ofl'><table>";
			echo "<tr>","<th>Nr</th><th class='la'>User Id</th>","<th class='la'>User</th>",
			"<th>Projects</th><th>Last</th>", "<th>Registered</th>","<th class='la'>Email</th>","</tr>";
			$i=0;
			foreach ($users as $user){
				echo "\n<tr>";
				if($sel){
				        if($user[6] > 1) {// donation
					        echo "<td class='ca'></td>"; // unchecked for donors
						$i++;
				        } else {		        
					        echo "<td class='ca'><input form='ua' type='checkbox' name='chk[", $i, "]'", 
						        (isset($marked[$i++]) ? "checked='checked' " : ""), "></td>"; // Nr
				        }
				} else {
					echo "<td class='ca'>", ++$i, "</td>"; // Nr
				}
				echo "<td class='ca'><span class='res'>", $user[0], "</span></td>"; // Id
				if($user[6] > 1) // donation
				        echo "<td class = 'hl'>", $user[1], "</td>"; // user
				else
				        echo "<td>", $user[1], "</td>"; // user
				echo "<td class='ca'>", $user[3], "</td>"; // projects
				echo "<td class='nwr'>", $user[5], "</td>"; // last
				echo "<td class='nwr ca'>", $user[4], "</td>"; // registered
				echo "<td class='wbr'>", $user[2], "</td>"; // email
				echo "</tr>";
			}
			echo "</table></div>\n";
		} else
			echo "<p class='err'>No users</p>";
	}


/*
 * Get donation record for user with uid from donations table
 */
	public function getDonor($uid){
		if ($this->dataBaseConnection()){
		$sql  = "SELECT * from donations WHERE donations.trUid = :uid;";
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':uid', $uid, PDO::PARAM_STR);
			$query->execute();
			$donor = $query->fetchall(PDO::FETCH_ASSOC);
			return $donor;
		}
		return array();
	}

	
/* check whether name has made a donation
 * return true, otherwise false
 */
public function checkDonation($name){
	if ($this->dataBaseConnection()){
		$sql = 'SELECT trNo from users
					LEFT JOIN donations ON users.user_id = donations.trUid
					WHERE users.user_name = :name';
		$query = $this->db_connection->prepare($sql);
		$flag =  $query->bindValue(':name', $name, PDO::PARAM_STR);
		$flag &= $query->execute();
		$d = $query->fetch(PDO::FETCH_NUM);
		if ($d[0] != NULL)
			return true;
		else
			return false;
	}
}

	
/*
 * Get donation record for donation with trNo = $trNo
 */
	public function getDonorDetails($trNo){
		if ($this->dataBaseConnection()){
		$sql  = "SELECT trNo, trDate, trId, trAmnt, trCur, trFx, trFee, trName, trEmail, trCmnt, trUid, user_name, user_email 
			FROM donations LEFT JOIN users ON users.user_id = donations.trUid 
			WHERE donations.trNo = :trNo;";
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':trNo', $trNo, PDO::PARAM_STR);
			$query->execute();
			$donor = $query->fetch(PDO::FETCH_ASSOC);
			return $donor;
		}
		return array();
	}


/* 
 * Get list of all transaction numbers trNo
 */
	public function getAllTrNos(){
		if ($this->dataBaseConnection()){
			$sql  = "SELECT trNo FROM donations;";
			$query = $this->db_connection->query($sql);
			$trNos = $query->fetchall(PDO::FETCH_COLUMN);
			return $trNos;
		}
	}


/* Get all donors from donation table
 * Optional: select year:
 *  0 = all
 *  1 = current year
 *  2 = current and last year
 * -1 = last year
 */
	public function getAllDonors($yr=0){
		if ($this->dataBaseConnection()){
			// calculate data range
			$year = date("Y");
			if ($yr == 1) {        // current year
				$ld = $year . "-01-01";
				$ud = $year . "-12-31";
			} elseif ($yr == 2) {  // current and last
				$ld = $year-1 . "-01-01";
				$ud = $year . "-12-31";
			} elseif ($yr == -1) { // last year
				$ld = $year-1 . "-01-01";
				$ud = $year-1 . "-12-31";
			}
			$sql  = "SELECT trNo, trDate, trId, trAmnt, trCur, trFx, trFee, trName, trEmail, trCmnt, trUid, user_name 
				FROM donations
				LEFT JOIN users ON users.user_id = donations.trUid "
				. ($yr != 0 ? 
				"WHERE trDate >= '" . $ld . "' AND trDate <= '" . $ud . "';"
				: ";");
			$query = $this->db_connection->query($sql);
			$donors = $query->fetchAll(PDO::FETCH_ASSOC);
			return $donors;
		}
	}


/* 
 * Insert new donation
 */
 	public function writeUserDonation($trDate, $trId, $trAmnt, $trFee, $trName, $trEmail, $trCmnt, $trUid){
		if ( $this->dataBaseConnection()) {
			$sql= "INSERT INTO donations ( trDate, trId, trAmnt, trFee, trName, trEmail, trCmnt, trUid )
                	           VALUES(:trDate, :trId, :trAmnt, :trFee, :trName, :trEmail, :trCmnt, :trUid);";
    		try {
					$query = $this->db_connection->prepare($sql);
					$flag =  $query->bindValue(':trDate', $trDate, PDO::PARAM_STR);
					$flag &= $query->bindValue(':trId',   $trId,   PDO::PARAM_STR);
					$flag &= $query->bindValue(':trAmnt', $trAmnt, PDO::PARAM_STR);
					$flag &= $query->bindValue(':trFee',  $trFee, PDO::PARAM_STR);
					$flag &= $query->bindValue(':trName', $trName, PDO::PARAM_STR);
					$flag &= $query->bindValue(':trEmail',$trEmail, PDO::PARAM_STR);
					$flag &= $query->bindValue(':trCmnt', $trCmnt,  PDO::PARAM_STR);
					$flag &= $query->bindValue(':trUid',  $trUid, PDO::PARAM_INT);
					$flag &= $query->execute();
	   		} catch (PDOException $e){
					$this->errors[] = "<br>Donation insert failed " . $e;
					return false;
	  		}
			return $flag;
		} 
 	}


/* 
 * Modify existing donation
 */
 	public function modifyUserDonation($para){
		// $trDate, $trId, $trAmnt, $trFee, $trName, $trEmail, $trCmnt, $trUid
		if ( $this->dataBaseConnection()) {
			$sql= "UPDATE donations SET trDate = :trDate , trId = :trId, trAmnt = :trAmnt, trFee = :trFee, trName = :trName, 
					trEmail = :trEmail, trCmnt = :trCmnt, trUid = :trUid 
					WHERE trNo = :trNo;";
    	try {
				$query = $this->db_connection->prepare($sql);
				$query->bindValue(':trDate', $para['trDate'], PDO::PARAM_STR);
				$query->bindValue(':trId',   $para['trId'],   PDO::PARAM_STR);
				$query->bindValue(':trAmnt', $para['trAmnt'], PDO::PARAM_STR);
				$query->bindValue(':trFee',  $para['trFee'], PDO::PARAM_STR);
				$query->bindValue(':trName', $para['trName'], PDO::PARAM_STR);
				$query->bindValue(':trEmail',$para['trEmail'], PDO::PARAM_STR);
				$query->bindValue(':trCmnt', $para['trCmnt'],  PDO::PARAM_STR);
				$query->bindValue(':trUid',  $para['trUid'], PDO::PARAM_INT);
				$query->bindValue(':trNo',  $para['trNo'], PDO::PARAM_INT);
				$query->execute();
				if($query->rowCount() > 0)
					return true;
				else
					return false;
	   	} catch (PDOException $e){
				$this->errors[] = "<br>Donation update failed " . $e;
				return false;
	  	}
		} 
	return false;
 	}


/* 
 * Display table of all donors
 */
	public function displayDonorTable($donors){
		$dsum = 0.;
		$fsum = 0.;
		if(!empty($donors)){
			echo "<div class='ofl'><table>";
			echo "<tr>","<th>Nr</th><th>Date</th>","<th>Name</th>", "<th>E-mail</th>",
			"<th>Amount</th><th>Fee</th>", "<th>User name</th>", "<th>Comment</th>", "</tr>";
			$i = 0;
			foreach ($donors as $donor){
				$style = ($i++%2 ? "class='odd'" : "class='even'");
				$dsum += $donor['trAmnt'];
				$fsum += is_numeric($donor['trFee']) ? $donor['trFee'] : 0.;
				echo "<tr $style>";
				echo "<td class='ca'>", $donor['trNo'], "</td>";
				echo "<td class='nwr'>",$donor['trDate'], "<span class='res'></td>";
				echo "<td>", $donor['trName'], "</td>";
				echo "<td>", $donor['trEmail'], "</td>";
				echo "<td class='ra'>", $donor['trAmnt'], "</td>";
				echo "<td class='ca'>", $donor['trFee'], "</td>";
				echo "<td>", $donor['user_name'], "</td>";
				echo "<td>", $donor['trCmnt'], "</td>";
				echo "</tr>";
			}
			echo "</table></div>";
		echo "<p>Total <span class='res'>" , $i, "</span> donations: 
		<span class='res'>", sprintf("%01.2f",$dsum), "</span> SGD, net: <span class='res'>", sprintf("%01.2f",$dsum - $fsum), "</span> SGD</p>";
		} else
			echo "<p class='err'>No donors</p>";
	}
} // end class AhpAdmin
