$(document).ready(function(){

    $("#accordionSource").accordion({
        active: false,
        collapsible:true
    });
    $('#createFile').bind('click', createNewFile);
    $('.addRowButton').bind('click', addRow);
    $('#environmentList').bind('change', changeEnvList);

});


    function changeEnvList()
    {

        var userFunction = function(response){

            if( $('#environmentList').children(':selected').data('id') == 0 )
            {
                $('#createFile').prop("disabled", "disabled");
            }
            else
            {
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
            $('.delRowButton').bind('click', delRow);
            $('td').bind('click', cellClick);
        };
        var action = 'getCurrentConfig';
        var param = {};
        sendData(action, param, userFunction);
    }

    function reInitAccordion(accordionId, response, initParams)
    {
        if($(accordionId).accordion('instance'))
        {
            $(accordionId).accordion('destroy');
        }
        $(accordionId)
            .empty()
            .append(response)
            .accordion(initParams)
            .find('.cronCommands')
            .children()
            .children('[data-flag="inactiveCronCommand"]')
            .css('background-color', 'grey');

    }

    function activeAccordionContent(tableClass)
    {
         var rowData = {};
         $('#accordionCurrent')
             .find('[aria-hidden="false"]')
             .find('.'+tableClass)
             .children()
             .children('[data-type="tableContent"]')
             .not('[data-delete="true"]')
             .each(function(i) {
             rowData[i] =  $.map(
                    $(this).children('[data-type="editable"]'),
                    function(a) {
                        return $(a).text().trim();
                    });
         });

        return rowData;
    }

    function addRow()
    {
        var data = getData(this);
        var envVar = getEnvironmentVariables(this)
        var cronTiming = getCronTiming(this);
        var action = 'addRow';
        var userFunction = function(response)
        {
            var table = $('#accordionCurrent')
                .find('[aria-hidden="false"]')
                .find('.'+data.tableClass);
            var emptyRow = table
                .children()
                .children('[data-new-row="newEmptyRow"]');

            if ( emptyRow.length != 0 )
            {
                emptyRow.before(response);
            }
            else
            {
                table.append(response);
            }
        };

        if (data.tableClass == 'envVars')
        {
            var dataContent = envVar;
        }
        else
        {
            dataContent = cronTiming;
        }

        var param = {
            "currentConfigName":data.currentConfigName,
            "data":dataContent
        };

        if (undefined === data.currentConfigName)
        {
            alert('Файл не определен!');
        }
        else
        {
            sendData(action, param, userFunction);
            //$('td').bind('click', cellClick);
        }


    }

    function delRow()
    {

        $(this)
            .closest('tr')
            .attr('data-delete', 'true')
            .css('background-color', 'grey')
            .prop('disabled', 'disabled');
    }


    function saveFile()
    {

        var environVarsConfig = activeAccordionContent('envVars');
        var cronCommandsConfig = activeAccordionContent('cronCommands');
        var activeCurrentConfig = $('#accordionCurrent').find("[aria-hidden='false']");

        var currentConfigName = activeCurrentConfig.find('#fileName').data('configName');

        var action = 'saveFile';

        var userFunction = function(response)
        {
            $('#message').html(response);
        };

        if (currentConfigName == "newFile")
        {
            if ($('#fileName').val())
            {
                currentConfigName = $('#fileName').val();
            }
            else
            {
                alert('Имя файла не задано!');
                return;
            }
        }

        var param = {
            "currentConfigName":currentConfigName,
            "environVarsConfig":environVarsConfig,
            "cronCommandsConfig":cronCommandsConfig
        };

        if (undefined === currentConfigName)
        {
            alert('Файл не определен!');
        }
        else
        {
            sendData(action, param, userFunction);
        }

    }

    function createNewFile()
    {
        var action = 'createNewFile';
        var param = {};
        var userFunction = function(response)
        {
            var  initAccordionParam = {
                'active': 0,
                'collapsible':true,
                'heightStyle': "content"
            };

            if ($('#accordionCurrent').accordion('instance'))
            {
                $('#accordionCurrent').accordion('destroy');
                var data = $('#accordionCurrent').html();
                response += data;
            }

            reInitAccordion('#accordionCurrent', response, initAccordionParam);

            $('.saveButton').bind('click', saveFile);
            $('.deleteButton').bind('click', deleteConfig);

            if ($('#accordionCurrent').find('h3').filter('[data-new="true"]').length != 0)
            {
                $('#createFile').prop('disabled', true);
            }
        };

        sendData(action, param, userFunction);
    }

    function deleteConfig()
    {
        var action = 'removeConfig';
        var activeCurrentConfig = $('#accordionCurrent')
            .find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig
            .find('#fileName')
            .data('configName');
        var param = {
            "currentConfigName":currentConfigName
        };
        var userFunction = function(response)
        {
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
        if ($(this).data('type') == 'editable')
        {
            $(this)
                .prop('contenteditable', 'true')
                .focus();
            $(this)
                .parent()
                .css('background-color', '#7eacd7');
            var tdContent = $(this)
                .text();
            $(this).focusout(function(){
                $(this)
                    .prop('contenteditable', 'false');
                $(this)
                    .parent()
                    .css('background-color', '#D8E6F3');
                if (tdContent.localeCompare( $(this).text() ) != 0)
                {
                    $(this)
                        .parent()
                        .css('background-color', 'green');
                }
            });
        }
    }

    function getData(button)
    {
        var rowIndex = $('#'+button.id)
            .data('rowIndex');
        var environmentId = $('#environmentList option:selected')
            .data('id');
        var activeCurrentConfig = $('#accordionCurrent')
            .find("[aria-hidden='false']");
        var activeSourceConfig = $('#accordionSource')
            .find("[aria-hidden='false']");
        var currentConfigName = activeCurrentConfig
            .find('#fileName')
            .data('configName');
        var sourceConfigId = activeSourceConfig
            .find('#fileName')
            .data('configId');
        var tableClass = $(button)
            .data('tableClass');
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
        var tdText = [];
        $('#'+button.id)
            .closest('tr')
            .children('[data-type="content"]')
            .each(function(i) {
                tdText[i] = $(this)
                    .text()
                    .trim();
            });

        var cronTiming = {
            "minute":tdText[0],
            "hour":tdText[1],
            "day":tdText[2],
            "month":tdText[3],
            "weekday":tdText[4],
            "owner":tdText[5],
            "command":tdText[6]
        };

        return cronTiming;
    }

    function getEnvironmentVariables(button)
    {
        return {
            "envVar":
                $('#'+button.id)
                    .closest('tr')
                    .find(':first-child')
                    .text()
                    .trim()
        };
    }

    function sendData(action, param, func)
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