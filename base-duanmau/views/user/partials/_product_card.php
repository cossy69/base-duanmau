<?php if (empty($products)): ?>
    <p>Không có sản phẩm nào để hiển thị.</p>
<?php else: ?>
    <?php foreach ($products as $product): ?>
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
                    <div class="action_pro d-flex justify-content-between">
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
                    <?php if ($product['discount_amount'] > 0): ?>
                        <p class="p_bottom giam_gia">-<?php echo round($product['discount_percent']); ?>%</p>
                    <?php endif; ?>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>