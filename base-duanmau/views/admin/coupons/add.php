<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Thêm Mã Giảm Giá Mới</h3>
        <a href="index.php?class=admin&act=coupons" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?class=admin&act=add_coupon" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mã Code (*)</label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="VD: SALE50, TET2025" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <input type="text" name="description" class="form-control" placeholder="VD: Giảm giá 50% nhân dịp Tết">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại giảm giá (*)</label>
                                <select name="discount_type" class="form-select" id="discount_type" onchange="toggleMaxDiscount()">
                                    <option value="FIXED">Số tiền cố định (VNĐ)</option>
                                    <option value="PERCENT">Phần trăm (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá trị giảm (*)</label>
                                <input type="number" name="discount_value" class="form-control" required min="0">
                                <div class="form-text">Nhập số tiền hoặc số %</div>
                            </div>
                        </div>
                        <div class="mb-3" id="max_discount_div" style="display: none;">
                            <label class="form-label">Giảm tối đa (VNĐ)</label>
                            <input type="number" name="max_discount_value" class="form-control" value="0">
                            <div class="form-text">Chỉ áp dụng cho loại Phần trăm (Nhập 0 nếu không giới hạn)</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" name="min_order_amount" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới hạn số lần dùng</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="Để trống nếu không giới hạn">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày bắt đầu (*)</label>
                                <input type="datetime-local" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày kết thúc (*)</label>
                                <input type="datetime-local" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label" for="isActive">Kích hoạt ngay</label>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Lưu mã giảm giá</button>
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