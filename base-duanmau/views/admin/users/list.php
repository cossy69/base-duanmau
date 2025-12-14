<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">Quản lý Người dùng</h2>
        <div class="position-relative">
            <input type="text" id="searchUser" class="form-control ps-5" placeholder="Tìm tên hoặc email..." style="border-radius: 20px; min-width: 300px;">
            <i class='bx bx-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted'></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Người dùng</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php foreach ($users as $u): ?>
                            <?php
                            // Kiểm tra xem có phải là chính mình không (để ẩn nút sửa quyền)
                            $isSelf = ($u['user_id'] == $_SESSION['user_id']);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['full_name']); ?>&background=random&color=fff&size=128"
                                            class="rounded-circle me-3 shadow-sm" width="45" height="45" alt="Avatar">
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                            <small class="text-muted">ID: #<?php echo $u['user_id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-secondary"><?php echo $u['email']; ?></span></td>

                                <td>
                                    <?php 
                                    // Debug: hiển thị giá trị role thực tế
                                    $roleValue = $u['role'] ?? 'NULL';
                                    ?>
                                    <?php if ($u['role'] === 'super_admin'): ?>
                                        <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill px-3">
                                            <i class='bx bxs-star me-1'></i> Super Admin
                                        </span>
                                    <?php elseif ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                            <i class='bx bxs-crown me-1'></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3">
                                            Khách hàng (<?php echo htmlspecialchars($roleValue); ?>)
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($u['is_disabled']): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                            <i class='bx bxs-lock-alt me-1'></i> Đã khóa
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                            <i class='bx bxs-check-circle me-1'></i> Hoạt động
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end pe-4">
                                    <?php
                                    // Logic kiểm tra quyền dựa trên role thực tế từ database
                                    $currentUserId = $_SESSION['user_id'];
                                    $isSelf = ($u['user_id'] == $currentUserId);
                                    
                                    // Lấy role của user hiện tại từ database
                                    global $pdo;
                                    $currentUserStmt = $pdo->prepare("SELECT role FROM user WHERE user_id = ?");
                                    $currentUserStmt->execute([$currentUserId]);
                                    $currentUserRole = $currentUserStmt->fetchColumn();
                                    
                                    $isSuperAdmin = ($currentUserRole === 'super_admin');
                                    $targetIsSuperAdmin = ($u['role'] === 'super_admin');
                                    ?>

                                    <?php 
                                    // CHỈ SUPER ADMIN mới có quyền nâng/hạ quyền
                                    $canChangeRole = ($isSuperAdmin && !$isSelf && !$targetIsSuperAdmin);
                                    
                                    // Quyền khóa/mở khóa:
                                    // - Super Admin: khóa/mở tất cả (trừ chính mình và Super Admin khác)
                                    // - Admin: chỉ khóa/mở User thường
                                    $canToggleUser = false;
                                    if ($isSuperAdmin && !$isSelf && !$targetIsSuperAdmin) {
                                        $canToggleUser = true; // Super Admin khóa/mở tất cả
                                    } elseif ($currentUserRole === 'admin' && !$isSelf && $u['role'] === 'user') {
                                        $canToggleUser = true; // Admin chỉ khóa/mở User
                                    }
                                    ?>
                                    
                                    <?php if ($canChangeRole): ?>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <button class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="changeRole(this, <?php echo $u['user_id']; ?>, 0)"
                                                title="Hạ xuống Khách hàng">
                                                <i class='bx bx-user'></i> Xuống Khách
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-primary me-1"
                                                onclick="changeRole(this, <?php echo $u['user_id']; ?>, 1)"
                                                title="Thăng cấp lên Admin">
                                                <i class='bx bx-crown'></i> Lên Admin
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($canToggleUser): ?>
                                        <?php if ($u['is_disabled']): ?>
                                            <button class="btn btn-sm btn-success" onclick="toggleUser(this, <?php echo $u['user_id']; ?>, 0)" title="Mở khóa">Mở khóa</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="toggleUser(this, <?php echo $u['user_id']; ?>, 1)" title="Khóa">Khóa user</button>
                                        <?php endif; ?>
                                    <?php elseif ($isSelf): ?>
                                        <span class="text-muted small fst-italic">(Bạn)</span>
                                    <?php elseif ($targetIsSuperAdmin): ?>
                                        <span class="badge bg-warning text-dark"><i class='bx bxs-star'></i> Super Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. Hàm Khóa/Mở khóa (Giữ nguyên) ---
    function toggleUser(btn, id, status) {
        if (!confirm(status === 1 ? 'Chắc chắn KHÓA tài khoản này?' : 'Chắc chắn MỞ KHÓA?')) return;

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch('index.php?class=admin&act=toggle_user', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') location.reload();
                else {
                    alert('Lỗi!');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
    }

    // --- 2. Hàm Đổi Vai Trò (MỚI) ---
    function changeRole(btn, id, role) {
        const roleName = role === 1 ? 'ADMIN (Quản trị viên)' : 'KHÁCH HÀNG';
        if (!confirm(`Cảnh báo: Bạn muốn thay đổi quyền người dùng này thành ${roleName}?`)) return;

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('role', role);

        fetch('index.php?class=admin&act=update_user_role', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Lỗi cập nhật!');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(() => {
                alert('Lỗi kết nối');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

    // --- 3. Hàm Tìm kiếm ---
    document.getElementById('searchUser').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#userTableBody tr').forEach(row => {
            const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            row.style.display = (name.includes(value) || email.includes(value)) ? '' : 'none';
        });
    });
</script>

<?php include_once './views/admin/footer.php'; ?>