<?php

// Lấy danh sách sản phẩm để xác định số cột
$products = $products ?? [];
$specs = $specs ?? [];
$numProducts = count($products);
$productIds = array_column($products, 'id'); // Lấy mảng ID sản phẩm
?>

<main class="py-5">
    <div class="container-fluid px-3 px-lg-5" data-aos="fade-up">
        <h1 class="text-center fw-bold mb-4 text-dark">
            Bảng So Sánh Thông Số Kỹ Thuật Chi Tiết
        </h1>
        <p class="text-center text-secondary mb-5">
            Bạn có thể so sánh tối đa 3 sản phẩm cùng lúc.
        </p>

        <?php if ($numProducts == 0): ?>
            <div class="alert alert-info text-center">
                <i class="bx bx-info-circle me-2"></i> Chưa có sản phẩm nào được chọn để so sánh. Vui lòng thêm sản phẩm từ trang chi tiết.
            </div>
        <?php else: ?>
            <div class="table-responsive shadow-xl rounded-4 overflow-hidden" id="comparison-table-wrapper">
                <table
                    class="comparison-table table table-hover align-middle bg-white">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="product-col text-dark feature-header border-top border-bottom">
                                <span class="fw-bold fs-5">Đặc điểm</span>
                            </th>

                            <?php foreach ($products as $product): ?>
                                <th scope="col" class="feature-header product-col-data text-dark product-col-<?php echo $product['id']; ?>">
                                    <div class="product-card">
                                        <img
                                            src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            class="img-fluid mb-2 shadow-lg" />
                                        <span class="product-name text-dark"><?php echo htmlspecialchars($product['name']); ?></span>
                                        <button
                                            class="btn btn-sm btn-danger rounded-3 fw-medium remove-comp-btn"
                                            data-product-id="<?php echo $product['id']; ?>">
                                            <i class="bx bx-trash me-1"></i> Xóa
                                        </button>
                                    </div>
                                </th>
                            <?php endforeach; ?>

                            <?php for ($i = $numProducts; $i < 3; $i++): ?>
                                <th scope="col" class="feature-header product-col-data text-muted">
                                    <div class="product-card">
                                        <i class="bx bx-plus-circle fs-1 mb-2"></i>
                                        <span class="product-name">Thêm Sản phẩm</span>
                                    </div>
                                </th>
                            <?php endfor; ?>

                        </tr>
                    </thead>

                    <tbody>
                        <>
                            <td class="product-col feature-header">Giá (VNĐ)</td>
                            <?php foreach ($products as $product): ?>
                                <td class="product-col-data fw-bold text-primary gia product-col-<?php echo $product['id']; ?>">
                                    <?php echo $product['price']; ?>
                                </td>
                            <?php endforeach; ?>
                            <?php for ($i = $numProducts; $i < 3; $i++): ?>
                                <td class="product-col-data">--</td>
                            <?php endfor; ?>
                            </tr>

                            <?php foreach ($specs as $groupName => $specsInGroup): ?>
                                <tr class="table-secondary bg-opacity-25">
                                    <td class="product-col feature-header feature-group-header" colspan="<?php echo $numProducts < 3 ? $numProducts + 1 : 4; ?>">
                                        <?php echo htmlspecialchars($groupName); ?>
                                    </td>
                                </tr>

                                <?php foreach ($specsInGroup as $specName => $valuesByProduct): ?>
                                    <tr>
                                        <td class="product-col"><?php echo htmlspecialchars($specName); ?></td>

                                        <?php foreach ($productIds as $productId): ?>
                                            <td class="product-col-data product-col-<?php echo $productId; ?>">
                                                <?php echo htmlspecialchars($valuesByProduct[$productId] ?? '--'); ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <?php for ($i = $numProducts; $i < 3; $i++): ?>
                                            <td class="product-col-data">--</td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>


                            <tr class="table-primary bg-opacity-10">
                                <td class="product-col text-dark">Hành động</td>
                                <?php foreach ($products as $product): ?>
                                    <td class="product-col-data product-col-<?php echo $product['id']; ?>">
                                        <a
                                            href="index.php?class=product&act=product_detail&id=<?php echo $product['id']; ?>"
                                            class="btn btn-primary btn-lg rounded-3 shadow-lg px-4 fw-medium"
                                            title="Xem chi tiết sản phẩm">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                                <?php for ($i = $numProducts; $i < 3; $i++): ?>
                                    <td class="product-col-data">--</td>
                                <?php endfor; ?>
                            </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-center mt-5">
                <button id="clear-all-comp" class="btn btn-outline-danger">Xóa tất cả sản phẩm khỏi bảng</button>
            </p>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableWrapper = document.getElementById('comparison-table-wrapper');
        const clearAllBtn = document.getElementById('clear-all-comp');

        // --- Hàm xử lý xóa 1 sản phẩm ---
        document.body.addEventListener('click', function(event) {
            const removeBtn = event.target.closest('.remove-comp-btn');
            if (!removeBtn) return;

            event.preventDefault();
            const productId = removeBtn.dataset.productId;

            if (confirm(`Bạn có chắc muốn xóa sản phẩm ID ${productId} khỏi bảng so sánh?`)) {
                const formData = new FormData();
                formData.append('product_id', productId);

                fetch('index.php?class=compare&act=removeProduct', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(json => {
                        if (json.status === 'success') {
                            // Logic xóa cột khỏi DOM (Dùng CSS selector)
                            document.querySelectorAll(`.product-col-${productId}`).forEach(el => el.remove());

                            // Nếu không còn sản phẩm nào, tải lại trang
                            if (json.data.count === 0) {
                                window.location.reload();
                            }

                            // Cập nhật lại số lượng (Có thể cần thêm logic để ẩn/hiện cột trống nếu muốn)
                        } else {
                            alert(json.message);
                        }
                    })
                    .catch(err => console.error('Lỗi xóa sản phẩm:', err));
            }
        });

        // --- Hàm xử lý xóa TẤT CẢ ---
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn xóa TẤT CẢ sản phẩm khỏi bảng so sánh?')) {
                    const productIds = [<?php echo implode(',', $productIds); ?>];

                    // Gửi request xóa từng cái
                    productIds.forEach(id => {
                        const formData = new FormData();
                        formData.append('product_id', id);
                        fetch('index.php?class=compare&act=removeProduct', {
                            method: 'POST',
                            body: formData
                        });
                    });

                    // Sau khi xóa, tải lại trang
                    setTimeout(() => window.location.reload(), 200);
                }
            });
        }

        // --- (Toast và Add to Cart - Giả định rằng script global sẽ bắt) ---
    });
</script>