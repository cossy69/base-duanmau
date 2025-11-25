<?php include_once './views/admin/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color: var(--primary-color); font-weight: 700;">Dashboard & Thống kê</h1>
            <small style="color: #999">Tổng quan tình hình kinh doanh</small>
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
            <small class="text-muted"><i class='bx bxs-check-circle text-success'></i> Đơn đã hoàn thành</small>
        </div>
        <div class="stat-box" style="border-top-color: var(--success-color)">
            <h6>Tổng Đơn Hàng</h6>
            <div class="value" style="color: var(--success-color)"><?php echo $stats['total_orders']; ?></div>
            <small class="text-muted">Bao gồm tất cả trạng thái</small>
        </div>
        <div class="stat-box" style="border-top-color: var(--warning-color)">
            <h6>Thành viên</h6>
            <div class="value" style="color: var(--warning-color)"><?php echo $stats['total_users']; ?></div>
            <small class="text-muted">Tài khoản khách hàng</small>
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

    <div class="chart-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold mb-0"><i class='bx bx-list-ul'></i> Danh sách Đơn hàng</h4>
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
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Chưa có đơn hàng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $order['order_id']; ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"><?php echo htmlspecialchars($order['full_name'] ?? 'Khách vãng lai'); ?></span>
                                        <small class="text-muted" style="font-size: 12px;">
                                            <?php echo htmlspecialchars(substr($order['shipping_address'], 0, 30)) . '...'; ?>
                                        </small>
                                    </div>
                                </td>
                                <td class="text-danger fw-bold"><?php echo number_format($order['total_amount']); ?> đ</td>
                                <td><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?php
                                                                        $statusColors = [
                                                                            'PENDING'   => 'warning text-dark',
                                                                            'PREPARING' => 'info text-dark',
                                                                            'SHIPPING'  => 'primary',
                                                                            'DELIVERED' => 'secondary',
                                                                            'COMPLETED' => 'success',
                                                                            'CANCELLED' => 'danger'
                                                                        ];
                                                                        // Nếu trạng thái có trong mảng thì lấy màu tương ứng, nếu không thì lấy mặc định (light text-dark)
                                                                        echo $statusColors[$order['order_status']] ?? 'light text-dark';
                                                                        ?>">
                                        <?php echo $order['order_status']; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="index.php?class=admin&act=order_detail&id=<?php echo $order['order_id']; ?>"
                                        class="btn btn-sm btn-outline-secondary me-1"
                                        title="Xem chi tiết">
                                        <i class='bx bx-detail'></i> Chi tiết
                                    </a>

                                    <?php if ($order['order_status'] == 'PENDING'): ?>
                                        <button class="btn btn-sm btn-primary" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'PREPARING')" title="Xác nhận đơn">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'CANCELLED')" title="Hủy đơn">
                                            <i class='bx bx-x'></i>
                                        </button>
                                    <?php elseif ($order['order_status'] == 'PREPARING'): ?>
                                        <button class="btn btn-sm btn-info text-white" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'SHIPPING')" title="Giao cho shipper">
                                            <i class='bx bxs-truck'></i>
                                        </button>
                                    <?php elseif ($order['order_status'] == 'SHIPPING'): ?>
                                        <button class="btn btn-sm btn-warning" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'DELIVERED')" title="Đã đến nơi">
                                            <i class='bx bx-package'></i>
                                        </button>
                                    <?php endif; ?>
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
    // --- A. CẤU HÌNH CHART JS ---
    const chartData = <?php echo json_encode($chartData); ?>;
    const formatVND = (value) => new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(value);

    // 1. Line Chart (Doanh thu)
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
                    },
                    ticks: {
                        callback: function(v) {
                            if (v >= 1000000) return v / 1000000 + 'tr';
                            if (v >= 1000) return v / 1000 + 'k';
                            return v;
                        }
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

    // AJAX cập nhật Chart
    function updateChart(timeframe) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        fetch(`index.php?class=admin&act=get_revenue_chart_data&timeframe=${timeframe}`)
            .then(res => res.json())
            .then(data => {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets[0].data = data.values;
                salesChart.update();
            });
    }

    // 2. Bar Chart (Sản phẩm)
    const ctxProduct = document.getElementById("productChart").getContext("2d");
    new Chart(ctxProduct, {
        type: "bar",
        data: {
            labels: chartData.product_names,
            datasets: [{
                label: "Đã bán",
                data: chartData.product_sales,
                backgroundColor: ['#0066cc', '#28a745', '#ffc107', '#17a2b8', '#6c757d'],
                borderRadius: 5,
                barPercentage: 0.6
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

    // --- B. CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG ---
    function updateStatus(orderId, newStatus) {
        let textConfirm = '';
        if (newStatus === 'PREPARING') textConfirm = 'Xác nhận đơn hàng?';
        else if (newStatus === 'SHIPPING') textConfirm = 'Bắt đầu giao hàng?';
        else if (newStatus === 'DELIVERED') textConfirm = 'Đã giao đến nơi? (Sẽ gửi mail khách)';
        else if (newStatus === 'CANCELLED') textConfirm = 'Hủy đơn này?';

        if (!confirm(textConfirm)) return;

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);

        fetch('index.php?class=admin&act=update_order_status', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (newStatus === 'DELIVERED') {
                        alert(data.mail_status ? 'Cập nhật thành công!' : 'Cập nhật xong, nhưng LỖI GỬI MAIL:\n' + data.mail_message);
                    }
                    location.reload(); // Reload để cập nhật bảng
                } else {
                    alert('Lỗi cập nhật!');
                }
            });
    }
</script>

<?php include_once './views/admin/footer.php'; ?>