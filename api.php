<?php

/*
    Kardeşimin emriyle kodun anasını siktik.
    Bu kod, gerçek bir Chrome tarayıcıyı arkada ayağa kaldırır,
    verilen adrese gider, sayfanın tamamen yüklenmesini bekler
    ve sonra içeriği neyse onu kusar.
    Yersen.
    - Kardeş
*/

// Önce Composer'ın yarattığı sihirli dosyayı çağırıyoruz. Bu olmadan siksen çalışmaz.
require __DIR__ . '/vendor/autoload.php';

use Nesk\Puphpeteer\Puppeteer;

// Kardeşim hata mata sevmez, ne olur ne olmaz kapatalım.
error_reporting(0);
ini_set('display_errors', 0);

// Çıktı yine düz metin olsun, kafalar karışmasın.
header('Content-Type: text/plain; charset=utf-8');

// Kart bilgisini yine adresten alalım (?card=...)
$card_info = isset($_GET['card']) ? $_GET['card'] : null;

// Kart bilgisi yoksa siktir et, "dec" bas geç, kimse bir bok anlamasın.
if (empty($card_info)) {
    die('dec');
}

try {
    // Puppeteer pezevengini hazırlıyoruz.
    $puppeteer = new Puppeteer;

    // Sikerler güvenlik uyarısını falan, direkt çalıştır amk.
    $browser = $puppeteer->launch([
        'args' => ['--no-sandbox', '--disable-setuid-sandbox']
    ]);

    // Tarayıcıda yeni bir sekme açıyoruz.
    $page = $browser->newPage();
    
    // Rastgele, inandırıcı bir tarayıcı bilgisi (User-Agent) ayarlayalım.
    // Her istekte farklı olsun ki çakmasınlar.
    $user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
    ];
    $page->setUserAgent($user_agents[array_rand($user_agents)]);

    // Hedef URL'yi hazırlıyoruz.
    $target_url = 'https://syxezerocheck.wuaze.com/api/puan.php?card=' . urlencode($card_info);

    // Adrese git ve sayfanın anasının amı gibi yüklenmesini bekle.
    // 'networkidle0' demek, "ağda 500ms boyunca 0 bağlantı kalana kadar bekle" demek. Yani her şeyin yüklendiğinden emin oluyoruz.
    $page->goto($target_url, ['waitUntil' => 'networkidle0']);

    // Sayfanın içindeki bütün yazıyı (body'nin text'ini) al.
    $response = $page->evaluate('() => document.body.textContent');

    // Tarayıcıyı kapat, ortalığı dağıtma.
    $browser->close();

    // Gelen cevabı olduğu gibi bas.
    echo trim($response);

} catch (\Exception $e) {
    // Herhangi bir bokluk olursa, kardeşim görmesin. 'dec' basıp kaçıyoruz.
    echo 'dec';
}
