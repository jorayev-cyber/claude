# EduCRM - O'quv Markaz CRM Tizimi

## 📋 Loyiha haqida
To'liq funksional o'quv markaz boshqaruv tizimi (CRM). Frontend sayt + Admin panel lavozimga qarab.

## 🛠 Texnologiyalar
- **PHP** - Backend
- **MySQL** - Ma'lumot bazasi
- **Bootstrap 5.3** - UI Framework
- **JavaScript/jQuery** - Interaktivlik
- **Chart.js** - Grafiklar
- **DataTables** - Jadvallar
- **Animate.css + AOS** - Animatsiyalar
- **SweetAlert2** - Bildirishnomalar
- **Font Awesome 6** - Ikonlar

## 👥 Foydalanuvchi rollari

| Rol | Imkoniyatlar |
|-----|-------------|
| **Super Admin** | To'liq boshqaruv: o'quvchilar, kurslar, guruhlar, to'lovlar, xarajatlar, lidlar, xodimlar, hisobotlar, sozlamalar |
| **Manager** | O'quvchilar, guruhlar, to'lovlar, lidlar, davomat |
| **O'qituvchi** | O'z guruhlari, davomat belgilash, o'quvchilarni ko'rish |

## 🚀 O'rnatish

### 1. Ma'lumot bazasi
```bash
mysql -u root -p < database.sql
```

### 2. Konfiguratsiya
`config/database.php` faylida:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'edu_crm');
define('DB_USER', 'root');
define('DB_PASS', '');
```

`config/config.php` faylida:
```php
define('BASE_URL', 'http://localhost/claude/');
```

### 3. Kirish
- **URL:** http://localhost/claude/login.php
- **Email:** admin@educrm.uz
- **Parol:** admin123

## 📁 Loyiha Strukturasi
```
claude/
├── admin/
│   ├── super/         # Super Admin panel
│   ├── manager/       # Manager panel
│   ├── teacher/       # O'qituvchi panel
│   ├── includes/      # Header, footer, sidebar
│   └── assets/        # Admin CSS/JS
├── assets/            # Frontend CSS/JS/IMG
├── config/            # Konfiguratsiya
├── includes/          # PHP funksiyalar
├── pages/             # Frontend sahifalar
├── uploads/           # Yuklangan fayllar
├── index.php          # Bosh sahifa
├── login.php          # Kirish sahifasi
├── logout.php         # Chiqish
└── database.sql       # Ma'lumot bazasi
```

## ✨ Xususiyatlar
- 🎨 Minimalist va kreativ dizayn
- 🌙 Dark/Light mode
- 📱 To'liq responsive
- 🎬 Animatsiyali UI
- 📊 Interaktiv grafiklar
- 🔒 Xavfsiz autentifikatsiya
- 📋 DataTables bilan qidirish/saralash
- 🔔 Bildirishnoma tizimi
- 📈 Moliyaviy hisobotlar
