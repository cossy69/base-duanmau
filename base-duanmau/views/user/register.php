<div class="registration-container">
      <div
        class="card registration-card p-4 p-md-5 border-0 rounded-4 shadow-lg w-100 bg-white"
      >
        <div class="d-flex flex-column align-items-center mb-md-5">
          <i
            class="bx bxs-user-plus text-primary fs-1 mb-3"
            style="font-size: 3rem"
          ></i>

          <h2 class="h3 fw-bold text-dark mb-1">Tạo Tài Khoản Mới</h2>
          <p class="small text-secondary">
            Tham gia cùng chúng tôi chỉ trong vài bước.
          </p>
        </div>

        <form action="#" method="POST">
          <div class="mb-1">
            <label for="full_name" class="form-label fw-medium text-dark small"
              >Họ và Tên</label
            >
            <input
              type="text"
              id="full_name"
              name="full_name"
              placeholder="Nguyễn Văn A"
              required
              class="form-control rounded-3 shadow-sm py-2"
              aria-label="Họ và Tên"
            />
          </div>

          <div class="mb-1">
            <label for="email_reg" class="form-label fw-medium text-dark small"
              >Địa chỉ Email</label
            >
            <input
              type="email"
              id="email_reg"
              name="email"
              placeholder="example@gmail.com"
              required
              class="form-control rounded-3 shadow-sm py-2"
              aria-label="Địa chỉ Email"
            />
          </div>

          <div class="mb-1">
            <label
              for="password_reg"
              class="form-label fw-medium text-dark small"
              >Mật khẩu</label
            >
            <input
              type="password"
              id="password_reg"
              name="password"
              placeholder="••••••••"
              required
              class="form-control rounded-3 shadow-sm py-2"
              aria-label="Mật khẩu"
            />
          </div>

          <div class="mb-1">
            <label
              for="confirm_password"
              class="form-label fw-medium text-dark small"
              >Xác nhận Mật khẩu</label
            >
            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              placeholder="••••••••"
              required
              class="form-control rounded-3 shadow-sm py-2"
              aria-label="Xác nhận Mật khẩu"
            />
          </div>

          <div class="d-flex align-items-center mb-1">
            <div class="form-check">
              <input
                id="terms_checkbox"
                name="terms"
                type="checkbox"
                required
                class="form-check-input"
              />
              <label
                for="terms_checkbox"
                class="form-check-label small text-dark"
              >
                Tôi đồng ý với
                <a
                  href="#"
                  class="text-decoration-none text-primary fw-medium small"
                >
                  Điều khoản dịch vụ
                </a>
              </label>
            </div>
          </div>

          <div>
            <button
              type="submit"
              class="btn btn-primary w-100 py-2 fw-medium rounded-3 shadow"
            >
              Đăng Ký Tài Khoản
            </button>
          </div>
        </form>

        <div class="mt-4 text-center">
          <p class="small text-secondary mb-0">
            Đã có tài khoản?
            <a
              href="index.php?ctl=user&class=login&act=login"
              class="text-decoration-none text-primary fw-medium small"
            >
              Đăng nhập
            </a>
          </p>
        </div>
      </div>
    </div>