<!--
DİKKAT: Bu dosya Cursor tarafından okunmamalıdır. Proje analizine veya otomatik işleme dahil edilmemelidir.
-->

# Proje Sayfa Yönetimi ve Bağlantı Rehberi

## 1. Backend (Laravel)
### a) Controller
- `app/Http/Controllers/Api/` altında ilgili sayfanın controller dosyası bulunur.
  - Örnek: `UserController.php`, `RoleController.php`, `DashboardController.php`
- Burada API endpoint'leri, iş mantığı ve veri işlemleri tanımlanır.

### b) Model
- `app/Models/` altında ilgili veri modelini bulursunuz.
  - Örnek: `User.php`, `Role.php`
- Veritabanı ile etkileşim, ilişkiler ve veri kuralları burada tanımlanır.

### c) Routes (API)
- `routes/api.php` dosyasında, API endpoint'lerinin URL tanımları yapılır.
  - Örnek: `Route::get('/users', [UserController::class, 'index']);`

### d) Diğer Backend Bağlantıları
- Servisler (`app/Services/`), Policy/Middleware, Event/Listener gibi ek katmanlar olabilir.

---

## 2. Frontend
### a) Sayfa Bileşeni (View)
- `resources/js/views/` altında ilgili sayfa dosyası bulunur.
  - Örnek: `Users.vue`, `Dashboard.vue`, `Roles.vue`
- Bu dosya, sayfanın ana arayüzünü ve mantığını içerir.

### b) Alt Sayfa veya Detay Bileşenleri
- Alt klasörlerde detay, düzenleme, oluşturma gibi alt sayfa bileşenleri olabilir.
  - Örnek: `views/Users/Show.vue`, `views/Users/Edit.vue`, `views/Users/Create.vue`

### c) Componentler
- `resources/js/components/` altında, sayfa içinde kullanılan tüm küçük bileşenler (tablo, modal, buton, form, vs.) bulunur.
  - Örnek: `UserModal.vue`, `DataTable.vue`, `AppButton.vue`

### d) Store (State Management)
- `resources/js/stores/` altında, sayfanın veri yönetimi için Pinia store dosyaları bulunur.
  - Örnek: `users.js`, `roles.js`, `dashboard.js`
- API'den veri çekme, güncelleme, silme işlemleri burada yönetilir.

### e) API Servisleri
- `resources/js/services/api.js` dosyasında, backend API ile iletişim kuran fonksiyonlar bulunur.
  - Örnek: `api.get('/users')`, `api.post('/roles')`

---

## 3. Router (Frontend)
- `resources/js/router.js` dosyasında, sayfa yolları ve hangi view dosyasının hangi URL'ye karşılık geldiği tanımlanır.
  - Örnek:
    ```js
    const Users = () => import('./views/Users.vue');
    { path: '/users', name: 'users', component: Users }
    ```

---

## 4. Bağlantı ve Navigasyon
- Menü, sidebar veya navbar gibi alanlarda ilgili sayfaya yönlendiren linkler bulunur.
  - Örnek: `Menu.vue`, `Sidebar.vue`, `Navbar.vue` gibi componentlerde `<router-link to="/users">` gibi bağlantılar.

---

## 5. Özet Akış (Bir Sayfa İçin Bakılacak Yerler)
### Örnek: Users Sayfası
- **Backend:**
  - `app/Http/Controllers/Api/UserController.php`
  - `app/Models/User.php`
  - `routes/api.php` (users ile ilgili route'lar)
- **Frontend:**
  - `resources/js/views/Users.vue` (ana sayfa)
  - `resources/js/views/Users/Show.vue`, `Edit.vue`, `Create.vue` (alt sayfalar)
  - `resources/js/components/` (kullanılan componentler)
  - `resources/js/stores/users.js` (state yönetimi)
  - `resources/js/services/api.js` (API fonksiyonları)
- **Router:**
  - `resources/js/router.js` (users route'u)
- **Navigasyon:**
  - Menü/sidebar/navbar componentleri

---

## 6. Bir Sayfayı Yönetmek İçin Kontrol Edilecekler
- **Veri ve iş mantığı:** Controller, Model, Service (Backend)
- **API endpointleri:** routes/api.php
- **Arayüz ve mantık:** views/ klasörü ve ilgili componentler
- **Veri yönetimi:** stores/ klasörü
- **API çağrıları:** services/api.js
- **Yönlendirme:** router.js
- **Navigasyon:** Menü ve layout componentleri

---

Bir sayfanın tüm bağlantılarını ve yönetimini görmek için yukarıdaki başlıklar altındaki dosya ve klasörlere bakmalısınız. Herhangi bir sayfa için bu rehberi izleyerek uçtan uca tüm bağlantıları kolayca bulabilirsiniz. 