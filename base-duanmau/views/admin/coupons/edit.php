<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Sửa Mã Giảm Giá</h3>
        <a href="index.php?class=admin&act=coupons" class="btn btn-secondary">Quay lại</a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class='bx bx-check-circle me-2'></i>
            <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class='bx bx-error-circle me-2'></i>
            <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?class=admin&act=edit_coupon&id=<?php echo $coupon['coupon_id']; ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mã Code (*)</label>
                            <input type="text" name="code" class="form-control text-uppercase" 
                                   value="<?php echo htmlspecialchars($coupon['code']); ?>" 
                                   placeholder="VD: SALE50, TET2025" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <input type="text" name="description" class="form-control" 
                                   value="<?php echo htmlspecialchars($coupon['description']); ?>"
                                   placeholder="VD: Giảm giá 50% nhân dịp Tết">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại giảm giá (*)</label>
                                <select name="discount_type" class="form-select" id="discount_type" onchange="toggleMaxDiscount()">
                                    <option value="FIXED" <?php echo $coupon['discount_type'] == 'FIXED' ? 'selected' : ''; ?>>Số tiền cố định (VNĐ)</option>
                                    <option value="PERCENT" <?php echo $coupon['discount_type'] == 'PERCENT' ? 'selected' : ''; ?>>Phần trăm (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá trị giảm (*)</label>
                                <input type="number" name="discount_value" class="form-control" 
                                       value="<?php echo $coupon['discount_value']; ?>" required min="0">
                                <div class="form-text">Nhập số tiền hoặc số %</div>
                            </div>
                        </div>
                        <div class="mb-3" id="max_discount_div" style="display: <?php echo $coupon['discount_type'] == 'PERCENT' ? 'block' : 'none'; ?>;">
                            <label class="form-label">Giảm tối đa (VNĐ)</label>
                            <input type="number" name="max_discount_value" class="form-control" 
                                   value="<?php echo $coupon['max_discount_value'] ?? 0; ?>">
                            <div class="form-text">Chỉ áp dụng cho loại Phần trăm (Nhập 0 nếu không giới hạn)</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" name="min_order_amount" class="form-control" 
                                   value="<?php echo $coupon['min_order_amount'] ?? 0; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới hạn số lần dùng</label>
                            <input type="number" name="usage_limit" class="form-control" 
                                   value="<?php echo $coupon['usage_limit']; ?>"
                                   placeholder="Để trống nếu không giới hạn">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày bắt đầu (*)</label>
                                <input type="datetime-local" name="start_date" class="form-control" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($coupon['start_date'])); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày kết thúc (*)</label>
                                <input type="datetime-local" name="end_date" class="form-control" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($coupon['end_date'])); ?>" required>
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" 
                                   <?php echo $coupon['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="isActive">Kích hoạt</label>
                        </div>
                        
                        <!-- Hiển thị thông tin sử dụng -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">Thông tin sử dụng:</h6>
                            <small class="text-muted">
                                Đã sử dụng: <strong><?php echo $coupon['used_count'] ?? 0; ?></strong> lần
                                <?php if ($coupon['usage_limit']): ?>
                                    / <?php echo $coupon['usage_limit']; ?> lần
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Cập nhật mã giảm giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script để ẩn/hiện ô "Giảm tối đa" khi chọn loại giảm giá
    function toggleMaxDiscount() {
        var type = document.getElementById('discount_type').value;
        var maxDiv = document.getElementById('max_discount_div');
        if (type === 'PERCENT') {
            maxDiv.style.display = 'block';
        } else {
            maxDiv.style.display = 'none';
        }
    }
    // Chạy 1 lần khi load trang
    toggleMaxDiscount();
</script>

<?php include_once './views/admin/footer.php'; ?>