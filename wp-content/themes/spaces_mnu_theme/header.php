<?php

/**
 * header с бургер-меню для мобильных
 *
 * @package spaces_mnu_theme
 * 
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<link rel="manifest" href="/manifest.json">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="theme-color" content="#0073aa">
	<meta name="apple-mobile-web-app-title" content="Spaces MNU">
	<link rel="apple-touch-icon" href="https://spaces.mnu.kz/wp-content/uploads/2024/11/logo-icon-dark-192.png">
	<link rel="apple-touch-icon" sizes="512x512" href="https://spaces.mnu.kz/wp-content/uploads/2024/11/logo-icon-dark-512.png">
	
	<!-- Принудительно скрываем мобильные элементы на десктопе -->
	<style>
	@media (min-width: 768px) {
		#mobile-menu-button,
		#mobile-menu {
			display: none !important;
		}
	}
	
	/* Скрываем десктопный аватар на мобиле */
	@media (max-width: 767px) {
		#desktop-user-container {
			display: none !important;
		}
	}
	
	/* Стили для мобильного sidebar */
	#mobile-menu {
		position: fixed;
		top: 0;
		right: -100%;
		width: 280px;
		height: 100vh;
		background: #1a1a1a;
		z-index: 1000;
		transition: right 0.3s ease-in-out;
		overflow-y: auto;
	}
	
	#mobile-menu.show {
		right: 0;
	}
	
	/* Overlay для закрытия sidebar */
	#mobile-menu-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		z-index: 999;
		opacity: 0;
		visibility: hidden;
		transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
	}
	
	#mobile-menu-overlay.show {
		opacity: 1;
		visibility: visible;
	}
	</style>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<nav class="twt-w-full twt-bg-[#0F0F0F] twt-py-4">
			<div class="twt-mx-auto twt-max-w-7xl twt-px-2 sm:twt-px-6 lg:twt-px-8">
				<div class="twt-relative twt-grid twt-grid-cols-3 md:twt-grid-cols-3 twt-h-16 twt-items-center twt-justify-start">
					
					<!-- Левая часть: Spaces (всегда) -->
					<div class="twt-flex twt-flex-1 twt-items-center twt-pl-2 twt-justify-start sm:twt-items-stretch twt-gap-4">
						<a href="/">
							<h2 class="twt-text-xl md:twt-text-3xl twt-font-medium twt-text-white">Spaces</h2>
						</a>
					</div>
					
					<!-- Центр: Логотип (всегда) -->
					<div class="twt-w-full twt-flex twt-justify-center twt-items-center">
						<a href="/"><img width="157" height="50" src="<?php echo get_template_directory_uri() . '/berik/img/logo_mnu.svg' ?>" alt="MNU Logo"></a>
					</div>
					
					<!-- Правая часть -->
					<div class="twt-gap-4 twt-flex twt-justify-end twt-items-center twt-pr-2 sm:twt-static sm:twt-inset-auto sm:twt-ml-6 sm:twt-pr-0">
						<?php if (is_user_logged_in()) :
							$current_user = wp_get_current_user();
							$current_user_id = get_current_user_id();
							$custom_avatar = get_user_meta($current_user_id, 'custom_avatar', true);
							$first_name = get_user_meta($current_user_id, 'first_name', true);
							?>
							
							<!-- ДЕСКТОП: Только аватар + имя (md и больше) -->
							<div id="desktop-user-container" class="twt-ml-3 twt-block twt-relative twt-z-50">
								<div id="desktop-user-button" class="twt-flex twt-items-center twt-gap-3 twt-cursor-pointer">
									<?php
									if ($custom_avatar) {
										echo '<img class="twt-h-10 twt-w-10 twt-rounded-full twt-object-cover twt-aspect-square" src="' . esc_url($custom_avatar) . '" alt="' . esc_attr__('User Avatar', 'spaces_mnu_plugin') . '">';
									} else {
										$avatar = get_avatar($current_user_id, 32, '', esc_attr__('User Avatar', 'spaces_mnu_plugin'), ['class' => 'twt-h-10 twt-w-10 twt-rounded-full']);
										echo $avatar;
									}
									if ($first_name) {
										$first_name_parts = explode(' ', $first_name);
										$display_name = substr($first_name_parts[0], 0, 20);
										echo '<span id="headerFirstName" class="twt-text-xl md:twt-text-2xl twt-font-medium twt-text-white">' . esc_html($display_name) . '</span>';
									} else {
										// Если нет first_name, показываем имя пользователя
										echo '<span id="headerFirstName" class="twt-text-xl md:twt-text-2xl twt-font-medium twt-text-white">' . esc_html($current_user->display_name) . '</span>';
									}
									?>
								</div>
								
								<!-- Выпадающее меню для десктопа -->
								<div id="desktop-user-menu" class="twt-absolute twt-right-0 twt-top-full twt-z-50 twt-mt-2 twt-w-48 twt-origin-top-right twt-rounded-md twt-bg-white twt-py-1 twt-shadow-lg twt-ring-1 twt-ring-black twt-ring-opacity-5 twt-hidden">
									<a href="<?php echo esc_url(home_url('/profile-' . pll_current_language())); ?>" class="twt-block twt-px-4 twt-py-2 twt-text-sm twt-text-gray-700 hover:twt-bg-gray-100"><?= __('Profile', 'spaces_mnu_theme'); ?></a>
									<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="twt-block twt-px-4 twt-py-2 twt-text-sm twt-text-gray-700 hover:twt-bg-gray-100"><?= __('Sign Out', 'spaces_mnu_theme'); ?></a>
								</div>
							</div>

							<!-- МОБИЛЬНЫЕ: Бургер-меню (только на мобильных) -->
							<button id="mobile-menu-button" class="twt-block md:twt-hidden twt-p-2 twt-text-white twt-rounded-md">
								<svg class="twt-h-6 twt-w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
								</svg>
							</button>

						<?php else : ?>
							<!-- Кнопка входа для неавторизованных -->
							<h3 id="openModalBtn" class="twt-text-xl md:twt-text-3xl twt-font-medium twt-text-gray-400 twt-cursor-pointer">
								<?= __('Sign In', 'spaces_mnu_theme'); ?>
							</h3>
						<?php endif; ?>
					</div>
				</div>

				<!-- МОБИЛЬНОЕ МЕНЮ (только на мобильных) -->
				<?php if (is_user_logged_in()) : ?>
				<!-- Overlay для закрытия sidebar -->
				<div id="mobile-menu-overlay" class="twt-block md:twt-hidden"></div>
				
				<div id="mobile-menu" class="twt-block md:twt-hidden">
					<div class="twt-p-6">

						<!-- Пункты меню -->
						<div class="twt-space-y-3">
							<a href="<?php echo esc_url(home_url('/profile-' . pll_current_language())); ?>" class="twt-flex twt-items-center twt-gap-4 twt-px-4 twt-py-4 twt-text-white twt-rounded-lg hover:twt-bg-[#333] twt-transition-colors">
								<svg class="twt-h-6 twt-w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
								</svg>
								<span class="twt-text-lg"><?= __('Profile', 'spaces_mnu_theme'); ?></span>
							</a>
							<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="twt-flex twt-items-center twt-gap-4 twt-px-4 twt-py-4 twt-text-white twt-rounded-lg hover:twt-bg-[#333] twt-transition-colors">
								<svg class="twt-h-6 twt-w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
								</svg>
								<span class="twt-text-lg"><?= __('Sign Out', 'spaces_mnu_theme'); ?></span>
							</a>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</nav>
		<div class="site-content">