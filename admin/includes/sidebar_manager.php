            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/" class="nav-link <?= ($currentPage ?? '') == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/students.php" class="nav-link <?= ($currentPage ?? '') == 'students' ? 'active' : '' ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>O'quvchilar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/groups.php" class="nav-link <?= ($currentPage ?? '') == 'groups' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Guruhlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/payments.php" class="nav-link <?= ($currentPage ?? '') == 'payments' ? 'active' : '' ?>">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>To'lovlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/leads.php" class="nav-link <?= ($currentPage ?? '') == 'leads' ? 'active' : '' ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span>Lidlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ADMIN_URL ?>manager/attendance.php" class="nav-link <?= ($currentPage ?? '') == 'attendance' ? 'active' : '' ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Davomat</span>
                    </a>
                </li>
            </ul>
