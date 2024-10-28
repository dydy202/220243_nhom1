<?php
include_once("connect.php"); // Kết nối đến cơ sở dữ liệu

// Lấy từ khóa tìm kiếm từ query string
$query = isset($_GET['query']) ? $_GET['query'] : '';

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
        .row.content {
            height: 100vh;
        }

        .left-column {
            background-color: #f1f1f1;
            padding: 15px;
            height: 100%;
        }

        .list-group {
            margin-top: 20px;
            /* Điều chỉnh khoảng cách giữa tiêu đề và danh sách */
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-3 left-column" style="width: 20%;  min-height: 200px;">
                <!-- Thông tin lớp học -->
                <h4>
                    <a data-toggle="collapse" href="#collapseLop">Thông tin lớp học</a>
                </h4>
                <div id="collapseLop" class="panel-collapse collapse">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="lophoc.php" class="btn btn-default">Xem lớp</a></li>
                        <li><a href="form_lop.php" class="btn btn-default">Thêm lớp</a></li>
                        <li><a href="sua_lop.php" class="btn btn-default">Sửa lớp</a></li>
                        <li><a href="xoa_lop.php" class="btn btn-default">Xóa lớp</a></li>
                    </ul>
                </div>

                <!-- Thông tin sinh viên -->
                <h4>
                    <a data-toggle="collapse" href="#collapseSV">Thông tin sinh viên</a>
                </h4>
                <div id="collapseSV" class="panel-collapse collapse">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="sinhvien.php" class="btn btn-default">Xem sinh viên</a></li>
                        <li><a href="xem_ttsv.php" class="btn btn-default">Xem thông tin sinh viên</a></li>
                        <li><a href="xuly_themsv.php" class="btn btn-default">Thêm sinh viên</a></li>
                        <li><a href="sua_sv.php" class="btn btn-default">Sửa sinh viên</a></li>
                        <li><a href="xoa_sv.php" class="btn btn-default">Xóa sinh viên</a></li>
                    </ul>
                </div>

                <!-- Thùng rác -->
                <h4>
                    <a data-toggle="collapse" href="#collapseTrash">Thùng rác</a>
                </h4>
                <div id="collapseTrash" class="panel-collapse collapse">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="thungrac_lop.php" class="btn btn-default">Xem danh sách lớp đã xóa</a></li>
                        <li><a href="thungrac_sv.php" class="btn btn-default">Xem danh sách sinh viên đã xóa</a></li>
                    </ul>
                </div>
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
                        <a href="logout.php" class="btn btn-sm" title="Đăng xuất" style="background-color: navy; color: white; border: none;">
                            <i class="fas fa-sign-out-alt" style="font-size: 24px;"></i>
                        </a>
                    </div>

                </div>

                <?php
                // Kiểm tra xem có từ khóa tìm kiếm không
                if (!empty($query)) {
                    // Truy vấn để tìm kiếm thông tin trong cơ sở dữ liệu
                    $sql = "SELECT l.tenLop, l.maLop, s.hoLot, s.tenSV, s.maSV 
                FROM lophoc l 
                LEFT JOIN sinhvien s ON l.maLop = s.maLop 
                WHERE l.tenLop LIKE ? OR l.maLop LIKE ? OR s.maSV LIKE ? OR s.hoLot LIKE ? OR s.tenSV LIKE ?";
                    $stmt = $conn->prepare($sql);
                    $searchTerm = "%" . $query . "%"; // Thêm ký tự % để tìm kiếm theo kiểu LIKE
                    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Hiển thị tiêu đề kết quả tìm kiếm
                    echo "<h3>Kết quả tìm kiếm cho: " . htmlspecialchars($query) . "</h3>"; // Tiêu đề hiển thị tìm kiếm

                    // Kiểm tra và hiển thị kết quả tìm kiếm
                    echo "<ul class='list-group'>"; // Mở thẻ danh sách
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Hiển thị thông tin lớp học
                            echo "<li class='list-group-item'>" . htmlspecialchars($row['tenLop']) . " (Mã lớp: " . htmlspecialchars($row['maLop']) . ")";

                            // Nếu có sinh viên, hiển thị họ và tên sinh viên
                            if (!empty($row['hoLot']) || !empty($row['tenSV'])) {
                                echo " - Sinh viên: " . htmlspecialchars($row['hoLot']) . " " . htmlspecialchars($row['tenSV']) . " (Mã SV: " . htmlspecialchars($row['maSV']) . ")";
                            }
                            echo "</li>"; // Đóng thẻ danh sách
                        }
                    } else {
                        echo "<p>Không tìm thấy kết quả cho từ khóa: " . htmlspecialchars($query) . "</p>"; // Nếu không có kết quả
                    }
                    echo "</ul>"; // Đóng thẻ danh sách

                    $stmt->close();
                } else {
                    echo "<p>Vui lòng nhập từ khóa để tìm kiếm.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>

    </div>
    </div>

</body>

</html>