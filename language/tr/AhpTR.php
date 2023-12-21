<?php

class AhpTR
{
    public $titles = array(
        'pageTitle'     =>    "AHP Çevrimiçi Sistemi - AHP-OS",
        'h1title'       =>    "<h1>AHP Çevrimiçi Sistemi - AHP-OS</h1>",
        'h2subTitle'    =>    "<h2>Analitik Hiyerarşi Süreci Kullanarak Çok Kriterli Karar Verme</h2>",
        'h2contact'     =>    "<h2>İletişim ve Geri Bildirim</h2>"
    );

    public $msg = array(
        'tu'    =>    "Teşekkür Ederim!",
        'cont'  =>    "Devam"
    );

    public $info = array(
        'contact'    =>    "<p>
                            Lütfen bir <a href='%s'>yorum</a> bırakmaktan çekinmeyin.</p>",
        'intro11'    =>    "<div class='entry-content'><p style='text-align:justify;'>
                            Bu ücretsiz <b>web tabanlı AHP çözümü</b>, karar verme süreçlerine destek sağlayan
                            bir araçtır. Programlar, günlük işlerinizde basit karar problemleri için yardımcı olabilir ve
                            aynı zamanda karmaşık karar verme problemlerine destek sağlar. Bir grup oturumuna katılın ve
                            <a href='https://bpmsg.com/participate-in-an-ahp-group-session-ahp-practical-example/'>pratik bir örnek</a> deneyin.
                            <a href='docs/BPMSG-AHP-OS-QuickReference.pdf' target='_blank'>Hızlı başvuru kılavuzunu</a> veya
                            <a href='docs/BPMSG-AHP-OS.pdf' target='_blank'>AHP-OS kılavuzunu</a> indirin.
                            Tam işlevselliğe sahip olmak için giriş yapmanız gerekmektedir. Henüz bir hesabınız yoksa lütfen
                            <a href='includes/login/do/do-register.php'>kayıt olun</a>. Tamamen ücretsizdir!
                            </p></div>",

        'intro12'    =>    "<ol style='line-height:150%;'>
                            <li><span style='cursor:help;'
                            title='Tüm AHP projelerini ve grup oturumlarını yönet. Kayıtlı bir kullanıcı olmanız ve giriş yapmanız gerekiyor.' >
                            <a href='ahp-session-admin.php'>AHP Projelerim</a></span></li>
                            <li><span style='cursor:help;'
                            title='AHP öncelik hesaplayıcı, çiftli karşılaştırmalara dayalı olarak bir dizi kriter için öncelikleri veya ağırlıkları hesaplar.' >
                            <a href='ahp-calc.php'>AHP Öncelik Hesaplayıcı</a></span></li>
                            <li><span style='cursor:help;'
                            title='AHP altındaki tam karar problemleriyle başa çıkın. Kriterlerin hiyerarşisini tanımlayın ve alternatifleri değerlendirin.' >
                            <a href='ahp-hierarchy.php'>AHP Hiyerarşileri</a></span></li>
                            <li><span style='cursor:help;'
                            title='Bir grup üyesi olarak kriterleri veya alternatifleri değerlendirmek için AHP grup oturumuna katılın' >
                            <a href='ahp-hiergini.php'>AHP Grup Oturumu</a></span></li>
                            <li><span style='cursor:help;'
                            title='Grup ortak görüşünü analiz edin' >
                            <a href='ahp-cluster.php'>Grup Ortak Görüş Küme Analizi</a></span>
                            <small>(deneysel)</small></li>
                            </ol>",

        'intro13'    =>    "<p style='text-align:justify;'>
                            Programlar 2 ve 3 için sonuçları csv dosyaları (virgülle ayrılmış değerler) olarak dışa aktarabilirsiniz.
                            Bu dosyaları excelde daha fazla işleme tabi tutabilirsiniz.</p>",

        'intro14'    =>    "<p style='text-align:justify;'>
                            <b>Kullanım koşulları için lütfen </b>
                            <a href='https://bpmsg.com/about/user-agreement-and-privacy-policy/' >
                            kullanıcı anlaşması ve gizlilik politikamıza</a> bakın.</p>",

        'intro15'    =>    "<p style='text-align:justify;'>
                            Programı beğendiyseniz, <span class='err'>lütfen yardımcı olun ve web sitesini sürdürebilmek için bir
                            <a href='ahp-news.php'>bağış</a> yapın</span>.</p>",

        'intro16'    =>    "<p><b>Çalışmanız için lütfen şunu belirtin:</b><br>
                            <code>Goepel, K.D. (2018). Implementation of an Online Software Tool for the Analytic Hierarchy 
                            Process (AHP-OS). <i>International Journal of the Analytic Hierarchy Process</i>, Vol. 10 Issue 3 2018, pp 469-487,
                            <br><a href='https://doi.org/10.13033/ijahp.v10i3.590'>https://doi.org/10.13033/ijahp.v10i3.590</a>
                            </code></p>",

        'intro21'    => "<h3>Giriş</h3>
                            <div style='display:inline;'>
                            <img src='images/AHP-icon-150x150.png' alt='AHP' style='float: left; height:15%; width:15%; padding:5px;'>
                            </div><div class='entry-summary'><p style='text-align:justify;'>
                            AHP, <i>Analitik Hiyerarşi Süreci</i> için kısaltılmış bir terimdir. Bu, çok kriterli karar verme süreçlerini
                            desteklemek için bir yöntemdir ve başlangıçta Prof. Thomas L. Saaty tarafından geliştirilmiştir. AHP, kriterlerin
                            çiftli karşılaştırmalarından oran ölçekler türetir ve bazı küçük tutarsızlıklara izin verir. Girişler gerçek ölçümler
                            olabilir, ancak aynı zamanda öznel görüşler de olabilir. Sonuç olarak, <i>öncelikler</i> (ağırlıklar) ve bir
                            <i>tutarlılık oranı</i> hesaplanacaktır. Uluslararası alanda AHP, örneğin tedarikçilerin değerlendirilmesi,
                            proje yönetimi, işe alım süreci veya şirket performansının değerlendirilmesi gibi birçok uygulamada kullanılmaktadır. </p></div>",

        'intro22'    =>"    <div style='display:block;clear:both;'>
                            <h3>AHP'nin Faydaları</h3>
                            <p style='text-align:justify;'>
                            AHP'yi karar verme sürecine destekleyici bir araç olarak kullanmak, karmaşık karar problemlerine
                            <i>daha iyi bir içgörü kazanmanıza yardımcı olacaktır</i>. Problemi bir hiyerarşi olarak yapılandırmanız
                            gerektiğinden dolayı, problemi düşünmeye, olası karar kriterlerini düşünmeye ve karar hedefine göre en
                            önemli kriterleri seçmeye zorlar. Çiftli karşılaştırmaların kullanılması, mantıksal tutarsızlıkları
                            keşfetmeye ve düzeltmeye yardımcı olur. Yöntem ayrıca öznelerin tercihleri veya duyguları gibi
                            öznel görüşleri, ölçülebilir sayısal ilişkilere \"çevirmenize\" izin verir. AHP, kararları daha
                            rasyonel bir şekilde almanıza ve daha şeffaf ve anlaşılır hale getirmenize yardımcı olur.
                            </p>",

        'intro23'    =>"    <h3>Metod</h3>
                            <p style='text-align:justify;'>
                            Matematiksel olarak yöntem, bir eigen değer problemi çözümüne dayanmaktadır. Çiftli karşılaştırmaların
                            sonuçları bir matrise yerleştirilir. Matrisin ilk (baskın) normalleştirilmiş sağ eigen vektörü,
                            oran ölçeğini (ağırlıklandırma) verir, eigen değer tutarlılık oranını belirler.
                            </p>",

        'intro24'    =>"    <h3>AHP Örnekleri</h3>
                            <p style='text-align:justify;'>
                            Yöntemi daha anlaşılır hale getirmek ve farklı karar hiyerarşileri için olası uygulama
                            alanlarını göstermek için <a href='ahp-examples.php' >örnekler</a> veriyoruz.
                            Yöntemin basitleştirilmiş bir tanıtımı <a href='docs/AHP-articel.Goepel.en.pdf' target='_blank'>burada</a> bulunabilir.
                            </p></div>"
    );

    public $tbl    = array(
        'grTblTh'    =>     "\n<thead><tr class='header'><th>Katılımcı</th>",
        'grTblTd1'   =>    "<td><strong>Grup sonucu</strong></td>"
    );
}
