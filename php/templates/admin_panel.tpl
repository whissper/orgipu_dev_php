<div class="sav2-admin-wa">
<div class="row">
	<div class="col-md-12">
		<div class="btn-group" role="group">
			  <button type="button" class="btn btn-default active" id="showTab1">Ввод данных</button>
			  <button type="button" class="btn btn-default" id="showTab2">Договоры</button>
			  <button type="button" class="btn btn-default" id="showTab3">Теплоустановки</button>
			  <button type="button" class="btn btn-default" id="showTab4">Приборы учета</button>
			  <button type="button" class="btn btn-default" id="showTab5">Показания ПУ</button>
			  <button type="button" class="btn btn-default" id="showTab6">Расчет расходов</button>
			  <button type="button" class="btn btn-primary" id="logout">Выйти</button>
		</div>
	</div>
</div>

<br />

<div class="row">
	<div class="col-md-12" id="sav2-infobox-info">
				
	</div>
</div>
<!-- TAB 1 -->
<div class="row sav2-tabs sav2-tab1">
	<div class="col-md-12">
		<!-- -->
		<div class="col-md-3">
			<div class="form-group">
				<input type="text" class="form-control" value="Договор:" disabled>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label for="contract_num">Номер договора:</label>
				<input type="text" class="form-control" id="contract_num" placeholder="Номер договора">
			</div>
		</div>
		<div class="col-md-12">
			<hr />
		</div>
		<!-- -->
		<!-- HEATED-OBJECT HEADER start: -->
		<div class="col-md-3">
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" value="Тепловые установки:" disabled>
					<span class="input-group-btn">
						<button class="btn btn-default add-heated-object" title="Добавить">
							<span class="glyphicon glyphicon-plus"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<!-- HEATED-OBJECT HEADER end; -->
		<!-- HEATED-OBJECTS start: -->
		<div class="sav2-heated-object-list">
			<!-- HEATED-OBJECT start: -->
			
			<!-- HEATED-OBJECT end; -->		
		</div>
		<!-- HEATED-OBJECTS end; -->
	</div>
	<div class="col-md-12">
		<div class="col-md-12">
			<hr />
		</div>
		<div class="col-md-12">
			<button class="btn btn-lg btn-primary" data-toggle="modal" data-target="#addRecord" id="recordnew">Добавить</button>
		</div>
	</div>
	
	<!-- Modal -->
	<div id="addRecord" class="modal fade" role="dialog">
	  <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Добавление новой записи в базу</h4>
		  </div>
		  <div class="modal-body">
			<p>Данные новой записи:</p>
			<hr />
			<div id="recordData">
					
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal" id="addRecordYes">Добавить</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" id="addRecordNo">Отмена</button>
		  </div>
		</div>

	  </div>
	</div>
</div>
<!-- TAB 2 -->
<div class="row sav2-tabs sav2-tab2">	
	<div class="col-md-12">
		<p>Поиск договоров:</p>
		<div class="row">
			<div class="col-md-4 col-srch">
				<div class="input-group">
					<span class="input-group-addon">id договора: </span>
					<input type="text" class="form-control sav2-srch-contract" id="srch-contract-id">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-contract" title="Очистить" id="clear-srch-contract-id">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-4 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-contract" id="srch-contract-num" 
					placeholder="№ договора" data-toggle="tooltip" data-placement="top" title="№ договора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-contract" title="Очистить" id="clear-srch-contract-num">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<hr />
		<p>Редактирование договоров:</p>
		<div class="sav2-edit-contract-table">
		<!-- DYNAMIC start: -->
		
		<!-- DYNAMIC end; -->
		</div>	
	</div>
</div>
<!-- TAB 3 -->
<div class="row sav2-tabs sav2-tab3">
	<div class="col-md-12">
		<p>Поиск тепловых установок:</p>
		<div class="row">
			<div class="col-md-4 col-srch">
				<div class="input-group">
					<span class="input-group-addon">id теплоустановки: </span>
					<input type="text" class="form-control sav2-srch-heated-object" id="srch-heated-object-id">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-heated-object" title="Очистить" id="clear-srch-heated-object-id">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-4 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-heated-object" id="srch-heated-object-name" 
					placeholder="Наименование" data-toggle="tooltip" data-placement="top" title="Наименование">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-heated-object" title="Очистить" id="clear-srch-heated-object-name">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-4 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-heated-object" id="srch-heated-object-contractnum" 
					placeholder="№ договора" data-toggle="tooltip" data-placement="top" title="№ договора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-heated-object" title="Очистить" id="clear-srch-heated-object-contractnum">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<hr />
		<p>Редактирование тепловых установок:</p>
		<div class="sav2-edit-heated-object-table">
		<!-- DYNAMIC start: -->
		
		<!-- DYNAMIC end; -->
		</div>	
	</div>
</div>
<!-- TAB 4 -->
<div class="row sav2-tabs sav2-tab4">
	<div class="col-md-12">
		<p>Поиск приборов учета:</p>
		<div class="row">
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<span class="input-group-addon">id прибора: </span>
					<input type="text" class="form-control sav2-srch-device" id="srch-device-id">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-device" title="Очистить" id="clear-srch-device-id">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-device" id="srch-device-num" 
					placeholder="№ прибора" data-toggle="tooltip" data-placement="top" title="№ прибора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-device" title="Очистить" id="clear-srch-device-num">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-device" id="srch-device-nameHO" 
					placeholder="наименование ТУ" data-toggle="tooltip" data-placement="top" title="наименование теплоустановки">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-device" title="Очистить" id="clear-srch-device-nameHO">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-device" id="srch-device-idHO" 
					placeholder="id ТУ" data-toggle="tooltip" data-placement="top" title="id теплоустановки">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-device" title="Очистить" id="clear-srch-device-idHO">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-device" id="srch-device-contractnum" 
					placeholder="№ договора" data-toggle="tooltip" data-placement="top" title="№ договора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-device" title="Очистить" id="clear-srch-device-contractnum">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="checkbox" style="background-color: #eee; white-space: nowrap; padding: 7px; margin: 0;" data-toggle="tooltip" data-placement="top" title="фильтр по типу ПУ">
					<label style="margin: 0px 10px;"><input type="checkbox" class="sav2-srch-device-by-type" id="srch-device-isBoiler" > Бойлер</label>
					<label><input type="checkbox" class="sav2-srch-device-by-type" id="srch-device-isHeatmeter" > Теплосчетчик</label>
				</div>				
			</div>
		</div>
		<hr />
		<p>Редактирование приборов учета:</p>
		<div class="sav2-edit-device-table">
		<!-- DYNAMIC start: -->
		
		<!-- DYNAMIC end; -->
		</div>	
	</div>
</div>
<!-- TAB 5 -->
<div class="row sav2-tabs sav2-tab5">
	<div class="col-md-12">
		<p>Поиск показаний ПУ:</p>
		<div class="row">
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<span class="input-group-addon">id показания: </span>
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-id">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-id">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-deviceid" 
					placeholder="id прибора" data-toggle="tooltip" data-placement="top" title="id прибора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-deviceid">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-devicenum" 
					placeholder="№ прибора" data-toggle="tooltip" data-placement="top" title="№ прибора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-devicenum">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-month" 
					placeholder="месяц" data-toggle="tooltip" data-placement="top" title="месяц">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-month">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-year" 
					placeholder="год" data-toggle="tooltip" data-placement="top" title="год">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-year">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-nameHO" 
					placeholder="наименование ТУ" data-toggle="tooltip" data-placement="top" title="наименование теплоустановки">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-nameHO">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-devicevals" id="srch-devicevals-contractnum" 
					placeholder="№ договора" data-toggle="tooltip" data-placement="top" title="№ договора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-devicevals" title="Очистить" id="clear-srch-devicevals-contractnum">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<hr />
		<p>Редактирование показаний ПУ:</p>
		<div class="sav2-edit-devicevals-table">
		<!-- DYNAMIC start: -->
		
		<!-- DYNAMIC end; -->
		</div>	
	</div>
</div>
<!-- TAB 6 -->
<div class="row sav2-tabs sav2-tab6">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<span class="input-group-addon">Период: </span>
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-date">
				</div>
			</div>
			<div class="col-md-3 col-srch" id="tab6-checkbox">
				<div class="checkbox">
					<label><input type="checkbox" id="hideNormativeVals" > скрыть показания по нормативу</label>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<button id="writeDataIntoXLSX" title="Выгрузить в Excel" type="button" class="btn btn-success">Выгрузить в Excel&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-th"></span></button>
			</div>
		</div>
		<hr />
		<p>Поиск по ключевым полям:</p>
		<div class="row">
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-id"
					placeholder="id прибора" data-toggle="tooltip" data-placement="top" title="id прибора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-deviceconsume" title="Очистить" id="clear-srch-deviceconsume-id">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-num" 
					placeholder="№ прибора" data-toggle="tooltip" data-placement="top" title="№ прибора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-deviceconsume" title="Очистить" id="clear-srch-deviceconsume-num">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-nameHO" 
					placeholder="наименование ТУ" data-toggle="tooltip" data-placement="top" title="наименование теплоустановки">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-deviceconsume" title="Очистить" id="clear-srch-deviceconsume-nameHO">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-idHO" 
					placeholder="id ТУ" data-toggle="tooltip" data-placement="top" title="id теплоустановки">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-deviceconsume" title="Очистить" id="clear-srch-deviceconsume-idHO">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-srch">
				<div class="input-group">
					<input type="text" class="form-control sav2-srch-deviceconsume" id="srch-deviceconsume-contractnum" 
					placeholder="№ договора" data-toggle="tooltip" data-placement="top" title="№ договора">
					<span class="input-group-btn">
						<button class="btn btn-default clear-srch-deviceconsume" title="Очистить" id="clear-srch-deviceconsume-contractnum">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<hr />
		<p>Показания расходов:</p>
		<div class="sav2-edit-deviceconsume-table">
		<!-- DYNAMIC start: -->
		
		<!-- DYNAMIC end; -->
		</div>	
	</div>
</div>
<!-- TAB 7 -->
<div class="row sav2-tabs sav2-tab7">

</div>

<!-- Modal -->
<div id="updateElement" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title"></h4>
	  </div>
	  <div class="modal-body">
		<p></p>
		<div id="elementDataUpd">
				
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal" id="updElementYes">Сохранить</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" id="updElementNo">Отмена</button>
	  </div>
	</div>

  </div>
</div>

<!-- Modal -->
<div id="insertElement" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title"></h4>
	  </div>
	  <div class="modal-body">
		<p></p>
		<div id="elementDataIns">
				
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal" id="insElementYes">Добавить</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" id="insElementNo">Отмена</button>
	  </div>
	</div>

  </div>
</div>

</div>
<br />
<br />
<script>
	$('#srch-contract-id').inputmask({mask: "9{0,}", greedy: false});
	$('#srch-heated-object-id').inputmask({mask: "9{0,}", greedy: false});
	$('#srch-devicevals-id').inputmask({mask: "9{0,}", greedy: false});
	$('#srch-devicevals-deviceid').inputmask({mask: "9{0,}", greedy: false});
	$('#srch-devicevals-month').inputmask({mask: "9{0,}", greedy: false});
	$('#srch-devicevals-year').inputmask({mask: "9{0,}", greedy: false});
	
	$('#srch-deviceconsume-date').datepicker({
		format: "mm.yyyy",
		viewMode: "years", 
		minViewMode: "months",
		language: 'ru'
	});
	
	$('[data-toggle="tooltip"]').tooltip();
	
	$('#srch-device-isBoiler').on('change', function() {
		if ($(this).prop('checked')) {
			$('#srch-device-isHeatmeter').prop('checked', false);
		}
	});
	
	$('#srch-device-isHeatmeter').on('change', function() {
		if ($(this).prop('checked')) {
			$('#srch-device-isBoiler').prop('checked', false);
		}
	});
</script>