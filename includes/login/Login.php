<?php
/*
 * handles the user login/logout/session
 * Original idea from
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 * @uses phpMailer
 * Modified by Klaus D Goepel
 * Todo: change mailer reference!
 * session() called here when initiating the class!
 *
 * Copyright (C) 2022  <Klaus D. Goepel>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

class Login
{
    public $db_type = DB_TYPE;
    public $db_name = DBNAME;
    public $db_connection = null; // The database connection
    public $passwordResetSet = false;
    public $errors = array();
    public $messages = array();

    public $user_name = "";
    public $refUrl = "";

    private $db_sqlite_path = DB_PATH;
    private $dbUser = DBUSER;
    private $dbPass = DBPASS;
    private $user_id = null;
    private $user_email = "";
    private $user_is_logged_in = false;
    private $password_reset_link_is_valid = false;
    private $password_reset_was_successful = false;
    private $lang;
    /*
     * the function "__construct()" automatically starts whenever an object
     * of this class is created, you know, when you do
     * "$login = new Login();"
     */
    public function __construct($dbname = DBNAME)
    {
        // --- if db names is explicitely given with extension .db, type is set to sqlite
        // --- create/read session
        if (!isset($_SESSION)) {
            session_start();
        }
        global $lang;

        // --- language
        if (isset($_SESSION['lang'])) {
            $this->lang = $_SESSION['lang'];
        } else {
            $this->lang = $lang;
        }
        $class = get_class() . $this->lang;
        $this->lgTxt = new $class();

        if (isset($_GET['logout'])) {
            $this->doLogout();
        // if user has an active session on the server
        } elseif (!empty($_SESSION['user_name'])
                  && ($_SESSION['user_logged_in'] == 1)) {
            $this->loginWithSessionData();

            // checking for form submit from editing screen
            if (isset($_POST["user_edit_submit_name"])) {
                // --- uses use $_SESSION['user_id'] et $_SESSION['user_email']
                $this->editUserName($_POST['user_name']);
            } elseif (isset($_POST["user_edit_submit_email"])) {
                // --- use $_SESSION['user_id'] et $_SESSION['user_email']
                $this->editUserEmail($_POST['user_email']);
            } elseif (isset($_POST["user_edit_submit_password"])) {
                // ---uses $_SESSION['user_name'] and $_SESSION['user_id']
                $this->editUserPassword(
                    $_POST['user_password_old'],
                    $_POST['user_password_new'],
                    $_POST['user_password_repeat']
                );
            }
        } elseif (isset($_COOKIE['rememberme'])) { // --- login with cookie
            $this->loginWithCookieData();

        // if user just submitted a login form
        } elseif (isset($_POST["login"])) {
            if (!isset($_POST['user_rememberme'])) {
                $_POST['user_rememberme'] = null;
            }
            $this->loginWithPostData(
                $_POST['user_name'],
                $_POST['user_password'],
                $_POST['user_rememberme']
            );
        }
        // --- checking if user requested a password reset mail
        // --- changed from user name - email!
        if (isset($_POST["request_password_reset"]) && isset($_POST['user_email'])) {
            if ($this->setPasswordResetDatabaseTokenAndSendMail($_POST['user_email']) == true) {
                $this->passwordResetSet = true;
            }
        } elseif (isset($_GET["user_name"]) && isset($_GET["verification_code"])) {
            $this->checkIfEmailVerificationCodeIsValid($_GET["user_name"], $_GET["verification_code"]);
        } elseif (isset($_POST["submit_new_password"])) {
            $this->editNewPassword(
                $_POST['user_name'],
                $_POST['user_password_reset_hash'],
                $_POST['user_password_new'],
                $_POST['user_password_repeat']
            );
        }
    }


    /*
     * Checks if database connection is opened. If not, then this method tries to open it.
     * @return bool Success status of the database connecting process
     */
    public function databaseConnection()
    {
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using constants config.php
            try {
                if ($this->db_type == 'sqlite') {
                    $this->db_connection = new PDO($this->db_type
                            . ':' . DB_PATH . $this->db_name . ".db");
                } else {
                    // --- MySQL database type - added 2015-10-01
                    $dsn = 'mysql:host=' . DBHOST . ';dbname='
                            . $this->db_name . ';charset=utf8';
                    $this->db_connection = new PDO($dsn, DBUSER, DBPASS);
                }
                return true;
            } catch (PDOException $e) {
                $this->errors[] = $this->lgTxt->err['dbCon']
                    . $e->getMessage(); // MESSAGE_DATABASE_ERROR
            }
        }
        return false;
    }


    /*
     * Search into database for the user data of user_name specified as parameter
     * @return user data as an object if existing user
     * @return false if user_name is not found in the database
     */
    public function getUserData($user_name)
    {
        if ($this->databaseConnection()) {
            $query_user = $this->db_connection->prepare(
                "SELECT * FROM users WHERE user_name = :user_name"
            );
            $query_user->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_user->execute();
            return $query_user->fetch(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }


    /*
     * Search into database for the user data of user_email specified as parameter
     * @return user data as an object if existing user
     * @return false if user_name is not found in the database
     */
    private function getUserfmEmail($user_email)
    {
        if ($this->databaseConnection()) {
            $query_user = $this->db_connection->prepare(
                "SELECT * FROM users WHERE user_email = :user_email;"
            );
            $query_user->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_user->execute();
            return $query_user->fetch(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }


    /*
     * Logs in with S_SESSION data.
     * Technically we are already logged in at that point of time,
     * as the $_SESSION values already exist.
     */
    private function loginWithSessionData()
    {
        $this->user_name = $_SESSION['user_name'];
        $this->user_email = $_SESSION['user_email'];
        $this->user_is_logged_in = true;
    }


    /*
     * Logs in via the Cookie
     * @return bool success state of cookie login
     */
    private function loginWithCookieData()
    {
        if (isset($_COOKIE['rememberme'])) {
            // extract data from the cookie
            list($user_id, $token, $hash)
                = explode(':', $_COOKIE['rememberme']);
            // check cookie hash validity
            if ($hash == hash('sha256', $user_id . ':' . $token
                . COOKIE_SECRET_KEY) && !empty($token)) {
                // cookie looks good, try to select corresponding user
                if ($this->databaseConnection()) {
                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare(
                        "SELECT user_id, user_name, user_email FROM users 
                     WHERE user_id = :user_id    
                     AND user_rememberme_token = :user_rememberme_token 
                     AND user_rememberme_token IS NOT NULL;"
                    );
                    $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $sth->bindValue(':user_rememberme_token', $token, PDO::PARAM_STR);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->user_id)) {
                        // write user data into PHP SESSION [a file on your server]
                        $_SESSION['user_id'] = $result_row->user_id;
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_logged_in'] = 1;

                        // succsessfull login - write last login field - added
                        $sth = $this->db_connection->prepare(
                            "UPDATE users 
                             SET user_last_login = :user_last_login 
                             WHERE user_id = :user_id;"
                        );
                        $sth->bindValue(':user_id', $result_row->user_id, PDO::PARAM_INT);
                        $sth->bindValue(':user_last_login', date("Y-m-d H:i:s"), PDO::PARAM_STR);
                        $sth->execute();

                        // declare user id, set the login status to true
                        $this->user_id = $result_row->user_id;
                        $this->user_name = $result_row->user_name;
                        $this->user_email = $result_row->user_email;
                        $this->user_is_logged_in = true;

                        // Cookie token usable only once
                        $this->newRememberMeCookie();
                        return true;
                    }
                }
            }
            // A cookie has been used but is not valid... we delete it
            $this->deleteRememberMeCookie();
            // MESSAGE_COOKIE_INVALID
            $this->errors[] = $this->lgTxt->err['iCk'];
        }
        return false;
    }


    /*
     * Logs in with the data provided in $_POST, coming from the login form
     * @param $user_name
     * @param $user_password
     * @param $user_rememberme
     */
    private function loginWithPostData($user_name, $user_password, $user_rememberme)
    {
        if (empty($user_name)) {
            // MESSAGE_USERNAME_EMPTY
            $this->errors[] = $this->lgTxt->err['unE'];
        } elseif (empty($user_password)) {
            // MESSAGE_PASSWORD_EMPTY
            $this->errors[] = $this->lgTxt->err['pwE'];

        // if POST data (from login form) contains non-empty
        // user_name and non-empty user_password
        } else {
            // user can login with his username or his email address.
            // if user has not typed a valid email address,
            // we try to identify him with his user_name
            if (!filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                // database query, getting all the info of the selected user
                $result_row = $this->getUserData(trim($user_name));
            // if user has typed a valid email address, we try to identify him with his user_email
            } elseif ($this->databaseConnection()) {
                // database query, getting all the info of the selected user
                $query_user = $this->db_connection->prepare(
                    "SELECT * FROM users WHERE user_email = :user_email;"
                );
                $query_user->bindValue(':user_email', trim($user_name), PDO::PARAM_STR);
                $query_user->execute();
                // get result row (as an object)
                $result_row = $query_user->fetch(PDO::FETCH_OBJ);
            }

            // if this user not exists
            if (! isset($result_row->user_id)) {
                // was MESSAGE_USER_DOES_NOT_EXIST before,
                // TODO: but has changed to MESSAGE_LOGIN_FAILED
                // to prevent potential attackers showing if the user exists

                // MESSAGE_USER_DOES_NOT_EXIST
                $this->errors[] = $this->lgTxt->err['unNe'];
            } elseif (($result_row->user_failed_logins >= 3)
                    && ($result_row->user_last_failed_login > (time() - 30))) {
                // MESSAGE_PASSWORD_WRONG_3_TIMES
                $this->errors[] = $this->lgTxt->err['pwW3'];
            // using PHP 5.5's password_verify() function
            } elseif (! password_verify($user_password, $result_row->user_password_hash)) {
                // increment the failed login counter for that user
                $sth = $this->db_connection->prepare(
                    "UPDATE users SET user_failed_logins = user_failed_logins+1, 
                         user_last_failed_login = :user_last_failed_login 
                         WHERE user_name= :user_name OR user_email= :user_email;"
                );
                $sth->execute(array(
                    ':user_name' => $user_name,
                    ':user_email' => $user_name,
                    ':user_last_failed_login' => time()
                    ));
                // MESSAGE_PASSWORD_WRONG
                $this->errors[] = $this->lgTxt->err['pwW'];
            // has the user activated their account with the verification email
            } elseif ($result_row->user_active != 1) {
                // MESSAGE_ACCOUNT_NOT_ACTIVATED
                $this->errors[] = $this->lgTxt->err['aNact'];
            } else {
                // --- write user data into PHP SESSION [a file on your server]
                $_SESSION['user_id'] = $result_row->user_id;
                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_logged_in'] = 1;

                // --- succsessfull login - write last login field
                $sth = $this->db_connection->prepare(
                    "UPDATE users 
                     SET user_last_login =  :user_last_login 
                     WHERE user_id = :user_id;"
                );
                $sth->bindValue(':user_id', $result_row->user_id, PDO::PARAM_INT);
                $sth->bindValue(':user_last_login', date("Y-m-d H:i:s"), PDO::PARAM_STR);
                $sth->execute();

                // --- declare user id, set the login status to true
                $this->user_id = $result_row->user_id;
                $this->user_name = $result_row->user_name;
                $this->user_email = $result_row->user_email;
                $this->user_is_logged_in = true;

                // --- reset the failed login counter for that user
                $sth = $this->db_connection->prepare(
                    "UPDATE users
                     SET user_failed_logins = 0, user_last_failed_login = NULL 
                     WHERE user_id = :user_id AND user_failed_logins != 0;"
                );
                $sth->execute(array(':user_id' => $result_row->user_id));

                // if user has check the "remember me" checkbox,
                // then generate token and write cookie
                if (isset($user_rememberme)) {
                    $this->newRememberMeCookie();
                } else {
                    // Reset remember-me token
                    $this->deleteRememberMeCookie();
                }

                // OPTIONAL: recalculate the user's password hash
                // DELETE this if-block if you like, it only exists to
                // recalculate users's hashes when you provide a cost factor,
                // by default the script will use a cost factor of 10 and never change it.
                // check if the have defined a cost factor in config/hashing.php
                if (defined('HASH_COST_FACTOR')) {
                    // check if the hash needs to be rehashed
                    if (password_needs_rehash(
                        $result_row->user_password_hash,
                        PASSWORD_DEFAULT,
                        array('cost' => HASH_COST_FACTOR)
                    )
                        ) {
                        // calculate new hash with new cost factor
                        $user_password_hash = password_hash(
                            $user_password,
                            PASSWORD_DEFAULT,
                            array('cost' => HASH_COST_FACTOR)
                        );

                        // TODO: this should be put into another method !?
                        $query_update
                        = $this->db_connection->prepare(
                            "UPDATE users 
                             SET user_password_hash = :user_password_hash 
                             WHERE user_id = :user_id;"
                        );
                        $query_update->bindValue(
                            ':user_password_hash',
                            $user_password_hash,
                            PDO::PARAM_STR
                        );
                        $query_update->bindValue(
                            ':user_id',
                            $result_row->user_id,
                            PDO::PARAM_INT
                        );
                        $query_update->execute();

                        if ($query_update->rowCount() == 0) {
                            // writing new hash was successful.
                            //you should now output this to the user ;)
                        } else {
                            // writing new hash was NOT successful.
                            // you should now output this to the user ;)
                        }
                    }
                }
            }
        }
    }


    /*
     * Create all data needed for remember me cookie connection on client and server side
     */
    private function newRememberMeCookie()
    {
        if ($this->databaseConnection()) {
            // generate 64 char random string and store it in current user data
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->prepare(
                "UPDATE users SET user_rememberme_token = :user_rememberme_token 
                 WHERE user_id = :user_id"
            );
            $sth->execute(
                array(
                    ':user_rememberme_token' => $random_token_string,
                    ':user_id' => $_SESSION['user_id'])
            );

            // generate cookie string that consists of userid,
            // randomstring and combined hash of both
            $cookie_string_first_part = $_SESSION['user_id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;
            // set cookie
            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }


    /*
     * Delete all data needed for remember me cookie connection on client and server side
     */
    private function deleteRememberMeCookie()
    {
        if ($this->databaseConnection()) {
            // Reset rememberme token
            $sth = $this->db_connection->prepare(
                "UPDATE users SET user_rememberme_token = NULL 
                WHERE user_id = :user_id"
            );
            if (isset($_SESSION['user_id'])) {
                $sth->execute(array(':user_id' => $_SESSION['user_id']));
            }
        }
        // set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obivously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }


    /*
     * Perform the logout, resetting the session
     */
    public function doLogout()
    {
        // --- audit trail
        if ($this->databaseConnection() && isset($_SESSION['user_logged_in'])) {
            try {
                $sql = "INSERT INTO audit (a_trg, a_uid, a_un,  a_act)
                VALUES ('U', '" . $_SESSION['user_id'] . "','" . $_SESSION['user_name']
                . "','Logout');";
                $insert = $this->db_connection->prepare($sql);
                $insert->execute();
            } catch (PDOException $e) {
                // MESSAGE_DATABASE_ERROR
                $this->errors[] = $this->lgTxt->err['dbCon'] . $e;
            }
        }
        $this->deleteRememberMeCookie();

        // --- When logout, only delete the $_SESSION parameters set by login:
        // --- user_id, user_name, user_email, user_logged_in
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_logged_in']);
        $this->user_is_logged_in = false;
        // MESSAGE_LOGGED_OUT
        $this->messages[] = $this->lgTxt->msg['lgOut'];
        $this->messages = array_merge($this->messages, $this->errors);
    }


    /*
     * Simply return the current state of the user's login
     * @return bool user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->user_is_logged_in;
    }

    /*
     * Edit the user's name, provided in the editing form
     */
    public function editUserName($user_name)
    {
        // --- prevent database flooding
        $user_name = substr(trim($user_name), 0, 64);

        if (!empty($user_name) && $user_name == $_SESSION['user_name']) {
            // MESSAGE_USERNAME_SAME_LIKE_OLD_ONE
            $this->errors[] = $this->lgTxt->err['unDb'];

        // username cannot be empty and must be azAZ09 and 2-64 characters
        // TODO: maybe this pattern should also be implemented
        // in Registration.php (or other way round)
        } elseif (empty($user_name)
            || !preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $user_name)) {
            // MESSAGE_USERNAME_INVALID
            $this->errors[] = $this->lgTxt->err['unIv'];
        } else {
            // --- check if new username already exists
            $result_row = $this->getUserData($user_name);
            if (isset($result_row->user_id)) {
                // MESSAGE_USERNAME_EXISTS
                $this->errors[] = $this->lgTxt->err['unTk'];
            } else {
                // write user's new data into database
                $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                $query_edit_user_name = $this->db_connection->prepare(
                    "UPDATE users SET user_name = :user_name 
                     WHERE user_id = :user_id;"
                );
                $query_edit_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_edit_user_name->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $query_edit_user_name->execute();

                if ($query_edit_user_name->rowCount()) {
                    $_SESSION['user_name'] = $user_name;
                    // MESSAGE_USERNAME_CHANGED_SUCCESSFULLY
                    $this->messages[] = $this->lgTxt->msg['unCok'] . $user_name;
                } else {
                    // MESSAGE_USERNAME_CHANGE_FAILED
                    $this->errors[] = $this->lgTxt->err['unF'];
                }
            }
        }
    }


    /*
     * Edit the user's email, provided in the editing form
     */
    public function editUserEmail($user_email)
    {
        // prevent database flooding
        $user_email = substr(trim($user_email), 0, 64);

        if (!empty($user_email) && $user_email == $_SESSION["user_email"]) {
            // MESSAGE_EMAIL_SAME_LIKE_OLD_ONE
            $this->errors[] = $this->lgTxt->err['emlD'];
        // user mail cannot be empty and must be in email format
        } elseif (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            // MESSAGE_EMAIL_INVALID
            $this->errors[] = $this->lgTxt->err['emlI'];
        } elseif ($this->databaseConnection()) {
            // --- check if new email already exists
            $query_user = $this->db_connection->prepare(
                "SELECT * FROM users WHERE user_email = :user_email;"
            );
            $query_user->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_user->execute();
            // --- get result row (as an object)
            $result_row = $query_user->fetchObject();

            // --- if this email exists
            if (isset($result_row->user_id)) {
                // MESSAGE_EMAIL_ALREADY_EXISTS
                $this->errors[] = $this->lgTxt->err['emlR'];
            } else {
                // --- write users new data into database
                $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                $query_edit_user_email = $this->db_connection->prepare(
                    "UPDATE users SET user_email = :user_email 
                     WHERE user_id = :user_id;"
                );
                $query_edit_user_email->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                $query_edit_user_email->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $query_edit_user_email->execute();

                if ($query_edit_user_email->rowCount()) {
                    $_SESSION['user_email'] = $user_email;
                    // MESSAGE_EMAIL_CHANGED_SUCCESSFULLY
                    $this->messages[] = sprintf($this->lgTxt->msg['emlCok'], $user_email);
                } else {
                    // MESSAGE_EMAIL_CHANGE_FAILED
                    $this->errors[] = $this->lgTxt->err['emlNc'];
                }
            }
        }
    }


    /*
     * Edit the user's password, provided in the editing form
     */
    public function editUserPassword($user_password_old, $user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) || empty($user_password_repeat) || empty($user_password_old)) {
            // MESSAGE_PASSWORD_EMPTY
            $this->errors[] = $this->lgTxt->err['pwE'];
        } elseif ($user_password_new !== $user_password_repeat) {
            // MESSAGE_PASSWORD_BAD_CONFIRM
            $this->errors[] = $this->lgTxt->err['pwNi'];
        } elseif (strlen($user_password_new) < 6) {
            // MESSAGE_PASSWORD_TOO_SHORT
            $this->errors[] = $this->lgTxt->err['pwS'];
        } else {
            // ---database query, getting hash of currently logged in user
            //    (to check with just provided password)
            $result_row = $this->getUserData($_SESSION['user_name']);

            // --- if this user exists
            if (isset($result_row->user_password_hash)) {

                // using PHP 5.5's password_verify()
                if (password_verify($user_password_old, $result_row->user_password_hash)) {

                    // now it gets a little bit crazy: check if we have a
                    // constant HASH_COST_FACTOR defined (in config/hashing.php),
                    // if so: put the value into $hash_cost_factor,
                    // if not, make $hash_cost_factor = null
                    $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                    // crypt the user's password with the PHP 5.5's password_hash()
                    // function, results in a 60 character hash string
                    // the PASSWORD_DEFAULT constant is defined by the PHP 5.5,
                    // or if you are using PHP 5.3/5.4, by the password hashing
                    // compatibility library. the third parameter looks a little
                    // bit shitty, but that's how those PHP 5.5 functions
                    // want the parameter: as an array with, currently only used with 'cost' => XX.
                    $user_password_hash
                        = password_hash(
                            $user_password_new,
                            PASSWORD_DEFAULT,
                            array('cost' => $hash_cost_factor)
                        );

                    // write users new hash into database
                    $query_update = $this->db_connection->prepare(
                        "UPDATE users 
                         SET user_password_hash = :user_password_hash 
                         WHERE user_id = :user_id;"
                    );
                    $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                    $query_update->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $query_update->execute();

                    // --- check if exactly one row was successfully changed:
                    if ($query_update->rowCount()) {
                        // MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY
                        $this->messages[] = $this->lgTxt->msg['pwCok'];
                    } else {
                        // MESSAGE_PASSWORD_CHANGE_FAILED
                        $this->errors[] = $this->lgTxt->err['pwCf'] ;
                    }
                } else {
                    // MESSAGE_OLD_PASSWORD_WRONG
                    $this->errors[] = $this->lgTxt->err['pwOw'];
                }
            } else {
                // MESSAGE_USER_DOES_NOT_EXIST
                $this->errors[] = $this->lgTxt->err['unNe'];
            }
        }
    }


    /*
     * Sets a random token into the database (that will verify the user when
     * he/she comes back via the link in the email) and sends the according email.
     * changed from $user_name to $user_email
     */
    public function setPasswordResetDatabaseTokenAndSendMail($user_email)
    {
        $user_email = trim($user_email);

        if (empty($user_email)) {
            // MESSAGE_USERNAME_EMPTY
            $this->errors[] = $this->lgTxt->err['unE'];
        } else {
            $temporary_timestamp = time();
            // generate random hash for email password reset verification (40 char string)
            $user_password_reset_hash = sha1(uniqid(mt_rand(), true));
            $result_row = $this->getUserfmEmail($user_email);
            // if this user exists
            if (isset($result_row->user_id)) {
                $user_name = $result_row->user_name;
                if (isset($result_row->user_password_reset_timestamp)
                        && ($temporary_timestamp - $result_row->user_password_reset_timestamp) < 180) {
                    $this->errors[] = "Password reset already requested, please wait for the mail. ";
                    return false;
                }
                // database query:
                $this->db_connection->exec("SET autocommit=0");
                $this->db_connection->exec(
                    ($this->db_type == 'sqlite' ? "BEGIN TRANSACTION;" : "START TRANSACTION;"));

                $query_update = $this->db_connection->prepare(
                    "UPDATE users 
                     SET user_password_reset_hash = :user_password_reset_hash,
                            user_password_reset_timestamp = :user_password_reset_timestamp
                     WHERE user_name = :user_name;"
                );
                $query_update->bindValue(':user_password_reset_hash', $user_password_reset_hash, PDO::PARAM_STR);
                $query_update->bindValue(':user_password_reset_timestamp', $temporary_timestamp, PDO::PARAM_INT);
                $query_update->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_update->execute();

                // check if exactly one row was successfully changed:
                if ($query_update->rowCount() == 1) {
                    // send a mail to the user, containing a link with that token hash string
                    if($this->sendPasswordResetMail($user_name, $result_row->user_email, $user_password_reset_hash))
                    {
                        $this->db_connection->exec("COMMIT;");
                        $this->db_connection->exec("SET autocommit=0");
                        return true;
                    } else {
                        $this->db_connection->exec("ROLLBACK;");
                        $this->db_connection->exec("SET autocommit=0");
                        return false;
                    }

                } else {
                    // MESSAGE_DATABASE_ERROR
                    $this->errors[] = $this->lgTxt->err['dbCon'];
                }
            } else {
                // MESSAGE_USER_DOES_NOT_EXIST
                $this->errors[] = $this->lgTxt->err['unNe'];
            }
        }
        return false;
    }


    /*
     * Sends the password-reset-email.
     */
    public function sendPasswordResetMail($user_name, $user_email, $user_password_reset_hash)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        // --- use SMTP or use mail()
        if (EMAIL_USE_SMTP) {
            // Set mailer to use SMTP
            $mail->IsSMTP();
            // --- useful for debugging, shows full SMTP errors
            // --- $mail->SMTPDebug = 1;
            //     debugging: 1 = errors and messages, 2 = messages only
            // --- Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // --- Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION ;
            }
            // --- Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME ;
            $mail->Password = EMAIL_SMTP_PASSWORD ;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }

        $mail->From = EMAIL_PASSWORDRESET_FROM ;
        $mail->FromName = EMAIL_PASSWORDRESET_FROM_NAME ;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_PASSWORDRESET_SUBJECT ;

        $link = EMAIL_PASSWORDRESET_URL.'?user_name='.urlencode($user_name)
            .'&verification_code='.urlencode($user_password_reset_hash);
        $mail->Body = EMAIL_PASSWORDRESET_CONTENT . ' ' . $link;

        if (!$mail->Send()) {
            // MESSAGE_PASSWORD_RESET_MAIL_FAILED
            $this->errors[] = $this->lgTxt->err['pwCf'] . $mail->ErrorInfo;
            return false;
        } else {
            // MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT
            $this->messages[] = $this->lgTxt->msg['pwRms'];
            return true;
        }
    }


    /*
     * Checks if the verification string in the account verification mail
     * is valid and matches to the user.
     */
    public function checkIfEmailVerificationCodeIsValid($user_name, $verification_code)
    {
        $user_name = trim($user_name);

        if (empty($user_name) || empty($verification_code)) {
            // MESSAGE_LINK_PARAMETER_EMPTY
            $this->errors[] = $this->lgTxt->err['lnkE'];
        } else {
            // database query, getting all the info of the selected user
            $result_row = $this->getUserData($user_name);

            // if this user exists and have the same hash in database
            if (isset($result_row->user_id)
                && $result_row->user_password_reset_hash == $verification_code) {
                $timestamp_one_hour_ago = time() - 3600; // 3600 seconds are 1 hour

                if ($result_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
                    // set the marker to true, making it possible
                    // to show the password reset edit form view
                    $this->password_reset_link_is_valid = true;
                } else {
                    // MESSAGE_RESET_LINK_HAS_EXPIRED
                    $this->errors[] = $this->lgTxt->err['lnkExp'];
                }
            } else {
                // MESSAGE_USER_DOES_NOT_EXIST
                $this->errors[] = $this->lgTxt->err['unNe'];
            }
        }
    }


    /*
     * Checks and writes the new password.
     */
    public function editNewPassword(
        $user_name,
        $user_password_reset_hash,
        $user_password_new,
        $user_password_repeat
    )
    {
        $user_name = trim($user_name);

        if (empty($user_name) || empty($user_password_reset_hash)
            || empty($user_password_new) || empty($user_password_repeat)) {
            // MESSAGE_PASSWORD_EMPTY
            $this->errors[] = $this->lgTxt->err['pwE'];
        } elseif ($user_password_new !== $user_password_repeat) {
            // MESSAGE_PASSWORD_BAD_CONFIRM
            $this->errors[] = $this->lgTxt->err['pwNi'];
        } elseif (strlen($user_password_new) < 6) {
            // MESSAGE_PASSWORD_TOO_SHORT
            $this->errors[] = $this->lgTxt->err['pwS'];
        } elseif ($this->databaseConnection()) {
            // now it gets a little bit crazy: check if we have a constant
            // HASH_COST_FACTOR defined (in config/hashing.php),
            // if so: put the value into $hash_cost_factor, if not, make
            // $hash_cost_factor = null
            $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

            // crypt the user's password with the PHP 5.5's password_hash()
            // function, results in a 60 character hash string
            // the PASSWORD_DEFAULT constant is defined by the PHP 5.5,
            // or if you are using PHP 5.3/5.4, by the password hashing
            // compatibility library. the third parameter looks a little
            // bit shitty, but that's how those PHP 5.5 functions
            // want the parameter: as an array with, currently only used
            // with 'cost' => XX.
            $user_password_hash
                = password_hash(
                    $user_password_new,
                    PASSWORD_DEFAULT,
                    array('cost' => $hash_cost_factor)
                );

            // write users new hash into database
            $query_update = $this->db_connection->prepare(
                "UPDATE users SET user_password_hash = :user_password_hash,
                            user_password_reset_hash = NULL, user_password_reset_timestamp = NULL
                 WHERE user_name = :user_name 
                 AND user_password_reset_hash = :user_password_reset_hash;"
            );
            $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
            $query_update->bindValue(':user_password_reset_hash', $user_password_reset_hash, PDO::PARAM_STR);
            $query_update->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_update->execute();

            // check if exactly one row was successfully changed:
            if ($query_update->rowCount() == 1) {
                $this->password_reset_was_successful = true;
                // MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY
                $this->messages[] = $this->lgTxt->msg['pwCok'];
            } else {
                // MESSAGE_PASSWORD_CHANGE_FAILED
                $this->errors[] = $this->lgTxt->msg['pwCf'];
            }
        }
    }

    /*
     * Gets the success state of the password-reset-link-validation.
     * TODO: should be more like getPasswordResetLinkValidationStatus
     * @return boolean
     */
    public function passwordResetLinkIsValid()
    {
        return $this->password_reset_link_is_valid;
    }

    /*
     * Gets the success state of the password-reset action.
     * TODO: should be more like getPasswordResetSuccessStatus
     * @return boolean
     */
    public function passwordResetWasSuccessful()
    {
        return $this->password_reset_was_successful;
    }

    /*
     * Gets the username
     * @return string username
     */
    public function getUsername()
    {
        return $this->user_name;
    }

    /* Get errors
     * @return string $this->errors
     */
    public function getErrors()
    {
        $msg  = "<span class='msg'>" . implode(" ", $this->messages) . "</span>";
        $msg .= "<br><span class='err'>" . implode(" ", $this->errors) . "</span>";
        return $msg;
    }
}
