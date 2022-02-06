<?php
/*
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
include ('../../config.php');

require_once('../../PHPMailer/PHPMailer.php'); // Mailer
require_once('../../PHPMailer/SMTP.php'); // Mailer
require_once('../../PHPMailer/Exception.php'); // Mailer

$title="User Registration";
$version = substr('$LastChangedDate: 2022-02-06 16:09:31 +0800 (Sun, 06 Feb 2022) $',18,10);
$rev = trim('$Rev: 110 $', "$");

session_start();

if(isset($_COOKIE['lang'])  && in_array(strtolower($_COOKIE['lang']),$languages ))
		$_SESSION['lang'] = $lang = $_COOKIE['lang'];
	elseif (isset($_SESSION['lang']))
		$lang = $_SESSION['lang'];
	else
		$lang = "EN";

$registration = new Registration();

	if(!isset($_SESSION['REFERER']) && isset($_SERVER['HTTP_REFERER'])){
		$_SESSION['REFERER'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
		$url = $_SESSION['REFERER'];
	} else 
		$url = SITE_URL;
	if(!empty($_POST['website']))
		die();

// --- MAIN
$webHtml = new WebHtml($registration->rgTxt->titles['h1reg'], 600);
	echo '<div style="display:block;float:left;padding:2px;">', 
				'<a href="' . SITE_URL . '">AHP-OS Home</a></div>';
	echo '<div style="padding:2px;float:right;"><a href="',$url,'">back</a></div>';
	echo '<div style="clear:both;"></div>';
	echo "<h1>",$registration->rgTxt->titles['h1reg'],"</h1>";
	if( SELFREG ){
		$formToken = $_SESSION['formToken'] = uniqid();
		// showing the register view (with the registration form, and messages/errors)
		include("../form.registration.php");
	} else {
 		echo "<p class='msg'>",$registration->rgTxt->info['nReg'],"</p>";
	}
$webHtml->webHtmlFooter($version);
