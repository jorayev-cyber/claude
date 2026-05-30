        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="page-title animate__animated animate__fadeInLeft"><?= $pageTitle ?></h4>
            </div>
            
            <div class="topbar-right">
                <!-- Qidiruv -->
                <div class="search-box d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Qidirish..." id="globalSearch">
                </div>

                <!-- Dark Mode Toggle -->
                <button class="theme-toggle" onclick="toggleTheme()" title="Tema o'zgartirish">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- Bildirishnomalar -->
                <div class="dropdown notification-dropdown">
                    <button class="notification-btn" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-menu">
                        <div class="notification-header">
                            <h6>Bildirishnomalar</h6>
                            <?php if ($unreadCount > 0): ?>
                                <a href="#" onclick="markAllRead()">Barchasini o'qilgan deb belgilash</a>
                            <?php endif; ?>
                        </div>
                        <div class="notification-body">
                            <?php if (empty($unreadNotifications)): ?>
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>Yangi bildirishnoma yo'q</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($unreadNotifications as $notif): ?>
                                    <a href="<?= $notif['link'] ?: '#' ?>" class="notification-item">
                                        <div class="notif-icon bg-<?= $notif['type'] ?>">
                                            <i class="fas fa-<?= $notif['type'] == 'success' ? 'check' : ($notif['type'] == 'warning' ? 'exclamation' : 'info') ?>"></i>
                                        </div>
                                        <div class="notif-content">
                                            <p><?= clean($notif['title']) ?></p>
                                            <span><?= timeAgo($notif['created_at']) ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Profil -->
                <div class="dropdown profile-dropdown">
                    <button class="profile-btn" data-bs-toggle="dropdown">
                        <div class="avatar">
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= BASE_URL . $currentUser['avatar'] ?>" alt="">
                            <?php else: ?>
                                <span><?= strtoupper(substr($currentUser['full_name'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="profile-info d-none d-md-block">
                            <span class="name"><?= clean($currentUser['full_name']) ?></span>
                            <span class="role"><?= ucfirst(str_replace('_', ' ', $currentUser['role'])) ?></span>
                        </div>
                        <i class="fas fa-chevron-down d-none d-md-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end profile-menu">
                        <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Profil</a>
                        <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Sozlamalar</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>logout.php" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Chiqish
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content animate__animated animate__fadeIn">
            <?php
            $flash = getFlash();
            if ($flash):
            ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                    <i class="fas fa-<?= $flash['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
