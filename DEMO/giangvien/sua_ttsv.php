<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối cơ sở dữ liệu
include_once("../connect.php");

// Kiểm tra xem có mã sinh viên không
if (isset($_GET['maSV'])) {
    $maSV = $_GET['maSV'];

    // Truy vấn để lấy thông tin sinh viên
    $stmt = $conn->prepare("SELECT * FROM sinhvien WHERE maSV = ?");
    $stmt->bind_param("s", $maSV);
    $stmt->execute();
    $result = $stmt->get_result();
    $sinhVien = $result->fetch_assoc();
    $stmt->close();

    if (!$sinhVien) {
        echo "<div class='alert alert-danger'>Không tìm thấy sinh viên.</div>";
        exit();
    }

    // Xử lý cập nhật thông tin khi gửi form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $hoLot = $_POST['hoLot'];
        $tenSV = $_POST['tenSV'];
        $ngaySinh = $_POST['ngaySinh'];
        $gioiTinh = $_POST['gioiTinh'];

        $updateSql = "UPDATE sinhvien SET hoLot = ?, tenSV = ?, ngaySinh = ?, gioiTinh = ? WHERE maSV = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssss", $hoLot, $tenSV, $ngaySinh, $gioiTinh, $maSV);

        if ($updateStmt->execute()) {
            // Lưu tên sinh viên vừa cập nhật vào session
            $_SESSION['success_message'] = "Thông tin sinh viên đã được cập nhật thành công!";
            $_SESSION['highlighted_student'] = $tenSV;

            // Chuyển hướng về trang sinhvien.php với thông báo
            header("Location: ../giangvien/sinhvien.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Có lỗi xảy ra khi cập nhật.</div>";
        }

        $updateStmt->close();
    }
} else {
    // Nếu không có mã sinh viên, chuyển hướng về trang danh sách
    header("Location: ../giangvien/sinhvien.php");
    exit();
}
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
            <div class="col-sm-3 left-column" style="width: 20%; height: 110vh;">
                <h4>Thông tin lớp học</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="../giangvien/lophoc.php" class="btn btn-default">Xem lớp</a></li>
                    <li><a href="#" class="btn btn-default" onclick="showAccessDeniedMessage();">Thêm lớp</a></li>
                    <li><a href="#" class="btn btn-default" onclick="showAccessDeniedMessage()">Sửa lớp</a></li>
                    <li><a href="#" class="btn btn-default" onclick="showAccessDeniedMessage()">Xóa lớp</a></li>
                </ul>

                <h4>Thông tin sinh viên</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="../giangvien/sinhvien.php" class="btn btn-default">Xem sinh viên</a></li>
                    <li><a href="xem_ttsv.php?maSV=<?php echo urlencode($row['maSV']); ?>" class="btn btn-link">Xem
                            thông tin sinh viên</a></li>
                    <li><a href="../giangvien/form_sv.php" class="btn btn-default">Thêm sinh viên</a></li>
                    <li><a href="../giangvien/sua_sv.php" class="btn btn-default">Sửa sinh viên</a></li>
                    <li><a href="../giangvien/xoa_sv.php" class="btn btn-default">Xóa sinh viên</a></li>
                </ul>

                <h4>Thùng rác</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="#" class="btn btn-default" onclick="showAccessDeniedMessage();">Danh sách
                            lớp đã xóa</a>
                    <li><a href="../giangvien/thungrac_sv.php" class="btn btn-default">Xem danh sách sinh viên đã
                            xóa</a></li>
                </ul>
            </div>
            <div class="col-sm-9"><br>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <form action="../giangvien/search.php" method="GET" class="input-group"
                        style="flex: 1; margin-right: 10px;">
                        <input type="text" class="form-control" name="query" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </form>

                    <div class="user-info">
                        <a href="../giangvien/logout.php" class="btn btn-sm" title="Đăng xuất"
                            style="background-color: navy; color: white; border: none;">
                            <i class="fas fa-sign-out-alt" style="font-size: 24px;"></i>
                        </a>
                    </div>
                </div>

                <!-- Giao diện chỉnh sửa -->
                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin: 0 auto;">
                    <h2>Chỉnh sửa thông tin sinh viên</h2>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="maSV" value="<?php echo htmlspecialchars($sinhVien['maSV']); ?>">
                    <div class="form-group">
                        <label for="hoLot">Họ lót:</label>
                        <input type="text" class="form-control" name="hoLot"
                            value="<?php echo htmlspecialchars($sinhVien['hoLot']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tenSV">Tên sinh viên:</label>
                        <input type="text" class="form-control" name="tenSV"
                            value="<?php echo htmlspecialchars($sinhVien['tenSV']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ngaySinh">Ngày sinh:</label>
                        <input type="date" class="form-control" name="ngaySinh"
                            value="<?php echo htmlspecialchars($sinhVien['ngaySinh']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gioiTinh">Giới tính:</label><br>
                        <label class="radio-inline">
                            <input type="radio" name="gioiTinh" value="Nam"
                                <?php if ($sinhVien['gioiTinh'] == 'Nam') echo 'checked'; ?>> Nam
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="gioiTinh" value="Nữ"
                                <?php if ($sinhVien['gioiTinh'] == 'Nữ') echo 'checked'; ?>> Nữ
                        </label>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>
</body>

</html>

<?php
// Đóng kết nối
$conn->close();
?>