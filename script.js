(function ($) {
    $(document).ready(function () {

        /* Панель навигации */
        // Описываем функцию, которая
        function lpHeader() {
            if ($(window).scrollTop() == 0) { // Если находимся в начале страницы
                $('header').addClass('top'); // Добавляет класс top для панели
            } else { // Иначе
                $('header.top').removeClass('top'); // Удаляет его
            }
        }
        // Вызываем эту функцию
        lpHeader(); // Единожды при загрузке страницы
        $(window).on('scroll', lpHeader); // И каждый раз при скролле

        /* Плавный скролл при клике на ссылку в меню */
        // Помещаем набор ссылок в переменную
        var lpMenuItems = $('ul.nav li a');
        // Для каждой ссылке в наборе
        lpMenuItems.each(function () {
            // Помещаем в переменные
            var link = $(this), // Саму ссылку
                linkHref = link.attr('href'), // Путь, на который она ссылается
                linkTrgt = $(linkHref); // Объект, на который она ссылается
            // Если таковой объект существует
            if (linkTrgt.length > 0) {
                // То добавляем обработчик на клик по ссылке
                link.on('click', function (e) {
                    // Отменяем переход по умолчанию
                    e.preventDefault();
                    // Вычисляем отступ до объекта-назначения
                    var offset = linkTrgt.offset().top;
                    // И плавно скроллим страницу к этой точке
                    $('body, html').animate({
                        scrollTop: offset - linkTrgt.attr('data-offset')
                    }, 750);
                });
            }
        });

        /* Отслеживание активного экрана */
        // Объявляем массив
        var lpNavItems = [];
        // Описываем функцию, которая
        function lpSetNavItems() {
            // Помещает в массив для каждого экрана
            $('section').each(function () {
                lpNavItems.push({
                    top: $(this).offset().top, // Его отступ от начала экрана
                    name: $(this).attr('id') // Значение ID
                });
            });
        }
        // Вызываем эту функцию
        lpSetNavItems(); // Единожды при загрузке страницы
        $(window).on('resize', lpSetNavItems); // И каждый раз при изменении размера окна

        // Описываем функцию, которая вычисляет активный экран
        function lpSetNavActive() {
            // В этой переменной в конце forEach будет ID активного экрана
            var curItem = '';
            // Чтобы он туда попал, перебираем все экраны
            lpNavItems.forEach(function (item) {
                // И если положение экрана от начала страницы меньше текущего скролла
                if ($(window).scrollTop() > item.top - 200) {
                    curItem = item.name; // В переменную вносим ID этого экрана
                }
            });
            // Далее, если href ссылки внутри активного пункта меню не совпадает с ID найденного нами активного экрана
            // Либо активные элементы отсутствуют в меню
            if ($('ul.nav li.active a').attr('href') != '#' + curItem || $('ul.nav li.active').length == 0) {
                // Удаляем класс у текущего активного элемента
                $('ul.nav li.active').removeClass('active');
                // И добавляем класс active пункту, внутри которого лежит ссылка, у которой href совпал с ID активного экрана 
                $('ul.nav li a[href="#' + curItem + '"]').parent().addClass('active');
            }
        }
        // Вызываем эту функцию
        lpSetNavActive(); // Единожды при загрузке страницы
        $(window).on('scroll', lpSetNavActive); // И каждый раз при скролле

        /* Слайдер */

        $('.lp-slider1').owlCarousel({
            singleItem: true,
            navigation: true,
            navigationText: ['<i class="fa fa-arrow-left"></i>', '<i class="fa fa-arrow-right"></i>']
        });

        $('.lp-slider2').owlCarousel({
            navigation: true,
            navigationText: ['<i class="fa fa-arrow-left"></i>', '<i class="fa fa-arrow-right"></i>'],
            itemsCustom: [[0, 1], [400, 3], [900, 5]]
        });

    });
})(jQuery);