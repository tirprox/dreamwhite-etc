// Состояние начала диалога
var Flag_chat = false; //Состояние JivoSite
var Flag_data = false; //Пока false данные в статистику не отправляются

var Time; //Время для JivoSite
var Time_flag_data; //Время для Flag_data

/* По умолчанию обрабатывать события ввода от пользователя,
первое событие ставит этот флаг на false и запускает таймер.
Таймер через заданное время возвращает флагу true и выполняет нужное действие */
var Handle_input_from_user  = true;
var User_input_timeout = 4000; // (4 секунды) Через сколько таймер должен снова начать слушать ввод, в мс

// Функция должна вызываться по событию
function onInput() {
    if (Handle_input_from_user) {
        Handle_input_from_user = false;
        setTimeout(onTimeout, User_input_timeout);
    }

    // Тут обычная обработка события, например расчет стоимости должен работать всегда, независимо от таймера

}

function onTimeout() {
    if (!Handle_input_from_user) {
        Handle_input_from_user = true;
    }

    // Тут нужно отправить данные в метрику и куда надо

}


var Time_ecommerce; //Время для Ecommerce

// Функция отображает состояние JivoSite
function jivo_onLoadCallback () {
    if (jivo_api.chatMode() == "online") {
        Flag_chat = true;
    }
}

function jivo_onOpen() {
    Flag_chat = false;
}

// Вввод данных в быстрый калькулятор
function com_count_event(){
    var t = document.getElementById( 'com_count' );

    if (t.value > 0) {
        document.getElementById( 'com_top_block' ).value = '' + t.value + '';
        com_top_event();
    } else {
        document.getElementById( 'com_cost' ).innerHTML = '';
    }
};

// Вввод данных в калькулятор цен

function com_top_event(){
    clearTimeout(Time);
    clearTimeout(Time_flag_data);

    document.getElementById( 'com_top_block' ).placeholder = '1';

    var t = document.getElementById( 'com_top_block' );

    var price1 = 1330
    var price2 = 20

    var price3 = 1010
    var price4 = 4

    var price5 = 910
    var price6 = 2

    if( t.value > 99 )
        t.value = 0;
    if( t.value > 0 )
        b = 2000;
    if( t.value > 1 )
        b = 1800;
    if( t.value > 2 )
        b = 1600;
    if( t.value > 3 )
        b = price1 - price2 * (t.value);
    if( t.value > 20 )
        b = price3 - price4 * (t.value);
    if( t.value > 50 )
        b = price5 - price6 * (t.value);
    var a = t.value;
    if (isNaN(t.value) == true || t.value == 0) {
        var a = 1;
        b = 2000;
    }
    var res = b
    var res12 = a * b

    document.getElementById( 'com_cr_2_block' ).innerHTML = ' ' + a + ' ' + Padej(a, "номером", "номерами", "номерами") + '';

    document.getElementById( 'com_cr_3_block' ).innerHTML = '' + res12.formatMoney( 0 ) + ' <span class="vc_icon_element-icon fa fa-rub"></span>';


    if (document.getElementById('com_count')) {
        document.getElementById( 'com_count' ).value = '' + a + '';
        document.getElementById( 'com_cost' ).innerHTML = '' + res12.formatMoney( 0 ) + ' <span class="vc_icon_element-icon fa fa-rub"></span>';
    }

    if (t.value > 0) {
        w = 42000;
        k = 54600;
        spec = 'Средняя зарплата системного администратора в СПб ~' + w.formatMoney( 0 ) + '&nbsp;<span class="vc_icon_element-icon fa fa-rub"></span> в месяц (' + k.formatMoney( 0 ) + '&nbsp;<span class="vc_icon_element-icon fa fa-rub"></span>, включая страховые взносы)';
        zarnal12 = k * 12;
    }
    if (t.value > 50) {
        w = 56000;
        k = 72800;
        spec = 'Средняя зарплата высококвалифицированного специалиста в СПб ~' + w.formatMoney( 0 ) + '&nbsp;<span class="vc_icon_element-icon fa fa-rub"></span> в месяц (' + k.formatMoney( 0 ) + '&nbsp;<span class="vc_icon_element-icon fa fa-rub"></span>, включая страховые взносы)';
        zarnal12 = k * 12;
    }

    document.getElementById( 'com_center_left_2' ).innerHTML = ' ' + spec + '';
    document.getElementById( 'com_cl_3_block' ).innerHTML = ' ' + zarnal12.formatMoney( 0 ) + ' <span class="vc_icon_element-icon fa fa-rub"></span>';

    function second_passed() {
        var h = t.value;
        if ( Flag_chat == true ) {
            if (typeof yaCounter24468002 != 'undefined') { yaCounter24468002.reachGoal('calc_jivo_hotel'); }
            if (typeof gtag !== 'undefined') { ga('send', 'pageview', 'calc_jivo_hotel');  }
            var res = h * b
            var j = res.formatMoney(0);
            var w3 = NumberToWords(h, ["номером", "номерами", "номерами"]);
            jivo_api.showProactiveInvitation('Здравствуйте! Стоимость обслуживания гостиницы с ' + w3 + ' – ' + j + ' рублей в месяц. Если у вас есть вопросы, готов на них ответить прямо сейчас');
            Flag_chat = false;
        }

    }
    Time = setTimeout(second_passed, 11000);


    function second_passed_flag_data() {
        Flag_data = true;
    }
    Time_flag_data = setTimeout(second_passed_flag_data, 400);

    function second_passed_ecommerce() {
        if ( Flag_data == true ) {
            if (typeof yaCounter24468002 != 'undefined') { yaCounter24468002.reachGoal('calc_hotel'); }
            if (typeof gtag !== 'undefined') { ga('send', 'pageview', 'calc_hotel');  }

            dataLayer.push({
                "ecommerce": {
                    "detail": {
                        "products": [
                            {
                                "id": "Hotel#" + a,
                                "category": "Гостиницы",
                                "name" : "Номеров: " + a + ". Цена: " + res12.formatMoney(0) + " руб. в месяц",
                                "price": res12
                            }
                        ]
                    }
                }
            });

            Flag_data = false;
        }
    }
    Time_ecommerce = setTimeout(second_passed_ecommerce, 500);
};

//Денежный формат
Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "," : d,
        t = t == undefined ? " " : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

//Падеж после цифр
var Padej = function (_number, _case1, _case2, _case3) {
    var base = _number - Math.floor(_number / 100) * 100;
    var result;
    if (base > 9 && base < 20) {
        result = _case3;
    } else {
        var remainder = _number - Math.floor(_number / 10) * 10;
        if (1 == remainder) result = _case1;
        else if (0 < remainder && 5 > remainder) result = _case2;
        else result = _case3;
    }
    return result;
}

//Перевод цифр в слова
var NumberToWords = (function () {
    var words = [['ноль', ['одним', 'одна', 'одно'],
            ['двумя', 'две', 'два'], 'тремя', 'четырьмя', 'пятью',
            'шеcтью', 'семью', 'восьмью', 'девятью', 'десятью',
            'одиннадцатью', 'двенадцатью', 'тринадцатью',
            'четырнадцатью', 'пятнадцатью', 'шестнадцатью',
            'семнадцатью', 'восемнадцатью', 'девятнадцатью'],
            [,,'двадцатью', 'тридцатью', 'сорока', 'пятьюдесятью',
                'шестьюдесятью', 'семьюдесятью', 'восьмьюдесятью',
                'девяноста'],
            [,'ста', 'двумястами', 'трёхстами', 'четырьмястами', 'пятьюстами',
                'шестьюстами', 'семьюстами', 'восемьюстами', 'девятьюстами'],
            ['тысяча', 'тысячи', 'тысяч'],
            ['миллион', 'миллиона', 'миллионов']],
        gap = String.fromCharCode(32),
        overdo = 'слишком много';
    function Convert(aNum, aCase, aBool) {
        var p, a;
        aNum = parseInt(aNum, 10);
        if (aNum < 20) {
            return ((aBool && !aNum)
                ? gap
                : ((a = words[0][aNum]) instanceof Array)
                    ? a[aCase || 0]
                    : a);
        }
        if (aNum < 100) {
            p = parseInt(aNum / 10, 10);
            return Join(words[1][p],
                Convert(aNum % 10, aCase, true));
        }
        if (aNum < 1000) {
            p = parseInt(aNum / 100, 10);
            return Join(words[2][p],
                Convert(aNum % 100, aCase, true));
        }
        if (aNum < 1000000) {
            p = parseInt(aNum / 1000, 10);
            return Join(Convert(p, 1, true),
                Proper(p, words[3]),
                Convert(aNum % 1000, aCase, true));
        }
        p = parseInt(aNum / 1000000, 10);
        return Join(Convert(p, 0, true),
            Proper(p, words[4]),
            Convert(aNum % 1000000, aCase, true));

    };
    function Proper(aNum, aArr) {
        aNum = Simple(aNum);
        return ((aNum == 1)
            ? aArr[0]
            : ((aNum < 5 && aNum)
                ? aArr[1]
                : aArr[2]));
    };
    function Simple(aNum) {
        return ((aNum < 20)
            ? aNum
            : (aNum < 100)
                ? aNum % 10
                : arguments.callee(aNum % 100));
    }
    function Join() {
        return Array.prototype.join.call(arguments, gap);
    };
    return (function (aNum, aArr, aCase) {
        var b = (aNum > 999999999),
            w = (b) ? overdo
                : Convert(aNum, aCase),
            i = (aArr instanceof Array)
                ? Proper(b ? 5 : aNum, aArr)
                : gap;
        return Join(w, i).replace(/\s\s\s?/g, gap);
    });
})();

//Уведомления в JivoSite при вводе данных для обращения внимания
var jivo_onIntroduction = function() {
    var a = {}, b = jivo_api.getContactInfo();
    if(b.client_name !== null) a["Имя"] = b.client_name;
    if(b.email !== null) a["Email"] = b.email;
    if(b.phone !== null) a["Телефон"] = b.phone;
    jivo_api.sendMessage(a, "Клиент указал свои контакты");
}