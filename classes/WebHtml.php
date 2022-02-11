<?php
/**
* Framework for AHP-OS html php pages
*
* @author Klaus D. Goepel
* @copyright 2014 Klaus D. Goepel
* @version 2017-09-10 last version w/o SVN
*
*   Copyright (C) 2022  <Klaus D. Goepel>
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
* 
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

class WebHtml
{
/*
* @param string $pageUrl Url of the page to build up
* @param boolean $pwk Tracking with Piwik. Includes pwl_klaus.php if set
* @param boolean $share Social networks Facebook, Trigger
* @return void
* @todo merge webHtmlHeader()
*/
    public function __construct($pageTitle="", $width=900)
    {
        global $lang;
        global $cssUrl;
        include ABS_PATH . BASE . "includes" . DIRECTORY_SEPARATOR . "header.html";
        echo "\n<!-- Content -->\n";
        echo "<div class='hentry' id='content' style='background-color:white;margin-left:auto;
		margin-right:auto;max-width:{$width}px;padding:15px;'>";
        return;
    }


    /*
    * HTML Footer
    * sends footer for HTML page. Closes content, sets honeypot link,
    * closes HTML, outputs debugging information when DEBUG is true
    *
    * @return void
    */
    public function webHtmlFooter($version="2019-07-02")
    {
        global $s,$rev;

        if (DEBUG) {
            echo "<hr>";
            $e = microtime(true);
            echo "Execution time: ",round(1000*(microtime(true)-$s), 1)," mSec<br>";
            printf(
                "<p>Memory: %.1f k (nom) %.1f k (peak) of %g MBytes",
                memory_get_usage()/1024,
                memory_get_peak_usage()/1024,
                ini_get('memory_limit')
            );
            if (isset($_SESSION)) {
                echo "<p>Session variables</p>";
                displayArray($_SESSION);
            }
            if (isset($_POST)) {
                echo "<p>Post variables</p>";
                displayArray($_POST);
            }
        }
        include ABS_PATH  . BASE . "includes" . DIRECTORY_SEPARATOR . "footer.html";
    }


    // --------------------------------------------------------------------------
    // --- These are general functions, but not used in AHP-OS, todo: delete? ---
    // --------------------------------------------------------------------------

    /* Session Closing - closes open php session
     * To be called before any html output
     */
    public function sessionClose()
    {
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        //	session_regenerate_id(true);
        return;
    }

    /** Measures session time based on $_SESSION['tstart']
     * @return float time in minutes
     */
    public function sessionTime()
    {
        $st = gettimeofday(true);
        if (isset($_SESSION['tstart'])) {
            return round(bcsub($st, $_SESSION['tstart'], 6)/60., 1);
        }
        return 0.0;
    }

    /* timer to measure execution time of scripts */
    public function timer_start()
    {
        $this->timeparts = explode(" ", microtime());
        $this->starttime = $this->timeparts[1] . substr($this->timeparts[0], 1);
        return $this->starttime;
    }

    public function time_end()
    {
        $this->timeparts = explode(" ", microtime());
        $this->endtime = $this->timeparts[1].substr($this->timeparts[0], 1);
        return bcsub($this->endtime, $this->starttime, 6);
    }

    // strip any special characters from form fields
    public function stripStringInput($data)
    {
        $bad = array( "\t","\n","\r","\0","\x0B",);
        if (is_string($data)) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            $data = str_replace($bad, "", $data);
            return $data;
        }
        return "";
    }
    // -- end general functions
} // end WebHtml class
