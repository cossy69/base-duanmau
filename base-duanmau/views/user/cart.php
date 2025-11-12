<?php
// Giả sử anh đã gọi controller và có được $cartData
// Ví dụ:
require_once  'controllers/CartController.php';
$cartController = new CartController();
$cartData = $cartController->getCartContents();

// --- Bắt đầu phần dữ liệu ---
$cartItems = $cartData['items'];
$subtotal = $cartData['subtotal'];

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
                                    <tr data-price="<?php echo $item['price']; ?>" data-variant-id="<?php echo $item['variant_id']; ?>">
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