<?php
session_start(); // Bắt đầu phiên làm việc

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: index.php?message=Bạn cần đăng nhập để truy cập trang này.");
    exit; // Dừng thực thi mã tiếp theo
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
            <div class="col-sm-3 left-column" style="width: 20%;  min-height: 200px;">
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

                <?php
                include_once("../connect.php");

                // Viết câu truy vấn để chỉ lấy sinh viên chưa xóa
                $sql = "SELECT * FROM sinhvien WHERE is_deleted = 0";
                $result = $conn->query($sql);
                ?>

                <?php
                // Kiểm tra và hiển thị thông báo
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-" . $_SESSION['msg_type'] . "'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
                    unset($_SESSION['msg_type']); // Xóa kiểu thông báo
                }
                ?>

                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin: 0 auto;">
                    <h2>Danh sách sinh viên</h2>
                </div>

                <div class="table-container mt-3">
                    <?php
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-hover'>";
                        echo "<tr><th>Mã SV</th><th>Họ Lót</th><th>Tên</th><th>Ngày Sinh</th><th>Giới Tính</th><th>Mã Lớp</th><th>Thao tác</th></tr>";

                        // Duyệt qua các dòng dữ liệu và thêm liên kết Xóa
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["maSV"]) . "</td>"; // Bảo vệ dữ liệu đầu vào
                            echo "<td>" . htmlspecialchars($row["hoLot"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["tenSV"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["ngaySinh"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["gioiTinh"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["maLop"]) . "</td>";
                            echo "<td>
                    <a href='javascript:void(0);' onclick='confirmDelete(\"" . htmlspecialchars($row['maSV']) . "\")' class='btn btn-link'>Xóa</a></td>"; // Gọi hàm xác nhận
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "<div class='alert alert-warning'>Không có sinh viên nào.</div>"; // Thay đổi cách hiển thị không có dữ liệu
                    }

                    // Xử lý xóa sinh viên
                    if (isset($_GET['maSV'])) {
                        $maSV = $_GET['maSV'];

                        // Kiểm tra xem sinh viên có tồn tại không
                        $checkStudentSql = "SELECT * FROM sinhvien WHERE maSV = ? AND is_deleted = 0";
                        $stmt = $conn->prepare($checkStudentSql);
                        $stmt->bind_param("s", $maSV);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            // Đánh dấu sinh viên là đã xóa (chuyển vào thùng rác)
                            $updateSql = "UPDATE sinhvien SET is_deleted = 1 WHERE maSV = ?";
                            $stmt = $conn->prepare($updateSql);
                            $stmt->bind_param("s", $maSV);
                            $stmt->execute();
                            echo "<div class='alert alert-success'>Sinh viên đã được chuyển vào thùng rác.</div>";
                        } else {
                            // Nếu sinh viên không tồn tại hoặc đã bị xóa trước đó
                            echo "<div class='alert alert-danger'>Sinh viên không tồn tại hoặc đã bị xóa.</div>";
                        }
                    }

                    $conn->close();
                    ?>
                </div>

                <script>
                    // Hàm xác nhận xóa sinh viên
                    function confirmDelete(maSV) {
                        if (confirm("Bạn có chắc chắn muốn xóa sinh viên này không?")) {
                            window.location.href = '../giangvien/xoa_sv.php?maSV=' + maSV;
                        }
                    }
                </script>

            </div>
        </div>

    </div>
    </div>

</body>

</html>

<!-- Thêm thông báo không có quyền truy cập -->
<div id="accessDeniedMessage" class="alert alert-danger"
    style="display: none; text-align: center; border: 2px solid #f3c6cb; background-color: #f9d6d8; padding: 20px; border-radius: 10px; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
    Bạn không có quyền truy cập!
</div>

<!-- JavaScript để hiển thị và ẩn thông báo -->
<script>
    function showAccessDeniedMessage() {
        var message = document.getElementById("accessDeniedMessage");
        message.style.display = "block"; // Hiển thị thông báo

        // Ẩn thông báo sau 1.1 giây
        setTimeout(function() {
            message.style.display = "none"; // Ẩn thông báo
        }, 1100);
    }

    // Thêm sự kiện click cho các nút không cho phép, ngoại trừ 'Xem lớp'
    document.querySelectorAll('.nav-pills > li > a.btn-default').forEach(function(button) {
        if (button.innerText.includes("Thêm lớp") || button.innerText.includes("Sửa lớp") ||
            button.innerText.includes("Xóa lớp") || button.innerText.includes(
                "Danh sách lớp đã xóa")) {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn chặn hành động mặc định của liên kết
                showAccessDeniedMessage();
            });
        }
    });
</script>

<?php
ob_end_flush(); // Kết thúc output buffering
?>