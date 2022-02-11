<?php

class AhpCalcEN
{
// Titles
    public $titles = array(
    'h3ResP'	=>	"<h3>Priorities</h3>",
    'h3ResDm'	=>	"<h3>Decision Matrix</h3>"
    );

    // Errors
    public $err = array(
    'ePwc'		=>	"<span class='err'>Input error</span>",
    'adjPwc'	=>	"<span class='err'>Adjust highlighted judgments to improve consistency</span>",
    'nCrit'		=>	"<span class='err'>Warning, n should be between 1 and %g, n was set to default.</span>"
    );

    // Result text
    public $res = array(
    'npc'		=>	"Number of comparisons = <span class='res'>%g</span><br>",
    'cr'		=>	"<b>Consistency Ratio CR</b> = <span class='res'>%2.1f%%</span><br>",
    'ev'		=>	"Principal eigen value = <span class='res'>%2.3f</span><br>",
    'it'		=>	"Eigenvector solution: <span class='res'>%d</span> iterations, 
                     delta = <span class='res'>%01.1E</span>"
    );
    // Messages
    public $msg = array(
    'ok'		=>	"<span class='msg'>OK</span>",
    'sPwc'		=>	"<span class='msg'>Please start pairwise comparison</span>",
    'def'		=>	"<span class='msg'>Some names set to default.</span>"
    );
    
    // Information
    public $info= array(
    'pwcAB'		=>	"A - Importance - or B? ",
    'resP'		=>	"These are the resulting weights for the criteria based on your pairwise comparisons:",
    'resDm'		=>	"The resulting weights are based on the principal eigenvector of the decision matrix:",
    'cNbr'		=>	"<span class='hl'>Input number and names (2 - %g) </span>",
    'wlMax'		=>	"<small>max. %g character ea.</small>"
    );
    
    // Tables
    public $tbl	= array(
    'cTblTh'	=>	"<thead><tr class='header'>
                    <th colspan='3' class='ca' >%s</th>
                    <th>Equal</th>
                    <th class='ca' >How much more?</th></tr></thead>",
    'pTblTh'	=> "<th colspan='2' class='la' >Cat</th>
                    <th>Priority</th>
                    <th>Rank</th>",
    'gcTblTh'	=>	"<tr><th colspan='2' class='ca' >Name of %s</th></tr>"
    );
    
    // Menu and buttons
    public $mnu = array(
    'btnChk'	=>	"<input id='sbm1' %s type='submit' value='Calculate' name='pc_submit' />",
    'btnSbm'	=>	"<input type='submit' value='%s' name='%s' %s %s />",
    'btnDl'		=>	"dec. comma"
    );
    
}
