<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Statistics</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
    crossorigin="anonymous" />
  <link
    href="https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <style>
    :root {
      --primary-color: #0066cc;
      --secondary-color: #81beff;
      --danger-color: #ff1212;
      --success-color: #28a745;
      --warning-color: #ffc107;
      --bg-light: #f8f9fa;
      --text-dark: #1a1a1a;
    }

    body {
      background-color: var(--bg-light);
      color: var(--text-dark);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
        "Helvetica Neue", Arial, sans-serif;
    }

    .admin-header {
      background-color: white;
      border-bottom: 2px solid var(--secondary-color);
      padding: 20px 0;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .admin-header h1 {
      color: var(--primary-color);
      font-weight: 700;
      margin: 0;
    }

    .sidebar {
      background-color: var(--primary-color);
      min-height: 100vh;
      padding: 30px 0;
      position: fixed;
      width: 250px;
      left: 0;
      top: 0;
    }

    .sidebar .nav-link {
      color: white;
      padding: 15px 25px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-left: 4px solid transparent;
      transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: rgba(255, 255, 255, 0.1);
      border-left-color: var(--secondary-color);
    }

    .sidebar .nav-link i {
      font-size: 20px;
    }

    .main-content {
      margin-left: 250px;
      padding: 30px;
    }

    .chart-wrapper {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      position: relative;
    }

    .chart-title {
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 25px;
      font-size: 18px;
    }

    .chart-container {
      position: relative;
      height: 300px;
      margin-bottom: 20px;
    }

    .section-title {
      color: var(--primary-color);
      font-weight: 700;
      margin: 30px 0 20px 0;
      font-size: 24px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-box {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      border-top: 4px solid var(--primary-color);
    }

    .stat-box h6 {
      color: #999;
      font-size: 12px;
      text-transform: uppercase;
      margin-bottom: 10px;
      letter-spacing: 0.5px;
    }

    .stat-box .value {
      font-size: 28px;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 8px;
    }

    .stat-box .description {
      font-size: 13px;
      color: #666;
    }

    .filter-section {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-section label {
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 0;
    }

    .filter-section select,
    .filter-section input {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 8px 12px;
      font-size: 13px;
    }

    .filter-section button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 20px;
      cursor: pointer;
      font-weight: 600;
    }

    .filter-section button:hover {
      background-color: #0052a3;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        position: relative;
        min-height: auto;
        padding: 10px 0;
      }

      .sidebar .nav-link {
        padding: 10px 15px;
        font-size: 14px;
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      .chart-container {
        height: 250px;
      }

      .filter-section {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-section select,
      .filter-section input,
      .filter-section button {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <div class="ps-3 mb-4">
      <h4 style="color: white; margin: 0; font-weight: 700">Tech Hub</h4>
      <small style="color: rgba(255, 255, 255, 0.7)">Admin Panel</small>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link" href="admin.html">
        <i class="bx bxs-dashboard"></i>
        <span>Dashboard</span>
      </a>
      <a class="nav-link active" href="statistics.html">
        <i class="bx bxs-bar-chart-alt-2"></i>
        <span>Statistics</span>
      </a>
      <a class="nav-link" href="#">
        <i class="bx bxs-package"></i>
        <span>Products</span>
      </a>
      <a class="nav-link" href="#">
        <i class="bx bxs-shopping-bags"></i>
        <span>Orders</span>
      </a>
      <a class="nav-link" href="#">
        <i class="bx bxs-user-account"></i>
        <span>Users</span>
      </a>
      <a class="nav-link" href="#">
        <i class="bx bxs-message-dots"></i>
        <span>Messages</span>
      </a>
      <a class="nav-link" href="#">
        <i class="bx bxs-cog"></i>
        <span>Settings</span>
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <div class="admin-header mb-4">
      <div class="container-fluid">
        <h1>Statistics & Analytics</h1>
        <small style="color: #999">Track your business performance and trends</small>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <label for="dateRange">Date Range:</label>
      <input type="date" id="dateRange" />
      <label for="category">Category:</label>
      <select id="category">
        <option>All Categories</option>
        <option>Electronics</option>
        <option>Smartphones</option>
        <option>Accessories</option>
      </select>
      <button onclick="applyFilter()">Apply Filter</button>
      <button onclick="resetFilter()" style="background-color: #999">Reset</button>
    </div>

    <!-- Key Metrics -->
    <h2 class="section-title">Key Metrics</h2>
    <div class="stats-grid">
      <div class="stat-box">
        <h6>Total Revenue</h6>
        <div class="value">$156,890</div>
        <div class="description">
          <i class="bx bxs-up-arrow" style="color: var(--success-color)"></i>
          23% increase from last period
        </div>
      </div>
      <div class="stat-box" style="border-top-color: var(--success-color)">
        <h6>Total Orders</h6>
        <div class="value" style="color: var(--success-color)">12,456</div>
        <div class="description">
          <i class="bx bxs-up-arrow" style="color: var(--success-color)"></i>
          15% increase from last period
        </div>
      </div>
      <div class="stat-box" style="border-top-color: var(--warning-color)">
        <h6>Average Order Value</h6>
        <div class="value" style="color: var(--warning-color)">$125.60</div>
        <div class="description">
          <i class="bx bxs-down-arrow" style="color: var(--danger-color)"></i>
          5% decrease from last period
        </div>
      </div>
      <div class="stat-box" style="border-top-color: var(--danger-color)">
        <h6>Conversion Rate</h6>
        <div class="value" style="color: var(--danger-color)">3.45%</div>
        <div class="description">
          <i class="bx bxs-up-arrow" style="color: var(--success-color)"></i>
          8% increase from last period
        </div>
      </div>
    </div>

    <!-- Sales Chart -->
    <h2 class="section-title">Sales Performance</h2>
    <div class="chart-wrapper">
      <h3 class="chart-title">Monthly Sales Trend</h3>
      <div class="chart-container">
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <!-- Category Distribution -->
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="chart-wrapper">
          <h3 class="chart-title">Sales by Category</h3>
          <div class="chart-container">
            <canvas id="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Product Performance -->
      <div class="col-lg-6">
        <div class="chart-wrapper">
          <h3 class="chart-title">Top Products</h3>
          <div class="chart-container">
            <canvas id="productChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Analytics -->
    <div class="row g-4" style="margin-top: 10px">
      <div class="col-lg-6">
        <div class="chart-wrapper">
          <h3 class="chart-title">Customer Segments</h3>
          <div class="chart-container">
            <canvas id="customerChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Traffic Sources -->
      <div class="col-lg-6">
        <div class="chart-wrapper">
          <h3 class="chart-title">Traffic Sources</h3>
          <div class="chart-container">
            <canvas id="trafficChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <script>
    // Chart Color Palette
    const primaryColor = "#0066cc";
    const successColor = "#28a745";
    const warningColor = "#ffc107";
    const dangerColor = "#ff1212";
    const secondaryColor = "#81beff";

    // Sales Chart
    const salesCtx = document.getElementById("salesChart").getContext("2d");
    new Chart(salesCtx, {
      type: "line",
      data: {
        labels: [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec",
        ],
        datasets: [{
          label: "Sales",
          data: [
            12000, 19000, 15000, 25000, 22000, 30000, 28000, 32000, 35000,
            38000, 42000, 45000,
          ],
          borderColor: primaryColor,
          backgroundColor: "rgba(0, 102, 204, 0.1)",
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: primaryColor,
          pointBorderColor: "#fff",
          pointBorderWidth: 2,
          pointRadius: 5,
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "#f0f0f0",
            },
          },
        },
      },
    });

    // Category Distribution Pie Chart
    const categoryCtx = document.getElementById("categoryChart").getContext("2d");
    new Chart(categoryCtx, {
      type: "doughnut",
      data: {
        labels: ["Smartphones", "Accessories", "Electronics", "Others"],
        datasets: [{
          data: [35, 25, 25, 15],
          backgroundColor: [
            primaryColor,
            successColor,
            warningColor,
            secondaryColor,
          ],
          borderColor: "#fff",
          borderWidth: 2,
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });

    // Top Products Bar Chart
    const productCtx = document.getElementById("productChart").getContext("2d");
    new Chart(productCtx, {
      type: "bar",
      data: {
        labels: [
          "iPhone 15",
          "Samsung S24",
          "Xiaomi 14",
          "OPPO Find",
          "OnePlus 12",
        ],
        datasets: [{
          label: "Units Sold",
          data: [450, 380, 320, 290, 250],
          backgroundColor: primaryColor,
          borderColor: primaryColor,
          borderWidth: 1,
          borderRadius: 6,
        }, ],
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              color: "#f0f0f0",
            },
          },
        },
      },
    });

    // Customer Segments Pie Chart
    const customerCtx = document.getElementById("customerChart").getContext("2d");
    new Chart(customerCtx, {
      type: "pie",
      data: {
        labels: ["New Customers", "Regular", "VIP", "Inactive"],
        datasets: [{
          data: [25, 45, 20, 10],
          backgroundColor: [
            secondaryColor,
            primaryColor,
            successColor,
            warningColor,
          ],
          borderColor: "#fff",
          borderWidth: 2,
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });

    // Traffic Sources Bar Chart
    const trafficCtx = document.getElementById("trafficChart").getContext("2d");
    new Chart(trafficCtx, {
      type: "bar",
      data: {
        labels: ["Direct", "Organic", "Social", "Email", "Referral"],
        datasets: [{
          label: "Visitors",
          data: [3200, 5600, 2800, 1800, 1200],
          backgroundColor: [
            primaryColor,
            successColor,
            warningColor,
            dangerColor,
            secondaryColor,
          ],
          borderRadius: 6,
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "#f0f0f0",
            },
          },
        },
      },
    });

    function applyFilter() {
      alert("Filter applied! In a real app, this would reload data.");
    }

    function resetFilter() {
      document.getElementById("dateRange").value = "";
      document.getElementById("category").value = "All Categories";
      alert("Filter reset!");
    }
  </script>
</body>

</html>