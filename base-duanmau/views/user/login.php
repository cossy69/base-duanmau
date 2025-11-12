<div class="login-container">
    <div
        class="card login-card p-4 p-md-5 border-0 rounded-4 shadow-lg w-100 bg-white">
        <div class="d-flex flex-column align-items-center mb-4 mb-md-5">
            <i
                class="bx bxs-rocket text-primary fs-1 mb-3"
                style="font-size: 3rem"></i>

            <h2 class="h3 fw-bold text-dark mb-1">Đăng Nhập</h2>
            <p class="mt-2 small text-secondary">
                Truy cập để trải nghiệm sự đẳng cấp của Etroluc Hub.
            </p>
        </div>

        <form action="index.php?class=login&act=handleLogin" method="POST">
            <?php
            if (isset($_SESSION['register_success'])):
            ?>
                <div class="alert alert-success" role="alert">
                    <?php
                    echo $_SESSION['register_success'];
                    unset($_SESSION['register_success']);
                    ?>
                </div>
            <?php endif; ?>
            <?php
            if (isset($_SESSION['login_error'])):
            ?>
                <div class="alert alert-danger" role="alert">
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <label for="email" class="form-label fw-medium text-dark small">Địa chỉ Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="example@gmail.com"
                    required
                    class="form-control rounded-3 shadow-sm py-2"
                    aria-label="Địa chỉ Email" />
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-medium text-dark small">Mật khẩu</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="form-control rounded-3 shadow-sm py-2"
                    aria-label="Mật khẩu" />
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input
                        id="remember-me"
                        name="remember-me"
                        type="checkbox"
                        class="form-check-input" />
                    <label for="remember-me" class="form-check-label small text-dark">
                        Ghi nhớ tôi
                    </label>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    class="btn btn-primary w-100 py-2 fw-medium rounded-3 shadow">
                    Đăng Nhập
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="small text-secondary mb-0">
                Chưa có tài khoản?
                <a
                    href="index.php?ctl=user&class=login&act=register"
                    class="text-decoration-none text-primary fw-medium small">
                    Đăng ký ngay
                </a>
            </p>
        </div>
    </div>
</div>