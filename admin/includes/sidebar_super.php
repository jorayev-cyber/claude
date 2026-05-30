            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/" class="nav-link <?= ($currentPage ?? '') == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/students.php" class="nav-link <?= ($currentPage ?? '') == 'students' ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>O'quvchilar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/groups.php" class="nav-link <?= ($currentPage ?? '') == 'groups' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Guruhlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/courses.php" class="nav-link <?= ($currentPage ?? '') == 'courses' ? 'active' : '' ?>">
                        <i class="fas fa-book-open"></i>
                        <span>Kurslar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/teachers.php" class="nav-link <?= ($currentPage ?? '') == 'teachers' ? 'active' : '' ?>">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>O'qituvchilar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/payments.php" class="nav-link <?= ($currentPage ?? '') == 'payments' ? 'active' : '' ?>">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>To'lovlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/expenses.php" class="nav-link <?= ($currentPage ?? '') == 'expenses' ? 'active' : '' ?>">
                        <i class="fas fa-receipt"></i>
                        <span>Xarajatlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/leads.php" class="nav-link <?= ($currentPage ?? '') == 'leads' ? 'active' : '' ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span>Lidlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/attendance.php" class="nav-link <?= ($currentPage ?? '') == 'attendance' ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Davomat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/users.php" class="nav-link <?= ($currentPage ?? '') == 'users' ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i>
                        <span>Xodimlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/reports.php" class="nav-link <?= ($currentPage ?? '') == 'reports' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Hisobotlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>super/settings.php" class="nav-link <?= ($currentPage ?? '') == 'settings' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        <span>Sozlamalar</span>
                    </a>
                </li>
            </ul>
