$(document).ready(function(){



    $('#environmentList').bind('change', function() {
        var action = 'getCurrentConfig';
        var environmentId = $('#environmentList option:selected').data('id');
        var userFunction = function(reqData){
            $('#cronConfig').html(reqData);
            $("#accordionCurrent").accordion({
                active: false,
                collapsible:true
            });
            $('.delRowButton').bind('click', delRow);
            $('.changeRowButton').bind('click', changeRow);
            $('td').bind('click', cellClick);
        };
        var param = {
            "environmentId":environmentId

        };
        sendData(action, param, userFunction);
    });



    $('.addFullConfig').bind('click', addRowGroup);
    $('.addRowButton').bind('click', addRow);

    function addRow() {
        var data = getData(this);
        var cronTiming = getCronTiming(this);
        var action = 'addRowConfig';
        var userFunction = function(reqData) {
            alert(reqData);
        };
        var param = {
            "configId":data.configId,
            "environmentId":data.environmentId,
            "cronTiming":cronTiming

        };
        if (undefined === data.configId) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function delRow() {
        var data = getData(this);
        var action = 'deleteRowConfig';
        var userFunction = function(reqData) {
            alert(reqData);
        };
        var param = {
                "configId":data.configId,
                "environmentId":data.environmentId,
                "rowIndex":data.rowIndex

        };
        if (undefined === data.configId) {
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
            alert(reqData);
        };
        var param = {
                "configId":data.configId,
                "environmentId":data.environmentId,
                "rowIndex":data.rowIndex,
                "cronTiming":cronTiming

            };
        if (undefined === data.configId) {
            alert('Файл не определен!');
        } else {
            sendData(action, param, userFunction);
        }
    }

    function addRowGroup() {
        //var data = getData(this);
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
            alert(reqData);
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
        var currentConfigId = activeCurrentConfig.find('#fileName').data('configId');
        var activeSourceConfig = $('#accordionSource').find("[aria-hidden='false']");
        var sourceConfigId = activeSourceConfig.find('#fileName').data('configId');
        var object = {
            "rowIndex":rowIndex,
            "environmentId":environmentId,
            "configId":currentConfigId,
            "sourceConfigId":sourceConfigId
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