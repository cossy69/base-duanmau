<?php include_once './views/admin/header.php'; ?>

<style>
    /* Custom Scrollbar cho bảng nếu quá dài */
    .variant-list-container {
        max-height: 600px;
        overflow-y: auto;
    }

    /* Sticky Sidebar: Giữ form thêm luôn nổi khi cuộn */
    .sticky-sidebar {
        position: -webkit-sticky;
        position: sticky;
        top: 20px;
        z-index: 10;
    }

    /* Style cho badge thuộc tính */
    .attr-badge {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 8px;
        font-weight: 500;
        background-color: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 4px;
    }
</style>

<div class="main-content bg-light pb-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
            <div>
                <h2 class="text-primary fw-bold mb-1">Cập nhật Sản phẩm</h2>
                <p class="text-muted mb-0">Chỉnh sửa thông tin và quản lý phiên bản giá bán</p>
            </div>
            <a href="index.php?class=admin&act=products" class="btn btn-outline-secondary rounded-pill px-3">
                <i class='bx bx-arrow-back me-1'></i> Quay lại
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold text-dark"><i class='bx bx-info-circle text-primary me-2'></i>Thông tin chung</h5>
            </div>
            <div class="card-body p-4">
                <form action="index.php?class=admin&act=edit_product&id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control fw-bold text-dark" id="prodName" value="<?php echo htmlspecialchars($product['name']); ?>" required placeholder="Tên sản phẩm">
                                <label for="prodName">Tên sản phẩm</label>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Danh mục</label>
                                    <select name="category_id" class="form-select py-2" required>
                                        <?php foreach ($categories as $c): ?>
                                            <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Thương hiệu</label>
                                    <select name="brand_id" class="form-select py-2" required>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b['brand_id']; ?>" <?php echo ($b['brand_id'] == $product['brand_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($b['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-bold text-muted small">Mô tả ngắn</label>
                                <textarea name="short_description" class="form-control" rows="3"><?php echo htmlspecialchars($product['short_description']); ?></textarea>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-bold text-muted small">Mô tả chi tiết</label>
                                <textarea name="detail_description" class="form-control" rows="5"><?php echo htmlspecialchars($product['detail_description']); ?></textarea>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái hiển thị</label>
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo ($product['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fs-6 text-muted" for="isActive">Đang bán</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ảnh đại diện chính</label>
                                    <input type="hidden" name="current_image" value="<?php echo $product['main_image_url']; ?>">
                                    <input type="file" name="image" class="form-control form-control-sm mb-2" accept="image/*" onchange="previewImage(this, 'mainImgPreview')">

                                    <div class="text-center bg-white p-2 rounded border" style="height: 250px; display: flex; align-items: center; justify-content: center;">
                                        <img id="mainImgPreview" src="<?php echo $product['main_image_url']; ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" name="update_product" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
                            <i class='bx bx-save me-1'></i> Lưu thông tin chung
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <h4 class="fw-bold text-primary mb-3 d-flex align-items-center">
                    <i class='bx bxs-component me-2'></i> Quản lý Phiên bản & Giá bán
                </h4>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow rounded-4 sticky-sidebar">
                    <div class="card-header bg-primary text-white py-3 rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class='bx bx-plus-circle me-1'></i> Thêm Phiên bản mới</h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="add_variant" value="1">

                            <div class="alert alert-light border text-muted small mb-3">
                                <i class='bx bx-bulb text-warning'></i> Chọn các thuộc tính kết hợp (ví dụ: Màu Đỏ + 128GB) để tạo ra một phiên bản bán hàng.
                            </div>

                            <?php foreach ($allAttributes as $attributeItem): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-1"><?php echo htmlspecialchars($attributeItem['name']); ?></label>
                                    <select name="attribute_values[]" class="form-select bg-light border-0 fw-medium">
                                        <option value="">-- Mặc định --</option>
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

                            <hr class="border-dashed my-3">

                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-success small">Giá bán thực tế (*)</label>
                                    <div class="input-group">
                                        <input type="number" name="var_price" class="form-control fw-bold text-success" required min="0" placeholder="0">
                                        <span class="input-group-text bg-success-subtle text-success fw-bold">VNĐ</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small">Giá niêm yết</label>
                                    <input type="number" name="var_original_price" class="form-control form-control-sm" min="0" placeholder="Gốc">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small">Tồn kho</label>
                                    <input type="number" name="var_qty" class="form-control form-control-sm" required min="0" value="10">
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label small fw-bold">Ảnh riêng (Nếu có)</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="file" name="variant_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'varImgPreview')">
                                    <img id="varImgPreview" src="image/default.png" class="rounded border" width="40" height="40" style="object-fit: cover;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm hover-scale">
                                <i class='bx bx-check-circle'></i> Lưu Phiên bản này
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-0">
                        <?php if (empty($variants)): ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class='bx bx-package text-muted' style="font-size: 80px; opacity: 0.3;"></i>
                                </div>
                                <h5 class="text-muted fw-bold">Chưa có phiên bản nào</h5>
                                <p class="text-secondary mb-0">Sản phẩm này hiện chưa thể bán.</p>
                                <p class="text-secondary">Vui lòng thêm ít nhất một phiên bản ở cột bên trái.</p>
                            </div>
                        <?php else: ?>
                            <div class="variant-list-container">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr class="text-secondary text-uppercase small">
                                            <th class="ps-4 py-3">Hình ảnh</th>
                                            <th>Đặc điểm / Thuộc tính</th>
                                            <th>Giá bán / Kho</th>
                                            <th class="text-end pe-4">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($variants as $v): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="position-relative" style="width: 60px; height: 60px;">
                                                        <img src="<?php echo !empty($v['main_image_url']) ? $v['main_image_url'] : $product['main_image_url']; ?>"
                                                            class="rounded border shadow-sm w-100 h-100"
                                                            style="object-fit: cover;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($v['attributes'])): ?>
                                                        <div class="d-flex flex-wrap">
                                                            <?php foreach ($v['attributes'] as $a): ?>
                                                                <span class="attr-badge">
                                                                    <?php echo $a['value']; ?>
                                                                    <span class="text-muted small ms-1" style="font-size: 0.7em;">(<?php echo $a['name']; ?>)</span>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Mặc định</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-danger fw-bold fs-6">
                                                            <?php echo number_format($v['current_variant_price']); ?>
                                                            <span class="small">đ</span>
                                                        </span>
                                                        <?php if ($v['original_variant_price'] > $v['current_variant_price']): ?>
                                                            <small class="text-decoration-line-through text-muted" style="font-size: 0.8rem;">
                                                                <?php echo number_format($v['original_variant_price']); ?> đ
                                                            </small>
                                                        <?php endif; ?>
                                                        <small class="text-dark mt-1">
                                                            Kho: <span class="fw-bold"><?php echo $v['quantity']; ?></span>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="index.php?class=admin&act=edit_product&id=<?php echo $product['product_id']; ?>&delete_variant=<?php echo $v['variant_id']; ?>"
                                                        class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                                        title="Xóa phiên bản này"
                                                        onclick="return confirm('Bạn có chắc muốn xóa biến thể này?')">
                                                        <i class='bx bx-trash'></i> Xóa
                                                    </a>
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
        </div>
    </div>
</div>

<script>
    // Hàm preview ảnh khi chọn file
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
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