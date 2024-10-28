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
                    <li><a href="xem_ttsv.php" class="btn btn-default">Xem thông tin sinh viên</a></li>
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
                        <a href="logout.php" class="btn btn-sm" title="Đăng xuất" style="background-color: navy; color: white; border: none;">
                            <i class="fas fa-sign-out-alt" style="font-size: 24px;"></i>
                        </a>
                    </div>

                </div>

                <div id="classInfo" class="mt-3">
                    <!-- Thông tin lớp học sẽ được tải vào đây -->
                    <p style="text-align:center;">Vui lòng chọn để xem thông tin.</p>
                </div>
            </div>

        </div>
    </div>

</body>

</html>