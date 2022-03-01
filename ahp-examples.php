<?php
/** AHP hierarchy project calculation with alternatives
* @author Klaus D. Goepel
* @package AHP-OS
* @since 2013-12-01 release first version ahp hierarchy
* @version 2019-06-26 using SVN
*
* Last Change: $LastChangedDate: 2022-03-01 11:44:47 +0800 (Di, 01 Mär 2022) $
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

$pageTitle = "AHP Examples";
$title = "AHP Examples";
$version = substr('$LastChangedDate: 2022-03-01 11:44:47 +0800 (Di, 01 Mär 2022) $', 18, 10);
$rev = trim('$Rev: 178 $', "$");

$login = new Login();

$rsHtml = new WebHtml($pageTitle);
    include('includes/login/form.login-hl.php');
echo"<h1>$title</h1>";
    include 'views/ahpExamples.html';
$rsHtml->webHtmlFooter($version);
