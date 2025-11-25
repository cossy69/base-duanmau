<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $order['order_id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .table-light th {
            background-color: #f1f4f9;
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">
                <a href="index.php?class=admin&act=dashboard" class="text-decoration-none text-primary"><i class='bx bx-arrow-back'></i></a>
                Chi tiết đơn hàng #<?php echo $order['order_id']; ?>
            </h2>
            <div>
                <span class="badge bg-<?php
                                        $statusMap = [
                                            'PENDING'   => 'warning text-dark',
                                            'PREPARING' => 'info text-dark',
                                            'SHIPPING'  => 'primary',
                                            'DELIVERED' => 'secondary',
                                            'COMPLETED' => 'success',
                                            'CANCELLED' => 'danger'
                                        ];
                                        // Nếu không tìm thấy trạng thái thì mặc định là secondary
                                        echo $statusMap[$order['order_status']] ?? 'secondary';
                                        ?>">
                    <?php echo $order['order_status']; ?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Sản phẩm đã đặt</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end pe-4">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subtotal = 0;
                                    foreach ($items as $item):
                                        $itemTotal = $item['unit_price'] * $item['quantity'];
                                        $subtotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($item['main_image_url']); ?>"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                                                    <div>
                                                        <span class="fw-bold d-block"><?php echo htmlspecialchars($item['name']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                                            <td class="text-end"><?php echo number_format($item['unit_price']); ?> ₫</td>
                                            <td class="text-end pe-4 fw-bold"><?php echo number_format($itemTotal); ?> ₫</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold"><?php echo number_format($subtotal); ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span><?php echo number_format($order['shipping_fee']); ?> ₫</span>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Giảm giá (<?php echo htmlspecialchars($order['coupon_code'] ?? ''); ?>):</span>
                                <span>- <?php echo number_format($order['discount_amount']); ?> ₫</span>
                            </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-4 fw-bold text-primary"><?php echo number_format($order['total_amount']); ?> ₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Chuỗi mẫu: "Người nhận: Tên | Email: a@b.c | SĐT: ... | Đ/c: ..."
                        // Tách chuỗi để hiển thị đẹp hơn
                        $shippingInfo = $order['shipping_address'];
                        $parts = explode('|', $shippingInfo);
                        ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($parts as $part): ?>
                                <li class="mb-2 pb-2 border-bottom">
                                    <?php
                                    $p = explode(':', $part, 2);
                                    if (count($p) == 2) {
                                        echo "<small class='text-muted d-block'>" . trim($p[0]) . "</small>";
                                        echo "<span class='fw-bold'>" . trim($p[1]) . "</span>";
                                    } else {
                                        echo $part;
                                    }
                                    ?>
                                </li>
                            <?php endforeach; ?>

                            <li class="mt-3">
                                <small class="text-muted d-block">Ngày đặt hàng</small>
                                <span class="fw-bold"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="index.php?class=admin&act=dashboard" class="btn btn-outline-secondary">Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>