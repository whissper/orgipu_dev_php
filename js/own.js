/**
 -- ORG-IPU --
 --- front-end ---
 @author: SAV2
 @version 0.6.1
 @since: 25.03.2020
 **/

var currentPageContract = 0;
var currentPageHeatedObject = 0;
var currentPageDevice = 0;
var currentPageDeviceVals = 0;
var currentPageDeviceConsume = 0;

//lock panel 
function lockPanel()
{
    $('body').css({'overflow': 'hidden'});
    $('#light_cover').fadeIn(200);
}

//unlock panel
function unlockPanel()
{
    $('body').css({'overflow': 'auto'});
    $('#light_cover').fadeOut(200);
}

//reset new record form
function resetNewRecordForm()
{
    $('#contract_num').val('');
    $('.sav2-heated-object-item').remove();
}

/** SHOW INFO BOX **/
function showInfoBox(messageVal, messageType) {
    $('#sav2-infobox-info').empty();
    var infoBoxHTML = '';
    switch (messageType) {
        case 'INFOBOX_SUCCESS':
            infoBoxHTML += '<div class="alert alert-success fade in alert-dismissable">' +
                                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            infoBoxHTML += '<span class="glyphicon glyphicon-ok-circle"></span> ' + messageVal;
            infoBoxHTML += '</div>';
            break;
        case 'INFOBOX_ERROR':
            infoBoxHTML += '<div class="alert alert-danger fade in alert-dismissable">' +
                                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            infoBoxHTML += '<span class="glyphicon glyphicon-remove-circle"></span> ' + messageVal;
            infoBoxHTML += '</div>';
            break;
        case 'INFOBOX_INFO':
            infoBoxHTML += '<div class="alert alert-info fade in alert-dismissable">' +
                                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            infoBoxHTML += '<span class="glyphicon glyphicon-info-sign"></span> ' + messageVal;
            infoBoxHTML += '</div>';
            break;
    }
    $('#sav2-infobox-info').html(infoBoxHTML);
}

/** CHOOSE PANEL **/
function choosePanel(panelId)
{
    switch (panelId) {
        case '0':
            drawArea('login_page');
            break;
        case '1':
            drawArea('admin_panel');
            break;
        case '2':
            drawArea('another_panel');
            break;
        case '3':
            drawArea('admin_panel');
            break;
        default:
            drawArea('login_page');
            break;
    }
}

/** LOAD WORKSPACE **/
function loadUserWorkspace(message) {
    var userData = $.parseJSON(message);

    if (userData.isvalid)
    {
        $.ajax({
            type: "POST",
            url: "php/MainEntrance.php?action=load_workspace",
            data: {
                'userid': userData.userid,
                'userrole': userData.userrole
            },
            dataType: "text",
            timeout: 5000,
            success: function (message) {
                choosePanel(message);
            },
            error: function () {
                alert('error occured during ajax-request to the server : ' +
                      'method -- loadUserWorkspace (load_workspace)');
            }
        });
    } else
    {
        alert('Wrong Login data');
    }
}

/** SHOW WORKSPACE based on user's privileges **/
function keepUserWorkspace() {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=keep_workspace",
        data: {
            'id': '0'
        },
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            choosePanel(message);
        },
        error: function () {
            alert('error occured during ajax-request to the server : ' +
                  'method -- keepUserWorkspace (keep_workspace)');
        }
    });
}

/** DRAW AREA TMPL **/
function drawArea(areaTmpl) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=draw_panel",
        data: {
            'tmplname': areaTmpl
        },
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            $('.sav2-workspace').empty();
            $('.sav2-workspace').html(message);
        },
        error: function () {
            alert('error occured during ajax-request to the server : ' +
                  'method -- drawArea (tmpl: ' + areaTmpl + ')');
        }
    });
}

/** FILL TABLE for EDIT <some_elements> action **/
/**
 @param tableData -- object that contains table data
 > prefix -- prefix for current table instance
 > content -- table content selector
 > header -- items(columns names) of table header
 @param dataVal -- data object
 > countrows - amount of rows
 > page - page id
 > perpage - rows per page
 > rowitems - array of columns of each row
 */
function fillTable(tableData, dataVal) {
    var numberOfPages = Math.ceil(dataVal.countrows / dataVal.perpage);
    numberOfPages = (numberOfPages < 1 ? 1 : numberOfPages);

    var curPage = parseInt(dataVal.page) + 1;// id + 1, i.e. page starts from 0, but in pagination plugin page starts from 1

    if (curPage > numberOfPages) {
        switch (tableData.prefix) {
            case 'contract':
                currentPageContract = numberOfPages - 1;
                selectContracts(numberOfPages - 1);
                break;
            case 'heated-object':
                currentPageHeatedObject = numberOfPages - 1;
                selectHeatedObjects(numberOfPages - 1);
                break;
            case 'device':
                currentPageDevice = numberOfPages - 1;
                selectDevices(numberOfPages - 1);
                break;
            case 'devicevals':
                currentPageDeviceVals = numberOfPages - 1;
                selectDeviceVals(numberOfPages - 1);
                break;
            case 'deviceconsume':
                currentPageDeviceConsume = numberOfPages - 1;
                selectDeviceConsume(numberOfPages - 1);
                break;
        }

        return;//"exit" fillTable method
    }

    //amount of selected rows info
    var tableString =
            '<div class="input-group">' +
                '<span class="input-group-addon">Всего найдено записей: </span>' +
                '<input id="rowsCount" type="text" class="form-control" ' +
                'disabled="" style="max-width: 200px;" value="' + dataVal.countrows + '">' +
            '</div>' +
            '<hr />';

    tableString += '<ul class="pagination sav2-pages-' + tableData.prefix + '"></ul>';

    //table body start:	
    tableString +=
            '<div class="table-responsive">' +
                '<table class="table table-striped">';

    //header start:
    tableString += '<tr class="info">';

    $.each(tableData.header, function (index, value) {
        tableString += '<td>' + value + '</td>';
    });

    tableString += '</tr>';
    //header end;

    //rows start:
    $.each(dataVal.rowitems, function (index, value) {
        if (value.length != 0)
        {
            tableString += '<tr>';

            //columns start:
            $.each(value, function (index, value) {
                //special condition
                switch (tableData.prefix) {
                    //special condition (start):
                    case 'device':
                        if (index == 2) {
                            tableString += '<td><div data-placement="left" data-toggle="tooltip" title="id: ' + value + '">';
                        } else if (index == 3) {
                            tableString += value + '</div></td>';
                        } else if (index == 5) {
                            if (value == 1) {
                                tableString += '<td><span class="glyphicon glyphicon-check sav2-color-lightgray" style="font-size: 20px; margin-left: 15px;"></span></td>';
                            } else {
                                tableString += '<td></td>';
                            }
                        } else if (index == 6) { 
							if (value == 1) {
                                tableString += '<td><span class="glyphicon glyphicon-check sav2-color-lightgray" style="font-size: 20px; margin-left: 15px;"></span></td>';
                            } else {
                                tableString += '<td></td>';
                            }
						} else {
                            tableString += '<td>' + value + '</td>';
                        }
                        break;
                    case 'devicevals':
                        if (index == 2 || index == 4) {
                            tableString += '<td><div data-placement="left" data-toggle="tooltip" title="id: ' + value + '">';
                        } else if (index == 3 || index == 5) {
                            tableString += value + '</div></td>';
                        } else if (index == 9) {
                            if (value == 1) {
                                tableString += '<td><span class="glyphicon glyphicon-check sav2-color-lightgray" style="font-size: 20px; margin-left: 40px;"></span></td>';
                            } else {
                                tableString += '<td></td>';
                            }
                        } else {
                            tableString += '<td>' + value + '</td>';
                        }
                        break;
                    case 'deviceconsume':
                        if (index == 1 || index == 3) {
                            tableString += '<td><div data-placement="left" data-toggle="tooltip" title="id: ' + value + '">';
                        } else if (index == 2 || index == 4) {
                            tableString += value + '</div></td>';
                        } else if (index == 5 || index == 6) {
                            if (value.indexOf('ERROR') != -1) {
                                var errorInfo = value.split('|');
                                tableString += '<td><div data-placement="left" data-toggle="tooltip" title="' + errorInfo[1] + '"><span class="glyphicon glyphicon-exclamation-sign sav2-color-crimson sav2-error"></span></div></td>';
                            } else if (value.indexOf('ZERO') != -1) {
                                var errorInfo = value.split('|');
                                tableString += '<td><div data-placement="left" data-toggle="tooltip" title="' + errorInfo[1] + '"><span class="sav2-color-crimson">0</span></div></td>';
                            } else if (value.indexOf('NORMATIVE') != -1) {
                                var normativeInfo = value.split('|');
                                tableString += '<td><div class="sav2-color-bluestrict" data-placement="left" data-toggle="tooltip" title="' + normativeInfo[1] + '">Норматив</div></td>';
                            } else if (value.indexOf('BOILER') != -1) {
                                tableString += '<td><span class="sav2-color-lightgray">Бойлер</span></td>';
                            } else if (value.indexOf('HEATMETER') != -1) {
								tableString += '<td><span class="sav2-color-lightgray">Теплосчетчик</span></td>';
							} else {
                                var floatVal = parseFloat(value);
                                if (floatVal >= 0) {
                                    tableString += '<td><span class="sav2-color-greenforest">' + value + '</span></td>';
                                } else {
                                    tableString += '<td><span class="sav2-color-crimson">' + value + '</span></td>';
                                }
                            }
                        } else {
                            tableString += '<td>' + value + '</td>';
                        }
                        break;
                        //special condition (end);
                    default :
                        tableString += '<td>' + value + '</td>';
                        break;
                }
            });
            //columns end;

            /** 
             single column with options
             value[0] is supposed to be 'id' of current row(element)	
             */
            switch (tableData.prefix) {
                case 'deviceconsume':

                    break;
                default :
                    tableString += '<td>' +
                                        '<button title="Изменить" type="button" ' +
                                        'class="btn btn-success btn-sm sav2-opt-btn sav2-upd-' + tableData.prefix + '" ' +
                                        'data-toggle="modal" data-target="#updateElement" id="' + value[0] + '">' +
                                            '<span class="glyphicon glyphicon-pencil"></span>' +
                                        '</button>' +
                                        '<button title="Удалить" type="button" ' +
                                        'class="btn btn-danger btn-sm sav2-opt-btn sav2-del-' + tableData.prefix + '" id="' + value[0] + '">' +
                                            '<span class="glyphicon glyphicon-trash"></span>' +
                                        '</button>';
                    break;
            }
            //additional action buttons
            switch (tableData.prefix) {
                case 'contract':
                    tableString +=
                            '<button title="Добавить теплоустановку" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-insertdata-' + tableData.prefix + '" ' +
                            'data-toggle="modal" data-target="#insertElement" id="' + value[0] + '" contractnum="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-plus"></span>' +
                            '</button>';

                    tableString +=
                            '<button title="Связанные теплоустановки" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-nexttab-' + tableData.prefix + '" ' +
                            'id="' + value[0] + '" contractnum="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-arrow-right"></span>' +
                            '</button>';
                    break;
                case 'heated-object':
                    tableString +=
                            '<button title="Добавить прибор учета" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-insertdata-' + tableData.prefix + '" ' +
                            'data-toggle="modal" data-target="#insertElement" id="' + value[0] + '" heatedobjname="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-plus"></span>' +
                            '</button>';

                    tableString +=
                            '<button title="Связанные приборы учета" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-nexttab-' + tableData.prefix + '" ' +
                            'id="' + value[0] + '" heatedobjname="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-arrow-right"></span>' +
                            '</button>';
                    break;
                case 'device':
                    tableString +=
                            '<button title="Добавить показания ПУ" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-insertdata-' + tableData.prefix + '" ' +
                            'data-toggle="modal" data-target="#insertElement" id="' + value[0] + '" devicenum="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-plus"></span>' +
                            '</button>';

                    tableString +=
                            '<button title="Связанные показания" type="button" ' +
                            'class="btn btn-info btn-sm sav2-opt-btn sav2-nexttab-' + tableData.prefix + '" ' +
                            'id="' + value[0] + '" devicenum="' + value[1] + '">' +
                                '<span class="glyphicon glyphicon-arrow-right"></span>' +
                            '</button>';
                    break;
                case '':

                    break;
            }

            tableString +=         '</td>' +
                            '</tr>';
        }
    });
    //rows end;

    tableString +=
            '</table>' +
        '</div>';
    //table body end;

    tableString += '<ul class="pagination sav2-pages-' + tableData.prefix + '"></ul>';

    //fill table content
    $(tableData.content).empty();
    $(tableData.content).html(tableString);
    //pagination start:
    $('.sav2-pages-' + tableData.prefix).twbsPagination({
        totalPages: numberOfPages,
        visiblePages: 7,
        initiateStartPageClick: false,
        startPage: curPage,
        first: '<span class="glyphicon glyphicon-backward" title="В начало"></span>',
        prev: '<span class="glyphicon glyphicon-step-backward" title="Предыдущая"></span>',
        next: '<span class="glyphicon glyphicon-step-forward" title="Следующая"></span>',
        last: '<span class="glyphicon glyphicon-forward" title="В конец"></span>',
        onPageClick: function (event, page) {
            switch (tableData.prefix) {
                case 'contract':
                    currentPageContract = page - 1;
                    selectContracts(currentPageContract);
                    break;
                case 'heated-object':
                    currentPageHeatedObject = page - 1;
                    selectHeatedObjects(currentPageHeatedObject);
                    break;
                case 'device':
                    currentPageDevice = page - 1;
                    selectDevices(currentPageDevice);
                    break;
                case 'devicevals':
                    currentPageDeviceVals = page - 1;
                    selectDeviceVals(currentPageDeviceVals);
                    break;
                case 'deviceconsume':
                    currentPageDeviceConsume = page - 1;
                    selectDeviceConsume(currentPageDeviceConsume);
                    break;
            }
        }
    });
    //pagination end;
}

/** SELECT <some_elements> **/
/**
 @param queryName -- call named query from backend
 @param searchParams -- object of search paramateres with its values
 @param tableData -- prefix | content - i.e. element(tag) selector | header
 */
function doSelect(queryName, searchParams, tableData) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=" + queryName,
        data: searchParams,
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            if (message == 'ERROR_ACCESS_DENIED') {
                showInfoBox('access denied : method -- ' + queryName, 'INFOBOX_ERROR');
            } else if (message.indexOf('ERROR_PDO') != -1) {
                var errorInfo = message.split('|');
                showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- ' + queryName, 'INFOBOX_ERROR');
            } else {
                var selectedData = $.parseJSON(message);
                fillTable(tableData, selectedData);
                if (queryName == 'select_devices' ||
                        queryName == 'select_devicevals' ||
                        queryName == 'consumptions')
                {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- doSelect (' + queryName + ')', 'INFOBOX_ERROR');
        }
    });
}

/** DELETE <some_elements> **/
/**
 @param queryName -- call named query from backend
 @param searchParams -- object of search paramateres with its values
 @param elementDescription -- "CONTRACT" | "HEATED_OBJECT" | "DEVICE" | "DEVICE_VALUES"
 @param curPageId -- current page
 */
function doDelete(queryName, searchParams, elementDescription, curPageId) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=" + queryName,
        data: searchParams,
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            if (message == 'ERROR_ACCESS_DENIED')
            {
                showInfoBox('access denied : method -- ' + queryName, 'INFOBOX_ERROR');
            } else if (message.indexOf('ERROR_PDO') != -1) {
                var errorInfo = message.split('|');
                showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- ' + queryName, 'INFOBOX_ERROR');
            } else {
                showInfoBox(message, 'INFOBOX_SUCCESS');
                switch (elementDescription) {
                    case 'CONTRACT':
                        selectContracts(curPageId);
                        break;
                    case 'HEATED_OBJECT':
                        selectHeatedObjects(curPageId)
                        break;
                    case 'DEVICE':
                        selectDevices(curPageId);
                        break;
                    case 'DEVICE_VALUES':
                        selectDeviceVals(curPageId);
                        break;
                }
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- doDelete (' + queryName + ')', 'INFOBOX_ERROR');
        }

    });
}

/**
 build input form for called "UpdateModal"-window
 */
function loadUpdateForm(element_id, tmpl_name) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=draw_panel",
        data: {
            'tmplname': tmpl_name
        },
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            $('#elementDataUpd').empty();
            $('#elementDataUpd').html(message);
        },
        complete: function () {
            switch (tmpl_name) {
                case 'update_contract_modal':
                    setEditFieldsForUpdateModal(element_id,
                            'select_contract_by_id',
                            {'mainTitle': 'Изменение данных договора', 'paragraph': 'Данные по договору:'});
                    break;
                case 'update_heated_object_modal':
                    setEditFieldsForUpdateModal(element_id,
                            'select_heated_object_by_id',
                            {'mainTitle': 'Изменение данных теплоустановки', 'paragraph': 'Данные по теплоустановке:'});
                    break;
                case 'update_device_modal':
                    setEditFieldsForUpdateModal(element_id,
                            'select_device_by_id',
                            {'mainTitle': 'Изменение данных прибора', 'paragraph': 'Данные по прибору:'});
                    break;
                case 'update_devicevals_modal':
                    setEditFieldsForUpdateModal(element_id,
                            'select_devicevals_by_id',
                            {'mainTitle': 'Изменение данных показаний ПУ', 'paragraph': 'Данные по показаниям ПУ:'});
                    break;
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- loadUpdateForm (' + tmpl_name + ')', 'INFOBOX_ERROR');
        }
    });
}

/**
 fill input fields with current(actual) values of "UpdateModal"-window
 */
function setEditFieldsForUpdateModal(element_id, queryName, modalTitles) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=" + queryName,
        data: {
            'id': element_id
        },
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            if (message == 'ERROR_ACCESS_DENIED') {
                showInfoBox('access denied : method -- ' + queryName, 'INFOBOX_ERROR');
            } else if (message.indexOf('ERROR_PDO') != -1) {
                var errorInfo = message.split('|');
                showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- ' + queryName, 'INFOBOX_ERROR');
            } else {
                $('#updateElement .modal-title').empty();
                $('#updateElement .modal-body p').empty();
                $('#updateElement .modal-title').html(modalTitles.mainTitle);
                $('#updateElement .modal-body p').html(modalTitles.paragraph);

                var elementDataObj = $.parseJSON(message);
                $('#entity').val(elementDataObj.entity);
                $.each(elementDataObj.fields, function (key, value) {
                    //special fields start:
                    if (key.indexOf('calcperiodDevicevalsUpd') != -1)
                    {
                        $('#' + key).datepicker('update', value);
                    } else if (key.indexOf('isNormativeUpd') != -1)
                    {
                        if (value == '1') {
                            $('#' + key).prop('checked', true);
                        } else {
                            $('#' + key).prop('checked', false);
                        }
                    } else if (key.indexOf('isBoilerUpd') != -1)
                    {
                        if (value == '1') {
                            $('#' + key).prop('checked', true);
                        } else {
                            $('#' + key).prop('checked', false);
                        }
                    } else if (key.indexOf('isHeatmeterUpd') != -1)
                    {
                        if (value == '1') {
                            $('#' + key).prop('checked', true);
                        } else {
                            $('#' + key).prop('checked', false);
                        }
                    }
                    //special fields end;
                    else
                    {
                        $('#' + key).val(value);
                    }
                });
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- setEditFieldsForUpdateModal (' + queryName + ')', 'INFOBOX_ERROR');
        }
    });
}

/** UPDATE <some_elements> **/
/**
 @param queryName -- call named query from backend
 @param dataObject -- POST-parameters
 */
function doUpdate(queryName, dataObject) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=" + queryName,
        data: dataObject,
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            if (message == 'ERROR_ACCESS_DENIED') {
                showInfoBox('access denied : method -- ' + queryName, 'INFOBOX_ERROR');
            } else if (message.indexOf('ERROR_PDO') != -1) {
                var errorInfo = message.split('|');
                showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- ' + queryName, 'INFOBOX_ERROR');
            } else {
                showInfoBox(message, 'INFOBOX_INFO');
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- doUpdate (' + queryName + ')', 'INFOBOX_ERROR');
        }
    });
}


/**
 build input form for called "InsertModal"-window
 */
function loadInsertForm(element_id, additional_id, tmpl_name) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=draw_panel",
        data: {
            'tmplname': tmpl_name
        },
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            $('#elementDataIns').empty();
            $('#elementDataIns').html(message);
        },
        complete: function () {
            $('#insertElement .modal-title').empty();
            $('#insertElement .modal-body p').empty();
            switch (tmpl_name) {
                case 'insert_contract_modal':

                    break;
                case 'insert_heated_object_modal':
                    $('#insertElement .modal-title').html('Ввод новой теплоустановки');
                    $('#insertElement .modal-body p').html('Данные по теплоустановке:');
                    $('#insertElement #elementDataIns #contractIDIns').val(element_id);
                    $('#insertElement #elementDataIns #contractNumIns').val(additional_id);
                    break;
                case 'insert_device_modal':
                    $('#insertElement .modal-title').html('Ввод нового прибора учета');
                    $('#insertElement .modal-body p').html('Данные по прибору учета:');
                    $('#insertElement #elementDataIns #heatedobjIDIns').val(element_id);
                    $('#insertElement #elementDataIns #heatedobjNameIns').val(additional_id);
                    break;
                case 'insert_devicevals_modal':
                    $('#insertElement .modal-title').html('Ввод новых показаний ПУ');
                    $('#insertElement .modal-body p').html('Данные по показаниям ПУ:');
                    $('#insertElement #elementDataIns #deviceIDIns').val(element_id);
                    $('#insertElement #elementDataIns #deviceNumIns').val(additional_id);
                    break;
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- loadInsertForm (' + tmpl_name + ')', 'INFOBOX_ERROR');
        }
    });
}

/** INSERT <some_elements> **/
/**
 @param queryName -- call named query from backend
 @param dataObject -- POST-parameters
 */
function doInsert(queryName, dataObject) {
    $.ajax({
        type: "POST",
        url: "php/MainEntrance.php?action=" + queryName,
        data: dataObject,
        dataType: "text",
        timeout: 10000,
        success: function (message) {
            if (message == 'ERROR_ACCESS_DENIED') {
                showInfoBox('access denied : method -- ' + queryName, 'INFOBOX_ERROR');
            } else if (message.indexOf('ERROR_PDO') != -1) {
                var errorInfo = message.split('|');
                showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- ' + queryName, 'INFOBOX_ERROR');
            } else {
                showInfoBox(message, 'INFOBOX_INFO');
            }
        },
        error: function () {
            showInfoBox('error occured during ajax-request to the server : ' +
                        'method -- doInsert (' + queryName + ')', 'INFOBOX_ERROR');
        }
    });
}

//tab-2 contracts
function selectContracts(pageId) {
    doSelect('select_contracts',
            {
                'page': pageId,
                'id': $('#srch-contract-id').val().trim(),
                'contract_num': $('#srch-contract-num').val().trim()
            },
            {
                'prefix': 'contract',
                'content': '.sav2-edit-contract-table',
                'header': ['id', '№ договора', 'Действие']
            }
    );
}

function updateContract() {
    doUpdate('update_contract',
            {
                'id': $('#updateElement #elementDataUpd #idUpd').val(),
                'contract_num': $('#updateElement #elementDataUpd #numContractUpd').val().trim()
            }
    );
}

function insertHeatedObject() {
    doInsert('insert_heated_object',
            {
                'contract_id': $('#insertElement #elementDataIns #contractIDIns').val(),
                'name': $('#insertElement #elementDataIns #nameHeatedobjIns').val().trim()
            }
    );
}
//tab-3 heated objects
function selectHeatedObjects(pageId) {
    doSelect('select_heated_objects',
            {
                'page': pageId,
                'id': $('#srch-heated-object-id').val().trim(),
                'name': $('#srch-heated-object-name').val().trim(),
                'contractnum': $('#srch-heated-object-contractnum').val().trim()
            },
            {
                'prefix': 'heated-object',
                'content': '.sav2-edit-heated-object-table',
                'header': ['id', 'Наименование', '№ договора', 'Действие']
            }
    );
}

function updateHeatedObject() {
    doUpdate('update_heated_object',
            {
                'id': $('#updateElement #elementDataUpd #idUpd').val(),
                'name': $('#updateElement #elementDataUpd #nameHeatedObjUpd').val().trim()
            }
    );
}

function insertDevice() {
    doInsert('insert_device',
            {
                'heated_object_id': $('#insertElement #elementDataIns #heatedobjIDIns').val(),
                'device_num': $('#insertElement #elementDataIns #deviceNumIns').val().trim(),
                'is_boiler': $('#insertElement #elementDataIns #isBoilerIns').is(':checked') ? '1' : '0',
				'is_heatmeter': $('#insertElement #elementDataIns #isHeatmeterIns').is(':checked') ? '1' : '0'
            }
    );
}
//tab-4 devices
function selectDevices(pageId) {
    doSelect('select_devices',
            {
                'page': pageId,
                'id': $('#srch-device-id').val().trim(),
                'device_num': $('#srch-device-num').val().trim(),
                'heated_object_name': $('#srch-device-nameHO').val().trim(),
                'heated_object_id': $('#srch-device-idHO').val().trim(),
                'contractnum': $('#srch-device-contractnum').val().trim(),
				'is_boiler': $('#srch-device-isBoiler').is(':checked') ? '1' : '',
				'is_heatmeter': $('#srch-device-isHeatmeter').is(':checked') ? '1' : ''
            },
            {
                'prefix': 'device',
                'content': '.sav2-edit-device-table',
                'header': ['id', '№ прибора', 'Теплоустановка', '№ договора', 'Бойлер', 'Теплосчетчик', 'Действие']
            }
    );
}

function updateDevice() {
    doUpdate('update_device',
            {
                'id': $('#updateElement #elementDataUpd #idUpd').val(),
                'device_num': $('#updateElement #elementDataUpd #numDeviceUpd').val().trim(),
                'is_boiler': $('#updateElement #elementDataUpd #isBoilerUpd').is(':checked') ? '1' : '0',
				'is_heatmeter': $('#updateElement #elementDataUpd #isHeatmeterUpd').is(':checked') ? '1' : '0'
            }
    );
}

function insertDeviceVals() {
    var monthNyear = $('#insertElement #elementDataIns #calcperiodIns').val().trim().split('.');

    doInsert('insert_devicevals',
            {
                'device_id': $('#insertElement #elementDataIns #deviceIDIns').val(),
                'calc_value': $('#insertElement #elementDataIns #meteringvalsIns').val().trim(),
                'calc_month': monthNyear[0],
                'calc_year': monthNyear[1],
                'is_normative': $('#insertElement #elementDataIns #isNormativeIns').is(':checked') ? '1' : '0'
            }
    );
}
//tab-5 device values (aka metering values)
function selectDeviceVals(pageId) {
    doSelect('select_devicevals',
            {
                'page': pageId,
                'id': $('#srch-devicevals-id').val().trim(),
                'device_id': $('#srch-devicevals-deviceid').val().trim(),
                'device_num': $('#srch-devicevals-devicenum').val().trim(),
                'calc_month': $('#srch-devicevals-month').val().trim(),
                'calc_year': $('#srch-devicevals-year').val().trim(),
                'heated_object_name': $('#srch-devicevals-nameHO').val().trim(),
                'contract_num': $('#srch-devicevals-contractnum').val().trim()
            },
            {
                'prefix': 'devicevals',
                'content': '.sav2-edit-devicevals-table',
                'header': ['id', '№ Договора', 'Теплоустановка', '№ прибора', 'показания', 'месяц', 'год', 'По нормативу', 'Действие']
            }
    );
}

function updateDeviceVals() {
    var monthNyear = $('#updateElement #elementDataUpd #calcperiodDevicevalsUpd').val().trim().split('.');

    doUpdate('update_devicevals',
            {
                'id': $('#updateElement #elementDataUpd #idUpd').val(),
                'calc_value': $('#updateElement #elementDataUpd #meteringvalsDevicevalsUpd').val().trim(),
                'calc_month': monthNyear[0],
                'calc_year': monthNyear[1],
                'is_normative': $('#updateElement #elementDataUpd #isNormativeUpd').is(':checked') ? '1' : '0'
            }
    );
}
//tab-6 device consume
function selectDeviceConsume(pageId) {
    var monthNyear = $('#srch-deviceconsume-date').val().trim().split('.');

    doSelect('consumptions',
            {
                'page': pageId,
                'id': $('#srch-deviceconsume-id').val().trim(),
                'device_num': $('#srch-deviceconsume-num').val().trim(),
                'heated_object_name': $('#srch-deviceconsume-nameHO').val().trim(),
                'heated_object_id': $('#srch-deviceconsume-idHO').val().trim(),
                'contractnum': $('#srch-deviceconsume-contractnum').val().trim(),
                'calc_month': monthNyear[0],
                'calc_year': monthNyear[1],
                'hide_normative_vals': $('#hideNormativeVals').is(':checked') ? '1' : '0'
            },
            {
                'prefix': 'deviceconsume',
                'content': '.sav2-edit-deviceconsume-table',
                'header': ['№ договора', 'Теплоустановка', '№ прибора', 'Расход (м3)', 'Расход (Гкал)']
            }
    );
}

//initialization: kinda -- "public static void main(String[] args){ ... }"
/**
 *	public static void (String[] args){ ... }
 */
$(document).ready(function () {
    //globals
    currentPageContract = 0;
    currentPageHeatedObject = 0;
    currentPageDevice = 0;
    currentPageDeviceVals = 0;
    currentPageDeviceConsume = 0;

    var newRecordJSON = '';

    keepUserWorkspace();

    //login button
    $(document).on('click', '#send', function () {

        var usr = $('#usr').val().trim();
        var pwd = $('#pwd').val();

        if ($(this).attr('active') == 'true' && usr.length != 0 && pwd.length != 0)
        {
            $.ajax({
                type: "POST",
                url: "php/MainEntrance.php?action=login",
                data: {
                    'id': 'isuservalid',
                    'usr': usr,
                    'pwd': pwd
                },
                dataType: "text",
                timeout: 5000,
                success: function (message) {
                    if (message.indexOf('ERROR_PDO') != -1) {
                        var errorInfo = message.split('|');
                        alert('PDO Error: (' + errorInfo[1] + ') : method -- login');
                    } else if (message == 'ERROR_POSTDATA_INCORRECT') {
                        alert('postdata is incorrect : method -- login');
                    } else {
                        loadUserWorkspace(message);
                    }
                },
                error: function () {
                    alert('error occured during ajax-request to the server : ' +
                          'method -- login');
                }
            });
        }
    });

    //handle 'ENTER' keypress event
    $(document).on('keypress', '#usr', function (e) {
        if (e.which == 13) {
            $('#send').trigger('click');
            return false;
        }
    });

    //handle 'ENTER' keypress event
    $(document).on('keypress', '#pwd', function (e) {
        if (e.which == 13) {
            $('#send').trigger('click');
            return false;
        }
    });

    //logout button
    $(document).on('click', '#logout', function () {
        $.ajax({
            type: "POST",
            url: "php/MainEntrance.php?action=logout",
            data: {
                'id': '0'
            },
            dataType: "text",
            timeout: 5000,
            success: function (message) {

            },
            error: function () {
                alert('error occured during ajax-request to the server : ' +
                      'method -- logout');
            },
            complete: function () {
                keepUserWorkspace();
            }
        });

    });

    //Admin workarea options (tabs)
    $(document).on('click', '.sav2-admin-wa .btn-group .btn', function () {
        $('.sav2-admin-wa .btn-group .btn').removeClass('active');
        $(this).addClass('active');

        var tabToShow = '.sav2-tab1';

        if ($(this).attr('id') == 'showTab2') {
            tabToShow = '.sav2-tab2';
        } else if ($(this).attr('id') == 'showTab3') {
            tabToShow = '.sav2-tab3';
        } else if ($(this).attr('id') == 'showTab4') {
            tabToShow = '.sav2-tab4';
        } else if ($(this).attr('id') == 'showTab5') {
            tabToShow = '.sav2-tab5';
        } else if ($(this).attr('id') == 'showTab6') {
            tabToShow = '.sav2-tab6';
        } else if ($(this).attr('id') == 'showTab7') {
            tabToShow = '.sav2-tab7';
        } else {
            tabToShow = '.sav2-tab1';
        }

        $('.sav2-tabs').hide(0, function () {
            $(tabToShow).show(0);
        });
    });

    //modal window for update element -- button "updElementYes"
    $(document).on('click', '#updElementYes', function () {
        switch ($('#updateElement #elementDataUpd #entity').val()) {
            case 'contract':
                updateContract();
                selectContracts(currentPageContract);
                break;
            case 'heated-object':
                updateHeatedObject();
                selectHeatedObjects(currentPageHeatedObject);
                break;
            case 'device':
                updateDevice();
                selectDevices(currentPageDevice);
                break;
            case 'devicevals':
                updateDeviceVals();
                selectDeviceVals(currentPageDeviceVals);
                break;
        }
    });

    //modal window for insert element -- button "insElementYes"
    $(document).on('click', '#insElementYes', function () {
        switch ($('#insertElement #elementDataIns #entity').val()) {
            case 'contract':

                break;
            case 'heated-object':
                insertHeatedObject();
                break;
            case 'device':
                insertDevice();
                break;
            case 'devicevals':
                insertDeviceVals();
                break;
        }
    });

    /** -- TAB 1 -- **/
    //Admin workarea Tab-1 (button .add-heated-object)
    $(document).on('click', '.add-heated-object', function () {
        var factoryString =
                '<div class="col-md-12 sav2-heated-object-item">' +
                '	<div class="form-group">' +
                '		<label for="heated-object-name">Тепловая установка:</label>' +
                '		<div class="input-group">' +
                '			<input type="text" class="form-control heated-object-name" placeholder="Наименование">' +
                '			<span class="input-group-btn">' +
                '				<button class="btn btn-default remove-heated-object" title="Удалить"><span class="glyphicon glyphicon-minus"></span></button>' +
                '			</span>' +
                '		</div>' +
                '	</div>' +
                '	<div class="col-md-12 sav2-device-content">' +
                '		<div class="col-md-3">' +
                '			<div class="form-group">' +
                '				<div class="input-group">' +
                '					<input type="text" class="form-control" value="Приборы учета:" disabled>' +
                '					<span class="input-group-btn">' +
                '						<button class="btn btn-default add-device" title="Добавить"><span class="glyphicon glyphicon-plus"></span></button>' +
                '					</span>' +
                '				</div>' +
                '			</div>' +
                '		</div>' +
                '		<div class="sav2-device-list">' +
                //device items
                '		</div>' +
                '	</div>' +
                '</div>';

        $('.sav2-heated-object-list').append(factoryString);
        /*
         $('.heated-object-name', document).suggestions({
         token: "aa87679e3bfbbdaca05a44aa93ad6af10d54045a",
         type: "ADDRESS",
         count: 7
         });
         */
    });

    //Admin workarea Tab-1 (button .remove-heated-object)
    $(document).on('click', '.remove-heated-object', function () {
        $(this).parents('.sav2-heated-object-item').remove();
    });

    //Admin workarea Tab-1 (button add-device)
    $(document).on('click', '.add-device', function () {
        var factoryString =
                '<div class="col-md-12 sav2-device-item">' +
                '	<div class="form-group">' +
                '		<label for="device_num">Прибор учета:</label>' +
                '		<div class="input-group">' +
                '			<input type="text" class="form-control device_num" placeholder="Номер прибора">' +
                '			<span class="input-group-addon">' +
                '				<label class="checkbox-inline"><input type="checkbox" class="is_boiler" >Бойлер</label>' +
				'				<label class="checkbox-inline"><input type="checkbox" class="is_heatmeter" >Теплосчетчик</label>' +
                '			</span>' +
                '			<span class="input-group-btn">' +
                '				<button class="btn btn-default remove-device" title="Удалить"><span class="glyphicon glyphicon-minus"></span></button>' +
                '			</span>' +
                '		</div>' +
                '	</div>' +
                '</div>';

        $(this).parents('.sav2-device-content').find('.sav2-device-list').append(factoryString);
    });

    //Admin workarea Tab-1 (button .remove-device)
    $(document).on('click', '.remove-device', function () {
        $(this).parents('.sav2-device-item').remove();
    });
	
	//Admin workarea Tab-1 (checkbox .is_boiler)
	$(document).on('change', '.is_boiler', function () {
		if ($(this).prop('checked')) {
			$(this).parents('.input-group-addon').find('.is_heatmeter').prop('checked', false);
		}
    });
	
	//Admin workarea Tab-1 (checkbox .is_heatmeter)
	$(document).on('change', '.is_heatmeter', function () {
		if ($(this).prop('checked')) {
			$(this).parents('.input-group-addon').find('.is_boiler').prop('checked', false);
		}
    });

    /**
     JSON array of objects builders
     -- START --
     */
    //devices
    function buildDeviceObjects(premiseObj) {
        if (premiseObj.length == 0)
        {
            newRecordJSON += '"devices" : []';
        } else
        {
            newRecordJSON += '"devices" : [ ';
            premiseObj.each(function (index, element) {
                if (index === premiseObj.length - 1) {
                    newRecordJSON += '{ ';
                    newRecordJSON += '"device_num" : "' + $(element).find('.device_num').val() + '", ';
                    newRecordJSON += '"is_boiler" : "' + ($(element).find('.is_boiler').is(':checked') ? '1' : '0') + '", ';
					newRecordJSON += '"is_heatmeter" : "' + ($(element).find('.is_heatmeter').is(':checked') ? '1' : '0') + '"';
                    newRecordJSON += ' }';
                } else {
                    newRecordJSON += '{ ';
                    newRecordJSON += '"device_num" : "' + $(element).find('.device_num').val() + '", ';
                    newRecordJSON += '"is_boiler" : "' + ($(element).find('.is_boiler').is(':checked') ? '1' : '0') + '", ';
					newRecordJSON += '"is_heatmeter" : "' + ($(element).find('.is_heatmeter').is(':checked') ? '1' : '0') + '"';
                    newRecordJSON += ' },';
                }
            });
            newRecordJSON += ' ]';
        }
    }

    //heated objects
    function buildHeatedObjObjects() {
        if ($('.sav2-heated-object-item').length == 0)
        {
            newRecordJSON += '"heated_objects" : []';
        } else
        {
            newRecordJSON += '"heated_objects" : [ ';
            $('.sav2-heated-object-item').each(function (index, element) {
                if (index === $('.sav2-heated-object-item').length - 1) {
                    newRecordJSON += '{ ';
                    newRecordJSON += '"name" : "' + $(element).find('.heated-object-name').val().replace(/"/g, '\\"') + '", ';
                    buildDeviceObjects($(element).find('.sav2-device-item'));
                    newRecordJSON += ' }';
                } else {
                    newRecordJSON += '{ ';
                    newRecordJSON += '"name" : "' + $(element).find('.heated-object-name').val().replace(/"/g, '\\"') + '", ';
                    buildDeviceObjects($(element).find('.sav2-device-item'));
                    newRecordJSON += ' },';
                }
            });
            newRecordJSON += ' ]';
        }
    }
    /**
     JSON array of objects builders
     -- END --
     */

    //Admin workarea Tab-1 (button #recordnew -- send record)
    $(document).on('click', '#recordnew', function () {
        $('#recordData').empty();
        var htmlString = '';
        htmlString += 'Номер договора: ' + $('#contract_num').val() + '<br />';
        htmlString += '<ul>';
        $('.sav2-heated-object-item').each(function (index, element) {
            htmlString += '<li>';
            htmlString += 'Теплоустановка: ' + $(element).find('.heated-object-name').val();
            htmlString += '<ul>';
            $(element).find('.sav2-device-item').each(function (index, element) {
                htmlString += '<li>';
                htmlString += 'Номер ПУ: ' + $(element).find('.device_num').val();
                htmlString += ($(element).find('.is_boiler').is(':checked') ? ' <i>(Бойлер)</i>' : '');
				htmlString += ($(element).find('.is_heatmeter').is(':checked') ? ' <i>(Теплосчетчик)</i>' : '');
                htmlString += '</li>';
            });
            htmlString += '</ul>';
            htmlString += '</li>';
        });
        htmlString += '</ul>';

        $('#recordData').html(htmlString);
    });

    //Admin workarea Tab-1 (button #addRecordYes -- send record MODAL)
    $(document).on('click', '#addRecordYes', function () {
        newRecordJSON = '';
        newRecordJSON += '{ ';
		
        newRecordJSON += '"contract_num" : "' + $('#contract_num').val() + '", ';

        //heated-objects -> devices
        buildHeatedObjObjects();

        newRecordJSON += ' }';

        //alert(newRecordJSON);

        $.ajax({
            type: "POST",
            url: "php/MainEntrance.php?action=insert_newrecord",
            data: {
                'newRecordJSON': newRecordJSON
            },
            dataType: "text",
            timeout: 10000,
            success: function (message) {
                if (message == 'ERROR_ACCESS_DENIED') {
                    showInfoBox('access denied : method -- insert_newrecord', 'INFOBOX_ERROR');
                } else if (message.indexOf('ERROR_PDO') != -1) {
                    var errorInfo = message.split('|');
                    showInfoBox('PDO Error: (' + errorInfo[1] + ') : method -- insert_newrecord', 'INFOBOX_ERROR');
                } else if (message.indexOf('ERROR_CONTRACT_EXISTS') != -1) {
                    var errorInfo = message.split('|');
                    showInfoBox('Договор с номером <b>' + errorInfo[1] + '</b> уже существует в базе', 'INFOBOX_ERROR');
                } else {
                    resetNewRecordForm();
                    showInfoBox(message, 'INFOBOX_SUCCESS');
                }
            },
            error: function () {
                showInfoBox('error occured during ajax-request to the server : ' +
                            'method -- insert_newrecord', 'INFOBOX_ERROR');
            },
            complete: function () {
                $('html, body').animate({scrollTop: 0}, 500);
            }
        });
    });

    /** -- TAB 2 -- **/
    //Admin workarea Tab-2
    //show up
    $(document).on('click', '#showTab2', function () {
        selectContracts(currentPageContract);
    });

    //Admin workarea Tab-2 (search)
    $(document).on('input', '.sav2-srch-contract', function () {
        selectContracts(currentPageContract);
    });

    //Admin workarea Tab-2 (clear search input)
    $(document).on('click', '.clear-srch-contract', function () {
        $(this).parents('.input-group').find('.sav2-srch-contract').val('');
        selectContracts(currentPageContract);
    });

    //Admin workarea Tab-2 (delete contract)
    $(document).on('click', '.sav2-del-contract', function () {
        var delConfirm = confirm('Удалить данный договор (и все связанные с ним объекты) под id: ' + $(this).attr('id'));
        if (delConfirm) {
            doDelete('delete_contract', {'id': $(this).attr('id')}, 'CONTRACT', currentPageContract);
        }
    });

    //Admin workarea Tab-2 (update contract)
    $(document).on('click', '.sav2-upd-contract', function () {
        loadUpdateForm($(this).attr('id'), 'update_contract_modal');
    });

    //Admin workarea Tab-2 (insert heated object)
    $(document).on('click', '.sav2-insertdata-contract', function () {
        loadInsertForm($(this).attr('id'), $(this).attr('contractnum'), 'insert_heated_object_modal');
    });

    //Admin workarea Tab-2 (contract - nexttab)
    $(document).on('click', '.sav2-nexttab-contract', function () {
        $('#srch-heated-object-contractnum').val($(this).attr('contractnum'));
        $('#srch-heated-object-contractnum').css({'background-color': 'rgba(255, 0, 0, 0.25)'});
        $('#showTab3').trigger('click');
    });

    /** -- TAB 3 -- **/
    //Admin workarea Tab-3
    //show up
    $(document).on('click', '#showTab3', function () {
        selectHeatedObjects(currentPageHeatedObject);
    });

    //Admin workarea Tab-3 (search)
    $(document).on('input', '.sav2-srch-heated-object', function () {
        selectHeatedObjects(currentPageHeatedObject);
        $(this).removeAttr('style');
    });

    //Admin workarea Tab-3 (clear search input)
    $(document).on('click', '.clear-srch-heated-object', function () {
        $(this).parents('.input-group').find('.sav2-srch-heated-object').val('');
        selectHeatedObjects(currentPageHeatedObject);
        $(this).parents('.input-group').find('.sav2-srch-heated-object').removeAttr('style');
    });

    //Admin workarea Tab-3 (delete heated-object)
    $(document).on('click', '.sav2-del-heated-object', function () {
        var delConfirm = confirm('Удалить данную теплоустановку (и все связанные с ней объекты) под id: ' + $(this).attr('id'));
        if (delConfirm) {
            doDelete('delete_heated_object', {'id': $(this).attr('id')}, 'HEATED_OBJECT', currentPageHeatedObject);
        }
    });

    //Admin workarea Tab-3 (update heated-object)
    $(document).on('click', '.sav2-upd-heated-object', function () {
        loadUpdateForm($(this).attr('id'), 'update_heated_object_modal');
    });

    //Admin workarea Tab-3 (insert device)
    $(document).on('click', '.sav2-insertdata-heated-object', function () {
        loadInsertForm($(this).attr('id'), $(this).attr('heatedobjname'), 'insert_device_modal');
    });

    //Admin workarea Tab-3 (heated-object - nexttab)
    $(document).on('click', '.sav2-nexttab-heated-object', function () {
        $('#srch-device-idHO').val($(this).attr('id'));
        $('#srch-device-idHO').css({'background-color': 'rgba(255, 0, 0, 0.25)'});
        $('#showTab4').trigger('click');
    });

    /** -- TAB 4 -- **/
    //Admin workarea Tab-4
    //show up
    $(document).on('click', '#showTab4', function () {
        selectDevices(currentPageDevice);
    });

    //Admin workarea Tab-4 (search)
    $(document).on('input', '.sav2-srch-device', function () {
        selectDevices(currentPageDevice);
        $(this).removeAttr('style');
    });
	
	//Admin workarea Tab-4 (search by type)
	$(document).on('change', '.sav2-srch-device-by-type', function () {
        selectDevices(currentPageDevice);
    });
	
    //Admin workarea Tab-4 (clear search input)
    $(document).on('click', '.clear-srch-device', function () {
        $(this).parents('.input-group').find('.sav2-srch-device').val('');
        selectDevices(currentPageDevice);
        $(this).parents('.input-group').find('.sav2-srch-device').removeAttr('style');
    });

    //Admin workarea Tab-4 (delete device)
    $(document).on('click', '.sav2-del-device', function () {
        var delConfirm = confirm('Удалить данный прибор учета (и все связанные с ним показания) под id: ' + $(this).attr('id'));
        if (delConfirm) {
            doDelete('delete_device', {'id': $(this).attr('id')}, 'DEVICE', currentPageDevice);
        }
    });

    //Admin workarea Tab-4 (update device)
    $(document).on('click', '.sav2-upd-device', function () {
        loadUpdateForm($(this).attr('id'), 'update_device_modal');
    });

    //Admin workarea Tab-4 (insert device values)
    $(document).on('click', '.sav2-insertdata-device', function () {
        loadInsertForm($(this).attr('id'), $(this).attr('devicenum'), 'insert_devicevals_modal');
    });

    //Admin workarea Tab-4 (device - nexttab)
    $(document).on('click', '.sav2-nexttab-device', function () {
        $('#srch-devicevals-deviceid').val($(this).attr('id'));
        $('#srch-devicevals-deviceid').css({'background-color': 'rgba(255, 0, 0, 0.25)'});
        $('#showTab5').trigger('click');
    });

    /** -- TAB 5 -- **/
    //Admin workarea Tab-5
    //show up
    $(document).on('click', '#showTab5', function () {
        selectDeviceVals(currentPageDeviceVals);
    });

    //Admin workarea Tab-5 (search)
    $(document).on('input', '.sav2-srch-devicevals', function () {
        selectDeviceVals(currentPageDeviceVals);
        $(this).removeAttr('style');
    });

    //Admin workarea Tab-5 (clear search input)
    $(document).on('click', '.clear-srch-devicevals', function () {
        $(this).parents('.input-group').find('.sav2-srch-devicevals').val('');
        selectDeviceVals(currentPageDeviceVals);
        $(this).parents('.input-group').find('.sav2-srch-devicevals').removeAttr('style');
    });

    //Admin workarea Tab-5 (delete deviceval)
    $(document).on('click', '.sav2-del-devicevals', function () {
        var delConfirm = confirm('Удалить данные показания прибора учета под id: ' + $(this).attr('id'));
        if (delConfirm) {
            doDelete('delete_devicevals', {'id': $(this).attr('id')}, 'DEVICE_VALUES', currentPageDeviceVals);
        }
    });

    //Admin workarea Tab-5 (update deviceval)
    $(document).on('click', '.sav2-upd-devicevals', function () {
        loadUpdateForm($(this).attr('id'), 'update_devicevals_modal');
    });

    /** -- TAB 6 -- **/
    //Admin workarea Tab-6
    //show up
    $(document).on('click', '#showTab6', function () {
        $('#srch-deviceconsume-date').datepicker('update', new Date());
        //selectDeviceConsume(currentPageDeviceConsume); -- already triggered onChange event on '#srch-deviceconsume-date' element
    });

    //Admin workarea Tab-6 (search)
    $(document).on('input', '.sav2-srch-deviceconsume', function () {
        selectDeviceConsume(currentPageDeviceConsume);
    });

    //Admin workarea Tab-6 (datepicker search)
    $(document).on('change', '#srch-deviceconsume-date', function () {
        selectDeviceConsume(currentPageDeviceConsume);
    });

    //Admin workarea Tab-6 (checkbox search)
    $(document).on('click', '#hideNormativeVals', function () {
        selectDeviceConsume(currentPageDeviceConsume);
    });

    //Admin workarea Tab-6 (clear search input)
    $(document).on('click', '.clear-srch-deviceconsume', function () {
        $(this).parents('.input-group').find('.sav2-srch-deviceconsume').val('');
        selectDeviceConsume(currentPageDeviceConsume);
    });

    //Admin workarea Tab-6 (button writeDataIntoXLSX)
    $(document).on('click', '#writeDataIntoXLSX', function () {
        lockPanel();
        var monthNyear = $('#srch-deviceconsume-date').val().trim().split('.');
        $.ajax({
            type: "POST",
            url: "php/MainEntrance.php?action=consumptionSOAP",
            data: {
                'month': monthNyear[0],
                'year': monthNyear[1]
            },
            dataType: "text",
            timeout: 60000,
            success: function (message) {
                unlockPanel();
                if (message == 'ERROR_ACCESS_DENIED') {
                    showInfoBox('access denied : method -- Tab-6 click->#writeDataIntoXLSX', 'INFOBOX_ERROR');
                } else if (message.indexOf('ERROR_WS') != -1) {
                    var errorInfo = message.split('|');
                    showInfoBox('Web service call error: ' + errorInfo[1], 'INFOBOX_ERROR');
                } else if (message.indexOf('ERROR') != -1) {
                    var errorInfo = message.split('|');
                    showInfoBox('Java runtime error: ' + errorInfo[1], 'INFOBOX_ERROR');
                } else {
                    window.location = window.location.href + message;
                    showInfoBox("Данные успешно выгрузились", 'INFOBOX_INFO');
                }
            },
            error: function () {
                unlockPanel();
                showInfoBox('error occured during ajax-request to the server : method -- Tab-6 click->#writeDataIntoXLSX', 'INFOBOX_ERROR');
            },
            complete: function () {

            }
        });
    });
});
