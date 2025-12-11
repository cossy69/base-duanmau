<?php
// Hàm helper để định dạng tiền tệ (nếu chưa có trong helper chung)
if (!function_exists('format_vnd')) {
    function format_vnd($price)
    {
        return number_format($price, 0, ',', '.') . ' VNĐ';
    }
}
// Ở trang Cart lúc này chưa tính ship, nên ship = 0
$shippingFee = 0;
$totalAmount = $subtotal;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Giỏ hàng của bạn</h5>
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
                                <tr>
                                    <td colspan="5" class="text-center p-4">Giỏ hàng trống</td>
                                </tr>
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
                                            <input type="number" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" min="1" style="width: 80px" />
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
                                <i class="bx bx-trash"></i> Xóa mục đã chọn
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Thanh toán</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã giảm giá:</label>
                        <select class="form-select" id="coupon-select">
                            <option value="0" data-type="FIXED">-- Chọn mã giảm giá --</option>
                            <?php foreach ($coupons as $coupon): ?>
                                <option value="<?php echo $coupon['discount_value']; ?>"
                                    data-type="<?php echo $coupon['discount_type']; ?>"
                                    data-code="<?php echo $coupon['code']; ?>"
                                    data-max="<?php echo $coupon['max_discount_value'] ?? 0; ?>">
                                    <?php echo $coupon['code']; ?> - <?php echo $coupon['description']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Tạm tính: <span id="subtotal"><?php echo format_vnd($subtotal); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light text-muted">
                            Phí vận chuyển: <span>Tính ở bước sau</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light text-success">
                            Giảm giá: <span id="discount-amount">- 0 VNĐ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light fw-bold fs-5 text-primary">
                            Tổng cộng: <span id="total-amount"><?php echo format_vnd($subtotal); ?></span>
                        </li>
                    </ul>

                    <form action="index.php?class=cart&act=checkout" method="POST" id="checkout-form">
                        <input type="hidden" name="discount_amount" id="hidden-discount-amount" value="0">
                        <input type="hidden" name="coupon_code" id="hidden-coupon-code" value="">
                        <input type="hidden" name="selected_items" id="hidden-selected-items" value="">

                        <button type="submit" class="btn btn-primary w-100 py-2 <?php echo empty($cartItems) ? 'disabled' : ''; ?>" id="btn-checkout">
                            Tiến hành Thanh toán
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartBody = document.getElementById('cart-table-body');
        const subtotalEl = document.getElementById('subtotal');
        const totalAmountEl = document.getElementById('total-amount');
        const discountAmountEl = document.getElementById('discount-amount');
        const couponSelect = document.getElementById('coupon-select');

        // Inputs ẩn gửi sang checkout
        const hiddenDiscount = document.getElementById('hidden-discount-amount');
        const hiddenCoupon = document.getElementById('hidden-coupon-code');

        function formatVND(number) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(number);
        }

        function parseMoney(moneyStr) {
            return parseInt(moneyStr.replace(/[^\d]/g, '')) || 0;
        }

        // Hàm tính toán lại tổng tiền (Chỉ trừ giảm giá, chưa có ship)
        function recalculateCart() {
            let subtotal = parseMoney(subtotalEl.textContent);
            let discount = 0;

            if (couponSelect && subtotal > 0) {
                const selected = couponSelect.options[couponSelect.selectedIndex];
                const val = parseFloat(selected.value) || 0;
                const type = selected.dataset.type;
                const max = parseFloat(selected.dataset.max) || 0;

                if (val > 0) {
                    if (type === 'PERCENT') {
                        discount = subtotal * (val / 100);
                        if (max > 0 && discount > max) discount = max;
                    } else {
                        discount = val;
                    }
                }
            }
            if (discount > subtotal) discount = subtotal;

            discountAmountEl.textContent = '- ' + formatVND(discount);
            totalAmountEl.textContent = formatVND(subtotal - discount);

            // Cập nhật input ẩn để gửi đi
            hiddenDiscount.value = discount;
            hiddenCoupon.value = couponSelect.value !== '0' ? couponSelect.options[couponSelect.selectedIndex].dataset.code : '';
        }

        if (couponSelect) {
            couponSelect.addEventListener('change', function() {
                recalculateCart();
                // Cũng cập nhật tổng tiền dựa trên sản phẩm được chọn
                if (typeof updateSelectedSubtotal === 'function') {
                    updateSelectedSubtotal();
                }
            });
        }

        // ... (Giữ nguyên các đoạn JS xử lý xóa/sửa số lượng/checkbox ở file cũ) ...
        // ... (Copy phần JS xử lý quantity-input, remove-selected-btn từ file cũ vào đây) ...
    });
</script>

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
                updateSelectedSubtotal(); // Cập nhật tổng tiền khi chọn/bỏ chọn
            });
        }

        // --- Sự kiện cho CÁC checkbox của từng mục ---
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateRemoveButtonState();
                updateSelectedSubtotal(); // Cập nhật tổng tiền khi chọn/bỏ chọn
            });
        });

        // --- Sự kiện submit form checkout ---
        const checkoutForm = document.getElementById('checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                const selectedItems = getSelectedItems();
                if (selectedItems.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
                    return false;
                }
                // Lưu danh sách sản phẩm được chọn vào input ẩn
                document.getElementById('hidden-selected-items').value = JSON.stringify(selectedItems);
            });
        }

        // --- Hàm lấy danh sách sản phẩm được chọn ---
        function getSelectedItems() {
            const checkedItems = cartBody.querySelectorAll('.item-checkbox:checked');
            const selectedItems = [];
            checkedItems.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const productId = row.dataset.productId;
                const variantId = row.dataset.variantId || 0;
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;
                selectedItems.push({
                    product_id: parseInt(productId),
                    variant_id: parseInt(variantId) || 0,
                    quantity: quantity
                });
            });
            return selectedItems;
        }

        // --- Cập nhật tổng tiền dựa trên sản phẩm được chọn ---
        function updateSelectedSubtotal() {
            const selectedItems = getSelectedItems();
            let selectedSubtotal = 0;
            
            selectedItems.forEach(item => {
                const row = cartBody.querySelector(`tr[data-product-id="${item.product_id}"][data-variant-id="${item.variant_id}"]`);
                if (row) {
                    const price = parseFloat(row.dataset.price) || 0;
                    selectedSubtotal += price * item.quantity;
                }
            });

            // Cập nhật hiển thị
            const subtotalEl = document.getElementById('subtotal');
            const totalAmountEl = document.getElementById('total-amount');
            const discountAmountEl = document.getElementById('discount-amount');
            const couponSelect = document.getElementById('coupon-select');
            const hiddenDiscount = document.getElementById('hidden-discount-amount');
            const hiddenCoupon = document.getElementById('hidden-coupon-code');
            
            if (subtotalEl && totalAmountEl) {
                // Tính giảm giá
                let discount = 0;
                if (couponSelect && selectedSubtotal > 0) {
                    const selected = couponSelect.options[couponSelect.selectedIndex];
                    const val = parseFloat(selected.value) || 0;
                    const type = selected.dataset.type;
                    const max = parseFloat(selected.dataset.max) || 0;

                    if (val > 0) {
                        if (type === 'PERCENT') {
                            discount = selectedSubtotal * (val / 100);
                            if (max > 0 && discount > max) discount = max;
                        } else {
                            discount = val;
                        }
                    }
                }
                if (discount > selectedSubtotal) discount = selectedSubtotal;

                subtotalEl.textContent = formatVND(selectedSubtotal);
                if (discountAmountEl) discountAmountEl.textContent = '- ' + formatVND(discount);
                totalAmountEl.textContent = formatVND(selectedSubtotal - discount);

                // Cập nhật input ẩn
                if (hiddenDiscount) hiddenDiscount.value = discount;
                if (hiddenCoupon) hiddenCoupon.value = couponSelect && couponSelect.value !== '0' ? couponSelect.options[couponSelect.selectedIndex].dataset.code : '';
            }

            // Cập nhật trạng thái nút checkout
            const btnCheckout = document.getElementById('btn-checkout');
            if (btnCheckout) {
                if (selectedItems.length === 0) {
                    btnCheckout.disabled = true;
                    btnCheckout.classList.add('disabled');
                } else {
                    btnCheckout.disabled = false;
                    btnCheckout.classList.remove('disabled');
                }
            }
        }

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
                                updateSelectedSubtotal(); // Cập nhật lại tổng tiền

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
                                updateSelectedSubtotal(); // Cập nhật tổng tiền sản phẩm được chọn
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
                                    updateSelectedSubtotal(); // Cập nhật tổng tiền sản phẩm được chọn
                                }
                            });
                    } else {
                        input.value = 1;
                    }
                }
            }
        });

        // Khởi tạo: Cập nhật tổng tiền khi trang load
        updateSelectedSubtotal();
    });
</script>