<?php
/*
* General PHP utilities useful for several program packages
*
* $LastChangedDate:$
* $Rev:$
* 
*   public function get_client_ip() get ip
*   public function my_httpbl_check($ip)
*   function startNewSession()
*   public function closeSession()
*   public function displayArray($array)
*   function display_filesize($filesize)
*   function validateDate($date, $format = 'Y-m-d')
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

class PhpUtil
{
    public function __construct()
    {
        mb_internal_encoding('UTF-8');      
    }


    /* Get clients IP address */
    public function get_client_ip()
    {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP']) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_X_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif ($_SERVER['HTTP_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif ($_SERVER['REMOTE_ADDR']) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = '';
        }
        return $ipaddress;
    }


    /* Spam query function project honeypot
     * Please see https://www.projecthoneypot.org */
    public function my_httpbl_check($ip)
    {
        if(defined('HPAPIKEY')){
            $request = HPAPIKEY . "."
            . implode(".", array_reverse(explode(".", $ip)))
            . ".dnsbl.httpbl.org";
            $result = explode(".", gethostbyname($request));
            return($result[0] == 127 ? $result : "ok");
        }
        return "";
    }

    /* Compares current session id (sid) with $_SESSION['sid']
     * If $_SESSION['sid'] is not set, starts a new session
    * @return string $sid session id
    */
    public function startNewSession()
    {
        $sid = session_id();
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $sid = md5($sid . $_SERVER['HTTP_USER_AGENT']);
        } else {
            $sid = md5($sid . "any rubbish text whatever");
        }
        if (isset($_SESSION['sid'])) {
            if ($sid != $_SESSION['sid']) {
                unset($_SESSION['sid']);
                die("Invalid session");
            }
        } else {
            $_SESSION['sid'] = $sid;
            $_SESSION['tstart'] = gettimeofday(true);
        }
        return $sid;
    }


   /* Close session */
    public function closeSession()
    {
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
    }


    /* Makes arrays easily readable for debugging purposes */
    public function displayArray($array)
    {
        if (is_array($array)) {
            echo "<p>";
            foreach ($array as $key => $val) {
                echo "<span style='color:blue'>$key: </span>";
                if (is_array($val)) {
                    print_r($val);
                } else {
                    echo "$val";
                }
                echo "<br>";
            }
            echo "</p>";
        } else {
            echo "<p>$array</p>";
        }
    }


    /* Display file size */
    function display_filesize($filesize)
    {
        if (is_numeric($filesize)) {
            $decr = 1024;
            $step = 0;
            $prefix = array('Byte','KB','MB','GB','TB','PB');

            while (($filesize / $decr) > 0.9) {
                $filesize = $filesize / $decr;
                $step++;
            }
            return round($filesize, 1).' '.$prefix[$step];
        } else {
            return 'NaN';
        }
    }


    /* Validate Date format Y-m-d */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
