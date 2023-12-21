<?php
/* AhpDb.php için Metinler */

class AhpDbTR
{
    public $titles = array(
        'h3pDat'    =>   "<h3>Proje Verileri</h3>",
        'h3pPart'   =>   "<h3>Proje Katılımcıları</h3>\n",
        'h3pAlt'    =>   "<h3>Proje Alternatifleri</h3>"
    );

    public $err = array(
        'dbType'    =>   "Böyle bir SQL Veritabanı türü yok: ",
        'scInv'     =>   "Geçersiz Oturum Kodu ",
        'scInUse'   =>   "Oturum kodu kullanılıyor ",
        'dbWrite'   =>   "Veritabanına veri yazılamadı. Lütfen daha sonra tekrar deneyin.",
        'dbWriteA'  =>   "Veritabanı hatası, alternatifler saklanamadı ",
        'dbUpd'     =>   "Veri güncellenemedi. Lütfen daha sonra tekrar deneyin.",
        'dbSubmit'  =>   "Veri zaten gönderildi ",
        'noSess'    =>   "Saklanmış oturum yok ",
        'dbReadSc'  =>   "Veritabanı hatası, şu veriler alınamıyor ",
        'pClosed'   =>   "Proje kapalı. Çiftli karşılaştırma girişi izin verilmez.",
        'pNoMod'    =>   "Projede katılımcılar olduğundan, hiyerarşi değiştirilemez."
    );

    public $msg = array(
        'noSess'    =>   "Saklanmış oturum yok"
    );

    public $tbl = array(
        'scTblTh'   =>   "<thead><tr>
                            <th>No</th>
                            <th>Oturum</th>
                            <th>Proje</th>
                            <th>Tür<sup>1</sup></th>
                            <th>Durum</th>
                            <th>Açıklama</th>
                            <th>Kat.<sup>2</sup></th>
                            <th>oluşturuldu</th></tr></thead>",
        'scTblFoot' =>   "<tfoot><tr><td colspan='8'>
                            <sup>1</sup> H: Öncelik değerlendirme hiyerarşisi, A: Alternatif değerlendirme, 
                            <sup>2</sup> Katılımcı sayısı</td>
                            </tr></tfoot>",
        'pdTblTh'   =>   "<thead><tr>
                            <th>Alan</th>
                            <th>İçerik</th></tr></thead>\n",
        'pdTblR1'   =>   "<tr><td>Oturum Kodu</td><td class='res'>%s</td></tr>\n",
        'pdTblR2'   =>   "<tr><td>Proje Adı</td><td class='res'>%s</td></tr>\n",
        'pdTblR3'   =>   "<tr><td>Açıklama </td><td class='res'>%s</td></tr>\n",
        'pdTblR4'   =>   "<tr><td>Yazar</td><td class='res'>%s</td></tr>\n",
        'pdTblR5'   =>   "<tr><td>Tarih</td><td class='res'>%s</td></tr>\n",
        'pdTblR6'   =>   "<tr><td>Durum</td><td class='res'>%s</td></tr>\n",
        'pdTblR7'   =>   "<tr><td>Tür</td><td class='res'>%s</td></tr>\n",
        'paTblTh'   =>   "<thead><tr>
                            <th>No</th>
                            <th>Alternatifler</th>
                            </tr></thead>\n",
        'ppTblTh'   =>   "<thead><tr>
                            <th>No</th>
                            <th>Seç</th>
                            <th>Adı</th>
                            <th>Tarih</th>
                            </tr></thead>\n",
        'ppTblLr1'  =>   "<tr><td colspan='4'><input id='sbm0' type='submit' name='pselect' value='Seçimi Yenile'>&nbsp;<small>
                            <input class='onclk0' type='checkbox' name='ptick' value='0' ",
        'ppTblLr2'  =>   ">&nbsp;hepsini seç&nbsp;<input class='onclk0' type='checkbox' name='ntick' value='0' ",
        'ppTblLr3'  =>   ">&nbsp;seçimleri kaldır</small></td></tr>",
        'ppTblFoot' =>   "<tfoot><tr><td colspan='4'>
                            <small>Hiçbiri seçilmezse, tümü dahil edilecektir.</small>
                            </td></tr></tfoot>"
    );

    public $info = array(
        'shPart'    =>   "<p><span class='var'>%g</span> katılımcı. 
                            <button class='toggle'>Göster/Gizle</button> hepsi.</p>"
    );
}
