<?php
/**
* Analytic Hierarchy Process database functions for ahp
*
* $LastChangedDate: 2022-02-15 14:53:37 +0800 (Di, 15 Feb 2022) $
* $Rev: 136 $
*
* @author Klaus D. Goepel
* @copyright 2014-2017 Klaus D. Goepel
* @package AHP online
* --- Version History ----
* @since 2014-04-05
* @version 2017-10-05 displayParticipants added jquery script
* - last version w/o SVN
*
* @uses $_SESSION['ipart'] list of selected participants
* @uses $_SESSION['ptick'] tick all participants
* @uses $_SESSION['ntick'] untick all participants
* @uses $_POST['pselect']
* @uses $_POST['ipart'] list of selected participants
* @uses $_POST['ptick'] tick all participants
* @uses $_POST['ntick'] untick all participants
*
* --- Class Methods ---
*
* public  function __construct()
* public  function getErrors()
* public  function databaseConnection()  public because used in ahp recover
* public  function checkDbIntegrity()    todo: extend for mariadb
* public  function generateSessionCode($length=8, $strength=0)
* public  function checkSessionCode($sc)
* public  function getStoredSessions($name)
* public  function readProjectData($sc)
* public  function checkParticipant($sc, $name)
* public  function getParticipants($sc)
* public  function getSelectedParticipants($part)
* public  function getPwcArray($sc, $name, $nod="")
* public  function deleteRecord($sc)
* public  function writeProjectData($sc, $project, $description, $hText,
* 									 $author, $alt=array())
* public  function updateProjectData($sc, $project, $description, $hText,
*									 $author, $alt=array())
* public  function submitGroupData($sc, $name, $pwc)
* public  function setSessfmPrjc($sc)
* public  function toggleStatus($sc)
*
* private function readParticipantData($sc)
* private function getPwc($sc, $participant, $nod)
* private function convertPwc($pwc)
* private function convertPwcToString($pwc)
* private function writePwcData($sc, $participant, $pwc) - still in use?
* private function getAllSessions($name) - used by displaySessionTable
*
* --- User activity ---
* public  function getTopUsers($lmt = 25)
* public  function getActivityLevel($name)
*
* --- HTML output functions ---
* public  function displayProjectDetails($sc, $sel=false) - ahp-group,
* private function displayParticipantTable($part)
* 		- called by displayProjectDetails
* private function displaySessionTable($name)
*
* private function getMatrixFromPwc($pwc) Same function as in ahp class!
*
* --- CSV export functions ---
* public  function exportProjectDetails($sc, $ds)
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

define("MESSAGE_DATABASE_ERROR", "Database error ");


class AhpDb
{
    /** Class constants */
    public const NEWL = "\n";      // for csv file export
    public const ENCL = '"';

    /** Properties */

    public $err = array();
    public $db_sqlite_path = DB_PATH;
    public $db_type =  DB_TYPE;
    public $db_name;
    public $db_connection;
    public $sessionCode;

    private $ahpDbTxt;
    private $lang;

    /** Methods */
    public function __construct($dbname = DBNAME)
    {
        $this->db_name = $dbname;
        mb_internal_encoding('UTF-8');
        global $lang;
        $class = get_class() . $lang;
        $this->lang = $lang;
        $this->ahpDbTxt = new $class();
        // $this->db_name = $dbname;
        // if db name is explicitely given with extension .db,
        // type is set to sqlite
        $l = strlen($dbname);
        if (substr($dbname, -3) == ".db") {
            $this->db_type = "sqlite";
            $this->db_name = substr($dbname, 0, $l-3);
        } else {
            $this->db_name = $dbname;
        }
        return;
    }

    /* same function as in login_admin */
    public function getErrors()
    {
        if (empty($this->err)) {
            return "";
        } else {
            return implode(" ", $this->err);
        }
    }


    /*
     * Checks if database connection is opened. If not, then this method
     * tries to open it.
     * @return bool Success status of the database connecting process
     */
    private function databaseConnection()
    {
        // if connection already exists
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a db connection, using the constants from config/config.php
            try {
                if ($this->db_type == 'sqlite') {
                    $this->db_connection = new PDO('sqlite:' . DB_PATH
                    . $this->db_name. ".db");
                } elseif ($this->db_type == 'mysql') {
                    $dsn = 'mysql:host=' . DBHOST . ';dbname=' . $this->db_name
                    . ';charset=utf8';
                    $this->db_connection = new PDO($dsn, DBUSER, DBPASS);
                } else {
                    $err[] = $this->ahpDbTxt->err['dbType'] . DB_TYPE;
                    return false;
                }
                return true;
                // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->err[] = MESSAGE_DATABASE_ERROR . $e->getMessage();
            }
        }
        // default return
        return false;
    }


    /*
     * The session code is used to identify the AHP projects with a unique code
     * @return string session code
     */
    public function generateSessionCode($length=8, $strength=0)
    {
        $vowels = 'aeuy';
        $consonants = 'bdfghjkmnpqrstvwz';
        if ($strength & 1) {
            $consonants .= 'BDGHJKLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEUY";
        }
        if ($strength & 4) {
            $consonants .= '23456789';
        }
        if ($strength & 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        do {
            for ($i = 0; $i < $length; $i++) {
                if ($alt == 1) {
                    $password .= $consonants[(rand() % strlen($consonants))];
                    $alt = 0;
                } else {
                    $password .= $vowels[(rand() % strlen($vowels))];
                    $alt = 1;
                }
            }
        } while ($this->checkSessionCode($password));
        return $password;
    }


    /* Check whether session code exists, if yes, return true, otherwise false
     * ensures unique code for new projects
     */
    public function checkSessionCode($sc)
    {
        // check whether session code already existing
        if ($this->dataBaseConnection()) {
            $queryCheckSc =
            $this->db_connection->prepare(
                "SELECT project_sc FROM projects WHERE project_sc = :sc"
            );
            $queryCheckSc->bindValue(':sc', $sc, PDO::PARAM_STR);
            $queryCheckSc->execute();
            $result = $queryCheckSc->fetchAll();
            if (count($result) > 0) {
                $this->sessionCode = $sc;
                return true;
            } else {
                $this->sessionCode = "";
                return false;
            }
        }
    }


    /*
     * get stored sessions from author $name as sorted array of session codes
     * @return array session codes or empty array
     */
    public function getStoredSessions($name)
    {
        $result = array();
        if ($this->dataBaseConnection()) {
            $sql = "SELECT project_sc FROM projects 
                    WHERE project_author = :name ORDER BY project_sc";
            $sql .= ($this->db_type == "sqlite" ? " COLLATE NOCASE ASC;" : ";") ;
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($result)) {
                return $result;
            } else {
                $this->err[] = $this->ahpDbTxt->err['noSess'];
                return array();
            }
        }
    }


    /*
     * reads project data with session code $sc from database
     * @return assoc array of project data:
     * array(
     * ['project_id']=>int, ['project_sc'] =>char(6),
     * ['project_name'] =>varchar(64),
     * ['project_description'] =>text(400), ['project_hText']=>text(6000),
     * ['project_datetime'] =>datetime, ['project_author'] =>varchar(64)
     * )
     * If alternatives are defined: ['project_alt']=>varchar(64)
     */
    public function readProjectData($sc)
    {
        if ($this->checkSessionCode($sc)) {
            $sql = "SELECT * FROM `projects` WHERE `project_sc` = :sc";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) != 1) {
                $this->err[] = $this->ahpDbTxt->err['dbReadSc'] . $sc . " ";
                return array();
            }
            // check for alternatives
            $sql = "SELECT `alt` from `alternatives` WHERE `project_sc` = :sc";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->execute();
            $alt = $query->fetchAll(PDO::FETCH_COLUMN, 'alt');
            if (!empty($alt)) {
                $result[0] = array_merge($result[0], array('project_alt'=>$alt));
            }
            return $result[0];
        } else {
            $this->err[] = $this->ahpDbTxt->err['scInv'];
            return array();
        }
    }


    /* Toggle project status open/closed
     * @return project status 1 (open) or 0 (closed)
     */
    public function toggleStatus($sc)
    {
        $this->databaseConnection();
        $sql = "SELECT `project_status` from `projects` 
                WHERE `project_sc` = :sc";
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':sc', $sc, PDO::PARAM_STR);
        $query->execute();
        $status = $query->fetch(PDO::FETCH_NUM);
        $stnew = ($status[0] == 1 ? 0 : 1);
        $sql = "UPDATE `projects` SET `project_status` = :stnew
                WHERE `project_sc` = :sc;";
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':sc', $sc, PDO::PARAM_STR);
        $query->bindValue(':stnew', $stnew, PDO::PARAM_STR);
        $state = $query->execute();
        return $stnew;
    }


    /* check whether name is already participant in project sc
     * @return bool true if participant exists, false otherwise
     */
    public function checkParticipant($sc, $name)
    {
        $p = $this->getParticipants($sc);
        if (!empty($p) && in_array($name, $p)) {
            return true;
        } else {
            return false;
        }
    }


    /* get list of participants for project sc
     * @return array of participant names for project $sc or empty array
     */
    public function getParticipants($sc)
    {
        if ($this->dataBaseConnection()) {
            if ($this->checkSessionCode($sc)) {
                $sql = "SELECT DISTINCT pwc_part FROM pwc 
                        WHERE project_sc = :sc ORDER BY pwc_timestamp DESC;";
                $query = $this->db_connection->prepare($sql);
                $query->bindValue(':sc', $sc, PDO::PARAM_STR);
                $query->execute();
                $part = $query->fetchAll(PDO::FETCH_COLUMN, 'pwc_part');
                return $part;
            } else {
                $this->err[] = $this->ahpDbTxt->err['scInv'];
            }
        }
        return array();
    }


    /* get list of participants with date of input for project sc
     * called from display project details (participants)
     */
    private function readParticipantData($sc)
    {
        if ($this->dataBaseConnection()) {
            $sql = "SELECT pwc_part, max(pwc_timestamp) AS 'pwcDate' 
                    FROM pwc WHERE project_sc = :sc
                    GROUP BY pwc_part ORDER BY pwcDate DESC;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->execute();
            $part = $query->fetchAll(PDO::FETCH_NUM);
            return($part);
        }
    }


    /*
     * delete participant with name $pn from project with session code $sc
     * @return bool true if deleted, false otherwise
     */
    public function delParticipant($sc, $pn)
    {
        if ($this->dataBaseConnection()) {
            $sql = "DELETE FROM `pwc` WHERE `project_sc` = :sc 
                    AND `pwc_part` = :pn;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->bindValue(':pn', $pn, PDO::PARAM_STR);
            $deleteStatus = $query->execute();
            if (!$deleteStatus) {
                $this->err[] = DB_DELETE_ERROR;
            }
            return $deleteStatus;
        }
        return false;
    }


    /*
     * get complete array of all pairwise comparisons from participant $name
     * for project $sc. If parameter $node is given, get pwc for this node only
     * Reads [pwc_ab] => varchar(255) and [pwc_intense] => varchar(255) from DB
     * [pwc_ab] is 0 (preference "A") or 1 (preference "B")
     * [pwc_intense] contains rational scale values range 1 to 9
     *
     * @return array [node] (array [A], array[intense])
     * @uses function convertPwc to get final arraymatching ahpH
     * @uses function getPwc
     */
    public function getPwcArray($sc, $name, $nod="")
    {
        $nodes = array();
        $pwcA = array();
        if ($nod=="") {
            $sql = "SELECT pwc_node FROM pwc WHERE project_sc = :sc 
                    AND pwc_part = :name;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $nodes = $query->fetchAll(PDO::FETCH_COLUMN, 'pwc_node');
        } else {
            $nodes[] = $nod;
        }
        // get pwc
        foreach ($nodes as $node) {
            $pwc = $this->getPwc($sc, $name, $node);
            if (empty($pwc)) {
                $pwcA[$node] = array();
            } else {
                $pwcA[$node] = $this->convertPwc($pwc);
            }
        }
        return $pwcA;
    }


    /*
     * get pairwise comparisons for project $sc from
     * participant $participant for node $node
     * @return array [pwc_ab] => varchar(255) and [pwc_intense] => varchar(255)
     * @uses function convertPwc to get final array matching ahpH
     */
    private function getPwc($sc, $participant, $nod)
    {
        if ($this->checkSessionCode($sc)) {
            $sql = "SELECT `pwc_ab`, `pwc_intense` FROM `pwc` 
                    WHERE `project_sc` = :sc AND `pwc_part` = :participant 
                    AND `pwc_node` LIKE :nod";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->bindValue(':participant', $participant, PDO::PARAM_STR);
            $query->bindValue(':nod', $nod, PDO::PARAM_STR);
            $query->execute();
            $pwc = $query->fetch(PDO::FETCH_ASSOC);
            return $pwc;
        } else {
            $this->err[] = $this->ahpDbTxt->err['scInv'];
            return array();
        }
    }


    /* converts pwc array of a specific node from two strings
     * to an array [A],[Intense]
     */
    private function convertPwc($pwc)
    {
        $pwcA = $pwc['pwc_ab'];
        $pwcI = $pwc['pwc_intense'];
        $pwcConv = array( 'A' =>str_split($pwcA), 'Intense' =>str_split($pwcI) );
        return $pwcConv;
    }


    /* converts $pwc array [node][A],[node][Intense] into
     * two strings ['node']['pwc_ab'], ['node']['pwc_intense']
     * called by submitGroupData
     */
    private function convertPwcToString($pwc)
    {
        foreach ($pwc as $node=>$judgment) {
            $pwcA = implode('', $judgment['A']);
            $pwcI = implode('', $judgment['Intense']);
            $pwcConv[$node] = array( 'pwc_ab'=>$pwcA, 'pwc_intense'=>$pwcI);
        }
        return $pwcConv;
    }


    /* Function to load project data and set all session parameters
     * for pairwise comparisons. For completed judgments priorities
     * will be calculated.
     * @uses $_SESSION['name']
     * @returns url for pwc input (urlAhpH or urlAlt)
     */
    public function setSessfmPrjc($sc)
    {
        global $urlAhpH;
        global $urlAlt;
        $pwcsCnt = 0;
        if (!$this->checkSessionCode($sc)) {
            // session code does not exist
            $this->err[] = $this->ahpDbTxt->err['scInv'];
            return"";
        }
        $prjct = $this->readProjectData($sc);
        if ($prjct['project_status'] == 0) {
            $this->err[] = $this->ahpDbTxt->err['pClosed'];
            return "";
        }
        $_SESSION['groupSession'] = $_SESSION['sid'];
        $_SESSION['hText'] = $prjct['project_hText'];
        $_SESSION['project'] = $prjct['project_name'];
        $_SESSION['description'] = $prjct['project_description'];
        if (isset($prjct['project_alt'])) {
            $_SESSION['alt'] = $prjct['project_alt'];
            $altNum = $_SESSION['altNum'] = count($prjct['project_alt']);
            $hier = false;
        } else {
            if (isset($_SESSION['alt'])) {
                unset($_SESSION['alt']);
            }
            if (isset($_SESSION['altNum'])) {
                unset($_SESSION['altNum']);
            }
            $hier = true;
        }
        $pcnt = count($this->getParticipants($sc));
        if ($pcnt != 0) {
            // project has participants - we clear old session data
            if (isset($_SESSION['pwcaDone'])) {
                unset($_SESSION['pwcaDone']);
            }
            if (isset($_SESSION['pwcDone'])) {
                unset($_SESSION['pwcDone']);
            }
            if (isset($_SESSION['pwc'])) {
                unset($_SESSION['pwc']);
            }
            if (isset($_SESSION['prioAlt'])) {
                unset($_SESSION['prioAlt']);
            }
            if (isset($_SESSION['prioTot'])) {
                unset($_SESSION['prioTot']);
            }
            $pwcs = $this->getPwcArray($sc, $_SESSION['name']);
            //logged in user is/is not participant
            if (!empty($pwcs) && count($pwcs)>0) {
                $_SESSION['pwc'] = 	$pwcs;
                $pwcsCnt = count($pwcs);
            } else {
                $pwcsCnt = 0;
            }
        }
        if ($hier) { // Hierarchy - calculate priorities from pwc
            if ($pwcsCnt > 0) {
                $ahpH = new AhpHier();
                $ahpH->setHierarchy($_SESSION['hText']);
                foreach ($pwcs as $node=>$pwc) {
                    $npc = count($pwc['A']);
                    $n = (int)(0.5+sqrt(2*$npc+0.25));
                    $ahp = new AhpCalc($n);
                    $ahp->set_evm($pwc);
                    $ev = $ahp->evm_evec;
                    $branch = $ahpH->getTreeNode($ahpH->hierarchy, $node);
                    $nodeTxt = $node . ": ";
                    for ($j = 0; $j < $n; $j++) {
                        $nodeTxt .= $branch[$j] . "=" . round($ev[$j], 8) . ", ";
                    }
                    $nodeTxt = rtrim($nodeTxt, ", ") . ";";
                    $txtNa = explode(":", $nodeTxt);
                    $_SESSION['hText'] = $ahpH->setNewText(
                        $_SESSION['hText'],
                        $node,
                        $txtNa[1]
                    );
                    unset($ahp);
                }
                // pwcDone will be set when calling hierarchy
                $ahpH = new AhpHier();
                $ahpH->setHierarchy($_SESSION['hText']);
                if ($ahpH->pwcDoneFlg) {
                    $_SESSION['pwcDone'] = true;
                } else {
                    unset($_SESSION['pwcDone']);
                }
            }
            $url = $urlAhpH;
        } else { // Alternatives - calculate priorities from pwc
            $ahp = new AhpCalc($altNum);
            if ($pwcsCnt > 0) {
                $ahpH = new AhpHierAlt();
                $ahpH->setHierarchy($_SESSION['hText']);
                $lvCnt = $ahpH->leafCnt;
                $ahpH->altNum = $altNum;
                if (($lvCnt - $pwcsCnt) == 0) {
                    $_SESSION['pwcaDone'] = true;
                } else {
                    $_SESSION['pwcaDone'] = false;
                }
                foreach ($pwcs as $leaf=>$pwc) {
                    // calculate $_SESSION['prioAlt'][$header]
                    $ahp->set_evm($pwc);
                    $ev = $ahp->evm_evec;
                    $_SESSION['prioAlt'][$leaf] = $ev;
                }
            }
            $url = $ahp->getUrlCode(
                $urlAlt,
                $altNum,
                $_SESSION['project'],
                $_SESSION['alt']
            );
        }
        return $url;
    }


    /* delete session record (project)
     *
     */
    public function deleteRecord($sc)
    {
        if ($this->checkSessionCode($sc)) {
            $this->db_connection->exec("PRAGMA foreign_keys = ON;");
            $sql = "DELETE FROM `projects` WHERE `project_sc` = :project_sc;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':project_sc', $sc, PDO::PARAM_STR);
            $deleteStatus = $query->execute();
            if (!$deleteStatus) {
                $this->err[] = DB_DELETE_ERROR;
            }
            return $deleteStatus;
        } else {
            $this->err[] = DB_NOTHING_TO_DELETE;
            return 0;
        }
    }


    /*
     * writes project data into database
     */
    public function writeProjectData(
        $sc,
        $project,
        $description,
        $hText,
        $author,
        $alt=array()
    ) {
        if ($this->checkSessionCode($sc)) {
            $this->err[] = $this->ahpDbTxt->err['scInUse'];
            return false;
        }
        // write project data
        try {
            $sql = "INSERT INTO projects (
                    project_sc, project_name, project_description, project_hText, 
                    project_datetime, project_author) 
                    VALUES (
                     :project_sc, :project_name, :project_description,
                     :project_hText, :project_datetime, :project_author
                    );";
            $queryInsert = $this->db_connection->prepare($sql);
            $insertState =
                $queryInsert->execute(
                    array(':project_sc' => $sc,
                                ':project_name' => $project,
                                ':project_description' => $description,
                                ':project_hText' => $hText,
                                ':project_datetime' => date("Y-m-d H:i:s"),
                                ':project_author' => $author
                               )
                );
            if (!$insertState) {
                $this->err[] = $ahpDSbTxt->err['dbWrite'];
                return false;
            }
            // Alternatives
            if (!empty($alt)) {
                $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                $sql = "INSERT INTO alternatives (project_sc, alt) 
                        VALUES (:project_sc, :alt);";
                $queryIns = $this->db_connection->prepare($sql);
                $queryIns->bindValue(':project_sc', $sc, PDO::PARAM_STR);
                foreach ($alt as $altName) {
                    $queryIns->bindValue(':alt', $altName, PDO::PARAM_STR);
                    $insertState &= $queryIns->execute();
                }
                if (!$insertState) {
                    $this->err[] = $this->ahpDbTxt->err['dbWriteA'];
                    return false;
                }
            }
            return $insertState;
        } catch (PDOException $e) {
            $this->err[] = $this->ahpDbTxt->err['dbWrite'] . $e;
        }
        return false;
    }


    /*
     * Update/modify project data
     */
    public function updateProjectData(
        $sc,
        $project,
        $description,
        $hText,
        $alt=array()
    ) {
        if (!$this->checkSessionCode($sc)) {
            $this->err[] = $this->ahpDbTxt->err['scInv'];
            return false;
        }
        $query = $this->db_connection->prepare(
            "SELECT project_hText FROM projects WHERE project_sc = :sc;"
        );
        $query->bindValue(':sc', $sc, PDO::PARAM_STR);
        $query->execute();
        $phText = $query->fetch(PDO::FETCH_NUM);
        if (count($this->getParticipants($sc)) > 0 && $phText[0] != $hText) {
            $this->err[] = $this->ahpDbTxt->err['pNoMod'];
            return false;
        }
        // update project data
        $this->db_connection->exec("PRAGMA foreign_keys = ON;");
        try {
            $sql = "UPDATE projects SET project_name = :project_name, 
                    project_description = :project_description , 
                    project_hText = :project_hText 
                    WHERE project_sc = :project_sc;";
            $query = $this->db_connection->prepare($sql);
            $state = $query->execute(array(
                                ':project_sc' => $sc,
                                ':project_name' => $project,
                                ':project_description' => $description,
                                ':project_hText' => $hText));
            if (!$state) {
                $this->err[] = $this->ahpDbTxt->err['dbWrite'];
                return false;
            }
        } catch (PDOException $e) {
            $this->err[] = $this->ahpDbTxt->err['dbWrite'] . $e;
        }
        // check, whether project has alternatives
        $sql = "select count(project_sc) FROM alternatives 
                WHERE `project_sc` = :project_sc;";
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':project_sc', $sc, PDO::PARAM_STR);
        $query->execute();
        $acnt = $query->fetch(PDO::FETCH_NUM);
        // first delete existing
        if ($acnt>0) {
            $sql = "DELETE FROM alternatives WHERE `project_sc` = :project_sc;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':project_sc', $sc, PDO::PARAM_STR);
            $deleteStatus = $query->execute();
            if (!$deleteStatus) {
                $this->err[] = DB_DELETE_ERROR;
                return false;
            }
        }
        // Alternatives: then insert new ones
        if (!empty($alt)) {
            // insert new ones
            $this->db_connection->exec("PRAGMA foreign_keys = ON;");
            try {
                $sql = "INSERT INTO alternatives (project_sc, alt) 
                        VALUES (:project_sc, :alt)";
                $queryIns = $this->db_connection->prepare($sql);
                $queryIns->bindValue(':project_sc', $sc, PDO::PARAM_STR);
                foreach ($alt as $altName) {
                    $queryIns->bindValue(':alt', $altName, PDO::PARAM_STR);
                    $state &= $queryIns->execute();
                }
                if (!$state) {
                    $this->err[] = $this->ahpDbTxt->err['sbWriteA'];
                    return false;
                }
            } catch (PDOException $e) {
                $this->err[] = $this->ahpDbTxt->err['dbWrite'] . $e;
                return false;
            }
        }
        return true;
    }


    /* Submit group data
     * pwc array in format as stored in session parameter [node][A], [node][Intense]
     * @para $sc session code
     * @para $name participant
     * @pwc pairwise comparison array
     * @return array update count [''] and insert count ['']
     */
    public function submitGroupData($sc, $name, $pwc)
    {
        $pwcConv = $this->convertPwcToString($pwc);
        $timestamp = time();
        if ($this->dataBaseConnection()) {
            $rslt = $this->checkPwcCons($sc);
            // --- temporary trying to find a possible bug
            if (!empty($rslt)) {
                // Something wrong
                trigger_error("submitGroupData 1st consistency check $sc", E_USER_WARNING);
            }
            $sql = "SELECT pwc_node FROM pwc WHERE project_sc = :sc 
                    AND pwc_part = :part;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->bindValue(':part', $name, PDO::PARAM_STR);
            $query->execute();
            $pwcUpdNod = $query->fetchall(PDO::FETCH_COLUMN);
            $pwcNewNod = array_diff(array_keys($pwcConv), $pwcUpdNod);
            $updCnt = 0;
            $insCnt = 0;

            if (count($pwcUpdNod)>0) {
                // there are already pwcs submitted for nodes
                try {
                    foreach ($pwcUpdNod as $node) {
                        $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                        $sql = "SELECT pwc_ab, pwc_intense FROM pwc 
                                WHERE project_sc = :sc AND pwc_part = :part 
                                AND pwc_node = :nod;";
                        $query = $this->db_connection->prepare($sql);
                        $query->bindValue(':sc', $sc, PDO::PARAM_STR);
                        $query->bindValue(':part', $name, PDO::PARAM_STR);
                        $query->bindValue(':nod', $node, PDO::PARAM_STR);
                        $query->execute();
                        $pwcOld = $query->fetch(PDO::FETCH_ASSOC);
                        if (is_array($pwcConv[$node]) && !empty(array_diff($pwcConv[$node], $pwcOld))) {
                            // Update pwc
                            $sql = "UPDATE pwc SET pwc_timestamp = :ts, pwc_ab = :ab, pwc_intense = :it
                                    WHERE project_sc = :sc AND pwc_part = :part AND pwc_node = :nod;";
                            $query = $this->db_connection->prepare($sql);
                            $query->bindValue(':ts', $timestamp, PDO::PARAM_INT);
                            $query->bindValue(':ab', $pwcConv[$node]['pwc_ab'], PDO::PARAM_STR);
                            $query->bindValue(':it', $pwcConv[$node]['pwc_intense'], PDO::PARAM_STR);
                            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
                            $query->bindValue(':part', $name, PDO::PARAM_STR);
                            $query->bindValue(':nod', $node, PDO::PARAM_STR);
                            if ($query->execute()) {
                                ++$updCnt;
                            } else {
                                $this->err[] = $this->ahpDbTxt->err['dbUpd'];
                                return array();
                            }
                        }
                        unset($pwcConv[$node]);
                    }
                } catch (PDOException $e) {
                    $this->err[] = $this->ahpDbTxt->err['dbUpd'] . $e;
                    return array();
                }
            }
            if (count($pwcNewNod)>0) {
                // write new judgments
                try {
                    $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                    $sql = "INSERT INTO `pwc` (
                            `project_sc`, `pwc_part`, `pwc_timestamp`, `pwc_node`, 
                            `pwc_ab`, `pwc_intense`)
                            VALUES ( :project_sc, :pwc_part, :pwc_timestamp, 
                            :pwc_node, :pwc_ab, :pwc_intense);";
                    $queryIns = $this->db_connection->prepare($sql);
                    $queryIns->bindValue(':project_sc', $sc, PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_part', $name, PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_timestamp', $timestamp, PDO::PARAM_INT);
                    foreach ($pwcConv as $node=>$judgm) {
                        $queryIns->bindValue(':pwc_node', $node, PDO::PARAM_STR);
                        $queryIns->bindValue(':pwc_ab', $judgm['pwc_ab'], PDO::PARAM_STR);
                        $queryIns->bindValue(':pwc_intense', $judgm['pwc_intense'], PDO::PARAM_STR);
                        if ($queryIns->execute()) {
                            ++$insCnt;
                        } else {
                            $this->err[] = $this->ahpDbTxt->err['dbWrite'];
                            return array();
                        }
                    }
                } catch (PDOException $e) {
                    $this->err[] = $this->ahpDbTxt->err['dbWrite'] . $e;
                    return array();
                }
            } else { // no pwcs to insert
                $insCnt = 0;
            }
            $rslt = $this->checkPwcCons($sc);
            // --- temporary trying to find a possible bug
            if (!empty($rslt)) {
                // Something wrong
                trigger_error("submitGroupData consistency check after insert $sc", E_USER_WARNING);
            }
            return array( 'upd' => $updCnt, 'ins' => $insCnt);
        }
        $this->err[] = $this->ahpDbTxt->err['dbSubmit'];
        return array();
    }

    /*
     * Used by displaySessionTable below
     */
    private function getAllSessions($name)
    {
        if ($this->dataBaseConnection()) {
            $sql = "SELECT projects.project_sc, projects.project_name, altcnt, 
                    projects.project_description, projects.project_author, 
                    count(pwpart), date(projects.project_datetime), 
                    projects.project_status FROM projects
                    LEFT JOIN (SELECT DISTINCT pwc.project_sc AS pwsc, 
                    pwc.pwc_part AS pwpart FROM pwc) AS t1 ON projects.project_sc = pwsc 
                    LEFT JOIN (SELECT alternatives.project_sc AS altsc, 
                    count(projects.project_sc) AS altcnt FROM projects, alternatives 
                    WHERE projects.project_sc = alternatives.project_sc 
                    GROUP BY alternatives.project_sc) AS t2 ON projects.project_sc = altsc 
                    WHERE projects.project_author = :name 
                    GROUP BY project_sc ORDER BY projects.project_datetime DESC;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $projects = $query->fetchAll(PDO::FETCH_NUM);
            return $projects;
        }
        return array();
    }

    private function checkAlt($sc)
    {
        $sql = "SELECT DISTINCT project_sc FROM alternatives WHERE project_sc = :sc ;";
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':sc', $sc, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        if (count($result)>0) {
            return true;
        } else {
            return false;
        }
    }


    /* Check pwc consistency: Due to some unknown reason it happens
     * that hierarchy and pwc nodes under the same session code do not
     * match. This functions checks whether the pwc nodes of a project
     * in the pwc table are all nodes or leafs of the corresponding
     * hierarchy
     * @param $psc project session code, '%' = all
     * @uses ahpH
     * @return array
     */
    public function checkPwcCons($psc="%")
    {
        $result = array();
        $pwcnods =array();
        $dn = array();
        if ($this->dataBaseConnection()) {
            // all projects with pwc
            $query = $this->db_connection->prepare(
                "SELECT DISTINCT project_sc FROM pwc
                 WHERE project_sc LIKE :sc;"
            );
            $query->bindValue(':sc', $psc, PDO::PARAM_STR);
            $query->execute();
            $prjSc = $query->fetchAll(PDO::FETCH_COLUMN);
            $pCnt = 0;
            $dn = array();
            $rslt = array();
            $sql = "SELECT project_author, project_hText from projects 
                    WHERE project_sc = :sc;";
            /* for each project get author and hierarchy text */
            foreach ($prjSc as $sc) {
                $query = $this->db_connection->prepare($sql);
                $query->bindValue(':sc', $sc, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    $user =  $result[0]['project_author'];
                    $hText = $result[0]['project_hText'];
                    $text = $ahpH = new AhpHier();
                    $ahpH->setHierarchy($hText);
                    $query = $this->db_connection->prepare(
                        "SELECT DISTINCT pwc.pwc_node FROM pwc 
                         WHERE project_sc = :sc;"
                    );
                    $query->bindValue(':sc', $sc, PDO::PARAM_STR);
                    $query->execute();
                    $pwcnods = $query->fetchAll(PDO::FETCH_COLUMN);
                    $nCnt = 0;
                    // $dn: pwcs nodes not in the hierarchy
                    if ($this->checkAlt($sc)) {
                        $aflg = true;
                        $dn = array_diff(array_values($pwcnods), $ahpH->leafs);
                    } else {
                        $aflg = false;
                        $dn = array_diff(array_values($pwcnods), $ahpH->nodes);
                    }
                    if (!empty($dn)) {
                        $rslt[$pCnt]['user'] =  $user;
                        $rslt[$pCnt]['sc'] = $sc;
                        $rslt[$pCnt]['nodes'] = $dn;
                        $rslt[$pCnt++]['type'] = ($aflg ? "a" : "h");
                    }
                }
                unset($ahpH);
            }
            return $rslt;
        }
    }


    /*
     * List of top users based on
     * (1) audit table login entries,
     * (2) project participants,
     * (3) number of projects
     * (4) complexity of project (chars in hierarchy).
     *
     * @return array [0] username [1] usage index (0 - 100), [2] last login
     *
     * Views:
     * pcntv:	project count view, number of projects per user (pCnt)
     * 			number of characters in hierarchy text (hCnt)
     * acnt:	number of audit entries for login per user (aCnt)
     * dmcntv:	number of participants in users projects (dmCnt)
     * alv:		Summary view of all three above weighted and combined
     *
     * This method is called from dbintegrity
     *
     */
    public function getTopUsers($lmt = 25)
    {
        if ($this->dataBaseConnection()) {

// View for number of audit Login entries per user
            $sql = "CREATE VIEW IF NOT EXISTS acntv AS
            SELECT audit.a_un as user, count(audit.a_ts) as aCnt FROM audit
            JOIN users ON users.user_name = audit.a_un
            WHERE audit.a_act LIKE 'Login'
            GROUP BY audit.a_un
            ORDER BY aCnt DESC;";
            $query = $this->db_connection->prepare($sql);
            $query->execute();

            // View for number of participants in users's projects
            $sql = "CREATE VIEW IF NOT EXISTS dmcntv AS
            SELECT projects.project_author, count(pwpart) as dmCnt FROM projects
            LEFT JOIN (SELECT DISTINCT pwc.project_sc AS pwsc, pwc.pwc_part AS pwpart 
            FROM pwc) AS t 
            ON projects.project_sc = pwsc
            GROUP BY projects.project_author
            ORDER BY dmCnt DESC;";
            $query = $this->db_connection->prepare($sql);
            $query->execute();

            // View for number of projects and chars in hierarchy text
            $sql = "CREATE VIEW IF NOT EXISTS pcntv AS
            SELECT projects.project_author, count(projects.project_sc) as pCnt, 
            sum(length(projects.project_hText)) as hCnt FROM projects 
            GROUP BY projects.project_author 
            ORDER BY pCnt DESC;";
            $query = $this->db_connection->prepare($sql);
            $query->execute();

            // View for combined index for all users, limited to $lmt +1
            $sqlite = "CREATE VIEW IF NOT EXISTS alv AS
            SELECT pcntv.project_author,
            ROUND(min(20,pcntv.pCnt) + 0.05 * min(300,acntv.aCnt) 
            + 0.003 * min(10000,pcntv.hCnt) 
            + 0.175 * min(200,dmcntv.dmCnt)) as actlv,
            users.user_last_login  
            FROM pcntv
            JOIN dmcntv ON pcntv.project_author = dmcntv.project_author
            JOIN acntv ON pcntv.project_author = acntv.user
            JOIN users ON pcntv.project_author = users.user_name
            ORDER BY min(20,pcntv.pCnt) + 0.05 * min(300,acntv.aCnt) 
            + 0.003 * min(10000,pcntv.hCnt) 
            + 0.175 * min(200,dmcntv.dmCnt) DESC
            LIMIT " . ($lmt+1) . ";";

            $mysql ="CREATE VIEW IF NOT EXISTS alv AS
            SELECT pcntv.project_author,
            ROUND(LEAST(20,pcntv.pCnt) + 0.05 * LEAST(300,acntv.aCnt) 
                + 0.003 * LEAST(10000,pcntv.hCnt) 
                + 0.175 * LEAST(200,dmcntv.dmCnt)) as actlv,
            users.user_last_login  
            FROM pcntv
            JOIN dmcntv ON pcntv.project_author = dmcntv.project_author
            JOIN acntv ON pcntv.project_author = acntv.user
            JOIN users ON pcntv.project_author = users.user_name
            ORDER BY actlv DESC
            LIMIT " . ($lmt+1) . ";";

            $sql = ($this->db_type == 'sqlite' ? $sqlite : $mysql);
            $query = $this->db_connection->prepare($sql);
            $query->execute();

            $sql = "SELECT * FROM alv LIMIT " . $lmt . ";";
            $query = $this->db_connection->prepare($sql);
            $query->execute();
            $act = $query->fetchall(PDO::FETCH_NUM);
            return $act;
        }
    }


    /*
     * User activity level: same as getTopUsers above for selected user $name
     * @return array with aCnt (number of audit table entries),
     * pCnt (no of projects), hCnt (no of chars in hierarchies),
     * dmCnt (no of participants in projects),
     * This method is called from do-user-admin
     */
    public function getActivityLevel($name)
    {
        if ($this->dataBaseConnection()) {
            $act = array();
            // --- aCnt
            $sql = "SELECT count(audit.a_ts) as 'aCnt' FROM audit 
                    JOIN users ON users.user_name = audit.a_un
                    WHERE audit.a_un =:name AND audit.a_act LIKE 'Login';";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $act = $query->fetch(PDO::FETCH_ASSOC);
            // --- pCnt, hCnt
            $sql = "SELECT count(projects.project_sc) as 'pCnt', 
                    sum(length(projects.project_hText)) as 'hCnt' from projects 
                    WHERE projects.project_author =:name;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $act = array_merge($act, $query->fetch(PDO::FETCH_ASSOC));
            // --- dmCnt
            $sql = "SELECT count(pwpart) as 'dmCnt' FROM projects
                    LEFT JOIN (SELECT DISTINCT pwc.project_sc AS pwsc, 
                    pwc.pwc_part AS 'pwpart' FROM pwc) AS t1
                    ON projects.project_sc = pwsc
                    WHERE projects.project_author=:name
                    GROUP BY projects.project_author;";
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $dm = $query->fetch(PDO::FETCH_ASSOC);
            if (is_array($dm)) {
                $act = array_merge($act, $dm);
            } else {
                $act['dmCnt'] = 0;
            }
            // --- calculate combined index actlv
            $actlv = round(
                min(20, $act['pCnt'])
            + 0.050 * min(300, $act['aCnt'])
            + 0.003 * min(10000, $act['hCnt'])
            + 0.175 * min(200, $act['dmCnt']),
                1
            );
            $act = array_merge($act, array("actlv" => $actlv));
            return($act);
        }
        return array();
    }


    /* --- ahp-user-recover functions ---/

    /*
     * get user account data for $user_name
     */
    private function getAccountData($userName)
    {
        if ($this->dataBaseConnection()) {
            $query = $this->db_connection->prepare(
                "SELECT * FROM `users` WHERE `user_name` = :user_name"
            );
            $query->bindValue(':user_name', $userName, PDO::PARAM_STR);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        }
    }


    /*
     * get array of all projects from $user
     * with session coder LIKE $sc. Without sc given
     * will return all projects
     */
    public function getAllProjects($user, $sc="%")
    {
        if ($this->databaseConnection()) {
            $query = $this->db_connection->prepare(
                "SELECT * FROM `projects` 
                WHERE `project_author` = :user_name
                AND `project_sc` LIKE :sc;"
            );
            $query->execute(array(':user_name' => $user,
                                ':sc' => $sc));
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }


    /*
     * get all pairwise comparisons for project with session code $sc
     */
    public function getAllPwc($sc)
    {
        if ($this->databaseConnection()) {
            $query = $this->db_connection->prepare(
                "SELECT * FROM `pwc` WHERE `project_sc` = :sc"
            );
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }


    /*
     *  get all alternatives for projects with session code $sc
     */
    public function getAllAlt($sc)
    {
        if ($this->databaseConnection()) {
            $query = $this->db_connection->prepare(
                "SELECT * FROM `alternatives` WHERE `project_sc` = :sc"
            );
            $query->bindValue(':sc', $sc, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }



    /*
     * Get complete user data from $userName and return as $ahpUser array
     */
    public function getUser($userName)
    {
        $ahpUser = array();
        if ($this->databaseConnection()) {
            $ahpUser['user'] = $this->getAccountData($userName);
            $ahpUser['projects'] = $this->getAllProjects($userName);
            $pwc = array();
            $a = array();
            foreach ($ahpUser['projects'] as $p) {
                $r = $this->getAllPwc($p['project_sc']);
                if (!empty($r)) {
                    foreach ($r as $row) {
                        $pwc[]= $row;
                    }
                }
                $al = $this->getAllAlt($p['project_sc']);
                if (!empty($al)) {
                    foreach ($al as $row) {
                        $a[] = $row;
                    }
                }
            }
            $ahpUser['pwc'] = $pwc;
            $ahpUser['a'] = $a;
            return $ahpUser;
        }
    }

    /*
     * Restore user
     */
    public function restoreUser($user, $projects, $pwc, $alt=array())
    {
        $insState = true;
        if ($this->databaseConnection()) {
            $this->db_connection->exec("SET autocommit=0");
            $this->db_connection->exec(($this->db_type == 'sqlite' ? "BEGIN TRANSACTION;" : "START TRANSACTION;"));

            // write user data
            try {
                $sql = "INSERT INTO `users` 
            (`user_name`, `user_password_hash`, `user_email`, `user_active`, 
             `user_registration_ip`, `user_registration_datetime`, `user_last_login`) 
            VALUES( :user_name, :user_password_hash, :user_email, :user_active,
             :user_registration_ip, :user_registration_datetime, :user_last_login);";
                $queryIns = $this->db_connection->prepare($sql);
            } catch (PDOException $e) {
                $this->err[] = DB_PROJECT_WRITE_ERROR . $e;
            }
            if (is_object($queryIns)) {
                $queryIns->bindValue(':user_name', $user['user_name'], PDO::PARAM_STR);
                $queryIns->bindValue(':user_password_hash', $user['user_password_hash'], PDO::PARAM_STR);
                $queryIns->bindValue(':user_email', $user['user_email'], PDO::PARAM_STR);
                $queryIns->bindValue(':user_active', 0, PDO::PARAM_INT);
                $queryIns->bindValue(':user_registration_ip', $user['user_registration_ip'], PDO::PARAM_STR);
                $queryIns->bindValue(':user_registration_datetime', $user['user_registration_datetime'], PDO::PARAM_STR);
                $queryIns->bindValue(':user_last_login', $user['user_last_login'], PDO::PARAM_STR);
                $insState = $queryIns->execute();
            }
            if ($insState != true) {
                $this->err[] = "User insert failed ";
                $this->db_connection->exec("ROLLBACK;");
                return false;
            }
            $user_id = $this->db_connection->lastInsertId();
            // write projects
            if (is_array($projects) && count($projects)>0) {
                try {
                    $sql = "INSERT INTO `projects` 
                (`project_sc`, `project_name`, `project_description`, 
                 `project_hText`, `project_datetime`, `project_author`) 
                VALUES ( :project_sc, :project_name, :project_description, 
                 :project_hText, :project_datetime, :project_author);";
                    $queryInsert = $this->db_connection->prepare($sql);
                } catch (PDOException $e) {
                    $this->err[] = DB_PROJECT_WRITE_ERROR . $e;
                }
                if (is_object($queryInsert)) {
                    foreach ($projects as $p) {
                        // write project data
                        $queryInsert->bindValue(':project_sc', $p['project_sc'], PDO::PARAM_STR);
                        $queryInsert->bindValue(':project_name', $p['project_name'], PDO::PARAM_STR);
                        $queryInsert->bindValue(':project_description', $p['project_description'], PDO::PARAM_STR);
                        $queryInsert->bindValue(':project_hText', $p['project_hText'], PDO::PARAM_STR);
                        $queryInsert->bindValue(':project_datetime', $p['project_datetime'], PDO::PARAM_STR);
                        $queryInsert->bindValue(':project_author', $p['project_author'], PDO::PARAM_STR);
                        $insState &= $queryInsert->execute();
                        if (!$insState) {
                            $err[] = DB_PROJECT_WRITE_ERROR;
                        }
                    }
                }
            }
            if (!$insState) {
                $this->db_connection->exec("ROLLBACK;");
                $this->err[] = "Project insert failed ";
                return false;
            }
            // write pwc
            if (is_array($pwc) && count($pwc)>0) {
                try {
                    $sql = "INSERT INTO pwc ( project_sc, pwc_part, pwc_timestamp, pwc_node, pwc_ab, pwc_intense ) 
                        VALUES ( :project_sc, :pwc_part, :pwc_timestamp, :pwc_node, :pwc_ab, :pwc_intense );";
                    $queryIns = $this->db_connection->prepare($sql);
                } catch (PDOException $e) {
                    $err[] = DB_PROJECT_WRITE_ERROR . $e;
                }
                foreach ($pwc as $c) {
                    $queryIns->bindValue(':project_sc', $c['project_sc'], PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_part', $c['pwc_part'], PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_timestamp', $c['pwc_timestamp'], PDO::PARAM_INT);
                    $queryIns->bindValue(':pwc_node', $c['pwc_node'], PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_ab', $c['pwc_ab'], PDO::PARAM_STR);
                    $queryIns->bindValue(':pwc_intense', $c['pwc_intense'], PDO::PARAM_STR);
                    $insState &= $queryIns->execute();
                }
                if (!$insState) {
                    $this->db_connection->exec("ROLLBACK;");
                    $this->err[] = DB_PROJECT_WRITE_ERROR;
                    return false;
                }
            }
            // write Alternatives
            if (is_array($alt) && count($alt)>0) {
                try {
                    $this->db_connection->exec("PRAGMA foreign_keys = ON;");
                    $sql = "INSERT INTO alternatives ( project_sc, alt) 
                        VALUES ( :project_sc, :alt);";
                    $queryIns = $this->db_connection->prepare($sql);
                } catch (PDOException $e) {
                    $this->err[] = DB_PROJECT_WRITE_ERROR . $e;
                }
                foreach ($alt as $a) {
                    $queryIns->bindValue(':project_sc', $a['project_sc'], PDO::PARAM_STR);
                    $queryIns->bindValue(':alt', $a['alt'], PDO::PARAM_STR);
                    $insState &= $queryIns->execute();
                }
                if (!$insState) {
                    $this->db_connection->exec("ROLLBACK;");
                    $this->err[] = DB_PROJECT_WRITE_ERROR;
                    return false;
                }
            }
            // write audit table
            try {
                $sql = "INSERT INTO audit ( a_trg, a_uid, a_un, a_act) 
                    VALUES ( 'R', :a_uid, :a_un, 'Backup recovery' );";
                $queryIns = $this->db_connection->prepare($sql);
            } catch (PDOException $e) {
                $this->err[] = DB_PROJECT_WRITE_ERROR . $e;
            }
            $queryIns->bindValue(':a_uid', $user_id, PDO::PARAM_INT);
            $queryIns->bindValue(':a_un', $user['user_name'], PDO::PARAM_STR);
            $insState &= $queryIns->execute();
            if (!$insState) {
                $this->databaseConnection->exec("ROLLBACK;");
                $thisd->err[] = DB_PROJECT_WRITE_ERROR;
                return false;
            }
            // --- COMMIT all inserts
            $this->db_connection->exec("COMMIT;");
            $this->db_connection->exec("SET autocommit=0");
            return true;
        }
        $this->err[] .= "Restore error for $userName, no database connection.";
        return false;
    }


    /*
     * HTML output of session table (projects) from user $name
     */
    public function displaySessionTable($name)
    {
        global $urlSessionAdmin;
        $projects = $this->getAllSessions($name);
        if (empty($projects)) {
            echo $this->ahpDbTxt->msg['noSess'];
            return;
        }
        echo "\n<!-- DISPLAY SESSION TABLE -->\n";
        echo "<div class='ofl'>";
        echo "<table>";
        echo $this->ahpDbTxt->tbl["scTblTh"];
        echo "<tbody>";
        $i = 0;
        foreach ($projects as $project) {
            $url = $urlSessionAdmin;
            $url .= "?sc=" . urlencode($project[0]);
            $url .= "&pn=" . urlencode($project[4]);
            echo "<tr>";
            echo "<td class='ca'>", ++$i, "</td>";
            echo "<td><span class='res'><a href='$url'>", $project[0], "</a></td>";
            echo "<td>", $project[1], "</td>";
            echo "<td class='ca'>", ($project[2]>0 ? "A" : "H"), "</td>";
            echo "<td class='ca'>",($project[7] == 1 ? "open" : "closed"),"</td>";
            echo "<td style='max-width:380px;'>", $project[3], "</td>";
            echo "<td class='ca'>", $project[5], "</td>";
            $fmt = ($project[6] == date('Y-m-d') ? " class = 'hl'" : "");
            echo "<td class='nwr'>", $project[6], "</td>";
            echo "</tr>\n";
        }
        echo "</tbody>";
        echo $this->ahpDbTxt->tbl['scTblFoot'];
        echo "</table></div>";
    }


    /*
     * HTML Display of detailed information about the project (Project Summary)
     * @return array of selected participants
     */
    public function displayProjectDetails($sc, $sel=true)
    {
        $project = $this->readProjectData($sc);
        if (!empty($project)) {
            $ptype = (isset($project['project_alt']) ? "Alternatives" : "Hierarchy");
            $psel = array();
            echo "\n<!-- DISPLAY PROJECT DATA -->\n";
            echo "<div style='max-width:550px;height:auto;float:left;padding:10px;'>\n";
            // Project Data
            echo $this->ahpDbTxt->titles['h3pDat'];
            echo "<table id='pdTbl'>";
            echo $this->ahpDbTxt->tbl['pdTblTh'];
            echo "<tbody>";
            printf($this->ahpDbTxt->tbl['pdTblR1'], $project['project_sc']);
            printf($this->ahpDbTxt->tbl['pdTblR2'], $project['project_name']);
            printf($this->ahpDbTxt->tbl['pdTblR3'], $project['project_description']);
            printf($this->ahpDbTxt->tbl['pdTblR4'], $project['project_author']);
            printf($this->ahpDbTxt->tbl['pdTblR5'], $project['project_datetime']);
            printf(
                $this->ahpDbTxt->tbl['pdTblR6'],
                ($project['project_status'] == 1 ? "open" : "<span class='hl'>closed</span>")
            );
            printf($this->ahpDbTxt->tbl['pdTblR7'], $ptype);
            echo "</tbody></table>";
            echo "</div>";
            // Alternatives
            if ($ptype == "Alternatives") {
                echo "\n<!-- DISPLAY ALTERNATIVE TABLE -->\n";
                echo "<div style='width:auto;height:auto;float:left;padding:10px;'>";
                echo $this->ahpDbTxt->titles['h3pAlt'];
                echo "<table>";
                echo $this->ahpDbTxt->tbl['paTblTh'];
                $i = 0;
                foreach ($project['project_alt'] as $alt) {
                    echo "<tr><td class='ca'>", ++$i, "</td><td class='res'>$alt</td></tr>";
                }
                echo "</table></div>";
            }
            // Participants
            $part = $this->readParticipantData($sc);
            if (count($part) > 0) {
                $psel = $this->displayParticipantTable($part, $sel);
            }
            echo "<div style='clear:both;'></div>";
            return $psel;
        }
    }


    /*
     * display list of participants $part
     * called by displayProjectDetails
     * contains a form to select individual participants
     * @return void
     * @uses $_SESSION['ipart']
     * @uses $_SESSION['ptick']
     * @uses $_SESSION['ntick']
     *
     */
    private function displayParticipantTable($part, $sel)
    {
        $partSel = array(); // array of selected participants
        echo "\n<!-- DISPLAY PARTICIPANT TABLE -->\n";
        echo "<script src='js/sh-part.js'></script>";
        echo "<div style='float:left;padding:10px;'>";
        echo $this->ahpDbTxt->titles["h3pPart"];
        $pCnt = count($part);
        if ($pCnt>10) {
            printf($this->ahpDbTxt->info['shPart'], $pCnt);
        }
        // --- Select participants form ---
        echo "<form method='POST' action=" . $_SERVER['PHP_SELF'] . " >\n";
        echo "<table id='ppTbl'>";
        echo $this->ahpDbTxt->tbl['ppTblTh'];
        echo "<tbody>";
        // each participant is one row
        for ($i=0; $i < $pCnt; $i++) {
            echo "<tr ", ($i>9 ? "class='tgl'" : " "), "><td class='ca'>", $pCnt-$i, "</td>";
            if (isset($_POST['ipart'])) {
                echo "<td class='ca'><input class='onclk0' type='checkbox' name='ipart[$i]' value='1' ",
                    (isset($_POST['ipart'][$i]) ? " checked " : ""), "></td>";
            } else { // all selected by default
                echo "<td class='ca'><input class='onclk0' type='checkbox' name='ipart[$i]' value='1' ",
                    (isset($_SESSION['ipart'][$i]) ? " checked " : ""), "></td>";
            }
            echo "<td class='res'>",$part[$i][0],"</td><td>",date('Y-m-d', $part[$i][1]),"</td></tr>\n";
        }
        if ($sel) {
            echo $this->ahpDbTxt->tbl['ppTblLr1'], (isset($_SESSION['ptick']) ? " checked " : ""),
             $this->ahpDbTxt->tbl['ppTblLr2'], (isset($_SESSION['ntick']) ? " checked " : ""),
             $this->ahpDbTxt->tbl['ppTblLr3'];
        }
        echo "</tbody>\n";
        if ($sel && count($part) > 1) {
            echo $this->ahpDbTxt->tbl['ppTblFoot'];
        }
        echo "</table>";
        echo "</form>\n";
        echo "</div>\n";
    }


    /*
     * get array of selected participants for project $sc
     * from either $_POST['ipart'] or $_SESSION['ipart']
     * store in $_SESSION['ipart'] for csv download function
     * @uses $_SESSION['ipart'], $_POST['ipart']
     * @return array of selected participants' names
     */
    public function getSelectedParticipants($sc)
    {
        $partSel = array(); // array of selected participants
        $part = $this->readParticipantData($sc);
        if (isset($_POST['ptick'])) {
            // tick all
            $_POST['ipart'] = array_fill(0, count($part), 1);
        } elseif (isset($_POST['ntick'])) {
            unset($_POST['ipart']);
        }
        // array of selected participants
        if (isset($_POST['pselect']) && !empty($_POST['ipart'])) {
            // participants were selected
            unset($_SESSION['ipart']);
            foreach ($_POST['ipart'] as $kp=>$pi) {
                $partSel[] = $part[$kp][0];
                $_SESSION['ipart'][$kp] = $part[$kp][0];
            }
            return($partSel);
        } elseif (empty($_POST['pselect']) && isset($_SESSION['ipart'])) {
            // no refresh of selections
            $partSel= $_SESSION['ipart'];
        } else {
            // no selection, take all participants
            foreach ($part as $kp=>$pi) {
                $partSel[] = $part[$kp][0];
            }
            unset($_SESSION['ipart']);
        }
        return($partSel);
    }


    /*
     * csv export function for project details
     */
    public function exportProjectDetails($sc, $ds)
    {
        $textout = array();
        $part=array();
        $fs = ($ds == ',' ? ';' : ',');
        $pjd = $this->readProjectData($sc);
        $part = $this->getSelectedParticipants($sc);
        $partCnt = count($part);
        $hiermode = (isset($pjd['project_alt']) ? false : true);
        $ahpC = new AhpCalc(0);
        if ($hiermode) {
            $nodes = explode(";", $pjd['project_hText']);
        }
        // --- first line tells Excel the character used as field seperator
        $textout[] = "sep=" . $fs . self::NEWL;

        // --- Header (Project information)
        $textout[] = self::ENCL . "Session Code" . self::ENCL . $fs
                    . self::ENCL . $sc . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Project Name" . self::ENCL .$fs
                    . self::ENCL . $pjd['project_name'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Description" . self::ENCL . $fs
                    . self::ENCL . $pjd['project_description'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Author" . self::ENCL . $fs
                    . self::ENCL . $pjd['project_author'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Created" . self::ENCL . $fs
                    . self::ENCL . $pjd['project_datetime'] . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "Evaluation type" . self::ENCL . $fs
                    . self::ENCL
                        . (isset($pjd['project_alt']) ? "Alternatives" : "Hierarchy")
                    . self::ENCL . self::NEWL;
        $textout[] = self::ENCL . "No of Participants"
                    . self::ENCL . $fs . $partCnt . self::NEWL;

        // --- Get decision matrices
        // loop through participants
        foreach ($part as $name) {
            $textout[] = self::ENCL . "Participant" . self::ENCL . $fs
                    . self::ENCL . $name . self::ENCL . self::NEWL;
            $pwcA = $this->getPwcArray($sc, $name);
            if ($hiermode) {
                // Hierarchy Mode
                // to be added: leafs of node
                $textout[] = self::ENCL . "" . self::ENCL . self::NEWL;
            } else {
                // Alternative Mode
                $buf = self::ENCL . "Alternatives" . self::ENCL . $fs;
                foreach ($pjd['project_alt'] as $alternative) {
                    $buf .= self::ENCL . $alternative . self::ENCL . $fs;
                }
                $textout[] = $buf .  self::NEWL;
            }

            // loop through nodes/criteria
            $in=0;
            foreach ($pwcA as $node=>$pwc) {
                $buf = "";
                $pwcCnt = count($pwc['A']);
                // matrix dimension from number of pairwise comparisons
                $n = 1 + (sqrt(8*$pwcCnt + 1)-1)/2;
                $textout[] = self::ENCL . ($hiermode ? "Node" : "Criterion")
                        . self::ENCL . $fs
                        . self::ENCL . $node . self::ENCL . $fs
                        . self::ENCL . $n . "x" . $n . " decision matrix"
                        . self::ENCL . self::NEWL;
                $dm = $ahpC->getMatrixFromPwc($pwc);
                for ($i=0; $i<$n; $i++) {
                    $buf .= $fs . $fs;
                    for ($j=0; $j<$n; $j++) {
                        $buf .= self::ENCL . number_format($dm[$i][$j], ROUND, $ds, "")
                         . self::ENCL . $fs;
                    }
                    $buf .= self::NEWL;
                }
                $textout[] = $buf;
            }
            $textout[] = self::NEWL;
        }
        return implode($textout);
    }
} // end class ahpDb
