<?php 

	$rows = self::$plans;
	$cols = self::$intervals;
	
	if($_POST['plan-nd-price'] == 'Y'){
		update_option('document_revision_plan_price', array('plan' => $_POST['plan'], 'price' => $_POST['price']));
	}

	$plan_price = self::get_plans_prices();
	
?>


<div class="wrap">	
	<?php 
	if($_POST['plan-nd-price'] == 'Y'){
		echo '<div class="updated"><p>Saved</p></div>';
	}
	?>

	<form action="" method="post">
		<input type="hidden" name="plan-nd-price" value="Y" />
		
		<h3> Plans </h3>	
		<table class="form-table">
			<?php 
				foreach($rows as $key => $row){
					?>
						<tr>
							<th scope="row"> <label for="<?php echo $row[0] . '_' . $key; ?>"><?php echo $row[1]; ?></label> </th>
							<td> <textarea rows="2" cols="70" id="<?php echo $row[0] . '_' . $key; ?>" name="plan[<?php echo $row[0]; ?>]"><?php echo trim($plan_price['plan'][$row[0]]); ?></textarea> </td>
						</tr>
					<?php 
				}
			?>						
		</table>
		
		<h3>Price</h3>
		<p> Per word in USD ($) </p>
		<table class="form-table">
			<thead>
				<tr>
					<th> &nbsp; </th>
					<?php 
						foreach($cols as $c){
							echo '<th>' . $c[1] . '</th>';
						}
					?>
				</tr>
			</thead>
			<?php 
				foreach($rows as $rkey => $row){
					echo '<tr>';
					echo '<td>' . $row[1] . '</td>';
					foreach($cols as $ckey => $col){
						?>
						<td><input size="10" type="text" name="price[<?php echo $row[0] ?>][<?php echo $col[0] ?>]" value="<?php echo trim($plan_price['price'][$row[0]][$col[0]]) ; ?>" /></td>
						<?php 
					}
					echo '</tr>';
				}
			?>
		</table>
		
		<p> <input type="submit" value="Save" class="button button-primary" /> </p>		
	</form>
</div>