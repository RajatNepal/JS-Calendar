// import * as requests from '../js/requests.js';

$.getScript("../js/requests.js", function() {
    console.log("requests.js script loaded");
 });

 //Important to load requests before renders

 $.getScript("../js/renders.js", function() {
    console.log("renders.js script loaded");
 });
 
 let logged_in = false;


 //global.month = 3; //Gonna just use this to keep track of what month we're in.




//For some reason importing this with jquery makes it explode so 
//I will just leave it up here for now.
// calendar library from https://classes.engineering.wustl.edu/cse330/index.php?title=JavaScript_Calendar_Library
(function(){
    Date.prototype.deltaDays=function(c){
        return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)
    };
    Date.prototype.getSunday=function(){
        return this.deltaDays(-1*this.getDay())
    }
})();

function Week(c){
    this.sunday=c.getSunday();
    this.nextWeek=function(){
        return new Week(this.sunday.deltaDays(7))
    };
    this.prevWeek=function(){
        return new Week(this.sunday.deltaDays(-7))
    };
    this.contains=function(b){
        return this.sunday.valueOf()===b.getSunday().valueOf()
    };
    this.getDates=function(){
        for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));
        return b
    }
}

function Month(c,b){
    this.year=c;
    this.month=b;
    this.nextMonth=function(){
        return new Month(c+Math.floor((b+1)/12),(b+1)%12)
    };
    this.prevMonth=function(){
        return new Month(c+Math.floor((b-1)/12),(b+11)%12)
    };
    this.getDateObject=function(a){
        return new Date(this.year,this.month,a)
    };
    this.getWeeks=function(){
            var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);
            for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);return c
        }
    };

//function to get attributes of a date obj
function getDate(date){
    this.fullDate=date;
    this.month = this.fullDate.getMonth();
    //$("#headerCal").text(this.month);
    this.year = this.fullDate.getFullYear();
    let objMonth = new Month(this.year,this.month);
    this.weeks=objMonth.getWeeks();
}   

//the function to display calendar body
function disCalendar(weeks){
    $("#tableCal").empty();
    this.weeks=weeks;
    //display the month and year
    let disMonthFull=months[dateInfo.month];
    $("#headerCal").text(disMonthFull+", "+dateInfo.year);

    //display the headers of the calendar
    let days=["Sunday","Monday", "Tuesday","Wednesday", "Thursday", "Friday", "Saturday"];
    $trHeaders=$("<tr></tr>");
    for (let s=0; s<days.length; s++){
        $th=$("<th></th>").text(days[s]);
        
        $trHeaders.append($th);
    }
    $("#tableCal").append($trHeaders);

    //display the body of calendar
    for (let i=0; i<this.weeks.length; i++){
        let curWeek=this.weeks[i];
        let $tr=$("<tr></tr>");
        let curDates=curWeek.getDates();
        for (let j=0; j<curDates.length; j++){
            let curDate=curDates[j];
            curMonthNum = curDate.getMonth() +1;
            curDateNum = curDate.getDate();

            let $td=$("<td>");
            $td.append(curDateNum);

            if(logged_in){
                
                $td.append("<br>");
                

                //let [range, month_num] = param;

                server_fetch(
                    "user_events", 
                    "POST", 
                    {
                        "range_start": curDateNum,
                        "range_end": curDateNum,
                        "month_num": curMonthNum,
                        "token":get_token_cookie()
                    })
                .then(res=>res.json())
                .then((response)=>{
                    console.log(JSON.stringify(response));

                    if (response.success){
                        if(response.num_events >0){

                            //for some reason, the curMonthNum and CurDateNum here are the last day of the month's info, here is a work around
                            let eventDate = new Date(response["event_0"]["event_date"]);
                            let curMonth = eventDate.getMonth() + 1;
                            let curDate = eventDate.getDate() + 1;

                            let $btnViewEvents = $("<button></button>").text("View "+ response.num_events +" event(s)");
                            $btnViewEvents.attr("id", "btnDeleteEvent"+curMonth + "_" + curDate);

                            $btnViewEvents.click(() => {
                                user_events(
                                    [[curDate,curDate], curMonth],
                                    disEventsList
                                );
                            });                   
                            $td.append($btnViewEvents);

                        }
            
                    }
                    else {
                        alert(response.description);
                    }
                })
                .catch((error)=>console.error("Error",error));

            }

            $td.append("</td>");

            $tr.append($td);

        }
        $("#tableCal").append($tr);
    }

}


//function to display the previous month
function disPreMonth(){
    // global.month--;
    // if (global.month == 0){
    //     global.month = 12;
    // }
    let disMonth=disDate.getMonth()-1;
    disDate.setMonth(disMonth);
    dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
    if (logged_in){
        user_events(
            [["0","0"], disMonth+1],
            disEventsList
        );
    }

}

//function to display the next month
function disNextMonth(){
    // global.month++;
    // if (global.month == 13){
    //     global.month = 1;
    // }
    let disMonth=disDate.getMonth()+1;
    disDate.setMonth(disMonth);
    dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
    if (logged_in){
        user_events(
            [["0","0"], disMonth+1],
            disEventsList
        );
    }


}





//initial function
//Check if we're already logged in before running this
//This is giving us the logout view whenever we refresh, even when we still have the token
$(document).ready(function(){
    //prepare month view
    window.disDate = new Date();
    window.months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    window.dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
    let disMonth=disDate.getMonth();

    console.log(disMonth);


    $("#divEventAdd").hide();
    $("#divLogout").hide();
    $("#tableEventList").hide();
    $("#divEventEdit").hide();
    $("#divEventTags").hide();
    $("#divShare").hide();
    $("#divEventList").hide();
    $("#divGroups").hide();
    

    $("#btnLoginSubmit").click(
        () => {
            server_login(
                [document.getElementById("inLoginUsrnm").value, document.getElementById("inLoginPswd").value],
                login_success
            )
        }

    );

    $("#btnLogoutSubmit").click(
        //NOTE: Right now logout doesn't seem to be destroying the session_id properly
        //fixed
        () => {
            server_logout(
                [],
                logout_success
            )
        }

    );


    $("#btnRegisterSubmit").click(
        () => {
            server_create_account(
            [document.getElementById("inRegisterUsrnm").value,document.getElementById("inRegisterPswd").value],
            account_create_success
            )
        }
    );


    $("#btnCalBodyPre").click(disPreMonth); //Doesn't fetch from our backend, doesn't need reformatting
    $("#btnCalBodyNext").click(disNextMonth); //Doesn't fetch from our backend, doesn't need reformatting


    $("#btnEventAddSubmit").click(
        () => { 
            //^Gets the tag input, later expand eventaddsubmit to work with tags also (change php)
            let tag = null;
            let radioValue = $("input[name='radioAddTag']:checked").val();
            if(radioValue){
                tag = radioValue; 
            }
            
            console.log("Tag "+tag);
            create_event( 
                [$("#inEventAddTitle").val(), "Some Description", $("#inEventAddDate").val(), $("#inEventAddTime").val(), "1", tag],
                create_event_success
                );
        }

        );

    $("#btnDisEventAll").click(
        () => {
            user_events(
                [["0","0"], disMonth+1],
                disEventsList
            );
        }
    );

});





