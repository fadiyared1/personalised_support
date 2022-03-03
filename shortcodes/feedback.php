<?php

class Feedback
{
	const item = "item";
	const value = "value";

	const ajax_send_feedback = 'ps_send_feedback';
	const ajax_nonce_name = 'ps_ajax_nonce';

	const download_feedbacks = 'ps_download_feedbacks';

	static $table_name = "";

	static function init()
	{
		global $wpdb;
		Feedback::$table_name = $wpdb->prefix . 'ps_feedbacks';

		Feedback::maybe_create_table();
	}

	static function maybe_create_table()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$table_name = Feedback::$table_name;

		$activite = Metadata::activite;
		$cours = Metadata::cours;
		$item = Feedback::item;
		$value = Feedback::value;

		$create_feedbacks_table_sql =
			"CREATE TABLE {$table_name}
			(
				id varchar(256) NOT NULL primary key,
				user_numero varchar(16) NOT NULL,
				{$activite} varchar(16) NOT NULL,
				{$cours} varchar(16) NOT NULL,
				{$item} int(16) NOT NULL,
				{$value} int(16) NOT NULL,
				INDEX user_index(user_numero)
			)";

		maybe_create_table($table_name, $create_feedbacks_table_sql);
	}

	static function set_feedback($user_numero, $activite, $cours, $item, $value)
	{
		global $wpdb;

		$table_name = Feedback::$table_name;

		$sql =
			"INSERT INTO {$table_name}
			(id, user_numero, activite, cours, item, value)
			VALUES
			(\"{$user_numero}-{$activite}-{$cours}-{$item}\",
			\"{$user_numero}\",\"{$activite}\",\"{$cours}\",{$item},{$value})
			ON DUPLICATE KEY UPDATE value={$value}";

		return $wpdb->query($sql);
	}
	static function get_feedbacks($user_numero)
	{
		global $wpdb;

		$table_name = Feedback::$table_name;

		$sql =
			"SELECT FROM {$table_name}
			WHERE (user_numero = '{$user_numero}')";

		return $wpdb->query($sql);
	}
	static function enqueue_ajax()
	{
		wp_add_inline_script(
			'personalized_support_script',
			'const personalizedSupport = ' . json_encode(array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'sendFeedback' => Feedback::ajax_send_feedback,
				'nonce' => wp_create_nonce(Feedback::ajax_nonce_name)
			)),
			'before'
		);
	}

	static function is_valid($atts)
	{
		return !empty($atts[Feedback::item]);
	}
}

add_action('admin_post_' . Feedback::download_feedbacks, 'export_feedbacks_to_csv');
function export_feedbacks_to_csv()
{
	$table_name = Feedback::$table_name;

	global $wpdb;
	$results = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);

	if ($wpdb->num_rows > 0)
	{
		$filename = "feedbacks_" . date('d-m-Y') . ".csv";

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=\"" . $filename . "\"");

		$f = fopen('php://output', 'w');

		$columns_heading = array('Numero', 'Cours', 'Activite', 'Item', 'Valeur');
		fputcsv($f, $columns_heading);

		foreach ($results as $row)
		{
			$line = array($row['user_numero'], $row[Metadata::cours], $row[Metadata::activite], $row[Feedback::item], $row[Feedback::value]);
			fputcsv($f, $line);
		}
	}
}
add_shortcode('feedback', 'cp_var' );  
function cp_var($s, $c) {
  if($s==$c){
	  return "checked";
  }
  else{
	  return '';
  }
}

add_shortcode('feedback', 'feedback_shortcode');
function feedback_shortcode($atts, $content)
{
	if (PSUser::has_valid_numero())
	{
		if (Feedback::is_valid($atts))
		{
			$metadata_attributes = Metadata::get_current_attributes();
			if (Metadata::is_valid($metadata_attributes))
			{
				$chq = "1";
				$num = PSUser::get_numero();
				$activite = $metadata_attributes[Metadata::activite];
				$cours = $metadata_attributes[Metadata::cours];				
				$item = $atts[Feedback::item];
				global $wpdb;
				$table_name = $wpdb->prefix . "ps_feedbacks";
				$registered = $wpdb->get_var( "SELECT value FROM $table_name WHERE user_numero = '{$num}'  AND activite = '{$activite}' AND cours = '{$cours}' AND item = '{$item}'" );
				
				if (!isset($atts['title']))
				{
					$title = Localisation::get('Feedback');
				}
				else
				{
					$title = $atts['title'];
				}

				$data_prefix = 'data-';
				$radio_group = microtime();

				$content =
					'<div class="feedback-container" ' .
					$data_prefix . Metadata::activite . "=\"{$activite}\" " .
					$data_prefix . Metadata::cours . "=\"{$cours}\" " .
					$data_prefix . Feedback::item . "=\"{$item}\"" .
					'>
						<span class="feedback-span">
							<label>
							<input type="radio" name="' . $radio_group . '" class="feedback-button" '.$blc= cp_var($registered,1).'> 
							1
							</label>
						</span>
						<span class="feedback-span">
							<label>
							<input type="radio" name="' . $radio_group . '" class="feedback-button" '.$blc= cp_var($registered,2).'>
							2
							</label>
						</span>
						<span class="feedback-span">
							<label>
							<input type="radio" name="' . $radio_group . '" class="feedback-button" '.$blc= cp_var($registered,3).'>
							3
							</label>
						</span>
						<span class="feedback-span">
							<label>
							<input type="radio" name="' . $radio_group . '" class="feedback-button" '.$blc= cp_var($registered,4).'>
							4
							</label>
						</span>
					</div>
					<div>
						<span class="more">Cliquer pour plus de détails</span>

						<span class="complete">
							<div>1 : Pas bien assimilé</div>
							<div>2 : Réussite ou compréhension fragile</div>
							<div>3 : Bien compris mais révision à prévoir</div>
							<div>4 : Très bien compris</div>
						</span>
					</div>';

				return HtmlGen::fieldset($title, $content);
			}
		}
		else
		{
			return '<div class="notice notice-error">
						<p>Balise feedback incorrecte, l\'attribut item doit avoir une valeur non null. 
						Exemple d\' une balise correcte : [feedback item="1"][\\feedback]</p>
            		</div>';
		}
	}

	return '';
}

add_action('wp_ajax_nopriv_' . Feedback::ajax_send_feedback, 'handle_feedback_from_user');
add_action('wp_ajax_' . Feedback::ajax_send_feedback, 'handle_feedback_from_user');
function handle_feedback_from_user()
{
	if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], Feedback::ajax_nonce_name))
	{
		die('Nonce value cannot be verified.');
	}

	if (!PSUser::has_valid_numero())
	{
		die('User not logged.');
	}

	if (isset($_REQUEST) && isset($_REQUEST['dataset']))
	{
		$dataset = $_REQUEST['dataset'];

		if (
			empty($dataset[Metadata::activite]) ||
			empty($dataset[Metadata::cours]) ||
			empty($dataset[Feedback::item]) ||
			empty($dataset[Feedback::value])
		)
		{
			die('Missing metadata.');
		}

		$user_numero = PSUser::get_numero();
		$activite = $dataset[Metadata::activite];
		$cours = $dataset[Metadata::cours];
		$item = $dataset[Feedback::item];
		$value = $dataset[Feedback::value];

		Feedback::set_feedback($user_numero, $activite, $cours, $item, $value);

		// Cela est retourné au client qui a fait la requête AJAX
		echo "Success adding feedback : \"{$user_numero}\",\"{$activite}\",\"{$cours}\",{$item},{$value}";
	}

	// Always die in functions echoing ajax content
	die();
}
