

function login_success(username){
    $("#pWelcome").show();
    $("#pWelcome").text("Logged in as: " + username);
    $("#divLogin").hide();
    $("#divRegister").hide();
    $("#divLogout").show();
    $("#divEventList").show();
    $("#divEventAdd").show();
    $("#divEventEdit").show();
    //$("#divEventTags").show();
    $("#divGroups").show();

    logged_in = true;
    window.disDate = new Date();
    window.months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    window.dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);

    let disMonth = disDate.getMonth();
    console.log("disMonth "+disMonth);
    //Need to display all of users events
    //Temporarily hard coded

    

    user_events(
        [["0","0"], disDate.getMonth()+1],
        disEventsList
    );

    //Also need to display all of users groups
    display_groups(
        [],
        disGroups
    )
}

function disGroups(data){
    //Massive render function

    $("#allGroupsWrapper").html(' <p id="pAllGroups"></p>');
    $("#userInGroupsWrapper").html(' <p id="pUserInGroups"></p>');
    $("#userOwnsGroupsWrapper").html(' <p id="pUserOwnsGroups"></p>');

    //First need to clear all elements,
    //then we re-append to them

    $("#pAllGroups").text("All Groups");
    $("#pUserInGroups").text('Groups You Are In');
    $("#pUserOwnsGroups").text('Groups You Own');

    let user_groups_map = data.users_groups;
    let user_groups_array = [];

    for (i in user_groups_map){
        let group= user_groups_map[i];

        //If user isn't in the group we want to bind the button to joining
        //If user is already in the group then the button should get bound to leaving

        let div_id = "group_u_"+group.group_id;
        $("#userInGroupsWrapper").append(
            "<div id="+div_id+"> <p>"+group.group_name+"</p></div>"
        )
        user_groups_array.push(group);
    }
    $("#userInGroupsWrapper").append(
        "<hr>"
    )

    let all_groups_map = data.all_groups;

    console.log("ALL GROUPS");
    console.log(all_groups_map)
    console.log("USER GROUPS");
    console.log(user_groups_map);

    //https://api.jquery.com/jquery.grep/
    let difference = $.grep(all_groups_map, (item) => {
        console.log(item);
        let isPresent = false;

        for (i = 0; i< user_groups_array.length; i++){
            console.log(user_groups_array[i].group_id);
            console.log(item.group_id);

            if (user_groups_array[i].group_id == item.group_id){
               isPresent = true;
            }
        }
        return !isPresent;

        //This language is such a piece of shit
        //It took me 3 hours to do this part
        //because object equality is completely fucked
        }   
    );

      console.log(difference);

    for (i in all_groups_map){
        let group= all_groups_map[i];

        //If user isn't in the group we want to bind the button to joining
        //If user is already in the group then the button should get bound to leaving
        //Need to somehow be able to tell which groups the user ISNT in
        //tried to use difference, map stuff, fucking dynamic typing makes this way
        //more complicated than it has to be

        //Note: Sometimes you have to double click to leave or join

        let div_id = "group_all_"+group.group_id;

        if (!difference.includes(group)){

            let button_id = "leave_"+group.group_id;
            $("#allGroupsWrapper").append(
                "<div id="+div_id+"> <p>"+group.group_name+"</p><button id="+button_id+"> - </button></div>"
            )

            //Note: Currently you need to click - twice for it to work???

            $("#"+button_id).click(
                () => {
                    console.log('in leave');
                    remove_user_from_group(
                        ["",group.group_id],
                        function () {} //For some reason it doesn't re-display after adding
                    ); 
                    //It should just re-display the groups if a user joins a group
                    display_groups(
                        [],
                        disGroups
                    );
                }
            )
        }
        else {
            
            let button_id = "join_"+group.group_id;

            $("#allGroupsWrapper").append(
                "<div id="+div_id+"> <p>"+group.group_name+"</p><button id="+button_id+"> + </button></div>"
            )
    
            $("#"+button_id).click(
                () => {
                    console.log('in join');
                    add_user_to_group(
                        ["",group.group_id],
                        function () {} //This function doesn't really matter
                    ); 

                    //It should just re-display the groups if a user joins a group
                    display_groups(
                        [],
                        disGroups
                    );
                }
            )
        }
       
    }
    $("#allGroupsWrapper").append(
        "<hr>"
    )


    let user_owns_groups_map = data.user_owns_groups;
    
    $("#userOwnsGroupsWrapper").append("<button id='create_group'> Create </button>");

    $("#create_group").click(
        () => {
            addGroupForm();
        }
    )

    for (i in user_owns_groups_map){
        let group= user_owns_groups_map[i];

        //We need to give user option to edit all groups which they own

        let div_id = "group_"+group.group_id;
        $("#userOwnsGroupsWrapper").append(
            "<div id="+div_id+"> <p>"+group.group_name+"</p><button id='edit_"+group.group_id+"'> Edit </button><button id='delete_"+group.group_id+"'> Delete </button></div>"
        )

        $("#edit_"+group.group_id).click(
            () => {
                editGroupForm(group.group_id);
            }
        )

        $("#delete_"+group.group_id).click(
            () => {
                console.log('made it in delete group');
                delete_group(
                    [group.group_id],
                    disDeleteGroup
                )
            }
        )
    }
    $("#userOwnsGroupsWrapper").append(
        "<hr>"
    )



}

function addGroupForm(){
    $('#divCEDGroups').empty();

    $header = $("<h4></h4>").text("Add Group");
    $labelTitle = $("<label></label>").text("Name: ");

    $labelDescription = $("<label></label>").text("Description: ");
   
    $inputTitle = $("<input></input>");
    $inputTitle.attr("type", "text");
    $inputTitle.attr("id", "inGroupAddTitle");

    

    $inputDescription = $("<input></input>");
    $inputDescription.attr("type", "text");
    $inputDescription.attr("id", "inGroupAddDescription");


    $button = $("<input></input>");
    $button.attr("id", "btnGroupCreateSubmit");
    $button.attr("type", "submit");
    $button.attr("value", "Create");



    $("#divCEDGroups").append($header, $labelTitle, $inputTitle, $("<br>"), $labelDescription, $inputDescription, $("<br>"), $button); 


    $("#btnGroupCreateSubmit").click(
        () =>{
            create_group(
                [$("#inGroupAddTitle").val(),$("#inGroupAddDescription").val()],
                disCreateGroup
            )
        }
    );
    

 

}

function editGroupForm(group_id){
    //This will let you edit information about the group, and also add / delete / edit group events

    $('#divCEDGroups').empty();

    $header = $("<h4></h4>").text("Edit Group");
    $labelTitle = $("<label></label>").text("Name: ");

    $labelDescription = $("<label></label>").text("Description: ");
   
    $inputTitle = $("<input></input>");
    $inputTitle.attr("type", "text");
    $inputTitle.attr("id", "inGroupEditTitle");



    $inputDescription = $("<input></input>");
    $inputDescription.attr("type", "text");
    $inputDescription.attr("id", "inGroupEditDescription");


    $buttonEdit = $("<input></input>");
    $buttonEdit.attr("id", "btnGroupEditSubmit");
    $buttonEdit.attr("type", "submit");
    $buttonEdit.attr("value", "Edit");

    $button = $("<input></input>");
    $button.attr("id", "btnShowManageGroupEvents");
    $button.attr("type", "submit");
    $button.attr("value", "Manage Group Events");



    $("#divCEDGroups").append($header, $labelTitle, $inputTitle, $("<br>"), $labelDescription, $inputDescription, $("<br>"), $buttonEdit, $("<br>"), $button, $("<hr>")); 


    $("#btnGroupEditSubmit").click(
        () =>{
            let description = "Some temporary description";
            edit_group(
                [group_id,$("#inGroupEditTitle").val(),description],
                disCreateGroup //Doesn't really matter what render function we run as long as it re-renders all groups
            )
        }
    );

    $("#btnShowManageGroupEvents").click(
        () =>{
            //[range, month_num, group_id]
            //Temporarily hard coded to 3
            //Hard coded to 2
            //Note, no group events in 3 yet
            group_events(
               
                [["0","0"],disDate.getMonth()+1,group_id],
                disGroupEventsList
            )

            groupEventAddForm(group_id);
        }
    );

 

}

function groupEventAddForm(group_id){
    $("#divCEDGroups").append($("<div id = 'groupEventAdd'></div>"));

    $event_add = $("#divEventAdd").clone(); //Making a copy of the hard coded event add in calendar.html
    //$event_add("h4").replaceAll("<h4> Add Group Event </h4>");

    //console.log("element" +$event_add.children()[1]);

    //Switching out its contents so that it'll work for group



    //console.log(document.createTextNode('<input type="text" id="inGroupEventAddTitle">'));
   // $event_add.children()[1] = $('<input type="text" id="inGroupEventAddTitle">');


    //Not in the mood to figure out how to do this in a jquery object
    //Have fun reading this later Anton
    
    
    $("#groupEventAdd").append('<h4>Add Group Event</h4>Title*: <input type="text" id="inGroupEventAddTitle"/><br/>Date*: <input type="date" id="inGroupEventAddDate"/><br/>Time*: <input type="time" id="inGroupEventAddTime"/><br/>Tag: <br/><input type="radio" name="groupRadioAddTag" id="inGroupRadioAddTagP" value="personal"> Personal<input type="radio" name="groupRadioAddTag" id="groupRadioAddTagA" value="academic"> Academic <input type="radio" name="groupRadioAddTag" id="groupRadioAddTagW" value="work"> Work <input type="radio" name="groupRadioAddTag" id="groupRadioAddTagH" value="holiday"> Holiday <input type="radio" name="groupRadioAddTag" id="groupRadioAddTagB" value="birthday"> Birthday <br/><input type="submit" id="btnGroupEventAddSubmit" value="Add"/>');


    $("#btnGroupEventAddSubmit").click(
        () => { 
            //$("input[name='radioAddTag']:checked").val()
            //^Gets the tag input, later expand eventaddsubmit to work with tags also (change php)
            let tag = null;
            let radioValue = $("input[name='groupRadioAddTag']:checked").val();
            if(radioValue){
                tag = radioValue; 
            }
            
            //console.log("Tag "+tag);
            create_group_event( 
                [$("#inGroupEventAddTitle").val(), "Some Description", $("#inGroupEventAddDate").val(), $("#inGroupEventAddTime").val(), group_id, tag],
                create_event_success
                );

                $("#divCEDGroups").empty();

                group_events( //Re-render the events list after making an edit to the group events
                    [["0","0"],disDate.getMonth()+1, group_id],
                    disGroupEventsList
                )
        }

        );
    //$('#groupEventAdd').append($event_add);
}

function groupEventEditForm(group_event_id, group_id){
    
    $("#groupEventAdd").empty();
    $("#divCEDGroups").append($("<div id = 'groupEventEdit'></div>"));
    $("#groupEventEdit").append('<h4>Edit Group Event</h4>Title*: <input type="text" id="inGroupEventEditTitle"/><br/>Date*: <input type="date" id="inGroupEventEditDate"/><br/>Time*: <input type="time" id="inGroupEventEditTime"/><br/>Tag: <br/><input type="radio" name="groupRadioEditTag" id="inGroupRadioAddTagP" value="personal"> Personal<input type="radio" name="groupRadioEditTag" id="groupRadioAddTagA" value="academic"> Academic <input type="radio" name="groupRadioEditTag" id="groupRadioAddTagW" value="work"> Work <input type="radio" name="groupRadioEditTag" id="groupRadioAddTagH" value="holiday"> Holiday <input type="radio" name="groupRadioEditTag" id="groupRadioAddTagB" value="birthday"> Birthday <br/><input type="submit" id="btnGroupEventEditSubmit" value="Edit"/>');


    $("#btnGroupEventEditSubmit").click(
        () => { 
            //$("input[name='radioAddTag']:checked").val()
            //^Gets the tag input, later expand eventaddsubmit to work with tags also (change php)
            let tag = null;
            let radioValue = $("input[name='groupRadioEditTag']:checked").val();
            if(radioValue){
                tag = radioValue; 
            }
            
            //console.log("Tag "+tag);
            edit_group_event( 
                [group_event_id, $("#inGroupEventEditTitle").val(), "Some Description", $("#inGroupEventEditDate").val(), $("#inGroupEventEditTime").val(), tag],
                disDeleteGroupEvent//Doesn't matter as long as it re-renders the groups
            );
            


            //$("#divCEDGroups").empty();

            group_events( //Re-render the events list after making an edit to the group events
                [["0","0"],disDate.getMonth()+1, group_id],
                disGroupEventsList
            )
        }

        );
    //$('#groupEventAdd').append($event_add);
}

function disGroupEventsList(data){

    //groupEventAddForm();

    // $("#divCEDGroups").append($("<button id = 'add_group_event'>Add Group Event</button>"))

    $("#divCEDGroups").append($("<table id = 'tableGroupEventList'></table>"));
    // bind the div of event list to a var 
    //let divEventList=$("#divEventList");

    // acquire the length of JSON data  
    let listLength=Object.keys(data).length;
    // get the values inside the JSON data and bind it with html elements
    let listKeys=Object.keys(data);
    // to empty the table and then add new table headers
    $("#tableGroupEventList").empty();

    

    $trHeaders = $("<tr></tr>"); 
    $th1 = $("<th></th>").text("Title");

    $th2 = $("<th></th>").text("Date");
    $th3 = $("<th></th>").text("Time");
    $th5 = $("<th></th>").text("Tag");
    $th4 = $("<th></th>").text("Options");
    $trHeaders.append($th1, $th2, $th3, $th5, $th4);


    $("#tableGroupEventList").append($trHeaders);
    for (x in listKeys){
        let indexData=String(listKeys[x]);

        // acquire data and append to html table
        if (data[indexData]!=true && data[indexData] != "Returning all events for user" && indexData != "num_events"){ 
            console.log("trying to add events to list");
            let $tr=$("<tr></tr>").attr("id", "trGroupEventsList"+x);
            let $tdEventTitle= $("<td></td>").text(data[indexData]["event_title"]);
            let $tdEventDate= $("<td></td>").text(data[indexData]["event_date"]);
            let $tdEventTime = $("<td></td>").text(data[indexData]["event_time"]);
            let $tdEventTag = $("<td></td>").text(data[indexData]["event_tag"]);
            
            let eventId = data[indexData]["event_id"];  
            let eventTitle = data[indexData]["event_title"];
            let eventDate = data[indexData]["event_date"];
            let eventTime = data[indexData]["event_time"];
            let eventTag = data[indexData]["event_tag"];
            let groupId = data[indexData]["group_id"];
            let groupEventId =data[indexData]["group_event_id"];
            

            let $btnEdit = $("<button></button>").text("Edit");
            $btnEdit.attr("id", "btnEditGroupEvent"+eventId);


            $btnEdit.click( () => {
                groupEventEditForm(groupEventId, groupId);
            });

            let $btnDelete = $("<button></button>").text("Delete");
            $btnDelete.attr("id", "btnDeleteGroupEvent"+eventId);

            $btnDelete.click(() => {
                delete_group_event(
                    [groupEventId], 
                    disDeleteGroupEvent);

                group_events( //Re-display group events
                    [["0","0"],disDate.getMonth()+1,group_id],
                    disGroupEventsList
                )
            });

            let $tdButtons = $("<td></td>");
            $tdButtons.append($btnEdit, $btnDelete);

            $tr.append($tdEventTitle, $tdEventDate, $tdEventTime, $tdEventTag, $tdButtons); 
            
            console.log(document.getElementById('tableGroupEventList'));
            

            $("#tableGroupEventList").append($tr);
            
        }
        
    }
    
    $("#tableGroupEventList").show();
}

function disDeleteGroupEvent(data) {
    $('#divCEDGroups').empty();

    disDeleteEvent(data); //Perform the same thing on the calendar as you would with the normal delete
}

function disDeleteGroup(data){
    $('#divCEDGroups').empty();
    //Just re-render all groups
    display_groups(
        [],
        disGroups
    )
}

function disCreateGroup(data){
    $('#divCEDGroups').empty();
    //Just re-render all groups
    display_groups(
        [],
        disGroups
    )
}

function logout_success(data){
    $("#pWelcome").hide();
    $("#divLogin").show();
    $("#divRegister").show();
    $("#divLogout").hide();
    $("#divEventList").hide();
    $("#divEventAdd").hide();
    $("#divEventEdit").hide();
    $("#divShare").hide();
    $("#inLoginUsrnm").val('');
    $("#inLoginPswd").val('');
    $("#divEventEdit").empty();
    $("#inEventAddTitle").val('');
    $("#inEventAddDate").val('');
    $("#inEventAddTime").val('');
    $("#radioAddTagP").prop("checked", false); 
    $("#radioAddTagH").prop("checked", false); 
    $("#radioAddTagW").prop("checked", false); 
    $("#radioAddTagA").prop("checked", false); 
    $("#radioAddTagB").prop("checked", false); 
    $("#inEventEditTitle").val("");
    $("#inEventEditDate").val("");
    $("#inEventEditTime").val("");
    $("#radioEventEditP").prop("checked", false); 
    $("#radioEventEditH").prop("checked", false); 
    $("#radioEventEditW").prop("checked", false); 
    $("#radioEventEditA").prop("checked", false); 
    $("#radioEventEditB").prop("checked", false); 

    logged_in = false;
    window.disDate = new Date();
    window.months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    window.dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
}

function account_create_success (data) {
    $("#divRegister").hide();
    $("#inRegisterUsrnm").val('');
    $("#inRegisterPswd").val('');
    alert("You have succesfully signed up. You can now log in.");
}

function create_event_success(response){
    alert(response.description);
    $("#inEventAddTitle").val('');
    $("#inEventAddDate").val('');
    $("#inEventAddTime").val('');
    $("#radioAddTagP").prop("checked", false); 
    $("#radioAddTagH").prop("checked", false); 
    $("#radioAddTagW").prop("checked", false); 
    $("#radioAddTagA").prop("checked", false); 
    $("#radioAddTagB").prop("checked", false); 
    radioValue = null;
    user_events(
        [["0","0"], disDate.getMonth()+1],
        disEventsList
    );
    logged_in = true;
    window.disDate = new Date();
    window.months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    window.dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
}

function disEventsList(data){

    let listKeys=Object.keys(data);
    // to empty the table and then add new table headers
    $("#tableEventList").empty();

    $trHeaders = $("<tr></tr>"); 
    $th1 = $("<th></th>").text("Title");

    $th2 = $("<th></th>").text("Date");
    $th3 = $("<th></th>").text("Time");
    $th5 = $("<th></th>").text("Tag");
    $th4 = $("<th></th>").text("Options");
    $trHeaders.append($th1, $th2, $th3, $th5, $th4);


    $("#tableEventList").append($trHeaders);
    for (x in listKeys){
        let indexData=String(listKeys[x]);

        // acquire data and append to html table
        if (data[indexData]!=true && data[indexData] != "Returning all events for user" && indexData != "num_events"){ 
            console.log("trying to add events to list");
            let $tr=$("<tr></tr>").attr("id", "trEventsList"+x);
            let $tdEventTitle= $("<td></td>").text(data[indexData]["event_title"]);
            let $tdEventDate= $("<td></td>").text(data[indexData]["event_date"]);
            let $tdEventTime = $("<td></td>").text(data[indexData]["event_time"]);
            let $tdEventTag = $("<td></td>").text(data[indexData]["event_tag"]);
            
            let eventId = data[indexData]["event_id"];  
            let eventTitle = data[indexData]["event_title"];
            let eventDate = data[indexData]["event_date"];
            let eventTime = data[indexData]["event_time"];
            let eventTag = data[indexData]["event_tag"];
            

            let $btnEdit = $("<button></button>").text("Edit");
            $btnEdit.attr("id", "btnEditEvent"+eventId);
            $btnEdit.click( () => {
                editEventForm(eventId, eventTitle, eventDate, eventTime, eventTag);
            });

            let $btnDelete = $("<button></button>").text("Delete");
            $btnDelete.attr("id", "btnDeleteEvent"+eventId);

            $btnDelete.click(() => {
                delete_event(
                    [eventId], 
                    disDeleteEvent);
            });

            let $tdButtons = $("<td></td>");
            $tdButtons.append($btnEdit, $btnDelete);

            $tr.append($tdEventTitle, $tdEventDate, $tdEventTime, $tdEventTag, $tdButtons); 
            
            $("#tableEventList").append($tr);
            
        }
        
    }
    $("#tableEventList").show();
    reloadRadio();
}

function disDeleteEvent(data){
    //Can un-hard code this using whats passed in through data
    user_events( //Temporarily hard coded
        [["0","0"], disDate.getMonth()+1],
        disEventsList
    );
    logged_in = true;
    window.disDate = new Date();
    window.months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    window.dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
}

function editEventForm(eventId, title, date, time, tag){ ``
 

    // show the table and the current value of event to be editted
    $("#divEventEdit").empty();
    $header = $("<h4></h4>").text("Edit Event");
    $labelTitle = $("<label></label>").text("Title: ");
    $labelDate = $("<label></label>").text("Date: ");
    $labelTime = $("<label></label>").text("Time: ");
    $inputTitle = $("<input></input>").val(title);
    $inputTitle.attr("type", "text");
    $inputTitle.attr("id", "inEventEditTitle");
    $inputDate = $("<input></input>").val(date);
    $inputDate.attr("type", "date");
    $inputDate.attr("id", "inEventEditDate");
    $inputTime = $("<input></input>").val(time);
    $inputTime.attr("type", "time");
    $inputTime.attr("id", "inEventEditTime");
    $button = $("<input></input>");
    $button.attr("id", "btnEventEditSubmit");
    $button.attr("type", "submit");
    $button.attr("value", "Submit");

    // add radio buttons for tags
    let $rb1 = $("<input></input>").attr("type", "radio");
    let $rb2 = $("<input></input>").attr("type", "radio");
    let $rb3 = $("<input></input>").attr("type", "radio");
    let $rb4 = $("<input></input>").attr("type", "radio");
    let $rb5 = $("<input></input>").attr("type", "radio");

    $rb1.attr("id", "radioEventEditP"); 
    $rb1.attr("value", "personal");
    $rb1.attr("name", "radioEventEdit")
    let $label1 = $("<label></label>").text("Personal");
    $rb2.attr("id", "radioEventEditA"); 
    $rb2.attr("value", "academic");
    $rb2.attr("name", "radioEventEdit")
    let $label2 = $("<label></label>").text("Academic");
    $rb3.attr("id", "radioEventEditW"); 
    $rb3.attr("value", "work");
    $rb3.attr("name", "radioEventEdit")
    let $label3 = $("<label></label>").text("Work");
    $rb4.attr("id", "radioEventEditH"); 
    $rb4.attr("value", "holiday");
    $rb4.attr("name", "radioEventEdit")
    let $label4 = $("<label></label>").text("Holiday");
    $rb5.attr("id", "radioEventEditB"); 
    $rb5.attr("value", "birthday");
    $rb5.attr("name", "radioEventEdit")
    let $label5 = $("<label></label>").text("Birthday");



    $("#divEventEdit").append($header, $labelTitle, $inputTitle, $("<br>"), $labelDate, $inputDate, $("<br>"), $labelTime, $inputTime, $("<br>"), $rb1, $label1, $rb2, $label2, $rb3, $label3, $rb4, $label4, $rb5, $label5, $("<br>"), $button); 


    $("#btnEventEditSubmit").click(
        () =>{
            let tag = null;
            let radioValue = $("input[name='radioEventEdit']:checked").val();
            if(radioValue){
                tag = radioValue; 
            }
            edit_event(
                [eventId,$("#inEventEditTitle").val(),"test description edited", $("#inEventEditDate").val(),$("#inEventEditTime").val(),tag],
                disEditEvent
            )
        }
    );
    
    $("#divEventEdit").show();

    
}

function disEditEvent(data){
    //Use data to un-hard-code this
    user_events( //Temporarily hard coded
        [["0","0"], disDate.getMonth()+1],
        disEventsList
    );
    $("#divEventEdit").hide();
}

function reloadRadio(){
    $("#disEventTagP").prop("checked", false); 
    $("#disEventTagH").prop("checked", false); 
    $("#disEventTagW").prop("checked", false); 
    $("#disEventTagA").prop("checked", false); 
    $("#disEventTagB").prop("checked", false);  
}

function render_test(data){
    console.log("Render function test: ");
    console.log(data);

    let ele = document.getElementById('csrf-display');
    ele.innerHTML += '<br>';
    ele.innerHTML += data;
}