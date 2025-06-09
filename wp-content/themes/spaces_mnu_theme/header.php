<?php

/**
 * header
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
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<nav class="twt-w-full twt-bg-[#0F0F0F] twt-py-4">
			<div class="twt-mx-auto twt-max-w-7xl twt-px-2 sm:twt-px-6 lg:twt-px-8">
				<div class="twt-relative twt-grid twt-grid-cols-3 twt-h-16 twt-items-center twt-justify-start">
					<div class="twt-flex twt-flex-1 twt-items-center twt-pl-2 twt-justify-start sm:twt-items-stretch twt-gap-4">
						<a href="/">
							<h2 class="twt-text-xl md:twt-text-3xl twt-font-medium twt-text-white">Spaces</h2>
						</a>
					</div>
					<div class="twt-w-full twt-flex twt-justify-center twt-items-center">
						<a href="/"><img width="157" height="50" src="<?php echo get_template_directory_uri() . '/berik/img/logo_mnu.svg' ?>" alt="MNU Logo"></a>
					</div>
					<div class="twt-gap-4 twt-flex twt-justify-end twt-items-center twt-pr-2 sm:twt-static sm:twt-inset-auto sm:twt-ml-6 sm:twt-pr-0">
						<?php if (is_user_logged_in()) :
							$current_user = wp_get_current_user(); ?>
							<div class="twt-relative twt-ml-3">
								<div id="user-menu-button" class="twt-flex twt-items-center twt-gap-3">
									<?php
									$current_user_id = get_current_user_id();
									$custom_avatar = get_user_meta($current_user_id, 'custom_avatar', true);
									$first_name = get_user_meta($current_user_id, 'first_name', true);
									if ($custom_avatar) {
										echo '<img class="twt-h-10 twt-w-10 twt-rounded-full twt-object-cover twt-aspect-square" aria-expanded="true" aria-haspopup="true" src="' . esc_url($custom_avatar) . '" alt="' . esc_attr__('User Avatar', 'spaces_mnu_plugin') . '">';
									} else {
										$avatar = get_avatar($current_user_id, 32, '', esc_attr__('User Avatar', 'spaces_mnu_plugin'), ['class' => 'twt-h-10 twt-w-10 twt-rounded-full']);
										echo $avatar;
									}
									if ($first_name) {
										$first_name_parts = explode(' ', $first_name);
										$display_name = substr($first_name_parts[0], 0, 20);
										echo '<span id="headerFirstName" class="twt-text-xl md:twt-text-2xl twt-font-medium twt-text-white">' . esc_html($display_name) . '</span>';
									}

									?>
								</div>
								<div class="twt-absolute twt-right-0 twt-z-10 twt-mt-2 twt-w-32 twt-origin-top-right twt-rounded-md twt-bg-white twt-py-1 twt-shadow-lg twt-ring-1 twt-ring-black twt-ring-opacity-5 focus:twt-outline-none twt-hidden" id="user-menu" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
									<a href="<?php echo esc_url(home_url('/profile-' . pll_current_language())); ?>" class="twt-block twt-px-4 twt-py-2 twt-text-sm twt-text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0"><?= __('Profile', 'spaces_mnu_theme'); ?></a>
									<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="twt-block twt-px-4 twt-py-2 twt-text-sm twt-text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2"><?= __('Sign Out', 'spaces_mnu_theme'); ?></a>
								</div>
							</div>
						<?php else : ?>
							<h3 id="openModalBtn" class="twt-text-xl md:twt-text-3xl twt-font-medium twt-text-gray-400 twt-cursor-pointer">
								<?= __('Sign In', 'spaces_mnu_theme'); ?>
							</h3>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</nav>
		<div class="site-content">