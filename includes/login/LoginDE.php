<?php

/* used for both login and registration */

class LoginDE
{
    public $titles = array(
    "h1edit"	=>	"Anmeldedaten bearbeiten",
    "h2info"	=>	"Konto Information",
    "h2act"		=>	"Letzte Kontoaktivitäten",
    "h2lgin"	=>	"Bitte einloggen",
    "h1reg"		=>	"Nutzer Registrierung",
    "h2reg"		=>	"Eingabe",
    "h1pwR"		=>	"Passwort zurücksetzen"
);

    public $err  = array(
    "aNact"		=>	"Dein Konto ist noch nicht aktiviert. Bitte klicke auf den Bestätigungslink in der Email.",
    "dbCon"		=>	"Datenbank Verbindungsproblem",
    "emlE"		=>	"Email Adress muß anzgegeben werden",
    "emlL"		=>	"Email Adresse darf nicht länger als 64 Yeichen sein.",
    "emlD"		=>	"Diese Email-Adresse ist die gleiche wie die aktuelle. Bitte wähle eine andere.",
    "emlI"		=>	"Deine Email Adress hat kein gültiges Email Format.",
    "emlR"		=>	"Diese Email-Adresse ist bereits registriert. Bitte nutze die Funktion \"Passwort vergessen\", wenn du es nicht erinnerst.",
    "emlNc"		=>	"Sorry, your email changing failed.",
    "emlVns"	=>	"Leider konnten wir keine Bestätigungs Email senden. Dein Konto wurde nicht angelegt.",
    "emlNs"		=>	"Bestätigungs Email nicht gesendet! Fehler: ",
    "pwW"			=>	"Login fehlgeschlagen. Bitte nochmal versuchen.",
    "pwW3"		=>	"Du hast das Passwort 3 oder mehrmals falsch eingegeben. Bitte warte 30 Sekunden bevor du es nochmal versuchst.",
    "pwS"			=>	"Passwort hat mindestens 6 Zeichen",
    "pwE"			=>	"Passwort Feld war leer",
    "pwNi"		=>	"Passwort und widerholtes Passwort sind unterschiedlich. ",
    "pwCf"		=>	"Passwortänderung fehlgeschlagen.",
    "pwRf"		=>	"Passwort Reset Email konnte nicht gesendet werden! Fehler: ",
    "pwOw"		=>	"Falsches (altes) Passwort. ",
    "unNe"		=>	"Diese(n) Email/Benutzer gibt es nicht.",
    "unTk"		=>	"Dieser Name ist bereits in Gebrauch. Bitte wähle einen anderen.",
    "unIv"		=>	"Benutzername passt nicht zum Namensschema: a-Z und Zahlen sind erlaubt, 2 bis 64 Zeichen",
    "unDb"		=>	"Dieser Name ist der gleiche wie der alte. Bitte wähle einen neuen Namen",
    "unF"			=>	"Leider hat die Umbenennung in den gewünschten Nutzernamen nicht funktioniert",
    "unE"			=>	"Feld Nutzername leer",
    "unL"			=>	"Nutyername darf nicht kürzer als 2 oder länger als 64 Yeichen sein",
    "lnkExp"	=>	"Reset Link abgelaufen. Bitte innerhalb einer Stunde benutzen.",
    "lnkE"		=>	"Leerer Parameter Link.",
    "regF"		=>	"Leider hat die Registrierung nicht funktioniert. Bitte nocheinmal versuchen.",
    "wVc"			=>	"Diese Id/Bestätigungs Kombination existiert nicht ...",
    "iCk"			=>	"Ungültiges Cookie",
    "wCp"			=>	"Captcha war falsch!"
);

    public $msg = array(
    "lgOut"	=>	"Du bist jetzt ausgeloggt.",
    "emlN"	=>	"Bitte eine gültige Email Adresse eingeben!",
    "emlCok"	=>	"Deine Email-Adresse wurde erfolgreich geändert. Die neue Email-Adresse ist %s",
    "unCok"	=>	"Dein Nutzername wurde erfolgreich geändert. Der neue Nutzername ist ",
    "pwCok"	=>	"Passwort wurde erfolgreich geändert",
    "pwRms"	=>	"Passwort Reset Mail erfolgreich abgesendet!",
    "aOk"		=>	"Dein Konto wurde erfolgreich erstellt. Bitte einloggen, um den Prozess abzuschließen!",
    "regOk"	=>	"Dein Konto wurde erfolgreich angelegt und wir haben eine Email zur Aktivierung geschickt (Bitte check auch den Spam Folder).
						  Klicke auf den VERIFICATION LINK in dieser Email, um das Konto zu aktivieren.",
    "verOk"	=>	"Dein Konto wurde erfolgreich aktiviert. ",
    "deact"	=>	"Dein Konto (%s) wurde erfolgreich deaktiviert",
    "deactm" => " und eine Reaktivierungs Email wurde geschickt."
);

    public $info = array(
    "reg"		=>	"Bitte fülle das folgende Formular aus, um dich zu registrieren und gebe eine gültige E-Mail-Adresse an.",
    "delA"	=>	"<p><span class='err'>Konto und alle Daten löschen.</span> 
							Dein Konto wird sofort inaktiviert, und du erhältst eine Email mit einem Reaktivierungs Link. 
							Wenn du das Konto nicht reaktivierst, wird dein Konto mit allen Daten nach 2 Tagen komplett gelöscht.</p>",
    "conf"	=>	"Mit meiner Registrierung bin ich damit einverstanden - nach jeweils drei Monaten Inaktivität - 
							E-Mails zur Reaktivierung meines Kontos zu erhalten. Wenn ich das Konto dann 
							nicht innerhalb von 48 Stunden reaktiviere, werden mein Account mit allen Daten automatisch gelöscht.",
    "pwRes"	=>	"Bitte deine E-Mail Adresse eingeben. Du erhältst dann eine E-Mail mit weiteren Anweisungen:<br>",
    "nlgin" =>  "Du benötigst ein Konto, um auf diese Website zuzugreifen.",
    "nReg"	=>	"Bitte wende dich an den <a href='mailto:webmaster@bpmsg.com'>Webmaster</a>, um ein Konto zu eröffnen."
);

    public $wrd = array(
    "crC"		=>	"Änderung Nutzername, Email und Passwort hier:",
    "emlC"	=>	"Ändere Email Adresse",
    "emlN"	=>	"Neue Email Adresse:",
    "pwC"		=>	"Ändere Passwort",
    "pwO"		=>	"Altes Passwort:",
    "pwN"		=>	"Neues Passwort:",
    "pwNr"	=>	"Widerhole neues Passwort:",
    "unC"		=>	"Ändere Nutzername",
    "unN"		=>	"Neuer Nutzername (2-30 Zeichen, azAZ09):",
    "delA"	=>	"Lösche mein Konto",
    "cont"	=>	"Weiter",
    "done"	=>	"Fertig",
    "eml"		=>	"Email Adresse (Du erhälts eine Bestätigungsmail mit einem Aktivierungslink)",
    "pw"		=>	"Passwort (min. 6 Zeichen!)",
    "pwr"		=>	"Passwort widerholen",
    "un"		=>	"Nutzername (nur Buchstaben und Zahlen, 2 to 30 Zeichen)",
    "pwRes"	=>	"Passwort zurücksetzen",
    "pwSbm"	=>	"Neues Passwort setzen",
    "hlPw"	=>	"Passwort",
    "hlUn"	=>	"Name oder Email",
    "hlAc"	=>	"Konto",
    "hlLo"	=>	"Ausloggen",
    "hlFg"	=>	"Vergessen?",
    "hlReg"	=>	"Registrieren",
    "hlWlc"	=>	"Willkommen "
);

    public $tbl = array(
    "tbEdTd1"	=>	"Nutzer ID",
    "tbEdTd2"	=>	"Nutzer Name",
    "tbEdTd3"	=>	"Email",
    "tbEdTd4"	=>	"Registriert seit",
    "tbEdTd5"	=>	"Remember Cookie"

);
}
