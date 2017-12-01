'use strict';

var tdMMapsModule = {};

tdMMapsModule.itemSelector = {};
tdMMapsModule.itemSelector.items = {};
tdMMapsModule.itemSelector.baseId = 0;
tdMMapsModule.itemSelector.selectedId = 0;

tdMMapsModule.itemSelector.onLoad = function (baseId, selectedId)
{
    tdMMapsModule.itemSelector.baseId = baseId;
    tdMMapsModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#tdMMapsModuleObjectType').change(tdMMapsModule.itemSelector.onParamChanged);

    jQuery('#' + baseId + '_catidMain').change(tdMMapsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + '_catidsMain').change(tdMMapsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'Id').change(tdMMapsModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(tdMMapsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(tdMMapsModule.itemSelector.onParamChanged);
    jQuery('#tdMMapsModuleSearchGo').click(tdMMapsModule.itemSelector.onParamChanged);
    jQuery('#tdMMapsModuleSearchGo').keypress(tdMMapsModule.itemSelector.onParamChanged);

    tdMMapsModule.itemSelector.getItemList();
};

tdMMapsModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajaxIndicator').removeClass('hidden');

    tdMMapsModule.itemSelector.getItemList();
};

tdMMapsModule.itemSelector.getItemList = function ()
{
    var baseId;
    var params;

    baseId = tdMMapsModule.itemSelector.baseId;
    params = {
        ot: baseId,
        sort: jQuery('#' + baseId + 'Sort').val(),
        sortdir: jQuery('#' + baseId + 'SortDir').val(),
        q: jQuery('#' + baseId + 'SearchTerm').val()
    }
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params[catidMain] = jQuery('#' + baseId + '_catidMain').val();
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params[catidsMain] = jQuery('#' + baseId + '_catidsMain').val();
    }

    jQuery.getJSON(Routing.generate('tdmmapsmodule_ajax_getitemlistfinder'), params, function( data ) {
        var baseId;

        baseId = tdMMapsModule.itemSelector.baseId;
        tdMMapsModule.itemSelector.items[baseId] = data;
        jQuery('#ajaxIndicator').addClass('hidden');
        tdMMapsModule.itemSelector.updateItemDropdownEntries();
        tdMMapsModule.itemSelector.updatePreview();
    });
};

tdMMapsModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = tdMMapsModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = tdMMapsModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.get(0).options[i] = new Option(item.title, item.id, false);
    }

    if (tdMMapsModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(tdMMapsModule.itemSelector.selectedId);
    }
};

tdMMapsModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = tdMMapsModule.itemSelector.baseId;
    items = tdMMapsModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (tdMMapsModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id == tdMMapsModule.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (null !== selectedElement) {
        jQuery('#' + baseId + 'PreviewContainer')
            .html(window.atob(selectedElement.previewInfo))
            .removeClass('hidden');
        tdMMapsInitImageViewer();
    }
};

tdMMapsModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = tdMMapsModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id').get(0);
    preview = window.atob(tdMMapsModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    tdMMapsModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
    tdMMapsInitImageViewer();
};

jQuery(document).ready(function() {
    var infoElem;

    infoElem = jQuery('#itemSelectorInfo');
    if (infoElem.length == 0) {
        return;
    }

    tdMMapsModule.itemSelector.onLoad(infoElem.data('base-id'), infoElem.data('selected-id'));
});
