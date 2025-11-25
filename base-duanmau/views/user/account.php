<div class="container my-5">
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-primary btn-sm">
            <i class="bx bx-arrow-back"></i> Trang chủ
        </a>
    </div>

    <h1 class="mb-4 text-primary fw-bold">Quản lý Tài khoản</h1>

    <?php if (isset($_SESSION['account_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['account_success'];
            unset($_SESSION['account_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['account_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['account_error'];
            unset($_SESSION['account_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group account-menu sticky-top" style="top: 100px; z-index: 1;">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#profile">
                    <i class='bx bx-user me-2'></i>Thông tin Cá nhân
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#orders">
                    <i class='bx bx-shopping-bag me-2'></i>Lịch sử Đơn hàng
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#settings">
                    <i class='bx bx-shield-quarter me-2'></i>Bảo mật
                </a>
                <a class="list-group-item list-group-item-action text-danger" href="index.php?class=login&act=logout">
                    <i class='bx bx-log-out me-2'></i>Đăng xuất
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">

                <div class="tab-pane fade show active" id="profile">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">Hồ sơ của tôi</h4>
                            <form action="index.php?class=account&act=update_profile" method="POST">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Họ và Tên</label>
                                    <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Email</label>
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    <small class="text-muted">Email không thể thay đổi.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Nhập số điện thoại">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted">Địa chỉ giao hàng</label>
                                    <textarea class="form-control" name="address" rows="2" placeholder="Nhập địa chỉ của bạn"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="orders">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">Đơn hàng gần đây</h4>
                            <?php if (empty($orders)): ?>
                                <div class="text-center py-5">
                                    <i class='bx bx-cart-alt fs-1 text-muted'></i>
                                    <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào.</p>
                                    <a href="index.php" class="btn btn-outline-primary">Mua sắm ngay</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mã ĐH</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Xem thêm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td class="fw-bold text-primary">#<?php echo $order['order_id']; ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                    <td class="fw-bold text-danger"><?php echo number_format($order['total_amount']); ?> đ</td>
                                                    <td>
                                                        <?php
                                                        $statusMap = [
                                                            'PENDING' => ['warning', 'Chờ xử lý'],
                                                            'PREPARING' => ['info', 'Đang chuẩn bị'],
                                                            'SHIPPING' => ['primary', 'Đang giao'],
                                                            'DELIVERED' => ['success', 'Đã giao'],
                                                            'CANCELLED' => ['danger', 'Đã hủy']
                                                        ];
                                                        $st = $statusMap[$order['order_status']] ?? ['secondary', $order['order_status']];
                                                        ?>
                                                        <span class="badge bg-<?php echo $st[0]; ?>"><?php echo $st[1]; ?></span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="viewOrderDetail(<?php echo $order['order_id']; ?>)">
                                                            <i class='bx bx-detail'></i> Chi tiết
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="settings">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">Đổi mật khẩu</h4>
                            <form action="index.php?class=account&act=change_password" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" name="oldPass" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="newPass" minlength="6" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" name="confirmPass" required>
                                </div>
                                <button type="submit" class="btn btn-warning text-white">Cập nhật mật khẩu</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Chi tiết đơn hàng #<span id="modal-order-id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-order-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewOrderDetail(orderId) {
        // 1. Cập nhật ID lên tiêu đề Modal
        document.getElementById('modal-order-id').innerText = orderId;

        // 2. Hiện Modal
        var myModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        myModal.show();

        // 3. Gọi AJAX lấy dữ liệu
        const contentDiv = document.getElementById('modal-order-content');
        contentDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';

        fetch(`index.php?class=account&act=get_order_detail_html&id=${orderId}`)
            .then(response => response.text())
            .then(html => {
                contentDiv.innerHTML = html;
            })
            .catch(err => {
                contentDiv.innerHTML = '<p class="text-danger text-center">Lỗi tải dữ liệu.</p>';
                console.error(err);
            });
    }
</script>