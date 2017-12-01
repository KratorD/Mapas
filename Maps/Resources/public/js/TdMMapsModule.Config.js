'use strict';

function mapsToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('tdmmapsmodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#tdmmapsmodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function() {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            mapsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        mapsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
