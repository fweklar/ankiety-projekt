/**
*Copyright (c) 2020 Filip Węklar, Konrad Gorczyca
*/

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
$msg = '';
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($poll) {
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
        $stmt->execute([$_GET['id']]);
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$user_session = md5($_SESSION['username']);
		$stmt = $pdo->prepare('SELECT id FROM user_answers WHERE username = :user_session and poll_id = :getid ');
		$stmt->execute([':user_session' => $user_session ,':getid' => $_GET['id']]);
		$user_answer = $stmt->fetchColumn();

        if (isset($_POST['poll_answer'])) {
            if ($user_answer != NULL) {
                $msg = 'Już oddałeś głos w tej ankiecie!';
            } else {
				$stmt = $pdo->prepare('UPDATE poll_answers SET votes = votes + 1 WHERE id = ?');
				$stmt->execute([$_POST['poll_answer']]);
                $stmt = $pdo->prepare('INSERT INTO user_answers VALUES (NULL, ?, ?)');
				$stmt->execute([md5($_SESSION['username']), $_GET['id']]);
				
				header ('Location: result.php?id=' . $_GET['id']);
				exit;
			}
        }
    } else {
        die ('Ankieta o takim ID nie istnieje.');
    }
} else {
    die ('Nie określono ID ankiety.');
}
?>

<?=template_header('Głosowanie')?>

<div class="content vote">
	<h2><?=$poll['title']?></h2>
	<p><?=$poll['desc']?></p>
    <form action="vote.php?id=<?=$_GET['id']?>" method="post">
		<?php for ($i = 0; $i < count($poll_answers); $i++): ?>
        <label>
            <input type="radio" name="poll_answer" value="<?=$poll_answers[$i]['id']?>"<?=$i == 0 ? ' checked' : ''?>>
            <?=$poll_answers[$i]['title']?>
        </label>
		<?php endfor; ?>
        <div>
            <input type="submit" value="Zagłosuj">
            <a href="result.php?id=<?=$poll['id']?>">Sprawdź wyniki</a>
        </div>
    </form>
	<?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>
