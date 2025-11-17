<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/../models/ReviewModel.php'; // Include the model

class ReviewController
{
    /**
     * [AJAX] Xử lý việc thêm đánh giá mới
     */
    public function addReview()
    {
        global $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse('error', 'Bạn cần đăng nhập để gửi đánh giá.');
            return;
        }
        $userId = $_SESSION['user_id'];

        // 2. Lấy dữ liệu
        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating_value'] ?? 0);
        $comment = (string)($_POST['comment'] ?? '');

        // 3. Validate
        if ($productId <= 0) {
            $this->jsonResponse('error', 'Sản phẩm không hợp lệ.');
            return;
        }
        if ($rating < 1 || $rating > 5) {
            $this->jsonResponse('error', 'Vui lòng chọn số sao từ 1 đến 5.');
            return;
        }
        if (empty(trim($comment))) {
            $this->jsonResponse('error', 'Vui lòng nhập nội dung đánh giá.');
            return;
        }

        // 4. Gọi Model để chèn
        try {
            $hasPurchased = ReviewModel::checkIfUserPurchasedProduct($pdo, $userId, $productId);

            if (!$hasPurchased) {
                $this->jsonResponse('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua hàng thành công.');
                return;
            }

            // 5. Gọi Model để chèn (giữ nguyên)
            $success = ReviewModel::insertReview($pdo, $productId, $userId, $rating, $comment);

            if ($success) {
                $this->jsonResponse('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
            } else {
                $this->jsonResponse('error', 'Không thể gửi đánh giá.');
            }
        } catch (PDOException $e) {
            // Bắt lỗi nếu người dùng cố tình gửi 2 lần
            if ($e->getCode() == '23000') {
                $this->jsonResponse('error', 'Bạn đã đánh giá sản phẩm này rồi.');
            } else {
                $this->jsonResponse('error', 'Lỗi CSDL: ' . $e->getMessage());
            }
        }
    }

    /**
     * Helper trả về JSON
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
