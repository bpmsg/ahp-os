<?php
/*  Configuration file for AHP-OS Application
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
 *  along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *  --------------------------------------------------------------------
 * 
 *  The whole program package program should be installed in a folder 
 *  one level below your web root folder. 
 *
 *  TODO: The package is initialized with one admin user "admin", 
 *  pw "admin". After installation please login and change this user 
 *  name and password.
 */


/*  When updating the productive system: 
 *  you can still do testing with your local IP 'MY_IP'
 */
	define('UPDATE', true);
	define('MY_IP',"192.168.1.112");

	// --- ini_set('error_reporting', E_ERROR | E_WARNING);
	ini_set('error_reporting', E_ALL & ~E_NOTICE);
	ini_set('display_errors',1); // Set to 0 for productive env.
	define('DEBUG', false);       // --- Debug on/off

/*  Mail settings */

	define('EMAIL_USE_SMTP', true);
	define('EMAIL_SMTP_HOST', "ssl://mail.example.com");
	define('EMAIL_SMTP_AUTH', true);
	define('EMAIL_SMTP_USERNAME', "webmaster@example.com");
	define('EMAIL_SMTP_PASSWORD', "myownpassword");
	define('EMAIL_SMTP_PORT', 465);
	define('EMAIL_SMTP_ENCRYPTION', "ssl");

/*  Database: "sqlite" or "mysql" 
 */
	define('DB_TYPE', "sqlite");
	define('DBNAME',  "ahp_os");

	define('DBUSER',  "ahp-os"); // for mysql
	define('DBPASS',  "ahp-os-mariaDB-password");


/*  Comment link from your blog and/or contact link, 
 *  comment out if you don't want to provide
 */ 
	// define('CMTLNK', "//example.com/feedback/");     // Comments
	// define('CNTLNK', "//example.com/contact-form/"); // Contact form


/*  Optional - Self registration and registration email.
 *  For intranet you can set CAPTCHA and REGISTRATION_EMAIL to false.
 *  SELFREG, CAPTCHA, REGISTRATION_EMAIL
 */
	define('SELFREG', true); // --- Whether user can self register
	define('CAPTCHA', true); // --- set false for intranet, true for web
	define('REGISTRATION_EMAIL', true);


/*  Spam honeypot - set to '' or modify footer.html if not used 
 */
	define('HONEYPOT', "https://bpmsg.com/tools/academicclub.php");


// -------------------------------------------------------------------
// --- NO NEED TO EDIT ANYTHING BELOW, IF NOT ABSOLUTELY NECESSARY ---
// -------------------------------------------------------------------

	if( UPDATE && $_SERVER['REMOTE_ADDR'] != MY_IP){
		require 'maintenance.html';
		exit;
	}

	// --- DIRECTORIES (PATH)
	define('ABS_PATH', $_SERVER['DOCUMENT_ROOT']);
	define('BASE',
 		substr($_SERVER['SCRIPT_NAME'],0,1+strpos($_SERVER['SCRIPT_NAME'],DIRECTORY_SEPARATOR,1)));

	// --- DOMAIN
	if(isset($_SERVER['HTTP_HOST']))
		define('MY_DOMAIN',$_SERVER['HTTP_HOST']);
	else
		define('MY_DOMAIN',$_SERVER['SERVER_NAME']);

	if( DB_TYPE == "mysql"){
		// provide access data to your mysql database
		define( 'DBHOST', MY_DOMAIN . ":3307");
		define( 'DB_PATH', "na");
		$dbName = DBNAME;
	} else {
		$dbName = DBNAME . '.db';	
		define('DB_PATH', ABS_PATH . BASE . "db" . DIRECTORY_SEPARATOR);
	}

	// --- COOKIES
	define('COOKIE_RUNTIME', 1209600); // 1209600 seconds = 2 weeks
	define('COOKIE_DOMAIN', "." . MY_DOMAIN); // like '.mydomain.com'
	define('COOKIE_SECRET_KEY', 'ZJ|>}Z8e:AH^9lZHo;E9yTpV'); // random value here 

	define('APP',   "AHP-OS");          // Application name
	define('AUTHOR',"Klaus D. Goepel"); // Program Author
	define('ORG',   "BPMSG");           // Company


	// --- PROGRAM LIMITS - TXTMAX, NODE_CNT, LEAF_MAX, LEVEL_MAX are defined in AhpHier.php
	define('WLMAX' , 45);     // Definition for word length of nodes and leafs in ahp
	define('ROUND',   6);     // rounding of results when exporting as csv
	define('CRITMAX',20);     // max number of criteria
	define('ALTAHP', 16);     // max number of alternatives for AHP evaluation
	define('ALTYN',  10);     // max number of alternatives for yes/no evaluation
	define('SESSIONLMT', 50); // max number of sessions
	define('NVAR' , 1000);    // Variation of judgments per node for Monte-Carlo Simulation

	// --- STATISTICS
	define('REGDAYS', 1);     // number of registrations the last REGDAYS days
	define('LHRS', 24);       // users of last LHRS hours
	
	// --- Configuration for: password reset email data
	define('EMAIL_PASSWORDRESET_URL',
		htmlspecialchars('https://' . MY_DOMAIN . BASE . "includes/login/do/do-reset-pw.php"));
	define('EMAIL_PASSWORDRESET_FROM', "webmaster@" . MY_DOMAIN);
	define('EMAIL_PASSWORDRESET_FROM_NAME', "webmaster");
	define('EMAIL_PASSWORDRESET_SUBJECT', APP . " Password reset");
	define('EMAIL_PASSWORDRESET_CONTENT', "Please click on this link to reset your password:");

	// --- Configuration for: verification email data
	define('EMAIL_VERIFICATION_URL', 
		htmlspecialchars('https://' . MY_DOMAIN . BASE .  "includes/login/do/do-register.php"));
	define('EMAIL_VERIFICATION_FROM', "webmaster@" . MY_DOMAIN);
	define('EMAIL_VERIFICATION_FROM_NAME', "webmaster");
	define('EMAIL_VERIFICATION_SUBJECT', APP . " Account activation");
	define('EMAIL_VERIFICATION_CONTENT', "Please click on this link and then login to activate your account:\n");
	define('EMAIL_VERIFICATION_INFO', "\nIf you don't activate and login within the next 24 hours, the activation link and your registration will expire, and you need to register again.");

	// --- Configuration for: reactivation email data
	define('EMAIL_REACTIVATION_SUBJECT', APP . " Account reactivation");
	define('EMAIL_REACTIVATION_CONTENT', "Your " . APP . " user account was deactivated. Click on the link below and login, if you want to reactivate:\n");
	define('EMAIL_REACTIVATION_INFO', "\nIf you don't reactivate within the next 48 hours, your account and user data will be deleted. \nThank You.");

	// --- Configuration for: Hashing strength @see http://stackoverflow.com/q/4443476/1114320
	define('HASH_COST_FACTOR', "10");
	
	// --- Admin, register
	define('ADMIN_ID', 1); // --- user id(s) of administrators for user accounts
	// --- enables additional functions for user donation administrations
	define('DONATIONS', true);
	define('URL_HOME', "//" . MY_DOMAIN) ; // --- Home link url
	define('SITE_URL', "//" . MY_DOMAIN . BASE); // --- Site link url

	if (version_compare(PHP_VERSION, '5.5.0', '<'))
    exit("Sorry, this script does not run on a PHP version smaller than 5.5.0 !");

// --- GLOBALS

	$admin = array(ADMIN_ID); // additional ids can be added here
	$loginHeaderText = "<a href='" . SITE_URL . "'>" . APP . "</a>
	&nbsp;&nbsp;<a href='ahp-news.php'>Latest News</a>";

	// --- Language settings
	$languages = array('en','de','es','pt');
	$lang = "EN"; // default

	$urlAct =  htmlspecialchars($_SERVER['PHP_SELF']);
	$myUrl =   htmlspecialchars('//' . MY_DOMAIN) . $urlAct;

	$hlNoLogin = "<div style='display:block;float:left'><a href='" 
		. URL_HOME . "'>Home</a>&nbsp;&nbsp;<a href='ahp-news.php'>Latest News</a></div>
		<div style='display:block;float:right'><a href='" . SITE_URL . "'>" . APP . "</a>"
		. "</div><div style='clear:both'></div>";

	// --- css style sheet
	$cssUrl =  SITE_URL .'includes/style.css';

	if(DEBUG) $s = microtime(true);

	$urlHome = '//'. MY_DOMAIN . ':' . $_SERVER['SERVER_PORT'] ;		// Home

	$urlAhp  = 'ahp.php';               // AHP start page
	$urlAhpH = 'ahp-hierarchy.php';     // Create/display hierarchy
	$urlAhpC = 'ahp-hiercalc.php';      // AHP calculation for hierarchy
	$urlAlt  = 'ahp-alt.php';	        // Create/display Alternatives
	$urlAhpA = 'ahp-altcalc.php';	    // AHP calculation for Alternatives
	$urlGroupInit = 'ahp-hiergini.php'; // Initiate group sessions
	$urlGroupRes  = 'ahp-group.php';    // Display group results
	$urlGinput    = 'ahp-g-input.php';  // Displays group input data

	$urlSessionAdmin = 'ahp-session-admin.php';   // Project administration
	$urlUserAdmin    = 'includes/login/do/do-user-admin.php'; // User administration

	// --- CLASS LOADER
	spl_autoload_register('appClassLoader');
	function appClassLoader($className){
		$lang = strtolower(substr($className, -2,2));
		$paths = array(
			ABS_PATH . BASE . 'classes'  . DIRECTORY_SEPARATOR,
			ABS_PATH . BASE . 'includes' . DIRECTORY_SEPARATOR . 'login' . DIRECTORY_SEPARATOR,
			ABS_PATH . BASE . 'language' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR
		);
		foreach($paths as $path){
			$file = $path . $className . '.php';
			if (is_readable($file))
				require $file;
		}
	}

	// --- FUNCTIONS

	/* Compares current session id (sid) with $_SESSION['sid']
	 * If $_SESSION['sid'] is not set, starts a new session 
 	* @return string $sid session id
 	*/
	function startNewSession(){
		$sid = session_id();
		if(isset($_SERVER['HTTP_USER_AGENT']))
        		$sid = md5( $sid . $_SERVER['HTTP_USER_AGENT']);
        	else
        	        $sid = md5( $sid . "any rubbish text whatever");
		if( isset($_SESSION['sid'])){
			if( $sid != $_SESSION['sid']){
				unset($_SESSION['sid']);
				die("Invalid session");
			}
		} else {
			$_SESSION['sid'] = $sid;
			$_SESSION['tstart'] = gettimeofday(true);
		}
	return $sid;
	}

	/* Checks session id with stored session id ssd
 	 * @return string $sid if sid is valid
	 * @todo: delete - no longer in use
 	*/
	function checkValidSession(){
		$sid=""; $ssd="";
		$sid = session_id();
		if(	$sid != ""){
			$sid = md5( $sid . $_SERVER['HTTP_USER_AGENT']);
	
			if(isset($_SESSION['sid'])){
				$ssd = $_SESSION['sid'];
			}
			if( $sid == $ssd){
				return $sid;
			}
		}
		closeSession();
		die("INVALID SESSION, SESSION EXPIRED! -&nbsp;&nbsp;<a href=" . SITE_URL . ">Back</a>");
	}

	/* Close session */
	function closeSession(){
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
	}
	
	/* Makes arrays easily readable for debugging purposes */
	function displayArray($array){
		if (is_array($array)){
			echo "<p>";
			foreach($array as $key => $val){
				echo "<span style='color:blue'>$key: </span>";
				if(is_array($val))
					print_r($val);
				else
					echo "$val";
				echo "<br>";
			}
			echo "</p>";
		} else
		echo "<p>$array</p>";
	}
