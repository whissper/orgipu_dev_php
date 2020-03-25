<input type="hidden" id="entity" />
<input type="hidden" id="idUpd" />
<div class="form-group">
	<label for="numDeviceUpd">№ прибора:</label>
	<input type="text" class="form-control sav2-field-upd" id="numDeviceUpd" placeholder="№ прибора">
</div>
<div class="form-group">
	<div class="checkbox" style="background-color: #eee; white-space: nowrap;">
		<label style="margin: 10px 10px;"><input type="checkbox" id="isBoilerUpd" > Бойлер</label>
		<label style="margin: 10px 10px;"><input type="checkbox" id="isHeatmeterUpd" > Теплосчетчик</label>
	</div>
</div>
<div class="form-group">
	<label for="HOidDeviceUpd">id теплоустановки:</label>
	<input type="text" class="form-control sav2-field-upd" id="HOidDeviceUpd" placeholder="" disabled>
</div>
<script>
	$('#isBoilerUpd').on('change', function() {
		if ($(this).prop('checked')) {
			$('#isHeatmeterUpd').prop('checked', false);
		}
	});
	
	$('#isHeatmeterUpd').on('change', function() {
		if ($(this).prop('checked')) {
			$('#isBoilerUpd').prop('checked', false);
		}
	});
</script>
