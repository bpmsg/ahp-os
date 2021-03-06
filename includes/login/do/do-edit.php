<?php
/* Edit credentials
 * @uses form.login-hl.php
 * @uses form.edit.php
 * form.edit calls displayLogTable($id) to show last 5 log entries
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

include '../../config.php';

session_start();

$version = substr('$LastChangedDate: 2022-02-27 14:35:52 +0800 (So, 27 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 176 $', "$");
$url = htmlspecialchars($_SERVER['HTTP_REFERER']);

$login = new Login();

  // --- MAIN
  $webHtml = new WebHtml($login->lgTxt->titles['h1edit'], 800);
        include('../form.login-hl.php');
  echo "<h1>",$login->lgTxt->titles['h1edit'],"</h1>";
    // ... ask if we are logged in here:
    if ($login->isUserLoggedIn() === true) {
        // --- User is logged in
        $formToken = $_SESSION['formToken'] = uniqid();
        $userDb = new LoginAdmin();
        $account = $login->getUserData(htmlspecialchars($_SESSION['user_name']));
        include('../form.edit.php');
    } else {
        echo "<h2>",$login->lgTxt->titles['h2lgin'], "</h2>";
        echo "<p>",$login->lgTxt->info['nlgin'],"</p>";
    }
    echo "<p></p>";
  $webHtml->webHtmlFooter($version);
