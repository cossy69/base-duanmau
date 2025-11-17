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

                    <h5 class="text-primary mt-5 mb-3">Tất cả đánh giá (<?php echo $reviewSummary['total_reviews']; ?>)</h5>
                    <div id="reviews-list-container">
                        <?php if (empty($reviews)): ?>
                            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item border-bottom mb-3 pb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-primary"><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                            <div class="rating text-warning small">
                                                <?php echo render_stars($review['rating']); ?>
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['review_date'])); ?></small>
                                    </div>
                                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
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

        // --- 1. XỬ LÝ SWIPER (SLIDER ẢNH) ---
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

        // --- 2. XỬ LÝ "XEM THÊM" MÔ TẢ ---
        const descriptionContent = document.getElementById('description-content');
        const toggleDescriptionBtn = document.getElementById('toggle-description');
        if (descriptionContent && toggleDescriptionBtn) {
            // Kiểm tra chiều cao thực tế
            if (descriptionContent.scrollHeight > 250) {
                descriptionContent.classList.add('collapsed');
            } else {
                toggleDescriptionBtn.style.display = 'none'; // Ẩn nút nếu ko đủ dài
                descriptionContent.classList.add('expanded'); // Hiện hết
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

        // --- 3. XỬ LÝ CHỌN BIẾN THỂ (VARIANT) ---
        const variantGroups = document.querySelectorAll('.variant-group');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const selectedVariantInput = document.getElementById('selected_variant_id');
        const productIdInput = document.getElementById('product_id');
        const currentPriceEl = document.getElementById('current-price');
        const originalPriceEl = document.getElementById('original-price');
        const stockStatusEl = document.getElementById('stock-status');
        const mainImageSlider = document.getElementById('main-image-slider');

        // Hàm helper format tiền
        function formatVND(number) {
            if (number === null || number === undefined) {
                return '0 VNĐ';
            }
            // Chuyển số thành chuỗi
            let numStr = String(number);

            // Dùng regex để thêm dấu chấm ('.') phân cách hàng nghìn
            let formattedStr = numStr.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            return formattedStr + ' VNĐ';
        }

        function updateHeaderCartCount(newCount) {
            const cartIcon = document.getElementById('header-cart-icon');
            let countBadge = document.getElementById('header-cart-count');

            if (newCount > 0) {
                if (countBadge) {
                    countBadge.textContent = newCount;
                } else {
                    // Nếu chưa có, tạo mới
                    countBadge = document.createElement('span');
                    countBadge.id = 'header-cart-count';
                    countBadge.className = 'badge rounded-pill bg-danger';
                    countBadge.textContent = newCount;
                    if (cartIcon) {
                        cartIcon.appendChild(countBadge);
                    }
                }
            } else {
                // Nếu số lượng là 0, xóa badge
                if (countBadge) {
                    countBadge.remove();
                }
            }
        }
        // Hàm gọi API để lấy thông tin biến thể
        function fetchVariantDetails() {
            const selectedOptions = [];
            variantGroups.forEach(group => {
                const selected = group.querySelector('.variant-option.selected');
                if (selected) {
                    selectedOptions.push(selected.dataset.valueId);
                }
            });

            // Kiểm tra xem tất cả các nhóm đã được chọn chưa
            if (selectedOptions.length !== variantGroups.length) {
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'Vui lòng chọn đủ tùy chọn';
                return;
            }

            const formData = new FormData();
            formData.append('product_id', productIdInput.value);
            // Gửi mảng các value_id đã chọn
            selectedOptions.forEach(id => formData.append('options[]', id));

            // Gọi API
            fetch('index.php?class=product&act=getVariantDetails', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const variant = data.data;

                        // Cập nhật giá
                        currentPriceEl.textContent = formatVND(variant.current_variant_price);
                        if (variant.original_variant_price > variant.current_variant_price) {
                            originalPriceEl.textContent = formatVND(variant.original_variant_price);
                            originalPriceEl.style.display = 'inline';
                        } else {
                            originalPriceEl.style.display = 'none';
                        }

                        // Cập nhật kho
                        if (variant.quantity > 0) {
                            stockStatusEl.textContent = `Còn hàng (${variant.quantity} sản phẩm)`;
                            stockStatusEl.className = 'badge bg-success';
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = 'Thêm vào Giỏ hàng';
                        } else {
                            stockStatusEl.textContent = 'Hết hàng';
                            stockStatusEl.className = 'badge bg-danger';
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Hết hàng';
                        }

                        // Cập nhật variant_id để thêm vào giỏ
                        selectedVariantInput.value = variant.variant_id;

                        // (Nâng cao) Cập nhật ảnh chính
                        if (variant.image_url) {
                            // Tìm slide có ảnh này và di chuyển tới đó
                            const slideIndex = Array.from(mainImageSlider.querySelectorAll('img')).findIndex(
                                img => img.src.includes(variant.image_url)
                            );
                            if (slideIndex !== -1 && swiper2.realIndex !== slideIndex) {
                                swiper2.slideToLoop(slideIndex);
                            }
                        }

                    } else {
                        // Nếu không tìm thấy (ví dụ: Titan Xanh + 1TB không tồn tại)
                        stockStatusEl.textContent = 'Phiên bản không tồn tại';
                        stockStatusEl.className = 'badge bg-warning text-dark';
                        addToCartBtn.disabled = true;
                        addToCartBtn.textContent = 'Không khả dụng';
                        selectedVariantInput.value = 0;
                    }
                })
                .catch(err => {
                    console.error('Lỗi fetchVariantDetails:', err);
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Đã có lỗi xảy ra';
                });
        }

        // Thêm sự kiện click cho các nút tùy chọn
        document.querySelectorAll('.variant-option').forEach(option => {
            option.addEventListener('click', function() {
                // Xóa 'selected' khỏi các anh em của nó
                this.closest('.variant-options-container').querySelectorAll('.variant-option').forEach(
                    el => el.classList.remove('selected')
                );
                // Thêm 'selected' cho cái được click
                this.classList.add('selected');

                // Gọi hàm fetch
                fetchVariantDetails();
            });
        });

        // Gọi fetch lần đầu khi tải trang (để lấy giá/kho của tùy chọn mặc định)
        fetchVariantDetails();

        // --- 4. XỬ LÝ THÊM VÀO GIỎ HÀNG ---
        const addToCartModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
        const modalCartMessage = document.getElementById('modal-cart-message');

        addToCartBtn.addEventListener('click', function(event) {
            event.stopImmediatePropagation();
            const productId = productIdInput.value;
            const variantId = selectedVariantInput.value;
            const quantity = document.getElementById('quantity').value;

            if (variantId == 0) {
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
                        // Cập nhật icon giỏ hàng trên header
                        updateHeaderCartCount(data.data.total_quantity);

                        // Hiển thị modal
                        const productName = document.querySelector('h1.text-primary').textContent;
                        modalCartMessage.textContent = `${productName} (Số lượng: ${quantity}) đã được thêm.`;
                        addToCartModal.show();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Lỗi addToCart:', err);
                    alert('Đã có lỗi xảy ra khi thêm vào giỏ hàng.');
                });
        });
        // --- 5. (MỚI) XỬ LÝ FORM ĐÁNH GIÁ ---
        const reviewForm = document.getElementById('review-form');
        const ratingStars = document.querySelectorAll('#reviewRating i');
        const ratingValueInput = document.getElementById('rating_value');
        const submitReviewBtn = document.getElementById('submit-review-btn');
        const reviewAlert = document.getElementById('review-alert-message');

        if (reviewForm) {

            // 5.1. Xử lý click sao
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingValueInput.value = rating; // Cập nhật input ẩn

                    // Tô màu sao
                    ratingStars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.classList.remove('bi-star');
                            s.classList.add('bi-star-fill');
                        } else {
                            s.classList.remove('bi-star-fill');
                            s.classList.add('bi-star');
                        }
                    });
                });
            });

            // 5.2. Xử lý Submit Form
            reviewForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Ngăn submit
                submitReviewBtn.disabled = true;
                submitReviewBtn.textContent = 'Đang gửi...';
                reviewAlert.innerHTML = ''; // Xóa thông báo cũ

                const formData = new FormData(reviewForm);

                fetch('index.php?class=review&act=addReview', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            reviewAlert.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            reviewForm.reset(); // Xóa form
                            // Reset lại sao
                            ratingStars.forEach(s => {
                                s.classList.remove('bi-star-fill');
                                s.classList.add('bi-star');
                            });
                            ratingValueInput.value = 0;
                        } else {
                            reviewAlert.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi gửi đánh giá:', err);
                        reviewAlert.innerHTML = `<div class="alert alert-danger">Đã có lỗi xảy ra, vui lòng thử lại.</div>`;
                    })
                    .finally(() => {
                        submitReviewBtn.disabled = false;
                        submitReviewBtn.textContent = 'Gửi đánh giá';
                    });
            });
        }
    });
</script>