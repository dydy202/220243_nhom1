<?php
session_start(); // Bắt đầu phiên làm việc

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: index.php?message=Bạn cần đăng nhập để truy cập trang này.");
    exit; // Dừng thực thi mã tiếp theo
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    <script>
    function confirmDelete(maLop) {
        if (confirm("Bạn có chắc chắn muốn xóa lớp này vĩnh viễn?")) {
            window.location.href = 'thungrac_lop.php?maLop=' + maLop + '&action=delete';
        }
    }

    function confirmRestore(maLop) {
        if (confirm("Bạn có chắc chắn muốn khôi phục lớp này?")) {
            window.location.href = 'thungrac_lop.php?maLop=' + maLop + '&action=restore';
        }
    }
    </script>
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

                <?php
                include_once("connect.php");

                // Truy vấn lấy các lớp học đã bị đánh dấu là đã xóa
                $sql = "SELECT * FROM lophoc WHERE is_deleted = 1";
                $result = $conn->query($sql);
                ?>

                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin: 0 auto;">
                    <h2>Thùng rác - Lớp học đã xóa</h2>
                </div>

                <div class="table-container mt-3">
                    <?php
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-hover'>";
                        echo "<tr><th>Mã lớp</th><th>Tên lớp</th><th>Ghi chú</th><th>Thao tác</th></tr>";

                        // Duyệt qua các dòng dữ liệu và thêm liên kết Khôi phục và Xóa
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["maLop"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["tenLop"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["ghiChu"]) . "</td>";
                            echo "<td>
                <a href='javascript:void(0);' onclick='confirmRestore(\"" . htmlspecialchars($row['maLop']) . "\")' class='btn btn-link'>Khôi phục</a> | 
                <a href='javascript:void(0);' onclick='confirmDelete(\"" . htmlspecialchars($row['maLop']) . "\")' class='btn btn-link'>Xóa vĩnh viễn</a>
              </td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "<div class='alert alert-warning'>Không có lớp nào trong thùng rác.</div>";
                    }

                    // Xử lý hành động khôi phục hoặc xóa vĩnh viễn
                    if (isset($_GET['maLop'])) {
                        $maLop = $_GET['maLop'];
                        $action = isset($_GET['action']) ? $_GET['action'] : ''; // Kiểm tra hành động của người dùng

                        if ($action === 'restore') {
                            // Khôi phục lớp học bằng cách đặt is_deleted = 0
                            $restoreSql = "UPDATE lophoc SET is_deleted = 0 WHERE maLop = ?";
                            $stmt = $conn->prepare($restoreSql);
                            $stmt->bind_param("s", $maLop);
                            $stmt->execute();
                            echo "<div class='alert alert-success'>Lớp đã được khôi phục thành công.</div>";
                        } elseif ($action === 'delete') {
                            // Xử lý xóa vĩnh viễn
                            // Kiểm tra xem lớp có sinh viên hay không
                            $checkStudentsSql = "SELECT COUNT(*) as count FROM sinhvien WHERE maLop = ?";
                            $stmt = $conn->prepare($checkStudentsSql);
                            $stmt->bind_param("s", $maLop);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();

                            if ($row['count'] > 0) {
                                // Nếu lớp có sinh viên, yêu cầu chuyển sinh viên sang lớp khác
                                if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
                                    // Xác nhận chọn lớp mới để chuyển sinh viên
                                    if (isset($_POST['newLop'])) {
                                        $newLop = $_POST['newLop'];

                                        // Cập nhật maLop của sinh viên sang lớp mới
                                        $updateStudentSql = "UPDATE sinhvien SET maLop = ? WHERE maLop = ?";
                                        $stmt = $conn->prepare($updateStudentSql);
                                        $stmt->bind_param("ss", $newLop, $maLop);
                                        $stmt->execute();

                                        // Sau khi cập nhật, xóa lớp
                                        $deleteSql = "DELETE FROM lophoc WHERE maLop = ?";
                                        $stmt = $conn->prepare($deleteSql);
                                        $stmt->bind_param("s", $maLop);
                                        $stmt->execute();

                                        echo "<div class='alert alert-success'>Lớp đã được xóa thành công. Các sinh viên đã được chuyển sang lớp mới.</div>";
                                    } else {
                                        // Hiển thị danh sách lớp để người dùng chọn lớp mới
                                        echo "<div class='alert alert-warning'>Vui lòng chọn lớp mới để chuyển sinh viên trước khi xóa lớp.</div>";

                                        $classesSql = "SELECT * FROM lophoc WHERE is_deleted = 0";
                                        $classesResult = $conn->query($classesSql);

                                        if ($classesResult->num_rows > 0) {
                                            echo "<form method='post' action='thungrac_lop.php?maLop=" . $maLop . "&action=delete&confirm=yes'>";
                                            echo "<div class='form-group'>";
                                            echo "<label for='newLop'>Chọn lớp mới:</label>";
                                            echo "<select name='newLop' id='newLop' class='form-control'>";
                                            while ($classRow = $classesResult->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($classRow['maLop']) . "'>" . htmlspecialchars($classRow['tenLop']) . "</option>";
                                            }
                                            echo "</select>";
                                            echo "</div>"; // End form-group
                                            echo "<div class='form-group' style='margin-top: 15px;'>"; // Add margin-top to create space between select and button
                                            echo "<button type='submit' class='btn btn-primary'>Chuyển sinh viên và xóa lớp</button>";
                                            echo "</div>"; // End form-group for button
                                            echo "</form>";
                                        } else {
                                            echo "<div class='alert alert-danger'>Không có lớp nào khả dụng để chuyển sinh viên.</div>";
                                        }
                                    }
                                } else {
                                    // Hiển thị thông báo xác nhận nếu người dùng chưa xác nhận
                                    echo "<div class='alert alert-warning'>Lớp học có sinh viên. Bạn có chắc chắn muốn xóa lớp không?</div>";
                                    echo "<a href='thungrac_lop.php?maLop=" . $maLop . "&action=delete&confirm=yes' class='btn btn-danger'>Chuyển sinh viên trước khi xóa</a>";
                                    echo "<a href='thungrac_lop.php' class='btn btn-secondary'>Hủy</a>";
                                }
                            } else {
                                // Nếu lớp không có sinh viên, xóa lớp ngay lập tức
                                $deleteSql = "DELETE FROM lophoc WHERE maLop = ?";
                                $stmt = $conn->prepare($deleteSql);
                                $stmt->bind_param("s", $maLop);
                                $stmt->execute();

                                echo "<div class='alert alert-success'>Lớp đã được xóa thành công.</div>";
                            }
                        }
                    }

                    $conn->close();
                    ?>
                </div>

                <script>
                function confirmRestore(maLop) {
                    if (confirm("Bạn có chắc chắn muốn khôi phục lớp này?")) {
                        window.location.href = 'thungrac_lop.php?maLop=' + maLop + '&action=restore';
                    }
                }

                function confirmDelete(maLop) {
                    if (confirm("Bạn có chắc chắn muốn xóa lớp này vĩnh viễn?")) {
                        window.location.href = 'thungrac_lop.php?maLop=' + maLop + '&action=delete';
                    }
                }
                </script>

            </div>
        </div>

    </div>
    </div>

</body>

</html>