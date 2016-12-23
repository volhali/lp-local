function cofeeMachine(cmPower, cpCapacity) {
 this.Power = cmPower;
 this.cpCapacity = cpCapacity;
    var cm = this,
        cmWaterCur,
        cmTimer,
        cmStatus = false;

    function cmBoilCalc() {
        return cmWaterCur * 4200 * 80 / cmPower;
    }

    function cmReady() {
        cmStatus = false;
       /* alert('Кофе готов!');*/
    }

    this.run = function () {
        if (!cmStatus) {
            cmTimer = setTimeout(cmReady, cmBoilCalc());
            cmStatus = true;
            return true;
        } else return false;
    }

    this.stop = function () {
        if (cmStatus) {
            clearTimeout(cmTimer);
            cmStatus = false;
            /*alert('Приготовление кофе отменено!');*/
            return true;
        } else return false;
    }

    this.addWater = function (water) {
        this.water=water;
        if (water === undefined) return cmWaterCur;
        else if (+water > 0 && +water <= cpCapacity) {
            cmWaterCur = water;
            this.cmWaterCur = cmWaterCur;
            return true;
        } else {
            alert('Некорректный объем воды');
            return false;
        }
    }

}

var cm1 = new cofeeMachine(300000, 2000);


var addWaterRes =cm1.addWater(2000);

var run = cm1.run();
/*var stopCook = setTimeout(function () {
   cm1.stop();
}, 2000);*/

(function ($) {
        $(document).ready(function(){
            $('#res').on('click',function(){
               $('#power').val(cm1.Power); 
               $('#capacity').val(cm1.cpCapacity); 
               $('#capacity_add').val(cm1.water); 
                 
               $('#capacity_cur').val(cm1.cmWaterCur); 
                if (!addWaterRes) {$('#status').val('Некорректный объем воды');}
/*                else if (stopCook) {$('#status').val('Приготовление кофе отменено!');}
*/                else if (run) {$('#status').val('Кофе готов!');}

            })
        })

    })(jQuery)
