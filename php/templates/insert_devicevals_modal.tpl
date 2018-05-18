<input type="hidden" id="entity" value="devicevals" />
<div class="form-group">
	<label for="deviceIDIns">id прибора:</label>
	<input type="text" class="form-control sav2-field-ins" id="deviceIDIns" placeholder="" disabled>
</div>
<div class="form-group">
	<label for="deviceNumIns">№ прибора:</label>
	<input type="text" class="form-control sav2-field-ins" id="deviceNumIns" placeholder="" disabled>
</div>
<div class="form-group">
	<label for="meteringvalsIns">Показания прибора:</label>
	<input type="text" class="form-control" id="meteringvalsIns" placeholder="Показания прибора">
</div>
<div class="form-group">
	<label for="calcperiodIns">Рассчетный период:</label>
	<input type="text" class="form-control" id="calcperiodIns" placeholder="Рассчетный период">
</div>
<div class="form-group">
	<div class="checkbox" style="background-color: #eee; white-space: nowrap;">
		<label style="margin: 10px 10px;"><input type="checkbox" id="isNormativeIns" > Показания по нормативу</label>
	</div>
</div>
<script>
$('#meteringvalsIns').inputmask({mask: "9{0,6}[.9{0,3}]", greedy: false});
$('#calcperiodIns').datepicker({
	format: "mm.yyyy",
	viewMode: "years", 
	minViewMode: "months",
	language: 'ru'
});
</script>
