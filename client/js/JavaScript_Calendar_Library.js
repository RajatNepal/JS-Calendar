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
            let $td=$("<td></td>");
            $td.text(curDate.getDate());
            $tr.append($td);
        }
        $("#tableCal").append($tr);
    }
}

//function to display the previous month
function disPreMonth(){
    let disMonth=disDate.getMonth()-1;
    disDate.setMonth(disMonth);
    dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
}

//function to display the next month
function disNextMonth(){
    let disMonth=disDate.getMonth()+1;
    disDate.setMonth(disMonth);
    dateInfo=new getDate(disDate);
    disCalendar(dateInfo.weeks);
}