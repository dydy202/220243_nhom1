<?php
include_once("connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy mã lớp từ GET
$maLop = isset($_GET['maLop']) ? $_GET['maLop'] : '';

// Lấy danh sách sinh viên theo mã lớp
$sql = "SELECT * FROM sinhvien WHERE maLop = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maLop);
$stmt->execute();
$result = $stmt->get_result();
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

        .notification {
            border: 2px solid pink;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #f9f9f9;
            color: #333;
            max-width: 600px;
            text-align: center;
        }

        .notification.error {
            background-color: #ffe6e6;
        }

        .notification.success {
            background-color: #e6ffcc;
        }

        .alert.error {
            border: 2px solid pink;
            background-color: #ffe6e6;
            color: red;
            /* Màu chữ đỏ */
        }

        .success-message {
            background-color: #e8f5e9;
            /* Màu nền xanh nhạt */
            color: green;
            /* Màu chữ xanh */
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            /* Bo góc khung */
        }

        .alert.success {
            background-color: #e8f5e9;
            /* Nền nhạt hơn */
            color: green;
            /* Màu chữ xanh */
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-3 left-column" style="width: 20%; min-height: 200px;">
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
                    <li><a href="xuly_themsv.php" class="btn btn-default">Thêm sinh viên</a></li>
                    <li><a href="sua_sv.php" class="btn btn-default">Sửa sinh viên</a></li>
                    <li><a href="xoa_sv.php" class="btn btn-default">Xóa sinh viên</a></li>
                </ul>

                <h4>Thùng rác</h4>
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="thungrac_lop.php" class="btn btn-default">Xem danh sách lớp đã xóa</a></li>
                    <li><a href="thungrac_sv.php" class="btn btn-default">Xem danh sách sinh viên đã xóa</a></li>
                </ul>
            </div>

            <div class="col-sm-9"><br>
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

                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin: 0 auto;">
                    <h2>Danh sách sinh viên lớp <?php echo htmlspecialchars($maLop); ?></h2>
                </div>

                <?php if (isset($_SESSION['messages'])): ?>
                    <?php foreach ($_SESSION['messages'] as $message): ?>
                        <div class="alert <?php echo $message['type']; ?>">
                            <?php echo $message['text']; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['messages']); // Xóa thông báo sau khi hiển thị 
                    ?>
                <?php endif; ?>

                <div id="message-area">
                    <?php
                    if (isset($_SESSION['messages'])):
                        foreach ($_SESSION['messages'] as $message):
                            echo "<div class='alert alert-success' role='alert'>{$message['text']}</div>";
                        endforeach;
                        unset($_SESSION['messages']); // Clear messages after display
                    endif;
                    ?>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã sinh viên</th>
                            <th>Họ</th>
                            <th>Tên sinh viên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['maSV']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hoLot']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tenSV']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['ngaySinh']))); ?></td>
                                    <td><?php echo htmlspecialchars($row['gioiTinh']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có sinh viên nào trong lớp này</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Form import file Excel -->
                <div class="form-container">
                    <form action="import_excel.php" method="post" enctype="multipart/form-data">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <input type="file" name="file" accept=".xlsx, .xls" required>
                            <a href="download_template.php" class="btn-download"
                                style="color: black; text-decoration: none;">Tải file nhập dữ liệu</a>
                        </div>

                        <br>

                        <button type="submit" class="btn btn-success">Import Excel</button>
                    </form>
                </div>

                <div style="margin-top: 20px;">
                    <a href="export_excel_dssv_theolop.php?maLop=<?php echo urlencode($maLop); ?>"
                        class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="export_dssv_lop_pdf.php?maLop=<?php echo urlencode($maLop); ?>" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a><br><br>
                </div>
            </div>
        </div>
    </div>
</body>

</html>