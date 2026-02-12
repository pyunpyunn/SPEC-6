<?php
    require_once("data/db.php");
    session_start();
    session_destroy();

    $deptcollid = $_GET['deptcollid'] ?? null;
    if (!$deptcollid) {
        
        header('Location: index.php?section=school&page=schoolList', true, 302);
        exit;
    }

    $dbStatement = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $dbStatement->execute(['collid' => $deptcollid]);
    $school = $dbStatement->fetch();

    $dbStatement = $db->prepare("SELECT * FROM departments WHERE deptcollid = :deptcollid");
    $dbStatement->execute(['deptcollid' => $deptcollid]);
    $departments = $dbStatement->fetchAll();
?>

<h1>Department List - <?php echo $school['collfullname']; ?></h1>
<div>
    <br>
    <h2><a href="index.php?section=department&page=departmentCreate&deptcollid=<?php echo htmlspecialchars($deptcollid); ?>" class="btn btn-primary">Create Department</a></h2>
    <br>
</div>
<table>
    <tr>
        <th>Department ID</th>
        <th>Department Full Name</th>
        <th>Department Short Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($departments as $department): ?>
    <tr>
        <td><?php echo $department['deptid']; ?></td>
        <td><?php echo $department['deptfullname']; ?></td>
        <td><?php echo $department['deptshortname']; ?></td>
        <td>
            <a href="index.php?section=department&page=departmentUpdate&deptid=<?php echo $department['deptid']; ?>" class="btn btn-info">Update</a>
            <a href="index.php?section=department&page=departmentDelete&deptid=<?php echo $department['deptid']; ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2">
            <span>
                Total of: <?= count($departments) ?> <?= (count($departments) === 1) ? 'department' : 'departments' ?> in the database
            </span>
        </td>
        <td colspan="2"></td>
    </tr>
</table>