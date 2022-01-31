<?php
/*
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

$title="User Account Log Table";
$version = substr('$LastChangedDate$',18,10);
$rev = trim('$Rev$', "$");

session_start();

$lang = (isset($_SESSION['lang']) ? $_SESSION['lang'] : "EN");
$login = new Login();
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);

if($login->isUserLoggedIn() && in_array($_SESSION['user_id'], $admin )){
	$webHtml = new WebHtml($title);
	$ahpDb = new LoginAdmin();

// --- MAIN
	include('../form.login-hl.php');
	echo "<h1>$title</h1>";

// --- Log --- all users, 25 lines max.
	$ahpDb->displayLogTable("%", 50);
	echo '<p></p>';
	echo $ahpDb->getErrors();
	echo '<p></p>';
	echo "<p><a href='do-user-admin.php'>back</a></p>";
	$webHtml->webHtmlFooter($version);

} else {
	$url = (isset($_SESSION['REFERER']) ? $_SESSION['REFERER'] : SITE_URL);
    	header("Location: " . "$url");
    	exit();
}

