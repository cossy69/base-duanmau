<?php include_once './views/admin/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800" style="color: var(--primary-color); font-weight: 700;">Quản lý Phản hồi</h1>
            <small class="text-muted">Ý kiến đóng góp từ khách hàng</small>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class='bx bx-message-detail'></i> Danh sách Phản hồi</h6>

            <select id="feedbackFilter" class="form-select w-auto shadow-sm" onchange="filterFeedbacks(this.value)">
                <option value="all">-- Tất cả trạng thái --</option>
                <option value="NEW">Mới gửi</option>
                <option value="IN_PROGRESS">Đang xử lý</option>
                <option value="RESOLVED">Đã giải quyết</option>
            </select>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Người gửi</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="feedbackTableBody">
                        <?php if (empty($feedbacks)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Chưa có phản hồi nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($feedbacks as $fb): ?>
                                <tr>
                                    <td>#<?php echo $fb['feedback_id']; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($fb['full_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($fb['email']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($fb['title']); ?></td>
                                    <td>
                                        <?php
                                        $content = htmlspecialchars($fb['content']);
                                        echo (strlen($content) > 50) ? substr($content, 0, 50) . '...' : $content;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        // 1. Xử lý màu sắc (Badge Color)
                                        $statusColorMap = [
                                            'NEW'         => 'danger',
                                            'IN_PROGRESS' => 'warning text-dark',
                                            'RESOLVED'    => 'success'
                                        ];
                                        // Nếu không tìm thấy thì mặc định là 'secondary'
                                        $statusColor = $statusColorMap[$fb['status']] ?? 'secondary';

                                        // 2. Xử lý nội dung chữ (Status Text)
                                        $statusTextMap = [
                                            'NEW'         => 'Mới',
                                            'IN_PROGRESS' => 'Đang xử lý',
                                            'RESOLVED'    => 'Đã giải quyết'
                                        ];
                                        // Nếu không tìm thấy thì hiện luôn mã trạng thái gốc
                                        $statusText = $statusTextMap[$fb['status']] ?? $fb['status'];
                                        ?>
                                        <span class="badge bg-<?php echo $statusColor; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td><?php echo date('d/m H:i', strtotime($fb['created_at'])); ?></td>
                                    <td class="text-end">
                                        <?php if ($fb['status'] == 'NEW'): ?>
                                            <button class="btn btn-sm btn-warning me-1" onclick="updateFeedback(<?php echo $fb['feedback_id']; ?>, 'IN_PROGRESS')" title="Đánh dấu đang xử lý">
                                                <i class='bx  bx-hourglass'></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($fb['status'] != 'RESOLVED'): ?>
                                            <button class="btn btn-sm btn-success me-1" onclick="updateFeedback(<?php echo $fb['feedback_id']; ?>, 'RESOLVED')" title="Đánh dấu đã xong">
                                                <i class='bx bx-check'></i>
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFeedback(<?php echo $fb['feedback_id']; ?>)" title="Xóa">
                                            <i class='bx bx-trash'></i>
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
</div>

<script>
    // 1. Hàm lọc AJAX
    function filterFeedbacks(status) {
        const tbody = document.getElementById('feedbackTableBody');
        tbody.style.opacity = '0.5'; // Hiệu ứng loading

        fetch(`index.php?class=admin&act=filter_feedbacks&status=${status}`)
            .then(response => response.text())
            .then(html => {
                tbody.innerHTML = html;
                tbody.style.opacity = '1';
            })
            .catch(err => console.error('Lỗi:', err));
    }

    // 2. Hàm cập nhật trạng thái
    function updateFeedback(id, status) {
        if (!confirm('Bạn muốn thay đổi trạng thái phản hồi này?')) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch('index.php?class=admin&act=update_feedback_status', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload lại danh sách theo bộ lọc hiện tại
                    const currentStatus = document.getElementById('feedbackFilter').value;
                    filterFeedbacks(currentStatus);
                } else {
                    alert('Có lỗi xảy ra!');
                }
            });
    }

    // 3. Hàm xóa
    function deleteFeedback(id) {
        if (confirm('Bạn có chắc chắn muốn xóa phản hồi này không?')) {
            window.location.href = `index.php?class=admin&act=delete_feedback&id=${id}`;
        }
    }
</script>

<?php include_once './views/admin/footer.php'; ?>