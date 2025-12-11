<?php include_once './views/admin/header.php'; ?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Sửa bài viết</h2>
        <a href="index.php?class=admin&act=posts" class="btn btn-outline-secondary"><i class='bx bx-arrow-back'></i> Quay lại</a>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề bài viết</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                        </div>

                        <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

                        <div class="mb-3">
                            <label class="form-label">Nội Dung Chi Tiết</label>
                            <textarea name="content" id="editor_content" class="form-control" rows="10" required>
                                <?php echo htmlspecialchars($post['content']); ?>
                            </textarea>
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                CKEDITOR.replace('editor_content', {
                                    height: 400
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $post['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh bìa</label>
                            <input type="hidden" name="current_thumbnail" value="<?php echo $post['thumbnail_url']; ?>">
                            <input type="file" name="thumbnail" class="form-control mb-2">
                            <?php if (!empty($post['thumbnail_url'])): ?>
                                <img src="<?php echo $post['thumbnail_url']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 150px; width: 100%; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_published" id="isPublished" <?php echo ($post['is_published']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="isPublished">Đăng ngay</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include_once './views/admin/footer.php'; ?>