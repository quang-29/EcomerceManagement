<?php 
    require 'includes/header.php';
    require 'vendor/autoload.php'; 
    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
    $client = new MongoDB\Client($uri);
    
    $database = $client->selectDatabase('test'); 
    $branchCollection = $database->selectCollection('branches');

    // Lấy danh sách các chi nhánh từ MongoDB
    $branches = $branchCollection->find()->toArray();
?>

<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Doanh Thu Theo Tháng
                </h6>
                
                <div class="dropdown no-arrow ">
                   
                    <select id="branchSelect" class="form-control ml-2">
                        <option value="">-- Chọn chi nhánh --</option> <!-- Option mặc định -->
                        <?php foreach($branches as $branch): ?>
                            <option value="<?php echo (string) $branch['_id']; ?>">
                                <?php echo $branch['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myChart" width="1000" height="500"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var myChart; // Biến toàn cục để lưu biểu đồ

    $(document).ready(function() {
        $('#branchSelect').change(function() {
            var branchId = $(this).val(); // Lấy giá trị của option đã chọn

            if (branchId) { // Kiểm tra xem có chọn chi nhánh không
                // Gửi yêu cầu AJAX để lấy dữ liệu
                $.ajax({
                    url: 'getRevenue.php', // Đường dẫn đến file PHP để lấy dữ liệu
                    method: 'POST',
                    data: { branch_id: branchId },
                    success: function(response) {
                        console.log(response); // Kiểm tra phản hồi
                        try {
                            var revenueData = JSON.parse(response); // Phân tích dữ liệu JSON
                            updateChart(revenueData); // Cập nhật biểu đồ với dữ liệu mới
                        } catch (error) {
                            console.error("Error parsing JSON:", error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error); // Xử lý lỗi AJAX
                    }
                });
            }
        });

        function updateChart(data) {
            if (!data.labels || !data.revenue || data.labels.length === 0 || data.revenue.length === 0) {
                console.error("Invalid data:", data);
                return; // Không tiếp tục nếu dữ liệu không hợp lệ
            }

            var ctx = document.getElementById('myChart').getContext('2d');

            // Nếu biểu đồ đã tồn tại thì phải xóa trước khi vẽ biểu đồ mới
            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'bar', // Thay đổi loại biểu đồ thành 'bar'
                data: {
                    labels: data.labels, // Tháng hoặc ngày
                    datasets: [{
                        label: 'Doanh Thu',
                        data: data.revenue, // Doanh thu tương ứng
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Màu nền cho cột
                        borderColor: 'rgba(54, 162, 235, 1)', // Màu viền cho cột
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true,
                            barPercentage: 1, // Điều chỉnh độ rộng của cột
                            categoryPercentage: 2 // Điều chỉnh tỷ lệ giữa các nhóm cột
                        },
                        y: {
                            beginAtZero: true // Bắt đầu trục Y từ 0
                        }
                    }
                }
            });
        }
    });
</script>




<?php
require('includes/footer.php');
?>

