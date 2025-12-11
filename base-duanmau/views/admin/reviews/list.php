<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Quản lý Đánh giá</h1>
            <small style="color: #999">Tổng hợp nhận xét sản phẩm</small>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Sản phẩm</th>
                            <th width="15%">Người dùng</th>
                            <th width="15%">Sao / Trạng thái</th>
                            <th width="35%">Nội dung</th>
                            <th width="10%">Ngày</th>
                            <th width="5%" class="text-end">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Chưa có đánh giá nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td>#<?php echo $review['review_id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($review['main_image_url']); ?>"
                                                alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="me-2">
                                            <span class="text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($review['product_name']); ?>">
                                                <?php echo htmlspecialchars($review['product_name']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($review['full_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-warning mb-1">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo ($i <= $review['rating']) ? "<i class='bx bxs-star'></i>" : "<i class='bx bx-star'></i>";
                                            }
                                            ?>
                                        </div>
                                        <span class="badge bg-success">Hiển thị</span>
                                    </td>
                                    <td>
                                        <p class="mb-0 small text-muted text-break">
                                            <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                        </p>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($review['review_date'])); ?></td>
                                    <td class="text-end">
                                        <a href="index.php?class=admin&act=delete_review&id=<?php echo $review['review_id']; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Xóa đánh giá này?');">
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