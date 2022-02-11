<?php
/* Program to create all necessary tables for the login package 
 * @version since  2015-11-03
 * @version 2017-09-15 modified pwc node varchar(64) last version w/o SVN
 * @version 2022-01-23 pwc_id key field added, set db_type in config!
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
 
define('DROP_DB_FIRST', false);
define('CREATE_TABLES', true);
define('DROP_DONATION_FIRST', false);
define('CREATE_TRIGGERS', true);
define('DROP_TRIGGER_FIRST', false);

include '../includes/config.php';

$dbConnection = null;
$err = array();

/* --- Create Database --- */
$mysqlDBdrop = "DROP DATABASE IF EXISTS $dbName";
$mysqlDbcreate = "CREATE OR REPLACE DATABASE $dbName CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';";

/* ---  USER ACCOUNT TABLE users --- */
$mysqlUserTable = "CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_active` tinyint(1) NOT NULL DEFAULT '0',
  `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL,
  `user_rememberme_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0',
  `user_last_failed_login` int(10) DEFAULT NULL,
  `user_registration_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_registration_ip` varchar(39) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `user_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
   INDEX(user_name)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqliteUserTable = "CREATE TABLE IF NOT EXISTS users(
	user_id INTEGER PRIMARY KEY,
	user_name varchar(64) NOT NULL UNIQUE,
	user_password_hash varchar(255) NOT NULL,
	user_email varchar(64) NOT NULL UNIQUE,
	user_active tinyint(1) NOT NULL DEFAULT '0',
	user_activation_hash varchar(40) DEFAULT NULL,
	user_password_reset_hash char(40) DEFAULT NULL,
	user_password_reset_timestamp bigint(20) DEFAULT NULL,
	user_rememberme_token varchar(64) DEFAULT NULL,
	user_failed_logins tinyint(1) NOT NULL DEFAULT '0',
	user_last_failed_login int(10) DEFAULT NULL,
	user_registration_datetime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	user_registration_ip varchar(39) NOT NULL DEFAULT '0.0.0.0',
 	user_last_login datetime NOT NULL DEFAULT '0000-00-00 00:00:00');";

/* --- AUDIT TABLE audit --- */
$mysqlAuditTable = "CREATE TABLE IF NOT EXISTS audit (
	a_id int(11) NOT NULL AUTO_INCREMENT,
	a_ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	a_trg char(1) NOT NULL,
	a_uid int(11) NOT NULL,
	a_un varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	a_act varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY ( a_id)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqliteAuditTable = "CREATE TABLE IF NOT EXISTS audit(
	a_id INTEGER PRIMARY KEY, 
	a_ts bigint(20) DEFAULT CURRENT_TIMESTAMP, 
	a_trg char(1) NOT NULL, 
	a_uid int(11) NOT NULL, 
	a_un varchar(64),
	a_act varchar(255)
	);";

/* --- UPDATE TRIGGER after_update_users --- */
$mysqlUpdtTrigger = "CREATE TRIGGER `after_update_users` AFTER UPDATE ON `users`
	FOR EACH ROW 
	BEGIN 
	  DECLARE vaAct varchar(255);
		CASE
			WHEN NEW.user_last_login != OLD.user_last_login AND NEW.user_active = OLD.user_active THEN SELECT 'User login' INTO vaAct;
			WHEN NEW.user_failed_logins != OLD.user_failed_logins THEN SELECT 'Failed login' INTO vaAct;
			WHEN NEW.user_active != OLD.user_active AND NEW.user_active = 0 THEN SELECT 'User deactivated' INTO vaAct;
			WHEN NEW.user_active != OLD.user_active AND NEW.user_active = 1 THEN SELECT 'User activated' INTO vaAct;
			WHEN NEW.user_rememberme_token != OLD.user_rememberme_token THEN SELECT 'Change of rememberme token' INTO vaAct;
			WHEN NEW.user_name != OLD.user_name THEN SELECT CONCAT('New user name: ', NEW.user_name) INTO vaAct;
			WHEN NEW.user_email != OLD.user_email THEN SELECT CONCAT('Change of user email to ', NEW.user_email) INTO vaAct;
			WHEN NEW.user_password_hash != OLD.user_password_hash THEN SELECT 'Password Change' INTO vaAct;
			WHEN NEW.user_password_reset_hash != OLD.user_password_reset_hash THEN SELECT 'Password reset' INTO vaAct;
			ELSE SELECT 'Other' INTO vaAct;
		END CASE;
	  INSERT INTO audit (a_trg, a_uid, a_un,  a_act) VALUES ('U', OLD.user_id, OLD.user_name, vaAct);
	END";

$sqliteUpdtTrigger = "CREATE TRIGGER after_update_users AFTER UPDATE ON users 
	FOR EACH ROW
	BEGIN 
	  INSERT INTO audit (a_trg, a_uid, a_un,  a_act) VALUES ('U', OLD.user_id, OLD.user_name,
	  ( CASE
			WHEN NEW.user_last_login <> OLD.user_last_login AND NEW.user_active = OLD.user_active THEN 'Login'
			WHEN NEW.user_failed_logins <> OLD.user_failed_logins THEN 'Failed login'
			WHEN NEW.user_active <> OLD.user_active AND NEW.user_active = 0 THEN 'User deactivated '
			WHEN NEW.user_active <> OLD.user_active AND NEW.user_active = 1 THEN 'User activated '
			WHEN NEW.user_rememberme_token <> OLD.user_rememberme_token THEN 'Change of rememberme token'
			WHEN NEW.user_name <> OLD.user_name THEN 'New user name'
			WHEN NEW.user_email <> OLD.user_email THEN 'New e-mail'
			WHEN NEW.user_password_hash <> OLD.user_password_hash THEN 'Password change'
			WHEN NEW.user_password_reset_hash <> OLD.user_password_reset_hash THEN 'Password reset'
			ELSE 'other'
	   END)
	  );
	END;";

/* --- INSERT TRIGGER after_insert_users --- 
 * requires DELIMITER $$ when executed in phpMyAdmin
 */
$mysqlInsTrigger = "CREATE TRIGGER after_insert_users 
	AFTER INSERT ON users FOR EACH ROW 
	  BEGIN
		INSERT INTO audit (audit.a_trg, audit.a_uid,audit.a_un,audit.a_act) 
		VALUES ('I', NEW.user_id, NEW.user_name,'New user registration');
	  END;";

$sqliteInsTrigger = "CREATE TRIGGER after_insert_users
	AFTER INSERT ON users FOR EACH ROW 
	  BEGIN 
		INSERT INTO audit (a_trg, a_uid, a_un,  a_act) 
		VALUES ('I', NEW.user_id, NEW.user_name,'New user registration');
	  END;";

/* --- DELETE TRIGGER after_delete_users --- 
 * requires DELIMITER $$ when executed in phpMyAdmin
 */
$mysqlDelTrigger = "CREATE TRIGGER after_delete_users AFTER DELETE ON users FOR EACH ROW
	BEGIN
		INSERT INTO audit(audit.a_trg, audit.a_uid, audit.a_un,audit.a_act) VALUES
		('D', OLD.user_id, OLD.user_name, 'User deleted');
	END;";

$sqliteDelTrigger = "CREATE TRIGGER after_delete_users AFTER DELETE ON users FOR EACH ROW
	BEGIN 
		INSERT INTO audit (a_trg, a_uid, a_un,  a_act) 
		VALUES ('D', OLD.user_id, OLD.user_name,'User deleted');
	END;";

/* ---AHP-OS tables --- */

$mysqlProjectTable = "CREATE TABLE IF NOT EXISTS `projects` (
 `project_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `project_sc` char(6) NOT NULL UNIQUE,
 `project_name` varchar(64) NOT NULL,
 `project_description` text(400) DEFAULT NULL,
 `project_hText` text(6000) NOT NULL,
 `project_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `project_author` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 	CONSTRAINT `fk_prj_author`
 	FOREIGN KEY (project_author)
 	REFERENCES users(user_name) 
 	ON DELETE CASCADE 
 	ON UPDATE CASCADE,
 `project_status` tinyint(1) NOT NULL DEFAULT '1'
 	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqliteProjectTable = "CREATE TABLE IF NOT EXISTS `projects` (
 `project_id` INTEGER PRIMARY KEY,
 `project_sc` char(6) NOT NULL UNIQUE,
 `project_name` varchar(64) NOT NULL,
 `project_description` text(400) DEFAULT NULL,
 `project_hText` text(6000) NOT NULL,
 `project_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `project_author` varchar(64) REFERENCES users(user_name) ON DELETE CASCADE ON UPDATE CASCADE,
 `project_status` tinyint(1) NOT NULL DEFAULT '1' 
);";

$mysqlAlternativesTable = "CREATE TABLE IF NOT EXISTS `alternatives` (
 `project_sc` char(6) NOT NULL,
  CONSTRAINT `fk_alt_sc`
 	FOREIGN KEY (project_sc)
 	REFERENCES projects (project_sc) 
 	ON DELETE CASCADE 
 	ON UPDATE CASCADE,
 `alt` varchar(64) DEFAULT NULL
 	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqliteAlternativesTable = "CREATE TABLE IF NOT EXISTS `alternatives` (
 `project_sc` char(6) REFERENCES projects(project_sc) ON DELETE CASCADE ON UPDATE CASCADE,
 `alt` varchar(64) DEFAULT NULL)";

 /* 
  * Modified 2017-09-15 pwc varchar(64)
  * Modified 2022-01-22 pwc_id autoincrement
  */
$mysqlPwcTable = "CREATE TABLE IF NOT EXISTS `pwc` (
 `project_sc` char(6) NOT NULL,
 	CONSTRAINT `fk_pwc`
 		FOREIGN KEY (project_sc)
 		REFERENCES projects(project_sc) 
 		ON DELETE CASCADE 
 		ON UPDATE CASCADE,
 `pwc_part` varchar(64) NOT NULL,
 `pwc_timestamp` bigint(20) DEFAULT NULL,
 `pwc_node` varchar(64) NOT NULL,
 `pwc_ab` varchar(255) NOT NULL,
 `pwc_intense` varchar(255) NOT NULL,
 `pwc_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqlitePwcTable = "CREATE TABLE IF NOT EXISTS `pwc` (
 `project_sc` char(6) REFERENCES projects(project_sc) ON DELETE CASCADE ON UPDATE CASCADE,
 `pwc_part` varchar(64) NOT NULL,
 `pwc_timestamp` bigint(20) DEFAULT NULL,
 `pwc_node` varchar(64) NOT NULL,
 `pwc_ab` varchar(255) NOT NULL,
 `pwc_intense` varchar(255) NOT NULL,
 `pwc_id` INTEGER PRIMARY KEY
)";
 /* 
  * Added 2017-05-11 Table with donations for bpmsg
  */
$mysqlDonationsTable = "CREATE TABLE IF NOT EXISTS `donations` (
	`trNo` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `trDate` DATE NOT NULL DEFAULT '0000-00-00',
    `trId`   VARCHAR( 30 ),
    `trAmnt` REAL NOT NULL,
    `trCur`  CHAR( 3 ) NOT NULL DEFAULT 'SGD',
    `trFx`   REAL DEFAULT 1.,
    `trFee` REAL,
    `trName` VARCHAR( 64 ) NOT NULL,
    `trEmail` VARCHAR( 64 ) NOT NULL,
    `trCmnt` VARCHAR( 255 ),
    `trUid`  INTEGER
 	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sqliteDonationsTable = "CREATE TABLE IF NOT EXISTS `donations` (
    `trNo`   INTEGER PRIMARY KEY,
    `trDate` DATE NOT NULL DEFAULT '0000-00-00',
    `trId`   VARCHAR( 30 ),
    `trAmnt` REAL NOT NULL,
    `trCur`  CHAR( 3 ) NOT NULL DEFAULT 'SGD',
    `trFx`   REAL DEFAULT ( 1. ),
    `trFee`  REAL,
    `trName` VARCHAR( 64 ) NOT NULL,
    `trEmail` VARCHAR( 64 ) NOT NULL,
    `trCmnt` VARCHAR( 255 ),
    `trUid`  INTEGER
);";

/* Database Connection */
  function databaseConnection(){
  	global $dbConnection;
  	if ($dbConnection != null) { // if connection already exists
    	return true;
  	} else {
      // create a database connection, using the constants from config/config.php
    	try {
				if (DB_TYPE == 'sqlite'){
	      	                        $dbConnection = new PDO( DB_TYPE . ':' . DB_PATH . DBNAME . ".db");
					return true;
 				} elseif (DB_TYPE == 'mysql'){
					$dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8';
                                        $dbConnection = new PDO($dsn, DBUSER, DBPASS);
					return true;
	 			} else {
 					$err[] = "SQL Database type not included";
 					return false;
 				}
   		// If an error is catched, database connection failed
    	} catch (PDOException $e) {
      	$err[] = "Database connection error " . $e->getMessage();
    	}
  	}	
    // default return
  return false;
  }


#$login = new Login();
// reset in case back from edit form
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);

$pageTitle ='AHP database creation';
$title = "AHP database creation";
$subTitle = "Create AHP-OS database tables";
$version = substr('$LastChangedDate: 2022-01-19 16:23:09 +0800 (Wed, 19 Jan 2022) $',18,10);
$rev = trim('$Rev: 35 $', "$");


/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml($pageTitle);
#include '../includes/login/form.login-hl.php';
if (!empty($login->errors)) 
	echo $login->getErrors();
echo "<h1>$title</h1>";
echo "<h2>$subTitle</h2>";

echo "<h2>AHP-OS database, table and trigger creation</h2>";
echo "<p>Database type: <span class='msg'>", DB_TYPE, "</span></p>";

/* --- Create empty file for SQLite DB --- */
if( DB_TYPE == "sqlite"){
        if (file_exists(DB_PATH . DBNAME . ".db" ))
                echo "<p class='err'>Warning: SQLITE database $dbName already exists!<br>"
                . DB_PATH . DBNAME . ".db</p>";
        else {
                touch(DB_PATH . DBNAME . ".db");
                echo "<p class='msg'>SQLITE database file created. <br>"
                . DB_PATH . DBNAME . ".db</p>";
        }        
}

/* --- Exit when no DB connection can be established --- */
if($flag = databaseConnection()){
	echo "<p span='msg'>Database connection established</p>";
} else {
	echo "<p span='err'>Database connection could not be established</p>";
	echo "<p>";
	echo implode($err);
	echo "</p>";
	$webHtml->webHtmlFooter($version);
	exit();
}
echo '<p></p>';

if(DROP_DB_FIRST && DB_TYPE == "mysql"){
	/* --- Drop existing and create new AHP Database --- */
	$sql = $mysqlDBdrop;
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare Drop DB successful";
		$query->execute();
		if($query)   
			echo "<br>SQL Drop DB successful";
	} else
		echo "<br>SQL drop DB <span class='err'>NOT successful</span>";
	echo '<p></p>';
	/* --- create new one --- */
	$sql = $mysqlDbcreate;
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create DB successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create DB successful";
	} else
		echo "<br>SQL create DB <span class='err'>NOT successful</span>";
		echo '<p></p>';
}
/* 
 * --- create all AHP Database tables --- 
 *     users, audit, projects, alternatives, 
 *     pwc,table, donations
 */
if(CREATE_TABLES){

	// --- CREATE USER TABLE ---//
	echo '<h3>Create User Tables</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteUserTable : $mysqlUserTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create user table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create table successful";
	} else
	echo "<br>SQL prepare create table <span class='err'>NOT successful</span>";

	// --- CREATE AUDIT TABLE ---//
	echo '<h3>Create Audit Table</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteAuditTable : $mysqlAuditTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create audit table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create table successful";
	} else
		echo "<br>SQL prepare create audit table <span class='err'>NOT successful</span>";

	// --- CREATE PROJECT TABLE ---//
	echo '<h3>Create Project Table</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteProjectTable : $mysqlProjectTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create project table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create project table successful";
	} else
		echo "<br>SQL prepare create project table <span class='err'>NOT successful</span>";

	// --- CREATE ALTERNATIVE TABLE ---//
	echo '<h3>Create AlternativesTable</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteAlternativesTable : $mysqlAlternativesTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create alternatives table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create alternatives table successful";
	} else
		echo "<br>SQL prepare create alternatives table <span class='err'>NOT successful</span>";

	// --- CREATE PWC TABLE ---//
	echo '<h3>Create PWC Table</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqlitePwcTable : $mysqlPwcTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create pwc table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create pwc table successful";
	} else
		echo "<br>SQL prepare create pwc table <span class='err'>NOT successful</span>";

	// --- CREATE DONATIONS TABLE ---//
	echo '<h3>Create Donations Table</h3>';

	// DROP FIRST EXISTING TABLE
	if(DROP_DONATION_FIRST){
		$sql = 'DROP TABLE IF EXISTS `donations`;';
		$query = $dbConnection->prepare($sql);
		if($query) {
				echo "<br>prepare drop donation successful";
				$query->execute();
				if($query)   
					echo "<br>Drop donations successful";
		} else
				echo "<br>SQL drop donations <span class='err'>NOT successful</span>";
	}

	$sql = ( DB_TYPE == 'sqlite' ? $sqliteDonationsTable : $mysqlDonationsTable);
	$query = $dbConnection->prepare($sql);
	if($query) {
		echo "<br>SQL prepare create Donations table successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create Donations table successful";
	} else
		echo "<br>SQL prepare create donations table <span class='err'>NOT successful</span>";
}

/* --- Create Triggers --- */
if(CREATE_TRIGGERS){
	if(DROP_TRIGGER_FIRST){
		$query = $dbConnection->prepare('DROP TRIGGER IF EXISTS after_delete_users;');	
		$query->execute();
		$query = $dbConnection->prepare('DROP TRIGGER IF EXISTS after_insert_users;');	
		$query->execute();
		$query = $dbConnection->prepare('DROP TRIGGER IF EXISTS after_update_users;');	
		$query->execute();
	}
	// --- CREATE UPDATE TRIGGER ---//
	echo '<h3>Create Update Trigger</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteUpdtTrigger : $mysqlUpdtTrigger);
	$query = $dbConnection->prepare($sql);
	  if($query) {
		echo "<br>SQL prepare create upate trigger successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create update trigger successful";
	  } else
		echo "<br>SQL prepare create update trigger <span class='err'>NOT successful</span>";

	//--- CREATE INSERT TRIGGER ---//
	echo '<h3>Create Insert Trigger</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteInsTrigger : $mysqlInsTrigger);
	$query = $dbConnection->prepare($sql);
	  if($query) {
		echo "<br>SQL prepare create insert trigger successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create insert trigger successful";
	  } else
		echo "<br>SQL prepare create insert trigger NOT successful";

	//--- CREATE DELETE TRIGGER ---//
	echo '<h3>Create Delete Trigger</h3>';
	$sql = ( DB_TYPE == 'sqlite' ? $sqliteDelTrigger : $mysqlDelTrigger);
	$query = $dbConnection->prepare($sql);
	  if($query) {
		echo "<br>SQL prepare create delete trigger successful";
		$query->execute();
		if($query)   
			echo "<br>SQL create delete trigger successful";
	  } else
		echo "<br>SQL prepare create delete trigger NOT successful";
}

echo '<p>Done!</p>';
$webHtml->webHtmlFooter($version);
