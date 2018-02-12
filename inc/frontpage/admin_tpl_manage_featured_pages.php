<div class="import-jde-products-form-container">
<?php
if ( isset($resp) && !empty($resp) ) {?>
	<div><p style="background-color: green; color: white;padding: 1em;">Settings Successfully Saved</p></div>
<?php }?>
<h2>ANC Desktop Frontpage Featured-Pages</h2>
<form name="anc-manage-frontpage" action="<?php echo admin_url('admin.php?page=anc-manage-frontpage') ?>" method="post">
	<table>
	<tbody>
		<tr>
			<th>#</th>
			<th>Title</th>
			<th>Page URL</th>
			<th>Image URL</th>
			<th>Caption</th>
		</tr>
		<?php for($i = 0; $i<6; $i++){?>
		<tr>
			<td><?=$i+1?></td>
			<td><input type="text" name="opts[<?=$i?>][caption]" value="<?=empty($opts[$i])?'':$opts[$i]['caption']?>" /></td>
			<td><input type="text" name="opts[<?=$i?>][target]" value="<?=empty($opts[$i])?'':$opts[$i]['target']?>"/></td>
			<td><input type="text" name="opts[<?=$i?>][image]" value="<?=empty($opts[$i])?'':$opts[$i]['image']?>"/></td>
			<td><input type="text" name="opts[<?=$i?>][excerpt]" value="<?=empty($opts[$i])?'':$opts[$i]['excerpt']?>"/></td>
		</tr>
		<?php }?>
	</tbody>
	</table>
<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save'); ?>" /></p>

</form>
</div>