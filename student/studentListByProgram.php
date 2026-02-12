<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$progid = $_GET['progid'] ?? null;
$deptid = $_GET['deptid'] ?? null;
$studcollid = $_GET['studcollid'] ?? null;

$school = null;
$department = null;
$program = null;
$students = [];

if ($studcollid){
    $schoolStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $schoolStmt->execute(['collid' => $studcollid]);
    $school = $schoolStmt->fetch();
}

if ($deptid){
    $deptStmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $deptStmt->execute(['deptid' => $deptid]);
    $department = $deptStmt->fetch();
}

if ($progid){
    $progStmt = $db->prepare("SELECT * FROM programs WHERE progid = :progid");
    $progStmt->execute(['progid' => $progid]);
    $program = $progStmt->fetch();
    
    $studStmt = $db->prepare("SELECT * FROM students WHERE studprogid = :progid");
    $studStmt->execute(['progid' => $progid]);
    $students = $studStmt->fetchAll();
}
?>

<h1>Students</h1>

<?php if ($school && $department && $program): ?>

    <br>
    <h2><?php echo $school['collfullname']; ?></h2>
    <h2><?php echo $department['deptfullname']; ?></h2>
    <h2><?php echo $program['progfullname']; ?></h2>
    
    <br>
    <a href="index.php?section=student&page=studentCreate&progid=<?php echo $progid; ?>&deptid=<?php echo $deptid; ?>&studcollid=<?php echo $studcollid; ?>" class="btn btn-primary">Create Student</a>
    <a href="index.php?section=student&page=studentList" class="btn btn-secondary">Back</a>
    <br><br>

    <table>
        <tr style="background-color: #4CAF50; color: white; height: 60px; text-align: center;">
            <th>Class No</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Year</th>
            <th>Actions</th>
        </tr>
        <?php $i = 1; foreach ($students as $s): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($s['studid']); ?></td>
            <td><?php echo htmlspecialchars($s['studfirstname']); ?></td>
            <td><?php echo htmlspecialchars($s['studmidname']); ?></td>
            <td><?php echo htmlspecialchars($s['studlastname']); ?></td>
            <td><?php echo htmlspecialchars($s['studyear']); ?></td>
            <td>
                <a href="index.php?section=student&page=studentUpdate&studid=<?php echo $s['studid']; ?>" class="btn btn-info">Update</a>
                <a href="index.php?section=student&page=studentDelete&studid=<?php echo $s['studid']; ?>" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="6">
                <span>
                    Total of: <?= count($students) ?> <?= (count($students) === 1) ? 'student' : 'students' ?> in the database
                </span>
            </td>
        </tr>
    </table>
    

<?php endif; ?>
