Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

// Biểu đồ cột
var ctx = document.getElementById("myChart");
var myBarChart;

// Hàm khởi tạo biểu đồ cột
function initBarChart(data) {
    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels, // Nhãn cho các cột
            datasets: [{
                label: "Doanh Thu",
                backgroundColor: "rgba(78, 115, 223, 0.5)", // Màu cột
                hoverBackgroundColor: "rgba(78, 115, 223, 0.7)", // Màu khi hover
                borderColor: "rgba(78, 115, 223, 1)",
                data: data.revenue, // Dữ liệu doanh thu
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    },
                    maxBarThickness: 25,
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return number_format(value) + ' VND';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' VND';
                    }
                }
            }
        }
    });
}

// Biến toàn cục để lưu biểu đồ doanh thu
var myChart;

$(document).ready(function() {
    // Khởi tạo biểu đồ cột với dữ liệu mặc định
    initBarChart({ labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], revenue: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] });

    $('.branch-item').click(function(e) {
        e.preventDefault();
        var branchId = $(this).data('branch-id');

        // Gửi yêu cầu AJAX để lấy dữ liệu
        $.ajax({
            url: 'getRevenue.php',
            method: 'POST',
            data: { branch_id: branchId },
            success: function(response) {

              try {
                var revenueData = JSON.parse(response);
                updateChart(revenueData);
            } catch (error) {
                console.error("Error parsing JSON:", error);
            }
            }
        });
    });

    function updateChart(data) {
        // Cập nhật biểu đồ cột
        if (myBarChart) {
            myBarChart.destroy();
        }

        initBarChart(data); // Khởi tạo lại biểu đồ với dữ liệu mới

        var ctx = document.getElementById('myChart').getContext('2d');

        // Nếu biểu đồ đã tồn tại thì phải xóa trước khi vẽ biểu đồ mới
        if (myChart) {
            myChart.destroy();
        }

        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels, // Tháng hoặc ngày
                datasets: [{
                    label: 'Doanh Thu',
                    data: data.revenue, // Doanh thu tương ứng
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
