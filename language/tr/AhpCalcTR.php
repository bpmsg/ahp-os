<?php

class AhpCalcTR
{
    // Başlıklar
    public $titles = array(
        'h3ResP'   =>   "<h3>Öncelikler</h3>",
        'h3ResDm'  =>   "<h3>Karar Matrisi</h3>"
    );

    // Hatalar
    public $err = array(
        'ePwc'      =>   "<span class='err'>Giriş hatası</span>",
        'adjPwc'    =>   "<span class='err'>Tutarlılığı artırmak için vurgulanan kararları ayarlayın</span>",
        'nCrit'     =>   "<span class='err'>Uyarı, n 1 ile %g arasında olmalıdır, n varsayılan olarak ayarlandı.</span>"
    );

    // Sonuç metni
    public $res = array(
        'npc'      =>   "Karşılaştırma sayısı = <span class='res'>%g</span><br>",
        'cr'       =>   "<b>Tutarlılık Oranı CR</b> = <span class='res'>%2.1f%%</span><br>",
        'ev'       =>   "Asıl eigen değeri = <span class='res'>%2.3f</span><br>",
        'it'       =>   "Eigen vektör çözümü: <span class='res'>%d</span> iterasyon, delta = <span class='res'>%01.1E</span>"
    );

    // Mesajlar
    public $msg = array(
        'ok'       =>   "<span class='msg'>Tamam</span>",
        'sPwc'     =>   "<span class='msg'>Lütfen çiftli karşılaştırmaya başlayın</span>",
        'def'      =>   "<span class='msg'>Bazı isimler varsayılana ayarlandı.</span>"
    );
    
    // Bilgi
    public $info= array(
        'pwcAB'     =>   "A - Önem - ya da B mi?",
        'resP'     =>   "Bu, çiftli karşılaştırmalarınıza dayalı olarak kriterler için elde edilen ağırlıklardır:",
        'resDm'    =>   "Sonuç ağırlıklar, karar matrisinin ana eigen vektörüne dayanmaktadır:",
        'cNbr'     =>   "<span class='hl'>Sayı ve isimleri girin (2 - %g) </span>",
        'wlMax'    =>   "<small>maks. %g karakter her biri için</small>"
    );

    // Tablolar
    public $tbl = array(
        'cTblTh'   =>   "<thead><tr class='header'>
                        <th colspan='3' class='ca' >%s</th>
                        <th>Eşit</th>
                        <th class='ca' >Ne kadar daha önemli?</th></tr></thead>",
        'pTblTh'   =>   "<th colspan='2' class='la' >Ktg</th>
                        <th>Öncelik</th>
                        <th>Sıra</th>",
        'gcTblTh'  =>   "<tr><th colspan='2' class='ca' >%s Adı</th></tr>"
    );

    // Menü ve düğmeler
    public $mnu = array(
        'btnChk'   =>   "<input id='sbm1' %s type='submit' value='Hesapla' name='pc_submit' />",
        'btnSbm'   =>   "<input type='submit' value='%s' name='%s' %s %s />",
        'btnDl'    =>   "ondalık virgül"
    );
    
}
