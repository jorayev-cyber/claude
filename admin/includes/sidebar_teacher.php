            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>teacher/" class="nav-link <?= ($currentPage ?? '') == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>teacher/groups.php" class="nav-link <?= ($currentPage ?? '') == 'groups' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Mening guruhlarim</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>teacher/attendance.php" class="nav-link <?= ($currentPage ?? '') == 'attendance' ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Davomat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>teacher/students.php" class="nav-link <?= ($currentPage ?? '') == 'students' ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>O'quvchilarim</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>teacher/schedule.php" class="nav-link <?= ($currentPage ?? '') == 'schedule' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Dars jadvali</span>
                    </a>
                </li>
            </ul>
