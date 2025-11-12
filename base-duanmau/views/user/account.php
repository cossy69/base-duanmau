<div class="container my-5">
    <div class="mb-3">
        <a href="index.php?ctl=user&class=home&act=home" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Quay về Trang chủ
        </a>
    </div>
    <h1 class="mb-4 text-primary">Quản lý Tài khoản</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group account-menu" id="account-tabs" role="tablist">
                <a
                    class="list-group-item list-group-item-action active"
                    id="profile-tab"
                    data-bs-toggle="list"
                    href="#profile"
                    role="tab">
                    Thông tin Cá nhân
                </a>
                <a
                    class="list-group-item list-group-item-action"
                    id="orders-tab"
                    data-bs-toggle="list"
                    href="#orders"
                    role="tab">
                    Lịch sử Đơn hàng
                </a>
                <a
                    class="list-group-item list-group-item-action"
                    id="addresses-tab"
                    data-bs-toggle="list"
                    href="#addresses"
                    role="tab">
                    Sổ địa chỉ
                </a>
                <a
                    class="list-group-item list-group-item-action"
                    id="settings-tab"
                    data-bs-toggle="list"
                    href="#settings"
                    role="tab">
                    Cài đặt Bảo mật
                </a>
                <a
                    class="list-group-item list-group-item-action text-danger"
                    href="logout.html">
                    Đăng xuất
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <h2>Thông tin Cá nhân</h2>
                    <form class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="full-name" class="form-label">Họ và Tên</label>
                            <input
                                type="text"
                                class="form-control"
                                id="full-name"
                                value="Nguyễn Văn A"
                                required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                value="vana@example.com"
                                readonly />
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                value="0901234567" />
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Cập nhật Thông tin
                        </button>
                    </form>
                </div>

                <div class="tab-pane fade" id="orders" role="tabpanel">
                    <h2>Lịch sử Đơn hàng</h2>
                    <div class="alert alert-info">
                        Bạn chưa có đơn hàng nào gần đây.
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#12345</td>
                                <td>01/01/2025</td>
                                <td>1.500.000 VNĐ</td>
                                <td><span class="badge bg-primary">Đã giao</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="addresses" role="tabpanel">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3">
                        <h2>Sổ địa chỉ</h2>
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addAddress">
                            Thêm địa chỉ mới
                        </button>
                    </div>

                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary text-white">
                            Địa chỉ Mặc định
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Nguyễn Văn A (0901234567)</h5>
                            <p class="card-text mb-2">
                                **200, Đường Tên Lửa**, Phường Bình Trị Đông B, Quận Bình
                                Tân, **TP. Hồ Chí Minh**
                            </p>
                            <button
                                style="font-size: 15px"
                                type="button"
                                class="btn text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editAddress">
                                Chỉnh sửa</button>|
                            <a href="#" class="card-link text-danger the_a">Xóa</a>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">Địa chỉ Phụ</div>
                        <div class="card-body">
                            <h5 class="card-title">Trần Thị B (0987654321)</h5>
                            <p class="card-text mb-2">
                                Số 10, Ngõ Chùa Liên Phái, Phố Bạch Mai, Quận Hai Bà Trưng,
                                **Hà Nội**
                            </p>
                            <a href="#" class="card-link text-primary the_a">Đặt làm Mặc định</a>
                            |
                            <button
                                style="font-size: 15px"
                                type="button"
                                class="btn text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editAddress">
                                Chỉnh sửa</button>|
                            <a href="#" class="card-link text-danger the_a">Xóa</a>
                        </div>
                    </div>
                </div>
                <div
                    class="modal fade"
                    id="addAddress"
                    tabindex="-1"
                    aria-labelledby="addAddressModel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="addToCartModalLabel">
                                    Thêm địa chỉ
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="" method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="fullname"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="phone"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <textarea
                                            class="form-control"
                                            name="address"
                                            rows="3"
                                            required></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer justify-content-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                        Hủy
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Thêm mới
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="modal fade"
                    id="editAddress"
                    tabindex="-1"
                    aria-labelledby="editAddressModel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="addToCartModalLabel">
                                    Chỉnh sửa địa chỉ
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="update_address.php" method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="fullname"
                                            value="Nguyễn Văn A"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            name="phone"
                                            value="0987654321"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <textarea
                                            class="form-control"
                                            name="address"
                                            rows="3"
                                            required>
123 Đường ABC, Quận 1, TP.HCM</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer justify-content-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                        Hủy
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="settings" role="tabpanel">
                    <h2>Cài đặt Bảo mật</h2>
                    <p>Thay đổi mật khẩu và quản lý phiên đăng nhập.</p>
                    <button
                        style="font-size: 15px"
                        type="button"
                        class="btn btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#editPassword">
                        Đổi mật khẩu</button>
                </div>
                <div
                    class="modal fade"
                    id="editPassword"
                    tabindex="-1"
                    aria-labelledby="editPasswordModel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title" id="addToCartModalLabel">
                                    Đổi mật khẩu
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="" method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu cũ</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="oldPass"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu mới</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="newPass"
                                            required />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Xác nhận mật khẩu mới</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="newPass"
                                            required />
                                    </div>
                                </div>

                                <div class="modal-footer justify-content-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                        Hủy
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        Xác nhận đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>