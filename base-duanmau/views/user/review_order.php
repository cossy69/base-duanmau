<div class="container my-5">
    <div class="text-center mb-5">
        <i class='bx bx-check-circle text-success' style="font-size: 60px;"></i>
        <h2 class="text-primary mt-3">Xác nhận thành công!</h2>
        <p class="text-muted">Cảm ơn bạn đã xác nhận nhận hàng. Hãy dành chút thời gian đánh giá sản phẩm nhé.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Đánh giá đơn hàng #<?php echo $_GET['id']; ?></h5>
        </div>
        <div class="card-body">
            <?php foreach ($orderItems as $item): ?>
                <div class="d-flex gap-3 mb-4 border-bottom pb-3">
                    <img src="<?php echo $item['main_image_url']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>

                        <form class="review-form mt-2" onsubmit="submitReview(event, this)">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $_GET['id']; ?>">

                            <div class="rating-stars mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class='bx bx-star fs-4 cursor-pointer' data-val="<?php echo $i; ?>" onclick="setRating(this, <?php echo $i; ?>)"></i>
                                <?php endfor; ?>
                                <input type="hidden" name="rating_value" value="5">
                            </div>

                            <div class="input-group">
                                <textarea class="form-control" name="comment" rows="1" placeholder="Chất lượng sản phẩm thế nào?"></textarea>
                                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                            </div>
                            <div class="feedback-msg mt-1 text-success small"></div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-secondary">Về trang chủ</a>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
        color: #ccc;
    }

    .bx-star.bxs-star {
        color: #ffc107;
    }

    /* Màu vàng cho sao đã chọn */
</style>

<script>
    // Xử lý chọn sao
    function setRating(star, rating) {
        const container = star.parentElement;
        const input = container.querySelector('input[name="rating_value"]');
        input.value = rating;

        // Tô màu sao
        const stars = container.querySelectorAll('i');
        stars.forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('bx-star');
                s.classList.add('bxs-star'); // Sao đặc (vàng)
            } else {
                s.classList.remove('bxs-star');
                s.classList.add('bx-star'); // Sao rỗng
            }
        });
    }

    // Khởi tạo 5 sao mặc định lúc load
    document.querySelectorAll('.rating-stars').forEach(div => {
        setRating(div.querySelector('i:last-child'), 5);
    });

    // Gửi đánh giá qua AJAX
    function submitReview(e, form) {
        e.preventDefault();
        const btn = form.querySelector('button');
        const msg = form.querySelector('.feedback-msg');

        btn.disabled = true;
        btn.textContent = 'Đang gửi...';

        const formData = new FormData(form);

        fetch('index.php?class=review&act=addReview', { // Đảm bảo class ReviewController có hàm addReview
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    msg.textContent = '✅ Đã gửi đánh giá!';
                    form.querySelector('textarea').disabled = true;
                } else {
                    msg.textContent = '❌ ' + data.message;
                    btn.disabled = false;
                    btn.textContent = 'Gửi đánh giá';
                }
            })
            .catch(err => {
                console.error(err);
                msg.textContent = 'Lỗi kết nối';
                btn.disabled = false;
            });
    }
</script>