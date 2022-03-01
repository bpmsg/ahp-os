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
    * Start webpage output
    * @para string $pageTitle
    * @para int $width
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
            $phpUtil = new PhpUtil();
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
                $phpUtil->displayArray($_SESSION);
            }
            if (isset($_POST)) {
                echo "<p>Post variables</p>";
                $phpUtil->displayArray($_POST);
            }
        }
        include ABS_PATH  . BASE . "includes" . DIRECTORY_SEPARATOR . "footer.html";
    }


    /* Display of language selection */
    public function displayLanguageSelection(){
        echo "<p>Language: <a href='", $urlAct, "?lang=en'>English</a>
           &nbsp;&nbsp;<a href='", $urlAct, "?lang=de'>Deutsch</a>
           &nbsp;&nbsp;<a href='", $urlAct, "?lang=es'>Español</a>
           &nbsp;&nbsp;<a href='", $urlAct, "?lang=pt'>Português</a>
          </p>";

    }
} // end WebHtml class
