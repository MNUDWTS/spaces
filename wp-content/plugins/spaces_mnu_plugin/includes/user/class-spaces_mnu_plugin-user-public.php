<?php
if (!defined('ABSPATH')) {
    exit;
}

class Spaces_MNU_Plugin_User_Public
{
    public function __construct()
    {
        add_shortcode('spaces_mnu_user_profile_form', [$this, 'render_user_profile_form']);
        add_filter('template_include', array($this, 'add_user_profile_template'));

        add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 20);
        add_action('wp_ajax_save_onboarding_data', [$this, 'save_onboarding_data']);

        add_action('wp_ajax_filter_staff', [$this, 'filter_staff_ajax']);

        add_action('wp_ajax_save_user_department', [$this, 'save_user_department']);
        add_action('wp_ajax_save_user_mnu_role', [$this, 'save_user_mnu_role']);

        add_action('wp_ajax_filter_students', [$this, 'filter_students_ajax']);
    }

    public function register_scripts()
    {
        if (is_page('profile')) {
            $current_user = wp_get_current_user();
            $mnu_role = get_user_meta($current_user->ID, 'mnu_role', true);

            wp_enqueue_script('toastify-js', SPACES_MNU_PLUGIN_URL . 'assets/js/toastify.min.js', [], SPACES_MNU_PLUGIN_VERSION, true);
            wp_enqueue_script('fullcalendar-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fullcalendar.main.min.js', [], SPACES_MNU_PLUGIN_VERSION, true);
            wp_enqueue_script('fullcalendar-locales-all-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fullcalendar.locales-all.min.js', [], SPACES_MNU_PLUGIN_VERSION, true);
            wp_enqueue_script('fancybox-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fancybox.min.js', [], SPACES_MNU_PLUGIN_VERSION, true);
            wp_enqueue_script('carousel-js', SPACES_MNU_PLUGIN_URL . 'assets/js/carousel.min.js', [], SPACES_MNU_PLUGIN_VERSION, true);
            wp_enqueue_style('fullcalendar-css', SPACES_MNU_PLUGIN_URL . 'assets/css/fullcalendar.main.min.css');
            wp_enqueue_style('toastify-css', SPACES_MNU_PLUGIN_URL . 'assets/css/toastify.min.css');
            wp_enqueue_style('carousel-css', SPACES_MNU_PLUGIN_URL . 'assets/css/carousel.min.css');
            wp_enqueue_style('fancybox-css', SPACES_MNU_PLUGIN_URL . 'assets/css/fancybox.min.css');
            wp_enqueue_script('bookings-js', SPACES_MNU_PLUGIN_URL . 'assets/js/bookings.js', ['jquery', 'fullcalendar-js', 'toastify-js'], SPACES_MNU_PLUGIN_VERSION, true);
            wp_localize_script('bookings-js', 'spacesMnuData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('spaces_mnu_nonce'),
                'current_users_role' => $mnu_role,
                'current_language' => get_locale(),
                'i18n' => [
                    'bookingCancelled' => __('Booking(s) cancelled successfully!', 'spaces_mnu_plugin'),
                    'cancellationFailed' => __('Failed to cancel booking(s).', 'spaces_mnu_plugin'),
                    'enterCancellationReason' => __('Please enter a reason for cancellation.', 'spaces_mnu_plugin'),
                    'bookingsForDate' => __('Bookings for date', 'spaces_mnu_plugin'),
                    'noBookingsFound' => __('No bookings found for this date.', 'spaces_mnu_plugin'),
                    'cancel' => __('Cancel', 'spaces_mnu_plugin'),
                    'requestedBy' => __('Requested By', 'spaces_mnu_plugin'),
                    'resource' => __('Resource', 'spaces_mnu_plugin'),
                    'slots' => __('Slots', 'spaces_mnu_plugin'),
                    'reason' => __('Reason for booking', 'spaces_mnu_plugin'),
                    'comment' => __('Reason for cancellation', 'spaces_mnu_plugin'),
                    'actions' => __('Actions', 'spaces_mnu_plugin'),
                ]
            ]);

            wp_enqueue_script('staff-ajax', SPACES_MNU_PLUGIN_URL . 'assets/js/staff.js', ['jquery'], null, true);
            wp_localize_script('staff-ajax', 'staff_ajax_obj', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('staff_filter_nonce'),
                'save_user_department_nonce' => wp_create_nonce("save_user_department_nonce"),
                'save_user_mnu_role_nonce' => wp_create_nonce("save_user_mnu_role_nonce"),
                'i18n' => [
                    'loading_text' => __('Loading...', 'spaces_mnu_plugin'),
                    'error_text' => __('An error occurred. Please try again.', 'spaces_mnu_plugin'),
                    'no_staff_text' => __('No staff members found.', 'spaces_mnu_plugin')
                ]
            ]);
            wp_enqueue_script('students-ajax', SPACES_MNU_PLUGIN_URL . 'assets/js/students.js', ['jquery'], null, true);
            wp_localize_script('students-ajax', 'students_ajax_obj', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('students_filter_nonce'),
                'save_user_mnu_role_nonce' => wp_create_nonce("save_user_mnu_role_nonce"),
                'i18n' => [
                    'loading_text' => __('Loading...', 'spaces_mnu_plugin'),
                    'error_text' => __('An error occurred. Please try again.', 'spaces_mnu_plugin'),
                    'no_students_text' => __('No students found.', 'spaces_mnu_plugin')
                ]
            ]);
        }
    }

    public function render_edit_resource_form($resource_id)
    {
        if (!is_user_logged_in()) {
            return '<div class="tw-h-[80vh] tw-bg-red-100 tw-text-red-800 tw-p-4 tw-rounded-md tw-text-center">
                        ' . __('You must be logged in to edit this resource.', 'spaces_mnu_plugin') . '
                    </div>';
        }
        $post = get_post($resource_id);
        if (!$post || $post->post_type !== 'resource') {
            return __('Resource not found.', 'spaces_mnu_plugin');
        }
        ob_start();
        include 'templates/resource-static-fields-meta-box.php';
        include 'templates/resource-localized-fields-meta-box.php';
        return ob_get_clean();
    }

    function add_user_profile_template($template)
    {
        if (is_page('profile')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/page-profile.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }

    public function render_user_profile_form()
    {
        if (!is_user_logged_in()) {
            return '<div class="tw-h-[80vh] tw-bg-red-100 tw-text-red-800 tw-p-4 tw-rounded-md tw-text-center">
                        ' . __('You must be logged in to edit your profile.', 'spaces_mnu_plugin') . '
                    </div>';
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spaces_mnu_user_profile_nonce']) && wp_verify_nonce($_POST['spaces_mnu_user_profile_nonce'], 'spaces_mnu_save_user_profile')) {
            $this->save_user_profile(get_current_user_id());
        }
        ob_start();
        if (isset($_GET['profile_updated']) && $_GET['profile_updated'] === 'true') {
            echo '<div class="tw-text-green-500 tw-font-semibold tw-mb-4">' . __('Profile updated successfully!', 'spaces_mnu_plugin') . '</div>';
        }
?>

        <div class="profile-page-container tw-max-w-7xl tw-w-full tw-mx-auto tw-py-4 tw-px-2 md:tw-p-8">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-12 tw-gap-4 md:tw-gap-8">
                <!-- Sidebar -->
                <?php include 'templates/sidebar.php';
                $user = wp_get_current_user();
                $user_role = get_the_author_meta('mnu_role', $user->ID);
                $active_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'calendar';
                ?>

                <!-- Content Area -->
                <div class="md:tw-col-span-8 tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl">
                    <!-- Edit Profile Section -->
                    <div id="edit-profile" class="content-section <?= $active_section === 'edit-profile' ? '' : 'tw-hidden'; ?>">
                        <?php include 'templates/edit_profile_form.php'; ?>
                    </div>

                    <!-- Calendar Section -->
                    <div id="calendar" class="content-section <?= $active_section === 'calendar' ? '' : 'tw-hidden'; ?>">
                        <?php include 'templates/calendar.php'; ?>
                    </div>
                    <?php if (strpos($user_role, 'management_staff') !== false && $user_role !== 'security_staff'): ?>
                        <!-- Bookings Section -->
                        <div id="bookings" class="content-section <?= $active_section === 'bookings' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/bookings.php'; ?>
                        </div>

                        <!-- Resources Section -->
                        <div id="resources" class="content-section <?= $active_section === 'resources' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/resources.php'; ?>
                        </div>

                        <!-- Events Section -->
                        <div id="events" class="content-section <?= $active_section === 'events' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/events.php'; ?>
                        </div>

                        <!-- Resource Create Section -->
                        <div id="resource-create" class="content-section <?= $active_section === 'resource-create' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/resource-create.php'; ?>
                        </div>

                        <!-- Resource Edit Section -->
                        <div id="resource-edit" class="content-section <?= $active_section === 'resource-edit' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/resource-edit.php'; ?>
                        </div>

                        <!-- Event Create Section -->
                        <div id="event-create" class="content-section <?= $active_section === 'event-create' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/event-create.php'; ?>
                        </div>

                        <!-- Event Edit Section -->
                        <div id="event-edit" class="content-section <?= $active_section === 'event-edit' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/event-edit.php'; ?>
                        </div>


                    <?php endif ?>
                    <?php if ($user_role === 'senior_management_staff' || $user_role === 'security_staff'): ?>
                        <!-- All Bookings Section -->
                        <div id="all-bookings" class="content-section <?= $active_section === 'bookings' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/all-bookings.php'; ?>
                        </div>
                    <?php endif ?>
                    <?php if (strpos($user_role, 'staff') !== false): ?>
                        <!-- Staff Section -->
                        <div id="staff" class="content-section <?= $active_section === 'staff' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/staff.php'; ?>
                        </div>
                    <?php endif ?>
                    <?php if (strpos($user_role, 'staff') !== false): ?>
                        <!-- Students Section -->
                        <div id="students" class="content-section <?= $active_section === 'students' ? '' : 'tw-hidden'; ?>">
                            <?php include 'templates/students.php'; ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const menuItems = document.querySelectorAll('.sidebar-menu-item');
                const contentSections = document.querySelectorAll('.content-section');
                const editButton = document.getElementById('edit-button');
                const createResourceButton = document.getElementById('resource-create-button');
                const editResourceButtons = document.querySelectorAll('.edit-resource-button');
                const createEventButton = document.getElementById('event-create-button');
                const editEventButtons = document.querySelectorAll('.edit-event-button');

                function showSection(sectionName, shouldScroll = false) {
                    // Скрываем все секции контента
                    contentSections.forEach(section => section.classList.add('tw-hidden'));
                    // Показываем нужную секцию
                    const targetSection = document.getElementById(sectionName);
                    if (targetSection) {
                        targetSection.classList.remove('tw-hidden');
                        if (sectionName === 'bookings')
                            window.bookingsCalendar();
                        if (sectionName === 'all-bookings')
                            window.allBookingsCalendar();
                        if (sectionName === 'calendar')
                            window.myBookingsCalendar();
                    } else {
                        console.error(`Section with ID ${sectionName} not found.`);
                    }
                    if (shouldScroll && window.innerWidth <= 768) {
                        document.getElementById(sectionName).scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }

                function setActiveMenuItem(sectionName) {
                    menuItems.forEach(item => {
                        const target = item.getAttribute('data-target');

                        if (target === sectionName) {
                            item.classList.add('active');
                            item.classList.remove('not-active');
                        } else {
                            item.classList.remove('active');
                            item.classList.add('not-active');
                        }
                        if (sectionName === 'edit-profile') {
                            editButton.classList.add('active');
                            editButton.classList.remove('not-active');
                        } else {
                            editButton.classList.add('not-active');
                            editButton.classList.remove('active');
                        }
                    });
                }

                // Навешиваем обработчики на элементы меню
                menuItems.forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = this.getAttribute('data-target');

                        // Обновляем URL с помощью History API
                        const newUrl = `${window.location.pathname}?section=${target}`;
                        window.history.replaceState({
                            section: target
                        }, '', newUrl);

                        // Показываем выбранную секцию и устанавливаем активный элемент меню
                        showSection(target, true);
                        setActiveMenuItem(target);
                    });
                });

                // Обработчик клика по кнопке редактирования профиля
                if (editButton) {
                    editButton.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Обновляем URL и показываем нужную секцию
                        const newUrl = `${window.location.pathname}?section=edit-profile`;
                        window.history.replaceState({
                            section: 'edit-profile'
                        }, '', newUrl);

                        showSection('edit-profile', true);
                        setActiveMenuItem('edit-profile');
                    });
                }

                // Обработчик клика по кнопке добавления нового ресурса
                if (createResourceButton) {
                    createResourceButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Обновляем URL и показываем нужную секцию
                        const newUrl = `${window.location.pathname}?section=resource-create`;
                        window.history.replaceState({
                            section: 'resource-create'
                        }, '', newUrl);
                        fetchResourceCreateForm();
                        showSection('resource-create');
                    });
                }

                if (createEventButton) {
                    createEventButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Обновляем URL и показываем нужную секцию
                        const newUrl = `${window.location.pathname}?section=event-create`;
                        window.history.replaceState({
                            section: 'event-create'
                        }, '', newUrl);
                        fetchEventCreateForm();
                        showSection('event-create');
                    });
                }

                editResourceButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const resourceId = this.getAttribute('data-resource-id');
                        const newUrl = `${window.location.pathname}?section=resource-edit`;
                        window.history.replaceState({
                            section: 'resource-edit'
                        }, '', newUrl);

                        fetchResourceEditForm(resourceId);
                        showSection('resource-edit');
                    });
                });

                editEventButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const eventId = this.getAttribute('data-event-id');
                        const newUrl = `${window.location.pathname}?section=event-edit`;
                        window.history.replaceState({
                            section: 'event-edit'
                        }, '', newUrl);

                        fetchEventEditForm(eventId);
                        showSection('event-edit');
                    });
                });


                function fetchResourceEditForm(resourceId) {
                    const resourceEditSection = document.getElementById('resource-edit-form-container');
                    jQuery.ajax({
                        url: spacesMnuPlugin.ajaxurl,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'get_resource_edit_form',
                            resource_id: resourceId,
                            nonce: spacesMnuPlugin.nonce.editForm
                        },
                        success: function(response) {
                            if (response.success) {
                                // Вставляем форму в контейнер
                                resourceEditSection.innerHTML = response.data.form_html;

                                // Показываем секцию редактирования ресурса
                                resourceEditSection.classList.remove('tw-hidden');

                                // Инициализируем скрипты для формы редактирования
                                window.initResourceEditFormScripts();
                            } else {
                                // alert(response.data.message || '<?= __('An error occurred.', 'spaces_mnu_plugin'); ?>');
                                Toastify({
                                    text: response.data.message || '<?= __('An error occurred.', 'spaces_mnu_plugin'); ?>',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    style: {
                                        background: "#E2474CCC",
                                    },
                                }).showToast();
                            }
                        },
                        error: function() {
                            // alert('<?= __('An error occurred while loading the edit form.', 'spaces_mnu_plugin'); ?>');
                            Toastify({
                                text: '<?= __('An error occurred while loading the edit form.', 'spaces_mnu_plugin'); ?>',
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                style: {
                                    background: "#E2474CCC",
                                },
                            }).showToast();
                        }
                    });
                }


                function fetchResourceCreateForm() {
                    const resourceCreateSection = document.getElementById('resource-create-form-container');
                    resourceCreateSection.innerHTML = '';
                    jQuery.ajax({
                        url: spacesMnuPlugin.ajaxurl,
                        method: 'POST', // Изменено с 'POST' на 'GET'
                        dataType: 'json',
                        data: {
                            action: 'load_resource_create_form',
                            nonce: spacesMnuPlugin.nonce.createForm
                        },
                        success: function(response) {
                            if (response.success) {
                                // Вставляем форму в контейнер
                                resourceCreateSection.innerHTML = response.data.form_html;

                                // Обновляем nonce в JavaScript
                                spacesMnuPlugin.nonce.createForm = response.data.nonce;

                                // Показываем секцию создания ресурса
                                resourceCreateSection.classList.remove('tw-hidden');
                                window.initResourceEditFormScripts();
                            } else {
                                // alert(response.data.message || 'Произошла ошибка.');
                                Toastify({
                                    text: response.data.message || 'Произошла ошибка.',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    style: {
                                        background: "#E2474CCC",
                                    },
                                }).showToast();
                            }
                        },
                        error: function() {
                            // alert('Произошла ошибка при загрузке формы создания.');
                            Toastify({
                                text: '<?= __('An error occurred while loading the create form.', 'spaces_mnu_plugin'); ?>',
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                style: {
                                    background: "#E2474CCC",
                                },
                            }).showToast();
                        }
                    });
                }



                function fetchEventEditForm(eventId) {
                    const eventEditSection = document.getElementById('event-edit-form-container');
                    jQuery.ajax({
                        url: spacesMnuPluginEvent.ajaxurl,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'get_event_edit_form',
                            event_id: eventId,
                            nonce: spacesMnuPluginEvent.nonce.editForm
                        },
                        success: function(response) {
                            if (response.success) {
                                // Вставляем форму в контейнер
                                eventEditSection.innerHTML = response.data.form_html;

                                // Показываем секцию редактирования ресурса
                                eventEditSection.classList.remove('tw-hidden');

                                // Инициализируем скрипты для формы редактирования
                                window.initEventEditFormScripts();
                            } else {
                                // alert(response.data.message || '<?= __('An error occurred.', 'spaces_mnu_plugin'); ?>');
                                Toastify({
                                    text: response.data.message || '<?= __('An error occurred.', 'spaces_mnu_plugin'); ?>',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    style: {
                                        background: "#E2474CCC",
                                    },
                                }).showToast();
                            }
                        },
                        error: function() {
                            // alert('<?= __('An error occurred while loading the edit form.', 'spaces_mnu_plugin'); ?>');
                            Toastify({
                                text: '<?= __('An error occurred while loading the edit form.', 'spaces_mnu_plugin'); ?>',
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                style: {
                                    background: "#E2474CCC",
                                },
                            }).showToast();
                        }
                    });
                }



                function fetchEventCreateForm() {
                    const eventCreateSection = document.getElementById('event-create-form-container');
                    eventCreateSection.innerHTML = '';
                    jQuery.ajax({
                        url: spacesMnuPluginEvent.ajaxurl,
                        method: 'POST', // Изменено с 'POST' на 'GET'
                        dataType: 'json',
                        data: {
                            action: 'load_event_create_form',
                            nonce: spacesMnuPluginEvent.nonce.createForm
                        },
                        success: function(response) {
                            if (response.success) {
                                // Вставляем форму в контейнер
                                eventCreateSection.innerHTML = response.data.form_html;

                                // Обновляем nonce в JavaScript
                                spacesMnuPluginEvent.nonce.createForm = response.data.nonce;

                                // Показываем секцию создания ресурса
                                eventCreateSection.classList.remove('tw-hidden');
                                window.initEventEditFormScripts();
                            } else {
                                // alert(response.data.message || 'Произошла ошибка.');
                                Toastify({
                                    text: response.data.message || 'Произошла ошибка.',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    style: {
                                        background: "#E2474CCC",
                                    },
                                }).showToast();
                            }
                        },
                        error: function() {
                            // alert('Произошла ошибка при загрузке формы создания.');
                            Toastify({
                                text: '<?= __('An error occurred while loading the create form.', 'spaces_mnu_plugin'); ?>',
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                style: {
                                    background: "#E2474CCC",
                                },
                            }).showToast();
                        }
                    });
                }

                // Слушатель события popstate для обработки нажатий кнопки "назад" в браузере
                window.addEventListener('popstate', function(event) {
                    if (event.state && event.state.section) {
                        showSection(event.state.section);
                    }
                });

                // Проверяем параметр "section" в URL при загрузке страницы
                const urlParams = new URLSearchParams(window.location.search);
                const section = urlParams.get('section') || 'calendar';
                showSection(section);
                setActiveMenuItem(section);

            });
        </script>

        <!-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.bookingsCalendar === 'function') {
                    window.bookingsCalendar();
                } else {
                    console.error('bookingsCalendar function not found.');
                }
            });
        </script> -->

    <?php
        return ob_get_clean();
    }


    private function save_user_profile($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }
        if (
            !isset($_POST['spaces_mnu_user_profile_nonce']) ||
            !wp_verify_nonce($_POST['spaces_mnu_user_profile_nonce'], 'spaces_mnu_save_user_profile')
        ) {
            return;
        }
        if (isset($_POST['first_name']) && strlen(trim($_POST['first_name'])) >= 2) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
        }
        if (isset($_POST['last_name']) && strlen(trim($_POST['last_name'])) >= 2) {
            update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
        }
        if (isset($_POST['job_title'])) {
            update_user_meta($user_id, 'job_title', sanitize_text_field($_POST['job_title']));
        }
        if (isset($_POST['phone'])) {
            update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
        }
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            $this->handle_avatar_upload($user_id);
        }
    }

    private function handle_avatar_upload($user_id)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        $uploaded_file = $_FILES['avatar'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2 * 1024 * 1024;
        if (!in_array($uploaded_file['type'], $allowed_types)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Spaces MNU Plugin: Invalid avatar file type - ' . $uploaded_file['type']);
            }
            return;
        }
        if ($uploaded_file['size'] > $max_file_size) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Spaces MNU Plugin: Avatar file size exceeds limit - ' . $uploaded_file['size']);
            }
            return;
        }
        $upload_overrides = ['test_form' => false];
        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            update_user_meta($user_id, 'custom_avatar', esc_url($movefile['url']));
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Spaces MNU Plugin: Avatar upload error - ' . $movefile['error']);
            }
        }
    }

    public function filter_staff_ajax()
    {
        check_ajax_referer('staff_filter_nonce', 'nonce');
        $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $department_filter = isset($_POST['department']) ? sanitize_text_field($_POST['department']) : '';
        $args = [
            'meta_query' => [
                [
                    'key'     => 'mnu_role',
                    'value'   => 'staff',
                    'compare' => 'LIKE'
                ],
                [
                    'relation' => 'OR',
                    [
                        'key'     => 'first_name',
                        'value'   => esc_attr($search_query),
                        'compare' => 'LIKE'
                    ],
                    [
                        'key'     => 'last_name',
                        'value'   => esc_attr($search_query),
                        'compare' => 'LIKE'
                    ]
                ]
            ],
        ];

        if (!empty($department_filter)) {
            $args['meta_query'][] = [
                'key'     => 'department',
                'value'   => $department_filter,
                'compare' => '='
            ];
        }
        $user_query = new WP_User_Query($args);
        $staff_members = $user_query->get_results();
        ob_start();
        if (!empty($staff_members)) {
            foreach ($staff_members as $user) {
                $this->render_staff_member($user);
            }
        } else {
            echo '<p>' . esc_html__('No staff members found.', 'spaces_mnu_plugin') . '</p>';
        }

        $html_output = ob_get_clean();
        wp_send_json_success($html_output);
    }

    private function render_staff_member($user)
    {
        $custom_avatar = get_user_meta($user->ID, 'custom_avatar', true);
        $first_name = get_user_meta($user->ID, 'first_name', true);
        $last_name = get_user_meta($user->ID, 'last_name', true);
        $job_title = get_user_meta($user->ID, 'job_title', true);
        $department_key = get_user_meta($user->ID, 'department', true);
        $departments = get_option('spaces_mnu_plugin_departments');
        $department = isset($departments[$department_key]) ? $departments[$department_key] : '';
        $mnu_role_key = get_user_meta($user->ID, 'mnu_role', true);
        $mnu_roles = get_option('spaces_mnu_plugin_roles');
        $phone = get_user_meta($user->ID, 'phone', true);
        $email = $user->user_email;
        $current_user = wp_get_current_user();
        $current_user_role = get_the_author_meta('mnu_role', $current_user->ID);

    ?>
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-items-center tw-my-4 tw-py-4 tw-border-b-[1px] tw-border-gray-200" data-user-id="<?= esc_attr($user->ID); ?>">
            <?php if ($custom_avatar): ?>
                <img src="<?= esc_url($custom_avatar); ?>" alt="<?= esc_attr($first_name . ' ' . $last_name); ?>" class="tw-w-20 tw-h-20 tw-rounded-full tw-mr-4 tw-object-cover tw-object-center">
            <?php else : ?>
                <?php echo get_avatar($user->ID, 80, '', 'Аватар', [
                    'class' => 'tw-w-20 tw-h-20 tw-rounded-full tw-mr-4 tw-object-cover tw-object-center'
                ]); ?>
            <?php endif; ?>
            <div class="tw-space-y-4 tw-my-2 tw-text-center md:tw-text-left">
                <h3 class="tw-text-lg tw-font-semibold"><?= esc_html($first_name . ' ' . $last_name); ?></h3>
                <p><?= esc_html($job_title); ?></p>
                <?php if ($mnu_role_key !== 'senior_management_staff' && str_ends_with($current_user_role, 'management_staff') !== false): ?>
                    <div class="">
                        <div>
                            <h4 class="tw-text-xs tw-text-gray-400 tw-uppercase"><?= __('Department', 'spaces_mnu_plugin'); ?></h4>

                            <div class="tw-py-2 tw-flex tw-items-center md:tw-items-start tw-space-x-0">
                                <select class="department-select tw-appearance-none tw-w-full tw-p-2 tw-border tw-border-r-0 tw-border-gray-300 tw-rounded-l tw-h-full">
                                    <option value="" <?= selected($department_key, '', false); ?>><?= __('Not selected', 'spaces_mnu_plugin'); ?></option>
                                    <?php foreach ($departments as $key => $department_name) : ?>
                                        <option value="<?= esc_attr($key); ?>" <?= selected($key, $department_key, false); ?>>
                                            <?= esc_html($department_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button class="save-department-btn tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-h-full tw-p-2 tw-rounded-r tw-border-l tw-border-gray-300">
                                    <?= __('Save', 'spaces_mnu_plugin'); ?>
                                </button>
                            </div>
                        </div>

                        <div>
                            <h4 class="tw-text-xs tw-text-gray-400 tw-uppercase"><?= __('Role', 'spaces_mnu_plugin'); ?></h4>

                            <div class="tw-py-2 tw-flex tw-items-center md:tw-items-start tw-space-x-0">
                                <select class="mnu-role-select tw-appearance-none tw-w-full tw-p-2 tw-border tw-border-r-0 tw-border-gray-300 tw-rounded-l tw-h-full">
                                    <option value="staff" <?= selected($mnu_role_key, '', false); ?>><?= __('Not selected', 'spaces_mnu_plugin'); ?></option>
                                    <?php foreach ($mnu_roles as $key => $mnu_role_data) : ?>
                                        <?php if (
                                            isset($mnu_role_data['parent'])
                                            && $mnu_role_data['parent'] === 'staff'
                                            && $key !== 'senior_management_staff'
                                            && $key !== 'security_staff'
                                        ) : ?>
                                            <option value="<?= esc_attr($key); ?>" <?= selected($key, $mnu_role_key, false); ?>>
                                                <?= esc_html($mnu_role_data['name']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <button class="save-mnu-role-btn tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-h-full tw-p-2 tw-rounded-r tw-border-l tw-border-gray-300">
                                    <?= __('Save', 'spaces_mnu_plugin'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p><?= esc_html(__($department, 'spaces_mnu_plugin')); ?></p>
                <?php endif; ?>


                <div class="tw-space-y-1">
                    <?php if ($phone): ?>
                        <div class="tw-flex tw-gap-2 tw-justify-start tw-items-center"><svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6383 2.50024H8.36167C7.07941 2.48212 6.02373 3.50391 6 4.78607V20.7977C6.02373 22.0799 7.07941 23.1017 8.36167 23.0836H16.6383C17.9206 23.1017 18.9763 22.0799 19 20.7977V4.78607C18.9763 3.50391 17.9206 2.48212 16.6383 2.50024ZM11.1458 4.12524H13.8542V4.6669C13.8542 4.81648 13.7329 4.93774 13.5833 4.93774H11.4167C11.2671 4.93774 11.1458 4.81648 11.1458 4.6669V4.12524ZM16.6383 21.4586C17.0234 21.4771 17.3517 21.1825 17.375 20.7977V4.78607C17.3517 4.40129 17.0234 4.10674 16.6383 4.12524H15.4792V4.6669C15.4792 5.16971 15.2794 5.65192 14.9239 6.00746C14.5684 6.363 14.0861 6.56274 13.5833 6.56274H11.4167C10.3696 6.56274 9.52083 5.71394 9.52083 4.6669V4.12524H8.36167C7.97662 4.10674 7.64828 4.40129 7.625 4.78607V20.7977C7.64828 21.1825 7.97662 21.4771 8.36167 21.4586H16.6383Z" fill="#9E9E9E" />
                            </svg>
                            <p><a href="tel:<?php echo $phone; ?>"><?= esc_html($phone); ?></a></p>
                        </div>
                    <?php endif;

                    if ($email): ?>
                        <div class="tw-flex tw-gap-2 tw-justify-start tw-items-center"><svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18 4.5H6C3.79086 4.5 2 6.29086 2 8.5V17.5C2 19.7091 3.79086 21.5 6 21.5H18C20.2091 21.5 22 19.7091 22 17.5V8.5C22 6.29086 20.2091 4.5 18 4.5ZM6 6.09H18C19.0657 6.09204 20.0025 6.79663 20.3 7.82L12.76 13.41C12.5534 13.612 12.2732 13.721 11.9843 13.7115C11.6955 13.7021 11.423 13.5751 11.23 13.36L3.72 7.83C4.01175 6.80973 4.9389 6.10216 6 6.09ZM3.59 17.5C3.59 18.831 4.66899 19.91 6 19.91H18C19.3271 19.9045 20.4 18.8271 20.4 17.5V9.47L13.6 14.47C13.1654 14.8746 12.5938 15.0997 12 15.1C11.3827 15.0902 10.7911 14.8514 10.34 14.43L3.59 9.43V17.5Z" fill="#9E9E9E" />
                            </svg>
                            <p><a href="mailto:<?php echo $email; ?>"><?= esc_html($email); ?></a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    }

    public static function get_staff_departments()
    {
        $departments = get_option('spaces_mnu_plugin_departments');
        return $departments ? $departments : [];
    }

    public static function get_students_mnu_roles()
    {
        $roles = get_option('spaces_mnu_plugin_roles');
        if (!$roles || !is_array($roles)) {
            return [];
        }
        $filtered_roles = array_filter($roles, function ($value, $key) {
            return str_ends_with($key, 'student');
        }, ARRAY_FILTER_USE_BOTH);

        return $filtered_roles;
    }

    public function save_user_department()
    {
        check_ajax_referer('save_user_department_nonce', 'nonce');
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $current_user_id = get_current_user_id();
        $current_user_role = get_the_author_meta('mnu_role', $current_user_id);
        $department = isset($_POST['department']) ? sanitize_text_field($_POST['department']) : '';
        if (!$user_id || empty($department)) {
            wp_send_json_error(['message' => __('Invalid user or department.', 'spaces_mnu_plugin')]);
        }
        if ($current_user_role != "management_staff" && $current_user_role != "senior_management_staff") {
            wp_send_json_error(['message' => __('Permission denied.', 'spaces_mnu_plugin')]);
        }
        if (update_user_meta($user_id, 'department', $department)) {
            wp_send_json_success(['message' => __('Department updated successfully!', 'spaces_mnu_plugin')]);
        } else {
            wp_send_json_error(['message' => __('Failed to update department.', 'spaces_mnu_plugin')]);
        }
    }

    public function save_user_mnu_role()
    {
        check_ajax_referer('save_user_mnu_role_nonce', 'nonce');
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $current_user_id = get_current_user_id();
        $current_user_role = get_the_author_meta('mnu_role', $current_user_id);
        $new_role = isset($_POST['mnu_role']) ? sanitize_text_field($_POST['mnu_role']) : '';
        if (!$user_id || empty($new_role)) {
            wp_send_json_error(['message' => __('Invalid user or role.', 'spaces_mnu_plugin')]);
        }
        if ($current_user_role != "management_staff" && $current_user_role != "senior_management_staff") {
            wp_send_json_error(['message' => __('Permission denied.', 'spaces_mnu_plugin')]);
        }
        $allowed_roles = ['staff', 'management_staff', 'teaching_staff', 'administrative_staff', 'student', 'ise_student', 'isj_student', 'mls_student', 'sla_student']; // список разрешенных ролей
        if (!in_array($new_role, $allowed_roles, true)) {
            wp_send_json_error(['message' => __('Invalid role selected.', 'spaces_mnu_plugin')]);
        }
        if (update_user_meta($user_id, 'mnu_role', $new_role)) {
            if (
                $new_role === 'management_staff'
                && !user_can($user_id, 'administrator')
            ) {
                $user_data = wp_update_user([
                    'ID' => $user_id,
                    'role' => 'author',
                ]);
                if (is_wp_error($user_data)) {
                    wp_send_json_error(['message' => __('Failed to update system role.', 'spaces_mnu_plugin')]);
                }
            } else if (!user_can($user_id, 'administrator')) {
                $user_data = wp_update_user([
                    'ID' => $user_id,
                    'role' => 'subscriber',
                ]);
                if (is_wp_error($user_data)) {
                    wp_send_json_error(['message' => __('Failed to update system role.', 'spaces_mnu_plugin')]);
                }
            }
            wp_send_json_success(['message' => __('Role updated successfully!', 'spaces_mnu_plugin')]);
        } else {
            wp_send_json_error(['message' => __('Failed to update role.', 'spaces_mnu_plugin')]);
        }
    }
    public function filter_students_ajax()
    {
        check_ajax_referer('students_filter_nonce', 'nonce');

        $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $mnu_role_students_filter = isset($_POST['mnu_role']) ? sanitize_text_field($_POST['mnu_role']) : '';

        $meta_query = [
            'relation' => 'AND', // Объединяем все условия
            [
                'key'     => 'mnu_role',
                'value'   => 'student',
                'compare' => 'LIKE'
            ]
        ];

        if (!empty($search_query)) {
            $meta_query[] = [
                'relation' => 'OR', // Поиск по имени или фамилии
                [
                    'key'     => 'first_name',
                    'value'   => $search_query,
                    'compare' => 'LIKE'
                ],
                [
                    'key'     => 'last_name',
                    'value'   => $search_query,
                    'compare' => 'LIKE'
                ]
            ];
        }

        if (!empty($mnu_role_students_filter)) {
            $meta_query[] = [
                'key'     => 'mnu_role',
                'value'   => $mnu_role_students_filter,
                'compare' => '='
            ];
        }

        $args = [
            'meta_query' => $meta_query,
        ];

        $user_query = new WP_User_Query($args);
        $students = $user_query->get_results();

        ob_start();
        if (!empty($students)) {
            foreach ($students as $user) {
                $this->render_student($user);
            }
        } else {
            echo '<p>' . esc_html__('No students found.', 'spaces_mnu_plugin') . '</p>';
        }

        $html_output = ob_get_clean();
        wp_send_json_success($html_output);
    }

    private function render_student($user)
    {
        $custom_avatar = get_user_meta($user->ID, 'custom_avatar', true);
        $first_name = get_user_meta($user->ID, 'first_name', true);
        $last_name = get_user_meta($user->ID, 'last_name', true);
        // $job_title = get_user_meta($user->ID, 'job_title', true);
        $mnu_role_key = get_user_meta($user->ID, 'mnu_role', true);
        $mnu_roles = get_option('spaces_mnu_plugin_roles');
        $mnu_role = isset($mnu_roles[$mnu_role_key]) ? $mnu_roles[$mnu_role_key] : '';
        $phone = get_user_meta($user->ID, 'phone', true);
        $email = $user->user_email;
        $current_user = wp_get_current_user();
        $current_user_role = get_the_author_meta('mnu_role', $current_user->ID);

    ?>
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-items-center tw-my-4 tw-py-4 tw-border-b-[1px] tw-border-gray-200" data-user-id="<?= esc_attr($user->ID); ?>">
            <?php if ($custom_avatar): ?>
                <img src="<?= esc_url($custom_avatar); ?>" alt="<?= esc_attr($first_name . ' ' . $last_name); ?>" class="tw-w-20 tw-h-20 tw-rounded-full tw-mr-4 tw-object-cover tw-object-center">
            <?php else : ?>
                <?php echo get_avatar($user->ID, 80, '', 'Аватар', [
                    'class' => 'tw-w-20 tw-h-20 tw-rounded-full tw-mr-4 tw-object-cover tw-object-center'
                ]); ?>
            <?php endif; ?>
            <div class="tw-space-y-4 tw-my-2 tw-text-center md:tw-text-left">
                <h3 class="tw-text-lg tw-font-semibold"><?= esc_html($first_name . ' ' . $last_name); ?></h3>

                <?php if (str_ends_with($current_user_role, 'management_staff') !== false): ?>
                    <div class="">
                        <div>
                            <h4 class="tw-text-xs tw-text-gray-400 tw-uppercase"><?= __('Role', 'spaces_mnu_plugin'); ?></h4>

                            <div class="tw-py-2 tw-flex tw-items-center md:tw-items-start tw-space-x-0">
                                <select class="mnu-role-select tw-appearance-none tw-w-full tw-p-2 tw-border tw-border-r-0 tw-border-gray-300 tw-rounded-l tw-h-full">
                                    <option value="student" <?= selected($mnu_role_key, '', false); ?>><?= __('Not selected', 'spaces_mnu_plugin'); ?></option>
                                    <?php foreach ($mnu_roles as $key => $mnu_role_data) : ?>
                                        <?php if (
                                            isset($mnu_role_data['parent'])
                                            && $mnu_role_data['parent'] === 'student'
                                            || $mnu_role_data['parent'] === 'staff'
                                            && $key !== 'senior_management_staff'
                                            && $key !== 'security_staff'
                                        ) : ?>
                                            <option value="<?= esc_attr($key); ?>" <?= selected($key, $mnu_role_key, false); ?>>
                                                <?= esc_html(__($mnu_role_data['name'], 'spaces_mnu_plugin')); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>

                                <button class="save-students-mnu-role-btn tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-h-full tw-p-2 tw-rounded-r tw-border-l tw-border-gray-300">
                                    <?= __('Save', 'spaces_mnu_plugin'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p><?= esc_html(__($mnu_role['name'], 'spaces_mnu_plugin')); ?></p>
                <?php endif; ?>
                <div class="tw-space-y-1">
                    <?php if ($phone): ?>
                        <div class="tw-flex tw-gap-2 tw-justify-start tw-items-center"><svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6383 2.50024H8.36167C7.07941 2.48212 6.02373 3.50391 6 4.78607V20.7977C6.02373 22.0799 7.07941 23.1017 8.36167 23.0836H16.6383C17.9206 23.1017 18.9763 22.0799 19 20.7977V4.78607C18.9763 3.50391 17.9206 2.48212 16.6383 2.50024ZM11.1458 4.12524H13.8542V4.6669C13.8542 4.81648 13.7329 4.93774 13.5833 4.93774H11.4167C11.2671 4.93774 11.1458 4.81648 11.1458 4.6669V4.12524ZM16.6383 21.4586C17.0234 21.4771 17.3517 21.1825 17.375 20.7977V4.78607C17.3517 4.40129 17.0234 4.10674 16.6383 4.12524H15.4792V4.6669C15.4792 5.16971 15.2794 5.65192 14.9239 6.00746C14.5684 6.363 14.0861 6.56274 13.5833 6.56274H11.4167C10.3696 6.56274 9.52083 5.71394 9.52083 4.6669V4.12524H8.36167C7.97662 4.10674 7.64828 4.40129 7.625 4.78607V20.7977C7.64828 21.1825 7.97662 21.4771 8.36167 21.4586H16.6383Z" fill="#9E9E9E" />
                            </svg>
                            <p><a href="tel:<?php echo $phone; ?>"><?= esc_html($phone); ?></a></p>
                        </div>
                    <?php endif;

                    if ($email): ?>
                        <div class="tw-flex tw-gap-2 tw-justify-start tw-items-center"><svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18 4.5H6C3.79086 4.5 2 6.29086 2 8.5V17.5C2 19.7091 3.79086 21.5 6 21.5H18C20.2091 21.5 22 19.7091 22 17.5V8.5C22 6.29086 20.2091 4.5 18 4.5ZM6 6.09H18C19.0657 6.09204 20.0025 6.79663 20.3 7.82L12.76 13.41C12.5534 13.612 12.2732 13.721 11.9843 13.7115C11.6955 13.7021 11.423 13.5751 11.23 13.36L3.72 7.83C4.01175 6.80973 4.9389 6.10216 6 6.09ZM3.59 17.5C3.59 18.831 4.66899 19.91 6 19.91H18C19.3271 19.9045 20.4 18.8271 20.4 17.5V9.47L13.6 14.47C13.1654 14.8746 12.5938 15.0997 12 15.1C11.3827 15.0902 10.7911 14.8514 10.34 14.43L3.59 9.43V17.5Z" fill="#9E9E9E" />
                            </svg>
                            <p><a href="mailto:<?php echo $email; ?>"><?= esc_html($email); ?></a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    }
}

new Spaces_MNU_Plugin_User_Public();
