<div class="container my-5">
  <div class="p-4 p-md-5 bg-white shadow-lg rounded-4">
    <h1 class="text-center mb-3 text-dark">Bảng Mã Giảm Giá</h1>
    <p class="text-center text-muted lead">
      Chọn mã giảm giá phù hợp với tổng giá trị đơn hàng của bạn.
    </p>

    <div class="table-responsive mt-4">
      <table class="table table-striped table-bordered table-hover coupon-table align-middle">
        <thead class="table-dark">
          <tr>
            <th>MÃ GIẢM GIÁ</th>
            <th>GIÁ TRỊ GIẢM</th>
            <th>MÔ TẢ</th>
            <th>ĐƠN TỐI THIỂU</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($coupons)): ?>
            <tr>
              <td colspan="5" class="text-center py-4">Hiện tại không có mã giảm giá nào khả dụng.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($coupons as $c): ?>
              <tr>
                <td class="code-display fw-bold text-primary">
                  <?php echo htmlspecialchars($c['code']); ?>
                </td>
                <td class="gia fw-bold text-danger">
                  <?php
                  if ($c['discount_type'] == 'PERCENT') {
                    echo number_format($c['discount_value']) . '%';
                    if ($c['max_discount_value'] > 0) {
                      echo '<br><small class="text-muted text-nowrap">(Tối đa: ' . number_format($c['max_discount_value']) . 'đ)</small>';
                    }
                  } else {
                    echo number_format($c['discount_value']) . ' VNĐ';
                  }
                  ?>
                </td>
                <td>
                  <?php echo htmlspecialchars($c['description']); ?>
                  <br>
                  <small class="text-muted">HSD: <?php echo date('d/m/Y', strtotime($c['end_date'])); ?></small>
                </td>
                <td class="gia">
                  <?php echo number_format($c['min_order_amount']); ?> VNĐ
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
  <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastText">
        Đã sao chép mã!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>