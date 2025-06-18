<?php

/**
 * spaces_mnu_theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package spaces_mnu_theme
 */

if (! defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function spaces_mnu_theme_setup()
{
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on spaces_mnu_theme, use a find and replace
		* to change 'spaces_mnu_theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain('spaces_mnu_theme', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'spaces_mnu_theme'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'spaces_mnu_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'spaces_mnu_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function spaces_mnu_theme_content_width()
{
	$GLOBALS['content_width'] = apply_filters('spaces_mnu_theme_content_width', 640);
}
add_action('after_setup_theme', 'spaces_mnu_theme_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function spaces_mnu_theme_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'spaces_mnu_theme'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'spaces_mnu_theme'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'spaces_mnu_theme_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function spaces_mnu_theme_scripts()
{
	wp_enqueue_style('spaces_mnu_theme-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('spaces_mnu_theme-style', 'rtl', 'replace');

	// wp_enqueue_script('spaces_mnu_theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'spaces_mnu_theme_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

// berik


function mnu_theme_enqueue_styles()
{
	$tailwind_path = get_template_directory() . '/berik/tailwind.css';
	if (file_exists($tailwind_path)) {
		wp_enqueue_style('tailwind-styles', get_template_directory_uri() . '/berik/tailwind.css');
	}
}
add_action('wp_enqueue_scripts', 'mnu_theme_enqueue_styles');

function mnu_theme_enqueue_scripts()
{
	wp_enqueue_style('theme-style', get_stylesheet_uri());

	$scripts_path = get_template_directory() . '/berik/scripts.js';
	if (file_exists($scripts_path)) {
		wp_enqueue_script('theme-menu-script', get_template_directory_uri() . '/berik/scripts.js', array(), null, true);
	}
}
add_action('wp_enqueue_scripts', 'mnu_theme_enqueue_scripts');


function restrict_admin_access()
{
	if (is_admin() && !current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
		wp_redirect(home_url());
		exit;
	}
}
add_action('admin_init', 'restrict_admin_access');

function custom_login_logo()
{
?>
	<style type="text/css">
		#login h1 a {
			background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/berik/img/logo_mnu_red.svg');
			background-size: contain;
			width: 100%;
			height: 100px;
		}
	</style>
<?php
}
add_action('login_enqueue_scripts', 'custom_login_logo');
function custom_login_logo_url()
{
	return 'https://spaces.mnu.kz';
}
add_filter('login_headerurl', 'custom_login_logo_url');
function custom_login_logo_url_title()
{
	return 'Spaces MNU';
}
add_filter('login_headertitle', 'custom_login_logo_url_title');



add_filter('show_admin_bar', '__return_false');

// Disable auto-update emails.
add_filter('auto_core_update_send_email', '__return_false');

// Disable auto-update emails for plugins.
add_filter('auto_plugin_update_send_email', '__return_false');

// Disable auto-update emails for themes.
add_filter('auto_theme_update_send_email', '__return_false');

add_filter('send_email_change_email', '__return_false');

add_filter('the_generator', '__return_empty_string');


// add_action('plugins_loaded', function () {
// 	error_log("plugins_loaded triggered for debug");
// });
// add_action('init', function () {
// 	error_log("init triggered for debug");
// });

function add_export_users_button()
{
?>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function() {
			let container = document.querySelector(".wrap h1");
			if (container) {
				let exportBtn = document.createElement("a");
				exportBtn.href = "<?php echo admin_url('users.php?action=export_users_csv'); ?>";
				exportBtn.className = "page-title-action";
				exportBtn.textContent = "Экспорт Email";
				container.appendChild(exportBtn);
			}
		});
	</script>
<?php
}
add_action('admin_footer-users.php', 'add_export_users_button');

function export_users_csv()
{
	if (!current_user_can('manage_options') || !isset($_GET['action']) || $_GET['action'] !== 'export_users_csv') {
		return;
	}

	// Заголовки для скачивания CSV
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="users_emails.csv"');

	$output = fopen('php://output', 'w');
	fputcsv($output, ['Email']); // Заголовки CSV

	$users = get_users(['fields' => ['user_email']]);
	foreach ($users as $user) {
		fputcsv($output, [$user->user_email]);
	}

	fclose($output);
	exit;
}
add_action('admin_init', 'export_users_csv');

// Регистрируем AJAX endpoints
add_action('wp_ajax_submit_bug_report', 'handle_bug_report_submission');
add_action('wp_ajax_nopriv_submit_bug_report', 'handle_bug_report_submission');

// Добавляем локализацию для AJAX
add_action('wp_enqueue_scripts', 'enqueue_bug_report_scripts');

function enqueue_bug_report_scripts() {
    wp_localize_script('jquery', 'bug_report_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bug_report_nonce')
    ));
}

function handle_bug_report_submission() {
    // Проверяем nonce для безопасности
    if (!wp_verify_nonce($_POST['nonce'], 'bug_report_nonce')) {
        wp_die('Ошибка безопасности');
    }

    // Получаем и очищаем данные
    $category = sanitize_text_field($_POST['category']);
    $priority = sanitize_text_field($_POST['priority']);
    $description = sanitize_textarea_field($_POST['description']);
    $steps = sanitize_textarea_field($_POST['steps']);
    $email = sanitize_email($_POST['email']);
    $page_url = esc_url_raw($_POST['url']);
    $user_agent = sanitize_text_field($_POST['user_agent']);
    $screen_resolution = sanitize_text_field($_POST['screen_resolution']);
    $viewport_size = sanitize_text_field($_POST['viewport_size']);
    $timestamp = sanitize_text_field($_POST['timestamp']);

    // Проверяем обязательные поля
    if (empty($category) || empty($priority) || empty($description)) {
        wp_send_json_error('Заполните все обязательные поля');
        return;
    }

    // Получаем информацию о пользователе
    $current_user = wp_get_current_user();
    $user_info = '';
    if ($current_user->ID != 0) {
        $user_info = "Пользователь: {$current_user->display_name} ({$current_user->user_email})\n";
        $user_info .= "ID пользователя: {$current_user->ID}\n";
    } else {
        $user_info = "Анонимный пользователь\n";
    }

    // Переводим категории на русский для email
    $categories_ru = array(
        'visual' => 'Проблема дизайна/визуала',
        'functional' => 'Проблема функциональности',
        'performance' => 'Проблема производительности',
        'mobile' => 'Проблема мобильной версии',
        'content' => 'Ошибка контента',
        'login' => 'Вход/Авторизация',
        'other' => 'Другое'
    );

    $priorities_ru = array(
        'low' => 'Низкий - незначительная проблема',
        'medium' => 'Средний - заметная проблема',
        'high' => 'Высокий - серьёзная проблема',
        'critical' => 'Критический - сайт не работает'
    );

    $category_ru = isset($categories_ru[$category]) ? $categories_ru[$category] : $category;
    $priority_ru = isset($priorities_ru[$priority]) ? $priorities_ru[$priority] : $priority;

    // Формируем сообщение для email
    $subject = "[SPACES MNU] Отчёт об ошибке - {$priority_ru}";
    
    $message = "=== ОТЧЁТ ОБ ОШИБКЕ ===\n\n";
    $message .= "ОСНОВНАЯ ИНФОРМАЦИЯ:\n";
    $message .= "Категория: {$category_ru}\n";
    $message .= "Приоритет: {$priority_ru}\n";
    $message .= "Дата: " . date('d.m.Y H:i:s', strtotime($timestamp)) . "\n\n";
    
    $message .= "ОПИСАНИЕ ОШИБКИ:\n";
    $message .= "{$description}\n\n";
    
    if (!empty($steps)) {
        $message .= "ШАГИ ДЛЯ ВОСПРОИЗВЕДЕНИЯ:\n";
        $message .= "{$steps}\n\n";
    }
    
    $message .= "ТЕХНИЧЕСКАЯ ИНФОРМАЦИЯ:\n";
    $message .= "URL страницы: {$page_url}\n";
    $message .= "User Agent: {$user_agent}\n";
    $message .= "Разрешение экрана: {$screen_resolution}\n";
    $message .= "Размер окна: {$viewport_size}\n\n";
    
    $message .= "ИНФОРМАЦИЯ О ПОЛЬЗОВАТЕЛЕ:\n";
    $message .= $user_info;
    
    if (!empty($email)) {
        $message .= "Email для обратной связи: {$email}\n";
    }
    
    $message .= "\n=== КОНЕЦ ОТЧЁТА ===";

    // Настройки email
    $to = 'nkuanyshuly2019@gmail.com'; // Основной email для отчетов
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Добавляем Reply-To если указан email пользователя
    if (!empty($email)) {
        $headers[] = "Reply-To: {$email}";
    }

    // Отправляем email
    $mail_sent = wp_mail($to, $subject, $message, $headers);

    if ($mail_sent) {
        // Опционально: сохраняем в базу данных для статистики
        save_bug_report_to_db($category, $priority, $description, $steps, $email, $page_url, $user_agent, $current_user->ID);
        
        wp_send_json_success('Отчёт успешно отправлен');
    } else {
        wp_send_json_error('Ошибка отправки отчёта. Попробуйте позже.');
    }
}

// Опциональная функция для сохранения в БД
function save_bug_report_to_db($category, $priority, $description, $steps, $email, $page_url, $user_agent, $user_id) {
    global $wpdb;
    
    // Создаем таблицу если не существует
    $table_name = $wpdb->prefix . 'bug_reports';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        category varchar(50) NOT NULL,
        priority varchar(20) NOT NULL,
        description text NOT NULL,
        steps text,
        user_email varchar(100),
        page_url text,
        user_agent text,
        user_id mediumint(9),
        status varchar(20) DEFAULT 'new',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Сохраняем отчёт
    $wpdb->insert(
        $table_name,
        array(
            'category' => $category,
            'priority' => $priority,
            'description' => $description,
            'steps' => $steps,
            'user_email' => $email,
            'page_url' => $page_url,
            'user_agent' => $user_agent,
            'user_id' => $user_id
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
    );
}