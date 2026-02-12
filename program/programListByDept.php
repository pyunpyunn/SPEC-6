<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$progcollid = $_GET['progcollid'] ?? null;
$deptid = $_GET['deptid'] ?? null;

$school = null;
$department = null;
$programs = [];


if ($progcollid){
    $schoolStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $schoolStmt->execute(['collid' => $progcollid]);
    $school = $schoolStmt->fetch();
}


if ($deptid){
    $deptDetailStmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $deptDetailStmt->execute(['deptid' => $deptid]);
    $department = $deptDetailStmt->fetch();
    
    
    $progStmt = $db->prepare("SELECT * FROM programs WHERE progcolldeptid = :deptid");
    $progStmt->execute(['deptid' => $deptid]);
    $programs = $progStmt->fetchAll();
}
?>

<?php if ($school && $department): ?>
    
        <h1>Programs - <?php echo $school['collfullname']; ?> | <?php echo $department['deptfullname']; ?></h1>
    
        <br>

        <a href="index.php?section=program&page=programCreate&deptid=<?php echo $deptid; ?>&progcollid=<?php echo $progcollid; ?>" class="btn btn-primary">Create Program</a>
        <a href="index.php?section=program&page=programList" class="btn btn-secondary">Back</a>

        <br><br>

    <table>
        <tr>
            <th>Program ID</th>
            <th>Program Full Name</th>
            <th>Program Short Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($programs as $p): ?>
        <tr>
            <td><?php echo $p['progid']; ?></td>
            <td><?php echo $p['progfullname']; ?></td>
            <td><?php echo $p['progshortname']; ?></td>
            <td>
                <a href="index.php?section=program&page=programUpdate&progid=<?php echo $p['progid']; ?>" class="btn btn-info">Update</a>
                <a href="index.php?section=program&page=programDelete&progid=<?php echo $p['progid']; ?>" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4">
                <span>
                    Total of: <?= count($programs) ?> <?= (count($programs) === 1) ? 'program' : 'programs' ?> in the database
                </span>
            </td>
        </tr>
    </table>

<?php endif; ?>
