<?php 
$custom = get_post_custom($post->ID);
foreach ($custom as $key => $value){
	echo '<p>'.$key.': '. $value[0] .'</p>';
}


?>

<style>
	div.file-uploader select, input#word_count{
		min-width: 250px;
	}
</style>

<div class="file-uploader">
	<table class="form-table">
		<tr>
			<th scope="row">Your country *</th>
			<td>
				<select document_revision="country" name="country">
					<option value="USA">USA</option>
					<option value="UK">UK</option>
				</select>
			</td>
		</tr>
		
		<tr> 
			<th scope="row">Your manuscript's file type *</th>
			<td>
				<?php 
					foreach(self::$file_types as $key => $type){
						?>
						<input <?php echo $key == 0 ? 'checked' : ''; ?> document_revision="file_type" id="<?php echo 'file_type_' . $key; ?>" type="radio" name="file_type" value="<?php echo $key +1; ?>" /> <label for="<?php echo 'file_type_' . $key; ?>"><?php echo $type; ?> </label> &nbsp; &nbsp;
						<?php 
					}
				?>
			</td> 
		</tr>
		
		<tr>
			<th>Show me *</th>
			<td>
				<input checked document_revision="show_me" type="radio" name="show_me" value="1" id="show_me_1" /> <label for="show_me_1"> Low-price plans first </label> &nbsp; &nbsp;
				<input document_revision="show_me" type="radio" name="show_me" value="2" id="show_me_2" /> <label for="show_me_2"> Fastest plans first </label>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label for="word_count"> Word count for your manuscript *</label></th>
			<td> <input document_revision="word_count" type="text" name="word_count" value="" id="word_count" /> <input id="document_revision_search" document_revision="search" type="button" name="search" value="search" class="button-primary button"> </td>
		</tr>
		
	</table>
	
	<!-- 
	<div class="Paypal-message" style="display:none"> Wait ... </div>
	<p id="paypal-payment"> <input type="button" value="Pay with Paypal" class="button button-primary" /></p>
	 -->
	
</div>