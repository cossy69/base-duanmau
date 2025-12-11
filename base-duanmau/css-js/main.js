// ===== CUON TRANG =====
try {
  const scrollBtn = document.getElementById("scrollTopBtn");
  if (scrollBtn) {
    // Thêm check tồn tại để tránh lỗi console
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        scrollBtn.style.display = "flex";
      } else {
        scrollBtn.style.display = "none";
      }
    });
    scrollBtn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
    scrollBtn.style.display = "none";
  }
} catch (error) {
  console.log("Loi cuon trang: ", error);
}

// ===== ACTIVE MENU =====
try {
  const active_menu = document.querySelectorAll(".nav-link");
  const param = new URLSearchParams(window.location.search);
  const currentPath = param.get("class") || "home";

  active_menu.forEach((item) => {
    const linkPath = item.getAttribute("href");
    if (linkPath && linkPath.includes(`class=${currentPath}`)) {
      item.classList.add("active", "fw-bold", "text-primary");
    } else {
      item.classList.remove("active", "fw-bold", "text-primary");
    }
  });
} catch (error) {
  console.log("Lỗi active menu: ", error);
}

// ===== CẮT TEXT NGẮN =====
try {
  // ... (Giữ nguyên phần cắt text vì nó dùng chung cho cả web)
  const texts = document.querySelectorAll(".card-text");
  if (texts.length > 0) {
    texts.forEach((e) => {
      const full = e.innerText.trim();
      if (full.length > 70) e.innerText = full.substring(0, 70) + "...";
    });
  }

  const text_new_t = document.querySelector(".text_new_t");
  if (text_new_t) {
    const full_text_nt = text_new_t.innerText.trim();
    if (full_text_nt.length > 300)
      text_new_t.innerText = full_text_nt.substring(0, 300) + "...";
  }

  const text_new_p = document.querySelectorAll(".title_n_p");
  if (text_new_p.length > 0) {
    text_new_p.forEach((e) => {
      const full_text_np = e.innerText.trim();
      if (full_text_np.length > 100)
        e.innerText = full_text_np.substring(0, 100) + "...";
    });
  }
} catch (err) {
  console.error("❌ Lỗi ở phần cắt text:", err);
}

// ===== ĐỊNH DẠNG GIÁ =====
try {
  const prices = document.querySelectorAll(".gia");
  if (prices.length > 0) {
    prices.forEach((p) => {
      // Chỉ định dạng nếu chưa có chữ 'đ' hoặc 'VNĐ' để tránh định dạng nhiều lần
      if (!p.innerText.includes("đ") && !p.innerText.includes("VNĐ")) {
        let num_p = parseInt(p.innerText.replace(/\./g, "")); // Xóa dấu chấm cũ nếu có
        if (!isNaN(num_p)) {
          p.innerText = num_p.toLocaleString("vi-VN") + "đ";
        }
      }
    });
  }
} catch (err) {
  console.error("❌ Lỗi định dạng giá:", err);
}

// ===== SWIPER =====
try {
  // GIỮ LẠI CÁI NÀY VÌ TRANG CHỦ CÓ THỂ DÙNG
  if (document.querySelector(".slide_pro.swiper")) {
    var swiper1 = new Swiper(".slide_pro.swiper", {
      slidesPerView: 4,
      spaceBetween: 20,
      direction: getDirection(),
      navigation: {
        nextEl: ".btn1.swiper-button-next",
        prevEl: ".btn1.swiper-button-prev",
      },
      on: {
        resize: function () {
          swiper1.changeDirection(getDirection());
        },
      },
    });

    function getDirection() {
      return window.innerWidth <= 760 ? "vertical" : "horizontal";
    }
  }

  // --- ĐÃ COMMENT LẠI VÌ ĐÃ CÓ TRONG PRODUCT_DETAIL.PHP ---
  /*
  if (document.querySelector(".pro_detail.swiper.mySwiper2")) {
    console.log("Found mySwiper2 in Main.js - Skipping to avoid conflict");
    // Code cũ đã bị comment để tránh xung đột
  }
  */
} catch (err) {
  console.error("❌ Lỗi swiper:", err);
}

// ===== ACTIVE DANH MỤC, MÀU, LOẠI SP, MỤC LỤC =====
// (Giữ nguyên các phần này vì chúng dùng chung hoặc không gây lỗi nghiêm trọng)
try {
  const active_dm = document.querySelectorAll(".pro button");
  if (active_dm.length > 0) {
    active_dm.forEach(function (active_pro) {
      active_pro.addEventListener("click", function (e) {
        active_dm.forEach((active) => {
          active.classList.remove("active");
        });
        this.classList.add("active");
      });
    });
  }
} catch (err) {
  console.error(err);
}

// ... (Giữ nguyên phần chọn màu/loại/mục lục/giá range) ...

// // ===== GIỎ HÀNG =====
// // Phần này giữ nguyên nếu anh dùng cho trang Giỏ hàng riêng biệt (cart.php)
// // Nếu trang detail dùng ajax add to cart thì không ảnh hưởng
// try {
//   const tableBody = document.getElementById("cart-table-body");
//   if (tableBody) {
//     // ... (Giữ nguyên logic giỏ hàng tĩnh) ...
//     // Code xử lý updateCartSummary...
//   }
// } catch (err) {
//   console.error("❌ Lỗi giỏ hàng:", err);
// }

// ===== MÔ TẢ SP (XEM THÊM) =====
// --- ĐÃ COMMENT LẠI VÌ ĐÃ CÓ TRONG PRODUCT_DETAIL.PHP ---
/*
try {
  const descContent = document.getElementById("description-content");
  // ... Code cũ gây xung đột ...
} catch (err) { ... }
*/

// ===== ĐÁNH GIÁ SAO =====
// --- ĐÃ COMMENT LẠI VÌ ĐÃ CÓ TRONG PRODUCT_DETAIL.PHP ---
/*
try {
  const ratingStars = document.querySelectorAll("#reviewRating i");
  // ... Code cũ gây xung đột ...
} catch (err) { ... }
*/

// ===== TOAST COPY =====
try {
  const toastEl = document.getElementById("copyToast");
  if (toastEl) {
    // Check tồn tại
    const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
    const toastText = document.getElementById("toastText");

    window.copyCode = function (code) {
      // Gán vào window để gọi được từ HTML onclick
      navigator.clipboard
        .writeText(code)
        .then(() => {
          toastText.textContent = `Đã sao chép mã: ${code}`;
          toast.show();
        })
        .catch((err) => {
          toastText.textContent = "Không thể sao chép!";
          toast.show();
          console.error(err);
        });
    };
  }
} catch (error) {
  console.log("Lỗi copy:", error);
}
