<?php
    /* hem giriş hem de kayıt için kullanılır */

    class LoginTR
    {
        public $titles = array(
        "h1edit"    => "Bilgilerinizi düzenleyin",
        "h2info"    => "Hesap Bilgileri",
        "h2act"     => "Son hesap faaliyetleri",
        "h2lgin"    => "Lütfen giriş yapın",
        "h1reg"     => "Kullanıcı Kaydı",
        "h2reg"     => "Kayıt Formu",
        "h1pwR"     => "Şifre Sıfırlama"
    );

        public $err  = array(
        "aNact"     =>  "Hesabınız henüz etkinleştirilmedi. Lütfen e-postadaki onay bağlantısına tıklayın.",
        "dbCon"     =>  "Veritabanı bağlantı sorunu.",
        "emlE"      =>  "E-posta boş olamaz",
        "emlL"      =>  "E-posta 64 karakterden uzun olamaz",
        "emlD"      =>  "Üzgünüz, bu e-posta adresi mevcut olanla aynı. Lütfen başka bir e-posta adresi seçin.",
        "emlI"      =>  "E-posta adresiniz geçerli bir e-posta formatında değil",
        "emlR"      =>  "Bu e-posta adresi zaten kayıtlı. Eğer hatırlamıyorsanız, lütfen \"Şifremi Unuttum\" sayfasını kullanın.",
        "emlNc"     =>  "Üzgünüz, e-posta değiştirme başarısız oldu.",
        "emlVns"    =>  "Üzgünüz, size bir doğrulama postası gönderemedik. Hesabınız OLUŞTURULAMADI.",
        "emlNs"     =>  "Doğrulama Postası BAŞARILI bir şekilde gönderilemedi! Hata: ",
        "pwW"       =>  "Giriş başarısız. Tekrar deneyin.",
        "pwW3"      =>  "3 veya daha fazla kez yanlış şifre girdiniz. Lütfen tekrar denemek için 30 saniye bekleyin.",
        "pwS"       =>  "Şifre en az 6 karakter uzunluğunda olmalıdır",
        "pwE"       =>  "Şifre alanı boş",
        "pwNi"      =>  "Şifre ve şifre tekrarı aynı değil",
        "pwCf"      =>  "Üzgünüz, şifre değiştirme başarısız oldu.",
        "pwRf"      =>  "Şifre sıfırlama postası BAŞARILI bir şekilde gönderilemedi! Hata: ",
        "pwOw"      =>  "ESKİ şifreniz yanlış.",
        "unNe"      =>  "Bu kullanıcı/e-posta mevcut değil",
        "unTk"      =>  "Üzgünüz, bu kullanıcı adı zaten alınmış. Lütfen başka bir tane seçin.",
        "unIv"      =>  "Kullanıcı adı isim düzenine uymuyor: sadece a-Z ve rakamlara izin verilir, 2 ila 64 karakter giriniz",
        "unDb"      =>  "Üzgünüz, bu kullanıcı adı mevcut olanla aynı. Lütfen başka bir tane seçin.",
        "unF"       =>  "Üzgünüz, seçtiğiniz kullanıcı adı değiştirme işlemi başarısız oldu",
        "unE"       =>  "Kullanıcı adı alanı boş",
        "unL"       =>  "Kullanıcı adı 2 karakterden kısa veya 64 karakterden uzun olamaz",
        "lnkExp"    =>  "Sıfırlama bağlantınız süresi doldu. Lütfen sıfırlama bağlantısını bir saat içinde kullanın.",
        "lnkE"      =>  "Boş bağlantı parametre verisi.",
        "regF"      =>  "Üzgünüz, kayıt başarısız oldu. Lütfen geri dönün ve tekrar deneyin.",
        "wVc"       =>  "Üzgünüz, burada böyle bir id/doğrulama kodu kombinasyonu yok...",
        "iCk"       =>  "Geçersiz çerez",
        "wCp"       =>  "Captcha yanlış!"
    );

        public $msg = array(
        "lgOut"     =>  "Çıkış yaptınız.",
        "emlN"      =>  "Lütfen geçerli bir e-posta adresi girin!",
        "emlCok"    =>  "E-posta adresiniz başarıyla değiştirildi. Yeni e-posta adresi: %s",
        "unCok"     =>  "Kullanıcı adınız başarıyla değiştirildi. Yeni kullanıcı adınız: ",
        "pwCok"     =>  "Şifre başarıyla değiştirildi!",
        "pwRms"     =>  "Şifre sıfırlama e-postası başarıyla gönderildi!",
        "aOk"       =>  "Hesabınız başarıyla etkinleştirildi. Lütfen işlemi tamamlamak için giriş yapın!",
        "regOk"     =>  "Hesabınız başarıyla oluşturuldu ve size bir e-posta gönderdik (Lütfen spam klasörünüzü de kontrol edin).
                         Lütfen hesabınızı etkinleştirmek için  E-POSTADA YER ALAN DOĞRULAMA BAĞLANTISINA tıklayın.",
        "verOk"     =>  "Hesabınız başarıyla etkinleştirildi.",
        "deact"     =>  "%s hesabı başarıyla devre dışı bırakıldı",
        "deactm"    =>  " ve yeniden etkinleştirme e-postası gönderildi."
    );

        public $info = array(
        "reg"       =>  "Lütfen kaydolmak için aşağıdaki formu doldurun ve geçerli bir e-posta adresi girin.",
        "delA"      =>  "<p><span class='err'>Hesabı ve tüm ilgili verileri sil</span>. 
                        Hesabınız hemen devre dışı bırakılacak ve yeniden etkinleştirme bağlantısı içeren bir e-posta alacaksınız. 
                        Eğer hesabınızı yeniden etkinleştirmezseniz, hesap bilgileriniz ve tüm ilgili veriler iki gün sonra tamamen silinecektir.</p>",
        "conf"      =>  "Kaydımla ilgili üç ay içinde bir hesap etkinliği olmazsa her üç ayda bir reaktivasyon e-postaları almayı kabul ediyorum. 
                        48 saat içinde yeniden etkinleştirilmezse, hesabım ve tüm veriler otomatik olarak silinecektir.",
        "pwRes"     =>  "E-posta adresinizi girdikten sonra talimatları içeren bir posta alacaksınız:<br>",
        "nlgin"     =>  "Bu web sitesine erişim sağlamak için bir hesabınız olması gereklidir.",
        "nReg"      =>  "Lütfen hesabı aktif etmek için <a href='mailto:webmaster@bpmsg.com'>Webmaster'a</a> iletişime geçin."    
);

        public $wrd = array(
        "crC"       =>  "Kimlik bilgilerinizi buradan düzenleyin:",
        "emlC"      =>  "E-postayı değiştir",
        "emlN"      =>  "Yeni e-posta:",
        "pwC"       =>  "Şifreyi değiştir",
        "pwO"       =>  "ESKİ Şifre:",
        "pwN"       =>  "Yeni şifre:",
        "pwNr"      =>  "Yeni şifreyi tekrarlayın:",
        "unC"       =>  "Kullanıcı adını değiştir",
        "unN"       =>  "Yeni kullanıcı adı (2-30 karakter, azAZ09):",
        "delA"      =>  "Hesabımı sil",
        "cont"      =>  "Devam et",
        "done"      =>  "Tamamlandı",
        "eml"       =>  "Kullanıcının e-posta adresi (lütfen geçerli bir e-posta adresi sağlayın, etkinleştirme bağlantısı içeren bir doğrulama e-postası alacaksınız)",
        "pw"        =>  "Şifre (en az 6 karakter!)",
        "pwr"       =>  "Şifreyi tekrarlayın",
        "un"        =>  "Kullanıcı adı (sadece harf ve sayılar, 2 ila 30 karakter)",
        "pwRes"     =>  "Şifremi sıfırla",
        "pwSbm"     =>  "Yeni şifreyi gönder",
        "hlPw"      =>  "şifre",
        "hlUn"      =>  "kullanıcı adı veya e-posta",
        "hlAc"      =>  "Hesap",
        "hlLo"      =>  "Çıkış",
        "hlFg"      =>  "Şifremi Unuttum?",
        "hlReg"     =>  "Kayıt&nbsp;ol",
        "hlWlc"     =>  "Hoş geldiniz "
    );

        public $tbl = array(
        "tbEdTd1"   =>  "Kullanıcı ID:",
        "tbEdTd2"   =>  "Kullanıcı Adı:",
        "tbEdTd3"   =>  "E-posta:",
        "tbEdTd4"   =>  "Kayıt tarihi:",
        "tbEdTd5"   =>  "Çerezleri Hatırla:"
    );
}
