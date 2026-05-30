/**
 * EduCRM Admin Panel - JavaScript
 * Minimalist & Creative
 */

// Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// Dark/Light Theme Toggle
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    const icon = document.querySelector('.theme-toggle i');
    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    
    const icon = document.querySelector('.theme-toggle i');
    if (icon) icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';

    // Animate counters
    animateCounters();

    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            language: {
                search: "Qidirish:",
                lengthMenu: "_MENU_ ta ko'rsatish",
                info: "_TOTAL_ dan _START_ - _END_",
                paginate: { previous: "←", next: "→" },
                zeroRecords: "Ma'lumot topilmadi",
                emptyTable: "Jadval bo'sh"
            },
            pageLength: 15,
            responsive: true
        });
    }
});

// Counter Animation
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = formatNumber(target);
                clearInterval(timer);
            } else {
                counter.textContent = formatNumber(Math.floor(current));
            }
        }, 16);
    });
}

// Format number with spaces
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

// Delete confirmation
function confirmDelete(url, name) {
    Swal.fire({
        title: "O'chirishni tasdiqlang",
        text: `"${name}" ni o'chirmoqchimisiz?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: "Ha, o'chirish",
        cancelButtonText: 'Bekor qilish',
        customClass: { popup: 'animate__animated animate__fadeInDown' }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Toast notification
function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'animate__animated animate__fadeInRight' }
    });
}

// Mark all notifications as read
function markAllRead() {
    fetch('ajax/notifications.php?action=mark_all_read', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-btn .badge').forEach(b => b.remove());
                showToast('success', "Barcha bildirishnomalar o'qildi");
            }
        });
}

// AJAX form submit
function submitForm(formId, callback) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            if (callback) callback(data);
        } else {
            showToast('error', data.message || 'Xatolik yuz berdi');
        }
    })
    .catch(err => {
        showToast('error', 'Server xatosi');
    });
}

// Close sidebar on mobile when clicking outside
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
        if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle-btn')) {
            sidebar.classList.remove('active');
        }
    }
});
