var customfileInfo;
function formatCustom(fileInfo){
    console.log("custom.js accessed");
    // set the fileInfo to the global variable
    customfileInfo = fileInfo;
    // hide the buttons
    $("#verify-confirm").html('<h3>Processing as a custom file.</h3>');
    $("#verify-explain").show();
    $("#verify-explain").html(`
        Does each line represent a computer? <br>
        <button id="verify-single-line-yes" class="btn btn-outline-primary">Yes</button>
        <button id="verify-single-line-no" class="btn btn-outline-primary">No</button>
        <br /><br />
    `);
    $("#verify-explain").append(`
        Is there a column that represents a group or business unit? <br> 
        <button id="verify-group-yes" class="btn btn-outline-primary">Yes</button> 
        <button id="verify-group-no" class="btn btn-outline-primary">No</button>
        <br /><br />
    `);
    //$("#verify-table").show();
}

// global object to keep track of button clicks
var trackClicks = {
    singleLine: false,
    singleLineClicked: false,
    group: false,
    groupClicked: false,
    memory: false,
    memoryClicked: false
};

// make sure all buttons have been clicked and highlight the active one
function verifyClicks(btn){
    // ##### first check if a button needs to be active
    // split to the last dash
    var btnID = btn.split('-').pop();
    // split everything before the last dash
    var btnType = btn.split('-').slice(0, -1).join('-');
    // make this button active
    $('#'+btnType+'-'+btnID).addClass('active');
    // check to make sure the other is inactive
    if(btnID == 'yes'){
        $('#'+btnType+'-no').removeClass('active');
    }else{
        $('#'+btnType+'-yes').removeClass('active');
    }

    // #### now check if both have been clicked
    if(trackClicks.singleLineClicked && trackClicks.groupClicked){
        console.log(trackClicks);
        // change the class of the active button to btn-primary (so we can see what was selected)
        $('.active').removeClass('btn-outline-primary').addClass('btn-primary');
        // disable all the buttons that start with verify, keep active highlighted
        $('[id^="verify"]').prop('disabled', true);

        // show the table
        $("#verify-table").show();

        var requiredHtml = '';
        var optionalHtml = '';

        // account for the selections above
        if(!trackClicks.singleLine){
            requiredHtml += columnMatch(true, 'Computer Count', 'The column with the quantity of computers in a single row', customfileInfo);
        }
        if(trackClicks.group){
            requiredHtml += columnMatch(true, 'Group', 'The column with the group or business unit', customfileInfo);
        }
        
        // set up the required table info
        requiredHtml += columnMatch(true, 'CPU', 'The column with the CPU count', customfileInfo);
        requiredHtml += columnMatch(true, 'Memory', 'The column with memory', customfileInfo);
        requiredHtml += confirmOptions(true, 'Memory Type', 'Is the memory in GB or MB?', ['GB', 'MB'], 'MB');
        requiredHtml += columnMatch(true, 'Storage', 'The column with individual machine storage listed', customfileInfo, true);
        requiredHtml += confirmOptions(true, 'Storage Type', 'Is the storage in GB or MB?', ['GB', 'MB'], 'GB');
        
        // set up the optional table info
        optionalHtml += columnMatch(false, 'OS', 'Add this so we can match up if it\'s Windows or Linux. We\'ll assume Windows if this is not set.', customfileInfo);
        optionalHtml += columnMatch(false, 'VM Name', 'The original VM name column. Add this if you want to see it matched up on the output file.', customfileInfo);
    
        // append to the tbody in the table
        $("#verify-body-required").append(requiredHtml);

        // show this if the optionalHtml is not empty
        if(optionalHtml != ''){
            //append to the tbody in the table
            $("#verify-body-optional").append(optionalHtml);
        }
        
        // show dynamic tooltips
        showTooltip();
    }
}

// handle first question (is this a single line per computer)
$(document).on('click', '#verify-single-line-yes', function(){
    trackClicks.singleLine = true;
    trackClicks.singleLineClicked = true;
    verifyClicks(this.id);
});

$(document).on('click', '#verify-single-line-no', function(){
    trackClicks.singleLine = false;
    trackClicks.singleLineClicked = true;
    verifyClicks(this.id);
});

// handle second question (is there a group or business unit column)
$(document).on('click', '#verify-group-yes', function(){
    trackClicks.group = true;
    trackClicks.groupClicked = true;
    verifyClicks(this.id);
});

$(document).on('click', '#verify-group-no', function(){
    trackClicks.group = false;
    trackClicks.groupClicked = true;
    verifyClicks(this.id);
});

// handle third question (is the memory in GB or MB)
$(document).on('click', '#verify-memory-yes', function(){
    trackClicks.memory = true;
    trackClicks.memoryClicked = true;
    verifyClicks(this.id);
});

$(document).on('click', '#verify-memory-no', function(){
    trackClicks.memory = false;
    trackClicks.memoryClicked = true;
    verifyClicks(this.id);
});