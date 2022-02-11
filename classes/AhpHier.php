<?php
/*
 * Analytic Hierarchy Process Hierarchy base class 2014-01-06
 * Includes the parser setHierarchy($text) to convert users text
 * input $hText for the decision hierarchy into a hierarchical array
 * $hierarchy (tree). In order to get the tree elements and parameters
 * like nodes, leafs, span etc., $hierarchy is converted to a
 * $flatarray.
 *
 * The class also contains HTML table output displayHierarchyTable(...)
 * and displayHierarchyInfo() to display the hierarchy and output
 * hierarchy information.
 *
 * With exportHierarchyTable($ds)csv output is generated for download.
 *
 * $LastChangedDate: 2022-02-11 08:19:55 +0800 (Fr, 11 Feb 2022) $
 * $Rev: 120 $
 *
 * @package AHP online
 * @author Klaus D. Goepel
 * @copyright 2014 Klaus D. Goepel
 * @since   2014-02-11
 * @version 2017-10-07 total rework of displayHierarchyTable last version w/o SVN
 *
 * @uses $_SESSION['hText', 'project', 'alt', 'pwc', 'nodPd']
 *
 *  Copyright (C) 2022  <Klaus D. Goepel>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */
class AhpHier
{
/** Class Constants */

    public const FLAT_DELIM = '~'; // delimiter for flat hierarchy
    public const TRIM_CHAR = " \t\n\r\0\x0B";
    public const NEWL = "\n";      // for csv file export
    public const ENCL = '"';

    public const TXTMAX = 6000;    // max number of chars defining the hierarchy
public const NODE_CNT = 50;    // max number of nodes
public const LEAF_MAX = 100;   // max number of leafs
public const LEVEL_MAX = 6;    // max number of hierarchy levels
public const CALC_TOL = 1.E-4; // calculation tolerance for sum of priorities

/** Class Properties */

    /* Main node of the hierarchy is taken as project name */
    public $project;	   			// Project name

    /* A flat array describing the hierarchy */
    protected $flatarray = array();	// flat hiearchy

    public $hText = ""; 			// Text string defining the hierarchy
public $hierarchy = array();	// Hierarchy as array

public $level; 					// number of hierarchy levels (depth)

    public $nodes = array();		// text array of hierarchy nodes
public $leafs = array();		// leafs (end points) of the heararchy

public $nodeCnt;				// number of nodes (categories) of the heararchy
public $leafCnt; 				// number of leafs (end points) of the heararchy

public $nodPd = array();		// nodes with pre-defined priorities
public $pLoc = array();			// local priorities for leafs in each node,
                                // keys are name of leafs

public $defLeafs = array();		// array with pre-defined priorities (float)

    public $priorities = array();	// Priority vectors, keys are node/leaf names
public $pGlb = array();			// global priorities for leafs, key is leaf name

/* Handling of errors and warninigs */
    public $err = array();			// error messages
public $wrn = array();			// warnings

/* flag to indicate completion of pairwise comparisons */
    public $pwcDoneFlg = false;   	// pairwise comparison done
public $pwcaDoneFlg  = false; 	// alternative evaluation done

private $pwcDone = array();
    private $colors;
    public $ahpHierTxt;				// language class

    /** Methods */

    public function __construct()
    {
        mb_internal_encoding('UTF-8');
        global $lang;
        $class = get_class() . $lang;
        $this->ahpHierTxt = new $class();
        $this->colors = new AhpColors();

        if (!defined('WLMAX')) {
            define('WLMAX', 45);
        }// max word lenght for hierarchy elements
        if (!defined('ROUND')) {
            define('ROUND', 6);
        } // rounding for csv export

        if (isset($this->hText) && $this->hText !="") {
            $this->hierarchy = $this->setHierarchy($this->hText);
            $this->getArrayDepth($this->hierarchy);
        }
        $this->altNum = 2;
        return;
    }


    /* get new hierarchy text and reset selected session parameters
     * unsets session parameter to prepare the submission of a new hierarchy structure
     * @return void
     */
    public function newHsession()
    {
        $_SESSION['tstart'] = gettimeofday(true);
        $_SESSION['hText'] = filter_input(INPUT_POST, 'hierarchy', FILTER_SANITIZE_STRING);
        unset($_SESSION['pwc']);
        unset($_SESSION['alt']);
        unset($_SESSION['altNum']);
        unset($_SESSION['prioAlt']);
        $this->pwcDoneFlg = false;
        $this->pwcaDoneFlg = false;
        unset($_SESSION['pwcDone']);
        unset($_SESSION['pwcaDone']);
        unset($_SESSION['prioTot']);
        //	unset ($_SESSION['sessionCode']);
    }


    /* Reset all session parameters
     * called from ahp-hierarchy when reset all
     */
    public function closeHier()
    {
        $this->pwcDoneFlg = false;
        $this->pwcaDoneFlg = false;
        unset($_SESSION['hText']);
        unset($_SESSION['project']);
        unset($_SESSION['description']);
        unset($_SESSION['name']);
        unset($_SESSION['owner']);
        unset($_SESSION['pwc']);
        unset($_SESSION['pwcDone']);
        unset($_SESSION['nodPd']);
        unset($_SESSION['prioTot']);

        unset($_SESSION['prioAlt']);
        unset($_SESSION['altNum']);
        unset($_SESSION['alt']);
        unset($_SESSION['pwcaDone']);

        unset($_SESSION['sessionCode']);
        unset($_SESSION['groupSession']);
    }


    /* Clear pairwise comparison session parameters
     * called from ahp-hierarchy when reset priorities
     */
    public function clearPwc()
    {
        unset($_SESSION['pwc']);
        unset($_SESSION['prioTot']);
        if (isset($_SESSION['prioAlt'])) {
            $this->pwcaDoneFlg = false;
            unset($_SESSION['prioAlt']);
            unset($_SESSION['pwcaDone']);
            unset($_SESSION['alt']);
            unset($_SESSION['altNum']);
            $this->altNum = 0;
        }
    }


    /* Clear alternative session parameters
     * called from ahp-hierarchy when reset priorities
     */
    public function clearAlternatives()
    {
        unset($_SESSION['altNum']);
        unset($_SESSION['alt']);
        unset($_SESSION['pwcaDone']);
        unset($ahpH->prioAlt);
        $ahpH->altNum = $altnum = 2;
        $ahpH->alt = array();
    }


    /* Calculates the depth (levels) of a hierarchical array
     * recursive
     * sets number of levels $this->level
     * @return int $max_depth level (depth) of a hierarchy
     */
    private function getArrayDepth(&$array)
    {
        $max_depth= 1;
        foreach ($array as $val) {
            if (is_array($val)) {
                $depth = $this->getArrayDepth($val)+1;
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $this->level = $max_depth;
    }


    /* Returns the span (total number of leafs) for a $node (recursive)
     * used to calculate row_span in hierarchy table
     * @param array $array Hierarchy
     * @param string $node requested node in Hierarchy
     * @return int $res number of leafs, or NULL if nodr not found
     * public for ahp_alt
     */
    private function getNodeSpan(&$array, $node)
    {
        $ret=null;
        if (array_key_exists($node, $array)) {
            return($this->getTreeSpan($array[$node]));
        }
        // search for $node
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $ret = $this->getNodeSpan($v, $node);
            } elseif ($k===$v) {
                return(0);
            }
            if ($ret) {
                return $ret;
            }
        }
        return false;
    }


    /* getTreeSpan returns the span (total number of leafs)
     * of the hierarchy (recursive)
     * Used in getNodeSpan and to display number of leafs
     * @param array $array
     * @return int $count total number of leafs, or FALSE on error
     */
    private function getTreeSpan($array)
    {
        $count=0;
        if (is_array($array)) {
            foreach ($array as $val) {
                if (is_array($val)) {
                    $count += $this->getTreeSpan($val);
                } else {
                    $count++;
                }
            }
            return $count;
        }
        return false;
    }


    /* Flattens array, preserving keys
     * sets property flatarray
     * @param array $array hierarchy
     * @param string $prefix
     * @return array flatarray
     */
    protected function setFlatArray(&$array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : self::FLAT_DELIM) . $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->setFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $this->flatarray = $result;
    }


    /* Returns all nodes of a tree (recursive)
     * Used to calculate default priorities
     * @param array $array hierarchy
     * @return array of all nodes in the hierarchy
     * @uses $this->getTreeNode
     */
    private function getTreeNodes(&$array)
    {
        static $nodes="";
        if (is_array($array)) {
            foreach ($array as $k =>$v) {
                if (is_array($v)) {
                    $nodes .= $k . self::FLAT_DELIM;
                }
                $this->getTreeNodes($v);
            }
        }
        return(explode(self::FLAT_DELIM, rtrim($nodes, self::FLAT_DELIM)));
    }


    /* Returns array of leafs for node $nd
     * Used to calculate default priorities
     * called from ahpGroup
     * @param array $array hierarchy
     * @param string $nd node
     * @return array $nods array of leafs under node
     * @uses $this->getTreeNode
     */
    public function getTreeNode(&$array, $nd)
    {
        if (is_array($array)) {
            foreach ($array as $k =>$v) {
                if ((is_array($v)) && ($k === $nd)) {
                    foreach ($v as $key => $val) {
                        if (is_numeric($key)) {
                            $nods[] = $val;
                        } else {
                            $nods[] = $key;
                        }
                    }
                } else {
                    $nods = $this->getTreeNode($v, $nd);
                }
                if (isset($nods)) {
                    return $nods;
                }
            }
        }
    }


    /* Returns array of leafs in the hierarchy
     * @uses $this->flatarray
     * @return array of all leafs in the hierarchy
     */
    private function getLeafs()
    {
        $leafs= array();
        foreach ($this->flatarray as $node=>$el) {
            $nda = explode(self::FLAT_DELIM, $node);
            $lv = count($nda);
            $ktxt="";
            for ($j= $lv-2 ; $j>0; $j--) {
                $ktxt .= $nda[$j] . " ";
            }
            $leafs[] = $el;
        }
        return $leafs;
    }


    /* Sets local priorities $this->pLoc
     * fills pLoc from priority vector, does not overwrite pLocs
     * as defined in the hierarchy text string
     * used in ahp-group class
     * @uses $this->priorities
     */
    public function setPlocAll()
    {
        foreach ($this->priorities as $k => $v) {
            $this->pLoc = array_merge($this->priorities[$k], $this->pLoc);
        }
        reset($this->priorities);
        return 0;
    }


    /* Check all priority vectors to sum-up to 1.
     * Warning, if not.
     * @return true if passes, false otherwise
     */
    private function checkPriorities()
    {
        $error="";
        foreach ($this->priorities as $category => $vector) {
            if (abs(array_sum($vector)-1.) > self::CALC_TOL) {
                $error .= $category . ", ";
            }
        }
        reset($this->priorities);
        if ($error == "") {
            return true;
        }
        $this->wrn[] = $this->ahpHierTxt->wrn['prioSum']
        . " <i>" . $error . "</i>";
        return false;
    }


    /* Fills priority vector from $this->pLoc
     * priority vector needs to be defined!
     * @return true if ok
     * @uses checkPriorities()
     */
    private function getPrioritiesFromPloc()
    {
        foreach ($this->pLoc as $k1 => $v1) {
            foreach ($this->priorities as $k2 => $v2) {
                if (array_key_exists($k1, $v2)) {
                    $this->priorities[$k2][$k1] = $this->pLoc[$k1];
                }
            }
        }
        reset($this->priorities);
        $check = $this->checkPriorities();
        return $check;
    }


    /* Calculates global priorities for tree leafs
     * @uses hierarchy, flatarray, pLoc and priorities
     * @return true when ok, false for outside 0 to 1
     */
    public function setPglb()
    {
        foreach ($this->flatarray as $k => $v) {
            $ka = explode(self::FLAT_DELIM, $k);
            $l = count($ka);
            $this->pGlb[$v] = $this->pLoc[$v];
            for ($i=$l-2; $i>0; $i--) {
                $this->pGlb[$v] *= $this->priorities[$ka[$i-1]][$ka[$i]];
            }
        }
        reset($this->priorities);
        // check value between 0 and 1
        $cnt = array_sum($this->pGlb);
        if (abs($cnt - 1.) > self::CALC_TOL) {
            $this->wrn[] = $this->ahpHierTxt->wrn['glbPrioS'];
            return false;
        }
        return true;
    }


    /* Merges branch $br under element $el (recursive)
     * used in get_hierarchy
     * @return void
     */
    private function mergeBranch(&$arr, $el, $br)
    {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if ($v == $el) {
                    if (count($arr)>1) {
                        unset($arr[$k]);
                        $arr = array_merge($arr, array($el => $br));
                    } else {
                        unset($arr[$k]);
                        $arr[$k] = $el;
                    }
                    return ;
                } else {
                    $this->mergeBranch($arr[$k], $el, $br);
                }
            }
        }
        return;
    }


    /* Basic check and cleanup of hierarchy text input string
     * uses multibyte functions for text
     * @return true ok
     */
    private function textCleanup(&$text)
    {
        $text = strip_tags($text);
        $text = trim($text, self::TRIM_CHAR);
        $text = str_replace(self::FLAT_DELIM, " ", $text);
        $text = preg_replace('/\s\s+/', ' ', $text);
        $text = str_replace(": ;", ":;", $text);
        $text = str_replace(",,", ",", $text);
        $text = str_replace(";;", ";", $text);

        if (mb_strlen($text)== 0) {
            $this->err[] = $this->ahpHierTxt->err['hEmpty'];
            return false;
        } elseif (mb_strpos($text, ";")=== false
        || mb_strpos($text, ":") === false
        || mb_strpos($text, ",") === false) {
            $this->err[] = $this->ahpHierTxt->err['hEmpty'];
            return false;
        } elseif (mb_strrpos($text, ";") != mb_strlen($text)-1) {
            $this->err[] = $this->ahpHierTxt->err['hSemicol'];
            return false;
        } elseif (mb_substr_count($text, ";") != mb_substr_count($text, ":")) {
            $this->err[] = $this->ahpHierTxt->err['hColSemi'] ;
            return false;
        } else {
            return $text;
        }
    }


    /* Reads $text and returns array with hierarchy
     * This is the "parse" to convert the hiierarchy text into a
     * hierarchical array.
     * Set class properties project, hierarchy, nodeCnt, leafCnt, level, ploc
     * sets pwcDoneFlg if all local priorities are set in $text ("=")
     * calls methods
     * textCleanup($text)
     * setDefPriorities()
     * setPlocAll();
     * getPrioritiesFromPloc();
     *
     * @param string $text input text
     * @uses const WLMAX maximum word lenght
     * @return array hierarchy or empty array on error
     */
    public function setHierarchy($text)
    {
        $pLoc = $this->pLoc;
        $res= array(); 	// resulting array of hierarchy
        $elem = array();
        $temp_arr = array();
        $k1 = array();
        $v1 = array();

        // --- Cleanup and check
        $text = $this->textCleanup($text);
        if ($text == false) {
            return array();
        }

        // --- get nodes and leafs
        $tk1 = strtok($text, ":;");
        $sq = 1;

        while ($tk1 !== false) {
            if (!strstr($tk1, ",")) {
                $node = mb_substr(trim($tk1, self::FLAT_DELIM . self::TRIM_CHAR), 0, WLMAX);
                if (is_numeric($node)) {
                    $this->err[] = $this->ahpHierTxt->err['hNoNum'] . $node;
                    return array();
                } elseif ($node == "") {
                    $this->err[] = $this->ahpHierTxt->err['hEmptyCat'];
                    return array();
                }
                $this->nodes[] = $node;
            } else {
                $el = trim($tk1, self::FLAT_DELIM . self::TRIM_CHAR);
                $elem = explode(",", $tk1);
                foreach ($elem as $k => $word) {
                    $w = trim($word, self::FLAT_DELIM . self::TRIM_CHAR);
                    if (is_numeric($w)) {
                        $this->err[] = $this->ahpHierTxt->err['hNoNum'] . $w;
                        return array();
                    } elseif ($w == "") {
                        $this->err[] = $this->ahpHierTxt->err['hEmptySub'];
                        return array();
                    }
                    // seperate priorities
                    $p = explode("=", $w);
                    if (count($p)==1) {
                        $elem[$k] = mb_substr($w, 0, WLMAX);
                    } else {
                        // -- Set priorities if given with "="
                        $elem[$k] = mb_substr(trim($p[0]), 0, WLMAX);
                        $pLoc = array_merge($pLoc, array(mb_substr(trim($p[0]), 0, WLMAX)
                        => mb_substr(trim($p[1]), 0, WLMAX)));
                        $this->nodPd = array_unique(array_merge($this->nodPd, array($node)));
                    }
                }
                $this->leafs = array_merge($this->leafs, $elem);
            }
            $tk1 = strtok(":;");

            if ($sq%2 && !strstr($tk1, ",")) {
                $res[] = $node;
                $sq++;
            } else {
                $res = array_merge($res, array($node => $elem));
            }
            $sq++;
        }

        // --- Check for duplication of nodes and leafs
        $temp_arr = array_diff_key($this->leafs, array_unique($this->leafs));
        if ($temp_arr) {
            $this->err[] = $this->ahpHierTxt->err['hSubDup'] . implode(", ", $temp_arr);
            return array();
        }
        $temp_arr = array_diff_key($this->nodes, array_unique($this->nodes));
        if ($temp_arr) {
            $this->err[] = $this->ahpHierTxt->err['hCatDup'] . implode(", ", $temp_arr);
            return array();
        }

        // --- merge subcategories under categories
        $temp_arr = array_intersect($this->nodes, $this->leafs);
        if (is_array($temp_arr) && !empty($temp_arr)) {
            foreach ($temp_arr as $k0 => $v0) {
                $branch = $res[$v0];
                unset($res[$v0]);
                $this->mergeBranch($res, $v0, $branch);
            }
        }
        reset($res);

        // --- Error check: more than one node
        if (is_numeric(key($res))) {
            $this->err[] = $this->ahpHierTxt->err['hHier'];
            return array();
        }

        // --- Error check: hierarchy has to starts with one main node
        $cnt0 = count($res);
        $errTxt = "";
        if ($cnt0 > 1) {
            foreach ($res as $k => $v) {
                if ($v == null) {
                    $this->err[] = $this->ahpHierTxt->err['hNoSub']
                    . "<span class='res'>" . $k . "</span>";
                    return array();
                }
                $errTxt .= "<span class='res'>".(is_numeric($k) ? "$v, " : "$k, ")."</span>";
            }
            $this->err[] = $this->ahpHierTxt->err['hMnod'] . $cnt0 . " " .
        $this->ahpHierTxt->wrd['nd'] . ": <span class='res'>" . $errTxt . "</span>";
            return array();
        }
        reset($res);
        // --- leafs contains ALL leafs, not just end nodes
        $this->leafs = array_diff($this->leafs, $this->nodes);
        $this->hText = $text;
        $this->hierarchy = $res;		// hierarchy
    $this->project = key($res);		// project name
    $this->leafCnt = $this->getTreeSpan($res);
        $this->nodeCnt = count($this->nodes);
        $this->pLoc = $pLoc;			// local priorities
        if (abs(array_sum($pLoc)- $this->nodeCnt) <= self::CALC_TOL) {
            $this->pwcDoneFlg = true;
        }
        $this->level = $this->getArrayDepth($res); // sets level
        if (!$this->checkHierarchyLimits()) {
            return array();
        }
        // --- List of leafs with already defined priorities
        $this->defLeafs = array_keys($this->pLoc);
        $this->setDefPriorities();
        $this->setPlocAll();
        // --- here we have local default priorities used to calculate gamma entropy
        $this->getPrioritiesFromPloc();
        return $res;
    }


    /* Checks hierarchy limits: number of levels, leafs, nodes
     * Generates flatArray
     * @return string true when ok
     * @Todo check criteria don't exceed limit for AHP calculation
     */
    private function checkHierarchyLimits()
    {
        // Check here for program limitations and plausible values of the hierarchy
        if ($this->level > self::LEVEL_MAX) {
            $this->err[] = $this->ahpHierTxt->err['hLmt'] . $this->ahpHierTxt->err['hLmtLv'];
        } elseif ($this->leafCnt > self::LEAF_MAX) {
            $this->err[] = $this->ahpHierTxt->err['hLmt'] . $this->ahpHierTxt->err['hLmtLf'];
        } elseif ($this->nodeCnt > self::NODE_CNT) {
            $this->err[] = $this->ahpHierTxt->err['hLmt'] . $this->ahpHierTxt->err['hLmtNd'];
        } else {
            $this->setFlatArray($this->hierarchy, $prefix = '');
            return true;
        }
        return false;
    }


    /* Sets default priority vector once hierarchy is defined
     * called after setHierarchy
     */
    private function setDefPriorities()
    {
        foreach ($this->nodes as $nd) {
            $p_temp = $this->getTreeNode($this->hierarchy, $nd);
            $val = 1./count($p_temp);
            $this->priorities[$nd] = array_fill_keys($p_temp, $val);
        }
        reset($this->priorities);
        return;
    }


    /* Get global priorities from hierarchy
     * Sets def priorities, pGlb
     * @return array float pGlb
     * called from ahp_alt
     */
    public function getPglbFromPloc()
    {
        $this->setDefPriorities();
        if (!empty($this->pLoc)) {
            $this->getPrioritiesFromPloc();
            $this->setPglb();
        }
        return $this->pGlb;
    }


    /* Get elements in hierarchy, for which pwc is already done
     * Is called by displayHierarchyTable
     * @return array string $pwcDone or false if $_SESSION['pwc'] not set
     * @uses array $_SESSION['pwc'] pairwise comp. strings of hierarchy
     */
    private function pwcDone()
    {
        if (isset($_SESSION['pwc'])) {
            foreach ($_SESSION['pwc'] as $node=>$el) {
                $pwcEl = $this->getTreeNode($this->hierarchy, $node);
                if (is_array($pwcEl)) {
                    $this->pwcDone = array_merge($this->pwcDone, $pwcEl);
                } else {
                    $this->pwcDone[] = $pwcEl;
                }
            }
            return $pwcDone;
        }
        return false;
    }


    /*
     * New version of Hierarchy Table 2017-10-07
     * HTML output of the hierarchy in table form
     *
     * @param int     $altNum Number of alternatives, 0: no alternatives shown
     * @param boolean $altButton  when true: show alternative button
     * @param boolean $ahp true: submit buttons "AHP" are displayed
     * @param boolean $pflag true: priorities in hierarchy are displayed
     * @return void
     * @uses flatarray
     * @uses $_SESSION['pwcDone'] = true, if pairwise comparison for hierarchy done
     */
    public function displayHierarchyTable($altNum, $altButton, $ahp = true, $pflag = true)
    {
        global $colors;
        $rgbBaseColor = "#50D27B";
        $rgbEndColor =  "#EBB5A2";
        if ($altNum != count($this->alt)) {
            $altnum = count($this->alt);
            trigger_error("displayHierarchyTable(): altNum = "
            . $altNum . "this->alt = "
            . count($this->alt), E_USER_NOTICE);
        }
        $cols=$this->level;	// columns
    $r_sp = array();		// rowspan for columns
    $k0 = array();			// previous node

    // --- Get array of pairwise comparisons (leafs) already done
        $pwcCnt = ($this->pwcDone ? count($this->pwcDone) : 0);

        // ---	Formats
        $floatFmt =   "<td class='res ca sm'>%02.1f</td>";
        $percBldFmt = "<td class='res ca sm'><b>%02.1f%%</b></td>";
        $csc = $this->colors->hueMap($this->pGlb, $rgbBaseColor, $rgbEndColor);

        // --- Table
        echo "\n<!-- DISPLAY HIERARCHY TABLE -->\n";
        echo "<div class='ofl'>";
        echo "<table id='hTbl' style='max-width:95%;'>";
        echo $this->ahpHierTxt->tbl['hTblCp'];
        echo "<tr>";
        // ---	Table Headers
        for ($i =0; $i< $cols; $i++) {
            echo "<th>", $this->ahpHierTxt->wrd['lvl']. " " . $i . "</th>";
        }
        if ($pflag) {
            echo "<th>" . $this->ahpHierTxt->wrd['glbP'] . "</th>";
        } // global priorities
        for ($j=0; $j< $altNum; $j++) {
            echo "<th>" . $this->alt[$j] . "</th>";
        }
        echo "</tr><tbody>";
        $row=0;
        foreach ($this->flatarray as $faKey => $val) {
            // --- flatarray has leafCnt elements
            $k1 = explode(self::FLAT_DELIM, $faKey);
            foreach ($k1 as $k=>$nod) {
                if (is_numeric($nod)) {
                    unset($k1[$k]);
                }
            }
            // --- we have $k1 as array of nodes + $val as leaf
            $colCnt = count($k1)+1;
            $c_sp = $cols-$colCnt+1;
            echo "<tr>";
            // --- Nodes
            for ($j=0; $j<$colCnt-1; $j++) {
                $r_sp[$j] = $this->getNodeSpan($this->hierarchy, $k1[$j]);
                if (!isset($k0[$j]) || $k1[$j] <> $k0[$j]) { // new node
                    $k0[$j] = $k1[$j];
                    echo "<td class='hier' rowspan='", $r_sp[$j], "'>$k1[$j] "; // nodes
                    // --- show priorities for nodes
                    if ($pflag) {
                        $inpfmt = (($this->pwcDone && in_array($k1[$j], $this->pwcDone)
                        || (in_array($k1[$j], $this->defLeafs))) ?
                        "class='resbox done'"
                        : "class='resbox'");
                        if ($j) {
                            printf(
                                "<span " . $inpfmt . ">%01.3f</span>",
                                round($this->pLoc[$k1[$j]], 3)
                            );
                        }
                    }
                    $tmp = $this->getTreeNode($this->hierarchy, $k1[$j]);
                    // --- AHP button - mark completed green, uncompleted red
                    if ($ahp) {
                        if (isset($_SESSION['nodPd']) && in_array($k1[$j], $_SESSION['nodPd'])) {
                            echo	"<input class='btng' disabled='disabled' ";
                        } elseif (isset($_SESSION['pwc'])
                        && in_array($k1[$j], array_keys($_SESSION['pwc']))
                        || in_array($tmp[0], $this->defLeafs)) {
                            echo	"<input class='btng' ";
                        } else {
                            echo	"<input class='btnr' ";
                        }
                        echo "type='submit' value='AHP' name='AHP[".$k1[$j]."]' >";
                    }
                    echo "</td>";
                } // --- if new node
            } // --- nodes
        // --- Leafs
        echo "<td class='hier' colspan='", $c_sp, "'>$val ";
            // --- show priorities for leafs
            if ($pflag) {
                $inpfmt = (
                    in_array($val, $this->defLeafs) ?
                "class='resbox done'" : "class='resbox'"
                );
                if ($j) {
                    printf(
                        "<span " . $inpfmt . ">%01.3f</span>",
                        round($this->pLoc[$val], 3)
                    );
                }
            }
            echo "</td>";
            // ---	Global priorities
            $pwctot = $this->leafCnt + $this->nodeCnt -1;
            $done = ($pwctot == $pwcCnt ? true : false);
            if (!$done && $this->pwcDoneFlg == false) {
                $this->pwcDoneFlg = false;
            } else {
                $this->pwcDoneFlg = true;
            }
            $pst = ($this->pwcDoneFlg ? "style='background-color:" . $csc[$row++] . "'" : "");
            if ($pflag) {
                printf("<td class='ac sm' $pst>%02.1f%%</td>", 100*$this->pGlb[$val]);
            }

            // ---	Alternatives
            for ($j=0; $j<$altNum; $j++) {
                echo "<td class=" . ($pflag ? 'ca' : 'hier') . ">";
                if (isset($this->prioAlt[$val][$j])) {
                    $pa = $this->prioAlt[$val][$j];
                    if ($pflag) {
                        printf("<span class='resbox done'>%01.3f</span>", round($pa, 3));
                    }
                } else {
                    $pa = 1./$altNum; // default: indifferent
                    /* Todo: we set alternative priorities to default,
                     * because no judgment available need to rework
                     * partial judgment calculation */
                    $this->prioAlt[$val][$j] = $pa;
                    if ($pflag) {
                        printf(" <span class='resbox'>%01.3f</span>", round($pa, 3));
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        // --- Submit calculation
        echo "<tr><td colspan='", $cols, "' class='res'>";
        if ($altButton) {
            if (!$done
            &&  $this->pwcDoneFlg == false) {
                echo $this->ahpHierTxt->msg['sbmPwc1'];
            } else {
                echo $this->ahpHierTxt->msg['sbmPwc2'];
                echo "&nbsp; <input type='submit' value='",
                $this->ahpHierTxt->wrd['alt'], "' name='eval' >";
            }
        }
        echo "</td>";
        // ---	sum of global priorities and alternatives
        if ($pflag) {
            printf($floatFmt, array_sum($this->pGlb));
        }
        for ($j=0; $j<$altNum; $j++) {
            $alt_sum = $this->calcPrioTotal($j, $this->pGlb);
            if ($pflag) {
                printf($percBldFmt, 100*$alt_sum);
            }
        }
        echo "</tr>";
        echo "</tbody></table></div>";
        return;
    }


    /* --- Display hierarchy informaton ---
     * displays session time and
     * @uses S_SESSION['sessions'] to display active sessions
     * @param string $warning Text for warning message
     * @return void
     */
    public function displayHierarchyInfo()
    {
        global $webHtml, $myUrl, $urlGroupInit, $urlAhpH;
        echo "<p>";
        echo "<span class='var'>" ,$this->level-1,"</span> (" . self::LEVEL_MAX . ") "
        . $this->ahpHierTxt->wrd['lvls'] . ",  ";
        echo "<span class='var'>", $this->leafCnt, "</span> (" . self::LEAF_MAX .   ") "
        . $this->ahpHierTxt->wrd['lfs'] . ", ";
        echo "<span class='var'>", $this->nodeCnt, "</span> (" . self::NODE_CNT .   ") "
        . $this->ahpHierTxt->wrd['nds'] . ", ";
        echo "<span class='var'>", mb_strlen($this->hText),"</span> (" . self::TXTMAX . ") "
        . $this->ahpHierTxt->wrd['chr'] . ". ";
        echo "</p>";
        return;
    }


    /* Assemble csv text string of hierarchy table
     * include alternatives if priorities prioAlt are calculated
     * all numbers are rounded to ROUND decimals
     * @param string $ds decimal separator, either ',' or '.'
     * @return string $textout contains csv text data
     */
    public function exportHierarchyTable($ds)
    {
        $fs = ($ds == ',' ? ';' : ',');
        $textout = array();
        $line="";

        // --- first line tells Excel the character used as field seperator
        $textout[] ="sep=" . $fs . self::NEWL;
        // --- Title
        $row = 1;
        $textout[] = $line . self::ENCL . "Project: " . $this->project
        . self::ENCL . self::NEWL;
        // ---	Table Headers
        for ($i =1; $i< $this->level; $i++) {
            $line .= self::ENCL . "Level " . $i . self::ENCL . $fs . self::ENCL
            . "p (L" . $i . ")" . self::ENCL . $fs;
        }
        $line .= self::ENCL . "Glb. Pr." . self::ENCL;
        // --- Table Header for alternatives
        if (isset($this->alt) && !empty($this->alt)) {
            foreach ($this->alt as $i => $a) {
                $line .= $fs . self::ENCL . $this->alt[$i] . self::ENCL;
            }
            foreach ($this->alt as $i => $a) {
                $line .= $fs . self::ENCL . $this->alt[$i] . self::ENCL;
            }
        }
        $textout[] .= $line . self::NEWL;
        $k0 = array_fill(0, $this->level, "");
        // ---	Hierarchy
        foreach ($this->flatarray as $key => $val) {
            $line = "";
            $k1 = explode(self::FLAT_DELIM, $key);
            $k1cnt = count($k1);
            $c_sp = $this->level - $k1cnt;
            // ---		Columns levels
            for ($i = 1; $i < $k1cnt; $i++) {
                if ($k1[$i] != $k0[$i] && !is_numeric($k1[$i]) && $k1[$i]!="") {
                    $line .= self::ENCL . $k1[$i] . self::ENCL . $fs
                    . number_format($this->pLoc[$k1[$i]], ROUND, $ds, "") . $fs;
                    $k0[$i] = $k1[$i];
                } elseif (!is_numeric($k1[$i]) && $k1[$i]!="") {
                    $line .=  $fs . $fs;
                }
            }
            // ---		leafs
            $line .= self::ENCL . $val . self::ENCL . $fs
            . number_format($this->pLoc[$val], ROUND, $ds, "") . $fs;
            for ($j=0; $j<$c_sp; $j++) {
                $line .= $fs . $fs ;
            }
            $line .=  number_format($this->pGlb[$val], ROUND, $ds, "");
            // ---		alternatives
            if (isset($this->prioAlt[$val]) && isset($this->alt)) {
                for ($i=0; $i < $this->altNum; $i++) {
                    $line  .= $fs
                . (isset($this->prioAlt[$val][$i]) ?
                    number_format($this->prioAlt[$val][$i], ROUND, $ds, "")
                    : self::ENCL . self::ENCL);
                }
                for ($i=0; $i < $this->altNum; $i++) {
                    $line  .= $fs
                .  (isset($this->prioAlt[$val][$i]) ?
                    number_format($this->prioAlt[$val][$i]
                * $this->pGlb[$val], ROUND, $ds, "")
                    : self::ENCL . self::ENCL);
                }
            }
            $textout[] .= $line . self::NEWL;
        }

        // -- Total priority of alternatives
        if (isset($this->alt)) {
            $tbc = 2 * ($this->level -1);
            $line = self::ENCL . "Total" . self::ENCL. $fs;
            for ($i = 1; $i < $tbc; $i++) {
                $line .= $fs;
            }
            for ($i = 0; $i < $this->altNum; $i++) {
                $line .= $fs;
            }
            for ($i = 0; $i < $this->altNum; $i++) {
                $sum = $this->calcPrioTotal($i, $this->pGlb);
                $line .= $fs . number_format($sum, ROUND, $ds, "");
            }
            $textout[] .= $line . self::NEWL;
        }
        return implode($textout);
    }


    /* Assemble csv text string for all matrices
     * @param string $ds decimal separator, either ',' or '.'
     * @return string $txtbuf with csv text string
     * @uses session parameters $_SESSION['pwc']
     * @uses $ahp->getTreenode(hierarchy, node),
     * @uses $ahp->getMatrixFromPwc($pwc)
     */
    public function showAllMat($ds)
    {
        global $ahp;
        $fs = ($ds == ',' ? ';' : ',');
        $txtbuf= self::NEWL;
        if (isset($_SESSION['pwc'])) {
            foreach ($_SESSION['pwc'] as $k=>$pwc) {
                if (is_array($this->prioAlt)) {
                    $ke =(array_key_exists($k, $this->prioAlt));
                }
                if ($ke) { // Alternatives
                    $nameArr = $this->alt;
                    $txtbuf .= self::ENCL . "Alternatives for " . $k
                     .  self::ENCL;
                } else { // Criteria
                    $nameArr = $this->getTreeNode($this->hierarchy, $k);
                    $txtbuf .= self::ENCL . $k . self::ENCL;
                }
                $mtrx = $ahp->getMatrixFromPwc($pwc);
                $txtbuf .= self::NEWL;
                $i = 0;
                foreach ($mtrx as $row) {
                    $txtbuf .= self::ENCL . $nameArr[$i++] . self::ENCL . $fs;
                    foreach ($row as $el) {
                        $txtbuf
                    .=  number_format($el, ROUND, $ds, "") . $fs;
                    }
                    $txtbuf .= rtrim($fs, $txtbuf) . self::NEWL;
                }
            }
        }
        return $txtbuf;
    }


    /* Inserts AHP calculated priorities into hierarchy text
     * Called from ahp-hiercalc
     * @param  string $text text string to change
     * @param  string $node name of node for new priority values
     * @param  string $txtNode new text containing priority values
     * @return string $text (modified if node was found)
     */
    public function setNewText($text, $node, $txtNode)
    {
        $text = preg_replace('/\s\s+/', ' ', $text);
        $ndlen = mb_strlen(mb_substr(trim($node), 0, WLMAX));
        $txtArr = explode(";", $text);
        foreach ($txtArr as $i=>$branch) {
            $branchArr = explode(":", $branch);
            $br = mb_substr(trim($branchArr[0]), 0, WLMAX);
            $br = trim($br);
            if (mb_strstr($br, trim($node)) !== false
        && mb_strlen($br) == $ndlen) {
                // found
                $branchArr[1] = rtrim($txtNode, ";");
                $branch = implode(":", $branchArr);
                $txtArr[$i] = $branch;
                reset($txtArr);
                $newTxt = implode(";", $txtArr);
                return $newTxt;
            }
        }
        return $text;
    }


    /* deletes all priorities given in the hierarchy text
     * given as "=0.1233"
     * @param  string $text hierarchy text
     * @return string $ntxt hierarchy text without priorities
     */
    public function clearTextPrio($text)
    {
        $tarr = explode("=", $text);
        $ntxt = "";
        foreach ($tarr as $line) {
            $ntxt .= ltrim($line, "0123456789.");
        }
        return $ntxt;
    }


    /* gets session code and name from url encoded parameters 'sc' and 'pn'
     * sets $_SESSION['name'] if name is given
     * @return string sessionCode if given, "" otherwise
     * string session code
     */
    public function getSessionCode()
    {
        if (!defined("SCLEN")) {
            define("SCLEN", 6);
        }
        $ret = "";
        // if session code is given as url parameter
        if (filter_has_var(INPUT_GET, 'sc') || filter_has_var(INPUT_GET, 'pn')) {
            $para = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
            // check for session code
            if (filter_has_var(INPUT_GET, 'sc')) {
                $ret = preg_replace('~[^\p{L}\p{N}]++~u', ' ', $para['sc']);
                $ret = substr($ret, 0, SCLEN);
                if (strlen($ret)!=SCLEN) {
                    $ret = "";
                }
            }
            // check for name
            if (filter_has_var(INPUT_GET, 'pn')) {
                $this->name = preg_replace('~[^\p{L}\p{N}]++~u', ' ', $para['pn']);
                $this->name = mb_substr($this->name, 0, WLMAX);
                $_SESSION['name'] = $this->name;
            }
        }
        return $ret;
    }
} // end class AhpHier
