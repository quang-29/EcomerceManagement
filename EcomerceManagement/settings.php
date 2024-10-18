<?php
    require 'includes/header.php';
?>

<div class="container">
    <hr class="my-4">
    <div class="row settings-section">
        <div class="col-lg-5 mb-4">
            <h3 class="section-title">Gói Dịch Vụ</h3>
            <div class="section-intro">Cài đặt cho gói dịch vụ hiện tại của bạn. Thông tin chi tiết và cách nâng cấp, hạ cấp gói dịch vụ có thể được tìm thấy <a href="help.html">tại đây</a>.</div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <div class="mb-2"><strong>Gói hiện tại:</strong> Chuyên Nghiệp</div>
                    <div class="mb-2"><strong>Trạng thái:</strong> <span class="badge bg-success">Đang hoạt động</span></div>
                    <div class="mb-2"><strong>Hạn sử dụng:</strong> 24-09-2030</div>
                    <div class="mb-4"><strong>Hóa đơn:</strong> <a href="#">xem chi tiết</a></div>
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <a class="btn app-btn-primary" href="#">Nâng cấp gói</a>
                        </div>
                        <div class="col-auto">
                            <a class="btn app-btn-secondary" href="#">Hủy gói</a>
                        </div>
                    </div>
                </div><!--//app-card-body-->
            </div><!--//app-card-->
        </div>
    </div><!--//row-->
    
    <hr class="my-4">
    
    <div class="row settings-section">
        <div class="col-lg-5 mb-4">
            <h3 class="section-title">Dữ Liệu &amp; Quyền Riêng Tư</h3>
            <div class="section-intro">Quản lý các cài đặt liên quan đến quyền riêng tư và dữ liệu cá nhân của bạn. Vui lòng lựa chọn các tùy chọn mà bạn muốn kích hoạt.</div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="settings-checkbox-1" checked>
                            <label class="form-check-label" for="settings-checkbox-1">
                                Lưu lịch sử hoạt động của ứng dụng người dùng
                            </label>
                        </div><!--//form-check-->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="settings-checkbox-2" checked>
                            <label class="form-check-label" for="settings-checkbox-2">
                                Lưu lại sở thích cá nhân của người dùng
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="settings-checkbox-3">
                            <label class="form-check-label" for="settings-checkbox-3">
                                Lưu lại lịch sử tìm kiếm của người dùng
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="settings-checkbox-4">
                            <label class="form-check-label" for="settings-checkbox-4">
                                Cho phép hiển thị quảng cáo tùy chỉnh
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="settings-checkbox-5">
                            <label class="form-check-label" for="settings-checkbox-5">
                                Nhận thông báo về chính sách bảo mật
                            </label>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn app-btn-primary">Lưu Thay Đổi</button>
                        </div>
                    </form>
                </div><!--//app-card-body-->
            </div><!--//app-card-->
        </div>
    </div><!--//row-->
    
    <hr class="my-4">
    
    <div class="row settings-section">
        <div class="col-lg-5 mb-4">
            <h3 class="section-title">Thông Báo</h3>
            <div class="section-intro">Quản lý các tùy chọn thông báo của bạn. Bạn có thể bật hoặc tắt các thông báo theo nhu cầu cá nhân.</div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="settings-switch-1" checked>
                            <label class="form-check-label" for="settings-switch-1">Thông báo dự án</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="settings-switch-2">
                            <label class="form-check-label" for="settings-switch-2">Thông báo đẩy qua trình duyệt</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="settings-switch-3" checked>
                            <label class="form-check-label" for="settings-switch-3">Thông báo đẩy qua điện thoại</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="settings-switch-4">
                            <label class="form-check-label" for="settings-switch-4">Thông báo về các hoạt động khác</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="settings-switch-5">
                            <label class="form-check-label" for="settings-switch-5">Thông báo khác</label>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn app-btn-primary">Lưu Thay Đổi</button>
                        </div>
                    </form>
                </div><!--//app-card-body-->
            </div><!--//app-card-->
        </div>
    </div><!--//row-->
    
    <hr class="my-4">
</div><!--//container-fluid-->

<?php
require('includes/footer.php');
?>
