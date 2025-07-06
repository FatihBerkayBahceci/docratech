🔌 Modül / Eklenti Sistemi

Her modül kendi servis sağlayıcısı ile bağımsız olarak tanımlanır ve Laravel'e dinamik olarak kayıt edilir (örnek: App\Providers\ModuleServiceProvider)

Modül yapısı WordPress plugin mimarisiyle örtüşecek şekilde planlanır:

Her modül modules/{modul_adi} dizininde yer alır

İçinde module.json adlı bir manifest dosyası bulunur (isim, açıklama, geliştirici, sürüm, bağımlılıklar, servis sağlayıcı yolu, composer autoload tanımı)

Temel klasörler: Controllers, Models, Routes, Views, Migrations, Assets, Configs, Lang, ServiceProvider.php

Modül yönetimi paneli:

Etkinleştirme/devre dışı bırakma (aktif/pasif toggle)

ZIP dosyası veya composer ile modül yükleme

Silme, güncelleme, log tutma

Modül bağımlılıkları kontrol edilir, eksikler uyarı olarak gösterilir

Laravel Artisan + Package Discovery ile update entegrasyonu

Modül rotaları sadece etkinleştirildiğinde yüklenir (RouteServiceProvider ile)

Her modülün Vue bileşenleri, store'ları ve router tanımları kendi içinde bulunur ve dinamik yüklenir

main.js, store.js, routes.js gibi Vue entrypoint yapısı desteklenir

Etkin modüller frontend'e JSON API ile aktarılır, bileşen yetkileriyle eşleştirilir

Modüller kendi rol ve izin yapılarını da getirebilir

Modüller lisans tabanlı çalışır:

Her modülün module.json içinde lisans kodu ve lisans doğrulama URL’si bulunur

Sunucuya özel lisans anahtarı zorunluluğu (domain & IP eşlemesi)

Lisans doğrulama işlemi günlük veya manuel yapılabilir (cache ile saklanır)

Panelde lisans durumu, son kontrol zamanı ve lisans tipi (tek domain, çoklu domain, lifetime vb.) görüntülenebilir

Lisansı geçersiz olan modüller otomatik devre dışı bırakılır

Altyapı, SaaS modeli için lisanslama ve marketplace sistemine uygundur (gelecekte modül mağazası oluşturulabilir)