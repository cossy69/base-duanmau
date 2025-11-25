<?php
// Thêm vào đầu file checkout.php
if (!isset($totalAmount)) {
    $shippingFee = $shippingFee ?? 0;
    $discountAmount = $discountAmount ?? 0;
    $totalAmount = $subtotal + $shippingFee - $discountAmount;
}
?>
<div class="container my-5">
    <h2 class="mb-4 text-center">Thanh toán đơn hàng</h2>

    <form action="index.php?class=order&act=process" method="POST">
        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class='bx bx-user-pin'></i> Thông tin nhận hàng</h5>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="shipping_fee" id="hidden-shipping-fee" value="0">
                        <input type="hidden" name="discount_amount" id="hidden-discount-amount" value="<?php echo $discountAmount ?? 0; ?>">
                        <input type="hidden" name="coupon_code" value="<?php echo $couponCode; ?>">
                        <input type="hidden" name="total_amount" id="hidden-total-amount" value="<?php echo $totalAmount; ?>">

                        <div class="mb-3">
                            <label class="form-label">Họ và tên (*)</label>
                            <input type="text" class="form-control" name="fullname" required
                                value="<?php echo htmlspecialchars($userName); ?>"
                                placeholder="Nhập họ tên người nhận">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại (*)</label>
                                <input type="text" class="form-control" name="phone" required
                                    value="<?php echo htmlspecialchars($userPhone); ?>"
                                    placeholder="Nhập số điện thoại">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email"
                                    value="<?php echo htmlspecialchars($userEmail); ?>"
                                    placeholder="Email nhận thông báo">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ nhận hàng (*)</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="address" id="customer-address" required
                                    value="<?php echo htmlspecialchars($customerAddress); ?>"
                                    placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/TP...">
                                <button class="btn btn-primary" type="button" id="btn-calc-ship">
                                    <i class='bx bx-map'></i> Tính phí ship
                                </button>
                            </div>
                            <div class="form-text">Vui lòng bấm "Tính phí ship" sau khi nhập địa chỉ.</div>

                            <div class="mt-2 p-2 bg-light border rounded d-none" id="shipping-result">
                                <div class="d-flex justify-content-between">
                                    <span id="distance-info" class="text-muted"></span>
                                    <span id="shipping-price" class="fw-bold text-primary"></span>
                                </div>
                                <div id="shipping-name" class="small text-muted"></div>
                            </div>
                            <div class="text-danger small d-none mt-1" id="shipping-error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea class="form-control" name="note" rows="2"
                                placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class='bx bx-credit-card'></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                            <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="VNPAY">
                            <label class="form-check-label d-flex align-items-center gap-2" for="vnpay">
                                Thanh toán Online qua VNPay
                                <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png" height="20">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Đơn hàng của bạn</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ
                                            <br><i><?php echo htmlspecialchars($item['variant_details']); ?></i>
                                        </small>
                                    </div>
                                    <span class="text-muted"><?php echo number_format($item['item_total'], 0, ',', '.'); ?> VNĐ</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span>Tạm tính</span>
                                <strong id="display-subtotal" data-value="<?php echo $subtotal; ?>">
                                    <?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ
                                </strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span>Vận chuyển</span>
                                <strong id="display-shipping">0 VNĐ</strong>
                            </li>

                            <?php if ($discountAmount > 0): ?>
                                <li class="list-group-item d-flex justify-content-between bg-light text-success">
                                    <span>Mã giảm giá (<?php echo $couponCode; ?>)</span>
                                    <strong>- <?php echo number_format($discountAmount, 0, ',', '.'); ?> VNĐ</strong>
                                </li>
                            <?php endif; ?>

                            <li class="list-group-item d-flex justify-content-between bg-light fs-5 fw-bold text-primary border-top">
                                <span>Tổng cộng</span>
                                <span id="display-total">
                                    <?php echo number_format($subtotal - $discountAmount, 0, ',', '.'); ?> VNĐ
                                </span>
                            </li>
                        </ul>

                        <button type="submit" class="btn btn-success w-100 mt-4 py-2 fw-bold" id="btn-place-order">
                            ĐẶT HÀNG
                        </button>
                        <div class="text-center mt-2">
                            <small class="text-danger d-none" id="order-error-msg">Vui lòng tính phí vận chuyển trước.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- CẤU HÌNH SHOP & GIÁ SHIP ---
        const SHOP_LAT = 19.774325609178057;
        const SHOP_LON = 105.78243656630887;

        // Lấy bảng giá ship từ PHP (Controller phải truyền biến $shippingMethods)
        // Nếu Controller chưa truyền, anh phải thêm dòng này vào CartController::checkout():
        // $shippingMethods = CartModel::getShippingMethods($pdo);
        const shippingData = <?php echo json_encode($shippingMethods ?? []); ?>;

        // Fallback nếu shippingData rỗng
        const RATE_NOI_THANH = shippingData[0] ? parseInt(shippingData[0]['price']) : 30000;
        const RATE_NGOAI_THANH = shippingData[1] ? parseInt(shippingData[1]['price']) : 50000;
        const RATE_NGOAI_TINH = shippingData[2] ? parseInt(shippingData[2]['price']) : 70000;

        // --- DOM ELEMENTS ---
        const addressInput = document.getElementById('customer-address');
        const btnCalcShip = document.getElementById('btn-calc-ship');
        const resultBox = document.getElementById('shipping-result');
        const errorBox = document.getElementById('shipping-error');
        const distanceInfo = document.getElementById('distance-info');
        const shippingPriceDisplay = document.getElementById('shipping-price');
        const shippingNameDisplay = document.getElementById('shipping-name');
        const btnPlaceOrder = document.getElementById('btn-place-order');
        const orderErrorMsg = document.getElementById('order-error-msg');

        // Elements hiển thị tiền
        const displayShipping = document.getElementById('display-shipping');
        const displayTotal = document.getElementById('display-total');
        const subtotalVal = parseInt(document.getElementById('display-subtotal').dataset.value);
        const discountVal = <?php echo $discountAmount; ?>;

        // Inputs ẩn
        const hiddenShippingFee = document.getElementById('hidden-shipping-fee');
        const hiddenTotalAmount = document.getElementById('hidden-total-amount');

        // Format tiền VNĐ
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Hàm cập nhật Tổng tiền cuối cùng
        function updateTotal(shippingFee) {
            // 1. Cập nhật giao diện hiển thị
            const displayShipping = document.getElementById('display-shipping');
            if (displayShipping) displayShipping.textContent = formatVND(shippingFee);

            // 2. QUAN TRỌNG: Cập nhật giá trị vào Input ẩn để gửi đi
            const hiddenShippingInput = document.getElementById('hidden-shipping-fee');
            if (hiddenShippingInput) {
                hiddenShippingInput.value = shippingFee;
            }

            // 3. Tính tổng cuối cùng
            // Lấy subtotal từ data-value (hoặc parse từ text)
            const subtotalEl = document.getElementById('display-subtotal');
            let subtotalVal = subtotalEl ? parseInt(subtotalEl.getAttribute('data-value')) : 0;
            // Nếu không có data-value thì parse từ text
            if (!subtotalVal && subtotalEl) subtotalVal = parseInt(subtotalEl.textContent.replace(/\D/g, '')) || 0;

            // Lấy discount (đã có từ PHP)
            const discountVal = <?php echo $discountAmount ?? 0; ?>;

            const finalTotal = subtotalVal + shippingFee - discountVal;
            const safeTotal = finalTotal < 0 ? 0 : finalTotal;

            // Hiển thị tổng
            const displayTotal = document.getElementById('display-total');
            if (displayTotal) displayTotal.textContent = formatVND(safeTotal);

            // Cập nhật input tổng tiền
            const hiddenTotalInput = document.getElementById('hidden-total-amount');
            if (hiddenTotalInput) hiddenTotalInput.value = safeTotal;

            // Cho phép đặt hàng
            const btnPlaceOrder = document.getElementById('btn-place-order');
            const orderErrorMsg = document.getElementById('order-error-msg');
            if (btnPlaceOrder) btnPlaceOrder.disabled = false;
            if (orderErrorMsg) orderErrorMsg.classList.add('d-none');
        }

        // --- LOGIC TÍNH SHIP ---
        async function calculateShipping() {
            const address = addressInput.value.trim();
            if (!address) {
                alert('Vui lòng nhập địa chỉ cụ thể.');
                addressInput.focus();
                return;
            }

            btnCalcShip.disabled = true;
            btnCalcShip.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> ...';
            errorBox.classList.add('d-none');
            resultBox.classList.add('d-none');

            try {
                // 1. Lấy tọa độ (Nominatim)
                const geoUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;
                const geoRes = await fetch(geoUrl);
                const geoData = await geoRes.json();

                if (!geoData.length) throw new Error('Không tìm thấy địa chỉ này trên bản đồ.');

                const userLat = geoData[0].lat;
                const userLon = geoData[0].lon;

                // 2. Tính khoảng cách (OSRM)
                const routeUrl = `https://router.project-osrm.org/route/v1/driving/${SHOP_LON},${SHOP_LAT};${userLon},${userLat}?overview=false`;
                const routeRes = await fetch(routeUrl);
                const routeData = await routeRes.json();

                if (routeData.code !== 'Ok') throw new Error('Không tính được đường đi.');

                const distanceKm = (routeData.routes[0].distance / 1000).toFixed(1);

                // 3. Áp dụng giá
                let fee = 0;
                let name = '';
                if (distanceKm <= 30) {
                    fee = RATE_NOI_THANH;
                    name = 'Nội thành';
                } else if (distanceKm <= 100) {
                    fee = RATE_NGOAI_THANH;
                    name = 'Ngoại thành';
                } else {
                    fee = RATE_NGOAI_TINH;
                    name = 'Ngoại tỉnh';
                }

                // 4. Hiển thị kết quả
                distanceInfo.textContent = `Khoảng cách: ${distanceKm} km`;
                shippingPriceDisplay.textContent = formatVND(fee);
                shippingNameDisplay.textContent = `Khu vực: ${name}`;

                resultBox.classList.remove('d-none');
                updateTotal(fee);

            } catch (err) {
                console.error(err);
                errorBox.textContent = err.message || 'Lỗi tính toán.';
                errorBox.classList.remove('d-none');
                updateTotal(0); // Reset ship về 0 nếu lỗi
            } finally {
                btnCalcShip.disabled = false;
                btnCalcShip.innerHTML = '<i class="bx bx-map"></i> Tính phí ship';
            }
        }

        btnCalcShip.addEventListener('click', calculateShipping);

        // Nếu khách nhập địa chỉ rồi Enter
        addressInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Ngăn submit form
                calculateShipping();
            }
        });

        // Tự động tính nếu đã có địa chỉ (từ DB) khi load trang?
        // Tùy chọn: Nếu muốn tự tính luôn khi vừa vào trang thì uncomment dòng dưới:
        if (addressInput.value.trim() !== '') {
            calculateShipping();
        } else {
            // Nếu chưa có địa chỉ, disable nút đặt hàng để bắt buộc tính ship
            btnPlaceOrder.disabled = true;
            orderErrorMsg.classList.remove('d-none');
        }
    });
</script>