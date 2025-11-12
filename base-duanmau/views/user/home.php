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
            <button class="btn btn-outline-primary active">All Product</button>

            <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                    <button class="btn btn-outline-primary">
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="product">
        <?php if (empty($newProducts)): ?>
            <p>Không có sản phẩm nào để hiển thị.</p>
        <?php else: ?>
            <?php foreach ($newProducts as $product): ?>
                <div class="card" style="width: 100%; position: relative">
                    <a style="text-decoration: none" href="index.php?class=product&act=product_detail&id=<?php echo $product['product_id']; ?>">
                        <img
                            src="<?php echo htmlspecialchars($product['image_url']); ?>"
                            class="card-img-top"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        <div class="card-body">
                            <h5 style="color: black" class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <div class="price d-flex align-items-center gap-2">
                                <p style="font-size: 22px; font-weight: 500; color: rgb(255, 18, 18);" class="p_bottom gia gia_cu">
                                    <?php echo number_format($product['current_price']); ?> VNĐ
                                </p>
                                <?php if ($product['discount_amount'] > 0): ?>
                                    <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                        <?php echo number_format($product['original_price']); ?> VNĐ
                                    </p>
                                <?php endif; ?>
                            </div>
                            <p style="color: black; margin-bottom: 10px" class="card-text">
                                Mô tả ngắn cho sản phẩm này...
                            </p>
                            <div class="action_pro d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-3">
                                    <button><i class="bxr bx-heart"></i></button>
                                    <button><i class="bxr bx-git-compare"></i></button>
                                </div>
                                <button class="btn btn-outline-primary">Add to cart</button>
                            </div>
                            <?php if ($product['discount_amount'] > 0): ?>
                                <p class="p_bottom giam_gia">-<?php echo round($product['discount_percent']); ?>%</p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
                        <div class="card" style="width: 100%; position: relative">
                            <a style="text-decoration: none" href="index.php?class=product&act=product_detail&id=<?php echo $product['product_id']; ?>">
                                <img
                                    src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" />
                                <div class="card-body">
                                    <h5 style="color: black" class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="price d-flex align-items-center gap-2">
                                        <p style="font-size: 22px; font-weight: 500; color: rgb(255, 18, 18);" class="p_bottom gia gia_cu">
                                            <?php echo number_format($product['current_price']); ?> VNĐ
                                        </p>
                                        <?php if ($product['discount_amount'] > 0): ?>
                                            <p style="font-size: 17px; font-weight: 400; color: rgb(59, 59, 59); text-decoration: line-through;" class="p_bottom gia gia_moi">
                                                <?php echo number_format($product['original_price']); ?> VNĐ
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p style="color: black; margin-bottom: 10px" class="card-text">
                                        Mô tả ngắn cho sản phẩm này...
                                    </p>
                                    <div class="action_pro d-flex justify-content-between">
                                        <div class="d-flex justify-content-between gap-3">
                                            <button><i class="bxr bx-heart"></i></button>
                                            <button><i class="bxr bx-git-compare"></i></button>
                                        </div>
                                        <button id="addToCartBtn" class="btn btn-outline-primary">Add to cart</button>
                                    </div>
                                    <?php if ($product['discount_amount'] > 0): ?>
                                        <p class="p_bottom giam_gia">-<?php echo round($product['discount_percent']); ?>%</p>
                                    <?php endif; ?>
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