<?php

/**
 * The template for displaying all single posts
 */

get_header();
$all_meta = get_post_meta(get_the_ID());
?>
<?php
the_post();
$post_id = get_the_ID();
$resource_type_terms = wp_get_post_terms($post_id, 'resource_type', array('fields' => 'names'));
if (!empty($resource_type_terms) && !is_wp_error($resource_type_terms)) {
    $resource_type = $resource_type_terms[0];
}
$unfiltered_resource_categories = wp_get_post_terms($post_id, 'resource_category');
$excluded_categories = array('all', 'all-space', 'all-equipment');
$resource_categories = array_filter($unfiltered_resource_categories, function ($term) use ($excluded_categories) {
    return !in_array($term->slug, $excluded_categories);
});
$resource_images = get_post_meta($post_id, '_resource_images', true);
$resource_responsible = get_post_meta($post_id, '_resource_responsible', true);
$resource_access = get_post_meta($post_id, '_resource_access', true);
$resource_floor = get_post_meta($post_id, '_resource_floor', true);

$resource_name_ru = get_post_meta($post_id, '_resource_name_ru', true);
$resource_name_kk = get_post_meta($post_id, '_resource_name_kk', true);
$resource_name_en = get_post_meta($post_id, '_resource_name_en', true);
$resource_description_ru = get_post_meta($post_id, '_resource_description_ru', true);
$resource_description_kk = get_post_meta($post_id, '_resource_description_kk', true);
$resource_description_en = get_post_meta($post_id, '_resource_description_en', true);
$resource_metadata_ru = get_post_meta($post_id, '_resource_metadata_ru', true);
$resource_metadata_kk = get_post_meta($post_id, '_resource_metadata_kk', true);
$resource_metadata_en = get_post_meta($post_id, '_resource_metadata_en', true);

$resource_availability_enabled = get_post_meta($post_id, '_resource_availability_enabled', true);
$resource_available_time = get_post_meta($post_id, '_resource_available_time', true);
$resource_available_days = get_post_meta($post_id, '_resource_available_days', true);

$current_language = get_locale();
$current_user_id = get_current_user_id();

?>
<main id="primary" class="site-main">
    <div class="resource-page-container tw-max-w-7xl tw-w-full tw-mx-auto tw-px-2 tw-py-4 sm:tw-p-8">
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-12 tw-gap-8">

            <!-- sidebar -->
            <div class="md:tw-col-span-4 tw-w-full tw-flex tw-flex-col tw-gap-4">
                <div class="tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl tw-h-fit tw-space-y-4">

                    <!-- images -->

                    <div id="resourceGalleryContainer">
                        <div id="productCarousel" class="f-carousel tw-md:order-last">
                            <?php
                            $images = [];
                            foreach ($resource_images as $image_id) {
                                $image_url = wp_get_attachment_url($image_id);
                                if ($image_url) {
                                    $images[$image_url] = $image_url;
                                }
                            }
                            foreach ($images as $thumb => $large) {
                                echo '<div class="f-carousel__slide" data-fancybox="gallery" data-src="' . esc_url($large) . '">';
                                echo '<img alt="" src="' . esc_url($thumb) . '"/>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>


                    <!-- resource info -->
                    <div class="resource-info tw-space-y-4">

                        <?php
                        // Определяем текущий язык
                        $current_language = get_locale();

                        $resource_name = '';
                        $resource_description = '';
                        $resource_metadata = '';

                        if ($current_language === 'en_US') {
                            $resource_name = $resource_name_en;
                            $resource_description = $resource_description_en;
                            $resource_metadata = $resource_metadata_en;
                        } elseif ($current_language === 'kk') {
                            $resource_name = $resource_name_kk;
                            $resource_description = $resource_description_kk;
                            $resource_metadata = $resource_metadata_kk;
                        } else {
                            $resource_name = $resource_name_ru;
                            $resource_description = $resource_description_ru;
                            $resource_metadata = $resource_metadata_ru;
                        }

                        if ($resource_name) {
                            echo '<h3 class="tw-text-2xl tw-font-bold">' . esc_html($resource_name) . '</h3>';
                        }

                        if ($resource_description) {
                            echo '<p class="tw-text-gray-700 tw-mb-6">' . esc_html($resource_description) . '</p>';
                        }

                        if (isset($resource_floor)) {
                            echo '<p class="tw-text-gray-700 tw-mb-4"><strong>' . esc_html__('Floor', 'spaces_mnu_plugin') . ': </strong> ' . esc_html($resource_floor) . '</p>';
                        }

                        if (is_array($resource_metadata)) {
                            echo '<ul class="tw-list-inside tw-space-y-4">';
                            foreach ($resource_metadata as $key => $value) {
                                echo '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
                            }
                            echo '</ul>';
                        }
                        // else {
                        //     echo '<p class="tw-text-gray-600">' . esc_html__('No metadata available.', 'spaces_mnu_plugin') . '</p>';
                        // }

                        // Отображение доступа
                        // if (is_array($resource_access)) {
                        //     echo '<h4 class="tw-text-lg tw-font-semibold tw-mb-2">' . esc_html__('Available For:', 'spaces_mnu_plugin') . '</h4>';
                        //     echo '<ul class="tw-list-disc tw-list-inside tw-mb-6">';
                        //     foreach ($resource_access as $access) {
                        //         echo '<li>' . esc_html($access) . '</li>';
                        //     }
                        //     echo '</ul>';
                        // }

                        // Отображение категорий
                        if (is_array($resource_categories)) {
                            echo '<div class="tw-flex tw-flex-wrap tw-gap-4">';
                            foreach ($resource_categories as $category) {
                                echo '<div class="tw-bg-white tw-px-4 tw-py-2 tw-rounded-xl tw-text-sm">' . esc_html(__($category->name, 'spaces_mnu_plugin')) . '</div>';
                            }
                            echo '</div>';
                        }

                        ?>

                    </div>

                </div>
            </div>

            <!-- Content Area -->
            <div class="md:tw-col-span-8 tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl tw-space-y-8">

                <?php if ($resource_availability_enabled == 'true'): ?>
                    <div class="tw-space-y-2">
                        <h3 class="tw-text-lg tw-text-[#717171]">1. <?= __('Select an available date', 'spaces_mnu_plugin'); ?></h3>
                        <div id='calendar'>

                        </div>
                    </div>
                    <div class="tw-space-y-2">
                        <h3 class="tw-text-lg tw-text-[#717171]">2. <?= __('Select available time slots', 'spaces_mnu_plugin'); ?> <span id="slots-title"></span></h3>
                        <div id="slots" class="tw-block"></div>
                        <!-- Прелоадер -->
                        <div id="slots-preloader" class="tw-justify-center tw-items-center tw-w-full tw-h-40 tw-hidden">
                            <div class="tw-loader tw-border-4 tw-border-[#E2474C] tw-border-t-transparent tw-rounded-full tw-w-10 tw-h-10 tw-animate-spin"></div>
                        </div>
                    </div>
                    <div class="tw-space-y-2">
                        <?php if (is_user_logged_in()): ?>
                            <?php if (check_access($current_user_id, $resource_access)): ?>
                                <h3 class="tw-text-lg tw-text-[#717171]">3. <?= __('Fill in the fields', 'spaces_mnu_plugin'); ?></h3>

                                <form id="booking-form">
                                    <div id="additional-fields">
                                        <!-- <label for="booking-reason"><?= __('Reason', 'spaces_mnu_plugin'); ?>:</label> -->
                                        <textarea id="booking-reason" name="reason" maxlength="150" class="tw-w-full tw-p-2 tw-border tw-rounded" rows="4" placeholder="<?= __('Required booking reason', 'spaces_mnu_plugin'); ?>"></textarea>

                                        <!-- <label for="booking-comment"><?= __('Comment', 'spaces_mnu_plugin'); ?>:</label>
                                    <textarea id="booking-comment" name="comment" class="tw-w-full tw-p-2 tw-border tw-rounded" placeholder="<?= __('Any additional comments', 'spaces_mnu_plugin'); ?>"></textarea> -->
                                    </div>

                                    <!-- Кнопка отправки формы -->
                                    <button type="submit" id="book-slots-btn" class="tw-flex tw-gap-4 tw-justify-center tw-items-center tw-w-full tw-mt-4 tw-px-2 tw-py-4 tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-rounded">
                                        <svg id="book-slots-btn-loader" style="display: none;" class="tw-h-8 tw-w-8 tw-animate-spin tw-text-white" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="tw-text-gray-500"></path>
                                        </svg>
                                        <?= __('Book Selected Slots', 'spaces_mnu_plugin'); ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <h3 class="tw-text-lg tw-text-[#717171]"><?= __('You do not have access to this resource.', 'spaces_mnu_plugin'); ?></h3>
                            <?php endif; ?>

                        <?php else: ?>
                            <h3 class="tw-text-lg tw-text-[#717171]">3. <?= __('You need to sign in to book a resource.', 'spaces_mnu_plugin'); ?></h3>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <h3 class="tw-text-lg tw-text-[#717171]"><?= __('The resource is not available for booking', 'spaces_mnu_plugin'); ?></h3>
                <?php endif; ?>

            </div>


        </div>
    </div>




</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var availability_enabled = <?= json_encode($resource_availability_enabled); ?>;
        var resourceID = <?= json_encode($post_id); ?>;
        const preloader = document.getElementById('slots-preloader');
        const container = document.getElementById('slots');

        function showPreloader() {
            container.classList.remove('tw-block');
            container.classList.add('tw-hidden');
            preloader.classList.remove('tw-hidden');
            preloader.classList.add('tw-flex')
        }

        function hidePreloader() {
            setTimeout(() => {
                preloader.classList.add('tw-hidden');
                preloader.classList.remove('tw-flex');
                container.classList.add('tw-block');
                container.classList.remove('tw-hidden');
            }, 200)

        }

        if (availability_enabled == 'true') {
            var calendarEl = document.getElementById('calendar');
            var available_days = <?= json_encode($resource_available_days); ?>;
            var availableTime = <?= json_encode($resource_available_time); ?>;
            var currentLanguage = '<?= $current_language; ?>';

            var localeMap = {
                'ru_RU': 'ru',
                'kk': 'kk',
                'en_US': 'en'
            };
            var calendarLocale = localeMap[currentLanguage] || 'en';

            var kazakhLocale = {
                monthNames: [
                    "Қаңтар", "Ақпан", "Наурыз", "Сәуір", "Мамыр", "Маусым",
                    "Шілде", "Тамыз", "Қыркүйек", "Қазан", "Қараша", "Желтоқсан"
                ],
                monthNamesShort: [
                    "Қаң", "Ақп", "Нау", "Сәу", "Мам", "Мау", "Шіл", "Там", "Қыр", "Қаз", "Қар", "Жел"
                ],
                dayNames: [
                    "Жексенбі", "Дүйсенбі", "Сейсенбі", "Сәрсенбі", "Бейсенбі", "Жұма", "Сенбі"
                ],
                dayNamesShort: [
                    "Жек", "Дүй", "Сей", "Сәр", "Бей", "Жұм", "Сен"
                ],
                dayNamesMin: [
                    "Жк", "Дү", "Сс", "Ср", "Бс", "Жм", "Сб"
                ]
            };

            var russianLocale = {
                monthNames: [
                    "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
                    "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
                ],
                monthNamesShort: [
                    "Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"
                ],
                dayNames: [
                    "Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"
                ],
                dayNamesShort: [
                    "Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"
                ],
                dayNamesMin: [
                    "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
                ]
            };

            if (FullCalendar.globalLocales) {
                var kkLocale = FullCalendar.globalLocales.find(function(locale) {
                    return locale.code === "kk";
                });

                if (kkLocale) {
                    kkLocale.buttonText = {
                        prev: 'Алдыңғы',
                        next: 'Келесі',
                        today: 'Бүгін',
                        month: 'Ай',
                        week: 'Апта',
                        day: 'Күн',
                        list: 'Күн тәртібі'
                    };
                    kkLocale.allDayText = 'Күні бойы';
                    kkLocale.moreLinkText = 'тағы';
                    kkLocale.noEventsText = 'Көрсету үшін оқиғалар жоқ';
                    kkLocale.weekText = "Апта";
                }
            }

            const dayMap = {
                'понедельник': 'monday',
                'вторник': 'tuesday',
                'среда': 'wednesday',
                'четверг': 'thursday',
                'пятница': 'friday',
                'суббота': 'saturday',
                'воскресенье': 'sunday',
                'дүйсенбі': 'monday',
                'сейсенбі': 'tuesday',
                'сәрсенбі': 'wednesday',
                'бейсенбі': 'thursday',
                'жұма': 'friday',
                'сенбі': 'saturday',
                'жексенбі': 'sunday',
                'monday': 'monday',
                'tuesday': 'tuesday',
                'wednesday': 'wednesday',
                'thursday': 'thursday',
                'friday': 'friday',
                'saturday': 'saturday',
                'sunday': 'sunday'
            };


            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                contentHeight: 400,
                selectable: false,
                locale: calendarLocale,
                firstDay: 1,
                dayHeaderContent: function(arg) {
                    // Рендерим день недели из кастомного локаля
                    if (calendarLocale === 'kk') {
                        return kazakhLocale.dayNamesMin[arg.date.getDay()];
                    }
                },
                titleFormat: { // Формат заголовка месяца
                    year: 'numeric',
                    month: 'long'
                },
                datesSet: function(arg) {
                    var header = document.querySelector('.fc-toolbar-title');
                    var month = new Date(arg.view.currentStart).getMonth();
                    var year = new Date(arg.view.currentStart).getFullYear();
                    if (calendarLocale === 'kk') {

                        if (header) {
                            header.innerHTML = kazakhLocale.monthNames[month] + ' ' + year;
                        }
                    }
                    if (calendarLocale === 'ru') {
                        if (header) {
                            header.innerHTML = russianLocale.monthNames[month] + ' ' + year;
                        }

                    }

                    // var today = new Date();
                    // var todayCell = document.querySelector(`[data-date="${today.toISOString().split('T')[0]}"]`);
                    // if (todayCell) {
                    //     todayCell.classList.add('fc-selected-day');
                    //     todayCell.click();
                    // }

                },
                dateClick: function(info) {
                    var clickedDate = new Date(info.date);
                    // console.log('clickedDate: ', clickedDate);

                    var formattedDate = dateFormatter(clickedDate);
                    // console.log('calendarLocale: ', calendarLocale);


                    // Определяем язык и возвращаем день недели
                    var dayName = clickedDate.toLocaleString(calendarLocale === 'ru' ? 'ru-RU' : (calendarLocale === 'kk' ? 'kk-KZ' : 'en-US'), {
                        weekday: 'long'
                    }).toLowerCase(); // Приводим к нижнему регистру

                    // console.log('dayName: ', dayName);


                    // Переводим названия дней на английский с помощью карты dayMap
                    var englishDayName = dayMap[dayName];

                    // Отладка - проверяем английское название дня
                    // console.log("englishDayName:", englishDayName);

                    // Проверяем, если день исключен
                    if (!available_days.includes(englishDayName)) {
                        showToast("<?php echo __('Not available', 'spaces_mnu_plugin'); ?>");
                        return;
                    }

                    // console.log("English day name:", englishDayName);
                    // console.log("Available time object:", availableTime);
                    // console.log("Checking if availableTime[englishDayName] exists:", availableTime[englishDayName]);


                    // Получаем занятые слоты для выбранного дня
                    if (availableTime[englishDayName] && availableTime[englishDayName]['from'] && availableTime[englishDayName]['to']) {
                        showPreloader();
                        fetchBookedSlots(formattedDate, function(bookedSlots) {
                            // console.log('bookedSlots: ', bookedSlots);

                            let slotsContainer = document.getElementById('slots');
                            var fromTime = availableTime[englishDayName]['from'];
                            var toTime = availableTime[englishDayName]['to'];
                            var slots = generateTimeSlots(fromTime, toTime, 30);
                            let slotsTitle = document.getElementById('slots-title');
                            slotsTitle.innerHTML = formattedDate;

                            // Отправляем занятые слоты в функцию для визуализации
                            // console.log('slots: ', slots, 'bookedSlots: ', bookedSlots);
                            insertSlotsIntoContainer(slotsContainer, slots, bookedSlots, clickedDate);
                        });
                        hidePreloader();
                    } else {
                        showToast("No available time slots for this day.");
                    }

                    // Удаляем предыдущие выделения
                    document.querySelectorAll('.fc-selected-day').forEach(function(el) {
                        el.classList.remove('fc-selected-day');
                    });

                    // Добавляем класс для выбранного дня
                    info.dayEl.classList.add('fc-selected-day');
                },
                validRange: {
                    start: new Date()
                },
                dayCellClassNames: function(arg) {
                    var day = arg.date.getDay();
                    var dayName = new Date(arg.date).toLocaleDateString(calendarLocale === 'ru' ? 'ru-RU' : (calendarLocale === 'kk' ? 'kk-KZ' : 'en-US'), {
                        weekday: 'long'
                    }).toLowerCase();

                    var englishDayName = dayMap[dayName];

                    if (!available_days.includes(englishDayName)) {
                        return ['fc-red-bg']; // Красный фон для исключенных дней
                    }

                    if (availableTime[englishDayName]) {
                        return ['fc-green-bg']; // Зеленый фон для доступных дней
                    }

                    return []; // Стандартное оформление для всех остальных дней
                }
            });

            calendar.render();

            function fetchBookedSlots(date, callback) {
                var bookingData = {
                    action: 'get_booked_slots',
                    resource_id: resourceID,
                    date: date,
                    nonce: spacesMnuData.nonce
                };
                // console.log('bookingData: ', bookingData);


                // console.log("Отправка запроса: ", bookingData);

                jQuery.post(spacesMnuData.ajax_url, bookingData, function(response) {
                    // console.log("Ответ сервера: ", response);
                    if (response.success) {
                        callback(response.data.booked_slots);
                    } else {
                        // console.log("Error fetching booked slots:", response);
                        callback([]);
                    }
                });
                // .fail(function(jqXHR, textStatus, errorThrown) {
                //     console.log("Ошибка AJAX: ", textStatus, errorThrown);
                // });
            }

            function insertSlotsIntoContainer(slotsContainer, slots, bookedSlots, clickedDate) {
                // console.log('bookedSlots: ', bookedSlots);
                // Получаем текущее время в UTC+5
                const now = new Date(new Date().toLocaleString("en-US", {
                    timeZone: "Asia/Yekaterinburg"
                })); // UTC+5

                // Преобразуем clickedDate в объект Date
                const clickedDateObj = new Date(clickedDate);

                // Проверяем, совпадает ли clickedDate с сегодняшней датой
                const isToday = clickedDateObj.toDateString() === now.toDateString();

                // HTML для списка слотов
                let slotsHtml = '<ul class="tw-flex tw-flex-wrap tw-justify-center tw-items-center tw-gap-2 tw-m-0">';
                slots.forEach(function(slot) {
                    // Приведение слота к единому формату
                    const normalizedSlot = slot.trim();

                    // console.log('normalizedSlot: ', normalizedSlot);

                    // Функция для преобразования времени в минуты с начала дня
                    const timeToMinutes = (time) => {
                        const [hours, minutes] = time.split(':').map(Number);
                        return hours * 60 + minutes;
                    };

                    // Функция для проверки пересечения двух интервалов
                    const isOverlap = (slotA, slotB) => {
                        const [startA, endA] = slotA.split('-').map(timeToMinutes);
                        const [startB, endB] = slotB.split('-').map(timeToMinutes);

                        return startA < endB && endA > startB;
                    };
                    const isBooked = bookedSlots.some((bookedSlot) => isOverlap(normalizedSlot, bookedSlot));
                    // console.log(isBooked);

                    // Проверка, забронирован ли слот
                    // const isBooked = bookedSlots.map(s => s.trim()).includes(normalizedSlot);

                    // Разбиваем диапазон времени на начало и конец
                    const [startTime, endTime] = normalizedSlot.split('-');
                    const [startHours, startMinutes] = startTime.split(':').map(Number);
                    const [endHours, endMinutes] = endTime.split(':').map(Number);

                    // Создаем Date объекты для начала и конца слота, используя clickedDate для года, месяца и дня
                    const slotStartTime = new Date(clickedDateObj.getFullYear(), clickedDateObj.getMonth(), clickedDateObj.getDate(), startHours, startMinutes);
                    const slotEndTime = new Date(clickedDateObj.getFullYear(), clickedDateObj.getMonth(), clickedDateObj.getDate(), endHours, endMinutes);

                    // Если clickedDate — это сегодня, проверяем, наступил ли слот
                    const isPast = isToday && slotStartTime <= now;

                    // Определение CSS-класса для слота
                    const slotClass = (isBooked || isPast) ?
                        "booked-slot tw-bg-[#E2474C1a] tw-cursor-not-allowed" :
                        "tw-bg-[#d1d1d1]";

                    // Добавляем HTML для слота
                    slotsHtml += `<li class="tw-w-[100px] tw-text-sm ${slotClass} tw-m-0 tw-p-2 tw-rounded-lg tw-flex tw-justify-center tw-items-center slot" data-slot="${slot}" ${isBooked || isPast ? 'data-booked="true"' : ''}>${slot}</li>`;
                });
                slotsHtml += '</ul>';

                slotsContainer.innerHTML = slotsHtml;

                // Добавляем слушатели на клики по свободным слотам
                document.querySelectorAll('.slot:not([data-booked="true"])').forEach(function(slotElement) {
                    slotElement.addEventListener('click', function() {
                        slotElement.classList.toggle('selected-slot');
                    });
                });
            }


            function generateTimeSlots(startTime, endTime, interval) {
                var start = new Date('1970-01-01T' + startTime);
                var end = new Date('1970-01-01T' + endTime);
                var slots = [];

                while (start < end) {
                    var nextSlot = new Date(start.getTime() + interval * 60000); // интервал в минутах
                    var slotText = start.toTimeString().substring(0, 5) + '-' + nextSlot.toTimeString().substring(0, 5);
                    slots.push(slotText);
                    start = nextSlot;
                }

                return slots;
            }

            function dateFormatter(date) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Месяцы начинаются с 0, поэтому добавляем 1
                const year = date.getFullYear();
                const formattedDate = `${day}.${month}.${year}`;

                return formattedDate;

            }

            function showToast(message) {
                Toastify({
                    text: message,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#E2474CCC",
                    },
                }).showToast();
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productCarousel = new Carousel(document.getElementById('productCarousel'), {
            transition: 'slide',
            preload: 1,
            infinite: true,
            Dots: false,
        });
        Fancybox.bind('[data-fancybox="gallery"]', {
            compact: false,
            idle: false,
            dragToClose: false,
            contentClick: () =>
                window.matchMedia('(max-width: 578px), (max-height: 578px)').matches ?
                'toggleMax' : 'toggleCover',

            animated: false,
            showClass: false,
            hideClass: false,

            Hash: false,
            Thumbs: false,

            Toolbar: {
                display: {
                    left: [],
                    middle: [],
                    right: ['close'],
                },
            },

            Carousel: {
                transition: 'fadeFast',
                preload: 3,
            },

            Images: {
                zoom: false,
                Panzoom: {
                    panMode: 'mousemove',
                    mouseMoveFactor: 1.1,
                },
            },
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var bookingForm = document.getElementById('booking-form');
        let btn = document.querySelector('#book-slots-btn');
        let btnLoader = document.querySelector('#book-slots-btn-loader');

        bookingForm && bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (btn) {
                btn.disabled = true;
                btn.style.cursor = 'not-allowed';
            }

            if (btnLoader) {
                btnLoader.style.display = 'block';
            }
            // else {
            //     console.error('Loader SVG not found!');
            // }



            var selectedSlots = Array.from(document.querySelectorAll('.selected-slot')).map(function(slotElement) {
                return slotElement.getAttribute('data-slot');
            });

            if (selectedSlots.length === 0) {
                Toastify({
                    text: '<?php echo esc_js(__('Please select at least one slot.', 'spaces_mnu_plugin')); ?>', // 'Please select at least one slot.',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#E2474CCC",
                    },
                }).showToast();
                btnLoaderDisable();
                return;
            }

            var reason = document.getElementById('booking-reason').value;
            if (!reason) {
                Toastify({
                    text: '<?php echo esc_js(__('Please enter a reason for booking.', 'spaces_mnu_plugin')); ?>', // 'Please enter a reason for booking.',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#E2474CCC",
                    },
                }).showToast();
                btnLoaderDisable();
                return;
            }
            // var comment = document.getElementById('booking-comment').value;
            var resourceID = <?= json_encode($post_id); ?>; // ID ресурса
            var selectedDate = document.getElementById('slots-title').textContent; // Дата, выбранная пользователем

            var bookingData = {
                action: 'create_booking',
                resource_id: resourceID,
                selected_slots: selectedSlots,
                reason: reason,
                // comment: comment,
                date: selectedDate,
                nonce: spacesMnuData.nonce
            };

            jQuery.post(spacesMnuData.ajax_url, bookingData, function(response) {
                if (response.success) {
                    Toastify({
                        text: '<?php echo esc_js(__('Booking(s) created successfully!', 'spaces_mnu_plugin')); ?>' || spacesMnuData.i18n.bookingCreated,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#00b09b",
                        },
                    }).showToast();
                    btnLoaderDisable();
                } else {
                    let errorMessage = response.data.message;
                    if (response.data.errors && response.data.errors.length > 0) {
                        errorMessage += ": " + response.data.errors.join(', ');
                    }
                    Toastify({
                        text: '<?php echo esc_js(__('Failed to create booking(s).', 'spaces_mnu_plugin')); ?>' || errorMessage,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#E2474CCC",
                        },
                    }).showToast();
                    btnLoaderDisable();
                }
            });

            function btnLoaderDisable() {
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                btnLoader.style.display = 'none';
            }
        });
    });
</script>


<?php
get_footer();
