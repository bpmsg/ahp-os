<?php

class AhpHierginiTR
{
    // Errors
    public $err = array(
        "pExc"      => "Proje sayısı aşıldı, veri kaydedilemiyor. Lütfen bazı projelerinizi iptal edin ve silin. ",
        "noSc"      => "Lütfen bir oturum kodu sağlayın. ",
        "noName"    => "Lütfen adınızı belirtin. ",
        "pwcCompl"  => "<span class='var'>%s</span> altında çiftli karşılaştırmalar tamamlandı.",
        "hDefP"     => "Hiyerarşide tanımlı öncelik yok. Proje güncellenemez.",
        "unknw"     => "Bilinmeyen hata - sahip: %s retFlg: %g"
    );

    public $titles = array(
        "pageTitle" => "AHP Oturum Girişi AHP-OS",
        "h1Title"   => "<h1>AHP Oturum Girişi</h1>",
        "subTitle1" => "AHP-OS Katılımcı Girişi",
        "subTitle2" => "AHP Projesi Kaydet/Güncelle",
        "subTitle3" => "Çiftli Karşılaştırma Girişi",
        "h3Pwc"     => "<h3>Çiftli Karşılaştırma <span class='var'>%s</span></h3>",
        "h3Res"     => "<h3 align='center'>Oluşan Öncelikler</h3>",
        "h2siMnu"   => "<h2>AHP Oturum Giriş Menüsü</h2>"
    );

    // Messages
    public $msg = array(
        "nProj" => "Yeni proje, kaydetmek için \"Git\" düğmesine tıklayın",
        "pMod"  => "Mevcut proje değiştirilecek ve üzerine yazılacak!"
    );

    // Information
    public $info = array(
        "intro"     => "<div class='entry-content'>
                            <p style='text-align:justify;'>AHP-OS, <i>Analitik Hiyerarşi Prosesi</i> (AHP) temelinde akılcı karar verme sürecini desteklemek için çevrimiçi bir araçtır. 
                            Seçilen katılımcı olarak, lütfen <b>oturum kodunuzu ve adınızı girin, anketi tamamlayın ve girişinizi grup değerlendirmesi için gönderin</b>. Bu, 
                            girişlerinizin nihai kararda yansıtılmasına yardımcı olacaktır. Teşekkür ederiz!</p>
                            </div>",
        "act1"      => "Yeni proje. Oturum kodu %s. ",
        "act2"      => "Projeyi güncelle. ",
        "act3"      => "Projede %g katılımcı(lar) var. ",
        "ok"        => "<p class='msg'>Tamam. Devam etmek için \"Git\" düğmesine tıklayın</p>",
        "siSc"      => "Lütfen AHP grup oturumuna katılmak için oturum kodunuzu girin",
        "siNm1"     => "<a href='%s?logout'>Başka bir katılımcının adını girmek için çıkış yapın</a>.",
        "siNm2"     => "Grup oturumunda kullanılacak adınız (3 - 25 karakter aralığında olacak şekilde).",
        "pName"     => "AHP Proje Adı:",
        "pStat"     => "Proje Durumu:",
        "pDescr"    => "Proje Kısa Açıklaması:",
        "descr"     => "</br><small>Grup oturumunun katılımcılarına görüntülenecek olan metin, en fazla 400 karakter olmalıdır. 

                        Metni vurgulamak veya renklendirmek için &lt;em&gt; veya &lt;font&gt; gibi HTML etiketlerini kullanabilirsiniz.</small>"
    );

    // Menu and buttons
    public $mnu = array(
        "lgd1"  => "AHP Oturumu Girişi",
        "lgd2"  => "Oturum Giriş Menüsü",
        "sc"    => "Oturum Kodu:",
        "nm"    => "Adınız:",
        "btn1"  => "Git",
        "btn2"  => "Girişi kontrol et",
        "btn3"  => "Grup sonucunu görüntüle",
        "btn4"  => "Sıfırla",
        "btn5"  => "İptal"
    );
}
