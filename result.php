<?php

  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "Musisz się zalogować!";
  	header('location: /phpoll/registration/login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: /phpoll/registration/login.php");
  }

include 'functions.php';
$pdo = pdo_connect_mysql();
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($poll) {
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY votes DESC');
        $stmt->execute([$_GET['id']]);
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_votes = 0;
        foreach ($poll_answers as $poll_answer) {
            $total_votes += $poll_answer['votes'];
        }
    } else {
        die ('Ankieta o tym ID nie istnieje.');
    }
} else {
    die ('Brak określonego ID ankiety.');
}
?>

<?=template_header('Wyniki ankiety')?>

<div class="content result">
	<h2><?=$poll['title']?></h2>
	<p><?=$poll['desc']?></p>
	<p></p>
        <div class="wrapper">
        <?php foreach ($poll_answers as $poll_answer): ?>
        <div class="question">
            <p><?=$poll_answer['title']?> <span>(<?=$poll_answer['votes']?> Głosów)</span></p>
            <div class="result-bar" style="width:<?=@(($poll_answer['votes']/$total_votes)*100)?>%">
                <?=@round(($poll_answer['votes']/$total_votes)*100)?>%
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>
