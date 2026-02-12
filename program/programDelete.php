<?php
require_once("data/db.php");
session_start();
session_regenerate_id();

$progid = $_GET['progid'] ?? null;
$stmt = $db->prepare("SELECT * FROM programs WHERE progid = :progid");
$stmt->execute(['progid' => $progid]);
$prog = $stmt->fetch();

if(!$prog) {
    header("Location: index.php?section=program&page=programList", true, 301);
    exit;
}

// fetch parent college
$collStmt = $db->prepare("SELECT * FROM colleges WHERE collid = :collid");
$collStmt->execute(['collid' => $prog['progcollid']]);
$coll = $collStmt->fetch();

// fetch parent department
$deptStmt = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
$deptStmt->execute(['deptid' => $prog['progcolldeptid']]);
$dept = $deptStmt->fetch();

// fetch students under program
?>
<h1>Program Delete</h1>
<span id="pageMessages">
    <?php echo $_SESSION['messages']['deleteSuccess'] ?? ''; ?>
    <?php echo $_SESSION['messages']['deleteError'] ?? ''; ?>
</span>

<p><strong>Warning:</strong> You are about to delete this program entry. This will also delete related students.</p>

<form action="index.php?section=program&page=processProgramData" method="post">
    <label for="progid">Program ID</label>
    <input type="text" id="progid" name="progid" value="<?php echo htmlspecialchars($prog['progid']); ?>" readonly>

    <label for="progfullname">Full Name</label>
    <input type="text" id="progfullname" name="progfullname" value="<?php echo htmlspecialchars($prog['progfullname']); ?>" readonly>

    <label for="progshortname">Short Name</label>
    <input type="text" id="progshortname" name="progshortname" value="<?php echo htmlspecialchars($prog['progshortname']); ?>" readonly>

    <input type="hidden" name="progcollid" value="<?php echo htmlspecialchars($prog['progcollid']); ?>">
    <input type="hidden" name="deptid" value="<?php echo htmlspecialchars($prog['progcolldeptid']); ?>">

    <a href="index.php?section=program&page=programListByDept&progcollid=<?php echo htmlspecialchars($prog['progcollid']); ?>&deptid=<?php echo htmlspecialchars($prog['progcolldeptid']); ?>" class="btn btn-primary">Cancel Operation</a>
    <button type="submit" name="confirmDeleteProgram" class="btn btn-danger">Confirm Operation</button>
</form>
