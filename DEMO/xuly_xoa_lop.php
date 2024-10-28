<?php
include_once("connect.php");

if (isset($_GET['maLop'])) {
    $maLop = $_GET['maLop'];

    // Kiểm tra xem lớp học có tồn tại không
    $checkClassSql = "SELECT * FROM lophoc WHERE maLop = ? AND is_deleted = 0";
    $stmt = $conn->prepare($checkClassSql);
    $stmt->bind_param("s", $maLop);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Đánh dấu lớp học là đã xóa (chuyển vào thùng rác)
        $updateSql = "UPDATE lophoc SET is_deleted = 1 WHERE maLop = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("s", $maLop);
        $stmt->execute();

        // Chuyển sinh viên vào thùng rác (nếu có)
        $sql_sv = "SELECT * FROM sinhvien WHERE maLop = ? AND is_deleted = 0";
        $stmt = $conn->prepare($sql_sv);
        $stmt->bind_param("s", $maLop);
        $stmt->execute();
        $result_sv = $stmt->get_result();
        $sinhvien_data = [];

        while ($row_sv = $result_sv->fetch_assoc()) {
            $sinhvien_data[] = $row_sv; // Lưu thông tin sinh viên vào mảng
        }

        // Chuyển thông tin sinh viên vào thùng rác
        if (count($sinhvien_data) > 0) {
            $sinhvien_json = json_encode($sinhvien_data);
            $sql_insert_trash = "INSERT INTO thungrac (maLop, tenLop, sinhvien) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_insert_trash);
            $stmt->bind_param("sss", $maLop, $lop['tenLop'], $sinhvien_json);
            $stmt->execute();
        }

        echo "<div class='alert alert-success'>Lớp học đã được chuyển vào thùng rác.</div>";
    } else {
        // Nếu lớp học không tồn tại hoặc đã bị xóa trước đó
        echo "<div class='alert alert-danger'>Lớp học không tồn tại hoặc đã bị xóa.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Mã lớp không hợp lệ.</div>";
}
?>
