<?php

class AhpPrioCalcTR
{
    public $wrd = array(
        "crit" => "Kriterler",
        "alt" => "Alternatifler"
    );

    // Hatalar
    public $err = array(
        "pgm" => "<br><span class='err'>Program Hatası</span>",
        "pwcInc" => "<span class='err'>Çiftli karşılaştırmalar henüz tamamlanmadı!</span>"
    );

    // calc (priority calculator)
    public $titles1 = array(
        "pageTitle" => "AHP hesaplayıcı - AHP-OS",
        "h1title" => "<h1>AHP Öncelik Hesaplayıcı</h1>",
        "h2subTitle" => "<h2>AHP Kriterleri</h2>",
        "h3Pwc" => "<h3>Çiftli Karşılaştırma <span class='var'>%s</span></h3>",
        "h3Res" => "<h3 align='center'>Sonuçlanan Öncelikler</h3>"
    );

    // hiercalc
    public $titles2 = array(
        "pageTitle" => "PWC Crit AHP-OS",
        "h1title" => "<h1>Çiftli Karşılaştırma AHP-OS</h1>",
        "h2subTitle" => "<h2><span class='var'>%s</span>Kriterlerin Değerlendirilmesi için</h2>",
    );

    // altcalc
    public $titles3 = array(
        "pageTitle" => "PWC Alt AHP-OS",
        "h1title" => "<h1>Çiftli Karşılaştırma AHP-OS</h1>",
        "h2subTitle" => "<h2><span class='var'>%s</span>Alternatiflerin Değerlendirilmesi için</h2>",
        "h2alt" => "<h2>Alternatifler</h2>",
        "h3Mnu" => "<h3>Alternatif Menüsü</h3>",
        "h3tblA" => "<h3>Proje Yapısı</h3>",
        "h3Res" => "<h3>Alternatiflerin Sonucu</h3>",
        "h4Res" => "<h4>Öncelikler ve Sıralama</h4>"
    );

    // calc1
    public $titles4 = array(
        "pageTitle" => "AHP Kriterleri",
        "h1title" => "<h1 class='ca' >AHP Kriter İsimleri</h1>"
    );

    // alt1
    public $titles5 = array(
        "pageTitle" => "AHP Alternatifleri",
        "h1title" => "<h1 class='ca' >AHP Alternatif İsimleri</h1>"
    );

    // Mesajlar
    public $msg = array(
        "nPwc" => "<span class='msg'>%g çiftli karşılaştırma(lar). </span>",
        "pwcAB" => "A - <span class='var'>%s</span> ile karşılaştırılınca - B?",
        "noPwc1" => "<span class='msg'>Lütfen öncelikle tüm çiftli karşılaştırmaları tamamlayın. ",
        "noPwc2" => "<input type='button' value='Alternatifler'> ardından ",
        "noPwc3" => "<input class='btnr ' type='button' value='AHP'></span>",
        "tu" => "Katılımınız için Teşekkür Ederiz!",
        "giUpd" => "<span class='msg'> %g karar güncellendi. </span>",
        "giIns" => "<span class='msg'> %g karar eklendi. </span>",
        "inpA" => "<p class='ca' >Lütfen doldurun</p>"
    );

    // Bilgiler
    public $info = array(
        "intro" => "Sayı ve kriter isimlerini seçin, ardından Analitik Hiyerarşi Süreci'ni kullanarak öncelikleri hesaplamak için çiftli karşılaştırmalara başlayın.",
        "pwcQ" => "<p><span class='hl'><i><span class='var'>%s</span></i> ile ilgili olarak, hangi kriter daha önemli ve bu ölçekte 1 ile 9 arasında ne kadar daha önemli%s</span></p>",
        "pwcQA" => "<p><span class='hl'><i><span class='var'>%s</span></i> ile ilgili olarak, hangi alternatif daha iyi uyar veya tercih edilir ve bu ölçekte 1 ile 9 arasında ne kadar daha iyi%s</span></p>",
        "selC" => "Kriter sayısını seçin:",
        "scale" => "<p style='font-size:small'>AHP Ölçeği: 1- Eşit Önem, 3- Orta Önem, 5- Güçlü Önem, 7- Çok Güçlü Önem, 9- Aşırı Önemli (2,4,6,8 ara değerlerdir).</p>",
        "doPwc" => "Lütfen tüm kriterlerin çiftli karşılaştırmasını yapın. Tamamlandığında, tutarlılığı kontrol etmek için <i>Tutarlılık Kontrolü</i>'ne tıklayın ve öncelikleri almak için <i>Sonuçları Gönder</i>'e tıklayın.<br>",
        "doPwcA" => "Lütfen tüm alternatiflerin çiftli karşılaştırmasını yapın ve ardından ağırlıkları almak için <i>Tutarlılık Kontrolü</i>'ne ve devam etmek için <i>Öncelikleri Gönder</i>'e tıklayın. ",
        "doPwcA1" => "<p>Alternatifleri kriterlere göre karşılaştırın (AHP'ye tıklayın). Alternatiflerin her bir kritere ne kadar iyi uyduğunu düşünüyorsunuz?</p>",
        "adj" => "<p class='msg'>Tutarlılığı artırmak için vurgulanan değerleri ölçekte artı veya eksi bir veya iki puan değiştirin.</p>",
        "inpAlt" => "Buradan alternatif sayınızı ve isimlerinizi girebilirsiniz.",
        "pSave" => "<p>Proje için tanımlanan alternatifleri içeren projeyi kaydetmek için <i>Projeyi Kaydet</i>'e tıklayın.</p>"
    );

    // Menü ve düğmeler
    public $mnu = array(
        "btnSbm" => "Gönder",
        "lgd1" => "AHP Öncelik Hesaplayıcı",
        "done" => "Devam",
        "next" => "Sonraki",
        "lgd2" => "Alternatif Menüsü",
        "btn1" => "Kararları Kaydet",
        "btn2" => "Alternatifleri Sıfırla",
        "btn3" => "Proje Olarak Kaydet"
    );
}
