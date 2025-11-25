<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">Quản lý Sản phẩm</h2>
        <div class="d-flex gap-2">
            <input type="text" id="searchProduct" class="form-control" placeholder="Tìm tên sản phẩm..." style="border-radius: 20px; min-width: 250px;">
            <a href="index.php?class=admin&act=add_product" class="btn btn-success rounded-pill px-3">
                <i class='bx bx-plus'></i> Thêm mới
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Sản phẩm</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($p['main_image_url']); ?>"
                                            class="rounded me-3 border" width="50" height="50" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['name']); ?></div>
                                            <small class="text-muted">ID: #<?php echo $p['product_id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-danger fw-bold"><?php echo number_format($p['price']); ?> đ</td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['category_name'] ?? '---'); ?></span>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($p['brand_name'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success"><i class='bx bx-check'></i> Hiện</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary"><i class='bx bx-hide'></i> Ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="index.php?class=admin&act=edit_product&id=<?php echo $p['product_id']; ?>"
                                        class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                        <i class='bx bx-edit-alt'></i>
                                    </a>
                                    <a href="index.php?class=admin&act=delete_product&id=<?php echo $p['product_id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')" title="Xóa">
                                        <i class='bx bx-trash'></i>
                                    </a>
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
    // Tìm kiếm nhanh
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#productTableBody tr').forEach(row => {
            const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            row.style.display = name.includes(value) ? '' : 'none';
        });
    });
</script>

<?php include_once './views/admin/footer.php'; ?>