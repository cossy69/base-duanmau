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
                    <div class="action_pro d-flex justify-content-between">
                        <div class="d-flex justify-content-between gap-3">
                            <?php
                            // Kiểm tra xem user có đăng nhập VÀ sản phẩm này có trong mảng yêu thích không
                            $isFavorited = (isset($favoriteProductIds) && in_array($product['product_id'], $favoriteProductIds));
                            ?>
                            <button class="favorite-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                <i class="bxr bx-heart <?php echo $isFavorited ? 'active_i' : ''; ?>"></i>
                            </button>
                            <?php
                            // Kiểm tra xem user có đăng nhập VÀ sản phẩm này có trong mảng so sánh không
                            // (Cần đảm bảo $compareProductIds được controller cung cấp)
                            $isCompared = (isset($compareProductIds) && in_array($product['product_id'], $compareProductIds));
                            ?>
                            <button class="compare-toggle-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                <i class="bxr bx-git-compare <?php echo $isCompared ? 'active_i' : ''; ?>"></i>
                            </button>
                        </div>
                        <button class="btn btn-outline-primary add-to-cart-btn"
                            data-product-id="<?php echo $product['product_id']; ?>"
                            data-variant-id="<?php echo $product['default_variant_id']; ?>">
                            Thêm vào giỏ
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