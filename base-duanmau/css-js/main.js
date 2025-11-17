// ===== CUON TRANG =====
try {
  const scrollBtn = document.getElementById("scrollTopBtn");
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
    if (linkPath.includes(`class=${currentPath}`)) {
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
  console.log("Trimming .card-text...");
  const texts = document.querySelectorAll(".card-text");
  if (texts.length > 0) {
    texts.forEach((e) => {
      const full = e.innerText.trim();
      if (full.length > 70) e.innerText = full.substring(0, 70) + "...";
    });
  }

  console.log("Trimming .text_new_t...");
  const text_new_t = document.querySelector(".text_new_t");
  if (text_new_t) {
    const full_text_nt = text_new_t.innerText.trim();
    if (full_text_nt.length > 300)
      text_new_t.innerText = full_text_nt.substring(0, 300) + "...";
  }

  console.log("Trimming .title_n_p...");
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
  console.log("Định dạng giá...");
  const prices = document.querySelectorAll(".gia");
  if (prices.length > 0) {
    prices.forEach((p) => {
      let num_p = parseInt(p.innerText);
      if (!isNaN(num_p)) {
        p.innerText = num_p.toLocaleString("vi-VN") + "đ";
      }
    });
  }
} catch (err) {
  console.error("❌ Lỗi định dạng giá:", err);
}

// ===== SWIPER =====
try {
  console.log("Khởi tạo swiper...");
  if (document.querySelector(".slide_pro.swiper")) {
    console.log("Found slide_pro");
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

  if (document.querySelector(".pro_detail.swiper.mySwiper2")) {
    console.log("Found mySwiper2");
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 10,
      slidesPerView: 4,
      freeMode: true,
      watchSlidesProgress: true,
    });
    var swiper2 = new Swiper(".mySwiper2", {
      spaceBetween: 10,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      thumbs: {
        swiper: swiper,
      },
    });
  }
} catch (err) {
  console.error("❌ Lỗi swiper:", err);
}

// ===== ACTIVE DANH MỤC =====
try {
  console.log("Xử lý .pro button...");
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
  console.error("❌ Lỗi active_dm:", err);
}

// ===== MÀU & LOẠI SP =====
try {
  console.log("Xử lý chọn màu...");
  const mau_sp = document.querySelectorAll("#color-options span");
  if (mau_sp.length > 0) {
    mau_sp.forEach((color) => {
      color.addEventListener("click", function () {
        mau_sp.forEach((mau) => mau.classList.remove("selected"));
        this.classList.add("selected");
      });
    });
  }

  console.log("Xử lý chọn loại...");
  const loai_sp = document.querySelectorAll("#capacity-options span");
  if (loai_sp.length > 0) {
    loai_sp.forEach((capacity) => {
      capacity.addEventListener("click", function () {
        loai_sp.forEach((loai) => loai.classList.remove("selected"));
        this.classList.add("selected");
      });
    });
  }
} catch (err) {
  console.error("❌ Lỗi chọn màu/loại:", err);
}

// ===== MỤC LỤC =====
try {
  console.log("Xử lý mục lục TOC...");
  const ml_new = document.querySelectorAll("#toc .nav-link");
  if (ml_new.length > 0) {
    ml_new.forEach(function (bd_trai) {
      bd_trai.addEventListener("click", function () {
        ml_new.forEach((bd) => bd.classList.remove("border-primary-subtle"));
        this.classList.add("border-primary-subtle");
      });
    });
  }
} catch (err) {
  console.error("❌ Lỗi mục lục:", err);
}

// ===== THANH SLIDER GIÁ =====
try {
  console.log("Xử lý priceRange...");
  const priceRange = document.getElementById("priceRange");
  const priceValue = document.getElementById("priceValue");

  function formatPrice(value) {
    const maxPrice = 50000000;
    const currentPrice = (value / 100) * maxPrice;
    return currentPrice.toLocaleString("vi-VN") + " VNĐ";
  }

  if (priceRange) {
    priceRange.value = 100;
    priceValue.textContent = formatPrice(priceRange.value);
    priceRange.addEventListener("input", (event) => {
      priceValue.textContent = formatPrice(event.target.value);
    });
  }
} catch (err) {
  console.error("❌ Lỗi priceRange:", err);
}

// ===== GIỎ HÀNG =====
try {
  console.log("Xử lý giỏ hàng...");
  const tableBody = document.getElementById("cart-table-body");
  if (tableBody) {
    const shippingFee = 30000;
    function formatCurrency(number) {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(number);
    }

    function updateCartSummary() {
      console.log("updateCartSummary()");
      let subtotal = 0;
      tableBody.querySelectorAll("tr").forEach((row) => {
        const price = parseInt(row.getAttribute("data-price"));
        const quantity = parseInt(row.querySelector(".quantity-input").value);
        const itemTotal = price * quantity;
        row.querySelector(".item-total").textContent =
          formatCurrency(itemTotal);
        subtotal += itemTotal;
      });
      const totalAmount = subtotal + shippingFee;
      document.getElementById("subtotal").textContent =
        formatCurrency(subtotal);
      document.getElementById("shipping-fee").textContent =
        formatCurrency(shippingFee);
      document.getElementById("total-amount").textContent =
        formatCurrency(totalAmount);
    }

    tableBody.addEventListener("change", function (event) {
      if (event.target.classList.contains("quantity-input")) {
        if (
          parseInt(event.target.value) < 1 ||
          isNaN(parseInt(event.target.value))
        ) {
          event.target.value = 1;
        }
        updateCartSummary();
      }
    });

    tableBody.addEventListener("click", function (event) {
      if (event.target.classList.contains("remove-btn")) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) {
          event.target.closest("tr").remove();
          updateCartSummary();
        }
      }
    });
    updateCartSummary();
  } else {
    console.log("Không tìm thấy #cart-table-body");
  }
} catch (err) {
  console.error("❌ Lỗi giỏ hàng:", err);
}

// ===== MÔ TẢ SP (XEM THÊM) =====
try {
  console.log("Xử lý toggle mô tả...");
  const descContent = document.getElementById("description-content");
  const toggleDescBtn = document.getElementById("toggle-description");
  if (descContent && toggleDescBtn) {
    toggleDescBtn.addEventListener("click", function () {
      if (descContent.classList.contains("expanded")) {
        descContent.classList.remove("expanded");
        toggleDescBtn.textContent = "Xem thêm";
      } else {
        descContent.classList.add("expanded");
        toggleDescBtn.textContent = "Thu gọn";
      }
    });

    setTimeout(() => {
      const actualHeight = descContent.scrollHeight;
      const limitHeight = 250;
      if (actualHeight <= limitHeight) {
        toggleDescBtn.style.display = "none";
        const gradient = descContent.querySelector(".collapse-gradient");
        if (gradient) gradient.style.display = "none";
      }
    }, 500);
  }
} catch (err) {
  console.error("❌ Lỗi mô tả sản phẩm:", err);
}

// ===== ĐÁNH GIÁ SAO =====
try {
  console.log("Xử lý rating...");
  const ratingStars = document.querySelectorAll("#reviewRating i");
  const ratingValueInput = document.getElementById("rating_value");

  if (ratingStars.length > 0) {
    ratingStars.forEach((star) => {
      star.addEventListener("mouseover", function () {
        const rating = parseInt(this.getAttribute("data-rating"));
        ratingStars.forEach((s) => {
          const starRating = parseInt(s.getAttribute("data-rating"));
          if (starRating <= rating) {
            s.classList.add("bi-star-fill");
            s.classList.remove("bi-star");
          } else {
            s.classList.remove("bi-star-fill");
            s.classList.add("bi-star");
          }
        });
      });

      star.addEventListener("mouseout", function () {
        const currentRating = parseInt(ratingValueInput.value);
        ratingStars.forEach((s) => {
          const starRating = parseInt(s.getAttribute("data-rating"));
          if (starRating <= currentRating) {
            s.classList.add("bi-star-fill");
            s.classList.remove("bi-star");
          } else {
            s.classList.remove("bi-star-half");
            s.classList.remove("bi-star-fill");
            s.classList.add("bi-star");
          }
        });
      });

      star.addEventListener("click", function () {
        const rating = parseInt(this.getAttribute("data-rating"));
        ratingValueInput.value = rating;
        ratingStars.forEach((s) => {
          const starRating = parseInt(s.getAttribute("data-rating"));
          if (starRating <= rating) {
            s.classList.add("bi-star-fill");
            s.classList.remove("bi-star");
          } else {
            s.classList.remove("bi-star-fill");
            s.classList.add("bi-star");
          }
        });
      });
    });
  }
} catch (err) {
  console.error("❌ Lỗi rating:", err);
}

try {
  const toastEl = document.getElementById("copyToast");
  const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
  const toastText = document.getElementById("toastText");

  function copyCode(code) {
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
  }
} catch (error) {
  console.log("Lỗi copy:", error);
}
