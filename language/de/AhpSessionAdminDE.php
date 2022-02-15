<?php

class AhpSessionAdminDE
{
    public $titles = array(
    'pageTitle'     =>    "AHP Projekte - AHP-OS",
    'h1title'       =>    "<h1>AHP Projekt Administration</h1>",
    'h2subTitle'    =>    "<h2>AHP-OS - Rationale Entscheidungsfindung einfach gemacht</h2>",
    'h2ahpProjSummary' => "<h2>Projekt Zusammenfassung</h2>" ,
    'h2ahpSessionMenu' => "<h2>AHP Session Menü</h2>",
    'h2ahpProjectMenu' => "<h2>AHP Projekt Menü</h2>",
    'h2myProjects'  =>    "<h2>Meine AHP Projekte</h2>",
    'h3groupInpLnk' =>    "<h3>Gruppen Eingabe Link</h3>" ,
    'h3projStruc'   =>    "<h3>Projekt Struktur</h3>",
    'h4hierDefTxt'  =>    "<h4>Hierarchie Definition</h4>",
);
    public $msg = array(
    'sDel'          => "<span class='msg'>Project mit Kennung <span class='var'>%s</span> wurde erfolgreich gelöscht</span>",
    'sDelp'         => "Teilnehmer <span class='var'>%s</span> erfolgreich gelöscht ",
    'pwcCompl'      => "Paarweiser Vergleich unter dem Namen <span class='var'>%s</span> ist bereits komplett.",
    'pClsd'         => "<p class='msg'>Das Projekt ist abgeschlossen. Klick auf <i>Toggle Proj. Status</i>, um es wieder zu öffnen.</p>",
    'pStat1'        => "Projekt Status geändert auf ",
    'pStatO'        => "offen.",
    'pStatC'        => "abgeschlossen.",
    'selPart'       => "<span class='msg'>Ausgewähle(r) Teilnehmer: </span><span class='var'>%s</span>",
    'hInfo1'        => "<span class='msg'>Die Entscheidungshierarchie hat definierte Gewichte</span>",
    'hInfo2'        => "<span class='msg'>. Das Projekt kann zur Definition von Alternativen benutzt werden. 
                        <br>Klick auf <i>Use Hierarchy</i></span>",
    'hInfo3'        => "<span class='msg'> und das Projekt hat <span class='var'>%g</span> definierte Alternativen.</span>",
    'usrStat1'      => "<p class='msg'><small>AHP-OS hat <span class='res'>%s</span> registrierte Benutzer, ",
    'usrStat2'      => "<span class='res'>%g</span> aktive Benutzer in den letzten %g Stunden.</small></p>\n",
    'usrStat3'      => "<p class='msg'>%s, du hast <span class='res'>%g</span> gespeicherte Projekte. \n",
    'usrStat4'      => "Dein Aktivitätsindex is <span class=res>%g%%</span>. \n",
    'usrDon1'       => "<br>Eine <a href='ahp-news.php'>Spende</a> hilft, diese Webseite aktiv zu halten",
    'usrDon2'       => "Vielen Dank für deine Spende."
);
    public $err = array(
    'invSess1'      => "Ungültige Kennung.",
    'invSess2'      => "Ungültige Kennung in der URL.",
    'noAuth'        => "Da du nicht der Projektautor bist, darfst du keine Teilnehmer löschen.",
    'pClosed'       => "Projekt ist absgeschlossen. Keine paarweise Vergleichs-Eingabe erlaubt.",
    'noDel'         => "konnte nicht gelöscht werden.",
    'sLmt'          => "<p><span class='err'>Max. Anzahl der Projekte erreicht.</span> Bittel lösche einige alte Projekte zuerst. </p>"
);
    public $info = array(
    'sc'            => "Projektkennung ist <span class='var'>%s</span>. ",
    'scLnk1'        => "<span>Gebe diese Projektkennung oder den folgenden Link an deine Teilnehmer: </span><br>",
    'scLnk2'        => "<textarea rows='1' cols='78'>https:%s?sc=%s</textarea><br>",
    'scLnk3'        => "Gehe zum Gruppen-Eingabelink: 
                                    <a href='https:%s?sc=%s' >Gruppen Eingabe</a><br>",
    'pOpen1'        => "Klicke auf die Projektkennung in der Tabelle, um die Projektzusammenfassung anzuzeigen.",
    'pOpen2'        => "<br>Definiere eine <a href='%s'>neue Hierarchie</a>.",
    'logout'        => "<div class='entry-content'>
                        Auf der Projekt Administration Seite kannst du deine 
                        AHP Projekte verwalten: neue Hierarchien definieren und Projekte öffnen, 
                        editieren oder löschen und Ergebnisse anzeigen. 
                        <p class='msg'>Dazu ist Registrierung und Login erforderlich.</p>
                        <p><a href='%s'>back</a></p></div>"
);
    public $mnu = array(
    'lgd1'          => "Session Administration Menü",
    'lbl1'          => "Projektkennung: ",
    'btnps1'        => "Projekt öffnen",
    'btnps2'        => "Neues Projekt",
    'btnps3'        => "Fertig",
    'btnps4'        => "Import Projekt",
    'lgd2'          => "Projekt Administration Menü",
    'btnpa1'        => "Ergebnis",
    'btnpa2'        => "PWC Eingabe",
    'btnpa3'        => "Use Hierarchie",
    'btnpa4'        => "Umbenennen",
    'btnpa5'        => "Editieren",
    'btnpa6'        => "Lösche sel. Teiln.",
    'btnpa7'        => "Lösche Projekt",
    'btnpa8'        => "Toggle Proj. Status",
    'btnpa9'        => "Fertig",
    'btnpa10'       => "Export Projekt"
);
}
