<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thùng Rác Lớp Học</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        /* Thêm CSS tùy chỉnh tại đây */
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách thùng rác lớp học</h2>
        <?php
        include_once("connect.php");

        // Viết câu truy vấn để lấy lớp học đã bị xóa
        $sql = "SELECT * FROM lophoc WHERE is_deleted = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-hover'>";
            echo "<tr><th>Mã lớp</th><th>Tên lớp</th><th>Ghi chú</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["maLop"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["tenLop"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ghiChu"]) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning'>Không có lớp nào trong thùng rác.</div>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
