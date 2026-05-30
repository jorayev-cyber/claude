<?php require_once __DIR__ . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCRM - O'quv Markaz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> EduCRM
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Bosh sahifa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#courses">Kurslar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Biz haqimizda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#teachers">O'qituvchilar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Aloqa</a></li>
                </ul>
                <a href="login.php" class="btn btn-light-custom ms-lg-3">
                    <i class="fas fa-sign-in-alt me-1"></i> Kirish
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">Kelajakni bugun <span class="gradient-text">boshlaymiz!</span></h1>
                    <p class="hero-subtitle">Zamonaviy ta'lim metodlari, tajribali o'qituvchilar va qulay sharoitda o'z bilimingizni oshiring</p>
                    <div class="hero-buttons">
                        <a href="#courses" class="btn btn-hero-primary">
                            <i class="fas fa-book-open me-2"></i>Kurslarni ko'rish
                        </a>
                        <a href="#contact" class="btn btn-hero-secondary">
                            <i class="fas fa-phone me-2"></i>Bog'lanish
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <strong>500+</strong>
                            <span>Bitiruvchilar</span>
                        </div>
                        <div class="stat-item">
                            <strong>20+</strong>
                            <span>Kurslar</span>
                        </div>
                        <div class="stat-item">
                            <strong>98%</strong>
                            <span>Mamnuniyat</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
                    <div class="hero-illustration">
                        <div class="hero-card card-1">
                            <i class="fas fa-code"></i>
                            <span>Web Dasturlash</span>
                        </div>
                        <div class="hero-card card-2">
                            <i class="fas fa-language"></i>
                            <span>Ingliz tili</span>
                        </div>
                        <div class="hero-card card-3">
                            <i class="fas fa-paint-brush"></i>
                            <span>Grafik Dizayn</span>
                        </div>
                        <div class="hero-card card-4">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Mobile Dev</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="section-padding" id="courses">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <span class="section-badge">Kurslar</span>
                <h2>Bizning kurslarimiz</h2>
                <p>Zamonaviy kasblarni o'rganing va kelajagingizni ta'minlang</p>
            </div>
            <div class="row g-4">
                <?php
                $courses = $db->fetchAll("SELECT * FROM courses WHERE status = 'active' ORDER BY created_at DESC LIMIT 6");
                foreach ($courses as $i => $course):
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h4><?= clean($course['name']) ?></h4>
                        <p><?= clean(substr($course['description'] ?? 'Zamonaviy dastur va amaliy loyihalar bilan', 0, 100)) ?></p>
                        <div class="course-meta">
                            <span><i class="fas fa-clock"></i> <?= $course['duration_months'] ?> oy</span>
                            <span><i class="fas fa-signal"></i> <?= ucfirst($course['level']) ?></span>
                        </div>
                        <div class="course-price">
                            <?php if ($course['discount_price']): ?>
                                <span class="old-price"><?= formatMoney($course['price']) ?></span>
                                <span class="new-price"><?= formatMoney($course['discount_price']) ?></span>
                            <?php else: ?>
                                <span class="new-price"><?= formatMoney($course['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="#contact" class="btn btn-course">Ro'yxatdan o'tish</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Kurslar tez orada qo'shiladi</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section-padding bg-light-custom" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image">
                        <div class="about-shape"></div>
                        <div class="about-content-box">
                            <h3>5+ yillik</h3>
                            <p>tajriba</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="section-badge">Biz haqimizda</span>
                    <h2>Nima uchun bizni tanlashadi?</h2>
                    <div class="about-features">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <h5>Tajribali o'qituvchilar</h5>
                                <p>5+ yillik amaliy tajribaga ega mutaxassislar</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <h5>Amaliy loyihalar</h5>
                                <p>Real loyihalar ustida ishlash tajribasi</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <h5>Ish bilan ta'minlash</h5>
                                <p>Bitiruvchilarni ishga joylashtirishda yordam</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <h5>Sertifikat</h5>
                                <p>Kurs yakunida xalqaro sertifikat beriladi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section-padding" id="contact">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <span class="section-badge">Aloqa</span>
                <h2>Biz bilan bog'laning</h2>
                <p>Savollaringiz bormi? Biz bilan bog'laning!</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <h5>Telefon</h5>
                        <p>+998 90 123 45 67</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <h5>Email</h5>
                        <p>info@educrm.uz</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <h5>Manzil</h5>
                        <p>Toshkent sh, Chilonzor tumani</p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="contact-form-card">
                        <form action="pages/submit_lead.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="full_name" class="form-control" placeholder="Ismingiz" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" name="phone" class="form-control" placeholder="Telefon raqamingiz" required>
                                </div>
                                <div class="col-12">
                                    <select name="course_interest" class="form-select">
                                        <option value="">Qaysi kurs qiziqtiradi?</option>
                                        <?php 
                                        $allCourses = $db->fetchAll("SELECT id, name FROM courses WHERE status = 'active'");
                                        foreach ($allCourses as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= clean($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Xabaringiz..."></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-hero-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Yuborish
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4><i class="fas fa-graduation-cap me-2"></i>EduCRM</h4>
                    <p>Zamonaviy ta'lim va kelajak uchun bilim!</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Havolalar</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Bosh sahifa</a></li>
                        <li><a href="#courses">Kurslar</a></li>
                        <li><a href="#about">Biz haqimizda</a></li>
                        <li><a href="#contact">Aloqa</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Aloqa</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone me-2"></i>+998 90 123 45 67</li>
                        <li><i class="fas fa-envelope me-2"></i>info@educrm.uz</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Toshkent, Chilonzor</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> EduCRM. Barcha huquqlar himoyalangan.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
