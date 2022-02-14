<?php
/* Text for AhpDb.php */

class AhpDbEN
{
    public $titles = array(
    'h3pDat'	=>	"<h3>Project Data</h3>",
    'h3pPart'	=>	"<h3>Project Participants</h3>\n",
    'h3pAlt'	=>	"<h3>Project Alternatives</h3>"
    );

    public $err = array(
    'dbType'	=>	"No such SQL Database type: ",
    'scInv'		=>	"Invalid Session Code ",
    'scInUse'	=>	"Session code in use ",
    'dbWrite'	=>	"Data could not be written to the database. Please try again later.",
    'dbWriteA'	=>	"Database error, could not store alternatives ",
    'dbUpd'		=>	"Data could not be updated. Please try again later.",
    'dbSubmit'	=>	"Data already submitted ",
    'noSess'	=>	"No stored sessions ",
    'dbReadSc'	=>	"Database error getting data for ",
    'pClosed'	=>	"Project is closed. No pairwise comparison input allowed.",
    'pNoMod'	=>	"Project has participants, hierarchy cannot be modified."
    );

    public $msg = array(
    'noSess' 		=> "No stored sessions"
    );

    public $tbl = array(
    'scTblTh'	=> "<thead><tr>
					<th>No</th>
					<th>Session</th>
					<th>Project</th>
					<th>Type<sup>1</sup></th>
					<th>Status</th>
					<th>Description</th>
					<th>Part.<sup>2</sup></th>
					<th>created</th></tr></thead>",
    'scTblFoot'	=> 	"<tfoot><tr><td colspan='8'>
					<sup>1</sup> H: Priority evaluation hierarchy, A: Alternative evaluation, 
					<sup>2</sup> Number of participants</td>
					</tr></tfoot>",
    'pdTblTh'	=>	"<thead><tr>
					<th>Field</th>
					<th>Content</th></tr></thead>\n",
    'pdTblR1'	=>	"<tr><td>Session Code</td><td class='res'>%s</td></tr>\n",
    'pdTblR2'	=>	"<tr><td>Project Name</td><td class='res'>%s</td></tr>\n",
    'pdTblR3'	=>	"<tr><td>Description </td><td class='res'>%s</td></tr>\n",
    'pdTblR4'	=>	"<tr><td>Author</td><td class='res'>%s</td></tr>\n",
    'pdTblR5'	=>	"<tr><td>Date</td><td class='res'>%s</td></tr>\n",
    'pdTblR6'	=>	"<tr><td>Status</td><td class='res'>%s</td></tr>\n",
    'pdTblR7'	=>	"<tr><td>Type</td><td class='res'>%s</td></tr>\n",
    'paTblTh'	=>	"<thead><tr>
					<th>No</th>
					<th>Alternatives</th>
					</tr></thead>\n",
    'ppTblTh'	=>	"<thead><tr>
					<th>No</th>
					<th>Sel</th>
					<th>Name</th>
					<th>Date</th>
					</tr></thead>\n",
    'ppTblLr1'	=>	"<tr><td colspan='4'><input id='sbm0' type='submit' name='pselect' value='Refresh Selection'>&nbsp;<small>
					<input class='onclk0' type='checkbox' name='ptick' value='0' ",
    'ppTblLr2'	=>	">&nbsp;check all&nbsp;<input class='onclk0' type='checkbox' name='ntick' value='0' ",
    'ppTblLr3'	=>	">&nbsp;uncheck all</small></td></tr>",
    'ppTblFoot'	=>	"<tfoot><tr><td colspan='4'>
					<small>If none selected, all will be included.</small>
					</td></tr></tfoot>"
    );

    public $info = array(
    'shPart'		=> "<p><span class='var'>%g</span> participants. 
					<button class='toggle'>Show/Hide</button> all.</p>"
    );
}
