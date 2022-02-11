<?php
/* php extension class for user administration and accounts statistics 
 * @author Klaus D. Goepel
 * @copyright 2015 Klaus D. Goepel
 * @package Login
 * @uses PHPMailer class
 * @since 2015-10-30
 * @version 2016-03-22 - user reactivation added
 * @version 2017-03-13 - getUidFirstLog( $uid) first entry of user in audit table added
 * @version 2017-04-10 - deleteUser: audit entries will be deleted too
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
 *
 * This version works for sqlite and mysql databases
 *
 * Public methods:
 * public function getActiveUserCnt()
 * public function getUserNames()             List of all user names
 * public function getUserDetails($user)      Details of $user
 * public function getAllUsers($days)         User registrations of the last $days
 * public function getLatestUsers($hours)     Active users the last $hours
 * public function getInactivatedUsers($days) Inactivated longer than $days: delete them 
 * public function getInactiveUsers($days)    Inactive users for more than $days: deactivate
 * public function deactivateUser($name, $mail = true) 
 * public function reactivateUser($name)
 * public function deleteUser($name)
 * public function getUserFlow($m = 12)
 * Audit/Log table:
 * public function cleanAuditTable()          Deletes all 'other' entries in the audit table
 * public function displayLogTable($uid = '%', $limit = 5) Output data in HTML table
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
 *
 */
 
define ( "DB_DELETE_ERROR", "Data could not be deleted");
define ( "DB_USER_ACTIVE", "User is still active");
define ( "DB_NOTHING_TO_DELETE", "Nothing to delete");
define ( "DB_USER_DEACTIVATED", "User account successfully deactivated");
define ( "DB_USER_DEACT_ERROR","User deactivation error");
define ( "DB_USER_REACT_ERROR","User reactivation failed");
define ( "DB_USER_NOT_ACTIVE", "User not active or not in database");
define ( "MESSAGE_REACTIVATION_MAIL_SENT", "Reactivation email was sent successfully");

class LoginAdmin extends Login {

/** Methods */
	public function __construct($dbname = DBNAME){
		parent::__construct($dbname);

		// if db names is explicitely given with extension .db, type is set to sqlite 
		if(substr($dbname, -3) == ".db"){
			$this->db_type = "sqlite";
			$this->db_name = substr($dbname,0,strlen($dbname)-3);
		} else
			$this->db_name = $dbname;
	}

/*** USER ADMINISTRATION ***/

/* count number of registered active users 
 * @return number of active users
 */
	public function getActiveUserCnt(){
		if ($this->dataBaseConnection()){
			$sql = 'SELECT  count(user_id) FROM users WHERE user_active = 1';
			$query = $this->db_connection->query($sql);
			$userCnt = $query->fetchAll(PDO::FETCH_COLUMN,'user_active');
			return $userCnt[0];
		}
	}


/* Check if user name exists in database 
 * @return count(user_id) - should be 1
 */
	private function checkUser($name){
		if ($this->dataBaseConnection()){
			$sql = "SELECT count(user_id) FROM users WHERE user_name = :name;";
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->execute();
			$user = $query->fetchAll(PDO::FETCH_NUM);
			return $user[0];
		}
		return 0;
	}


/* get user name list as array in alphbetic order used for user selection in menu */
	public function getUserNames(){
		if ($this->dataBaseConnection()){
			$sql = "SELECT user_name FROM users ORDER BY user_name " ;
			$sql .= ( $this->db_type == 'sqlite' ? "COLLATE NOCASE " : "");
			$sql .= "ASC;";
			$query = $this->db_connection->prepare($sql);
			$query->execute();
			$result = $query->fetchAll(PDO::FETCH_COLUMN );
			if(is_array($result)){
				return $result;
			} else {
				$this->errors[] = "no users";
				return array();
			}
		}
	}


/* Get the user details for $user
 * @return array [0] user_id [1] user_name [2] user_email [3] reg date [4] last login (d)
 */
	public function getUserDetails($user){
		if ($this->dataBaseConnection()){

			$mysql = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime), 
			TIMEDIFF(NOW(), user_last_login)
			FROM users WHERE users.user_name = :user";

			$sqlite = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') 
			- julianday( users.user_last_login ),1) || ' d',1,7) 
			FROM users WHERE users.user_name = :user";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':user', $user, PDO::PARAM_STR);
			$query->execute();		
			$user = $query->fetch(PDO::FETCH_NUM);
			return $user;
		}
	}


/* Get the user registrations of the last $days 
 * @return array [0] user_id [1] user_name [2] user_email [3] reg date [4] last login (d)
 */
	public function getAllUsers($days){
		if ($this->dataBaseConnection()){

			$mysql = "SELECT user_id, user_name, user_email, 
			user_registration_datetime,
			(CONCAT(DATEDIFF(CURDATE(), user_registration_datetime), ' d')) as days 
			FROM users WHERE DATEDIFF(CURDATE(), user_registration_datetime) < " . $days .
			" GROUP BY user_email ORDER BY user_registration_datetime DESC;";

			$sqlite = "SELECT user_id, user_name, user_email, 
			user_registration_datetime, 
			substr( round(julianday('now', 'localtime') 
			- julianday( users.user_last_login ),1) || ' d',1,7) 
			FROM users WHERE users.user_active = 1 
			AND julianday( users.user_registration_datetime) 
			> julianday('now', 'localtime', '" . -$days . " days') 
		    GROUP BY users.user_email 
		    ORDER BY users.user_registration_datetime DESC;";
		    
			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->query($sql);
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get user name, email and number of projects for active users logged in the last $hours hours */
	public function getLatestUsers($hours){
		if ($this->dataBaseConnection()){

			$sqlite = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime), 
			substr( round(24 * (julianday('now') 
			- julianday( users.user_last_login)),1) || '   h',1,8) AS 'last'
			FROM users 
			WHERE julianday( users.user_last_login) 
			> julianday('now', '" . -$hours . " hours') 
			GROUP BY users.user_email 
			ORDER BY julianday( users.user_last_login ) DESC;";
			
			$mysql = "SELECT user_id, user_name, user_email, 
			DATE(user_registration_datetime), 
			CONCAT(HOUR(TIMEDIFF(NOW(), user_last_login)), ':', 
			MINUTE(TIMEDIFF(NOW(), user_last_login)), ' h') AS 'last'
			FROM users 
			WHERE HOUR(TIMEDIFF(NOW(), user_last_login)) < " . $hours . " 
			GROUP BY user_email 
			ORDER BY user_last_login DESC;";
			
			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->query($sql);
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get users who registered but did not activate account the last $days days */
	public function getInactivatedUsers($days){
		if ($this->dataBaseConnection()){

			$mysql = "SELECT user_id, user_name, user_email,
			user_registration_datetime, 
			DATEDIFF(CURDATE(), user_registration_datetime)  
			FROM users WHERE users.user_active = 0 
			AND (DATEDIFF(CURDATE(), user_registration_datetime) >= " . $days . ") 
			GROUP BY user_email 
			ORDER BY user_registration_datetime DESC;";

			$sqlite = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') 
			- julianday( users.user_registration_datetime ),1) || ' d',1,7) 
			FROM users WHERE users.user_active = 0 
			AND max(julianday( users.user_registration_datetime), 
			julianday( users.user_last_login ))
			 < julianday('now', '" . -$days . " day', 'localtime') 
			 GROUP BY users.user_email 
			 ORDER BY julianday(users.user_registration_datetime) DESC;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->execute();
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}


/* Get inactive users with last login $days days ago */
	public function getInactiveUsers($days){
		if ($this->dataBaseConnection()){

			$mysql = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime),
			DATEDIFF(CURDATE(), user_last_login)
			FROM users 
			WHERE (DATEDIFF(CURDATE(), user_last_login) >= " . $days .") 
			GROUP BY users.user_email 
			ORDER BY user_last_login DESC;";

			$sqlite = "SELECT user_id, user_name, user_email, 
			date(users.user_registration_datetime), 
			substr( round(julianday('now', 'localtime') 
			- julianday( users.user_registration_datetime ),1) || ' d',1,7)
			FROM users 
			WHERE max(julianday( users.user_registration_datetime), 
			julianday( users.user_last_login ))
			 < julianday('now', '" . -$days . " day', 'localtime') 
			GROUP BY users.user_email 
			ORDER BY julianday( users.user_last_login ) DESC;";

			$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
			$query = $this->db_connection->prepare($sql);
			$query->execute();
			$users = $query->fetchAll(PDO::FETCH_NUM);
			return $users;
		}
	}

	
/* Deactivate user and optionally send email to reactivate his account 
 * @uses PHP Mailer (send reactivation email)
 * user_active status will be set to 0, a new user registration hash is set, and registration
 * datetime is updated to the current datetime. An re-activation email is sent to the user.
 * @param $name user name
 * @param $mail bool, whether to send activation email
 * @return true if successful, false otherwise
 */
	public function deactivateUser($name, $mail = true){
		if ( $this->dataBaseConnection() ){
		// first get user id and email for name

			$sql = "SELECT user_id, user_email, 
			user_registration_datetime FROM users 
			WHERE user_name = :name 
			AND user_active = 1;";

			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->execute();
			$user = $query->fetch(PDO::FETCH_ASSOC);
			if( empty($user) ){
				$this->errors[] = DB_USER_NOT_ACTIVE;
				return false;
			}
			// generate random hash for reactivation (40 char string)
			$hash = sha1(uniqid(mt_rand(), true));
			$actv = 0;
			$dttm = date("Y-m-d H:i:s");
			$this->db_connection->exec(
			 ($this->db_type == 'sqlite' ? "BEGIN TRANSACTION;" : "START TRANSACTION;") );

			$sql = "UPDATE users 
			SET user_active = :actv, user_activation_hash = :hash, 
			user_registration_datetime = :dttm  
		 	WHERE user_name = :name;";

			try {
				$update = $this->db_connection->prepare($sql);
				$update->bindValue(':actv', $actv, PDO::PARAM_INT);
				$update->bindValue(':hash', $hash, PDO::PARAM_STR);
				$update->bindValue(':dttm', $dttm, PDO::PARAM_STR);
				$update->bindValue(':name', $name, PDO::PARAM_STR);
				$updateState = $update->execute();
			} catch( PDOException $e ){
				$this->errors[] = MESSAGE_DATABASE_ERROR . $e;
			return false;
			}
			if($updateState){
				if($mail){
					// send reactivation email
					if ( $this->sendReactivationEmail( $user['user_id'], 
					$user['user_email'], $hash) ) {
						$this->db_connection->exec( "COMMIT;" );
						// when mail has been send successfully
						return true;
					} else {
					// rollback because email could not be sent
						$this->db_connection->exec( "ROLLBACK;" );
					}
				} else {
					$this->db_connection->exec( "COMMIT;" );
					return true;
				}
    	} 
		}
		$this->errors[] = DB_USER_DEACT_ERROR;
		return false;
	}	


/* Reactivate inactive user 
 * user_active status will be set to 1 and user registration hash will be set to Null
 * @param $name user name
 * @return true if successful, false otherwise
 */
	public function reactivateUser($name){
		if ( $this->dataBaseConnection() ){
		// first get user id and email for name

			$sql = "SELECT user_id, user_email, 
			user_registration_datetime FROM users 
			WHERE user_name = :name 
			AND user_active = 0;";

			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->execute();
			$user = $query->fetch(PDO::FETCH_ASSOC);
			if( empty($user) ){
				$this->errors[] = DB_USER_ACTIVE;
				return false;
			}
			// generate random hash for reactivation (40 char string)
			$hash = "NULL";
			$actv = 1;
			$dttm = date("Y-m-d H:i:s");
			$this->db_connection->exec(
			 ($this->db_type == 'sqlite' ? "BEGIN TRANSACTION;" : "START TRANSACTION;") );
			$sql = "UPDATE users 
			SET user_active = :actv, user_activation_hash = :hash, 
			user_registration_datetime = :dttm  
		 	WHERE user_name = :name;";
			try {
				$update = $this->db_connection->prepare($sql);
				$update->bindValue(':actv', $actv, PDO::PARAM_INT);
				$update->bindValue(':hash', $hash, PDO::PARAM_STR);
				$update->bindValue(':dttm', $dttm, PDO::PARAM_STR);
				$update->bindValue(':name', $name, PDO::PARAM_STR);
				$updateState = $update->execute();
			} catch( PDOException $e ){
				$this->errors[] = MESSAGE_DATABASE_ERROR . $e;
			return false;
			}
			if($updateState){
					$this->db_connection->exec( 'COMMIT;' );
					return true;
			}
		}
		$this->errors[] = DB_USER_REACT_ERROR;
		return false;
	}	


/* sends an reactivation email to the provided email address 
* @return boolean gives back true if mail has been sent, 
* gives back false if no mail could been sent
*/
  private function sendReactivationEmail($user_id, $user_email, $user_activation_hash){
		$mail = new PHPMailer\PHPMailer\PHPMailer();
    if (EMAIL_USE_SMTP) {
    	// Set mailer to use SMTP
      $mail->IsSMTP();
      $mail->SMTPAuth = EMAIL_SMTP_AUTH;
      if (defined(EMAIL_SMTP_ENCRYPTION)) {
      	$mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
      }
      $mail->Host = EMAIL_SMTP_HOST;
      $mail->Username = EMAIL_SMTP_USERNAME;
      $mail->Password = EMAIL_SMTP_PASSWORD;
      $mail->Port = EMAIL_SMTP_PORT;
    } else {
			// php built in mail function
      $mail->IsMail();
    }
    $mail->From = EMAIL_VERIFICATION_FROM;
    $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
    $mail->AddAddress($user_email);
    $mail->Subject = EMAIL_REACTIVATION_SUBJECT;
    $link = EMAIL_VERIFICATION_URL.'?id='.urlencode($user_id)
    .'&verification_code='.urlencode($user_activation_hash);

    // the link to your register.php, please set this value in config/email_verification.php
    $mail->Body = EMAIL_REACTIVATION_CONTENT.' ' . $link . EMAIL_REACTIVATION_INFO;

    if(!$mail->Send()) {
    	$this->errors[] = "Reactivation email could not be sent: " . $mail->ErrorInfo;
    	return false;
    } else {
      return true;
    }
  }


/* delete user with user name $name only when user was deactiveted before */
	public function deleteUser($name){
		$allUsers = $this->getUserNames();
		if( in_array($name, $allUsers) ){
			// check whether user is deactivated
			$sql = "SELECT user_active FROM users WHERE user_name = :name;";	
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->execute();
			$active = $query->fetch(PDO::FETCH_ASSOC);
			if($active['user_active'] == 1 ){		
				$this->errors[] = DB_USER_ACTIVE;
				return false;
			}
    		$this->db_connection->exec( "PRAGMA foreign_keys = ON;" );
			$sql = "DELETE FROM users WHERE user_name = :name;";	
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$deleteStatus = $query->execute();
			// delete entries from audit table
			$sql = "DELETE FROM audit WHERE a_un = :name 
			AND (a_trg<>'D' AND a_trg<>'I') ;";	
			$query = $this->db_connection->prepare($sql);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$deleteStatus &= $query->execute();
			
			if ($deleteStatus == false){
				$this->errors[] = DB_DELETE_ERROR;
				return false;
			} else{
				$this->messages[] 
					= "User " . $name . " successfully deleted.";
				return true;
			}
		} else {
			$this->errors[] = DB_NOTHING_TO_DELETE;
			return false;
		}
	}


/* --- Audit table functions --- */

/* get log data from user with id $uid from the audit table
 * date, time, a_id, a_un, activity
 */
	private function getUidLogData( $uid, $limit){
		if ( $this->dataBaseConnection()){

			$sqlite = "SELECT a_id, date(datetime(a_ts, 'localtime')) AS 'date',
			time(datetime(a_ts, 'localtime')) as 'time', 
			a_uid, a_un, a_act as 'activity' FROM audit 
			WHERE a_uid LIKE :uid 
			AND a_act != 'other' 
			ORDER BY a_id DESC LIMIT " . $limit . ";";

			$mysql = "SELECT a_id, date(a_ts) AS 'date', time(a_ts) as 'time', 
			a_uid, a_un, a_act as 'activity' FROM audit 
			WHERE a_uid LIKE :uid 
			AND a_act != 'other' 
			ORDER BY a_id DESC LIMIT " . $limit . ";";

			$query = $this->db_connection->prepare( ($this->db_type == 'sqlite' ? $sqlite : $mysql));
			$query->bindValue(':uid', $uid, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		}
		return array();
	}


/* get first log entrance from user with id $uid from the audit table
 * date, time, a_id, a_un, activity
 */
	private function getUidFirstLog( $uid){
		if ( $this->dataBaseConnection()){

			$sqlite = "SELECT a_id, date(datetime(a_ts, 'localtime')) AS 'date', 
			time(datetime(a_ts, 'localtime')) as 'time', 
			a_uid, a_un, a_act as 'activity' FROM audit 
			WHERE a_uid LIKE :uid 
			AND a_act != 'other' 
			ORDER BY a_id ASC LIMIT 1;";

			$mysql = "SELECT a_id, date(a_ts) AS 'date', time(a_ts) as 'time', 
			a_uid, a_un, a_act as 'activity' FROM audit 
			WHERE a_uid LIKE :uid 
			AND a_act != 'other' 
			ORDER BY a_id ASC LIMIT 1;";

			$query = $this->db_connection->prepare( ($this->db_type == 'sqlite' ? $sqlite : $mysql));
			$query->bindValue(':uid', $uid, PDO::PARAM_STR);
			$query->execute();
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			return $result[0];
		}
		return array();
	}


/* clean audit table: remove all "other" entries from the table */
	public function cleanAuditTable(){
		if ( $this->dataBaseConnection()){
			$query = 
				$this->db_connection->prepare("SELECT count(a_id) FROM audit;");
			$query->execute();
			$tot = $query->fetch(PDO::FETCH_COLUMN);
			$this->messages[] = $tot . " entries total, ";
			$sql = "DELETE FROM audit WHERE a_act = 'Other';";
			$query = $this->db_connection->prepare($sql);
			$deleteStatus = $query->execute();
			if ($deleteStatus == false){
				$this->errors[] = DB_DELETE_ERROR;
				return false;
			} else {
				$query = 
					$this->db_connection->prepare("SELECT count(a_id) FROM audit;");
				$query->execute();
				$tot -= $query->fetch(PDO::FETCH_COLUMN);
				$this->messages[] = $tot . " entries deleted.";
				return true;
			}
		}
	}


/* 
 * Get net user flow (registrations - deletions) over the last $m months
 * Called from dbIntegrity
 * 
 */
public function getUserFlow($m = 12){
	$mnth = array(); // array with $m months as key
	for($i=0; $i<$m; $i++)
		$mnth[] = date("Y-m",strtotime( date( 'Y-m-01')." -$i months"));

	if ($this->dataBaseConnection()){
		$reg = array();

		// --- Audit trigger "I" = new registration
		$sqlite = "SELECT count(audit.a_trg) as 'I' FROM audit 
		WHERE audit.a_ts <= date('now') 
			AND audit.a_ts > date('now','start of month','" . -$m . " month') 
			AND (audit.a_trg = 'I' )
		GROUP BY strftime('%Y-%m', audit.a_ts)
		ORDER by strftime('%Y-%m', audit.a_ts) DESC
		LIMIT " . $m . ";";
		
		$mysql = "SELECT count(audit.a_trg) as 'I' FROM audit 
		WHERE audit.a_ts <= CURRENT_TIMESTAMP() 
			AND audit.a_ts > (CURRENT_TIMESTAMP + INTERVAL " . -$m . " MONTH)
            AND (audit.a_trg = 'I' )
        GROUP BY DATE_FORMAT(audit.a_ts, '%Y-%m')
        ORDER by DATE_FORMAT(audit.a_ts, '%Y-%m') DESC
        LIMIT " . $m . ";";

		$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
		$query = $this->db_connection->prepare( $sql );
		$query->execute();
		$tmp = array_fill(0,$m,0);
		$tmp = array_replace($tmp, $query->fetchall(PDO::FETCH_COLUMN));
		$reg['I'] = array_combine($mnth, $tmp);

		// --- Audit trigger "D" = deletions
		$sqlite = "SELECT count(audit.a_trg) as 'D' FROM audit 
		WHERE audit.a_ts <= date('now') 
			AND audit.a_ts > date('now','start of month','" . -$m . " month') 
			AND (audit.a_trg = 'D' )
		GROUP BY strftime('%Y-%m', audit.a_ts)
		ORDER by strftime('%Y-%m', audit.a_ts) DESC
		LIMIT " . $m . ";";

		$mysql = "SELECT count(audit.a_trg) as 'D' FROM audit 
		WHERE audit.a_ts <= CURRENT_TIMESTAMP() 
			AND audit.a_ts > (CURRENT_TIMESTAMP + INTERVAL " . -$m . " MONTH)
            AND (audit.a_trg = 'D' )
        GROUP BY DATE_FORMAT(audit.a_ts, '%Y-%m')
        ORDER by DATE_FORMAT(audit.a_ts, '%Y-%m') DESC
        LIMIT " . $m . ";";

		$sql = ( $this->db_type == 'sqlite' ? $sqlite : $mysql);
		$query = $this->db_connection->prepare( $sql );
		$query->execute();
		$tmp = array_fill(0,$m,0);
		$tmp = array_replace($tmp, $query->fetchall(PDO::FETCH_COLUMN));
		$reg['D'] = array_combine($mnth, $tmp);
			
		return $reg;
	}
}


/* HTML output of audit log 
*  either all users (%) or for specific user id
*/
	public function displayLogTable($uid = '%', $limit = 5){
	  $log =   $this->getUidLogData($uid, $limit);
	  echo "\n<div class='ofl'>\n<table>";
	  echo "<tr><th>No</th><th>Date</th><th>Time</th><th>Id</th><th>Name</th><th>Activity</th></tr>";
	  $stl = "even";
	  $i=0;
		$ld = "";
	  foreach( $log as $entry){
			$ldat = ($entry['date'] == $ld ? "" : $entry['date']);
	  	echo "\n<tr class='$stl'>
	  		<td>", $entry['a_id'],"</td>
	  		<td>", $ldat, "</td>
	  		<td>", $entry['time'],"</td>
	  		<td>", $entry['a_uid'],"</td>
	  		<td>", $entry['a_un'],"</td>
	  		<td>", $entry['activity'], "</td>
	  		</tr>";
	    $stl = ( $i++ % 2 ? "odd" : "even");
	    $ld =  $entry['date'];
	  }	
	  $first = $this->getUidFirstLog( $uid);
	  echo "\n<tr class='$stl'><td colspan='6'>
	  First log entry of " . $first['a_un'] 
	  . ": <i>" 
	  . $first['activity'] 
	  . "</i> on " . $first['date'] 
	  . "</td></tr>";

	  echo "</table></div>\n";
	}


/* HTML output of user data in a table */
	public function displayUserTable($users){
		if(!empty($users)){
			echo "<table>";
			echo "<tr>","<th>Nr</th><th>User Id</th>","<th>User</th>", "<th>E-mail</th>",
			"<th>Registration</th>", "<th>Last</th>","</tr>";
			$i = 0;
			foreach ($users as $user){
				$style = ($i++%2 ? "class='odd'" : "class='even'");
				echo "<tr $style>";
				echo "<td style='text-align:right;'>", $i, "</td>";
				echo "<td style='text-align:center;'>", $user[0], "<span class='res'></td>";
				echo "<td>", $user[1], "</td>";
				echo "<td>", $user[2], "</td>";
				echo "<td>", $user[3], "</td>";
				echo "<td style='text-align:center;'>", $user[4], "</td>";
				echo "</tr>";
			}
			echo "</table>";
		} else
			echo "<p class='err'>No users</p>";
	}

} // end class Login-admin
