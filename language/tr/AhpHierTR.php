<?php

class AhpHierTR
{
    public $wrd = array(
        "lvl"           => "Seviye",
        "nd"            => "Düğüm",
        "lvls"          => "hiyerarşi seviye(leri)",
        "lfs"           => "hiyerarşi alt kategori(leri)",
        "nds"           => "hiyerarşi düğüm(leri)",
        "chr"           => "hiyerarşi karakterleri",
        "glbP"          => "Glb Öncelik",
        "alt"           => "Alternatifler"
    );

    public $wrn = array(
        "glbPrioS"      => "Global önceliklerin toplamı 100% değil. Hiyerarşiyi kontrol edin! ",
        "prioSum"       => "Uyarı! Önceliklerin toplamı %s kategorisi altında 100% değil."
    );

    public $err  = array(
        "hLmt"          => "Program sınırları aşıldı. ",
        "hLmtLv"        => "Çok fazla hiyerarşi seviyesi. ",
        "hLmtLf"        => "Çok fazla hiyerarşi alt kategorisi. ",
        "hLmtNd"        => "Çok fazla hiyerarşi düğümü. ",
        "hEmpty"        => "Hiyerarşi boş veya düğüm içermiyor, lütfen Hiyerarşi tanımlayın. ",
        "hSemicol"      => "Sonunda noktalı virgül eksik ",
        "hTxtlen"       => "Giriş metni maksimum uzunluğu aşıldı! ",
        "hNoNum"        => "Kategori/alt kategori adları sayı olmamalıdır; bulunan: ",
        "hEmptyCat"     => "Boş kategori adı ",
        "hEmptySub"     => "Boş alt kategori adı ",
        "hSubDup"       => "Yinelenen alt kategori ad(lar)ı: ",
        "hNoSub"        => "Kategoride 2'den az alt kategori ",
        "hCatDup"       => "Yinelenen kategori ad(lar)ı: ",
        "hColSemi"      => "<i>iki nokta üst üste</i> ve <i>noktalı virgül</i> sayısı eşit değil, hiyerarşi tanımını kontrol edin",
        "hHier"         => "Hiyerarşide hata, lütfen metni kontrol edin. ",
        "hMnod"         => "Hiyerarşi, birden fazla düğümle başlıyor - ",
        "unkn"          => "<span class='err'>Bilinmeyen Hata - Lütfen değerlendirmeyi %s tekrarlayın </span>"
    );

    public $msg = array(
        "sbmPwc1"       => "<small><span class='msg'>Lütfen çiftli karşılaştırmaları tamamlayın (\"AHP\" üzerine tıklayın)</span></small>",
        "sbmPwc2"       => "<small><span class='msg'>Tamam. Grup değerlendirmesi veya alternatif değerlendirme için gönderin.</span></small>",
        "aPwcCmplN"     => "<small><span class='msg'>%g/%g karşılaştırma tamamlandı</span></small>",
        "aPwcCmplA"     => "<small><span class='msg'>Tüm değerlendirmeler tamamlandı.</span></small>"
    );

    public $tbl = array(
        "hTblCp"        => "<caption>Karar Hiyerarşisi</caption>",
        "aTblCp"        => "<caption>Alternatiflerle Hiyerarşi</caption>",
        "aTblTh"        => "<th>No</th><th>Düğüm</th><th>Kriter</th><th>Glb Öncel.</th><th>Karşılaştır</th>",
        "aTblTd1"       => "Alternatiflerin toplam ağırlığı: "
    );
}
