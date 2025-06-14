/**
 * Moussiq PRO
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2014 silbersaiten
 * @version   2.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
var timePicker={
    renderTimePicker:function()
        {var element=$('div#timeSelect'),day,hour,minute,dayNum,insertStr='';
            insertStr+='<div class="leftSide">';
            insertStr+='<h3>'+tpDaysLabel+'</h3>';
            insertStr+='<ul class="timePickerList" id="daysList">';
            for(var i=0;i<7;i++){
                dayNum=i+1;insertStr+='<li class="days day_'+(dayNum==7?0:dayNum)+'"><span class="timepicker day">'+tpDaysNames[i]+'</span></li>'
            }
            insertStr+='</ul>';
            insertStr+='</div>';
            insertStr+='<div class="rightSide">';
            insertStr+='<h3>'+tpHourLabel+'</h3>';
            insertStr+='<ul class="timePickerList" id="hoursList">';
            for(var i=0;i<24;i++){
                hour=String(i);
                insertStr+='<li class="hours hour_'+i+'"><span class="timepicker hour">'+(hour.length==1?"0"+hour:hour)+'</span></li>'
            }
            insertStr+='</ul>';
            insertStr+='<h3>'+tpMinuteLabel+'</h3>';
            insertStr+='<ul class="timePickerList" id="minutesList">';
            for(var i=0;i<12;i++){
                minute=String(5*i);
                insertStr+='<li class="minutes minute_'+(5*i)+'"><span class="timepicker minute">'+(minute.length==1?"0"+minute:minute)+'</span></li>';
            }
            insertStr+='</ul>';
            insertStr+='</div>';
            insertStr+='<input type="hidden" name="daysList_input" value="" />';
            insertStr+='<input type="hidden" name="hoursList_input" value="" />';
            insertStr+='<input type="hidden" name="minutesList_input" value="" />';
            element.prepend($(insertStr));
            exporter.delegate('mouseover','span.timepicker',timePicker.timePickerMouseover);
            exporter.delegate('mouseout','span.timepicker',timePicker.timePickerMouseout);
            exporter.delegate('click','span.timepicker',timePicker.timePickerClick);
        }
    ,timePickerMouseover:function(element)
        {
            $(this).parent('li').addClass('hovered');
        }
    ,timePickerMouseout:function()
        {
            $(this).parent('li').removeClass('hovered');
        }
    ,timePickerClick:function()
        {
            var deselect=false,currentList=$(this).parents('ul'),selectedLi=$(this).parent('li');
            if(selectedLi.hasClass('selected')){
                deselect=true;
            }
            /*if(currentList.attr('id')!=='daysList'){currentList.find('li.selected').fadeOut('fast',function(){$(this).removeClass('selected');$(this).fadeIn('fast');});}*/
            selectedLi.fadeOut('fast',function(){
                if(!deselect){
                    $(this).addClass('selected');
                    switch(currentList.attr('id')){
                        case'hoursList':
                            timePicker.selectFirstValue('daysList');
                            break;
                        case'minutesList':
                            timePicker.selectFirstValue('hoursList');
                            timePicker.selectFirstValue('daysList');
                            break;
                    }
                }
                else
                {
                    $(this).removeClass('selected');
                }

                $(this).fadeIn('fast');
                timePicker.updateTimeInputs();
            });
        }
    ,selectFirstValue:function(listId)
        {
            var collection=$('ul#'+listId).find('li');
            if(collection.length>0){
                var active=collection.filter('.selected');
                if(active.length==0) {
                    collection.eq(0).fadeOut('fast',function(){
                        $(this).addClass('selected');
                        $(this).fadeIn('fast');
                    });
                }
            }
        }
    ,updateTimeInputs:function()
        {
            var lists=$('div#timeSelect').find('ul');
            if(lists.length>0){
                lists.each(function(){
                    timePicker.updateTimeInput($(this).attr('id'));
                });
            }
        }
    ,updateTimeInput:function(listId)
        {
            var collection=$('ul#'+listId).find('li'),updateStr='';
            if(collection.length>0){
                var active=collection.filter('.selected');
                if(active.length==0){
                    active=collection.eq(0);
                }
                active.each(function(){
                    updateStr+=parseInt($(this).attr('class').split(' ')[1].split('_')[1])+', ';
                });
                updateStr=updateStr.substr(0,parseInt(updateStr.length-2));
                $('input[name="'+listId+'_input"]').val(updateStr);
            }
        }
    ,loadPickedTime:function()
        {
            var minutes,hours,days,minutesList=$('ul#minutesList'),hoursList=$('ul#hoursList'),daysList=$('ul#daysList');
            if(pickedTime.length>0){
                pickedTime=pickedTime.split(' ');
                minutes=pickedTime[0].split(',');
                for(var i=0;i<minutes.length;i++){
                    minutesList.find('li.minute_'+minutes[i]).addClass('selected');
                }
                hours=pickedTime[1].split(',');
                for(var i=0;i<hours.length;i++){
                    hoursList.find('li.hour_'+hours[i]).addClass('selected');
                }
                days=pickedTime[4].split(',');
                for(var i=0;i<days.length;i++){
                    daysList.find('li.day_'+days[i]).addClass('selected');
                }
            }
            timePicker.updateTimeInputs();
        }
};
$(document).ready(function(){
    timePicker.renderTimePicker();
    timePicker.loadPickedTime();
});
