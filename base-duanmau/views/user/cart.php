<?php
// ... (Phần PHP đầu file giữ nguyên) ...
// Hàm helper để định dạng tiền tệ
function format_vnd($price)
{
    return number_format($price, 0, ',', '.') . ' VNĐ';
}
$shippingFee = 30000; // Tạm thời cố định 30k (hoặc 0 nếu anh muốn)
$totalAmount = $subtotal + $shippingFee;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 5%">
                                    <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                </th>
                                <th scope="col" style="width: 40%">Sản phẩm</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                            <?php if (empty($cartItems)): ?>
                            <?php else: ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-price="<?php echo $item['price']; ?>"
                                        data-variant-id="<?php echo $item['variant_id'] ?? 0; ?>"
                                        data-product-id="<?php echo $item['product_id']; ?>">

                                        <td class="text-center">
                                            <input class="form-check-input item-checkbox" type="checkbox">
                                        </td>

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

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if (!empty($cartItems)): ?>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-outline-danger" id="remove-selected-btn" disabled>
                                <i class="bx bx-trash"></i> Xóa (0) mục đã chọn
                            </button>
                        </div>
                    <?php endif; ?>

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

        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const removeSelectedBtn = document.getElementById('remove-selected-btn');
        const itemCheckboxes = cartBody.querySelectorAll('.item-checkbox');

        // --- SỬA 1: HÀM FORMAT TIỀN (Giống product_detail) ---
        function formatVND(number) {
            if (number === null || number === undefined || isNaN(number)) {
                number = 0;
            }
            let numStr = String(Math.round(number));
            let formattedStr = numStr.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return formattedStr + ' VNĐ';
        }

        // --- SỬA 2: HÀM CẬP NHẬT TỔNG TIỀN ---
        function updateTotals(subtotal) {
            const subtotalEl = document.getElementById('subtotal');
            const shippingFeeEl = document.getElementById('shipping-fee');
            const totalAmountEl = document.getElementById('total-amount');

            // Lấy phí vận chuyển (đã được PHP render)
            let shippingFee = 0;
            if (shippingFeeEl) {
                // Đọc text (ví dụ "30.000 VNĐ"), bỏ ký tự, lấy số
                let feeText = shippingFeeEl.textContent.replace(/[., VNĐ₫\s]/g, '');
                shippingFee = parseInt(feeText) || 0;
            }

            // Nếu subtotal là 0 (hết hàng) thì free ship
            if (subtotal === 0) {
                shippingFee = 0;
                if (shippingFeeEl) shippingFeeEl.textContent = "0 VNĐ"; // Cập nhật lại DOM
            }

            const totalAmount = subtotal + shippingFee;

            if (subtotalEl) subtotalEl.textContent = formatVND(subtotal);
            if (totalAmountEl) totalAmountEl.textContent = formatVND(totalAmount);

            // Vô hiệu hóa nút thanh toán nếu giỏ trống
            const checkoutButton = document.querySelector('a[href="checkout.html"]');
            if (checkoutButton) {
                if (subtotal === 0) {
                    checkoutButton.classList.add('disabled');
                } else {
                    checkoutButton.classList.remove('disabled');
                }
            }
        }

        // --- SỬA 3: HÀM CẬP NHẬT ICON HEADER ---
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

        // --- (Các hàm bên dưới giữ nguyên) ---

        // --- Hàm cập nhật trạng thái nút "Xóa đã chọn" ---
        function updateRemoveButtonState() {
            if (!removeSelectedBtn) return;
            const checkedItems = cartBody.querySelectorAll('.item-checkbox:checked');
            const checkedCount = checkedItems.length;
            if (checkedCount > 0) {
                removeSelectedBtn.disabled = false;
                removeSelectedBtn.innerHTML = `<i class="bx bx-trash"></i> Xóa (${checkedCount}) mục đã chọn`;
            } else {
                removeSelectedBtn.disabled = true;
                removeSelectedBtn.innerHTML = `<i class="bx bx-trash"></i> Xóa (0) mục đã chọn`;
            }
            if (selectAllCheckbox) {
                if (checkedCount > 0 && checkedCount === itemCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount > 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }
        }

        // --- Sự kiện cho checkbox "Chọn tất cả" ---
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateRemoveButtonState();
            });
        }

        // --- Sự kiện cho CÁC checkbox của từng mục ---
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateRemoveButtonState();
            });
        });

        // --- Sự kiện cho nút "Xóa đã chọn" ---
        if (removeSelectedBtn) {
            removeSelectedBtn.addEventListener('click', function() {
                const checkedItems = cartBody.querySelectorAll('.item-checkbox:checked');
                if (checkedItems.length === 0) return;

                if (confirm(`Bạn có chắc muốn xóa ${checkedItems.length} sản phẩm này?`)) {
                    const itemsToDelete = [];
                    const rowsToRemove = [];
                    checkedItems.forEach(checkbox => {
                        const row = checkbox.closest('tr');
                        const productId = row.dataset.productId;
                        const variantId = row.dataset.variantId;
                        itemsToDelete.push({
                            product_id: productId,
                            variant_id: variantId
                        });
                        rowsToRemove.push(row);
                    });

                    const formData = new FormData();
                    formData.append('items_json', JSON.stringify(itemsToDelete));

                    fetch('index.php?class=cart&act=removeSelectedItems', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(json => {
                            if (json.status === 'success') {
                                rowsToRemove.forEach(row => row.remove());
                                const newCount = json.data.total_quantity;
                                updateTotals(json.data.subtotal);
                                updateHeaderCartCount(newCount);
                                updateRemoveButtonState();

                                if (newCount === 0) {
                                    cartBody.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center p-5">
                                        <i class="bx bx-cart fs-1 text-muted"></i>
                                        <p class="mt-2">Giỏ hàng của bạn đang trống.</p>
                                    </td>
                                </tr>`;
                                    if (removeSelectedBtn) removeSelectedBtn.remove();
                                    if (selectAllCheckbox) selectAllCheckbox.closest('th').remove();
                                }
                            } else {
                                alert(json.message);
                            }
                        });
                }
            });
        }

        // --- SỰ KIỆN CẬP NHẬT SỐ LƯỢNG (Giữ nguyên) ---
        cartBody.addEventListener('change', function(event) {
            if (event.target.classList.contains('quantity-input')) {
                const input = event.target;
                const quantity = parseInt(input.value);
                const row = input.closest('tr');
                const productId = row.dataset.productId;
                const variantId = row.dataset.variantId;
                const price = parseFloat(row.dataset.price);

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('variant_id', variantId);

                if (quantity > 0) {
                    formData.append('quantity', quantity);
                    fetch('index.php?class=cart&act=updateQuantity', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(json => {
                            if (json.status === 'success') {
                                // SỬA: Dùng hàm formatVND
                                row.querySelector('.item-total').textContent = formatVND(price * quantity);
                                updateTotals(json.data.subtotal);
                                updateHeaderCartCount(json.data.total_quantity);
                            } else {
                                alert(json.message);
                            }
                        });
                } else {
                    if (confirm('Bạn muốn xóa sản phẩm này? (Gõ số 0)')) {
                        const itemsToDelete = [{
                            product_id: productId,
                            variant_id: variantId
                        }];
                        formData.append('items_json', JSON.stringify(itemsToDelete));
                        fetch('index.php?class=cart&act=removeSelectedItems', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(json => {
                                if (json.status === 'success') {
                                    row.remove();
                                    updateTotals(json.data.subtotal);
                                    updateHeaderCartCount(json.data.total_quantity);
                                }
                            });
                    } else {
                        input.value = 1;
                    }
                }
            }
        });
    });
</script>