$(document).ready(function(){

    $("#accordionSource").accordion({
        active: false,
        collapsible:true
    });
    $('#createFile').bind('click', createNewFile);
    //$('.addFullConfig').bind('click', addRowGroup);
    $('.addRowButton').bind('click', addRow);
    $('#environmentList').bind('change', changeEnvList);

});


    function changeEnvList()
    {

        var userFunction = function(response){

            if( $('#environmentList').children(':selected').data('id') == 0 ) {
                $('#createFile').prop("disabled", "disabled");
            } else {
                $('#createFile').prop("disabled", false);
            }

            var initAccordionParam = {
                'active':true,
                'collapsible': true,
                'heightStyle': "content"
            };

            reInitAccordion(
                '#accordionCurrent',
                response,
                initAccordionParam
            );

            $('.saveButton').bind('click', saveFile);
            $('.deleteButton').bind('click', deleteConfig);
            //$('.delRowButton').bind('click', delRow);
            $('td').bind('click', cellClick);
        };
        var action = 'getCurrentConfig';
        var param = {};
        sendData(action, param, userFunction);
    }

    function reInitAccordion(accordionId, response, initParams)
    {
        if( $(accordionId).accordion('instance') ) {
            $(accordionId).accordion('destroy');
        }
        $(accordionId)
            .empty()
            .append(response)
            .accordion(initParams);
    }

    function activeAccordionContent(tableClass)
    {
        var tableCronCommands = $('#accordionCurrent')
            .find('[aria-hidden="false"]')
            .find('.'+tableClass);

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

    function addRow()
    {
        var data = getData(this);
        var envVar = getEnvironmentVariables(this)
        var cronTiming = getCronTiming(this);
        var action = 'addRow';

        var userFunction = function(response){
            var table = $('#accordionCurrent')
                .find('[aria-hidden="false"]')
                .find('.'+data.tableClass);
            var emptyRow = table.children().children('[data-new-row="newEmptyRow"]');

            if ( emptyRow.length != 0 ) {
                emptyRow.before(response);
            } else {
                table.append(response);
            }
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
/*
    function delRow()
    {
        var data = getData(this);
        var action = 'delRow';
        var userFunction = function(response) {
            $('#message').html(response);
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

    function changeRow()
    {
        var data = getData(this);
        var cronTiming = getCronTiming(this);
        var action = 'editRow';
        var userFunction = function(response) {
            $('#message').html(response);
        };
        var param = {
            "currentConfigName":data.currentConfigName,
            "environVars":evironVars
            "cronTiming":cronTiming

        };
        if (undefined === data.currentConfigName) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }
*/

    function saveFile()
    {

        var environVarsConfig = activeAccordionContent('envVars');
        var cronCommandsConfig = activeAccordionContent('cronCommands');

        var action = 'saveFile';
        var userFunction = function(response) {
            $('#message').html(response);
        };
        var activeCurrentConfig = $('#accordionCurrent').find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig.find('#fileName').data('configName');
        if (currentConfigName == "new file") {
            if( $('#fileName').val() ) {
                currentConfigName = $('#fileName').val();
            } else {
                alert('Имя файла не задано!');
                    return;
            }

        }
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

    function createNewFile()
    {


        var action = 'createNewFile';
        var param = {};

        var userFunction = function(response){

            if( $('#accordionCurrent').accordion('instance') ) {
                $('#accordionCurrent').accordion('destroy');
                var data = $('#accordionCurrent').html();
                response += data;
            }

            var  initAccordionParam = {
                'active': 0,
                'collapsible':true,
                'heightStyle': "content"
            };

            reInitAccordion('#accordionCurrent', response, initAccordionParam);

            $('.saveButton').bind('click', saveFile);
            $('.deleteButton').bind('click', deleteConfig);
            if( $('#accordionCurrent').find('h3').filter('[data-new="true"]').length != 0 ){
                $('#createFile').prop('disabled', true);
            }
        };

        sendData(action, param, userFunction);
    }

    function deleteConfig()
    {
        var action = 'removeConfig';
        var activeCurrentConfig = $('#accordionCurrent').find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig.find('#fileName').data('configName');
        var param = {
            "currentConfigName":currentConfigName
        };
        var userFunction = function(response) {
            $('#message').html(response);
        };
        sendData(action, param, userFunction);
    }
/*
    function addRowGroup()
    {
        var rowGroup = $.map($('.rowsGroup').filter(":checkbox:checked"),
            function(el){
                return $(el).data('rowIndex');
            });
        var environmentId = $('#environmentList option:selected').data('id');
        var activeSourceConfig = $('#accordionSource').find("[aria-hidden='false']");
        var sourceConfigId = activeSourceConfig.find('#sourceFileName').data('configId');
        var action = 'addFullConfig';
        //alert(environmentId);
        var userFunction = function(response) {
            $('#message').html(response);
            //alert(response);
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
*/
    function cellClick()
    {
        if ($(this).data('type') == 'editable') {



            $(this).prop('contenteditable', 'true').focus();
            $(this).parent().css('background-color', '#7eacd7');
            var tdContent = $(this).text();
            //console.log(tr);
            $(this).focusout(function(){
                $(this).prop('contenteditable', 'false');
                $(this).parent().css('background-color', '#D8E6F3');
                if (tdContent.localeCompare( $(this).text() ) != 0 ) {
                    $(this).parent().css('background-color', 'green');
                }
            });
        }
    }



    function getData(button)
    {
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

    function getCronTiming(button)
    {
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
        var command = row
            .find(':nth-child(7)')
            .text()
            .trim();

        var cronTiming = {
            "minute":minute,
            "hour":hour,
            "day":day,
            "month":month,
            "weekday":weekday,
            "owner":owner,
            "command":command
        };

        return cronTiming;
    }

    function getEnvironmentVariables( button )
    {
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

    function sendData( action, param, func )
    {
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