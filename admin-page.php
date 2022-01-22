<?php

add_action('admin_menu', 'personalized_support_menu');
function personalized_support_menu()
{
	add_menu_page(Localisation::get('Plugin Personalized Support'), 'Personalized Support', 'manage_options', 'personalized-support', 'personalized_support_menu_output');
}

function field_separator()
{
	return ',';
}

function personalized_support_menu_output()
{
	if (isset($_POST['numeros']))
	{
		$numeros = explode(",", $_POST['numeros']);

		$html = "";

		$success = PSUsers::add_users($numeros);
		if (!$success)
		{
			global $wpdb;
			$html = '<div class="notice notice-error">
		        		<p>Erreur en rentrant les numéros : ' . $wpdb->last_error . '</p>
    				</div>';
		}
		else
		{
			$html = '<div class="notice notice-success is-dismissible">
		        		<p>Numéros ajoutés.</p>
    				</div>';
		}

		echo $html;
	}
	$all = PSUsers::get_all_users();

	
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://fdypress.edotnet.net/wp-content/plugins/personalised_support/css/admin-page.css" rel="stylesheet">
<!-- <script type="text/javascript">

(function() {
   // your page initialization code here
   // the DOM will be available here
   var maxRows = 1;

var table = document.getElementsByTagName('table');
var wrapper = table.parentNode;
var rowsInTable = table.length;
var height = 0;
if (rowsInTable > maxRows) {
	for (var i = 0; i < maxRows; i++) {
		height += table.rows[i].clientHeight;
	}
	wrapper.style.height = height + "px";
}
})();
    </script> -->
</head>
	<div class="wrap">
		<p id="head"><?php echo get_admin_page_title() ?></p>


<p id="title">Ajouter de nouveaux numeros</p>
<form action="" method="POST">
	<div class="sous">Numéros des étudiants à rentrer, séparateur de champ : 
	<br>Exemple : A104B10, B108B14, C504E95
	</div>
	<input type="text" size="50" name="numeros">
	<button type="submit" class="button-10" role="button">Enregistrer les numéros</button>
</form>

<p id="title">Télécharger le fichier excel</p>
		<form action="<?php echo admin_url('admin-post.php'); ?>">
			<div class="sous">Télécharger en format CSV tous les curseurs</div>
			<input type="hidden" name="action" value="<?php echo Feedback::download_feedbacks; ?>">
			<button type="submit" class="button-10" role="button">Télécharger</button>
		</form>

<p id="title">Les numeros existants</p>
<table class="table fixed_header" id="myTable">
  <thead>
    <tr id="header">
      <th scope="col">Id</th>
      <th scope="col">NumeroEtudiant</th>
    </tr>
  </thead>
  <tbody>
	<?php $i = 1;
	foreach($all as $a){?>
    <tr >
      <td scope="row"><?php echo $i ?></td>
      <td><?php echo $a; $i++;?></td>
    </tr>
	<?php
	}?>
</tbody>
</table>	

	</div>
</html>
<?php
}
?>