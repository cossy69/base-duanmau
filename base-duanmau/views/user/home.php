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

<section>
    <div class="banner">
        <div class="nen_banner">
            <div class="nd_banner">
                <p data-aos="fade-right" class="p_bottom">World of technology</p>
                <h2 data-aos="fade-down">What do you need?</h2>
                <form
                    data-aos="fade-right"
                    id="search"
                    class="d-flex align-items-center"
                    bindsubmit=""
                    role="search">
                    <input type="search" placeholder="Please search" />
                    <button type="submit">
                        <i
                            style="font-size: 1.7vw; color: white"
                            class="bxr bx-search"></i>
                    </button>
                </form>
            </div>
            <div class="anh_banner" data-aos="fade-left">
                <img
                    style="width: 100%; height: 100%; object-fit: cover"
                    src="image/photo-5-163698973434139514998.jpg"
                    alt="" />
            </div>
        </div>
    </div>
</section>

<div style="width: 90%" class="service">
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-truck"></i>
        <h4>Free Shipping</h4>
        <p class="p_bottom">Free for orders over 3,000,000 VND</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-shield"></i>
        <h4>Secure Payment</h4>
        <p class="p_bottom">100% secure payment</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-swap-horizontal"></i>
        <h4>30-day return</h4>
        <p class="p_bottom">30-day money back guarantee</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-phone"></i>
        <h4>Hỗ trợ 24/7</h4>
        <p class="p_bottom">Fast support anytime</p>
    </div>
</div>

<article
    style="width: 90%; margin-bottom: 100px"
    id="product"
    class="d-flex flex-column align-items-center gap-4">
    <div style="width: 100%" class="tieu_de d-flex justify-content-between">
        <h2 data-aos="fade-down-right">Our Organic Products</h2>
        <div data-aos="fade-down-left" class="pro">
            <button class="btn btn-outline-primary active" data-brand-id="all">All Product</button>

            <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                    <button class="btn btn-outline-primary" data-brand-id="<?php echo $brand['brand_id']; ?>">
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="product">
        <?php
        // Sửa: Dùng file partial
        // Gán $newProducts (từ controller) vào biến $products mà partial sẽ dùng
        $products = $newProducts;
        include 'views/user/partials/_product_card.php';
        ?>
    </div>
    <a href="index.php?ctl=user&class=product&act=product">View all products</a>
</article>

<section>
    <div class="banner2">
        <div class="nen_banner2">
            <div class="nd_banner2">
                <p data-aos="fade-right" class="p_bottom">Hold is quality</p>
                <h2 data-aos="fade-down">
                    Because style doesn't need much talking
                </h2>
            </div>
            <div class="anh_banner2" data-aos="fade-left">
                <img
                    style="width: 100%; height: 100%; object-fit: cover"
                    src="image/download-removebg-preview.png"
                    alt="" />
            </div>
        </div>
    </div>
</section>

<article id="slide_pro">
    <div
        style="width: 100%; margin-bottom: 10px"
        class="tieu_de d-flex justify-content-between">
        <h2>Good price product</h2>
    </div>
    <div class="slide_pro swiper">
        <div class="swiper-wrapper">
            <?php if (empty($bestDeals)): ?>
                <div class="swiper-slide">
                    <p>Không có sản phẩm giảm giá nào.</p>
                </div>
            <?php else: ?>
                <?php foreach ($bestDeals as $product): ?>
                    <div class="swiper-slide">
                        <div class="card" style="width: 100%; position: relative; height: 100%;">
                            <a style="text-decoration: none; display: flex; flex-direction: column; height: 100%;" href="index.php?class=product&act=product_detail&id=<?php echo $product['product_id']; ?>">

                                <?php if ($product['discount_amount'] > 0): ?>
                                    <p class="p_bottom giam_gia">-<?php echo round($product['discount_percent']); ?>%</p>
                                <?php endif; ?>

                                <img
                                    src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" />

                                <div class="card-body" style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <h5 style="color: black" class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>

                                    <div class="price d-flex align-items-center gap-2" style="min-height: 4.5em;">
                                        <p style="font-size: 22px; font-weight: 500; color: rgb(255, 18, 18);" class="p_bottom gia gia_cu">
                                            <?php echo $product['current_price']; ?> VNĐ
                                        </p>
                                        <?php if ($product['discount_amount'] > 0): ?>
                                            <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                                <?php echo $product['original_price']; ?> VNĐ
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p style="color: black; margin-bottom: 10px" class="card-text">
                                        Mô tả ngắn cho sản phẩm này...
                                    </p>

                                    <div class="action_pro d-flex justify-content-between" style="margin-top: auto;">
                                        <div class="d-flex justify-content-between gap-3">
                                            <button><i class="bxr bx-heart"></i></button>
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
            <?php endif; ?>

        </div>
        <div class="btn1 swiper-button-next"></div>
        <div class="btn1 swiper-button-prev"></div>
    </div>
    <div class="btn1 swiper-button-next"></div>
    <div class="btn1 swiper-button-prev"></div>
    </div>
</article>

<article id="news" class="d-flex flex-column align-items-center">
    <div class="news">
        <div
            style="width: 100%; margin-bottom: 10px"
            class="tieu_de d-flex justify-content-between">
            <h2>Breaking news</h2>
        </div>
        <div class="sum_new">

            <?php if (!empty($mainPost)): ?>
                <div class="new_trai d-flex flex-column gap-2">
                    <a class="img_n_t" href="index.php?class=news&act=new_detail&id=<?php echo $mainPost['post_id']; ?>">
                        <img src="<?php echo htmlspecialchars($mainPost['thumbnail_url'] ?? 'image/new.webp'); ?>" alt="<?php echo htmlspecialchars($mainPost['title']); ?>" />
                    </a>
                    <a class="title_n_t" href="index.php?class=news&act=new_detail&id=<?php echo $mainPost['post_id']; ?>">
                        <?php echo htmlspecialchars($mainPost['title']); ?>
                    </a>
                    <div class="d_u d-flex gap-4">
                        <div class="date_n d-flex align-items-center gap-1">
                            <i class="bxr bx-calendar-minus"></i>
                            <p class="p_bottom"><?php echo date('d/m/Y', strtotime($mainPost['created_at'])); ?></p>
                        </div>
                        <div class="user_n d-flex align-items-center gap-1">
                            <i class="bxr bx-user-square"></i>
                            <p classm="p_bottom"><?php echo htmlspecialchars($mainPost['author_name']); ?></p>
                        </div>
                    </div>
                    <p class="p_bottom text_new_t">
                        <?php echo htmlspecialchars($this->truncate($mainPost['content'], 250)); ?>
                    </p>
                </div>
            <?php else: ?>
                <p>Không có bài viết nào để hiển thị.</p>
            <?php endif; ?>

            <div class="new_phai">
                <?php if (!empty($sidePosts)): ?>
                    <?php foreach ($sidePosts as $post): ?>
                        <div class="new d-flex gap-3">
                            <a class="img_n_p" href="index.php?class=news&act=new_detail&id=<?php echo $post['post_id']; ?>">
                                <img src="<?php echo htmlspecialchars($post['thumbnail_url'] ?? 'image/new.webp'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" />
                            </a>
                            <div class="nd_n_p">
                                <a class="title_n_p" href="index.php?class=news&act=new_detail&id=<?php echo $post['post_id']; ?>">
                                    <?php echo htmlspecialchars($this->truncate($post['title'], 150)); ?>
                                </a>
                                <div class="d_u d-flex gap-4">
                                    <div class="date_n d-flex align-items-center gap-1">
                                        <i class="bxr bx-calendar-minus"></i>
                                        <p class="p_bottom"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></p>
                                    </div>
                                    <div class="user_n d-flex align-items-center gap-1">
                                        <i class="bxr bx-user-square"></i>
                                        <p class="p_bottom"><?php echo htmlspecialchars($post['author_name']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="link_new">
                    <a href="index.php?class=news&act=news">View all news</a>
                </div>
            </div>
        </div>
    </div>
</article>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- HÀM 1: Cập nhật icon header ---
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
            } else {
                if (countBadge) {
                    countBadge.remove();
                }
            }
        }

        // --- Lấy element toast ---
        const toastElement = document.getElementById('tb_Toast');
        const toastBody = document.getElementById('toastTb');
        // Khởi tạo toast của Bootstrap
        const bsToast = new bootstrap.Toast(toastElement, {
            delay: 3000
        }); // 3 giây

        // --- HÀM 2: Xử lý "Add to cart" ---
        const allAddButtons = document.querySelectorAll('.add-to-cart-btn');
        allAddButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const productId = this.dataset.productId;
                const variantId = this.dataset.variantId;
                const quantity = 1;

                // Sửa: Dùng 0 (như lần trước mình thống nhất)
                if (variantId === null || variantId === undefined) {
                    alert('Lỗi: Không tìm thấy biến thể sản phẩm.');
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
                        // === SỬA TỪ ĐÂY ===
                        if (data.status === 'success') {
                            // 1. Cập nhật icon giỏ hàng
                            const newCount = data.data.total_quantity;
                            updateHeaderCartCount(newCount);

                            // 2. Hiển thị toast thành công
                            toastBody.textContent = '✅ Đã thêm sản phẩm vào giỏ!';
                            bsToast.show();

                        } else {
                            // Hiển thị lỗi trên toast
                            toastBody.textContent = '❌ Lỗi: ' + data.message;
                            bsToast.show();
                        }
                        // === ĐẾN ĐÂY ===
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastBody.textContent = '❌ Lỗi: ' + error.message;
                        bsToast.show();
                    });
            });
        });

        // --- HÀM 3: Xử lý lọc brand (giữ nguyên) ---
        const brandButtonsContainer = document.querySelector('.pro');
        const productContainer = document.querySelector('.product');

        if (brandButtonsContainer) {
            brandButtonsContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON') {
                    e.preventDefault();
                    brandButtonsContainer.querySelector('.active').classList.remove('active');
                    e.target.classList.add('active');

                    const brandId = e.target.dataset.brandId;
                    const formData = new FormData();
                    formData.append('brand_id', brandId);
                    productContainer.innerHTML = '<p style="text-align: center; width: 100%;">Đang tải sản phẩm...</p>';

                    fetch('index.php?class=home&act=filterProducts', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(html => {
                            productContainer.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Lỗi khi lọc:', error);
                            productContainer.innerHTML = '<p style="text-align: center; width: 100%;">Lỗi khi tải sản phẩm. Vui lòng thử lại.</p>';
                        });
                }
            });
        }
    });
</script>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
    <div
        id="tb_Toast"
        class="toast align-items-center text-white border-0 bg-primary"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastTb"></div>
            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>