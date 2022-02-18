<?php

class AhpDE
{
    public $titles = array(
    'pageTitle'     =>  "AHP Online System - AHP-OS",
    'h1title'       =>  "<h1>AHP Online System - AHP-OS</h1>",
    'h2subTitle'    =>  "<h2>Rationale Entscheidungsfindung mit dem Analytischen Hierarchie Prozess</h2>",
    'h2contact'     =>  "<h2>Kontakt und Feedback</h2>"
);

    public $msg = array(
    'tu'            =>  "Danke!",
    'cont'          =>  "Weiter"
);

    public $info = array(
    'contact'       =>  "<p>Gerne hinterlasse einen <a href='%s'>Kommentar</a></p>",
    'intro11'       =>  "<div class='entry-content'><p style='text-align:justify;'>
                        Dieses freie <b>web-basierte AHP Programm</b> ist ein Hilfsmittel
                        zur rationalen Entscheidungsfindung. Das Programm kann hilfreich 
                        sein für einfache Entscheidungsprobleme in der täglichen Arbeit, 
                        es unterstützt aber auch komplexe Entscheidungsprozesse. Hier 
                        ein <a href='https://bpmsg.com/participate-in-an-ahp-group-session-ahp-practical-example/'>
                        praktisches Beispiel</a> (in Englisch). Ein <a href='docs/BPMSG-AHP-OS-QuickReference.pdf' target='_blank'>
                        Quick-Reference-Guide</a> und das <a href='docs/BPMSG-AHP-OS.pdf' target='_blank'>
                        AHP-OS Handbuch</a> stehen zur Verfügung zum Download. Zur Nutzung der 
                        vollen Funktionalität musst du dich als Nutzer <a href='includes/login/do/do-register.php'>registrieren</a> 
                        und einloggen, falls du noch keinen Account hast. Das Beste: es ist 
                        alles kostenfrei! </p></div>",
    'intro12'       =>  "<ol style='line-height:150%;'>
                        <li><span style='cursor:help;' 
                        title='Manage komplette AHP Projekte und Gruppen Entscheidungen.' >
                            <a href='ahp-session-admin.php'>Meine AHP Projekte</a></span></li>
                        <li><span style='cursor:help;' 
                        title='Der AHP Rechner berechnet Prioritaten für eine Gruppe von Kriterien über Paarvergleiche.' >
                            <a href='ahp-calc.php'>AHP Prioritätsrechner</a></span></li>
                        <li><span style='cursor:help;' 
                        title='Manage komplette Entscheidungsprobleme mit AHP. Definiere eine Hierarchie von Kriterien 
                        und evaluiere mögliche Alternativen.' >
                            <a href='ahp-hierarchy.php'>AHP Hierarchien</a></span></li>
                        <li><span style='cursor:help;' 
                        title='Partizipiere in AHP Gruppenentscheidungen in der Evaluierung von kriterien oder ALternativen als
                         Mitglied in der Gruppe' >
                        <a href='ahp-hiergini.php'>AHP Gruppen Session</a></span></li>
                        </ol>",
    'intro13'       =>  "<p style='text-align:justify;'>
                        Für die Programme 2. und 3. können die Ergebnisdaten als csv Files (comma separated values) für
                        eine weitere Verarbeitung in Excel exportiert werden.",
    'intro14'       =>  "<p style='text-align:justify;'>
                        <b>Nutzungsbedingungen</b> siehe 
                        <a href='https://bpmsg.com/about/user-agreement-and-privacy-policy/' >
                        User agreement and Privacy policy</a>.</p>",
    'intro15'    =>    "<p style='text-align:justify;'>
                        Wenn dir das Programm gefällt, <span class='err'>bitte 
                        erwäge eine <a href='ahp-news.php'>Spende</a>, um die Webseite zu pflegen und am Leben zu halten</span>.</p>",
    'intro16'    =>    "<p><b>In deiner Arbeit bitte zitiere:</b><br>
                        <code>
                        Goepel, K.D. (2018). Implementation of an Online Software Tool for the Analytic Hierarchy 
                        Process (AHP-OS). <i>International Journal of the Analytic Hierarchy Process</i>, Vol. 10 Issue 3 2018, pp 469-487,
                        <br><a href='https://doi.org/10.13033/ijahp.v10i3.590'>https://doi.org/10.13033/ijahp.v10i3.590</a>
                        </code></p>"
);
}
