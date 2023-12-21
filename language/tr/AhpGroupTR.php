<?php

class AhpGroupTR
{
    public $wrn = array(
        "noPart"		=>	"Projenin katılımcısı yok",
        "noPwc"			=>	"<br>Düğüm(ler) <span class='res'>%s</span> çiftli karşılaştırma verisi yok",
        "fUncEst"		=>	"<br>Düğüm <span class='res'>%s</span> sadece %g belirsizlik tahmini",
        "nUncEst1"		=>	"<br>Düğüm <span class='res'>%s</span> belirsizlik tahmini yok",
        "nUncEst2"		=>	"<br>Düğüm(ler) <span class='res'>%s</span>, belirsizlik tahmini yok"
    );
    public $err  = array(
        "noAlt"			=>	"Alternatif yok",
        "invSc"			=>	"Geçersiz Oturum Kodu",
        "dbE"			=>	"Katılımcı <span class='var'>%s</span> tarafından gelen çiftli karşılaştırma, hiyerarşi düğümü <span class='var'>%s</span> ile uyuşmuyor"
    );
    public $info = array(
        "cont"			=>	"<p><br><small>devam</small></p>"
    );
    public $tbl	= array(
        "grTblTh"		=>	"\n<thead><tr class='header'><th>Katılımcı</th>",
        "grTblTd1"		=>	"<td><strong>Grup sonucu</strong></td>"
    );
}
