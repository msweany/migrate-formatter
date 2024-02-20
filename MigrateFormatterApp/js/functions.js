function columnMatch(required, column, info, response, storage = false){
    // add a class to the row if it's required
    var className = required ? 'item-required' : 'item-optional';        
    var html = '<tr><td>'+column+' <i class="fa fa-info-circle show-tooltip" aria-hidden="true" data-bs-toggle="tooltip" title="'+info+'"></i></td>';
    // Create a select ID based on the column name, all lowercase and no spaces
    var selectID = column.toLowerCase().replace(' ', '_');
    html += '<td><select id="'+selectID+'" class="verify-select '+className+'" modified="false">';
        html += '<option>Select</option>';
        $.each(response.headers, function(key, value) {
            html += '<option value="' + key + '">' + value + '</option>';
        });
        if(storage){
            html += '<option value="storage_column">I don\'t have a storage column</option>';
        }
        html += '</select>';
    html += '</select></td></tr>';
    return html;
}

function showTooltip(){
    $('.show-tooltip').tooltip({
        trigger: 'hover'
    }).on('click', function () {
        $(this).tooltip('hide');
    });
}

// verify form
function verifyForm(){
    // count all item-required classes
    var requiredItems = $('.item-required').length;
    // count all item-optional classes
    var optionalItems = $('.item-optional').length;
    // count all item-required classes that have a value
    var requiredItemsWithValue = $('.item-required[modified="true"]').length;
    // if all required items have a value, enable the button
    if(requiredItems == requiredItemsWithValue){
        $("#verify-btn-continue").prop('disabled', false);
    } else {
        $("#verify-btn-continue").prop('disabled', true);
    }
}