$(document).ready(function(){

    $('#environmentList').bind('change', function() {
        var action = 'getCurrentConfig';
        //var environmentId = $('#environmentList option:selected').data('id');
        var userFunction = function(reqData){
            $('#cronConfig').html(reqData);
            $("#accordionCurrent").accordion({
                active: false,
                collapsible:true
            });
            $('.saveButton').bind('click', saveFile);
            $('.delRowButton').bind('click', delRow);
            $('.changeRowButton').bind('click', changeRow);
            $('td').bind('click', cellClick);
        };
        var param = {
        //    "environmentId":environmentId

        };
        sendData(action, param, userFunction);
    });


    function activeCronCommands() {
        var tableCronCommands = $('#accordionCurrent')
            .find('[aria-hidden="false"]')
            .find('.cronCommands');

        var rowsCronCommands = tableCronCommands.children().children('[data-type=tableContent]');
        var rowsObj = {};
        for (var i = 0; i < rowsCronCommands.length; i++) {
            var rowData = [];
            var td = $(rowsCronCommands[i]).children('[data-type=editable]');
            for (var j = 0; j < td.length; j++) {
                rowData.push($(td[j]).text().trim());
            }
            rowsObj[i] = rowData;
        }
        return rowsObj;
    }


    function activeEnvironVars() {

        var tableEnvironVars = $('#accordionCurrent')
            .find('[aria-hidden="false"]')
            .find('.envVars');

        var rowsEnvironVars = tableEnvironVars.children().children('[data-type=tableContent]');
        var rowsObj = {};
        for (var i = 0; i < rowsEnvironVars.length; i++) {
            var rowData = [];
            var td = $(rowsEnvironVars[i]).children();
            for (var j = 0; j < td.length; j++) {
                rowData.push($(td[j]).text().trim());
            }
            rowsObj[i] = rowData;
        }
       return rowsObj;
    }


    $('.addFullConfig').bind('click', addRowGroup);
    $('.addRowButton').bind('click', addRow);

    function addRow() {
        var data = getData(this);
        var envVar = getEnvironmentVariables(this)
        var cronTiming = getCronTiming(this);
        var action = 'addRow';

        var userFunction = function(reqData){
            $('#accordionCurrent')
                .find('[aria-hidden="false"]')
                .find('.'+data.tableClass)
                .append(reqData);
        };
        if(data.tableClass == 'envVars') {
            var dataContent = envVar;
        } else {
            dataContent = cronTiming;
        }
        var param = {
            "currentConfigName":data.currentConfigName,
            "data":dataContent
        };

        if (undefined === data.currentConfigName) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function delRow() {
        var data = getData(this);
        var action = 'delRow';
        var userFunction = function(reqData) {
            $('#message').html(reqData);
        };
        var param = {
                "currentConfigName":data.currentConfigName,
                "rowIndex":data.rowIndex

        };
        if (undefined === data.currentConfigName) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function changeRow() {
        var data = getData(this);
        var cronTiming = getCronTiming(this);
        var action = 'editRow';
        var userFunction = function(reqData) {
            $('#message').html(reqData);
        };
        var param = {
                "currentConfigName":data.currentConfigName,
                //"environmentId":data.environmentId,
                "rowIndex":data.rowIndex,
                "cronTiming":cronTiming

            };
        if (undefined === data.currentConfigName) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function saveFile() {

        var environVarsConfig = activeEnvironVars();
        var cronCommandsConfig = activeCronCommands();
        var action = 'saveFile';
        var userFunction = function(reqData) {
            $('#message').html(reqData);
        };
        var activeCurrentConfig = $('#accordionCurrent').find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig.find('#fileName').data('configName');
        var param = {
            "currentConfigName":currentConfigName,
            "environVarsConfig":environVarsConfig,
            "cronCommandsConfig":cronCommandsConfig
        };

        if (undefined === currentConfigName) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function addRowGroup() {
        var rowGroup = $.map($('.rowsGroup').filter(":checkbox:checked"),
                    function(el){
                        return $(el).data('rowIndex');
                    });
        var environmentId = $('#environmentList option:selected').data('id');
        var activeSourceConfig = $('#accordionSource').find("[aria-hidden='false']");
        var sourceConfigId = activeSourceConfig.find('#sourceFileName').data('configId');
        var action = 'addFullConfig';
        //alert(environmentId);
        var userFunction = function(reqData) {
            $('#message').html(reqData);
            //alert(reqData);
        };
        var param = {
            "sourceConfigId":sourceConfigId,
            "environmentId":environmentId,
            "rowGroup":rowGroup

        };
        if (undefined === sourceConfigId) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function cellClick() {
        if ($(this).data('type') == 'editable') {
            $(this).attr('contenteditable', 'true');
            $(this).focusout(function(){
                $(this).attr('contenteditable', 'false');
            });
        }
    }

    $("#accordionSource").accordion({
        active: false,
        collapsible:true
    });

    function getData(button) {
        var rowIndex = $('#'+button.id).data('rowIndex');
        var environmentId = $('#environmentList option:selected').data('id');
        var activeCurrentConfig = $('#accordionCurrent').find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig.find('#fileName').data('configName');
        var activeSourceConfig = $('#accordionSource').find("[aria-hidden='false']");
        var sourceConfigId = activeSourceConfig.find('#fileName').data('configId');
        var tableClass = $(button).data('tableClass');
        var object = {
            "rowIndex":rowIndex,
            "environmentId":environmentId,
            "currentConfigName":currentConfigName,
            "sourceConfigId":sourceConfigId,
            "tableClass":tableClass
        };
        return object;
    }

    function getCronTiming(button) {
        var row = $('#'+button.id)
            .parent()
            .parent()
            .parent();
        var minute = row
            .find(':nth-child(1)')
            .text()
            .trim();
        var hour = row
            .find(':nth-child(2)')
            .text()
            .trim();
        var day = row
            .find(':nth-child(3)')
            .text()
            .trim();
        var month = row
            .find(':nth-child(4)')
            .text()
            .trim();
        var weekday = row
            .find(':nth-child(5)')
            .text()
            .trim();
        var owner = row
            .find(':nth-child(6)')
            .text()
            .trim();
        var process = row
            .find(':nth-child(7)')
            .text()
            .trim();
        var command = row
            .find(':nth-child(8)')
            .text()
            .trim();

        var cronTiming = {
            "minute":minute,
            "hour":hour,
            "day":day,
            "month":month,
            "weekday":weekday,
            "owner":owner,
            "process":process,
            "command":command
        };

        return cronTiming;
    }

    function getEnvironmentVariables(button) {
        var row = $('#'+button.id)
            .parent()
            .parent()
            .parent();
        var envVar = row
            .find(':nth-child(1)')
            .text()
            .trim();
        var evironmentVars = {
            "envVar":envVar
        }
        return evironmentVars;
    }

    function sendData(action,param,func) {
        $.ajax({
            type: "POST",
            url: "index.php",
            data: {
                "action": action,
                "param": param
            },
            cache: false,
            success: function (data) {
                    func(data);
            }
        });
    }

});