<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Build filters
$where = "1";

if (!empty($_POST['department_id'])) {
    $where .= " AND s.department_id = " . (int)$_POST['department_id'];
}
if (!empty($_POST['semester_id'])) {
    $where .= " AND s.semester_id = " . (int)$_POST['semester_id'];
}
if (!empty($_POST['shift_id'])) {
    $where .= " AND s.shift_id = " . (int)$_POST['shift_id'];
}
if (!empty($_POST['subject_id'])) {
    $where .= " AND a.subject_id = " . (int)$_POST['subject_id'];
}
if (!empty($_POST['teacher_id'])) {
    $where .= " AND a.teacher_id = " . (int)$_POST['teacher_id'];
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $where .= " AND a.date BETWEEN '" . $_POST['from_date'] . "' AND '" . $_POST['to_date'] . "'";
}

$sql = "SELECT a.date, a.status, s.name AS student_name, s.roll_no, 
               d.name AS dept_name, sem.number AS semester, sh.name AS shift, 
               sub.name AS subject, t.name AS teacher, s.id as student_id
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        JOIN subjects sub ON a.subject_id = sub.id
        JOIN teachers t ON a.teacher_id = t.id
        JOIN departments d ON s.department_id = d.id
        JOIN semesters sem ON s.semester_id = sem.id
        JOIN shifts sh ON s.shift_id = sh.id
        WHERE $where
        ORDER BY a.date ASC";

$result = $conn->query($sql);

// Pivot data
$pivot = [];
$dates = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sid = $row['student_id'];
        $pivot[$sid]['name'] = $row['student_name'];
        $pivot[$sid]['roll_no'] = $row['roll_no'];
        $pivot[$sid]['dept_name'] = $row['dept_name'];
        $pivot[$sid]['semester'] = $row['semester'];
        $pivot[$sid]['shift'] = $row['shift'];
        $pivot[$sid]['subject'] = $row['subject'];
        $pivot[$sid]['teacher'] = $row['teacher'];
        $pivot[$sid]['attendance'][$row['date']] = $row['status'];

        if (!in_array($row['date'], $dates)) {
            $dates[] = $row['date'];
        }
    }
}

// Sort dates
sort($dates);

// Send headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_report.csv"');

// Open output stream
$output = fopen("php://output", "w");

// Heading info
$first = reset($pivot);
fputcsv($output, [
    'Department: ' . ($first['dept_name'] ?? ''),
    'Semester: ' . ($first['semester'] ?? ''),
    'Shift: ' . ($first['shift'] ?? ''),
    'Subject: ' . ($first['subject'] ?? ''),
    'Teacher: ' . ($first['teacher'] ?? '')
]);

// Blank line
fputcsv($output, []);

// Column headers
$header = ['Student Name', 'Roll No'];
foreach ($dates as $d) {
    $header[] = date("d-m", strtotime($d));
}
$header[] = "Total Present";
$header[] = "Total Absent";
$header[] = "Total Leave";
$header[] = "Attendance %";
fputcsv($output, $header);

// Data rows
foreach ($pivot as $stud) {
    $row = [$stud['name'], $stud['roll_no']];
    $present = $absent = $leave = 0;

    foreach ($dates as $d) {
        $status = $stud['attendance'][$d] ?? '-';
        $row[] = $status;

        if ($status == "Present") $present++;
        elseif ($status == "Absent") $absent++;
        elseif ($status == "Leave") $leave++;
    }

    $total = $present + $absent + $leave;
    $percentage = $total > 0 ? round(($present / $total) * 100, 2) . "%" : "0%";

    $row[] = $present;
    $row[] = $absent;
    $row[] = $leave;
    $row[] = $percentage;

    fputcsv($output, $row);
}

fclose($output);
exit;
?>
