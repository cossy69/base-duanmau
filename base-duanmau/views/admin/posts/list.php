<?php include_once './views/admin/header.php'; ?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Quản lý Tin tức</h2>
        <a href="index.php?class=admin&act=add_post" class="btn btn-success rounded-pill px-3">
            <i class='bx bx-plus'></i> Viết bài mới
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($p['thumbnail_url']); ?>" class="rounded me-3 border" width="60" height="40" style="object-fit: cover;">
                                        <div class="text-truncate" style="max-width: 300px;">
                                            <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($p['author_name']); ?></td>
                                <td>
                                    <?php if ($p['is_published']): ?>
                                        <span class="badge bg-success-subtle text-success">Đã đăng</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                                <td class="text-end pe-4">
                                    <a href="index.php?class=admin&act=edit_post&id=<?php echo $p['post_id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class='bx bx-edit-alt'></i></a>
                                    <a href="index.php?class=admin&act=delete_post&id=<?php echo $p['post_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa bài viết này?')"><i class='bx bx-trash'></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once './views/admin/footer.php'; ?>