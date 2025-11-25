<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">Thêm Sản phẩm mới</h2>
            <a href="index.php?class=admin&act=products" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back'></i> Quay lại
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="index.php?class=admin&act=add_product" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên sản phẩm (*)</label>
                                <input type="text" name="name" class="form-control" required placeholder="Ví dụ: iPhone 15 Pro Max">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Danh mục (*)</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php foreach ($categories as $c): ?>
                                            <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Thương hiệu (*)</label>
                                    <select name="brand_id" class="form-select" required>
                                        <option value="">-- Chọn thương hiệu --</option>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b['brand_id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả ngắn</label>
                                <textarea name="short_description" class="form-control" rows="3" placeholder="Mô tả tóm tắt hiển thị ở danh sách..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả chi tiết</label>
                                <textarea name="detail_description" class="form-control" rows="6" placeholder="Bài viết chi tiết về sản phẩm..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) (*)</label>
                                <input type="number" name="price" class="form-control" required min="0" placeholder="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ảnh đại diện (*)</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required onchange="previewImage(this)">
                                <div class="mt-3 text-center">
                                    <img id="imgPreview" src="image/default.png" class="img-thumbnail rounded" style="max-height: 200px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class='bx bx-save'></i> Lưu sản phẩm
                        </button>
                    </div>
                </form>
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
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include_once './views/admin/footer.php'; ?>