<div class="quick-buttons">
</div>

<div class="container my-5">
    <h1 class="fw-bolder mb-4 text-dark text-center">
        <i class="bx bxs-heart text-danger me-2"></i> Sản Phẩm Yêu Thích Của Bạn (<?php echo count($favoriteProducts); ?>)
    </h1>

    <?php if (empty($favoriteProducts)): ?>
        <p class="text-center text-secondary mb-5">Bạn chưa có sản phẩm yêu thích nào.</p>
    <?php else: ?>
        <p class="text-center text-secondary mb-5">Quản lý các sản phẩm bạn đã đánh dấu để mua sau.</p>
    <?php endif; ?>

    <div class="row g-4" id="favorite-product-list">

        <?php foreach ($favoriteProducts as $product): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4" id="fav-product-<?php echo $product['product_id']; ?>">

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
                                    <?php echo $product['current_price']; ?>
                                </p>
                                <?php if ($product['discount_amount'] > 0): ?>
                                    <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                        <?php echo $product['original_price']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="action_pro d-flex justify-content-between" style="margin-top: auto;">
                                <div class="d-flex justify-content-between gap-3">
                                    <button class="favorite-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                        <i class="bxr bx-heart active_i"></i>
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

            </div>
        <?php endforeach; ?>

    </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 10001">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy toast (script này copy từ footter_link.php)
        const toastElement = document.getElementById('tb_Toast');
        const toastBody = document.getElementById('toastTb');
        const bsToast = new bootstrap.Toast(toastElement, {
            delay: 3000
        });
        // Lấy icon header
        const headerBadge = document.querySelector('.favorite-count-badge');
        const pageTitleCount = document.querySelector('h1.fw-bolder');

        document.getElementById('favorite-product-list').addEventListener('click', function(event) {
            // Chỉ bắt sự kiện từ nút trái tim
            const heartButton = event.target.closest('.favorite-toggle-btn');
            if (!heartButton) return;

            event.preventDefault(); // Ngăn link <a>
            event.stopImmediatePropagation(); // Ngăn script global (ở footer) chạy

            const productId = heartButton.dataset.productId;
            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('index.php?class=favorite&act=toggleFavorite', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(json => {
                    if (json.status === 'success') {
                        toastBody.textContent = '✅ ' + json.message;
                        bsToast.show();

                        // Nếu hành động là 'removed'
                        if (json.data.action === 'removed') {
                            // 1. Xóa card khỏi DOM
                            const cardToRemove = document.getElementById('fav-product-' + productId);
                            if (cardToRemove) {
                                cardToRemove.style.opacity = '0';
                                setTimeout(() => cardToRemove.remove(), 300);
                            }

                            // 2. Cập nhật số lượng trên header
                            if (headerBadge) {
                                if (json.data.count > 0) {
                                    headerBadge.textContent = json.data.count;
                                } else {
                                    headerBadge.remove(); // Xóa nếu về 0
                                }
                            }

                            // 3. Cập nhật tiêu đề trang
                            if (pageTitleCount) {
                                pageTitleCount.innerHTML = `<i class="bx bxs-heart text-danger me-2"></i> Sản Phẩm Yêu Thích Của Bạn (${json.data.count})`;
                            }
                        }

                    } else {
                        toastBody.textContent = '❌ ' + json.message;
                        bsToast.show();
                    }
                });
        });
    });
</script>