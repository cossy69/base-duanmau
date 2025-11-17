<div class="quick-buttons">
    <a href="index.php?ctl=user&class=compare&act=compare" class="quick-btn compare-btn" title="So sánh">
        <i class="bx bx-swap-horizontal"></i>
        <span class="badge-compare">2</span>
    </a>

    <a href="index.php?ctl=user&class=discound&act=discound" class="quick-btn compare-btn" title="Voucher">
        <i class="bxr bxs-tickets"></i>
    </a>

    <button
        id="scrollTopBtn"
        class="quick-btn scroll-btn"
        title="Lên đầu trang">
        <i class="bxr bx-chevron-up"></i>
    </button>
</div>

<?php
// Lấy tất cả các tham số GET hiện tại để giữ lại khi phân trang/sắp xếp
$queryParams = $_GET;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <form id="filter-form" action="index.php" method="GET">
                    <input type="hidden" name="class" value="product">
                    <input type="hidden" name="act" value="product">

                    <div class="card-body">
                        <h6 class="text-primary mt-2">Danh mục</h6>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <?php if ($category['product_count'] > 0): ?>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="category[]"
                                            value="<?php echo $category['category_id']; ?>"
                                            id="category<?php echo $category['category_id']; ?>"
                                            <?php echo in_array($category['category_id'], $filterCategories) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category<?php echo $category['category_id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                            (<?php echo $category['product_count']; ?>)
                                        </label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Không có danh mục.</p>
                        <?php endif; ?>

                        <hr />

                        <h6 class="text-primary">Khoảng giá (Tối đa)</h6>
                        <div class="mb-3">
                            <input
                                type="range"
                                class="form-range"
                                id="priceRange"
                                name="max_price"
                                min="0"
                                max="<?php echo $maxPriceDefault; ?>"
                                step="1000000"
                                value="<?php echo htmlspecialchars($maxPrice); ?>" />
                            <div class="d-flex justify-content-between small">
                                <span>0 VNĐ</span>
                                <span id="priceValue">
                                    <?php echo $maxPrice ?> VNĐ
                                </span>
                            </div>
                        </div>
                        <hr />

                        <h6 class="text-primary">Thương hiệu</h6>
                        <select class="form-select form-select-sm" name="brand">
                            <option value="0">Tất cả</option>
                            <?php if (!empty($brands)): ?>
                                <?php foreach ($brands as $brand): ?>
                                    <option
                                        value="<?php echo $brand['brand_id']; ?>"
                                        <?php echo ($brand['brand_id'] == $filterBrandId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Áp dụng</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded shadow-sm border">
                <p class="mb-0 fw-bold">Tìm thấy <?php echo $totalProducts; ?> kết quả</p>

                <div class="d-flex align-items-center gap-2">
                    <label for="sortOrder" class="form-label mb-0 small text-muted">Sắp xếp theo:</label>
                    <?php
                    // Bỏ 'sort' ra khỏi query, để <select> xử lý
                    $sortParams = $queryParams;
                    unset($sortParams['sort']);
                    $sortBaseUrl = 'index.php?' . http_build_query($sortParams);
                    ?>
                    <select
                        class="form-select form-select-sm"
                        id="sortOrder"
                        style="width: 150px"
                        onchange="if(this.value) window.location.href = this.value;">
                        <option value="<?php echo $sortBaseUrl . '&sort=newest'; ?>" <?php echo ($sortOrder == 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="<?php echo $sortBaseUrl . '&sort=price_asc'; ?>" <?php echo ($sortOrder == 'price_asc') ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="<?php echo $sortBaseUrl . '&sort=price_desc'; ?>" <?php echo ($sortOrder == 'price_desc') ? 'selected' : ''; ?>>Giá giảm dần</option>
                    </select>
                </div>
            </div>

            <div class="row_product">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="card" style="width: 100%; position: relative; height: 100%;">
                            <a style="text-decoration: none; display: flex; flex-direction: column; height: 100%;" href="index.php?class=product&act=product_detail&id=<?php echo $product['product_id']; ?>">

                                <?php if ($product['discount_amount'] > 0): ?>
                                    <p class="p_bottom giam_gia">-<?php echo round($product['discount_percent']); ?>%</p>
                                <?php endif; ?>

                                <img
                                    src="<?php echo htmlspecialchars($product['image_url'] ?? 'image/default.png'); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" />

                                <div class="card-body" style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <h5 style="color: black" class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>

                                    <div class="price d-flex align-items-center gap-2" style="min-height: 4.5em;">
                                        <p style="font-size: 22px; font-weight: 500; color: rgb(255, 18, 18);" class="p_bottom gia gia_cu">
                                            <?php echo $product['current_price'] ?> VNĐ
                                        </p>
                                        <?php if ($product['discount_amount'] > 0): ?>
                                            <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                                <?php echo $product['original_price'] ?> VNĐ
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p style="color: black; margin-bottom: 10px" class="card-text">
                                    </p>

                                    <div class="action_pro d-flex justify-content-between" style="margin-top: auto;">
                                        <div class="d-flex justify-content-between gap-3">
                                            <?php
                                            // Kiểm tra xem user có đăng nhập VÀ sản phẩm này có trong mảng yêu thích không
                                            $isFavorited = (isset($favoriteProductIds) && in_array($product['product_id'], $favoriteProductIds));
                                            ?>
                                            <button class="favorite-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                                <i class="bxr bx-heart <?php echo $isFavorited ? 'active_i' : ''; ?>"></i>
                                            </button>
                                            <button><i class="bxr bx-git-compare"></i></button>
                                        </div>
                                        <button class="btn btn-outline-primary add-to-cart-btn"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-variant-id="<?php echo $product['default_variant_id']; ?>">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center w-100">Không tìm thấy sản phẩm nào phù hợp.</p>
                <?php endif; ?>
            </div>

            <nav class="mt-5" aria-label="Product pagination">
                <ul class="pagination justify-content-center">

                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <?php $queryParams['page'] = $currentPage - 1; ?>
                            <a class="page-link text-primary" href="index.php?<?php echo http_build_query($queryParams); ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php $queryParams['page'] = $i; ?>
                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>" <?php echo ($i == $currentPage) ? 'aria-current="page"' : ''; ?>>
                            <a class="page-link <?php echo ($i == $currentPage) ? 'bg-primary border-primary' : 'text-primary'; ?>"
                                href="index.php?<?php echo http_build_query($queryParams); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <?php $queryParams['page'] = $currentPage + 1; ?>
                            <a class="page-link text-primary" href="index.php?<?php echo http_build_query($queryParams); ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceSlider = document.getElementById('priceRange');
        const priceDisplay = document.getElementById('priceValue');

        if (priceSlider && priceDisplay) {

            // Hàm helper để cập nhật text (khớp với PHP)
            function updateDisplay(value) {
                priceDisplay.textContent = value + ' VNĐ';
            }

            // --- SỬA LỖI HIỂN THỊ ---

            // 1. Lấy giá trị hiện tại (mà PHP đã set)
            const initialValue = priceSlider.value;

            // 2. Cập nhật text ngay lập tức khi tải trang
            updateDisplay(initialValue);

            // 3. (Quan trọng) Set lại giá trị của slider
            //    Cái này sẽ "bắt" trình duyệt phải vẽ lại
            //    cái nút trượt (knob) về đúng vị trí
            priceSlider.value = initialValue;

            // 4. Thêm sự kiện khi người dùng kéo
            priceSlider.addEventListener('input', function() {
                updateDisplay(this.value);
            });
        }
    });
</script>