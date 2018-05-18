<input type="hidden" id="entity" />
<input type="hidden" id="idUpd" />
<div class="form-group">
	<label for="meteringvalsDevicevalsUpd">Показания прибора:</label>
	<input type="text" class="form-control sav2-field-upd" id="meteringvalsDevicevalsUpd" placeholder="Показания прибора">
</div>
<div class="form-group">
	<label for="calcperiodDevicevalsUpd">Рассчетный период:</label>
	<input type="text" class="form-control sav2-field-upd" id="calcperiodDevicevalsUpd" placeholder="Рассчетный период">
</div>
<div class="form-group">
	<div class="checkbox" style="background-color: #eee; white-space: nowrap;">
		<label style="margin: 10px 10px;"><input type="checkbox" id="isNormativeUpd" > Показания по нормативу</label>
	</div>
</div>
<div class="form-group">
	<label for="deviceidDevicevalsUpd">id прибора:</label>
	<input type="text" class="form-control sav2-field-upd" id="deviceidDevicevalsUpd" placeholder="" disabled>
</div>
<div class="form-group">
	<label for="devicenumDevicevalsUpd">№ прибора:</label>
	<input type="text" class="form-control sav2-field-upd" id="devicenumDevicevalsUpd" placeholder="" disabled>
</div>
<script>
$('#meteringvalsDevicevalsUpd').inputmask({mask: "9{0,6}[.9{0,3}]", greedy: false});
$('#calcperiodDevicevalsUpd').datepicker({
	format: "mm.yyyy",
	viewMode: "years", 
	minViewMode: "months",
	language: 'ru'
});
</script>
