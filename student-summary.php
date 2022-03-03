<?php
require_once ('../../../wp-config.php');
//start_session_wp();
?>
<!DOCTYPE html>
<html>
<head>
<link href="<?php echo plugins_url('personalised_support/css/student-page.css')?>" rel="stylesheet">
</head>
<body>
  <?php
  function checkcolor( $data ){
  if($data==1 || $data==2){
    return  "red";
  }
  else{
    return "green";
  }
  }
  function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }
  
$url = $_SERVER[REQUEST_URI];
$url = explode("/", $url);
$id = $url[count($url) - 1];

//$registered = Feedback::get_feedbacks($id);

global $wpdb;
$table_name = $wpdb->prefix . "ps_feedbacks";
$registered = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_numero = '{$id}'" );
//wp_die($registered);


?> 
  <div class="before">Synthèse pour l'étudiant: <?php echo $id;?></div>
  <table class="table" id="myTable">
			<thead>
				<tr id="header">
				<th scope="col">Activite</th>
				<th scope="col">Cours</th>
        <th scope="col">Item</th>
        <th scope="col">Value</th>
        <th scope="col">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1;
				foreach($registered as $reg)
				{?>

        <tr>
				<td><?php echo $reg->activite ?></td>			 
				<td><?php echo $reg->cours ?></td>
        <td><?php echo $reg->item ?></td>
        <td><?php echo $reg->value ?></td>
        <td><label class="<?php echo checkcolor($reg->value);?>" ><?php if(strcmp(checkcolor($reg->value),"red")) {echo "Maitrisé";}  else { echo "Non Maitrisé";}?></label></td>
        
				</tr>
				<?php
				}?>
			</tbody>
		</table>	
</body>