<?php include_once './views/admin/header.php'; ?>
<div class="main-content">

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class='bx bx-error-circle'></i> <?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class='bx bx-check-circle'></i> <?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Quản lý Thuộc tính</h2>
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
                        <div>
                            <h6 class="mb-0 fw-bold text-primary d-inline-block me-2"><?php echo htmlspecialchars($attr['name']); ?></h6>
                            <?php if (!empty($attr['unit'])): ?>
                                <span class="badge bg-light text-secondary">Đơn vị: <?php echo htmlspecialchars($attr['unit']); ?></span>
                            <?php endif; ?>
                        </div>

                        <a href="index.php?class=admin&act=delete_attribute&id=<?php echo $attr['attribute_id']; ?>"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3"
                            onclick="return confirm('CẢNH BÁO: Bạn chỉ có thể xóa nếu thuộc tính này chưa được gắn cho sản phẩm nào. Bạn có chắc chắn muốn thử xóa?')">
                            <i class='bx bx-trash'></i> Xóa
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <?php if (empty($attr['values'])): ?>
                                <small class="text-muted fst-italic">Chưa có giá trị nào.</small>
                            <?php else: ?>
                                <?php foreach ($attr['values'] as $val): ?>
                                    <span class="badge bg-secondary me-1 mb-1 p-2"><?php echo htmlspecialchars($val['value']); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <form method="POST" class="row g-2 align-items-center"
                            onsubmit="return validateAttributeValue(this, '<?php echo htmlspecialchars($attr['name']); ?>')">

                            <input type="hidden" name="attribute_id" value="<?php echo $attr['attribute_id']; ?>">

                            <div class="col-auto flex-grow-1">
                                <input type="text" name="value" class="form-control form-control-sm"
                                    placeholder="Thêm giá trị..." required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="add_value" class="btn btn-sm btn-outline-success">
                                    <i class='bx bx-plus'></i> Thêm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    function validateAttributeValue(form, attrName) {
        const input = form.querySelector('input[name="value"]');
        const value = input.value.trim();
        const lowerName = attrName.toLowerCase();

        // Ràng buộc nhập số cho RAM/Dung lượng
        if (lowerName.includes('ram') || lowerName.includes('dung lượng') || lowerName.includes('bộ nhớ')) {
            const isNumeric = /^[0-9]+(\.[0-9]+)?$/.test(value);
            if (!isNumeric) {
                alert(`Lỗi: Với thuộc tính "${attrName}", bạn chỉ được nhập số (Ví dụ: 8, 128).`);
                input.value = '';
                input.focus();
                return false;
            }
        }

        // Ràng buộc màu sắc không được là số
        if (lowerName.includes('màu')) {
            const isOnlyNumbers = /^[0-9]+$/.test(value);
            if (isOnlyNumbers) {
                alert(`Lỗi: Màu sắc không thể chỉ là số.`);
                input.focus();
                return false;
            }
        }
        return true;
    }
</script>

<?php include_once './views/admin/footer.php'; ?>