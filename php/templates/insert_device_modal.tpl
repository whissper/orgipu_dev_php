<input type="hidden" id="entity" value="device" />
<div class="form-group">
	<label for="heatedobjIDIns">id теплоустановки:</label>
	<input type="text" class="form-control sav2-field-ins" id="heatedobjIDIns" placeholder="" disabled>
</div>
<div class="form-group">
	<label for="heatedobjNameIns">Наименование теплоустановки:</label>
	<input type="text" class="form-control sav2-field-ins" id="heatedobjNameIns" placeholder="" disabled>
</div>
<div class="form-group">
	<label for="deviceNumIns">№ прибора учета:</label>
	<input type="text" class="form-control sav2-field-ins" id="deviceNumIns" placeholder="№ прибора">
</div>
<div class="form-group">
	<div class="checkbox" style="background-color: #eee; white-space: nowrap;">
		<label style="margin: 10px 10px;"><input type="checkbox" id="isBoilerIns" > Бойлер</label>
		<label style="margin: 10px 10px;"><input type="checkbox" id="isHeatmeterIns" > Теплосчетчик</label>
	</div>
</div>
<script>
	$('#isBoilerIns').on('change', function() {
		if ($(this).prop('checked')) {
			$('#isHeatmeterIns').prop('checked', false);
		}
	});
	
	$('#isHeatmeterIns').on('change', function() {
		if ($(this).prop('checked')) {
			$('#isBoilerIns').prop('checked', false);
		}
	});
</script>
