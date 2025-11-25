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

<section>
    <div class="banner">
        <div class="nen_banner">
            <div class="nd_banner">
                <p data-aos="fade-right" class="p_bottom">Thế Giới Công Nghệ - Ngay Trong Tầm Tay</p>
                <h2 data-aos="fade-down">Bạn cần tìm sản phẩm nào?</h2>
                <form
                    data-aos="fade-right"
                    id="search"
                    class="d-flex align-items-center"
                    action="index.php"
                    method="GET"
                    role="search">

                    <input type="hidden" name="class" value="search">
                    <input type="hidden" name="act" value="search">

                    <input
                        type="search"
                        name="keyword"
                        placeholder="Tìm kiếm sản phẩm..."
                        value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>"
                        required />

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
        <p class="p_bottom">Miễn phí vận chuyển nội thành</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-shield"></i>
        <h4>Giao Dịch An Toàn</h4>
        <p class="p_bottom">100% bảo mật, uy tín</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-swap-horizontal"></i>
        <h4>30 ngày hoàn trả</h4>
        <p class="p_bottom">Trả hàng trong vòng 30 ngày</p>
    </div>
    <div class="ser d-flex flex-column align-items-center gap-2">
        <i class="bxr bxs-phone"></i>
        <h4>Hỗ trợ 24/7</h4>
        <p class="p_bottom">Tiếp nhận, trả lời nhanh chóng</p>
    </div>
</div>

<article
    style="width: 90%; margin-bottom: 100px"
    id="product"
    class="d-flex flex-column align-items-center gap-4">
    <div style="width: 100%" class="tieu_de d-flex justify-content-between">
        <h2 data-aos="fade-down-right">Các Sản Phẩm Đang Bán</h2>
        <div data-aos="fade-down-left" class="pro">
            <button class="btn btn-outline-primary active" data-brand-id="all">Tất Cả Sản Phẩm</button>

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
    <a href="index.php?ctl=user&class=product&act=product">Xem tất cả sản phẩm</a>
</article>

<section>
    <div class="banner2">
        <div class="nen_banner2">
            <div class="nd_banner2">
                <p data-aos="fade-right" class="p_bottom">Chất lượng trong tầm giá</p>
                <h2 data-aos="fade-down">
                    Flop quá thì ghi tên anh vào!
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
        <h2>Sản Phẩm Giá Tốt</h2>
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
                                            <?php echo number_format($product['current_price'], 0, ',', '.'); ?> VNĐ
                                        </p>

                                        <?php if ($product['discount_amount'] > 0): ?>
                                            <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                                <?php echo number_format($product['original_price'], 0, ',', '.'); ?> VNĐ
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p style="color: black; margin-bottom: 10px; min-height: 3em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" class="card-text">
                                        <?php echo htmlspecialchars($product['short_description'] ?? ''); ?>
                                    </p>

                                    <div class="action_pro d-flex justify-content-between" style="margin-top: auto;">
                                        <div class="d-flex justify-content-between gap-3">
                                            <?php
                                            $isFavorited = (isset($favoriteProductIds) && in_array($product['product_id'], $favoriteProductIds));
                                            ?>
                                            <button class="favorite-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                                <i class="bxr bx-heart <?php echo $isFavorited ? 'active_i' : ''; ?>"></i>
                                            </button>
                                            <?php
                                            $isCompared = (isset($compareProductIds) && in_array($product['product_id'], $compareProductIds));
                                            ?>
                                            <button class="compare-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                                <i class="bxr bx-git-compare <?php echo $isCompared ? 'active_i' : ''; ?>"></i>
                                            </button>
                                        </div>

                                        <a href="index.php?class=product&act=product_detail&id=<?php echo $product['product_id']; ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            Xem thêm
                                        </a>
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
    </div>
</article>

<article id="news" class="d-flex flex-column align-items-center">
    <div class="news">
        <div
            style="width: 100%; margin-bottom: 10px"
            class="tieu_de d-flex justify-content-between">
            <h2>Tin Tức Mới Nhất</h2>
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
                        <?php echo htmlspecialchars($this->truncate(strip_tags($mainPost['content']), 250)); ?>
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
                    <a href="index.php?class=news&act=news">Xem tất cả tin</a>
                </div>
            </div>
        </div>
    </div>
</article>