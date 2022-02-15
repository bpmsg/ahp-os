<?php

class AhpSessionAdminEN
{
    public $titles = array(
    'pageTitle'         => "AHP projects - AHP-OS",
    'h1title'           => "<h1>AHP Project Administration</h1>",
    'h2subTitle'        => "<h2>AHP-OS - Rational decision making made easy</h2>",
    'h2ahpProjSummary'  => "<h2>Project Summary</h2>" ,
    'h2ahpSessionMenu'  => "<h2>AHP Session Menu</h2>",
    'h2ahpProjectMenu'  => "<h2>AHP Project Menu</h2>",
    'h2myProjects'      => "<h2>My AHP Projects</h2>",
    'h3groupInpLnk'     => "<h3>Group Input Link</h3>" ,
    'h3projStruc'       => "<h3>Project Structure</h3>",
    'h4hierDefTxt'      => "<h4>Hierarchy Definition Text</h4>",
);
    public $msg = array(
    'sDel'          => "<span class='msg'>Session <span class='var'>%s</span> was successfully deleted</span>",
    'sDelp'         => "Participant(s) <span class='res'>%s</span> successfully deleted ",
    'pwcCompl'      => "Pairwise comparisons under the name <span class='var'>%s</span> completed.",
    'pClsd'         => "<p class='msg'>The project is closed. Click on <i>Toggle Proj. Status</i> to reopen.</p>",
    'pStat1'        => "Project status changed to ",
    'pStatO'        => "open.",
    'pStatC'        => "closed.",
    'selPart'       => "<span class='msg'>Selected participant(s): </span><span class='var'>%s</span>",
    'hInfo1'        => "<span class='msg'>The decision hierarchy has defined priorities</span>",
    'hInfo2'        => "<span class='msg'>. Project can be used to define alternatives. <br>Click on <i>Use Hierarchy</i></span>",
    'hInfo3'        => "<span class='msg'> and project has <span class='var'>%g</span> defined alternatives.</span>",
    'usrStat1'      => "<p class='msg'><small>AHP-OS has <span class='res'>%s</span> registered users, ",
    'usrStat2'      => "<span class='res'>%g</span> active users in the last %g hours.</small></p>",
    'usrStat3'      => "<p class='msg'>%s, you have <span class='res'>%g</span> projects. ",
    'usrStat4'      => "Your program use index is <span class=res>%g%%</span>. ",
    'usrDon1'       => "Please consider a <a href='ahp-news.php'>donation</a>",
    'usrDon2'       => "Thanks for your donation"
);
    public $err = array(
    'invSess1'      => "Invalid Session Code.",
    'invSess2'      => "Invalid Session Code in url.",
    'noAuth'        => "As you are not the project author, you are not allowed to delete participants.",
    'pClosed'       => "Project is closed. No pairwise comparison input allowed.",
    'noDel'         => "could not be deleted.",
    'sLmt'          => "<p><span class='err'>Session limit reached.</span> Please delete some old sessions first. </p>"
);
    public $info = array(
    'sc'            => "The session code is <span class='var'>%s</span>.",
    'scLnk1'        => "Provide this session code or the following link to your participants: </span><br>",
    'scLnk2'        => "<textarea rows='1' cols='78'>https:%s?sc=%s</textarea><br>",
    'scLnk3'        => "Go to link above: <a href='https:%s?sc=%s' >Group Input</a><br>",
    'pOpen1'        => "Click on the session link in the table below to open a project. ",
    'pOpen2'        => "<br>Create a <a href='%s'>new hierarchy</a>.",
    'logout'        => "<div class='entry-content'>
                       On the AHP project administration page you can manage your AHP projects: 
                       create new hierarchies, open, edit or delete and view existing projects. 
                       <p class='msg'>You need to be a registered user and login to handle projects.</p>
                       <p><a href='%s'>back</a></p></div>"
);
    public $mnu = array(
    'lgd1'          => "Session Administration Menu",
    'lbl1'          => "Project Session Code: ",
    'btnps1'        => "Open Project",
    'btnps2'        => "New Project",
    'btnps3'        => "Done",
    'btnps4'        => "Import Project",
    'lgd2'          => "Project Administration Menu",
    'btnpa1'        => "View Result",
    'btnpa2'        => "PWC Input",
    'btnpa3'        => "Use Hierarchy",
    'btnpa4'        => "Rename",
    'btnpa5'        => "Edit",
    'btnpa6'        => "Del Sel. Part.(s)",
    'btnpa7'        => "Delete Project",
    'btnpa8'        => "Toggle Project Status",
    'btnpa9'        => "Done",
    'btnpa10'       => "Export Project"
);
}
