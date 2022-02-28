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

 */

    include '../../config.php';
    define('LOGMAX', 50);

    $title= "User Account Log Table";
    $version = substr('$LastChangedDate: 2022-02-27 14:35:52 +0800 (So, 27 Feb 2022) $', 18, 10);
    $rev = trim('$Rev: 176 $', "$");

    session_start();

    $login = new Login();

    if ($login->isUserLoggedIn() && in_array($_SESSION['user_id'], $admin)) {
        $webHtml = new WebHtml($title);
        $ahpDb = new LoginAdmin();

        // --- MAIN
        include('../form.login-hl.php');
        echo "<h1>$title</h1>";

        // --- Log --- all users, 25 lines max.
        $ahpDb->displayLogTable("%", LOGMAX );
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
