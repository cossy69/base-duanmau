<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">Cập nhật Sản phẩm</h2>
            <a href="index.php?class=admin&act=products" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back'></i> Quay lại
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="index.php?class=admin&act=edit_product&id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên sản phẩm</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Danh mục</label>
                                    <select name="category_id" class="form-select" required>
                                        <?php foreach ($categories as $c): ?>
                                            <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Thương hiệu</label>
                                    <select name="brand_id" class="form-select" required>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b['brand_id']; ?>" <?php echo ($b['brand_id'] == $product['brand_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($b['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả ngắn</label>
                                <textarea name="short_description" class="form-control" rows="3"><?php echo htmlspecialchars($product['short_description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả chi tiết</label>
                                <textarea name="detail_description" class="form-control" rows="6"><?php echo htmlspecialchars($product['detail_description']); ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                                <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo ($product['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isActive">Hiển thị sản phẩm</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <input type="hidden" name="current_image" value="<?php echo $product['main_image_url']; ?>">
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">

                                <div class="mt-3 text-center">
                                    <p class="text-muted small mb-1">Ảnh hiện tại:</p>
                                    <img id="imgPreview" src="<?php echo $product['main_image_url']; ?>" class="img-thumbnail rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" name="update_product" class="btn btn-primary px-4 fw-bold">
                            <i class='bx bx-save'></i> Cập nhật
                        </button>
                    </div>
                </form>
                <hr class="my-5">

                <h3 class="text-primary fw-bold mb-4">Quản lý Biến thể (Phiên bản)</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light fw-bold">Thêm phiên bản mới</div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="add_variant" value="1">

                                    <label class="form-label fw-bold">Thuộc tính:</label>
                                    <?php foreach ($allAttributes as $attributeItem): ?>
                                        <div class="mb-2">
                                            <label class="small text-muted fw-bold"><?php echo htmlspecialchars($attributeItem['name']); ?></label>

                                            <select name="attribute_values[]" class="form-select form-select-sm">
                                                <option value="">-- Chọn --</option>
                                                <?php if (isset($attributeItem['values']) && is_array($attributeItem['values'])): ?>
                                                    <?php foreach ($attributeItem['values'] as $val): ?>
                                                        <option value="<?php echo $val['value_id']; ?>">
                                                            <?php echo htmlspecialchars($val['value']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="mb-2">
                                        <label>Giá bán:</label>
                                        <input type="number" name="var_price" class="form-control" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Giá gốc (gạch ngang):</label>
                                        <input type="number" name="var_original_price" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Số lượng kho:</label>
                                        <input type="number" name="var_qty" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Ảnh:</label>
                                        <input type="file" name="variant_image" class="form-control">
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Lưu Biến Thể</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Thuộc tính</th>
                                        <th>Giá</th>
                                        <th>Kho</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variants as $v): ?>
                                        <tr>
                                            <td><img src="<?php echo $v['main_image_url']; ?>" width="40"></td>
                                            <td>
                                                <?php foreach ($v['attributes'] as $a): ?>
                                                    <span class="badge bg-info text-dark"><?php echo $a['name'] . ': ' . $a['value']; ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>
                                                <div class="text-danger fw-bold"><?php echo number_format($v['current_variant_price']); ?></div>
                                                <small class="text-decoration-line-through text-muted"><?php echo number_format($v['original_variant_price']); ?></small>
                                            </td>
                                            <td><?php echo $v['quantity']; ?></td>
                                            <td>
                                                <a href="index.php?class=admin&act=edit_product&id=<?php echo $product['product_id']; ?>&delete_variant=<?php echo $v['variant_id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Xóa biến thể này?')">Xóa</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('imgPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include_once './views/admin/footer.php'; ?>