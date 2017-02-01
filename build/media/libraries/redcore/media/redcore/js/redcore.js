/**
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Only define the redCORE namespace if not defined.
redCORE = window.redCORE || {};

/**
 * Method to get the event target and optionally target the parent of an icon element
 */
redCORE.getTarget = function(event, clearIcon)
{
    // IE or every other browser
    var targ = event.target || event.srcElement;

    if (clearIcon && targ.tagName == 'I')
    {
        targ = targ.parentElement;
    }

    return targ;
};

redCORE.ws =
{
    init:function()
    {
        jQuery('body')
            .on('click', '.fields-add-new-row', redCORE.ws.addNewRow)
            .on('click', '.fields-remove-row', redCORE.ws.removeRow)
            .on('click', '.ws-data-mode-switch input', redCORE.ws.toggleDataMode)
            .on('click', '.ws-validate-data-switch input', redCORE.ws.toggleValidateDataSwitch)
            .on('click', '.ws-documentationSource-switch input', redCORE.ws.toggleDocumentSource)
            .on('click', '.ws-use-forward-switch input', redCORE.ws.toggleForwardSwitch)
            .on('click', '.ws-isEnabled-trigger input', redCORE.ws.toggleEnabled)
            .on('click', '.fields-add-new-task', redCORE.ws.addNewTask)
            .on('click', '.fields-add-new-type',redCORE.ws.addNewType)
            .on('click', '.fields-edit-row', redCORE.ws.toggleEditRow)
            .on('click', '.fields-apply-row', redCORE.ws.toggleEditRow);
    },

    addNewTask: function(event)
    {
        event.preventDefault();
        var targ = jQuery(redCORE.getTarget(event, true));

        var getData = {};
        getData.taskName = targ.parents('.fields-add-new-task-row').find('[name="newTask"]').val().replace(/[^\w]/g,'');

        if (getData.taskName == '')
        {
            var msg = targ.attr('data-no-task-msg');
            alert(msg);

            return false;
        }

        jQuery.ajax({
                url: 'index.php?option=com_redcore&task=webservice.ajaxGetTask',
                data: getData,
                dataType: 'text',
                beforeSend: function ()
                {
                    targ.parents('#webserviceTabTask').addClass('opacity-40');
                }
        }).done(function (data){
            targ.parents('#webserviceTabTask').removeClass('opacity-40')
                .find('.tab-content:first').prepend(data);

            redCORE.ws.addTabLink('#taskTabs', getData.taskName, 'task');

            jQuery('select').chosen();
            jQuery('.hasTooltip').tooltip();
            rRadioGroupButtonsSet('#operationTabtask-' + getData.taskName);
            rRadioGroupButtonsEvent('#operationTabtask-' + getData.taskName);
            jQuery('#operationTabtask-' + getData.taskName + ' :input[checked="checked"]').click();
        });
    },

    addTabLink: function (id, tabIdFragment, prefix)
    {
        var html = '<li><a href="#operationTab' + prefix + '-'
                    + tabIdFragment + '" id="operation-' + prefix + '-'
                    + tabIdFragment + '-tab" data-toggle="tab">' + prefix + '-'
                    + tabIdFragment + '</a></li>';

        jQuery(id).prepend(html).find('li a:first').click();

    },

    addNewType: function(event)
    {
        event.preventDefault();
        var targ = jQuery(redCORE.getTarget(event, true));

        var getData = {};
        getData.typeName = targ.parents('.fields-add-new-type-row').find('[name="newType"]').val().replace(/[^\w]/g,'');

        if (getData.typeName == '')
        {
            var msg = targ.attr('data-no-type-msg');
            alert(msg);

            return false;
        }

        jQuery.ajax({
            url: 'index.php?option=com_redcore&task=webservice.ajaxAddComplexType',
            data: getData,
            dataType: 'text',
            beforeSend: function ()
            {
                targ.parents('#webserviceTabComplexTypes').addClass('opacity-40');
            }
        }).done(function (data){
            targ.parents('#webserviceTabComplexTypes').removeClass('opacity-40')
                .find('.tab-content:first').prepend(data);

            redCORE.ws.addTabLink('#typeTabs', getData.typeName, 'type');
            jQuery('select').chosen();
            jQuery('.hasTooltip').tooltip();
            rRadioGroupButtonsSet('#operationTabtype-' + getData.typeName);
            rRadioGroupButtonsEvent('#operationTabtype-' + getData.typeName);
            jQuery('#operationTabtype-' + getData.typeName + ' :input[checked="checked"]').click();
        });
    },

    addNewRow: function(event)
    {
        event.preventDefault();
        var targ = jQuery(redCORE.getTarget(event, true));
        var id = targ.closest('form').find('input[name="id"]');

        var getData = {};
        getData.operation = targ.find('[name="addNewRowOperation"]').val();
        getData.fieldList = targ.find('[name="addNewRowList"]').val();

        var rowType = targ.find('[name="addNewRowType"]').val();
        var optionType = targ.find('[name="addNewOptionType"]').val();

        if (typeof optionType == 'undefined')
        {
            optionType = rowType;
        }

        if (optionType == 'FieldFromDatabase' || optionType == 'ResourceFromDatabase')
        {
            getData['tableName'] = targ.parents('.form-inline:first').find('[name="jform[main][addFromDatabase]"]').val();
        }
        else if (optionType == 'ConnectWebservice')
        {
            getData['webserviceId'] = targ.parents('.form-inline:first').find('[name="jform[main][connectWebservice]"]').val();
        }

        jQuery.ajax({
            url: 'index.php?option=com_redcore&task=webservice.ajaxGet' + optionType  + '&id=' + id.val(),
            data: getData,
            dataType: 'text',
            beforeSend: function ()
            {
                targ.parents('fieldset:first').addClass('opacity-40');
            }
        }).done(function(data)
        {
            targ.parents('fieldset:first').removeClass('opacity-40')
                .find('.ws-row-list').prepend(data)
                .find('.fields-edit-row:first').click();
            jQuery('select').chosen();
            jQuery('.hasTooltip').tooltip();
            rRadioGroupButtonsSet('.ws-' + rowType + '-' + getData['operation']);
            rRadioGroupButtonsEvent('.ws-' + rowType + '-' + getData['operation']);
        })
    },

    removeRow:function(event)
    {
        event.preventDefault();
        var targ = jQuery(redCORE.getTarget(event, true));
        targ.parents('.row-stripped').remove();
    },

    toggleEditRow: function(event)
    {
        event.preventDefault();
        var targ = jQuery(redCORE.getTarget(event, true));
        var parent = targ.parents('.row-stripped');

        var editRow = parent.find('.ws-row-edit');
        var displayRow = parent.find('.ws-row-display');

        if (editRow.css('display') == 'none')
        {
            editRow.show();
            displayRow.hide();

            return true;
        }

        var rowValues = {};

        parent.find('.ws-row-edit :input')
            .each(function()
            {
                var input = jQuery(this);
                var name = input.attr('name');

                if ((!input.is(':radio') || input.prop('checked')) && typeof name !== typeof undefined && name !== false)
                {
                    if (input.is(':radio')){
                        name = name.split('_');
                        name = name[1];
                    }

                    var displayCell = parent.find('.ws-row-display-cell-' + name);
                        displayCell.html(input.val());
                        displayCell.parent().show();
                    rowValues[name] = input.val();
                }
            });

        parent.find('.ws-row-original').val(JSON.stringify(rowValues));
        editRow.hide();
        displayRow.show();
    },

    toggleEnabled: function(event)
    {
        var targ = jQuery(redCORE.getTarget(event, true));
        var parent = targ.parents('.ws-operation-configuration');
        parent.prop('disabled', targ.val() == 0)
            .find('chzn-done').prop('disabled', targ.val() == 0)
            .trigger('liszt:updated');
    },

    toggleForwardSwitch:function(event)
    {
        var targ = jQuery(redCORE.getTarget(event, true));

        if(targ.val() != '')
        {
            targ.parents('.ws-params').find('.ws-use-operation-fieldset').hide();

            return true;
        }

        targ.parents('.ws-params').find('.ws-use-operation-fieldset').show();
    },

    toggleDocumentSource: function(event)
    {
        var targ = jQuery(redCORE.getTarget(event, true));
        var dataMode = targ.val();
        var currentTab = targ.parents('.ws-params');

        redCORE.ws.toggleByDataMode(currentTab, '.ws-documentationSource', dataMode);
    },

    toggleValidateDataSwitch:function(event)
    {
        var targ = jQuery(redCORE.getTarget(event, true));
        var dataMode = targ.val();
        var currentTab = targ.parents('.ws-params');

        redCORE.ws.toggleByDataMode(currentTab, '.ws-validateData', dataMode);
    },

    toggleDataMode: function(event)
    {
        var targ = jQuery(redCORE.getTarget(event, true));
        var dataMode = targ.val();
        var currentTab = targ.parents('.ws-params');

        redCORE.ws.toggleByDataMode(currentTab, '.ws-dataMode', dataMode);
    },

    toggleByDataMode:function(currentTab, targetClass,dataMode)
    {
        currentTab.find(targetClass).hide();
        currentTab.find(targetClass + '-' + dataMode).show();
    }
};
