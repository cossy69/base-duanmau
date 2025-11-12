<?php
// Giả sử anh đã gọi controller và có được $cartData
// Ví dụ:


// Tạm tính phí ship, anh có thể thay đổi logic này
$shippingFee = ($subtotal > 0) ? 30000 : 0;
$totalAmount = $subtotal + $shippingFee;

// Hàm helper để định dạng tiền tệ
function format_vnd($price)
{
    return number_format($price, 0, ',', '.') . ' VNĐ';
}
// --- Kết thúc phần dữ liệu ---
?>

<div class="container my-5">
    <div class="mb-3">
        <a href="index.php?ctl=user&class=home&act=home" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Quay về Trang chủ
        </a>
    </div>
    <h1 class="mb-4 text-primary">Giỏ hàng của bạn</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Sản phẩm</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Thành tiền</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                            <?php if (empty($cartItems)): ?>
                                <tr>
                                    <td colspan="5" class="text-center p-5">
                                        <i class="bx bx-cart fs-1 text-muted"></i>
                                        <p class="mt-2">Giỏ hàng của bạn đang trống.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-price="<?php echo $item['price']; ?>"
                                        data-variant-id="<?php echo $item['variant_id'] ?? 0; ?>"
                                        data-product-id="<?php echo $item['product_id']; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                    <small class="d-block text-muted"><?php echo htmlspecialchars($item['variant_details']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo format_vnd($item['price']); ?></td>
                                        <td>
                                            <input
                                                type="number"
                                                class="form-control quantity-input"
                                                value="<?php echo $item['quantity']; ?>"
                                                min="1"
                                                style="width: 80px" />
                                        </td>
                                        <td class="item-total"><?php echo format_vnd($item['item_total']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger remove-btn">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tóm tắt đơn hàng</h5>
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Tổng tiền sản phẩm: <span id="subtotal"><?php echo format_vnd($subtotal); ?></span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Phí vận chuyển: <span id="shipping-fee"><?php echo format_vnd($shippingFee); ?></span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center bg-light fw-bold text-primary">
                            Tổng cộng: <span id="total-amount"><?php echo format_vnd($totalAmount); ?></span>
                        </li>
                    </ul>
                    <a href="checkout.html" class="btn btn-primary w-100 mt-3 <?php echo empty($cartItems) ? 'disabled' : ''; ?>">
                        Tiến hành Thanh toán
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartBody = document.getElementById('cart-table-body');
        if (!cartBody) return;

        function formatVND(number) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(number);
        }

        function updateTotals(subtotal) {
            const shippingFee = subtotal > 0 ? 30000 : 0;
            const totalAmount = subtotal + shippingFee;
            document.getElementById('subtotal').textContent = formatVND(subtotal);
            document.getElementById('shipping-fee').textContent = formatVND(shippingFee);
            document.getElementById('total-amount').textContent = formatVND(totalAmount);
            const checkoutButton = document.querySelector('a.btn-primary');
            if (subtotal <= 0) {
                checkoutButton.classList.add('disabled');
            } else {
                checkoutButton.classList.remove('disabled');
            }
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
            } else {
                if (countBadge) {
                    countBadge.remove();
                }
            }
        }

        // SỬA HÀM NÀY: Thêm 'productId' vào tham số
        function sendRequest(action, productId, variantId, quantity = 1) {
            const formData = new FormData();
            formData.append('product_id', productId); // Giờ 'productId' đã tồn tại
            formData.append('variant_id', variantId);
            let url = '';
            if (action === 'remove') {
                url = 'index.php?class=cart&act=removeItem';
            } else if (action === 'update') {
                url = 'index.php?class=cart&act=updateQuantity';
                formData.append('quantity', quantity);
            }
            return fetch(url, {
                method: 'POST',
                body: formData
            }).then(res => res.json());
        }

        // SỬA HÀM NÀY
        cartBody.addEventListener('change', function(event) {
            if (event.target.classList.contains('quantity-input')) {
                const input = event.target;
                const quantity = parseInt(input.value);
                const row = input.closest('tr');
                const productId = row.dataset.productId;
                const variantId = row.dataset.variantId;
                const price = parseFloat(row.dataset.price);

                if (quantity > 0) {
                    sendRequest('update', productId, variantId, quantity)
                        .then(json => {
                            if (json.status === 'success') {
                                row.querySelector('.item-total').textContent = formatVND(price * quantity);
                                updateTotals(json.data.subtotal);

                                // THÊM 2 DÒNG NÀY:
                                // Giờ mình cũng cập nhật icon header khi BỚT/THÊM SỐ LƯỢNG
                                const newCount = json.data.total_quantity;
                                updateHeaderCartCount(newCount);

                            } else {
                                alert(json.message);
                            }
                        });
                } else {
                    if (confirm('Bạn muốn xóa sản phẩm này?')) {
                        handleRemoveItem(row, productId, variantId);
                    } else {
                        input.value = 1;
                    }
                }
            }
        });

        cartBody.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-btn');

            if (removeButton) {
                event.preventDefault();
                if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                    const row = removeButton.closest('tr');
                    const productId = row.dataset.productId;
                    const variantId = row.dataset.variantId;
                    handleRemoveItem(row, productId, variantId);
                }
            }
        });

        // SỬA HÀM NÀY
        function handleRemoveItem(row, productId, variantId) {
            sendRequest('remove', productId, variantId)
                .then(json => {
                    if (json.status === 'success') {
                        row.remove();

                        // SỬA 1 DÒNG NÀY: Lấy TỔNG SỐ LƯỢNG
                        const newCount = json.data.total_quantity;

                        updateTotals(json.data.subtotal);
                        updateHeaderCartCount(newCount); // Cập nhật header

                        // Giờ mình dùng total_quantity để check, không dùng items.length
                        if (newCount === 0) {
                            cartBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center p-5">
                                <i class="bx bx-cart fs-1 text-muted"></i>
                                <p class="mt-2">Giỏ hàng của bạn đang trống.</p>
                            </td>
                        </tr>`;
                        }
                    } else {
                        alert(json.message);
                    }
                });
        }
    });
</script>