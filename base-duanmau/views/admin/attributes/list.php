<?php include_once './views/admin/header.php'; ?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Quản lý Thuộc tính (Màu, Size...)</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Thêm Thuộc tính mới</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tên thuộc tính</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Màu sắc, Dung lượng..." required>
                        </div>
                        <button type="submit" name="add_attribute" class="btn btn-primary w-100">Thêm mới</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php foreach ($attributes as $attr): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><?php echo htmlspecialchars($attr['name']); ?></h6>
                        <span class="badge bg-light text-dark">ID: <?php echo $attr['attribute_id']; ?></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php if (empty($attr['values'])): ?>
                                <small class="text-muted">Chưa có giá trị nào.</small>
                            <?php else: ?>
                                <?php foreach ($attr['values'] as $val): ?>
                                    <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($val['value']); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <form method="POST" class="row g-2 align-items-center">
                            <input type="hidden" name="attribute_id" value="<?php echo $attr['attribute_id']; ?>">
                            <div class="col-auto">
                                <input type="text" name="value" class="form-control form-control-sm" placeholder="Thêm giá trị (VD: Đỏ)" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="add_value" class="btn btn-sm btn-outline-success">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include_once './views/admin/footer.php'; ?>