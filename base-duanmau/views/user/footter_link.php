    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="css-js/main.js"></script>
    </body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- HÀM 1: Cập nhật icon header ---
            function updateHeaderCartCount(newCount) {
                const cartIcon = document.getElementById('header-cart-icon');
                let countBadge = document.getElementById('header-cart-count');
                if (newCount > 0) {
                    if (countBadge) {
                        countBadge.textContent = newCount;
                    } else {
                        countBadge = document.createElement('span');
                        countBadge.id = 'header-cart-count';
                        countBadge.className = 'badge rounded-pill bg-danger';
                        countBadge.textContent = newCount;
                        if (cartIcon) cartIcon.appendChild(countBadge);
                    }
                } else {
                    if (countBadge) {
                        countBadge.remove();
                    }
                }
            }

            // --- Lấy element toast ---
            const toastElement = document.getElementById('tb_Toast');
            const toastBody = document.getElementById('toastTb');
            // Khởi tạo toast của Bootstrap
            const bsToast = new bootstrap.Toast(toastElement, {
                delay: 3000
            }); // 3 giây

            // --- HÀM 2: Xử lý "Add to cart" ---
            const allAddButtons = document.querySelectorAll('.add-to-cart-btn');
            allAddButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const productId = this.dataset.productId;
                    const variantId = this.dataset.variantId;
                    const quantity = 1;

                    // Sửa: Dùng 0 (như lần trước mình thống nhất)
                    if (variantId === null || variantId === undefined) {
                        alert('Lỗi: Không tìm thấy biến thể sản phẩm.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('variant_id', variantId);
                    formData.append('quantity', quantity);

                    fetch('index.php?class=cart&act=addToCart', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            // === SỬA TỪ ĐÂY ===
                            if (data.status === 'success') {
                                // 1. Cập nhật icon giỏ hàng
                                const newCount = data.data.total_quantity;
                                updateHeaderCartCount(newCount);

                                // 2. Hiển thị toast thành công
                                toastBody.textContent = '✅ Đã thêm sản phẩm vào giỏ!';
                                bsToast.show();

                            } else {
                                // Hiển thị lỗi trên toast
                                toastBody.textContent = '❌ Lỗi: ' + data.message;
                                bsToast.show();
                            }
                            // === ĐẾN ĐÂY ===
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastBody.textContent = '❌ Lỗi: ' + error.message;
                            bsToast.show();
                        });
                });
            });
            // HÀM 3: Xử lý "Toggle Favorite" (AJAX)
            // ===============================================

            // Hàm helper cập nhật icon trái tim trên header
            function updateHeaderFavoriteCount(newCount) {
                // Cái span này luôn tồn tại (do header.php tạo ra)
                let countBadge = document.querySelector('.favorite-count-badge');

                if (countBadge) {
                    if (newCount > 0) {
                        // Nếu có sản phẩm
                        countBadge.textContent = newCount;
                        countBadge.style.display = ''; // Xóa 'display: none' để nó hiện ra
                    } else {
                        // Nếu không có
                        countBadge.textContent = newCount;
                        countBadge.style.display = 'none'; // Ẩn nó đi
                    }
                }
            }
            // Gắn sự kiện vào 'body' để bắt tất cả các nút (trừ trang favorite)
            document.body.addEventListener('click', function(event) {
                // Tìm nút trái tim được click
                const heartButton = event.target.closest('.favorite-toggle-btn');

                // Nếu không phải nút trái tim, hoặc không tìm thấy, thì bỏ qua
                if (!heartButton) return;

                // (Trang favorite.php đã có script riêng xử lý rồi)
                if (document.getElementById('favorite-product-list')) return;

                event.preventDefault(); // Ngăn link <a>
                event.stopPropagation(); // Ngăn script "Add to cart"

                const productId = heartButton.dataset.productId;
                if (!productId) return;

                const formData = new FormData();
                formData.append('product_id', productId);

                fetch('index.php?class=favorite&act=toggleFavorite', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(json => {
                        if (json.status === 'success') {
                            toastBody.textContent = '✅ ' + json.message;
                            bsToast.show();

                            // Cập nhật icon header
                            updateHeaderFavoriteCount(json.data.count);

                            // Cập nhật icon trên nút
                            const heartIcon = heartButton.querySelector('i');
                            if (json.data.action === 'added') {
                                heartIcon.classList.add('active_i');
                            } else {
                                heartIcon.classList.remove('active_i');
                            }

                        } else {
                            toastBody.textContent = '❌ ' + json.message;
                            bsToast.show();
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi Toggle Favorite:', err);
                        toastBody.textContent = '❌ Đã có lỗi xảy ra.';
                        bsToast.show();
                    });
            });
            // --- HÀM 3: Xử lý lọc brand (giữ nguyên) ---
            const brandButtonsContainer = document.querySelector('.pro');
            const productContainer = document.querySelector('.product');

            if (brandButtonsContainer) {
                brandButtonsContainer.addEventListener('click', function(e) {
                    if (e.target.tagName === 'BUTTON') {
                        e.preventDefault();
                        brandButtonsContainer.querySelector('.active').classList.remove('active');
                        e.target.classList.add('active');

                        const brandId = e.target.dataset.brandId;
                        const formData = new FormData();
                        formData.append('brand_id', brandId);
                        productContainer.innerHTML = '<p style="text-align: center; width: 100%;">Đang tải sản phẩm...</p>';

                        fetch('index.php?class=home&act=filterProducts', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text())
                            .then(html => {
                                productContainer.innerHTML = html;
                            })
                            .catch(error => {
                                console.error('Lỗi khi lọc:', error);
                                productContainer.innerHTML = '<p style="text-align: center; width: 100%;">Lỗi khi tải sản phẩm. Vui lòng thử lại.</p>';
                            });
                    }
                });
            }
        });

        function updateHeaderCompareCount(newCount) {
            const badge = document.querySelector('.badge-compare'); // Selector từ header.php
            if (badge) {
                badge.textContent = newCount;
                badge.style.display = newCount > 0 ? 'flex' : 'none'; // Hiện/Ẩn badge
            }
        }

        // Gắn sự kiện vào 'body' để bắt các nút so sánh
        document.body.addEventListener('click', function(event) {
            const compareBtn = event.target.closest('.compare-toggle-btn');
            if (!compareBtn) return;

            event.preventDefault();
            event.stopPropagation();

            const productId = compareBtn.dataset.productId;
            if (!productId) return;

            const formData = new FormData();
            formData.append('product_id', productId);

            // Gọi CompareController::toggleCompare
            fetch('index.php?class=compare&act=toggleCompare', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(json => {
                    const icon = compareBtn.querySelector('i');
                    const toastBody = document.getElementById('toastTb');

                    if (json.status === 'success') {
                        // Cập nhật icon trên nút
                        if (json.data.status === 'added') {
                            icon.classList.add('active_i');
                            toastBody.textContent = '✅ Đã thêm vào so sánh.';
                        } else {
                            icon.classList.remove('active_i');
                            toastBody.textContent = '✅ Đã xóa khỏi so sánh.';
                        }
                        // Cập nhật badge header
                        updateHeaderCompareCount(json.data.count);
                    } else if (json.status === 'error' && json.message.includes('tối đa')) {
                        // Cảnh báo giới hạn (tối đa 3)
                        toastBody.textContent = '⚠️ ' + json.message;
                    } else {
                        toastBody.textContent = '❌ Lỗi: ' + json.message;
                    }

                    // Hiển thị toast (giả định bsToast đã được khởi tạo ở đầu file)
                    const bsToast = new bootstrap.Toast(document.getElementById('tb_Toast'), {
                        delay: 3000
                    });
                    bsToast.show();
                })
                .catch(err => {
                    console.error('Lỗi Toggle Compare:', err);
                    // toastBody.textContent = '❌ Đã có lỗi xảy ra.';
                    // bsToast.show();
                });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Hàm định dạng tiền tệ
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            // Hàm gọi Ajax gợi ý (Debounce để tránh gọi quá nhiều khi gõ nhanh)
            let timeout = null;

            const searchInputs = document.querySelectorAll('.live-search-input');

            searchInputs.forEach(input => {
                // Xác định div chứa gợi ý tương ứng
                let suggestionBoxId = '';
                if (input.id === 'search-input-header') {
                    suggestionBoxId = 'suggestions-header';
                } else {
                    suggestionBoxId = 'suggestions-banner'; // ID ta đã đặt ở home.php
                }

                const suggestionBox = document.getElementById(suggestionBoxId);

                if (!suggestionBox) return; // Nếu không tìm thấy box thì bỏ qua

                input.addEventListener('input', function() {
                    const keyword = this.value.trim();

                    // Xóa timeout cũ
                    clearTimeout(timeout);

                    if (keyword.length < 2) {
                        suggestionBox.style.display = 'none';
                        suggestionBox.innerHTML = '';
                        return;
                    }

                    // Set timeout mới (chờ 300ms sau khi ngừng gõ mới gửi request)
                    timeout = setTimeout(() => {
                        fetch(`index.php?class=search&act=suggest&keyword=${encodeURIComponent(keyword)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    let html = '';
                                    data.forEach(prod => {
                                        // Xử lý ảnh (nếu null thì dùng ảnh mặc định)
                                        const imgUrl = prod.image ? prod.image : 'image/default.png';
                                        const price = formatCurrency(prod.price);

                                        html += `
                                    <a href="index.php?class=product&act=product_detail&id=${prod.product_id}" class="suggestion-item">
                                        <img src="${imgUrl}" alt="${prod.name}">
                                        <div class="suggestion-info">
                                            <h6>${prod.name}</h6>
                                            <span>${price}</span>
                                        </div>
                                    </a>
                                `;
                                    });
                                    // Thêm nút xem tất cả
                                    html += `
                                <a href="index.php?class=search&act=search&keyword=${encodeURIComponent(keyword)}" class="suggestion-item text-center justify-content-center text-primary fw-bold">
                                    Xem tất cả kết quả cho "${keyword}"
                                </a>
                            `;

                                    suggestionBox.innerHTML = html;
                                    suggestionBox.style.display = 'block';
                                } else {
                                    suggestionBox.innerHTML = '<div class="p-3 text-muted text-center">Không tìm thấy sản phẩm nào</div>';
                                    suggestionBox.style.display = 'block';
                                }
                            })
                            .catch(err => console.error('Lỗi gợi ý:', err));
                    }, 300);
                });

                // Ẩn gợi ý khi click ra ngoài
                document.addEventListener('click', function(e) {
                    if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                        suggestionBox.style.display = 'none';
                    }
                });

                // Hiện lại gợi ý khi click vào ô input nếu đã có nội dung
                input.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2 && suggestionBox.innerHTML.trim() !== "") {
                        suggestionBox.style.display = 'block';
                    }
                });
            });
        });
    </script>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 10001">
        <div
            id="tb_Toast"
            class="toast align-items-center text-white border-0 bg-primary"
            role="alert"
            aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastTb"></div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    </html>