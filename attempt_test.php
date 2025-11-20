<?php
session_start();
include 'config.php';

$student_id = $_SESSION['user_id'];
$test_id = $_GET['test_id'];

$query = $conn->prepare("SELECT * FROM questions WHERE test_id = ?");
$query->bind_param("i", $test_id);
$query->execute();
$result = $query->get_result();
?>

<h2>📝 Attempt Test</h2>
<form action="submit_test.php" method="POST">
    <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
    <?php $qnum = 1; while ($row = $result->fetch_assoc()): ?>
        <div>
            <p><strong>Q<?php echo $qnum++; ?>: <?php echo $row['question_text']; ?></strong></p>
            <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                <label>
                    <input type="radio" name="answers[<?php echo $row['id']; ?>]" value="<?php echo strtoupper($opt); ?>" required>
                    <?php echo $row["option_$opt"]; ?>
                </label><br>
            <?php endforeach; ?>
        </div>
        <hr>
    <?php endwhile; ?>
    <button type="submit">Submit Test</button>
</form>
