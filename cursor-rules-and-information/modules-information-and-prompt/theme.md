🎨 Tema & Şablon Yönetimi

Kullanıcıya özel tema seçim yeteneği (env → DB geçişli yapı, tenant başına bağımsız tema seçimi)

Tema başlık yapısı, renk şeması, font ayarları (Google Fonts desteği dahil)

Tema modüllerini override etme mantığı (slot ve bölgesel Vue bileşeni sistemi)

WordPress benzeri tema klasör mantığı:

Her tema resources/themes/{tema-adi}/ içinde tanımlı

Tema manifest dosyası: theme.json (isim, sürüm, geliştirici, açıklama, slot tanımları, desteklenen alanlar)

Tema aktivasyon sistemi:

Tek tıkla etkinleştirme, devre dışı bırakma

Demo içerik yükleme butonu (isteğe bağlı örnek sayfa/blog yükleyebilir)

Tema silme, yedekleme ve taşıma desteği

Tema ayarlarının canlı önizleme ile yapılabilmesi

AMP uyumlu temalar ve mobil öncelikli varyant desteği

Tema ayarlarının export/import yapılabilmesi (tema profilleri)

Vue üzerinden aktif tema ve stil verilerinin JSON olarak çekilebilmesi (tema store ile reactive yapı)