<?php
session_start(); // Bắt đầu phiên làm việc

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: index.php?message=Bạn cần đăng nhập để truy cập trang này.");
    exit; // Dừng thực thi mã tiếp theo
}
include_once("connect.php"); // Kết nối cơ sở dữ liệu

// Lấy danh sách các lớp từ cơ sở dữ liệu
$sql = "SELECT maLop, tenLop FROM lophoc WHERE is_deleted = 0";
$resultLop = $conn->query($sql);
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Quản Lý Lớp Học và Sinh Viên</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        .container-fluid {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .row.content {
            display: flex;
            flex-wrap: wrap;
            /* Cho phép phần tử con tự động xuống dòng */
            flex: 1;
        }

        .left-column {
            background-color: #f1f1f1;
            padding: 15px;
            flex: 0 0 auto;
            /* Chiều cao tự động thay đổi theo nội dung */
        }

        .col-sm-9,
        .left-column {
            max-width: 100%;
            /* Đảm bảo các phần không vượt quá màn hình */
        }

        footer {
            background-color: #555;
            color: white;
            padding: 15px;
        }

        @media screen and (max-width: 767px) {
            .left-column {
                height: auto;
                padding: 15px;
            }

            .row.content {
                height: auto;
            }
        }

        h4 a,
        h5 a {
            font-weight: bold;
            /* In đậm */
            color: black;
            /* Màu đen */
            text-decoration: none;
            /* Không gạch chân */
        }

        h4 a:hover,
        h5 a:hover {
            color: #000;
            /* Giữ màu đen khi hover */
        }

        h4 a:focus,
        h5 a:focus {
            text-decoration: none;
            /* Không gạch chân khi focus */
            color: black;
            /* Màu đen khi được chọn */
        }

        .nav-pills>li>a {
            border-radius: 0;
            background-color: transparent;
            color: #333;
            border: none;
            text-align: left;
        }

        .nav-pills>li>a:hover {
            background-color: #ddd;
            color: #000;
        }

        #classInfo {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            /* Đảm bảo chiếm hết chiều cao */
        }

        .table-container {
            max-width: 100%;
            /* Đảm bảo không vượt quá 100% chiều rộng của phần chứa */
        }

        .table {
            width: 100%;
            table-layout: auto;
            /* Thay đổi về auto để chiều rộng tự động điều chỉnh */
        }

        .table th,
        .table td {
            overflow: hidden;
            /* Ngăn không cho nội dung vượt ra ngoài ô */
            text-overflow: ellipsis;
            /* Thêm ba chấm (...) khi văn bản quá dài */
            white-space: normal;
            /* Cho phép ngắt dòng */
            padding: 8px;
            /* Tăng khoảng cách giữa nội dung và viền ô */
            max-width: 150px;
            /* Giới hạn chiều rộng tối đa */
        }

        .row.content {
            display: flex;
            flex-wrap: wrap;
            /* Cho phép ngắt dòng */
        }

        .left-column {
            flex: 0 0 20%;
            /* Sử dụng flex để kiểm soát chiều rộng */
            min-width: 200px;
            /* Chiều rộng tối thiểu */
        }

        .col-sm-9 {
            flex: 0 0 75%;
            /* Tương tự như left-column */
            max-width: 75%;

            /* Không vượt quá 75% chiều rộng */
        }

        .nav-pills>li>a {
            white-space: normal;
            /* Cho phép ngắt dòng */
            overflow: hidden;
            /* Ẩn nội dung tràn ra */
            text-overflow: ellipsis;
            /* Thêm ba chấm (...) khi văn bản quá dài */
            max-width: 100%;
            /* Đảm bảo không vượt quá chiều rộng của ô chứa */
            display: block;
            /* Để chiếm toàn bộ chiều rộng */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-3 left-column" style="width: 20%;">
                <h4>Thông tin lớp học</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="lophoc.php" class="btn btn-default">Xem lớp</a></li>
                    <li><a href="form_lop.php" class="btn btn-default">Thêm lớp</a></li>
                    <li><a href="sua_lop.php" class="btn btn-default">Sửa lớp</a></li>
                    <li><a href="xoa_lop.php" class="btn btn-default">Xóa lớp</a></li>
                </ul>

                <h4>Thông tin sinh viên</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="sinhvien.php" class="btn btn-default">Xem sinh viên</a></li>
                    <li><a href="xem_ttsv.php?maSV=<?php echo urlencode($row['maSV']); ?>" class="btn btn-link">Xem
                            thông tin sinh viên</a></li>
                    <li><a href="form_sv.php" class="btn btn-default">Thêm sinh viên</a></li>
                    <li><a href="sua_sv.php" class="btn btn-default">Sửa sinh viên</a></li>
                    <li><a href="xoa_sv.php" class="btn btn-default">Xóa sinh viên</a></li>
                </ul>

                <h4>Thùng rác</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="thungrac_lop.php" class="btn btn-default">Xem danh sách lớp đã xóa</a></li>
                    <li><a href="thungrac_sv.php" class="btn btn-default">Xem danh sách sinh viên đã xóa</a></li>
                </ul>
            </div>

            <div class="col-sm-9" style="min-height: 300px;"><br>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <form action="search.php" method="GET" class="input-group" style="flex: 1; margin-right: 10px;">
                        <input type="text" class="form-control" name="query" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </form>

                    <div class="user-info">
                        <a href="logout.php" class="btn btn-sm" title="Đăng xuất"
                            style="background-color: navy; color: white; border: none;">
                            <i class="fas fa-sign-out-alt" style="font-size: 24px;"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-10" style="margin: 0 auto; float: none;">
                    <h2 style="text-align: center; font-size: 30px; font-weight: bold;">Thêm sinh viên</h2>

                    <!-- Hiển thị thông báo lỗi -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Hiển thị thông báo thành công -->
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form action="xuly_themsv.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="maSV">Mã Sinh Viên:</label>
                            <input type="text" class="form-control" id="maSV" name="maSV" required
                                placeholder="Nhập mã sinh viên"
                                value="<?php echo isset($_POST['maSV']) ? htmlspecialchars($_POST['maSV']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="hoLot">Họ Lót:</label>
                            <input type="text" class="form-control" id="hoLot" name="hoLot" required
                                placeholder="Nhập họ lót"
                                value="<?php echo isset($_POST['hoLot']) ? htmlspecialchars($_POST['hoLot']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="tenSV">Tên Sinh Viên:</label>
                            <input type="text" class="form-control" id="tenSV" name="tenSV" required
                                placeholder="Nhập tên sinh viên"
                                value="<?php echo isset($_POST['tenSV']) ? htmlspecialchars($_POST['tenSV']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="ngaySinh">Ngày Sinh:</label>
                            <input type="date" class="form-control" id="ngaySinh" name="ngaySinh" required
                                value="<?php echo isset($_POST['ngaySinh']) ? htmlspecialchars($_POST['ngaySinh']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="gioiTinh">Giới Tính:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="gioiTinhNam" name="gioiTinh"
                                    value="Nam"
                                    <?php echo (isset($_POST['gioiTinh']) && $_POST['gioiTinh'] == "Nam") ? "checked" : ""; ?>
                                    required>
                                <label class="form-check-label" for="gioiTinhNam">Nam</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="gioiTinhNu" name="gioiTinh" value="Nữ"
                                    <?php echo (isset($_POST['gioiTinh']) && $_POST['gioiTinh'] == "Nữ") ? "checked" : ""; ?>
                                    required>
                                <label class="form-check-label" for="gioiTinhNu">Nữ</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tenLop">Tên Lớp:</label>
                            <select class="form-control" id="tenLop" name="tenLop" required size="5"
                                style="height: auto;">
                                <?php
                                if ($resultLop->num_rows > 0) {
                                    // Lặp qua từng dòng kết quả và hiển thị trong thẻ <option>
                                    while ($row = $resultLop->fetch_assoc()) {
                                        $selected = (isset($_POST['tenLop']) && $_POST['tenLop'] == $row['maLop']) ? "selected" : "";
                                        echo "<option value='" . htmlspecialchars($row['maLop']) . "' $selected>" . htmlspecialchars($row['maLop']) . " - " . htmlspecialchars($row['tenLop']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Không có lớp nào</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hinhAnh">Hình Ảnh:</label>
                            <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>