# 🎨 Docratech Admin Panel UI Kit - Bileşen Rehberi

## 📋 Genel Bakış

Bu dokümantasyon, Docratech Medical Website Platform için oluşturulan kapsamlı UI bileşen kitini içerir. Tüm bileşenler marka kimliğine uygun, animasyonlu, responsive ve erişilebilir şekilde tasarlanmıştır.

## 🎯 Tasarım Prensipleri

- **Marka Uyumlu**: Tüm bileşenler marka kiti renklerini kullanır
- **Animasyonlu**: Smooth geçişler ve hover efektleri
- **Responsive**: Mobil-first tasarım yaklaşımı
- **Erişilebilir**: ARIA etiketleri ve klavye navigasyonu
- **Dark Mode**: Tüm bileşenler dark mode destekli
- **Atomik**: Yeniden kullanılabilir ve modüler yapı
- **Vue 3**: Composition API kullanımı

---

## 📦 Mevcut Bileşenler

### 🎨 **Görsel Bileşenler (Visual Components)**

#### Layout & Navigation
- **`Sidebar.vue`** - Yan menü, katlanabilir, responsive
- **`Navbar.vue`** - Üst navigasyon, bildirimler, kullanıcı menüsü
- **`Footer.vue`** - Sayfa altı, marka ve sosyal medya
- **`MobileMenu.vue`** - Mobil açılır menü
- **`Drawer.vue`** - Yan panel, ayarlar veya detaylar için
- **`Menu.vue`** - Dikey veya açılır menü

#### Interactive Elements
- **`Icon.vue`** - Marka ikonları sistemi
- **`Button.vue`** - Tüm varyantlarıyla buton bileşeni
- **`Tag.vue`** - Etiketler
- **`Alert.vue`** - Uyarı kutuları
- **`Tooltip.vue`** - Bilgi balonu
- **`Popover.vue`** - Küçük açılır bilgi kutuları
- **`Badge.vue`** - Durum göstergeleri
- **`Chip.vue`** - Etiket/filtre chip'leri

#### Data Display
- **`Avatar.vue`** - Kullanıcı avatarı
- **`Image.vue`** - Resim gösterici
- **`Divider.vue`** - Bölücü çizgi
- **`Timeline.vue`** - Zaman çizelgesi
- **`TimelineItem.vue`** - Zaman çizelgesi öğesi
- **`Accordion.vue`** - Açılır kapanır bölümler
- **`AccordionItem.vue`** - Accordion öğesi
- **`Stepper.vue`** - Adım adım süreçler için
- **`Calendar.vue`** - Takvim, etkinlikler için
- **`DatePicker.vue`** - Tarih seçici
- **`TimePicker.vue`** - Saat seçici
- **`ColorPicker.vue`** - Renk seçici
- **`Slider.vue`** - Değer aralığı için slider
- **`Rating.vue`** - Puanlama için yıldızlar

### 🏗️ **Layout Bileşenleri (Layout Components)**

#### Main Layout
- **`AppLayout.vue`** - Ana layout (Sidebar + Navbar + Footer)
- **`MainContainer.vue`** - Sayfa ana kapsayıcısı
- **`Container.vue`** - Ana kapsayıcı
- **`Grid.vue`** - Responsive grid sistemi
- **`Flex.vue`** - Flexbox layout
- **`Divider.vue`** - Bölücü çizgi

#### Content Sections
- **`AppSection.vue`** - Bölüm başlıkları ve açıklamaları
- **`AppCard.vue`** - Kutulu içerik
- **`PageHeader.vue`** - Sayfa başlığı
- **`EmptyState.vue`** - Boş durum gösterimi

### 📝 **Form Bileşenleri (Form Components)**

#### Input Elements
- **`InputText.vue`** - Metin girişi
- **`Textarea.vue`** - Çok satırlı metin girişi
- **`NumberInput.vue`** - Sayı girişi
- **`SearchInput.vue`** - Arama girişi
- **`FileInput.vue`** - Dosya yükleme
- **`Select.vue`** - Açılır liste seçimi
- **`Checkbox.vue`** - Onay kutusu
- **`Radio.vue`** - Radyo buton
- **`SwitchToggle.vue`** - Açma/kapama düğmesi
- **`FormGroup.vue`** - Form grubu

#### Advanced Form Elements
- **`DateRangePicker.vue`** - Tarih aralığı seçici
- **`TimeRangePicker.vue`** - Saat aralığı seçici
- **`ColorSwatch.vue`** - Renk seçici için örnekler

### 🧭 **Navigasyon Bileşenleri (Navigation Components)**

- **`Breadcrumb.vue`** - Navigasyon breadcrumb
- **`Tabs.vue`** - Sekme navigasyonu
- **`Pagination.vue`** - Sayfalama
- **`LanguageSwitcher.vue`** - Dil seçici
- **`UserDropdown.vue`** - Kullanıcı menüsü

### 🔔 **Bildirim & Modal Bileşenleri (Notification & Modal Components)**

#### Notifications
- **`ToastContainer.vue`** - Bildirim sistemi
- **`ToastNotification.vue`** - Toast bildirimi
- **`SystemStatus.vue`** - Sistem durumu göstergesi

#### Modals & Dialogs
- **`AppDialog.vue`** - Modal dialog
- **`AppConfirmDialog.vue`** - Onay gerektiren işlemler için modal
- **`AppOverlay.vue`** - Arka planı karartan overlay
- **`SettingsPanel.vue`** - Kayan ayarlar paneli

### 📊 **Veri Görselleştirme (Data Visualization)**

#### Tables & Lists
- **`DataTable.vue`** - Gelişmiş tablo
- **`AppList.vue`** - Sıralı veya madde işaretli liste
- **`VirtualList.vue`** - Büyük veri listeleri için sanal liste

#### Charts & Progress
- **`Chart.vue`** - Grafik
- **`ProgressBar.vue`** - Yatay ilerleme çubuğu
- **`ProgressCircle.vue`** - Daire şeklinde ilerleme göstergesi

#### Statistics
- **`StatsCard.vue`** - İstatistik kutuları
- **`AppStats.vue`** - İstatistik kutuları, Dashboard için

### ⚡ **Akıllı Bileşenler (Smart Components)**

- **`QuickActions.vue`** - Hızlı erişim butonları
- **`InfiniteScroll.vue`** - Sonsuz kaydırma
- **`FilterBar.vue`** - Filtre çubuğu

### 🎭 **Durum Bileşenleri (State Components)**

- **`EmptyState.vue`** - Boş veri durumları için
- **`ErrorState.vue`** - Hata durumları için
- **`SuccessState.vue`** - Başarı durumları için
- **`LoadingSpinner.vue`** - Yükleme spinner'ı
- **`Skeleton.vue`** - Yükleme iskeleti

### 🔐 **Kimlik Doğrulama (Authentication Components)**

- **`LoginForm.vue`** - Giriş formu
- **`UserProfile.vue`** - Kullanıcı profili

### 🎨 **Tema Bileşenleri (Theme Components)**

- **`ThemeProvider.vue`** - Tema yönetimi
- **`ThemeToggle.vue`** - Tema değiştirici

### 🎯 **Özel Bileşenler (Specialized Components)**

#### Admin Specific
- **`RolesManager.vue`** - Rol yönetimi
- **`RoleModal.vue`** - Rol modal'ı
- **`RoleViewModal.vue`** - Rol görüntüleme modal'ı
- **`RoleCard.vue`** - Rol kartı
- **`Permissions.vue`** - İzinler
- **`UserCard.vue`** - Kullanıcı kartı
- **`UserModal.vue`** - Kullanıcı modal'ı
- **`Users.vue`** - Kullanıcılar listesi

---

## ❌ Eksik Bileşenler (İleri Düzey)

### 🔧 **Gelişmiş Form Bileşenleri**
- **`MultiSelect.vue`** - Çoklu seçim
- **`Autocomplete.vue`** - Otomatik tamamlama
- **`RichTextEditor.vue`** - Zengin metin editörü
- **`FileUpload.vue`** - Gelişmiş dosya yükleme
- **`ImageUpload.vue`** - Resim yükleme ve kırpma
- **`SignaturePad.vue`** - İmza pad'i
- **`CodeEditor.vue`** - Kod editörü

### 📊 **Gelişmiş Veri Görselleştirme**
- **`DataGrid.vue`** - Gelişmiş veri tablosu
- **`KanbanBoard.vue`** - Kanban tahtası
- **`GanttChart.vue`** - Gantt şeması
- **`Heatmap.vue`** - Isı haritası
- **`TreeView.vue`** - Ağaç görünümü
- **`OrgChart.vue`** - Organizasyon şeması

### 🎮 **İnteraktif Bileşenler**
- **`DragDrop.vue`** - Sürükle bırak
- **`SortableList.vue`** - Sıralanabilir liste
- **`ResizablePanel.vue`** - Yeniden boyutlandırılabilir panel
- **`SplitPane.vue`** - Bölünmüş panel
- **`ContextMenu.vue`** - Sağ tık menüsü
- **`CommandPalette.vue`** - Komut paleti

### 📱 **Mobil Özel Bileşenler**
- **`BottomSheet.vue`** - Alt sayfa
- **`PullToRefresh.vue`** - Aşağı çekerek yenileme
- **`SwipeableCard.vue`** - Kaydırılabilir kart
- **`TouchSlider.vue`** - Dokunmatik slider

### 🔍 **Gelişmiş Arama & Filtre**
- **`AdvancedSearch.vue`** - Gelişmiş arama
- **`FilterPanel.vue`** - Filtre paneli
- **`SearchSuggestions.vue`** - Arama önerileri
- **`SavedSearches.vue`** - Kaydedilmiş aramalar

### 📈 **Dashboard Bileşenleri**
- **`MetricCard.vue`** - Metrik kartı
- **`TrendChart.vue`** - Trend grafiği
- **`ActivityFeed.vue`** - Aktivite akışı
- **`NotificationCenter.vue`** - Bildirim merkezi
- **`QuickStats.vue`** - Hızlı istatistikler

### 🎨 **Gelişmiş UI Bileşenleri**
- **`Carousel.vue`** - Resim galerisi
- **`Lightbox.vue`** - Resim görüntüleyici
- **`Tour.vue`** - Kullanıcı turu
- **`Onboarding.vue`** - İlk kullanım rehberi
- **`Feedback.vue`** - Geri bildirim formu
- **`RatingModal.vue`** - Puanlama modal'ı

### 🔧 **Gelişmiş Layout**
- **`StickyHeader.vue`** - Yapışkan başlık
- **`StickyFooter.vue`** - Yapışkan alt bilgi
- **`CollapsiblePanel.vue`** - Katlanabilir panel
- **`ResizableLayout.vue`** - Yeniden boyutlandırılabilir layout
- **`MultiColumnLayout.vue`** - Çok sütunlu layout

### 📋 **Gelişmiş Liste Bileşenleri**
- **`VirtualTable.vue`** - Sanal tablo
- **`GroupedList.vue`** - Gruplandırılmış liste
- **`SelectableList.vue`** - Seçilebilir liste
- **`ExpandableList.vue`** - Genişletilebilir liste
- **`NestedList.vue`** - İç içe liste

---

## 🚀 Kullanım Örnekleri

### Ana Layout
```vue
<AppLayout>
  <template #sidebar>
    <Sidebar :collapsed="sidebarCollapsed">
      <!-- Sidebar içeriği -->
    </Sidebar>
  </template>
  
  <template #default>
    <AppSection title="Dashboard" description="Genel bakış">
      <QuickActions :actions="quickActions" />
      <StatsCard :stats="stats" />
    </AppSection>
  </template>
</AppLayout>
```

### Form Örneği
```vue
<AppCard title="Kullanıcı Ekle" variant="elevated">
  <FormGroup label="Ad Soyad">
    <InputText v-model="user.name" placeholder="Ad soyad girin" />
  </FormGroup>
  
  <FormGroup label="E-posta">
    <InputText v-model="user.email" type="email" />
  </FormGroup>
  
  <FormGroup label="Rol">
    <Select v-model="user.role" :options="roles" />
  </FormGroup>
  
  <template #footer>
    <AppButton variant="primary" @click="saveUser">Kaydet</AppButton>
  </template>
</AppCard>
```

### Bildirim Örneği
```vue
<ToastContainer position="top-right" />
<!-- Kullanım -->
this.$toast.success('Kullanıcı başarıyla eklendi!')
```

---

## 📝 Geliştirici Notları

### Bileşen İsimlendirme Kuralları
- Tüm bileşenler `PascalCase` ile isimlendirilir
- Dosya isimleri bileşen isimleriyle aynı olmalı
- Klasör yapısı bileşen kategorilerine göre organize edilir

### Props ve Events
- Tüm bileşenler `v-model` desteği sağlar
- Props için TypeScript tip tanımları kullanılır
- Events için `emit` kullanılır

### Styling
- TailwindCSS utility sınıfları kullanılır
- CSS custom properties ile tema desteği
- Dark mode için `.dark` sınıfı kullanılır

### Erişilebilirlik
- ARIA etiketleri eklenir
- Klavye navigasyonu desteklenir
- Screen reader uyumlu

---

## 🎯 Sonraki Adımlar

1. **Eksik bileşenlerin oluşturulması**
2. **Bileşen testlerinin yazılması**
3. **Storybook entegrasyonu**
4. **Tema sistemi geliştirmesi**
5. **Performans optimizasyonu**

---

## 📞 Destek

Bu UI kit sürekli geliştirilmektedir. Yeni bileşen önerileri veya iyileştirmeler için lütfen iletişime geçin.

---

**Not**: Tüm tasarımlar bu bileşenler üzerinden oluşturulacaktır. Tutarlılık ve kalite için mevcut bileşenleri kullanın. 