-- =============================================
-- O'QUV MARKAZ CRM - MA'LUMOT BAZASI
-- =============================================

CREATE DATABASE IF NOT EXISTS edu_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edu_crm;

-- =============================================
-- 1. FOYDALANUVCHILAR (Adminlar, Xodimlar)
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'manager', 'teacher', 'receptionist') NOT NULL DEFAULT 'teacher',
    avatar VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- 2. KURSLAR
-- =============================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    duration_months INT DEFAULT 0,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount_price DECIMAL(12,2) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- 3. GURUHLAR
-- =============================================
CREATE TABLE groups_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    schedule VARCHAR(255) DEFAULT NULL,
    time_slot VARCHAR(50) DEFAULT NULL,
    room VARCHAR(50) DEFAULT NULL,
    max_students INT DEFAULT 20,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- 4. O'QUVCHILAR (TALABALAR)
-- =============================================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    phone2 VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    gender ENUM('male', 'female') DEFAULT NULL,
    address TEXT DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    source ENUM('website', 'instagram', 'telegram', 'friend', 'other') DEFAULT 'other',
    notes TEXT DEFAULT NULL,
    status ENUM('active', 'graduated', 'dropped', 'frozen') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- 5. GURUH - O'QUVCHI BOG'LANISHI
-- =============================================
CREATE TABLE group_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_date DATE NOT NULL,
    status ENUM('active', 'completed', 'dropped', 'frozen') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups_table(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_student (group_id, student_id)
) ENGINE=InnoDB;

-- =============================================
-- 6. DAVOMAT
-- =============================================
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') NOT NULL DEFAULT 'present',
    note VARCHAR(255) DEFAULT NULL,
    marked_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups_table(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_attendance (group_id, student_id, date)
) ENGINE=InnoDB;

-- =============================================
-- 7. TO'LOVLAR
-- =============================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    group_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_type ENUM('cash', 'card', 'transfer', 'online') DEFAULT 'cash',
    payment_for VARCHAR(100) DEFAULT NULL,
    month_for DATE DEFAULT NULL,
    discount DECIMAL(12,2) DEFAULT 0,
    note TEXT DEFAULT NULL,
    received_by INT DEFAULT NULL,
    status ENUM('paid', 'pending', 'cancelled', 'refunded') DEFAULT 'paid',
    paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups_table(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- 8. XARAJATLAR
-- =============================================
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    category ENUM('salary', 'rent', 'utilities', 'supplies', 'marketing', 'other') DEFAULT 'other',
    description TEXT DEFAULT NULL,
    expense_date DATE NOT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- 9. LIDLAR (LEADS - POTENSIAL O'QUVCHILAR)
-- =============================================
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    course_interest INT DEFAULT NULL,
    source ENUM('website', 'instagram', 'telegram', 'facebook', 'friend', 'call', 'other') DEFAULT 'website',
    status ENUM('new', 'contacted', 'interested', 'enrolled', 'lost') DEFAULT 'new',
    assigned_to INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_interest) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- 10. XABARLAR / BILDIRISHNOMALAR
-- =============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- 11. TIZIM SOZLAMALARI
-- =============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT DEFAULT NULL,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- 12. FAOLIYAT LOGI
-- =============================================
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- BOSHLANG'ICH MA'LUMOTLAR
-- =============================================

-- Super Admin (parol: admin123)
INSERT INTO users (full_name, email, phone, password, role, status) VALUES
('Super Admin', 'admin@educrm.uz', '+998901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active');

-- Tizim sozlamalari
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'EduCRM - O\'quv Markaz', 'general'),
('site_logo', 'assets/img/logo.png', 'general'),
('site_email', 'info@educrm.uz', 'general'),
('site_phone', '+998 90 123 45 67', 'general'),
('site_address', 'Toshkent sh, Chilonzor tumani', 'general'),
('currency', 'UZS', 'finance'),
('timezone', 'Asia/Tashkent', 'general'),
('theme_color', '#6366f1', 'appearance'),
('dark_mode', '0', 'appearance');
