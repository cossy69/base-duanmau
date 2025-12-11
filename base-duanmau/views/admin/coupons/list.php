<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="color: var(--primary-color); font-weight: 700;">Quản lý Mã giảm giá</h1>
        <a href="index.php?class=admin&act=add_coupon" class="btn btn-primary">
            <i class='bx bx-plus'></i> Thêm mã mới
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Code</th>
                            <th>Loại</th>
                            <th>Giá trị giảm</th>
                            <th>Đơn tối thiểu</th>
                            <th>Hạn sử dụng</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coupons)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Chưa có mã giảm giá nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coupons as $c): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary"><?php echo htmlspecialchars($c['code']); ?></span>
                                        <div class="small text-muted"><?php echo htmlspecialchars($c['description']); ?></div>
                                    </td>
                                    <td>
                                        <?php echo ($c['discount_type'] == 'PERCENT') ? 'Phần trăm (%)' : 'Số tiền cố định'; ?>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        <?php
                                        if ($c['discount_type'] == 'PERCENT') echo number_format($c['discount_value']) . '%';
                                        else echo number_format($c['discount_value']) . ' đ';
                                        ?>
                                    </td>
                                    <td><?php echo number_format($c['min_order_amount']); ?> đ</td>
                                    <td>
                                        <small>
                                            Từ: <?php echo date('d/m/Y', strtotime($c['start_date'])); ?><br>
                                            Đến: <?php echo date('d/m/Y', strtotime($c['end_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($c['is_active'] && strtotime($c['end_date']) >= time()): ?>
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        <?php elseif (!$c['is_active']): ?>
                                            <span class="badge bg-secondary">Đã tắt</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Hết hạn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="index.php?class=admin&act=delete_coupon&id=<?php echo $c['coupon_id']; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Xóa mã này?');">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once './views/admin/footer.php'; ?>