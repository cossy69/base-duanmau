<div class="container my-5 py-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-5">
            <div class="mb-4">
                <i class='bx bx-check-circle text-success' style="font-size: 80px;"></i>
            </div>
            <h2 class="card-title mb-3 text-success">Đặt hàng thành công!</h2>
            <p class="card-text text-muted mb-4">
                Cảm ơn bạn đã mua sắm tại cửa hàng.<br>
                Mã đơn hàng của bạn là: <strong>#<?php echo isset($_GET['id']) ? $_GET['id'] : '---'; ?></strong>
            </p>

            <div class="alert alert-info" role="alert">
                Chúng tôi sẽ liên hệ với bạn qua số điện thoại sớm nhất để xác nhận đơn hàng.
            </div>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="index.php?ctl=user&class=home&act=home" class="btn btn-outline-primary">
                    <i class='bx bx-home'></i> Về trang chủ
                </a>
                <a href="index.php?ctl=user&class=product&act=product" class="btn btn-primary">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>