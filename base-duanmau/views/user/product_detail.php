<style>
    /* CSS cho phần chọn sao đánh giá */
    #reviewRating i {
        font-size: 2rem;
        /* Tăng kích thước lên chút cho dễ bấm */
        cursor: pointer;
        color: #ffc107;
        /* Màu vàng */
        transition: transform 0.2s;
    }

    #reviewRating i:hover {
        transform: scale(1.3);
        /* Phóng to khi di chuột */
    }

    /* Tắt viền xanh mặc định khi bấm nút */
    .btn:focus,
    .btn:active {
        box-shadow: none !important;
        outline: none !important;
    }

    /* CSS cho nút yêu thích khi active */
    .btn-outline-danger.active-fav {
        background-color: #fff !important;
        /* Nền trắng */
        color: #dc3545 !important;
        /* Chữ đỏ */
        border-color: #dc3545 !important;
        /* Viền đỏ */
    }

    /* Fix lỗi hover bị đổi màu nền */
    .btn-outline-danger.active-fav:hover {
        background-color: #fff !important;
        color: #dc3545 !important;
    }
</style>
<?php
function format_vnd_detail($price)
{
    return number_format($price, 0, ',', '.') . ' VNĐ';
}
// Hàm helper để render sao
function render_stars($rating)
{
    $stars = '';
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    for ($i = 0; $i < $full_stars; $i++) {
        $stars .= '<i class="bi bi-star-fill"></i>';
    }
    if ($half_star) {
        $stars .= '<i class="bi bi-star-half"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars .= '<i class="bi bi-star"></i>';
    }
    return $stars;
}
?>
<div class="quick-buttons">
    <a href="index.php?ctl=user&class=compare&act=compare" class="quick-btn compare-btn" title="So sánh">
        <i class="bx bx-swap-horizontal"></i>
        <?php
        // Lấy số lượng so sánh từ Controller (mặc định là 0 nếu chưa set)
        $compCount = $compareCount ?? 0;
        // Thêm style 'display: none' nếu count = 0
        $style = ($compCount <= 0) ? 'style="display: none;"' : '';
        ?>
        <span class="badge-compare" <?php echo $style; ?>>
            <?php echo $compCount; ?>
        </span>
    </a>

    <a href="index.php?ctl=user&class=discount&act=discount" class="quick-btn compare-btn" title="Voucher">
        <i class="bxr bxs-tickets"></i>
    </a>

    <button
        id="scrollTopBtn"
        class="quick-btn scroll-btn"
        title="Lên đầu trang">
        <i class="bxr bx-chevron-up"></i>
    </button>
</div>
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <div style="--swiper-navigation-color: #000; --swiper-pagination-color: #000;" class="pro_detail swiper mySwiper2">
                <div class="swiper-wrapper" id="main-image-slider">
                    <?php if (!empty($galleryImages)): ?>
                        <?php foreach ($galleryImages as $img): ?>
                            <div class="swiper-slide">
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Ảnh sản phẩm" />
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <img src="image/default.png" alt="Không có ảnh" />
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-button-next text-dark"></div>
                <div class="swiper-button-prev text-dark"></div>
            </div>
            <div thumbsSlider="" class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($galleryImages) && count($galleryImages) > 1): ?>
                        <?php foreach ($galleryImages as $img): ?>
                            <div class="swiper-slide">
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Ảnh thumbnail" />
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="text-primary"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="d-flex align-items-center mb-3">
                <div class="rating me-2 fs-5 text-warning">
                    <?php echo render_stars($reviewSummary['avg_rating']); ?>
                </div>
                <span class="text-muted small">(<?php echo number_format($reviewSummary['avg_rating'], 1); ?>/5 sao, <?php echo $reviewSummary['total_reviews']; ?> đánh giá)</span>
            </div>
            <p class="lead text-muted">
                <?php echo htmlspecialchars($product['short_description']); ?>
            </p>

            <div class="mb-3">
                <span class="gia h2 fw-bold text-danger" id="current-price">
                    <?php echo format_vnd_detail($product['default_price']); ?>
                </span>
                <span class="gia text-muted text-decoration-line-through ms-2" id="original-price">
                    <?php if ($product['default_original_price'] > $product['default_price']) echo format_vnd_detail($product['default_original_price']); ?>
                </span>
            </div>

            <div class="mb-4">
                <span class="badge bg-success" id="stock-status">
                    <?php echo ($product['total_stock'] > 0) ? 'Còn hàng (' . $product['total_stock'] . ' sản phẩm)' : 'Hết hàng'; ?>
                </span>
            </div>

            <form id="add-to-cart-form">
                <input type="hidden" id="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" id="selected_variant_id" value="0">

                <?php if (!empty($variantOptions)): ?>
                    <?php foreach ($variantOptions as $name => $values): ?>
                        <div class="mb-4 variant-group">
                            <label class="form-label fw-bold"><?php echo htmlspecialchars($name); ?>:</label>
                            <div class="variant-options-container">
                                <?php foreach ($values as $index => $value): ?>
                                    <span
                                        class="variant-option <?php echo $index === 0 ? 'selected' : ''; // Tự động chọn cái đầu tiên 
                                                                ?>"
                                        data-value-id="<?php echo $value['value_id']; ?>">
                                        <?php echo htmlspecialchars($value['value']); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="quantity" class="form-label fw-bold">Số lượng:</label>
                    <input
                        type="number"
                        id="quantity"
                        class="form-control"
                        value="1"
                        min="1"
                        style="width: 100px" />
                </div>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <?php
                    // Nhận biến từ Controller
                    $isFav = isset($isFavorited) && $isFavorited;
                    ?>
                    <button class="btn <?php echo $isFav ? 'btn-outline-danger active-fav' : 'btn-outline-secondary'; ?> favorite-toggle-btn"
                        data-product-id="<?php echo $product['product_id']; ?>">
                        <i class="bx <?php echo $isFav ? 'bxs-heart' : 'bx-heart'; ?>"></i> Yêu thích
                    </button>

                    <?php
                    // Nhận biến từ Controller
                    $isComp = isset($isCompared) && $isCompared;
                    ?>
                    <button class="btn <?php echo $isComp ? 'btn-primary' : 'btn-outline-secondary'; ?> compare-toggle-btn"
                        data-product-id="<?php echo $product['product_id']; ?>">
                        <i class="bx bx-git-compare"></i> So sánh
                    </button>
                </div>
                <div class="d-grid gap-2">
                    <button
                        id="addToCartBtn"
                        type="button"
                        class="btn btn-primary btn-lg">
                        Thêm vào Giỏ hàng
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-lg"
                        data-bs-toggle="modal"
                        data-bs-target="#buyNowModal">
                        Mua ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Mô tả Sản phẩm</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="spec-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">Thông số Kỹ thuật</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Đánh giá (<?php echo $reviewSummary['total_reviews']; ?>)</button>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div id="description-content" class="position-relative">
                        <div class="description-inner-content">
                            <?php echo $product['detail_description']; // In nội dung HTML 
                            ?>
                        </div>
                        <div class="collapse-gradient"></div>
                    </div>
                    <button id="toggle-description" class="btn btn-link p-0 fw-bold mt-2">Xem thêm</button>
                </div>
                <div class="tab-pane fade" id="specs" role="tabpanel">
                    <?php if (empty($productSpecs)): ?>
                        <p>Chưa có thông số kỹ thuật chi tiết cho sản phẩm này.</p>
                    <?php else: ?>
                        <?php foreach ($productSpecs as $groupName => $specs): ?>
                            <h5 class="text-primary mt-3 mb-2"><?php echo htmlspecialchars($groupName); ?></h5>
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <?php foreach ($specs as $spec): ?>
                                        <tr>
                                            <th scope="row" style="width: 30%"><?php echo htmlspecialchars($spec['name']); ?></th>
                                            <td><?php echo htmlspecialchars($spec['value']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <h4 class="text-primary mb-4">Đánh giá từ Khách hàng</h4>

                    <?php if ($reviewSummary['total_reviews'] > 0): ?>
                        <div class="row align-items-center mb-4 border-bottom pb-3">
                            <div class="col-md-3 text-center">
                                <h1 class="display-3 fw-bold text-primary"><?php echo number_format($reviewSummary['avg_rating'], 1); ?></h1>
                                <div class="rating fs-4 text-warning">
                                    <?php echo render_stars($reviewSummary['avg_rating']); ?>
                                </div>
                                <p class="text-muted small mt-1">(Dựa trên <?php echo $reviewSummary['total_reviews']; ?> đánh giá)</p>
                            </div>
                            <div class="col-md-9">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2"><?php echo $i; ?> sao</span>
                                        <div class="progress flex-grow-1" style="height: 10px">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: <?php echo $reviewSummary['percentages'][$i]; ?>%"
                                                aria-valuenow="<?php echo $reviewSummary['percentages'][$i]; ?>"></div>
                                        </div>
                                        <span class="ms-2 small">(<?php echo $reviewSummary[$i . '_star']; ?>)</span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card bg-light border-0 mb-5">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">Viết đánh giá của bạn</h5>

                            <?php if (!empty($isAllowedToReview)): ?>
                                <?php if ($userReview): ?>
                                    <div class="alert alert-info mb-3">
                                        <i class='bx bx-info-circle me-2'></i>
                                        Bạn đã đánh giá sản phẩm này với <?php echo $userReview['rating']; ?> sao.
                                        <?php if ($userReview['is_approved'] == 0): ?>
                                            <br><small class="text-muted">Đánh giá của bạn đang chờ được duyệt.</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <form id="review-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="rating_value" id="rating_value" value="<?php echo $userReview ? $userReview['rating'] : '0'; ?>">
                                    <?php if ($userReview): ?>
                                        <input type="hidden" name="review_id" id="review_id" value="<?php echo $userReview['review_id']; ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">1. Bạn cảm thấy thế nào về sản phẩm? (*)</label>
                                        <div id="reviewRating" class="d-flex gap-2">
                                            <?php 
                                            $userRating = $userReview ? $userReview['rating'] : 0;
                                            for ($i = 1; $i <= 5; $i++): 
                                                $starClass = $i <= $userRating ? 'bx-star' : 'bx-star';
                                                $filled = $i <= $userRating ? 'bxs' : 'bx';
                                            ?>
                                                <i class="bx <?php echo $filled; ?>-star <?php echo $i <= $userRating ? 'text-warning' : ''; ?>" 
                                                   data-rating="<?php echo $i; ?>" 
                                                   title="<?php 
                                                        if ($i == 1) echo 'Tệ';
                                                        elseif ($i == 2) echo 'Không hài lòng';
                                                        elseif ($i == 3) echo 'Bình thường';
                                                        elseif ($i == 4) echo 'Hài lòng';
                                                        else echo 'Tuyệt vời';
                                                   ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if ($userRating > 0): ?>
                                            <small class="text-muted">Bạn đã chọn <?php echo $userRating; ?> sao</small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">2. Nhận xét chi tiết (*)</label>
                                        <textarea class="form-control" name="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này... (Chất lượng, giao hàng, v.v)"><?php echo $userReview ? htmlspecialchars($userReview['comment']) : ''; ?></textarea>
                                    </div>

                                    <div id="review-alert-message" class="mb-2"></div>

                                    <button type="submit" id="submit-review-btn" class="btn btn-primary px-4">
                                        <i class="bx bx-send me-2"></i><?php echo $userReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá'; ?>
                                    </button>
                                </form>

                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class='bx bxs-info-circle fs-4 me-2'></i>
                                    <div>
                                        Bạn cần mua sản phẩm này và đơn hàng được giao thành công mới có thể viết đánh giá.
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="text-center py-3">
                                    <p class="mb-3 text-muted">Vui lòng đăng nhập để viết đánh giá.</p>
                                    <a href="index.php?class=login&act=login" class="btn btn-outline-primary">
                                        <i class='bx bx-user me-2'></i>Đăng nhập ngay
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h5 class="text-primary mt-5 mb-3">Tất cả đánh giá (<?php echo $reviewSummary['total_reviews']; ?>)</h5>
                    <div id="reviews-list-container">
                        <?php if (empty($reviews)): ?>
                            <p class="text-muted fst-italic">Chưa có đánh giá nào cho sản phẩm này.</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <?php 
                                $isCurrentUserReview = isset($_SESSION['user_id']) && $review['user_id'] == $_SESSION['user_id'];
                                $isPending = false;
                                ?>
                                <div class="review-item border-bottom mb-3 pb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex gap-3">
                                            <div class="avatar-placeholder bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                                                <?php
                                                // Sử dụng mb_substr để lấy ký tự đầu tiên của Tiếng Việt chính xác
                                                $name = $review['full_name'] ?? 'U'; // Nếu không có tên thì hiện chữ U
                                                echo mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'));
                                                ?>
                                            </div>
                                            <div>
                                                <strong class="text-dark d-block">
                                                    <?php echo htmlspecialchars($review['full_name']); ?>
                                                    <?php if ($isCurrentUserReview): ?>
                                                        <span class="badge bg-info text-white ms-2">Đánh giá của bạn</span>
                                                    <?php endif; ?>
                                                </strong>
                                                <div class="rating text-warning small my-1">
                                                    <?php echo render_stars($review['rating']); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block"><?php echo date('d/m/Y', strtotime($review['review_date'])); ?></small>
                                            <?php if ($isPending): ?>
                                                <small class="badge bg-warning text-dark mt-1">Đang chờ duyệt</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mt-2 ms-5">
                                        <p class="mb-0 text-secondary"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                        <?php if (isset($review['is_purchased']) && $review['is_purchased']): ?>
                                            <small class="text-success"><i class="bi bi-check-circle-fill"></i> Đã mua hàng</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Thành công!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="h5 text-success mb-3">✅ Đã thêm sản phẩm vào giỏ hàng!</p>
                <p id="modal-cart-message"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tiếp tục mua sắm</button>
                <a href="index.php?class=cart&act=cart" class="btn btn-primary">Xem Giỏ hàng</a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 0. (FIX LỖI) XỬ LÝ CHUYỂN TAB THỦ CÔNG ---
        const tabButtons = document.querySelectorAll('#productTab button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                const targetId = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });

        // --- 1. XỬ LÝ SWIPER ---
        if (document.querySelector(".mySwiper")) {
            var swiper = new Swiper(".mySwiper", {
                loop: false,
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
            });
            var swiper2 = new Swiper(".mySwiper2", {
                loop: true,
                spaceBetween: 10,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                thumbs: {
                    swiper: swiper,
                },
            });
        }

        // --- 2. XỬ LÝ MÔ TẢ ---
        const descriptionContent = document.getElementById('description-content');
        const toggleDescriptionBtn = document.getElementById('toggle-description');
        if (descriptionContent && toggleDescriptionBtn) {
            if (descriptionContent.scrollHeight > 250) {
                descriptionContent.classList.add('collapsed');
            } else {
                toggleDescriptionBtn.style.display = 'none';
                descriptionContent.classList.add('expanded');
            }
            toggleDescriptionBtn.addEventListener('click', function() {
                if (descriptionContent.classList.contains('collapsed')) {
                    descriptionContent.classList.remove('collapsed');
                    descriptionContent.classList.add('expanded');
                    this.textContent = 'Thu gọn';
                } else {
                    descriptionContent.classList.remove('expanded');
                    descriptionContent.classList.add('collapsed');
                    this.textContent = 'Xem thêm';
                }
            });
        }

        // --- 3. XỬ LÝ BIẾN THỂ (VARIANT) ---
        const variantGroups = document.querySelectorAll('.variant-group');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const selectedVariantInput = document.getElementById('selected_variant_id');
        const productIdInput = document.getElementById('product_id');
        const currentPriceEl = document.getElementById('current-price');
        const originalPriceEl = document.getElementById('original-price');
        const stockStatusEl = document.getElementById('stock-status');
        const mainImageSlider = document.getElementById('main-image-slider');

        function formatVND(number) {
            if (number === null || number === undefined) return '0 VNĐ';
            return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VNĐ';
        }

        function updateHeaderCartCount(newCount) {
            const cartIcon = document.getElementById('header-cart-icon');
            let countBadge = document.getElementById('header-cart-count');
            if (newCount > 0) {
                if (countBadge) {
                    countBadge.textContent = newCount;
                } else {
                    countBadge = document.createElement('span');
                    countBadge.id = 'header-cart-count';
                    countBadge.className = 'badge rounded-pill bg-danger';
                    countBadge.textContent = newCount;
                    if (cartIcon) cartIcon.appendChild(countBadge);
                }
            } else if (countBadge) {
                countBadge.remove();
            }
        }

        function fetchVariantDetails() {
            const selectedOptions = [];
            variantGroups.forEach(group => {
                const selected = group.querySelector('.variant-option.selected');
                if (selected) selectedOptions.push(selected.dataset.valueId);
            });

            if (selectedOptions.length !== variantGroups.length) {
                if (addToCartBtn) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Vui lòng chọn đủ tùy chọn';
                }
                return;
            }

            const formData = new FormData();
            formData.append('product_id', productIdInput.value);
            selectedOptions.forEach(id => formData.append('options[]', id));

            fetch('index.php?class=product&act=getVariantDetails', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const variant = data.data;
                        if (currentPriceEl) currentPriceEl.textContent = formatVND(variant.current_variant_price);
                        if (originalPriceEl) {
                            if (variant.original_variant_price > variant.current_variant_price) {
                                originalPriceEl.textContent = formatVND(variant.original_variant_price);
                                originalPriceEl.style.display = 'inline';
                            } else {
                                originalPriceEl.style.display = 'none';
                            }
                        }
                        if (stockStatusEl) {
                            if (variant.quantity > 0) {
                                stockStatusEl.textContent = `Còn hàng (${variant.quantity} sản phẩm)`;
                                stockStatusEl.className = 'badge bg-success';
                                if (addToCartBtn) {
                                    addToCartBtn.disabled = false;
                                    addToCartBtn.textContent = 'Thêm vào Giỏ hàng';
                                }
                            } else {
                                stockStatusEl.textContent = 'Hết hàng';
                                stockStatusEl.className = 'badge bg-danger';
                                if (addToCartBtn) {
                                    addToCartBtn.disabled = true;
                                    addToCartBtn.textContent = 'Hết hàng';
                                }
                            }
                        }
                        if (selectedVariantInput) selectedVariantInput.value = variant.variant_id;

                        if (variant.image_url && mainImageSlider) {
                            const slideIndex = Array.from(mainImageSlider.querySelectorAll('img')).findIndex(
                                img => img.src.includes(variant.image_url)
                            );
                            if (slideIndex !== -1 && typeof swiper2 !== 'undefined') {
                                swiper2.slideToLoop(slideIndex);
                            }
                        }
                    } else {
                        if (stockStatusEl) {
                            stockStatusEl.textContent = 'Phiên bản không tồn tại';
                            stockStatusEl.className = 'badge bg-warning text-dark';
                        }
                        if (addToCartBtn) {
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Không khả dụng';
                        }
                        if (selectedVariantInput) selectedVariantInput.value = 0;
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }

        document.querySelectorAll('.variant-option').forEach(option => {
            option.addEventListener('click', function() {
                this.closest('.variant-options-container').querySelectorAll('.variant-option').forEach(
                    el => el.classList.remove('selected')
                );
                this.classList.add('selected');
                fetchVariantDetails();
            });
        });

        // Gọi lần đầu nếu có variant
        if (variantGroups.length > 0) {
            fetchVariantDetails();
        }

        // --- 4. XỬ LÝ ADD TO CART ---
        const addToCartModalEl = document.getElementById('addToCartModal');
        let addToCartModal;
        if (addToCartModalEl) {
            addToCartModal = new bootstrap.Modal(addToCartModalEl);
        }
        const modalCartMessage = document.getElementById('modal-cart-message');

        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(event) {
                event.stopImmediatePropagation();
                const productId = productIdInput.value;
                const variantId = selectedVariantInput ? selectedVariantInput.value : 0;
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? quantityInput.value : 1;

                if (variantGroups.length > 0 && variantId == 0) {
                    alert('Vui lòng chọn đầy đủ các tùy chọn sản phẩm.');
                    return;
                }
                if (quantity <= 0) {
                    alert('Số lượng phải lớn hơn 0.');
                    return;
                }

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('variant_id', variantId);
                formData.append('quantity', quantity);

                fetch('index.php?class=cart&act=addToCart', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateHeaderCartCount(data.data.total_quantity);
                            const productNameEl = document.querySelector('h1.text-primary');
                            const productName = productNameEl ? productNameEl.textContent : 'Sản phẩm';
                            if (modalCartMessage && addToCartModal) {
                                modalCartMessage.textContent = `${productName} (Số lượng: ${quantity}) đã được thêm.`;
                                addToCartModal.show();
                            } else {
                                alert(`${productName} đã được thêm vào giỏ hàng!`);
                            }
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Đã có lỗi xảy ra khi thêm vào giỏ hàng.');
                    });
            });
        }

        // --- 5. XỬ LÝ FORM ĐÁNH GIÁ (FIXED) ---
        const reviewForm = document.getElementById('review-form');
        const ratingStars = document.querySelectorAll('#reviewRating i');
        const ratingValueInput = document.getElementById('rating_value');
        const submitReviewBtn = document.getElementById('submit-review-btn');
        const reviewAlert = document.getElementById('review-alert-message');

        if (reviewForm) {
            // 5.0. Khởi tạo sao nếu đã có đánh giá
            if (ratingValueInput && ratingValueInput.value > 0) {
                const currentRating = parseInt(ratingValueInput.value);
                ratingStars.forEach(s => {
                    const starRating = parseInt(s.dataset.rating);
                    if (starRating <= currentRating) {
                        s.classList.remove('bx-star');
                        s.classList.add('bxs-star', 'text-warning');
                    } else {
                        s.classList.remove('bxs-star', 'text-warning');
                        s.classList.add('bx-star');
                    }
                });
            }

            // 5.1. Xử lý click sao
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    // Cập nhật giá trị input ẩn
                    if (ratingValueInput) {
                        ratingValueInput.value = rating;
                        console.log("Đã chọn sao: " + rating); // Debug log
                    }

                    // Tô màu sao
                    ratingStars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.classList.remove('bx-star');
                            s.classList.add('bxs-star', 'text-warning');
                        } else {
                            s.classList.remove('bxs-star', 'text-warning');
                            s.classList.add('bx-star');
                        }
                    });
                });
            });

            // 5.2. Xử lý Submit Form
            reviewForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // KIỂM TRA QUAN TRỌNG: Nếu chưa chọn sao thì chặn luôn
                const currentRating = ratingValueInput ? ratingValueInput.value : 0;
                if (currentRating == 0) {
                    if (reviewAlert) {
                        reviewAlert.innerHTML = `<div class="alert alert-danger"><i class='bx bxs-error-circle'></i> Vui lòng chọn số sao trước khi gửi!</div>`;
                    } else {
                        alert('Vui lòng chọn số sao trước khi gửi!');
                    }
                    return; // Dừng lại, không gửi lên server
                }

                submitReviewBtn.disabled = true;
                submitReviewBtn.textContent = 'Đang gửi...';
                if (reviewAlert) reviewAlert.innerHTML = '';

                const formData = new FormData(reviewForm);

                fetch('index.php?class=review&act=addReview', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (reviewAlert) reviewAlert.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            // Reload trang sau 1.5 giây để hiển thị đánh giá mới
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            if (reviewAlert) reviewAlert.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                            submitReviewBtn.disabled = false;
                            const reviewIdInput = document.getElementById('review_id');
                            const isUpdate = reviewIdInput && reviewIdInput.value;
                            submitReviewBtn.innerHTML = `<i class="bx bx-send me-2"></i>${isUpdate ? 'Cập nhật đánh giá' : 'Gửi đánh giá'}`;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        if (reviewAlert) reviewAlert.innerHTML = `<div class="alert alert-danger">Lỗi kết nối server.</div>`;
                        submitReviewBtn.disabled = false;
                        const reviewIdInput = document.getElementById('review_id');
                        const isUpdate = reviewIdInput && reviewIdInput.value;
                        submitReviewBtn.innerHTML = `<i class="bx bx-send me-2"></i>${isUpdate ? 'Cập nhật đánh giá' : 'Gửi đánh giá'}`;
                    });
            });
        }
    });
    // --- 6. XỬ LÝ NÚT YÊU THÍCH (Hoàn thiện) ---
    const favBtns = document.querySelectorAll('.favorite-toggle-btn');
    favBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');

            // Kiểm tra trạng thái hiện tại dựa vào class icon hoặc button
            const isActive = this.classList.contains('active-fav') || icon.classList.contains('bxs-heart');

            if (!isActive) {
                // CHUYỂN THÀNH ACTIVE (Đỏ)
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-outline-danger', 'active-fav');

                icon.classList.remove('bx-heart');
                icon.classList.add('bxs-heart');
            } else {
                // CHUYỂN THÀNH INACTIVE (Xám)
                this.classList.remove('btn-outline-danger', 'active-fav');
                this.classList.add('btn-outline-secondary');

                icon.classList.remove('bxs-heart');
                icon.classList.add('bx-heart');
            }

            // Gửi Ajax
            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('index.php?class=favorite&act=toggle', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateBadge('.favorite-count-badge', data.count);
                    } else {
                        // Revert nếu lỗi
                        if (data.message === 'Vui lòng đăng nhập') {
                            alert('Vui lòng đăng nhập!');
                            window.location.href = 'index.php?class=login&act=login';
                        }
                        // Nếu cần revert UI thì làm ngược lại logic trên ở đây
                    }
                })
                .catch(err => console.error(err));
        });
    });

    // --- 7. XỬ LÝ NÚT SO SÁNH (Hoàn thiện) ---
    const compBtns = document.querySelectorAll('.compare-toggle-btn');
    compBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;

            // Kiểm tra trạng thái hiện tại
            const isActive = this.classList.contains('btn-primary');

            if (!isActive) {
                // CHUYỂN THÀNH ACTIVE (Xanh đặc)
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
            } else {
                // CHUYỂN THÀNH INACTIVE (Xám rỗng)
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-secondary');
            }

            // Gửi Ajax
            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('index.php?class=compare&act=toggle', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateBadge('.badge-compare', data.count);
                    }
                })
                .catch(err => console.error(err));
        });
    });
</script>