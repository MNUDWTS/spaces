// assets/js/staff.js

jQuery(document).ready(function ($) {

    // Переменные для кеширования данных фильтрации и дебаунса
    let cachedStaffData = null;
    let debounceTimeout;

    // Функция для фильтрации сотрудников с дебаунсом
    function filterStaff(searchQuery = '', department = '') {
        // Проверяем кеш, чтобы избежать лишнего запроса
        if (cachedStaffData && cachedStaffData.search === searchQuery && cachedStaffData.department === department) {
            $('#staff-list').html(cachedStaffData.html);
            return;
        }

        // Отправляем AJAX-запрос на фильтрацию сотрудников
        $.ajax({
            url: staff_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_staff',
                search: searchQuery,
                department: department,
                nonce: staff_ajax_obj.nonce
            },
            beforeSend: function () {
                $('#staff-list').html('<p>' + staff_ajax_obj.i18n.loading_text + '</p>');
            },
            success: function (response) {
                if (response.success) {
                    // Сохраняем данные в кеш и отображаем результат
                    cachedStaffData = {
                        search: searchQuery,
                        department: department,
                        html: response.data
                    };
                    $('#staff-list').html(response.data);
                } else {
                    $('#staff-list').html('<p>' + staff_ajax_obj.i18n.no_staff_text + '</p>');
                }
            },
            error: function () {
                $('#staff-list').html('<p>' + staff_ajax_obj.i18n.error_text + '</p>');
            }
        });
    }

    // Запускаем фильтрацию с дебаунсом
    function debounceFilterStaff(searchQuery, department) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function () {
            filterStaff(searchQuery, department);
        }, 300);
    }

    // Триггер для загрузки всех сотрудников при загрузке страницы
    // filterStaff();
    // Триггер для загрузки всех сотрудников при загрузке страницы
    const initialDepartment = $('select[name="department"]').val(); // Получаем текущий выбранный департамент
    filterStaff('', initialDepartment); // Передаём текущий департамент в фильтр


    // Обработка отправки формы для фильтрации
    $('#staff-filter-form').on('submit', function (e) {
        e.preventDefault();
        let searchQuery = $('input[name="search"]').val();
        let department = $('select[name="department"]').val();
        debounceFilterStaff(searchQuery, department);
    });

    // Обработчик для кнопки сохранения департамента
    $(document).on('click', '.save-department-btn', function (e) {
        e.preventDefault();
        // alert('save-department-btn clicked');

        let $button = $(this);
        let userId = $button.closest('[data-user-id]').data('user-id');
        let selectedDepartment = $button.siblings('.department-select').val();

        // AJAX-запрос для сохранения департамента
        $.ajax({
            url: staff_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'save_user_department',
                nonce: staff_ajax_obj.save_user_department_nonce,
                user_id: userId,
                department: selectedDepartment
            },
            success: function (response) {
                if (response.success) {
                    // Используем Toastify для отображения сообщения
                    Toastify({
                        text: response.data.message || "Department updated successfully!",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: { background: "#28a745" }
                    }).showToast();

                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                } else {
                    Toastify({
                        text: response.data.message || "Failed to update department.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" }
                    }).showToast();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                Toastify({
                    text: staff_ajax_obj.i18n.error_text,
                    duration: 2000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: { background: "#dc3545" }
                }).showToast();
            }
        });
    });

    $(document).on('click', '.save-mnu-role-btn', function(e){
        e.preventDefault();
        // alert('save-mnu-role-btn clicked');

        let $button = $(this);
        let userId = $button.closest('[data-user-id]').data('user-id');
        let selectedMnuRole = $button.siblings('.mnu-role-select').val();

        $.ajax({
            url: staff_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'save_user_mnu_role',
                nonce: staff_ajax_obj.save_user_mnu_role_nonce,
                user_id: userId,
                mnu_role: selectedMnuRole
            },
            success: function (response) {
                if (response.success) {
                    // Используем Toastify для отображения сообщения
                    Toastify({
                        text: response.data.message || "Role updated successfully!",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: { background: "#28a745" }
                    }).showToast();

                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                } else {
                    Toastify({
                        text: response.data.message || "Failed to update role.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" }
                    }).showToast();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                Toastify({
                    text: staff_ajax_obj.i18n.error_text,
                    duration: 2000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: { background: "#dc3545" }
                }).showToast();
            }
        });
        
    })

});


