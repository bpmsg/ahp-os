<?php
/*
 * AHP hierarchy project calculation with alternatives
 * @author Klaus D. Goepel
 * @package AHP-OS
 * @since 2013-12-01 release first version ahp hierarchy
 *
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

session_start();

$version = substr('$LastChangedDate: 2022-02-26 12:40:14 +0800 (Sa, 26 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 172 $', "$");

$class = 'Ahp' . $lang;
$ahpOs = new $class();
$_SESSION['lang'] = $lang;

$js = new JsCheck();
$login = new Login();

$loggedIn = $login->IsUserLoggedIn();

/*
 * --- Web Page HTML OUTPUT ---
 */
$webHtml = new WebHtml($ahpOs->titles['pageTitle']);

if (!(isset($_SESSION['javascript']) && $_SESSION['javascript'])) {
    $js->checkJsByForm();
}
// $js->checkJsByCookies();
$loginHeaderText = "<a href=".$urlHome.">BPMSG Home</a>&nbsp;&nbsp;
				<a href='ahp-news.php'>Latest News</a>";
if ($js->isJsActivated() === false) {
    $loginHeaderText .= "<span class='err'>&nbsp;For full functionality 
				please allow JavaScript! </span>";
} else {
    $loginHeaderText .= "<span class='msg'>&nbsp;&nbsp;Java is enabled. </span>";
}

include 'includes/login/form.login-hl.php';
    echo $ahpOs->titles['h1title'];

if (defined('SYS_MSG')) {
    echo "<p class='hl'>" . SYS_MSG . "</p>";
}

if (!empty($login->errors) || !empty($login->messages)) {
    echo $login->getErrors();
}

$webHtml->displayLanguageSelection();

if (DONATIONS) {
    echo "<p>Donation (please \"Send\", not \"Request\"): 
    <a href='https://paypal.me/ahpDonation'>paypal.me/ahpDonation</a></p>";
}
echo $ahpOs->titles['h2subTitle'];
echo $ahpOs->info['intro11'];
echo $ahpOs->info['intro12'];
echo $ahpOs->info['intro13'];

// --- Terms of use link
echo $ahpOs->info['intro14'];

if (DONATIONS) {
    echo $ahpOs->info['intro15'];
}

echo $ahpOs->info['intro16'];

if (!$loggedIn && ($lang == 'EN' || $lang == 'ES' || $lang == 'PT')) {
    echo $ahpOs->info['intro21'];
    echo $ahpOs->info['intro22'];
    echo $ahpOs->info['intro23'];
    echo $ahpOs->info['intro24'];
}

echo "<div style='clear:both;'>";

if (!$loggedIn) {
    if (defined('CMTLNK')) {
        echo $ahpOs->titles['h2contact'];
        printf($ahpOs->info['contact'], CMTLNK);
    }
    echo "<small>The AHP-OS package is realized in php and available from the author 
	on <a href='https://github.com/bpmsg/ahp-os' target='_blank' >Github</a>.</small>";
} elseif (in_array($_SESSION['user_id'], $admin)) {
    echo "<small><a href='includes/login/do/do-user-admin.php'>AHP-OS admin</a></small>";
}
echo "</div>";
$webHtml->webHtmlFooter($version);
