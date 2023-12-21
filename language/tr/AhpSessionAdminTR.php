<?php

class AhpSessionAdminTR
{
    public $titles = array(
        'pageTitle' => "AHP projeleri - AHP-OS",
        'h1title' => "<h1>AHP Proje Yönetimi</h1>",
        'h2subTitle' => "<h2>AHP-OS - Mantıklı karar verme kolaylaştırıldı</h2>",
        'h2ahpProjSummary' => "<h2>Proje Özeti</h2>",
        'h2ahpSessionMenu' => "<h2>AHP Oturumu Menüsü</h2>",
        'h2ahpProjectMenu' => "<h2>AHP Proje Menüsü</h2>",
        'h2myProjects' => "<h2>AHP Projelerim</h2>",
        'h3groupInpLnk' => "<h3>Grup Giriş Bağlantısı</h3>",
        'h3projStruc' => "<h3>Proje Yapısı</h3>",
        'h4hierDefTxt' => "<h4>Hiyerarşi Tanım Metni</h4>",
    );
    public $msg = array(
        'sDel' => "<span class='msg'>Oturum <span class='var'>%s</span> başarıyla silindi</span>",
        'sDelp' => "Katılımcı(lar) <span class='res'>%s</span> başarıyla silindi ",
        'pwcCompl' => "<span class='msg'><span class='var'>%s</span> adlı çiftli karşılaştırmalar tamamlandı.</span>",
        'pClsd' => "<p class='msg'>Proje kapalı. Açmak için <i>Proje Durumu Değiştir</i>'e tıklayın.</p>",
        'pStat1' => "Proje durumu değiştirildi: ",
        'pStatO' => "açık.",
        'pStatC' => "kapalı.",
        'selPart' => "<span class='msg'>Seçilen katılımcı(lar): </span><span class='var'>%s</span>",
        'hInfo1' => "<span class='msg'>Karar hiyerarşisi tanımlanmış önceliklere sahiptir</span>",
        'hInfo2' => "<span class='msg'>. Projeye alternatif tanımlanabilir. <br><i>Hiyerarşi Kullan</i>'a tıklayın</span>",
        'hInfo3' => "<span class='msg'> ve projenin <span class='var'>%g</span>tanımlanmış  alternatifi vardır.</span>",
        'usrStat1' => "<p class='msg'><small>AHP-OS'de <span class='res'>%s</span> kayıtlı kullanıcı var, ",
        'usrStat2' => "<span class='res'>%g</span> son %g saatte aktif kullanıcı.</small></p>",
        'usrStat3' => "<p class='msg'>%s, <span class='res'>%g</span> projeniz var. ",
        'usrStat4' => "Program kullanım endeksiniz <span class=res>%g%%</span>. ",
        'usrDon1' => "Lütfen bir <a href='ahp-news.php'>bağış</a> düşünün",
        'usrDon2' => "Bağışınız için teşekkür ederiz"
    );
    public $err = array(
        'invSess1' => "Geçersiz Oturum Kodu.",
        'invSess2' => "Url'de Geçersiz Oturum Kodu.",
        'noAuth' => "Proje yöneticisi olmadığınızdan, katılımcıları silme izniniz yok.",
        'pClosed' => "Proje kapalı. Çiftli karşılaştırma giriş izin verilmiyor.",
        'noDel' => "silinemedi.",
        'sLmt' => "<p><span class='err'>Oturum sınırına ulaşıldı.</span> Lütfen önce bazı eski oturumları silin. </p>"
    );
    public $info = array(
        'sc' => "Oturum kodu <span class='var'>%s</span>.",
        'scLnk1' => "Bu oturum kodunu veya aşağıdaki bağlantıyı katılımcılarınıza sağlayın: </span><br>",
        'scLnk2' => "<textarea rows='1' cols='78'>%s?sc=%s</textarea><br>",
        'scLnk3' => "Yukarıdaki linke git: <a href='%s?sc=%s' >Grup Girişi</a><br>",
        'pOpen1' => "Aşağıdaki tablodaki oturum bağlantısına tıklayarak bir projeyi açın. ",
        'pOpen2' => "<br><a href='%s'>Yeni bir hiyerarşi oluşturun</a>.",
        'logout' => "<div class='entry-content'>
                        AHP proje yönetimi sayfasında AHP projelerinizi yönetebilirsiniz: 
                        yeni hiyerarşiler oluşturabilir, mevcut projeleri açabilir, düzenleyebilir, silebilir ve görüntüleyebilirsiniz. 
                        <p class='msg'>Projeleri yönetmek için kayıtlı bir kullanıcı olmanız ve giriş yapmanız gerekmektedir.</p>
                        <p><a href='%s'>geri</a></p></div>"
    );
    public $mnu = array(
        'lgd1' => "Oturum Yönetimi Menüsü",
        'lbl1' => "Proje Oturum Kodu: ",
        'btnps1' => "Projeyi Aç",
        'btnps2' => "Yeni Proje",
        'btnps3' => "Tamamlandı",
        'btnps4' => "Projeyi İçe Aktar",
        'lgd2' => "Proje Yönetimi Menüsü",
        'btnpa1' => "Sonucu Görüntüle",
        'btnpa2' => "Çiftli Karşılaştırma Girişi",
        'btnpa3' => "Hiyerarşi Kullan",
        'btnpa4' => "Yeniden Adlandır",
        'btnpa5' => "Düzenle",
        'btnpa6' => "Seçilen Kat.(ları) Sil",
        'btnpa7' => "Projeyi Sil",
        'btnpa8' => "Proje Durumunu Değiştir",
        'btnpa9' => "Tamamlandı",
        'btnpa10' => "Projeyi Dışa Aktar"
    );
}
