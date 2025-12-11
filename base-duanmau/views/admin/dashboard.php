<?php include_once './views/admin/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .table-order td {
        vertical-align: top;
        font-size: 0.9rem;
    }

    .product-list-mini {
        font-size: 0.85rem;
        color: #333;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 6px;
        border: 1px dashed #dee2e6;
    }

    .customer-info-box {
        max-width: 250px;
    }

    /* Style cho option bị disable */
    option:disabled {
        background-color: #f0f0f0;
        color: #999;
    }
</style>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Dashboard & Thống kê</h1>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="fw-bold text-primary">Xin chào, Admin</span>
            <div style="width: 40px; height: 40px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class='bx bxs-user'></i>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h6>Tổng Doanh Thu</h6>
            <div class="value"><?php echo number_format($stats['revenue']); ?> đ</div>
        </div>
        <div class="stat-box" style="border-top-color: var(--success-color)">
            <h6>Tổng Đơn Hàng</h6>
            <div class="value" style="color: var(--success-color)"><?php echo $stats['total_orders']; ?></div>
        </div>
        <div class="stat-box" style="border-top-color: var(--warning-color)">
            <h6>Thành viên</h6>
            <div class="value" style="color: var(--warning-color)"><?php echo $stats['total_users']; ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="chart-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary fw-bold mb-0">Thống kê doanh thu</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('day')">7 Ngày</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('week')">Tuần</button>
                        <button type="button" class="btn btn-outline-primary active" onclick="updateChart('month')" id="btn-month">Tháng</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('year')">Năm</button>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-wrapper">
                <h5 class="text-primary fw-bold mb-3">Top 5 Sản phẩm bán chạy</h5>
                <div style="height: 300px;">
                    <canvas id="productChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-wrapper mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold mb-0"><i class='bx bx-list-ul'></i> Xử lý Đơn hàng</h4>
            <select id="orderStatusFilter" class="form-select w-auto shadow-sm"
                onchange="window.location.href='index.php?class=admin&act=dashboard&status=' + this.value">
                <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>-- Tất cả trạng thái --</option>
                <option value="PENDING" <?php echo $status == 'PENDING' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                <option value="PREPARING" <?php echo $status == 'PREPARING' ? 'selected' : ''; ?>>Đang chuẩn bị</option>
                <option value="SHIPPING" <?php echo $status == 'SHIPPING' ? 'selected' : ''; ?>>Đang giao</option>
                <option value="DELIVERED" <?php echo $status == 'DELIVERED' ? 'selected' : ''; ?>>Đã giao</option>
                <option value="COMPLETED" <?php echo $status == 'COMPLETED' ? 'selected' : ''; ?>>Hoàn thành</option>
                <option value="CANCELLED" <?php echo $status == 'CANCELLED' ? 'selected' : ''; ?>>Đã hủy</option>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-order align-middle border">
                <thead class="table-primary text-nowrap">
                    <tr>
                        <th width="5%">Mã</th>
                        <th width="25%">Khách hàng & Địa chỉ</th>
                        <th width="30%">Sản phẩm đặt mua</th>
                        <th width="15%">Thanh toán</th>
                        <th width="15%">Trạng thái</th>
                        <th width="10%" class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class='bx bx-clipboard fs-1'></i><br>Chưa có đơn hàng nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $rawAddr = $order['shipping_address'];
                            $arrAddr = explode('|', $rawAddr);
                            $cusName = $order['full_name'] ?? 'Khách lẻ';
                            $cusPhone = '---';
                            $cusAddress = $rawAddr;
                            foreach ($arrAddr as $part) {
                                if (strpos(trim($part), 'Người nhận:') === 0) $cusName = trim(str_replace('Người nhận:', '', $part));
                                if (strpos(trim($part), 'SĐT:') === 0) $cusPhone = trim(str_replace('SĐT:', '', $part));
                                if (strpos(trim($part), 'Đ/c:') === 0) $cusAddress = trim(str_replace('Đ/c:', '', $part));
                            }

                            // --- LOGIC RÀNG BUỘC TRẠNG THÁI ---
                            // Định nghĩa thứ tự cấp độ
                            $levels = [
                                'PENDING'   => 1, // Chờ xác nhận
                                'PREPARING' => 2, // Đang chuẩn bị
                                'SHIPPING'  => 3, // Đang giao
                                'DELIVERED' => 4, // Đã giao
                                'COMPLETED' => 5, // Hoàn thành
                                'CANCELLED' => 0  // Hủy (Trường hợp đặc biệt)
                            ];
                            $currentLevel = $levels[$order['order_status']] ?? 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">#<?php echo $order['order_id']; ?></div>
                                    <small class="text-muted d-block mt-1"><?php echo date('d/m', strtotime($order['created_at'])); ?></small>
                                    <small class="text-muted"><?php echo date('H:i', strtotime($order['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="customer-info-box">
                                        <div class="fw-bold mb-1"><i class='bx bx-user'></i> <?php echo $cusName; ?></div>
                                        <div class="text-danger fw-bold mb-1"><i class='bx bx-phone'></i> <?php echo $cusPhone; ?></div>
                                        <div class="small text-muted" style="line-height: 1.2;">
                                            <i class='bx bx-map'></i> <?php echo $cusAddress; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-list-mini">
                                        <?php echo isset($order['product_summary']) ? $order['product_summary'] : 'Không có thông tin SP'; ?>
                                    </div>
                                    <?php if (!empty($order['coupon_id'])): ?>
                                        <div class="mt-1 badge bg-warning text-dark"><i class='bx bxs-coupon'></i> Có dùng mã giảm giá</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fs-6 fw-bold text-danger mb-1"><?php echo number_format($order['total_amount']); ?> đ</div>
                                    <?php if ($order['payment_method'] == 'COD'): ?>
                                        <span class="badge border border-secondary text-secondary bg-light">COD (Thu hộ)</span>
                                    <?php elseif ($order['payment_method'] == 'VNPAY'): ?>
                                        <?php
                                            $paySt = $order['payment_status'] ?? 'PENDING';
                                            $map = [
                                                'COMPLETED'      => ['success', 'text-success', 'Đã trả'],
                                                'PENDING'        => ['warning', 'text-dark', 'Chờ thanh toán'],
                                                'FAILED'         => ['danger', 'text-white', 'Thất bại/Hủy'],
                                                'REFUND_PENDING' => ['info', 'text-dark', 'Chờ hoàn tiền'],
                                                'CANCELLED'      => ['secondary', 'text-dark', 'Đã hủy'],
                                            ];
                                            $pay = $map[$paySt] ?? ['secondary', 'text-dark', $paySt];
                                        ?>
                                        <span class="badge bg-<?php echo $pay[0]; ?> bg-opacity-10 <?php echo $pay[1]; ?> border border-<?php echo $pay[0]; ?>">
                                            VNPAY (<?php echo $pay[2]; ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo $order['payment_method'] ?? 'Chưa rõ'; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select onchange="updateStatus(<?php echo $order['order_id']; ?>, this.value)"
                                        class="form-select form-select-sm fw-bold 
                                            <?php
                                            if ($order['order_status'] == 'PENDING') echo 'text-warning border-warning';
                                            elseif ($order['order_status'] == 'COMPLETED') echo 'text-success border-success';
                                            elseif ($order['order_status'] == 'CANCELLED') echo 'text-danger border-danger';
                                            elseif ($order['order_status'] == 'DELIVERED') echo 'text-success border-success';
                                            else echo 'text-primary border-primary';
                                            ?>"
                                        style="width: 140px; font-size: 0.85rem;"
                                        <?php echo ($order['order_status'] == 'COMPLETED' || $order['order_status'] == 'CANCELLED') ? 'disabled' : ''; ?>
                                        <?php echo ($order['order_status'] == 'DELIVERED') ? 'disabled' : ''; ?>
                                        title="<?php echo ($order['order_status'] == 'DELIVERED') ? 'Đơn đã giao. Chờ người dùng xác nhận nhận hàng.' : ''; ?>">
                                        <option value="PENDING" <?php echo $order['order_status'] == 'PENDING' ? 'selected' : ''; ?>
                                            <?php echo ($currentLevel > 1) ? 'disabled' : ''; ?>>Chờ xác nhận</option>

                                        <option value="PREPARING" <?php echo $order['order_status'] == 'PREPARING' ? 'selected' : ''; ?>
                                            <?php echo ($currentLevel < 1 || $currentLevel > 2) ? 'disabled' : ''; ?>>Đang chuẩn bị</option>

                                        <option value="SHIPPING" <?php echo $order['order_status'] == 'SHIPPING' ? 'selected' : ''; ?>
                                            <?php echo ($currentLevel < 2 || $currentLevel > 3) ? 'disabled' : ''; ?>>Đang giao</option>

                                        <option value="DELIVERED" <?php echo $order['order_status'] == 'DELIVERED' ? 'selected' : ''; ?>
                                            <?php echo ($currentLevel < 3 || $currentLevel > 4) ? 'disabled' : ''; ?>>Đã giao hàng</option>

                                        <!-- Admin không thể chuyển sang COMPLETED, chỉ người dùng mới có quyền này -->
                                        <option value="COMPLETED" <?php echo $order['order_status'] == 'COMPLETED' ? 'selected' : ''; ?>
                                            disabled>Hoàn thành</option>

                                        <option value="CANCELLED" <?php echo $order['order_status'] == 'CANCELLED' ? 'selected' : ''; ?>
                                            <?php echo ($currentLevel >= 3) ? 'disabled' : ''; ?>>Hủy đơn</option>
                                    </select>
                                    <?php if ($order['order_status'] == 'DELIVERED'): ?>
                                        <br><small class="text-muted" style="font-size: 0.75rem;">
                                            <i class='bx bx-info-circle'></i> Chờ người dùng xác nhận nhận hàng
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="index.php?class=admin&act=order_detail&id=<?php echo $order['order_id']; ?>"
                                        class="btn btn-sm btn-light border" title="Xem chi tiết đầy đủ">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?class=admin&act=dashboard&page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>">Trước</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?class=admin&act=dashboard&page=<?php echo $i; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?class=admin&act=dashboard&page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>">Sau</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    const chartData = <?php echo json_encode($chartData); ?>;
    const formatVND = (value) => new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(value);

    // Sales Chart
    const ctxSales = document.getElementById("salesChart").getContext("2d");
    let gradientSales = ctxSales.createLinearGradient(0, 0, 0, 300);
    gradientSales.addColorStop(0, 'rgba(0, 102, 204, 0.6)');
    gradientSales.addColorStop(1, 'rgba(0, 102, 204, 0.0)');

    let salesChart = new Chart(ctxSales, {
        type: "line",
        data: {
            labels: chartData.months.map(m => "Tháng " + m),
            datasets: [{
                label: "Doanh thu",
                data: chartData.sales,
                borderColor: "#0066cc",
                backgroundColor: gradientSales,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: "#fff",
                pointBorderColor: "#0066cc",
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(c) {
                            return ' Doanh thu: ' + formatVND(c.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        color: '#e0e0e0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    function updateChart(timeframe) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        fetch(`index.php?class=admin&act=get_revenue_chart_data&timeframe=${timeframe}`).then(res => res.json()).then(data => {
            salesChart.data.labels = data.labels;
            salesChart.data.datasets[0].data = data.values;
            salesChart.update();
        });
    }

    // Product Chart
    const ctxProduct = document.getElementById("productChart").getContext("2d");
    new Chart(ctxProduct, {
        type: "bar",
        data: {
            labels: chartData.product_names,
            datasets: [{
                label: "Đã bán",
                data: chartData.product_sales,
                backgroundColor: ['#0066cc', '#28a745', '#ffc107', '#17a2b8', '#6c757d'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        borderDash: [5, 5]
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Update Status
    function updateStatus(orderId, newStatus) {
        if (!confirm('Cập nhật trạng thái đơn hàng này?')) {
            location.reload();
            return;
        }
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);
        fetch('index.php?class=admin&act=update_order_status', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                if (newStatus === 'DELIVERED') alert('Đã cập nhật trạng thái Giao hàng!');
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
</script>

<?php include_once './views/admin/footer.php'; ?>