<?php 
$custom = get_post_custom($post->ID);
/*
foreach ($custom as $key => $value){
	echo '<p>'.$key.': '. $value[0] .'</p>';
}
*/
$intervals = array(
			'rush' => 'Rush',
			'5day' => '5 Day',
			'2week' => '2 Week'
		);

?>

<div class="document-custom-fields">
	<table class="form-table">
		<tr>
			<td scope="row"> Delivery Interval: </td>
			<td><?php echo $intervals[$custom['_interval'][0]]; ?></td> 
		</tr>
		<tr>
			<td scope="row"> Country: </td>
			<td><?php echo $custom['_country'][0]; ?></td> 
		</tr>
		<tr>
			<td scope="row"> Word Count: </td>
			<td><?php echo $custom['_word_count'][0]; ?></td> 
		</tr>
		<tr>
			<td scope="row">Selected Plan</td>
			<td><?php echo $custom['_plan'][0]; ?></td>
		</tr>
		
		<tr>
			<td scope="row">Paid Amount: </td>
			<td>$<?php echo $custom['_mc_gross'][0]; ?></td>
		</tr>
		<tr>
			<td scope="row">Payment Status</td>
			<td><?php  
				if($custom['_mc_gross'][0] == $custom['_price'][0]){
					echo 'Fully Paid';
				}
				elseif($custom['_mc_gross'][0] < $custom['_price'][0]){
					echo 'Partially Paid ( $'.$custom['_price'][0]-$custom['_mc_gross'][0].' to be paid)'; 
				}
				elseif ($custom['_mc_gross'][0] > $custom['_price'][0]){
					echo 'Partially Paid ( $'.$custom['_mc_gross'][0]-$custom['_price'][0].' to be paid)';
				}
			?></td>
		</tr>
		<tr>
			<td scope="row">Txn Id: </td>
			<td><?php echo $custom['_txn_id'][0]; ?></td>
		</tr>
		
		<?php 
			if(current_user_can('manage_options')){
				?>
				
				<tr>
					<td scope="row">Payer Paypal Id: </td>
					<td> <?php echo $custom['_payer_email'][0]; ?> </td>
				</tr>
				<tr>
					<td scope="row">Payer Name: </td>
					<td> <?php echo $custom['_first_name'][0] . ' ' . $custom['_last_name'][0]; ?> </td>
				</tr>
				
				<?php 
			}
		?>
		
	</table>
</div>