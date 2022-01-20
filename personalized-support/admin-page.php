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

?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<form action="" method="POST">
			<div>Numéros des étudiants à rentrer, séparateur de champ : <b><?php echo field_separator() ?></b></div>
			<div>Exemple : A104B10<?php echo field_separator() ?>B108B14<?php echo field_separator() ?>C504E95</div>
			<input type="text" size="50" name="numeros">
			<button type="submit">Enregistrer les numéros</button>
		</form>

		<form action="<?php echo admin_url('admin-post.php'); ?>">
			<div>Télécharger en format CSV tous les curseurs</div>
			<input type="hidden" name="action" value="<?php echo Feedback::download_feedbacks; ?>">
			<button type="submit">Télécharger</button>
		</form>
	</div>
<?php
}
