<?php

class AhpGroupResTR
{
    // for ahp-group.php AND ahp-g-input.php
    /* Titles and headings */
    public $titles = array(
        "pageTitle1"    =>    "AHP Grup Sonuçları - AHP-OS",
        "h1title1"      =>    "<h1>AHP Grup Sonuçları</h1>",
        "h2subTitle1"   =>    "<h2>Proje Sonuç Verileri</h2>",

        "pageTitle2"    =>    "AHP Proje Giriş Verileri - AHP-OS",
        "h1title2"      =>    "<h1>AHP Grup Sonuçları</h1>",
        "h2subTitle2"   =>    "<h2>Proje Giriş Verileri</h2>",

        "h2hier"        =>    "<h2>Toplam Ağırlıklar ile Hiyerarşi</h2>",
        "h2consP"       =>    "<h2>Toplanmış Global Öncelikler</h2>",
        "h2consA"       =>    "<h2>Alternatiflerin Toplanmış Ağırlıkları</h2>",
        "h2sens"        =>    "<h2>Duyarlılık Analizi</h2>",
        "h3wUncrt"      =>    "<h3>Ağırlık Belirsizlikleri</h3>",
        "h2nodes"       =>    "\n<h2>Düğümlere Göre Ayrıntılar</h2>",
        "h4wCons"       =>    "<h4>Toplanmış Öncelikler</h4>",
        "h4mCons"       =>    "<h4>Toplanmış Karar Matrisi</h4>",
        "h4part"        =>    "<h4>Grup Sonucu ve Bireysel Katılımcıların Öncelikleri</h4>",
        "h2pGlob"       =>    "<h2>Global Öncelikler</h2>",
        "h3rob"         =>    "<h3>Dayanıklılık</h3>",
        "h2alt"         =>    "<h2>Katılımcılara Göre Alternatifler</h2>",
        "h2crit"        =>    "<h2>Kriterlere Göre Düğümler</h2>",
        "h4group"       =>    "<h4>Grup Sonucu ve Bireysel Katılımcıların Öncelikleri</h4>",
        "h2grMenu"      =>    "<h2>Grup Sonuç Menüsü</h2>",

        "h2dm"          =>    "<h2>Çiftli Karşılaştırma Karar Matrisleri</h2>",
        "h4dm"          =>    "<h4>Karar Matrisi</h4>",
        "h4crit"        =>    "<h4>Kriter: <span class='res'>%s</span></h4>",
        "h3part"        =>    "<h3>Katılımcı <span class='res'>%s</span></h3>",
        "h4nd"          =>    "<h4>Düğüm: <span class='res'>%s</span></h4>"
    );

    /* Individual words */
    public $wrd  = array(
        "crit"          =>    "kriter",
        "alt"           =>    "alternatifler"
    );

    /* Result output */
    public $res  = array(
        "cr"            =>    "Tutarlılık Oranı CR: <span class='res'>%02.1f%%</span>",
        "consens1"      =>    "<p>Ortalama AHP grup ortak görüşü <i>S</i>*: <span class='res'>%02.1f%%</span>",
        "consens2"      =>    " Kriter: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
        "consens3"      =>    "<br>Göreceli Homojenlik <i>S</i>: <span class='res'>%02.1f%%</span>",
        "gCons"         =>    " - AHP grup ortak görüşü: <span class='res'>%02.1f%%</span> ",
        "consens4"      =>    "<p><small>Alternatiflerin, kriter <span class='res'>%s</span> açısından değerlendirilmesinde ortak görüş: 
                            <span class='res'>%02.1f%%</span>",
        "nodeCr"        =>    " Düğüm: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
        "ovlp"          =>    "Aşağıdaki %s, örtüşme olmadan:<br>",
        "ovlpNo"        =>    "%s içinde belirsizlikler dahilinde örtüşme yok",
        "ovlpAll"       =>    "Tüm %s, belirsizlikler dahilinde örtüşüyor.",
        "ovlpGrp"       =>    "Aşağıdaki grup(lar) %s, belirsizlikler dahilinde örtüşüyor:<br>",
        "rtrb"          =>    "<p class='msg'>1. En iyi alternatif <span class='res'>%s</span> için çözüm dayanıklıdır.<br>",
        "rt10"          =>    "<p class='msg'>1. <i>Yüzde-en üst</i> kritik kriter <span class='res'>%s</span>'dir: 
                            <span class='res'>%g%%</span> değişiklik, mutlak <span class='res'>%g%%</span> 
                            ile alternatifler <span class='res'>%s</span> ve <span class='res'>%s</span> arasındaki 
                            sıralamayı değiştirecektir.<br>",
        "rt11"          =>    "2. <i>Yüzde-herhangi</i> kritik kriter <span class='res'>%s</span>'dir: 
                            <span class='res'>%g%%</span> değişiklik, mutlak <span class='res'>%g %%</span> 
                            ile alternatifler <span class='res'>%s</span> ve 
                            <span class='res'>%s</span> arasındaki sıralamayı değiştirecektir.<br>",
        "rt11s"         =>    "2. <i>Yüzde-herhangi</i> kritik kriter yukarıdakiyle aynıdır.<br>",
        "rt20"          =>    "3. <i>Yüzde-herhangi</i> kritik performans ölçütü, kriter <span class='res'>%s</span> 
                            altında <span class='res'>%s</span> alternatifi içindir. 
                            <span class='res'>%g%%</span> değişiklik, mutlak <span class='res'>%g%%</span> 
                            ile sıralama arasındaki değişikliği sağlayacaktır 
                            <span class='res'>%s</span> ve <span class='res'>%s</span>."
    );

    /* Messages */
    public $msg  = array(
        "scaleSel"        =>    "<p class='msg'>Seçilen ölçek: <span class ='hl'>%s</span></p>",
        "wMethod"         =>    "<p>Metod: <span class ='hl'>Ağırlıklı Çarpım Metodu (ACM)</span></p>",
        "rMethod"         =>    "<p>Rastgele varyasyon: <span class ='hl'>standart sapma temel alınarak</span></p>",
        "mcVar"           =>    "<p class='msg'>Tahmini ağırlık belirsizlikleri, <span class='res'>%g</span> karar varyasyonuna dayanmaktadır.",
        "pSel"            =>    "<p>Seçilen katılımcılar: <span class='res'>%s</span></p>",
        "noSens"          =>    "<p class='msg'>Duyarlılık analizi mümkün değil.</p>",
        "noPwc1"          =>    "<span class='msg'> - Çiftli karşılaştırma verisi yok.</span>",
        "noPwc2"          =>    "<p class='msg'>Katılımcılardan çiftli karşılaştırma verisi yok</p>",
        "noPwc3"          =>    " - Katılımcılardan çiftli karşılaştırma verisi yok.",
        "noPwc4"          =>    "<p>Uyarı: <span class='msg'>%s</span></p>",
        "noRt"            =>    "<p class='msg'>Dayanıklılık testi mümkün değil.</p>",
        "pCnt"            =>    "Bireysel kararların %g Katılımcı(lar) için birleştirilmesi",
        "nlgin"           =>    "<p class='msg'>Projeleri yönetmek için kayıtlı bir kullanıcı olmanız ve giriş yapmanız gerekmektedir.</p>"
    );

    /* Errors */
    public $err  = array(
        "incompl"         =>    "<p class='err'>Proje değerlendirmesi tamamlanmamış</p>",
        "consens0"        =>    "<p>AHP grup ortak görüşü: <span class='err'>n/a</span>",
        "consens1"        =>    " - Ortak Görüş <span class='res err'>n/a</span>",
        "consens2"        =>    "<p><small>Alternatiflerin, kriter <span class='res err'>n/a</span> açısından değerlendirilmesinde ortak görüş"
    );

    /* Information output */
    public $info = array(
        "sensDl"          =>    "<p><small>Not: Tam analiz için indirme yapınız.</small></p>",
        "cpbd"            =>    "Her bir kriter açısından alternatiflerin toplu tercihleri",
        "pwcfor"          =>    "Çiftli karşılaştırmalar için: <br>"
    );

    /* Menu and buttons */
    public $mnu = array(
        "btnNdD"      =>    "<p><button href='#%s' class='nav-toggle'>Ayrıntılar</button>",
        "lgd1"        =>    "Grup Sonuç Menüsü",
        "lbl4"        =>    "ondalık virgül",
        "btn1"        =>    "Yenile",
        "btn2"        =>    "Giriş Verilerini Görüntüle",
        "btn3"        =>    "İndir (.csv)",
        "btn4"        =>    "Alternatifleri Tanımla",
        "btn5"        =>    "Tamamlandı",
        "lgd2"        =>    "Proje Veri Giriş Menüsü"
    );
}
