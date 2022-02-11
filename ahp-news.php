<?php
/** AHP hierarchy project calculation with alternatives
* @author Klaus D. Goepel
* @package AHP online
* @since 2013-12-01 release first version ahp hierarchy
* @version 2017-03-11 last version w/o SVN
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
include 'includes/config.php';

$login = new Login();
// sets the session variable for language
$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
if ($lang != null && $lang != false && in_array($lang, $languages)) {
    $_SESSION['lang'] = $lang =strtoupper($lang);
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang ='EN';
}

$class = 'AhpNews' . $lang;
$ahpOs = new $class();

$version = substr('$LastChangedDate: 2022-02-11 16:19:15 +0800 (Fr, 11 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 126 $', "$");

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpOs->titles1['pageTitle']);
include('includes/login/form.login-hl.php');
echo $ahpOs->titles1['h1title'];
echo "<p>Language: <a href='$urlAct?lang=en'>English</a>
       &nbsp;&nbsp;<a href='$urlAct?lang=de'>German</a>
       &nbsp;&nbsp;<a href='$urlAct?lang=es'>Español</a>
       &nbsp;&nbsp;<a href='$urlAct?lang=pt'>Português</a>
      </p>";
echo "<div class='entry-content'>";
printf($ahpOs->titles1['h2welc'], (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ""));
printf($ahpOs->titles1['h3release'], $version, $rev);
echo $ahpOs->info['news1'];
echo $ahpOs->titles1['h3news2'];
echo $ahpOs->info['news2'];
if (DONATIONS) {
    echo $ahpOs->titles1['h3don'];
    echo $ahpOs->info['don'];
    // include "views/paypal-don.html";
}
echo "<div style='clear:both;text-align:justify;'>";
if (DONATIONS) {
    echo "<p><a href='https://paypal.me/ahpDonation' target='_blank'>Paypal.me/ahpDonation</a></p>";
}
echo $ahpOs->msg['tu'];
echo "<br><img src='images/Klaus.png' alt='Klaus' width='103' height='44' />";
echo "<div style='text-align:center;'><a href='" . SITE_URL . "'>",$ahpOs->msg['cont'],
     "</a></p></div>";
echo "</div>";

$webHtml->webHtmlFooter($version);
