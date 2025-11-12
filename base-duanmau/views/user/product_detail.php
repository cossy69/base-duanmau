    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <div
                    style="
              --swiper-navigation-color: #000;
              --swiper-pagination-color: #000;
            "
                    class="pro_detail swiper mySwiper2">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                    </div>
                    <div class="swiper-button-next text-dark"></div>
                    <div class="swiper-button-prev text-dark"></div>
                </div>
                <div thumbsSlider="" class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                        <div class="swiper-slide">
                            <img src="image/new.webp" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h1 class="text-primary">iPhone 15 Pro (Titan Tự Nhiên)</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="rating me-2 fs-5 text-warning">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span class="text-muted small">(4.5/5 sao, 128 đánh giá)</span>
                </div>
                <p class="lead text-muted">
                    Chiếc điện thoại flagship mới nhất với thiết kế **Titan** cao cấp,
                    bền bỉ và hiệu năng đỉnh cao từ chip **A17 Pro**.
                </p>

                <div class="mb-3">
                    <span class="gia h2 fw-bold text-danger">30000000</span>
                    <span class="gia text-muted text-decoration-line-through ms-2">32000000</span>
                </div>

                <div class="mb-4">
                    <span class="badge bg-success">Còn hàng (15 sản phẩm)</span>
                </div>

                <form>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Màu sắc:</label>
                        <div id="color-options">
                            <span
                                class="variant-option selected"
                                data-value="Titan Tu Nhien">Titan Tự Nhiên</span>
                            <span class="variant-option" data-value="Titan Xanh">Titan Xanh</span>
                            <span class="variant-option" data-value="Titan Trang">Titan Trắng</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Dung lượng:</label>
                        <div id="capacity-options">
                            <span class="variant-option selected" data-value="256GB">256GB</span>
                            <span class="variant-option" data-value="512GB">512GB</span>
                            <span class="variant-option" data-value="1TB">1TB</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng:</label>
                        <input
                            type="number"
                            id="quantity"
                            class="form-control"
                            value="1"
                            min="1"
                            style="width: 100px" />
                    </div>

                    <div class="d-grid gap-2">
                        <button
                            id="addToCartBtn"
                            type="button"
                            class="btn btn-primary btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#addToCartModal">
                            Thêm vào Giỏ hàng
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-primary btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#buyNowModal">
                            Mua ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="desc-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#description"
                            type="button"
                            role="tab">
                            Mô tả Sản phẩm
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="spec-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#specs"
                            type="button"
                            role="tab">
                            Thông số Kỹ thuật
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="review-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#reviews"
                            type="button"
                            role="tab">
                            Đánh giá Sản phẩm (5)
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3 border border-top-0">
                    <div
                        class="tab-pane fade show active"
                        id="description"
                        role="tabpanel">
                        <div id="description-content" class="position-relative">
                            <h4 class="text-primary">
                                Thiết kế Titan Bền bỉ và Sang trọng
                            </h4>
                            <p>
                                iPhone 15 Pro đánh dấu một bước tiến lớn với vật liệu
                                **Titan** cao cấp, không chỉ mang lại độ bền vượt trội mà còn
                                giảm đáng kể trọng lượng, cho cảm giác cầm nắm thoải mái hơn.
                                Khung viền mỏng và các góc bo tròn tinh tế tạo nên vẻ ngoài
                                hoàn hảo.
                            </p>
                            <h4 class="text-primary mt-4">Hiệu năng Bứt phá với A17 Pro</h4>
                            <p>
                                Chip **A17 Pro** là bộ vi xử lý di động đầu tiên được xây dựng
                                trên tiến trình 3nm, mang lại hiệu năng xử lý CPU nhanh hơn
                                10% và GPU nhanh hơn 20% so với thế hệ trước. Điều này đảm bảo
                                trải nghiệm chơi game mượt mà, chỉnh sửa video 4K dễ dàng và
                                hiệu suất đa nhiệm không giới hạn.
                            </p>
                            <p>
                                *THÊM NỘI DUNG DÀI HƠN ĐỂ THỬ TÍNH NĂNG XEM THÊM:* Đây là đoạn
                                nội dung được thêm vào để mô phỏng một mô tả dài hơn, nhằm
                                kiểm tra xem chức năng "Xem thêm" và "Thu gọn" hoạt động như
                                thế nào. Nội dung này sẽ được ẩn đi khi chiều cao vượt quá
                                250px. Mục đích là để người dùng không bị choáng ngợp bởi một
                                bức tường văn bản ngay lập tức. Tính năng này giúp tối ưu hóa
                                trải nghiệm người dùng và giữ cho trang sản phẩm trông gọn
                                gàng hơn. Bạn có thể thay thế đoạn này bằng nội dung thực tế
                                của mình.
                            </p>
                            <p class="fst-italic mt-3">
                                Sản phẩm bao gồm: Thân máy, Cáp sạc USB-C, Sách hướng dẫn và
                                Que chọc SIM.
                            </p>
                            <div class="collapse-gradient"></div>
                        </div>
                        <button
                            id="toggle-description"
                            class="btn btn-link p-0 fw-bold mt-2">
                            Xem thêm
                        </button>
                    </div>
                    <div class="tab-pane fade" id="specs" role="tabpanel">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%">Công nghệ màn hình</th>
                                    <td>Super Retina XDR OLED, 120Hz, ProMotion</td>
                                </tr>
                                <tr>
                                    <th scope="row">Kích thước màn hình</th>
                                    <td>6.1 inches</td>
                                </tr>
                                <tr>
                                    <th scope="row">Hệ điều hành</th>
                                    <td>iOS (Phiên bản mới nhất)</td>
                                </tr>
                                <tr>
                                    <th scope="row">Chip xử lý (CPU)</th>
                                    <td>Apple A17 Pro (3nm)</td>
                                </tr>
                                <tr>
                                    <th scope="row">Bộ nhớ trong (ROM)</th>
                                    <td>256GB / 512GB / 1TB</td>
                                </tr>
                                <tr>
                                    <th scope="row">RAM</th>
                                    <td>8GB</td>
                                </tr>
                                <tr>
                                    <th scope="row">Camera sau</th>
                                    <td>
                                        3 camera: Chính 48MP, Góc siêu rộng 12MP, Telephoto 12MP
                                        (Zoom quang 3x)
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Chất liệu thân máy</th>
                                    <td>Titanium cấp độ hàng không vũ trụ</td>
                                </tr>
                                <tr>
                                    <th scope="row">Cổng kết nối</th>
                                    <td>USB Type-C (hỗ trợ USB 3)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <h4 class="text-primary mb-4">Đánh giá từ Khách hàng</h4>

                        <div class="row align-items-center mb-4 border-bottom pb-3">
                            <div class="col-md-3 text-center">
                                <h1 class="display-3 fw-bold text-primary">4.5</h1>
                                <div class="rating fs-4 text-warning">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <p class="text-muted small mt-1">(Dựa trên 128 đánh giá)</p>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2">5 sao</span>
                                    <div class="progress flex-grow-1" style="height: 10px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 80%"
                                            aria-valuenow="80"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2 small">(102)</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2">4 sao</span>
                                    <div class="progress flex-grow-1" style="height: 10px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 10%"
                                            aria-valuenow="10"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2 small">(13)</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2">3 sao</span>
                                    <div class="progress flex-grow-1" style="height: 10px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 5%"
                                            aria-valuenow="5"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2 small">(6)</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2">2 sao</span>
                                    <div class="progress flex-grow-1" style="height: 10px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 2%"
                                            aria-valuenow="2"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2 small">(3)</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2">1 sao</span>
                                    <div class="progress flex-grow-1" style="height: 10px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 1%"
                                            aria-valuenow="1"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2 small">(4)</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="text-primary mt-4 mb-3">Gửi đánh giá của bạn 👋</h5>
                        <div class="card mb-4">
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="reviewRating" class="form-label fw-bold">Điểm đánh giá (*)</label>
                                        <div class="rating fs-5 text-warning" id="reviewRating">
                                            <i class="bi bi-star" data-rating="1"></i>
                                            <i class="bi bi-star" data-rating="2"></i>
                                            <i class="bi bi-star" data-rating="3"></i>
                                            <i class="bi bi-star" data-rating="4"></i>
                                            <i class="bi bi-star" data-rating="5"></i>
                                        </div>
                                        <input
                                            type="hidden"
                                            name="rating_value"
                                            id="rating_value"
                                            value="0" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="reviewName" class="form-label">Tên của bạn</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="reviewName"
                                            value="Anh Chánh"
                                            readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label for="reviewTitle" class="form-label">Sản phẩm đã mua</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="reviewTitle"
                                            value="Titan Trắng, 1TB"
                                            readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label for="reviewContent" class="form-label">Nội dung đánh giá (*)</label>
                                        <textarea
                                            class="form-control"
                                            id="reviewContent"
                                            rows="3"
                                            required
                                            placeholder="Nhập đánh giá...."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        Gửi Đánh giá
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h5 class="text-primary mt-5 mb-3">Tất cả đánh giá (128)</h5>
                        <div id="reviews-list-container">
                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Nguyễn Văn A</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Tự Nhiên, 256GB
                                        </p>
                                    </div>
                                    <small class="text-muted">20/08/2025</small>
                                </div>
                                <p class="mt-2">
                                    Máy đẹp, cầm nhẹ tay hơn hẳn 14 Pro Max. Chip A17 Pro chơi
                                    game mượt mà không có gì để chê. Rất đáng tiền!
                                </p>
                            </div>

                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Trần Thị B</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Xanh, 512GB
                                        </p>
                                    </div>
                                    <small class="text-muted">15/07/2025</small>
                                </div>
                                <p class="mt-2">
                                    Camera tuyệt vời, đặc biệt là zoom quang 3x rất hữu ích. Chỉ
                                    tiếc là thời lượng pin không cải thiện nhiều như kỳ vọng.
                                </p>
                            </div>

                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Lê Văn C</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Trắng, 1TB
                                        </p>
                                    </div>
                                    <small class="text-muted">10/06/2025</small>
                                </div>
                                <p class="mt-2">
                                    Cổng USB-C tốc độ cao tiện lợi, truyền dữ liệu video 4K
                                    Prores cực nhanh. Rất hài lòng với sản phẩm.
                                </p>
                            </div>

                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Hoàng Văn D</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Tự Nhiên, 512GB
                                        </p>
                                    </div>
                                    <small class="text-muted">01/06/2025</small>
                                </div>
                                <p class="mt-2">
                                    Khung viền titan cầm rất thích, nhưng giá hơi chát. Hy vọng
                                    máy bền bỉ xứng đáng với số tiền bỏ ra.
                                </p>
                            </div>
                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Phạm Thị E</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Đen, 256GB
                                        </p>
                                    </div>
                                    <small class="text-muted">25/05/2025</small>
                                </div>
                                <p class="mt-2">
                                    Giao hàng nhanh, đóng gói cẩn thận. Sản phẩm đúng như mô tả,
                                    không có điểm gì phải phàn nàn.
                                </p>
                            </div>
                            <div class="review-item border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-primary">Nguyễn Văn F</strong>
                                        <div class="rating text-warning small">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            Đã mua: Titan Xanh, 256GB
                                        </p>
                                    </div>
                                    <small class="text-muted">20/05/2025</small>
                                </div>
                                <p class="mt-2">
                                    Sản phẩm ok, nhưng máy hơi nóng khi chơi game nặng liên tục.
                                    Trừ 2 sao vì vấn đề này.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="addToCartModal"
        tabindex="-1"
        aria-labelledby="addToCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addToCartModalLabel">Thành công!</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="h5 text-success mb-3">
                        ✅ Đã thêm sản phẩm vào giỏ hàng!
                    </p>
                    <p>iPhone 15 Pro (Titan Tự Nhiên, 256GB) đã được thêm.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Tiếp tục mua sắm
                    </button>
                    <a href="index.php?ctl=user&class=cart&act=cart" class="btn btn-primary">Xem Giỏ hàng</a>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="buyNowModal"
        tabindex="-1"
        aria-labelledby="buyNowModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="buyNowModalLabel">Đặt hàng nhanh</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Vui lòng nhập thông tin để chúng tôi liên hệ xác nhận đơn hàng
                        **iPhone 15 Pro**:
                    </p>
                    <form>
                        <div class="mb-3">
                            <label for="orderName" class="form-label">Họ và Tên (*)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="orderName"
                                required />
                        </div>
                        <div class="mb-3">
                            <label for="orderPhone" class="form-label">Số điện thoại (*)</label>
                            <input
                                type="tel"
                                class="form-control"
                                id="orderPhone"
                                required />
                        </div>
                        <div class="mb-3">
                            <label for="orderAddress" class="form-label">Địa chỉ giao hàng (*)</label>
                            <textarea
                                class="form-control"
                                id="orderAddress"
                                rows="2"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Hoàn tất Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>