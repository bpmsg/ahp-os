<?php
/*
 * Page to check for database integrity
 * @version 2017-04-08
 * @version 2017-10-01 last version w/o SVN
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
    define('LMT', 1000); // limit of how many projects are checked
    define('MONTHS_USER_FLOW', 6);
    define('TOP_USERS', 20);

/* Includes */
    include '../../config.php';

global $ahpDb;

$login =    new Login();
$ahpDb =    new AhpDb();
$ahpAdmin = new AhpAdmin();

// --- HTML ---

$title="Database Integrity";
$version = substr('$LastChangedDate: 2022-02-03 14:55:25 +0800 (Thu, 03 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 91 $', "$");

$webHtml = new WebHtml($title);
    include('../form.login-hl.php');
echo "<h1>$title</h1>";

if ($login->isUserLoggedIn() && in_array($_SESSION['user_id'], $admin)) {
    $err = array();
    $reg = array();

    // --- php version
    echo "<h3>PHP Version ", phpversion(), "</h3>";

    // --- database integrity
    echo "<h3>Database Info</h3>";
    $res =$ahpAdmin->checkDbIntegrity();
    foreach ($res as $row) {
        echo key($row),"<span class='res'> ",$row[key($row)],"</span><br>";
    }

    echo "<h3>PWC Consistency Check</h3>";
    echo "<p>Projects with nodes in the pwc table, but not in the hierarchy of
    the corresponding project.</p>";
    $prjts = array();
    // all projects with pwc
    $rslt = $ahpDb->checkPwcCons();
    echo "<p><span class='res'>";
    if(empty($rslt)) {
        echo "o.k.</span></p>";
    } else {
        echo count($rslt), "</span> projects affected.</p>";
        foreach ($rslt as $project) {
            echo "<span class='hl'>", $project['user'], "</span> 
                <span class='res'>", $project['sc'], "</span> ",
                count($project['nodes']), " Node(s): ",
                implode(", ", $project['nodes']), $project['type'], "<br>";
        }
    }
    
    // --- user flow
    echo "<h3>User Flow</h3>";
    $reg = $ahpAdmin->getUserFlow(MONTHS_USER_FLOW);
    echo "Last " . MONTHS_USER_FLOW . " months: <br>";
    foreach ($reg['I'] as $mnth=>$val) {
        $reg['F'][$mnth] = $val - $reg['D'][$mnth];
        echo "$mnth ", $reg['F'][$mnth], " users<br>";
    }
    $cnt = array_sum($reg['F']);
    $ym = strftime('%Y-%m', time()); // current month
    echo "<p>$ym: <span class='res'>", $reg['I'][$ym],
    "</span> registrations, <span class='res'>", $reg['D'][$ym],
    "</span> deletions.</br>";
    echo "<span class='res'>", ($cnt >0 ? "+" : "") ,$cnt,
     "</span> users over the last $m months</p>";

    // --- top users
    echo "<h3>Top " . TOP_USERS . " Users</h3>";
    if (DONATIONS) {
        echo "<p>Users, who have donated, are highlighted.</p>";
    }
    $res =$ahpDb->getTopUsers(TOP_USERS);
    $resCnt = count($res);
    if (!empty($res) and $resCnt > 0) {
        // LEFT PART
        echo "<div style='float:left;'>";
        echo "<table>";
        echo "<tr>","<th>No</th><th class='nwb'>Date</th>",
        "<th class='la'>User</th>", "<th>Index</th>","</tr>";
        for ($i=0; $i < (int) $resCnt/2; $i++) {
            $style = ($i%2 ? "class='odd'" : "class='even'");
            echo "<tr $style>";
            echo "<td style='text-align:right;'>", $i+1, "</td>";
            echo "<td>", (substr($res[$i][2], 2, 8) == date('y-m-d') ?
            "<span class='hl'>" . $res[$i][2] . "</span>" : $res[$i][2]),
             "</td>";
            $df = $ahpAdmin->checkDonation($res[$i][0]);
            $hls = ($df ? "<span class='hl'>" : "<span class='var'>");
            echo "<td>", $hls, $res[$i][0], "</span></td>";
            echo "<td><span class='res'>", $res[$i][1], "</td>";
            echo "</tr>";
        }
        echo "</table>";
        // RIGHT PART
        echo "</div><div>";
        echo "<table>";
        echo "<tr>","<th>No</th><th class='nwb'>Date</th>",
        "<th class='la'>User</th>", "<th>Index</th>","</tr>";
        for ($i= round($resCnt/2); $i < $resCnt; $i++) {
            $style = ($i%2 ? "class='odd'" : "class='even'");
            echo "<tr $style>";
            echo "<td style='text-align:right;'>", $i+1, "</td>";
            echo "<td>", (substr($res[$i][2], 2, 8) == date('y-m-d') ?
            "<span class='hl'>" . $res[$i][2] . "</span>" : $res[$i][2]),
            "</td>";
            $df = $ahpAdmin->checkDonation($res[$i][0]);
            $hls = ($df ? "<span class='hl'>" : "<span class='var'>");
            echo "<td>", $hls, $res[$i][0], "</span></td>";
            echo "<td><span class='res'>", $res[$i][1], "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p><span class='err'>no top user data available</span></p>";
    }
    echo "<div style='clear:both'></div>";
    echo "<p><a href='do-user-admin.php'>back</a></p>";
}
$webHtml->webHtmlFooter($version);
