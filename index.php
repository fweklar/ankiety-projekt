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
$stmt = $pdo->query('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id GROUP BY p.id');
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Ankiety')?>

<div class="login content">
	<p></p>
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <?php  if (isset($_SESSION['username'])) : ?>
    	<p>Cześć <strong><?php echo $_SESSION['username']; ?></strong></p>
    	<p> <a href="index.php?logout='1'" style="color: red;">wyloguj się</a> </p>
    <?php endif ?>
</div>


<div class="content index">

	<h2>Ankiety</h2>
	<p>Witaj na naszej stronie umożliwiającej anonimowe głosowania. Tworzenie ankiet jak i głosowanie w nich jest szyfrowane. Użytkownik w każdej ankiecie może zagłosować tylko raz, natomiast usunąć ankietę może tylko osoba która ją założyła.</p>
	<a href="create.php" class="create-poll">Stwórz ankietę</a>
	<table>
        <thead>
            <tr>
                <td>#</td>
                <td>Tytuł</td>
				<td>Odpowiedzi</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
			<?php foreach ($polls as $poll): ?>
            <tr>
                <td><?=$poll['id']?></td>
                <td><?=$poll['title']?></td>
				<td><?=$poll['answers']?></td>
                <td class="actions">
					<a href="vote.php?id=<?=$poll['id']?>" class="view" title="Zobacz ankietę"><i class="fas fa-eye fa-xs"></i></a>
                    <a href="delete.php?id=<?=$poll['id']?>" class="trash" title="Usuń ankietę"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
			<?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>
