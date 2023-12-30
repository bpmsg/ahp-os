<?php
/*
 * Analytic Hierarchy Process i/o class
 * Contains functions for html and file i/o
 * Extends AHP base class
 *
 * $LastChangedDate: 2023-12-30 15:01:15 +0800 (Sa, 30 Dez 2023) $
 * $Rev: 217 $
 *
 * Main methods:
 * set_txtbuf(), txtDownload($fname, $txt)
 * printVector($names,$vector,$reverse)
 * print_matrix( $matrix)
 * ahpHtmlGetNewNames($n, $t, $urlAction, $nmax, $errCod)
 * getNewNames($act, $n, $title, $typ)
 *
 * @package AHP
 * @author Klaus D. Goepel
 * @copyright 2014 Klaus D. Goepel
 * @uses colorClass.php
 *
 * Copyright (C) 2022  <Klaus D. Goepel>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

class AhpCalcIo extends AhpCalc
{
    /* Class Constants */

    public const NEWL = "\n";      // for csv file export
    public const ENCL = '"';

    private $colors;

    /* AHP i-o properties */
    public function __construct($n)
    {
        parent::__construct($n);
        mb_internal_encoding('UTF-8');
        $this->colors = new AhpColors();
    }

    /* Download results as csv file
    * @param string $fname download filename
    * @param string %txt Text String to Download
    * @return int 0 for error, 1 for ok
    */
    public function txtDownload($fname, $txt, $content_type="text/csv")
    {
        $flen = mb_strlen($txt);
        if ($flen == 0) {
            echo "<span class='err'>Error: empty text file</span>";
            return 0;
        }
        ob_start();
        echo $txt;
        ob_start();
        header('Content-type: ' . $content_type);
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $fname);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $flen);
        ob_clean();
        flush();
        return 1;
    }


    /* Hierarchy text for use in ahp hierarchy module
    * generates a text string of a branch with syntax required by AHP hierarchy
    * node = AHP header, leafs = AHP criteria
    *
    * @return string $nodeTxt Format node: leaf1, leaf2 ... ;
    */
    public function getNodeTxt()
    {
        $nodeTxt = $this->header . ": ";
        foreach ($this->criteria as $index => $cname) {
            $nodeTxt .= $cname . "=" . round($this->evm_evec[$index], 8) . ", ";
        }
        $nodeTxt = rtrim($nodeTxt, ", ") . ";";
        return $nodeTxt;
    }


    /* Text Printout in csv format for download
    * assembles txt string in csv format
    *
    * @return string $txtbuf csv string
    */
    public function set_txtbuf($ds)
    {
        if (!defined("ROUND")) {
            define("ROUND", 8);
        }
        // --- first line tells Excel the character used as field seperator
        $n = $this->n;
        $fs = ($ds == ',' ? ';' : ',');
        $ts = '';
        $txtbuf ="sep=" . $fs . self::NEWL;
        // --- Title
        $txtbuf .= $this->header . $fs . self::ENCL . date("Y.m.d H:i:s")
                . self::ENCL.self::NEWL;
        $txtbuf .= $n;
        $txtbuf .= self::NEWL;

        // --- criteria
        $cbuf="";
        $mbuf="";
        $ebuf="";
        for ($i = 0; $i<$n; $i++) {
            $cbuf .= self::ENCL . $this->criteria[$i] . self::ENCL . $fs;
            for ($j=0; $j<$n; $j++) {
                $mbuf .= number_format($this->dm[$i][$j], ROUND, $ds, $ts) . $fs;
            }
            $mbuf = rtrim($mbuf, $fs);
            $ebuf .= number_format($this->evm_evec[$i], ROUND, $ds, $ts) . $fs;
            $mbuf .= self::NEWL;
        }
        $txtbuf .= rtrim($cbuf, $fs) . self::NEWL . $mbuf . rtrim($ebuf, $fs)
        . self::NEWL;
        $txtbuf .= number_format($this->evm_eval, ROUND, $ds, $ts) . $fs
        . number_format($this->cr_alo, ROUND, $ds, $ts) . self::NEWL;
        return utf8_decode($txtbuf);
    }


    /* Function to get the rank of an array
    *
    * @param array of values
    * @return array $rk reverse rank of values
    */
    public function get_rank($v)
    {
        $rk = array();
        foreach ($v as $number) {
            $smaller = 1;
            foreach ($v as $number2) {
                if ($number2 < $number) {
                    $smaller++;
                }
            }
            $rk[] = $smaller;
        }
        return $rk;
    }


    /* Function to get the reverse rank of an array
    *
    * @param array of values
    * @return array $rk reverse rank of values
    */
    public function get_rrank($v)
    {
        $rk = array();
        foreach ($v as $number) {
            $larger = 1;
            foreach ($v as $number2) {
                if ($number2 > $number) {
                    $larger++;
                }
            }
            $rk[] = $larger;
        }
        return $rk;
    }


    /* 
     * Vector printout as table: criteria, weight, ranking 
     */
    public function printVector($names, $vector, $reverse, $tol=array())
    {
        global $colors;
        $rgbBaseColor = "#50D27B";
        $rgbEndColor =  "#EBB5A2";
        $pctBfmt = "<span class='res'> %02.1f%%</span>";

        if (!is_array($vector)) {
            return 1;
        }
        if (count($tol)>1) {
            $tflg=true;
        } else {
            $tflg = false;
        }
        $n = count($vector);
        $csc = $this->colors->hueMap($vector, $rgbBaseColor, $rgbEndColor);
        // --- get ranking
        $rk = ($reverse ? $this->get_rrank($vector) : $this->get_rank($vector));
        echo "\n<!-- VECTOR PRINT -->\n";
        echo "<table id='vTbl' >";
        echo "<tr class='header'>";
        echo $this->ahpCalcTxt->tbl['pTblTh'];
        if ($tflg) {
            echo "<th>(+)</th><th>(-)</th>";
        }
        echo "</tr>\n<tbody>";
        for ($i = 0; $i < $n; $i++) {
            $rstyle = (($i+1) % 2 ? 'class="odd"' : 'class="even"');
            $style = $csc[$i];
            echo "<tr $rstyle>";
            echo "<td class='ca' >".($i+1)."</td>";
            echo "<td class='ra var'>", wordwrap($names[$i],30,'<br/>',true),"</td>";
            echo "<td class='ca' >";
            printf($pctBfmt, $vector[$i]*100);
            echo "</td><td class='ca' style='background-color:$style;' >"
                . $rk[$i] . "</td>";
            if ($tflg) {
                echo "<td class='ca' >";
                printf($pctBfmt, 100 *($tol['max'][$i]-$vector[$i]));
                echo "</td><td align='center' >";
                printf($pctBfmt, 100 *($vector[$i] - $tol['min'][$i]));
                echo "</td>";
            }
            echo "</tr>\n";
        }
        echo "</tbody></table>";
        return 0;
    }


    /* 
     * Printout matrix as table
     */
    public function print_matrix($matrix)
    {
        $matrixelfmt = "<td class='sm res'>%01.2f</td>";
        $matrixdgfmt = "<td class='sm ca' style='background-color:#E8E8E8;'>%01.0f</td>";
        $matrixidfmt = "<td class='sm ca var'>%2.0f</td>";
        if (is_array($matrix)) {
            $n_row = count($matrix);
            $n_col = count($matrix[0]);
            echo "\n<!-- MATRIX PRINT -->\n";
            echo "<table>";
            echo "<tr><td></td>";
            for ($j = 0; $j < $n_col; $j++) {
                printf($matrixidfmt, ($j+1));
            }
            echo "\n</tr>";
            for ($row = 0; $row < $n_row; $row++) {
                echo "<tr>";
                printf($matrixidfmt, ($row+1));
                for ($col = 0; $col < $n_col; $col++) {
                    ($row == $col ? printf($matrixdgfmt, $matrix[$row][$col])
                    : printf($matrixelfmt, $matrix[$row][$col]));
                    echo "\n";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        return 0;
    }


    /* Prints evm result information in 2 columns
    * number of comarisons, CR, and EV and number of iterarions
    */
    public function evm_info()
    {
        echo "\n<!-- EVM INFO DIV -->\n";
        echo "<div style='width:40%;height:auto;float:left;padding:20px;'>";
        printf($this->ahpCalcTxt->res['npc'], $this->npc);
        printf($this->ahpCalcTxt->res['cr'], $this->cr_alo*100);
        echo "</div>";
        echo "\n<div style='align:left;padding:20px;display:table-cell;vertical-align:top;'>";
        printf($this->ahpCalcTxt->res['ev'], $this->evm_eval);
        printf($this->ahpCalcTxt->res['it'], $this->evm_it, $this->evm_dt);
        echo "</div>";
        return;
    }


    /* HTML output of priority vector and decision matrix in 2 columns
    *  @uses evm_info()
    */
    public function showResult()
    {
        // --- LEFT COLUMN
        echo "\n<div style='width:40%;height:auto;float:left;padding:20px;'>";
        echo $this->ahpCalcTxt->titles['h3ResP'];
        echo $this->ahpCalcTxt->info['resP'];
        $this->printVector($this->criteria, $this->evm_evec, true, $this->evm_tol);
        echo "</div>";
        // --- RIGHT COLUMN
        echo "\n<div style='align:left;padding:20px;'>";
        echo $this->ahpCalcTxt->titles['h3ResDm'];
        echo $this->ahpCalcTxt->info['resDm'];
        $this->print_matrix($this->dm);
        echo "</div>";
        echo "<div style='clear:both'>";
        $this->evm_info();
        echo "</div>";
    }


    /* html display to get new names
    * used in ahp calculator and ahp os
    *
    * @param int $n             Number of names/criteria/alternatives
    * @param string $t          Project Title
    * @param string $urlAction  Form action URL
    * @param int $nmax          Max number of names
    * @param int $errCod        0 ok, 1 exceed nmax, 2 some default
    * @return void
    */
    public function ahpHtmlGetNewNames($n, $t, $urlAction, $nmax, $errCod)
    {
        echo "\n<!-- Get new names -->\n";
        echo "<form method='GET' action='$urlAction'>";
        printf($this->ahpCalcTxt->info['cNbr'], $nmax);
        echo "\n<input type='hidden' value='$t' name='t' >";
        echo "\n<input type='text' maxlength='3' size='3' value='$n' name='n'>";
        echo "\n<input type='submit' value='Go' name='new' >&nbsp;&nbsp;";
        switch ($errCod) {
            case 0:
                echo "OK"; break;
            case 1:
                printf($this->ahpCalcTxt->err['nCrit'], $nmax);
                break;
            case 2:
                echo $this->ahpCalcTxt->msg['def'];
                break;
        }
        echo "\n</form>";
        return;
    }


    /* Get form input for names of criteria/alternatives
    *
    * @param string $act form action address
    * @param int $n number of names (criteria/alternatives)
    * @param string $title title (from url get t= parameter)
    * @param string $typ type: "c(riteria)" or "a(lternatives)",
    *               for "alternatives" title field is disabled
    *               if $type == ""
    * @return void
    */
    public function getNewNames($act, $n, $title, $typ)
    {
        if (!defined("WLMAX")) {
            define("WLMAX", 60);
        }
        $c = array_fill(0, $n, "");
        if ($typ == "") {
            $typ = 'criteria';
        }
        if (mb_strpos($typ, 'c') === false) {
            $titleField = 'disabled';
        } else {
            $titleField = '';
        }
        echo "<form method='GET' action='$act' ><input type='hidden' name ='n' value='$n' >
            <table>";
        echo "<tr><td></td>";
        echo "<td>
            <input type='text' maxlength='" . WLMAX . "' size='" . WLMAX
            . "' name ='t' value='$title' $titleField ></td></tr>";
        printf($this->ahpCalcTxt->tbl['gcTblTh'], $typ);
        for ($i = 0; $i < $n; $i++) {
            echo "<tr>";
            echo "<td>" . ($i+1) . "</td>";
            echo "<td><input type='text' maxlength='" . WLMAX . "'  
                size='". WLMAX . "' name ='c[$i]' value='" . $c[$i] . "' ></td>";
            echo "</tr>";
        }
        echo "<tr><td colspan='2' class='ca' >";
        printf($this->ahpCalcTxt->info['wlMax'], WLMAX);
        echo "</td></tr>";
        echo "<tr><td colspan='2' style='text-align:center;' >
            <input type='submit' value='OK'></td></tr>";
        echo "</table></form>";
        return;
    }
}
