<div class="login-container py-5">
    <div class="card login-card p-4 border-0 rounded-4 shadow-lg mx-auto bg-white" style="max-width: 500px;">
        <h3 class="text-center mb-4 fw-bold">Quên Mật Khẩu?</h3>
        <p class="text-center text-muted mb-4">Nhập email của bạn để nhận link đặt lại mật khẩu.</p>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-info"><?php echo $_SESSION['login_error'];
                                            unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <form action="index.php?class=login&act=send_reset_link" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Email đăng ký</label>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Gửi Link</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php?class=login&act=login" class="text-decoration-none">Quay lại đăng nhập</a>
        </div>
    </div>
</div>