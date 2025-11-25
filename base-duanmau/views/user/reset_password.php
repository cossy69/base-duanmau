<div class="login-container py-5">
    <div class="card login-card p-4 border-0 rounded-4 shadow-lg mx-auto bg-white" style="max-width: 500px;">
        <h3 class="text-center mb-4 fw-bold">Đặt Lại Mật Khẩu</h3>

        <form action="index.php?class=login&act=handle_reset_password" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Đổi Mật Khẩu</button>
        </form>
    </div>
</div>