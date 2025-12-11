<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/../models/ReviewModel.php';

class ReviewController
{
    public function addReview()
    {
        global $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse('error', 'Bạn cần đăng nhập để gửi đánh giá.');
            return;
        }
        $userId = $_SESSION['user_id'];

        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating_value'] ?? 0);
        $comment = (string)($_POST['comment'] ?? '');

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

        try {
            $hasPurchased = ReviewModel::checkIfUserPurchasedProduct($pdo, $userId, $productId);

            if (!$hasPurchased) {
                $this->jsonResponse('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua hàng thành công.');
                return;
            }

            // Kiểm tra xem có review_id không (nếu có thì là cập nhật)
            $reviewId = (int)($_POST['review_id'] ?? 0);
            
            if ($reviewId > 0) {
                // Cập nhật đánh giá (hiển thị ngay, không cần duyệt)
                $success = ReviewModel::updateReview($pdo, $reviewId, $userId, $rating, $comment);
                if ($success) {
                    $this->jsonResponse('success', 'Đã cập nhật đánh giá của bạn.');
                } else {
                    $this->jsonResponse('error', 'Không thể cập nhật đánh giá.');
                }
            } else {
                // Thêm đánh giá mới (tự động hiển thị)
                $success = ReviewModel::insertReview($pdo, $productId, $userId, $rating, $comment);
                if ($success) {
                    $this->jsonResponse('success', 'Đã gửi đánh giá của bạn.');
                } else {
                    $this->jsonResponse('error', 'Không thể gửi đánh giá.');
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $this->jsonResponse('error', 'Bạn đã đánh giá sản phẩm này rồi. Vui lòng cập nhật đánh giá hiện có.');
            } else {
                $this->jsonResponse('error', 'Lỗi CSDL: ' . $e->getMessage());
            }
        }
    }

    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
