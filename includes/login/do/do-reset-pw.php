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
 */
include '../../config.php';
require('../../../vendor/autoload.php');

$version = substr('$LastChangedDate: 2022-02-24 07:15:49 +0800 (Do, 24 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 170 $', "$");

// create a login object. when this object is created, it will 
// do all login/logout stuff automatically
// so this single line handles the entire login process.
session_start();

$login = new Login();

    if (!isset($_SESSION['REFERER']) && isset($_SERVER['HTTP_REFERER'])) {
        $_SESSION['REFERER'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
        $url = $_SESSION['REFERER'];
    } else {
        $url = SITE_URL;
    }
  // --- MAIN
  $webHtml = new WebHtml($login->lgTxt->titles['h1pwR'], 600);

    // Login header has only a link back from the referring website
    echo "<div style='display:block;float:left;padding:2px;'>",
            "<a href='" . SITE_URL . "'>" . APP . " Home</a></div>";
    echo "<div style='padding:2px;float:right;'><a href='$url'>back</a></div>";
    echo "<div style='clear:both;'></div>";
  echo "<h1>",$login->lgTxt->titles['h1pwR'],"</h1>";
  // show potential errors / feedback (from login object)
    echo "<p>",$login->getErrors(),"</p>";
    // show form
  include('../form.resetpw.php');
  echo "<p></p>";
  $webHtml->webHtmlFooter($version);
