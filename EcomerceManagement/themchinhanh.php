<?php
require 'includes/header.php';
?>

<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Thêm Chi Nhánh</h1>
                        </div>
                        <form class="branch" method="post" action="addbranch.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">Branch Name:</label>

                                <input type="text" class="form-control form-control-user" id="name" name="name" placeholder="Branch name" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Branch Code</label>
                                <input type="text" class="form-control form-control-user" id="branchCode" name="branchCode" placeholder="Branch code" required>

                            </div>

                            <div class="form-group">
                                <label class="form-label">Address:</label>
                                <textarea name="address" class="form-control" placeholder="Address" required></textarea>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-7 mb-sm-0">
                                    <label class="form-label">Email:</label>
                                    <input type="text" class="form-control form-control-user" id="email" name="email" placeholder="Email" required>
                                </div>
                                <div class="col-sm-5 mb-sm-0">
                                    <label class="form-label">Phone number:</label>
                                    <input type="text" class="form-control form-control-user" id="phoneNumber" name="phoneNumber" placeholder="Phone number" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Established:</label>
                                <input type="date" class="form-control form-control-user" id="established" name="established" required>
                            </div>


                            <button class="btn btn-success">Thêm</button>
                        </form>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'includes/footer.php';
?>
