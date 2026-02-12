<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$deptid = $_GET['deptid'] ?? null;
$stmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
$stmt->execute(['deptid' => $deptid]);
$dept = $stmt->fetch();

if(!$dept) {
    header("Location: index.php?section=department&page=departmentList", true, 301);
    exit;
}

// fetch parent college
$collStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
$collStmt->execute(['collid' => $dept['deptcollid']]);
$coll = $collStmt->fetch();

// fetch child programs for info
$progStmt = $db->prepare("SELECT progid, progfullname FROM programs WHERE progcolldeptid = :deptid");
$progStmt->execute(['deptid' => $deptid]);
$progs = $progStmt->fetchAll();
?>
<h1>Department Delete</h1>
<p><strong>Warning:</strong> You are about to delete this department entry. This will also delete all programs and students under this department.</p>

<h3>College</h3>
<table>
    <tr><th>College ID</th><th>College Name</th></tr>
    <tr>
        <td><?php echo htmlspecialchars($coll['collid']); ?></td>
        <td><?php echo htmlspecialchars($coll['collfullname']); ?></td>
    </tr>
</table>

<h3>Department to Delete</h3>
<table>
    <tr><th>Department ID</th><th>Full Name</th><th>Short Name</th></tr>
    <tr>
        <td><?php echo htmlspecialchars($dept['deptid']); ?></td>
        <td><?php echo htmlspecialchars($dept['deptfullname']); ?></td>
        <td><?php echo htmlspecialchars($dept['deptshortname']); ?></td>
    </tr>
</table>
<?php if(count($progs)): ?>
    <h3>Programs That Will Be Deleted</h3>
    <table>
        <tr><th>Program ID</th><th>Program Name</th></tr>
        <?php foreach($progs as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['progid']); ?></td>
                <td><?php echo htmlspecialchars($p['progfullname']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p><em>No programs under this department.</em></p>
<?php endif; ?>

<form action="index.php?section=department&page=processDepartmentData" method="post">
    <input type="hidden" name="deptid" value="<?php echo $dept['deptid']; ?>">
    <a href="index.php?section=department&page=departmentList&deptcollid=<?php echo $dept['deptcollid']; ?>" class="btn btn-primary">Cancel Operation</a>
    <button type="submit" name="confirmDeleteDepartment" class="btn btn-danger">Confirm Operation</button>
</form>
