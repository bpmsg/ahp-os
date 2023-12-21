<?php

class AhpHierarchyTR
{
    public $titles = array(
        "pageTitle"     => "AHP hiyerarşisi - AHP-OS",
        "h1title"       => "<h1>AHP Hiyerarşisi</h1>",
        "h2subTitle"    => "<h2>AHP-OS Karar Hiyerarşisi</h2>",
        "h4pDescr"      => "<h4>Proje açıklaması</h4>",
        "h3hInfo"       => "\n<h3>Hiyerarşi Bilgisi</h3>",
        "h3Proj"        => "<h3>Proje: <span class= 'var'>%s</span></h3>",
        "h2ieHier"      => "<h2>Giriş/Düzenle Hiyerarşi</h2>"
    );
    public $err = array(
        "giH"           => "Hiyerarşi girişinde Hata"
    );
    public $msg = array(
        "lgin"          => "<span class='msg'>Tam işlevsellik için lütfen kayıt olun ve giriş yapın.</span>",
        "pInp"          => "<p class='msg'><span class='var'>%s</span> projesi için giriş</p>",
        "pMod"          => "<p class='msg'><span class='var'>%s</span> projesinin değiştirilmesi</p>",
        "pNew"          => "<p class='msg'>Yeni proje</p>",
        "hMode"         => "<p class='msg'>Mod: Hiyerarşi değerlendirmesi</p>",
        "aMode"         => "<p class='msg'>Mod: Alternatif değerlendirmesi <span class='var'>%g</span> alternatifler</p>",
        "giUpd"         => "<span class='msg'> %g değerlendirme güncellendi. </span>",
        "giIns"         => "<span class='msg'> %g değerlendirme eklendi. </span>",
        "giTu"          => "Katılımınız için teşekkür ederiz!",
        "giNcmpl"       => "Çiftli karşılaştırmalar henüz tamamlanmadı!",
        "giNds"         => "Veri kaydedilmedi. ",
        "giPcmpl"       => "Lütfen önce tüm çiftli karşılaştırmaları tamamlayın. "
    );

    public $info = array(
        "intro"         => "<div class='entry-content'><p style='text-align:justify;'>
                                Analitik Hiyerarşi Süreci (AHP) kullanarak kriterler için bir karar hiyerarşisi tanımlayın 
                                ve çiftli karşılaştırmalara dayanarak ağırlıklarını hesaplayın. 
                                Bir sonraki adımda, alternatif bir küme tanımlayarak ve bunları kriter listesine 
                                göre değerlendirerek en tercih edilen alternatifi bulur ve karar probleminizi çözersiniz.
                                </p><p style='text-align:justify;'>
                                Basit bir çiftli karşılaştırmaya dayalı öncelik hesaplamak için 
                                <a href='ahp-calc.php'>AHP öncelik hesaplayıcıyı</a> kullanabilirsiniz. 
                                Hesaplayıcıdan memnun kalırsanız, sayfanın altındaki <i>beğen</i> düğmesine tıklayın. Teşekkür ederiz!</p></div>",
        "clkH"          => "<input type='button' class='btnr' value='AHP'> üzerine tıklayarak çiftli karşılaştırmayı tamamlayın. ",
        "clkA"          => "<b>Alternatifler</b>'e, ardından <b>AHP</b>'ye tıklayarak çiftli karşılaştırmayı tamamlayın.",
        "clkS"          => "<input type='button' value='Değerlendirmeleri Kaydet'> üzerine tıklayarak değerlendirmelerinizi nihai hale getirin ve kaydedin.",
        "txtfld"        => "Yukarıdaki metin alanına metin girişi veya düzenleme yapın, ardından gönderin. (Bkz. <a href='ahp-examples.php'>örnekler</a>)",
        "synHelp"       => "<br><span style='text-align:justify; font-size:small;'>
                                Yukarıdaki metin giriş alanında yeni bir hiyerarşi tanımlayabilirsiniz. 
                                Düğümler bir <b><i>iki nokta üst üste</i></b> ile ayrılır, kategori bir <b><i>virgül</i></b> ile ayrılır, 
                                ve herbir alt kategori bir <b><i>noktalı virgül</i></b> ile sonlandırılmalıdır. 
                                Yaklaşık karakteri (~) atılır. Kategoriler ve alt kategoriler için adlar benzersiz olmalıdır. 
                                Kategori adı olarak sayılar kullanılamaz, <i>ör.</i> \"100\" yerine \"100 $\" kullanın. 
                                Bir kategorinin tek bir alt kategorisi olamaz. Varsayılan olarak, tüm öncelikler her kategoride 
                                veya alt kategoride eşit olarak ayarlanır ve toplamda %100'e tamamlanır. 
                                Not: Giriş büyük-küçük harfe duyarlıdır.</span>",
        "nlg"           => "<p class='hl'>Kayıtlı bir kullanıcı olarak öncelikleri indirebilir ve tanımlanan hiyerarşiyi bir proje olarak kaydedebilirsiniz.</p>",
        "lgi"           => "<p class='msg'>AHP öncelik değerlendirmesi yapabilmek üzere çiftli karşılaştırmlara başlamak için <i>Kaydet/Güncelle</i> proje sayfanızdan açın ve kaydedin.
                                Alternatif değerlendirmesi için önceden değerlendirilmiş veya tanımlanmış önceliklere sahip bir hiyerarşi kullanarak alternatif adlarını tanımlayın ve 
                                Alternatif menüsünden <i>Kaydet</i>'e tıklayın.</p>",
        "giPcmpl"       => "<input type='button' value='Alternatifler'> üzerine tıklayın, ardından <input class='btnr ' type='button' value='AHP'>"
    );

    public $mnu    = array(
        "lgd11"         => "Hiyerarşi Giriş Menüsü",
        "btn11"         => "Gönder",
        "btn12"         => "Kaydet/Güncelle",
        "btn13"         => "İndir (.csv)",
        "lbl11"         => "ondalık virgül",
        "btn14"         => "Öncelikleri Sıfırla",
        "btn15"         => "Tümünü Sıfırla",
        "btn16"         => "Tamamlandı",
        "lgd21"         => "Grup Giriş Menüsü",
        "btn21"         => "Değerlendirmeleri Kaydet",
        "btn22"         => "Grup Sonucunu Görüntüle",
    );
}
