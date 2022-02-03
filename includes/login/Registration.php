<?php
/**
* Handles the user registration
* @author Panique
* @link http://www.php-login.net
* @link https://github.com/panique/php-login-advanced/
* @license http://opensource.org/licenses/MIT MIT License
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

class Registration
{
    /**
     * @var string Type of used database (currently only SQLite, but feel free to expand this with mysql etc)
     */
    private $db_type = DB_TYPE; //

    /**
     * @var string Path of the database file (create this with _install.php)
     */
    private $db_sqlite_path = DB_PATH;
    /**
     * @var string MySQL database string)
     */
		private $dbUser = DBUSER;
		private $dbPass = DBPASS;

    /**
* @var object $db_connection The database connection
*/
    private $db_connection = null;
    /**
* @var bool success state of registration
*/
    public $registration_successful = false;
    /**
* @var bool success state of verification
*/
    public $verification_successful = false;
    /**
* @var array collection of error messages
*/
    public $errors = array();
    /**
* @var array collection of success / neutral messages
*/
    public $messages = array();

		public $un, $eml, $pw, $pwr;
		private $lang;

    /**
* the function "__construct()" automatically starts whenever an object of this class is created,
* you know, when you do "$login = new Login();"
*/
    public function __construct()
    {
				if(!isset($_SESSION))
	        session_start();
				global $lang;
				$this->lang = $lang;

				$class = 'Login' . $this->lang;
				$this->rgTxt = new $class;

        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST["register"])) {
					if(filter_has_var(INPUT_POST, 'user_name'))
        			$this->un = $_POST['user_name'];
					if(filter_has_var(INPUT_POST, 'user_email'))
        		$this->eml = $_POST['user_email'];
					if(filter_has_var(INPUT_POST, 'user_password_new'))
        		$this->pw = $_POST['user_password_new'];
					if(filter_has_var(INPUT_POST, 'user_password_repeat'))
        		$this->pwr = $_POST['user_password_repeat'];
          $this->registerNewUser($this->un, $this->eml, $this->pw, $this->pwr, $_POST['captcha'], $_POST['formToken']);
        // if we have such a GET request, call the verifyNewUser() method
        } else if (isset($_GET["id"]) && isset($_GET["verification_code"])) {
            $this->verifyNewUser($_GET["id"], $_GET["verification_code"]);
        }
    }

    /**
* Checks if database connection is opened and open it if not
*/
    public function databaseConnection()
    {
        // if connection already exists
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
					if ($this->db_type == 'sqlite'){
	      				$this->db_connection = new PDO($this->db_type . ':' . $this->db_sqlite_path . DBNAME . ".db");
	      			} else {
						// --- MySQL database type - added 2015-10-01
								$dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8';
          			$this->db_connection = new PDO($dsn, $this->dbUser, $this->dbPass);
        			}
        			return true;
        		// If an error is catched, database connection failed
         		} catch (PDOException $e) {
                $this->errors[] = $this->rgTxt->err['dbCon'] . $e->getMessage();
            }
        }
        // default return
        return false;
    }

    /**
* handles the entire registration process. checks all error possibilities, and creates a new user in the database if
* everything is fine
*/
    private function registerNewUser($user_name, $user_email, $user_password, $user_password_repeat, $captcha, $formToken = '')
    {
        // we just remove extra space on username and email
        $user_name = trim($user_name);
        $user_email = trim($user_email);
        if($formToken != $_SESSION['formToken']) {
          $this->errors[] = "Form input error";
        //  provided data validity
        // TODO: check for "return true" case early, so put this first
        } elseif (isset($_SESSION['captcha']) && strtolower($captcha) != strtolower($_SESSION['captcha'])) {
            $this->errors[] = $this->rgTxt->err['wCp'];  // MESSAGE_CAPTCHA_WRONG;
        } elseif (empty($user_name)) {
            $this->errors[] = $this->rgTxt->err['unE'];  // MESSAGE_USERNAME_EMPTY;
        } elseif (empty($user_password) || empty($user_password_repeat)) {
            $this->errors[] = $this->rgTxt->err['pwE'];  // MESSAGE_PASSWORD_EMPTY;
        } elseif ($user_password !== $user_password_repeat) {
            $this->errors[] = $this->rgTxt->err['pwNi']; // MESSAGE_PASSWORD_BAD_CONFIRM;
        } elseif (strlen($user_password) < 6) {
            $this->errors[] = $this->rgTxt->err['pwS'];  // MESSAGE_PASSWORD_TOO_SHORT;
        } elseif (strlen($user_name) > 64 || strlen($user_name) < 2) {
            $this->errors[] = $this->rgTxt->err['unL'];  // MESSAGE_USERNAME_BAD_LENGTH;
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $user_name)) {
            $this->errors[] = $this->rgTxt->err['unIv']; // MESSAGE_USERNAME_INVALID;
        } elseif (empty($user_email)) {
            $this->errors[] = $this->rgTxt->err['emlE']; // MESSAGE_EMAIL_EMPTY;
        } elseif (strlen($user_email) > 64) {
            $this->errors[] = $this->rgTxt->err['emlL']; // MESSAGE_EMAIL_TOO_LONG;
        } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = $this->rgTxt->err['emlI']; // MESSAGE_EMAIL_INVALID;

        // finally if all the above checks are ok
        } else if ($this->databaseConnection()) {
            // check if username or email already exists
            $query_check_user_name = $this->db_connection->prepare('SELECT user_name, user_email FROM users WHERE user_name=:user_name OR user_email=:user_email');
            $query_check_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_check_user_name->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_check_user_name->execute();
            $result = $query_check_user_name->fetchAll();

            // if username or/and email find in the database
            // TODO: this is really awful!
            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {
                    $this->errors[] = ($result[$i]['user_name'] == $user_name) ? 
                    	$this->rgTxt->err['unTk'] : $this->rgTxt->err['emlR'];
                }
            } else {
                // check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
                // generate random hash for email verification (40 char string)
                $user_activation_hash = sha1(uniqid(mt_rand(), true));

                // write new users data into database
                try {
                	$query_new_user_insert = $this->db_connection->prepare('INSERT INTO `users` (`user_name`, `user_password_hash`, `user_email`, `user_activation_hash`, `user_registration_ip`, `user_registration_datetime`)
                	VALUES(:user_name, :user_password_hash, :user_email, :user_activation_hash, :user_registration_ip, :user_registration_datetime)');
              	} catch (PDOException $e){
              			$this->errors[] = MESSAGE_DATABASE_ERROR . $e;
              	}
              	if(is_object($query_new_user_insert)){
                  		$query_new_user_insert->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                  		$query_new_user_insert->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                  		$query_new_user_insert->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                  		$query_new_user_insert->bindValue(':user_activation_hash', $user_activation_hash, PDO::PARAM_STR);
                  		$query_new_user_insert->bindValue(':user_registration_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                  		$query_new_user_insert->bindValue(':user_registration_datetime', date("Y-m-d H:i:s"), PDO::PARAM_STR);
                  		$insert_state = $query_new_user_insert->execute();
			}
//      id of new user - changed to using SQL function lastInsertId
//		  $sth = $this->db_connection->query('SELECT last_insert_rowid()');
//		  $user_id = $sth->fetch(PDO::FETCH_NUM);
//		  $user_id = $user_id[0];
              $user_id = $this->db_connection->lastInsertId();

                 if ($insert_state == true) {
                    if (defined("REGISTRATION_EMAIL") && REGISTRATION_EMAIL ){
                      // send a verification email
                      if ($this->sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {
                          // when mail has been send successfully
                          $this->messages[] = $this->rgTxt->msg['regOk']; // MESSAGE_VERIFICATION_MAIL_SENT;
                          $this->registration_successful = true;
													unset($_SESSION['formToken']);
                      } else {
                          // delete this users account immediately, as we could not send a verification email
                          $query_delete_user = $this->db_connection->prepare('DELETE FROM users WHERE user_id=:user_id');
                          $query_delete_user->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                          $query_delete_user->execute();

                        $this->errors[] = $this->rgTxt->err['emlVns']; // MESSAGE_VERIFICATION_MAIL_ERROR;
                      }
                    } else {
                      // Do verification without e-mail
                      $this->verifyNewUser($user_id, $user_activation_hash);
                    }
                } else {
                    $this->errors[] = $this->rgTxt->err['regF']; // MESSAGE_REGISTRATION_FAILED;
                }
            }
        }
    }

    /*
* sends an email to the provided email address
* @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
*/
    public function sendVerificationEmail($user_id, $user_email, $user_activation_hash)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {
            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }

        $mail->From = EMAIL_VERIFICATION_FROM;
        $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_VERIFICATION_SUBJECT;

        $link = EMAIL_VERIFICATION_URL. '?id=' . urlencode($user_id) . '&verification_code=' . urlencode($user_activation_hash);

        // the link to your register.php, please set this value in config/email_verification.php
        $mail->Body = EMAIL_VERIFICATION_CONTENT.' ' . $link . EMAIL_VERIFICATION_INFO;

        if(!$mail->Send()) {
            $this->errors[] = $this->rgTxt->err['emlNs'] . $mail->ErrorInfo; // MESSAGE_VERIFICATION_MAIL_NOT_SENT
            return false;
        } else {
            return true;
        }
    }

    /**
* checks the id/verification code combination and set the user's activation status to true (=1) in the database
* Added: user_last_login is automatically updated with valid activation
*/
    public function verifyNewUser($user_id, $user_activation_hash)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // try to update user with specified information
            $query_update_user = $this->db_connection->prepare(
            'UPDATE users SET user_active = 1, user_activation_hash = NULL, user_last_login = CURRENT_TIMESTAMP
            WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash');
            $query_update_user->bindValue(':user_id', intval(trim($user_id)), PDO::PARAM_INT);
            $query_update_user->bindValue(':user_activation_hash', $user_activation_hash, PDO::PARAM_STR);
            $query_update_user->execute();

            if ($query_update_user->rowCount() > 0) {
                $this->verification_successful = true;
                $this->messages[] = $this->rgTxt->msg['verOk']; // MESSAGE_VERIFICATION_SUCCESSFUL;
            } else {
                $this->errors[] = $this->rgTxt->err['wVc']; // MESSAGE_REGISTRATION_ACTIVATION_NOT_SUCCESSFUL;
            }
        }
    }
}
