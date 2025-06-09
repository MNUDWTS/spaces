<?php

/**
 * footer
 *
 * @package spaces_mnu_theme
 */

?>
</div><!-- основной main -->
<footer class="twt-w-full twt-bg-[#0F0F0F]">
	<div class="twt-w-full twt-mx-auto twt-max-w-screen-xl twt-p-4 md:twt-flex md:twt-items-center md:twt-justify-between">
		<span class="twt-text-sm twt-text-gray-500 sm:twt-text-center dark:twt-text-gray-400">2024 <?php $currentYear = date("Y");
																									echo $currentYear == '2024' ? '' : '- ' . $currentYear; ?> &copy; <a href="mailto:dwts@mnu.kz" class="twt-hover:underline">DWTS MNU</a></span>
		<ul class="twt-flex twt-flex-wrap twt-items-center twt-mt-3 twt-text-sm twt-font-medium twt-text-gray-500 dark:twt-text-gray-400 sm:twt-mt-0">
			<li>
				<a href="#" class="twt-hover:underline twt-me-4 md:twt-me-6">About</a>
			</li>
			<li>
				<a href="#" class="twt-hover:underline twt-me-4 md:twt-me-6">Privacy Policy</a>
			</li>
			<div>
				<?php
				$langs_array = pll_the_languages(array('dropdown' => 1, 'hide_current' => 1, 'raw' => 1));

				if ($langs_array && is_array($langs_array)) :
					function get_dynamic_language_switch_url($lang_slug, $current_url_path, $current_query_string)
					{
						if ($current_url_path === '/kk' || $current_url_path === '/ru' || $current_url_path === '/en') {
							return home_url("/" . $lang_slug . '/') . $current_query_string;
						}
						if (is_singular('resource')) {
							$translated_id = pll_get_post(get_the_ID(), $lang_slug);
							if ($translated_id) {
								return get_permalink($translated_id) . $current_query_string;
							} else {
								return home_url($current_url_path) . $current_query_string;
							}
						}
						$pattern = "~^/([^/]+?)(?:-(ru|en|kk))?/?$~";
						if (preg_match($pattern, $current_url_path, $matches)) {
							$base_slug = $matches[1];
							$new_slug = $base_slug . '-' . $lang_slug;
							return home_url("/" . $new_slug . '/') . $current_query_string;
						}
						return home_url('/') . $current_query_string;
					}
					$current_url_path = rtrim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
					$current_query_string = $_SERVER["QUERY_STRING"] ? '?' . $_SERVER["QUERY_STRING"] : '';
				?>
					<div class="twt-relative twt-inline-block twt-text-left">
						<div>
							<button class="twt-inline-flex twt-w-full twt-justify-center twt-gap-x-1.5 twt-rounded-md twt-px-3 twt-py-2 twt-text-sm twt-font-semibold twt-border-none twt-bg-transparent" id="language-menu-button-footer" aria-expanded="true" aria-haspopup="true">
								<?php echo pll_current_language('name'); ?>
								<svg class="twt--mr-1 twt-h-5 twt-w-5 twt-text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
									<path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
								</svg>
							</button>
						</div>

						<div class="twt-absolute twt-right-0 twt-z-10 twt--mt-32 twt-w-32 twt-origin-top-right twt-rounded-md twt-bg-white twt-shadow-lg twt-ring-1 twt-ring-black twt-ring-opacity-5 focus:twt-outline-none twt-hidden" id="language-menu-footer" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button-footer" tabindex="-1">
							<div class="twt-py-1" role="none">
								<?php foreach ($langs_array as $lang) :
									$new_url = get_dynamic_language_switch_url($lang['slug'], $current_url_path, $current_query_string);
								?>
									<a href="<?php echo esc_url($new_url); ?>" class="twt-block twt-px-4 twt-py-2 twt-text-sm twt-text-gray-700 hover:twt-bg-gray-100" role="menuitem" tabindex="-1">
										<?php echo esc_html($lang['name']); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php else : ?>
					<p><?= __('No languages available', 'spaces_mnu_theme') ?></p>
				<?php endif; ?>
			</div>
		</ul>
	</div>
</footer>
</div><!-- #page -->

<script>
	let touchStartY = 0;
	let touchEndY = 0;
	window.addEventListener('touchstart', (e) => {
		touchStartY = e.changedTouches[0].screenY;
	});
	window.addEventListener('touchend', (e) => {
		touchEndY = e.changedTouches[0].screenY;
		handleSwipe();
	});

	function handleSwipe() {
		if (touchEndY - touchStartY > 250) {
			location.reload();
		}
	}
</script>

<!-- Modal -->
<div id="signInModal" class="twt-z-[99999] twt-fixed twt-inset-0 twt-bg-gray-500 twt-bg-opacity-75 twt-transition-opacity twt-opacity-0 twt-pointer-events-none">
	<div class="twt-flex twt-h-full twt-items-center twt-justify-center twt-p-4 twt-text-center sm:twt-p-0">
		<div id="signInModalContent" class="twt-z-50 twt-relative twt-transform twt-overflow-hidden twt-rounded-lg twt-bg-white twt-text-left twt-shadow-xl twt-transition-all sm:twt-my-8 sm:twt-w-full sm:twt-max-w-lg">
			<button id="closeModalIcon" class="twt-z-50 twt-absolute twt-top-2 twt-right-2 twt-text-gray-400 hover:twt-text-gray-600">
				<svg xmlns="http://www.w3.org/2000/svg" class="twt-h-6 twt-w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
			<div class="twt-bg-white twt-px-4 twt-pb-4 twt-pt-5 sm:twt-p-6 sm:twt-pb-4">
				<div class="twt-flex twt-items-start">
					<div class="twt-mx-auto twt-w-full">
						<div id="wpo365OpenIdRedirect" class="wpo365-mssignin-wrapper">
							<div class="wpo365-mssignin-spacearound">
								<div class="wpo365-mssignin-button" onclick="window.wpo365.pintraRedirect.toMsOnline()">
									<div class="wpo365-mssignin-logo">
										<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
											<title>MS-SymbolLockup</title>
											<rect x="1" y="1" width="9" height="9" fill="#f25022" />
											<rect x="1" y="11" width="9" height="9" fill="#00a4ef" />
											<rect x="11" y="1" width="9" height="9" fill="#7fba00" />
											<rect x="11" y="11" width="9" height="9" fill="#ffb900" />
										</svg>
									</div>
									<div class="wpo365-mssignin-label"><?= __('Sign in with Outlook', 'spaces_mnu_theme'); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const languageMenuButtonFooter = document.getElementById('language-menu-button-footer');
		const languageMenuFooter = document.getElementById('language-menu-footer');

		function closeLangMenuFooter() {
			if (!languageMenuFooter.classList.contains('twt-hidden')) {
				languageMenuFooter.classList.add('twt-hidden');
			}
		}
		languageMenuButtonFooter.addEventListener('click', function(event) {
			event.stopPropagation();
			languageMenuFooter.classList.toggle('twt-hidden');
		});
		window.addEventListener('click', function(e) {
			if (!languageMenuButtonFooter.contains(e.target) && !languageMenuFooter.contains(e.target)) {
				closeLangMenuFooter();
			}
		});
	});
</script>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const openModalBtn = document.getElementById('openModalBtn');
		const closeModalIcon = document.getElementById('closeModalIcon');
		const modal = document.getElementById('signInModal');
		const modalContent = document.getElementById('signInModalContent');

		if (openModalBtn && modal) {
			openModalBtn.addEventListener('click', function() {
				modal.classList.remove('twt-opacity-0', 'twt-pointer-events-none');
				modal.classList.add('twt-opacity-100');
				document.body.classList.add('twt-overflow-hidden'); // Disable background scrolling
			});
		}

		function closeModal() {
			if (modal) {
				modal.classList.add('twt-opacity-0', 'twt-pointer-events-none');
				modal.classList.remove('twt-opacity-100');
				document.body.classList.remove('twt-overflow-hidden'); // Enable background scrolling
			}
		}
		if (closeModalIcon) {
			closeModalIcon.addEventListener('click', closeModal);
		}
		if (modal && modalContent) {
			modal.addEventListener('click', function(event) {
				if (!modalContent.contains(event.target)) {
					closeModal();
				}
			});
		}
	});
</script>

<?php wp_footer(); ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
	(function(m, e, t, r, i, k, a) {
		m[i] = m[i] || function() {
			(m[i].a = m[i].a || []).push(arguments)
		};
		m[i].l = 1 * new Date();
		for (var j = 0; j < document.scripts.length; j++) {
			if (document.scripts[j].src === r) {
				return;
			}
		}
		k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
	})
	(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

	ym(99409984, "init", {
		clickmap: true,
		trackLinks: true,
		accurateTrackBounce: true
	});
</script>
<noscript>
	<div><img src="https://mc.yandex.ru/watch/99409984" style="position:absolute; left:-9999px;" alt="" /></div>
</noscript>
<!-- /Yandex.Metrika counter -->
</body>

</html>