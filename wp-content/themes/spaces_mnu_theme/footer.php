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
			<li>
				<button id="reportBugBtn" class="twt-hover:underline twt-me-4 md:twt-me-6 twt-cursor-pointer twt-bg-transparent twt-border-none twt-text-gray-500 dark:twt-text-gray-400 twt-text-sm twt-font-medium">
					Нашли ошибку?
				</button>
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

<!-- Modal Sign In -->
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

<!-- Modal Bug Report -->
<div id="bugReportModal" class="twt-z-[99999] twt-fixed twt-inset-0 twt-bg-gray-500 twt-bg-opacity-75 twt-transition-opacity twt-opacity-0 twt-pointer-events-none">
	<div class="twt-flex twt-h-full twt-items-center twt-justify-center twt-p-4 twt-text-center sm:twt-p-0">
		<div id="bugReportModalContent" class="twt-z-50 twt-relative twt-transform twt-overflow-hidden twt-rounded-lg twt-bg-white twt-text-left twt-shadow-xl twt-transition-all sm:twt-my-8 sm:twt-w-full sm:twt-max-w-2xl">
			
			<div class="twt-bg-white twt-px-6 twt-py-6">
				<div class="twt-mb-6">
					<h3 class="twt-text-2xl twt-font-semibold twt-text-gray-900 twt-mb-2">
						Сообщить об ошибке
					</h3>
					<p class="twt-text-gray-600">
						Помогите нам улучшить платформу, сообщив о найденных проблемах.
					</p>
				</div>

				<form id="bugReportForm" class="twt-space-y-6">
					<!-- Bug Category -->
					<div>
						<label for="bugCategory" class="twt-block twt-text-sm twt-font-medium twt-text-gray-700 twt-mb-2">
							Категория ошибки <span class="twt-text-red-500">*</span>
						</label>
						<select id="bugCategory" name="bug_category" required class="twt-w-full twt-px-3 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-shadow-sm focus:twt-outline-none focus:twt-ring-2 focus:twt-ring-blue-500 focus:twt-border-blue-500">
							<option value="">Выберите категорию</option>
							<option value="visual">Проблема дизайна/визуала</option>
							<option value="functional">Проблема функциональности</option>
							<option value="performance">Проблема производительности</option>
							<option value="mobile">Проблема мобильной версии</option>
							<option value="content">Ошибка контента</option>
							<option value="login">Вход/Авторизация</option>
							<option value="other">Другое</option>
						</select>
					</div>

					<!-- Priority Level -->
					<div>
						<label for="bugPriority" class="twt-block twt-text-sm twt-font-medium twt-text-gray-700 twt-mb-2">
							Уровень приоритета <span class="twt-text-red-500">*</span>
						</label>
						<select id="bugPriority" name="bug_priority" required class="twt-w-full twt-px-3 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-shadow-sm focus:twt-outline-none focus:twt-ring-2 focus:twt-ring-blue-500 focus:twt-border-blue-500">
							<option value="">Выберите приоритет</option>
							<option value="low">Низкий - незначительная проблема</option>
							<option value="medium">Средний - заметная проблема</option>
							<option value="high">Высокий - серьёзная проблема</option>
							<option value="critical">Критический - сайт не работает</option>
						</select>
					</div>

					<!-- Bug Description -->
					<div>	
						<label for="bugDescription" class="twt-block twt-text-sm twt-font-medium twt-text-gray-700 twt-mb-2">
							Описание ошибки <span class="twt-text-red-500">*</span>
						</label>
						<textarea id="bugDescription" name="bug_description" required rows="5" 
							placeholder="Пожалуйста, опишите ошибку подробно. Укажите, что вы делали когда она произошла, что ожидали увидеть и что увидели на самом деле."
							class="twt-w-full twt-px-3 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-shadow-sm focus:twt-outline-none focus:twt-ring-2 focus:twt-ring-blue-500 focus:twt-border-blue-500 twt-resize-none"></textarea>
					</div>

					<!-- Steps to Reproduce -->
					<div>
						<label for="bugSteps" class="twt-block twt-text-sm twt-font-medium twt-text-gray-700 twt-mb-2">
							Шаги для воспроизведения
						</label>
						<textarea id="bugSteps" name="bug_steps" rows="4" 
							placeholder="1. Перейти на... 2. Нажать на... 3. Увидеть ошибку..."
							class="twt-w-full twt-px-3 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-shadow-sm focus:twt-outline-none focus:twt-ring-2 focus:twt-ring-blue-500 focus:twt-border-blue-500 twt-resize-none"></textarea>
					</div>

					<!-- Contact Email (if user wants updates) -->
					<div>
						<label for="bugEmail" class="twt-block twt-text-sm twt-font-medium twt-text-gray-700 twt-mb-2">
							Ваш email (необязательно)
						</label>
						<input type="email" id="bugEmail" name="bug_email" 
							placeholder="your.email@example.com"
							class="twt-w-full twt-px-3 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-shadow-sm focus:twt-outline-none focus:twt-ring-2 focus:twt-ring-blue-500 focus:twt-border-blue-500">
						<p class="twt-text-xs twt-text-gray-500 twt-mt-1">
							Необязательно: укажите email, если хотите получать обновления о решении проблемы.
						</p>
					</div>

					<!-- Submit Buttons -->
					<div class="twt-flex twt-justify-end twt-gap-3 twt-pt-4 twt-border-t twt-border-gray-200">
					<button type="button" id="cancelBugReport" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200">	
						Отмена
						</button>
						<button type="submit" id="submitBugReport" class="px-5 py-2 text-sm font-medium text-white bg-[#D62E1F] rounded-md hover:bg-red-600 transition duration-200">
						<span id="submitBugText">Отправить отчёт</span>
							<span id="submitBugLoading" class="twt-hidden">Отправка...</span>
						</button>
					</div>
				</form>

				<!-- Success Message -->
				<div id="bugReportSuccess" class="twt-hidden twt-text-center twt-py-8">
					<div class="twt-mx-auto twt-flex twt-items-center twt-justify-center twt-h-12 twt-w-12 twt-rounded-full twt-bg-green-100 twt-mb-4">
						<svg class="twt-h-6 twt-w-6 twt-text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
						</svg>
					</div>
					<h3 class="twt-text-lg twt-font-medium twt-text-gray-900 twt-mb-2">
						Отчёт отправлен!
					</h3>
					<p class="twt-text-gray-600 twt-mb-4">
						Спасибо за помощь в улучшении платформы. Мы рассмотрим ваш отчёт в ближайшее время.
					</p>
					<button id="closeBugReportSuccess" class="twt-px-4 twt-py-2 twt-text-sm twt-font-medium twt-text-white twt-bg-green-600 twt-rounded-md hover:twt-bg-green-700">
						Закрыть
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	#bugReportModalContent button {
		padding: 0.5rem 1rem;
		font-size: 0.875rem;
		font-weight: 500;
		border-radius: 0.375rem;
		transition: background-color 0.2s;
	}

	#bugReportModalContent h3 {
		font-size: 1.25rem;
		font-weight: 600;
		color: #111827;
		margin-bottom: 0.5rem;
	}
	
	#bugReportModalContent label {
		display: block;
		font-size: 0.875rem;
		font-weight: 500;
		color: #374151;
		margin-bottom: 0.3rem;
	}

	.twt-text-gray-600 {
    	font-size: 14px;
	}

	.twt-text-red-500{
		color: #ef4444;
		font-weight: bold;
	}
	#bugReportModalContent #cancelBugReport {
		color: #374151;
		background-color: #ffffff;
		border: 1px solid #d1d5db;
	}
	#bugReportModalContent #cancelBugReport:hover {
		background-color: #f9fafb;
	}
	#bugReportModalContent #submitBugReport {
		color: #ffffff;
		background-color: #D62E1F;
	}
	#bugReportModalContent #submitBugReport:hover {
		background-color: #dc2626;
	}
	#bugReportModalContent #closeBugReportSuccess {
		color: #ffffff;
		background-color: #16a34a;
	}
	#bugReportModalContent #closeBugReportSuccess:hover {
		background-color: #15803d;
	}

	#bugReportModalContent #bugReportSuccess h3 {
		font-size: 1.125rem;
		font-weight: 500;
		color: #111827;
		margin-bottom: 0.5rem;
	}
	#bugReportModalContent #bugReportSuccess p {
		color: #4b5563;
		font-size: 0.875rem;
		margin-bottom: 1rem;
	}

	#bugReportModalContent {
		padding: 1.5rem;
		max-width: 28rem; 
		width: 100%;
		margin-left: auto;
		margin-right: auto;
	}

	.twt-bg-white {
		--tw-bg-opacity: 1;
		background-color: rgb(255 255 255 / var(--tw-bg-opacity));
	}

	@media (min-width: 640px) {
		#bugReportModalContent {
			max-width: 90%;
		}
	}
</style>

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

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Bug Report Modal Elements
		const reportBugBtn = document.getElementById('reportBugBtn');
		const bugReportModal = document.getElementById('bugReportModal');
		const bugReportModalContent = document.getElementById('bugReportModalContent');
		const closeBugModalIcon = document.getElementById('closeBugModalIcon');
		const cancelBugReport = document.getElementById('cancelBugReport');
		const bugReportForm = document.getElementById('bugReportForm');
		const bugReportSuccess = document.getElementById('bugReportSuccess');
		const closeBugReportSuccess = document.getElementById('closeBugReportSuccess');

		// Open Bug Report Modal
		if (reportBugBtn && bugReportModal) {
			reportBugBtn.addEventListener('click', function() {
				bugReportModal.classList.remove('twt-opacity-0', 'twt-pointer-events-none');
				bugReportModal.classList.add('twt-opacity-100');
				document.body.classList.add('twt-overflow-hidden');
			});
		}

		// Close Bug Report Modal
		function closeBugModal() {
			if (bugReportModal) {
				bugReportModal.classList.add('twt-opacity-0', 'twt-pointer-events-none');
				bugReportModal.classList.remove('twt-opacity-100');
				document.body.classList.remove('twt-overflow-hidden');
				
				// Reset form and show form again
				if (bugReportForm) {
					bugReportForm.reset();
					bugReportForm.classList.remove('twt-hidden');
				}
				if (bugReportSuccess) {
					bugReportSuccess.classList.add('twt-hidden');
				}
			}
		}

		if (closeBugModalIcon) {
			closeBugModalIcon.addEventListener('click', closeBugModal);
		}
		if (cancelBugReport) {
			cancelBugReport.addEventListener('click', closeBugModal);
		}
		if (closeBugReportSuccess) {
			closeBugReportSuccess.addEventListener('click', closeBugModal);
		}

		// Close on outside click
		if (bugReportModal && bugReportModalContent) {
			bugReportModal.addEventListener('click', function(event) {
				if (!bugReportModalContent.contains(event.target)) {
					closeBugModal();
				}
			});
		}

		// Handle form submission
		if (bugReportForm) {
			bugReportForm.addEventListener('submit', function(e) {
				e.preventDefault();
				
				const submitBtn = document.getElementById('submitBugReport');
				const submitText = document.getElementById('submitBugText');
				const submitLoading = document.getElementById('submitBugLoading');
				
				// Show loading state
				if (submitText && submitLoading) {
					submitText.classList.add('twt-hidden');
					submitLoading.classList.remove('twt-hidden');
				}
				if (submitBtn) {
					submitBtn.disabled = true;
				}

				// Collect form data
				const formData = new FormData(bugReportForm);
				
				// Prepare data for AJAX
				const ajaxData = new FormData();
				ajaxData.append('action', 'submit_bug_report');
				ajaxData.append('nonce', bug_report_ajax.nonce);
				ajaxData.append('category', formData.get('bug_category'));
				ajaxData.append('priority', formData.get('bug_priority'));
				ajaxData.append('description', formData.get('bug_description'));
				ajaxData.append('steps', formData.get('bug_steps') || '');
				ajaxData.append('email', formData.get('bug_email') || '');
				ajaxData.append('url', window.location.href);
				ajaxData.append('user_agent', navigator.userAgent);
				ajaxData.append('screen_resolution', `${screen.width}x${screen.height}`);
				ajaxData.append('viewport_size', `${window.innerWidth}x${window.innerHeight}`);
				ajaxData.append('timestamp', new Date().toISOString());

				// Send AJAX request
				fetch(bug_report_ajax.ajax_url, {
					method: 'POST',
					body: ajaxData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						// Hide form and show success message
						bugReportForm.classList.add('twt-hidden');
						bugReportSuccess.classList.remove('twt-hidden');
					} else {
						// Show error message
						alert('Ошибка: ' + (data.data || 'Неизвестная ошибка'));
						
						// Reset button state
						if (submitText && submitLoading) {
							submitText.classList.remove('twt-hidden');
							submitLoading.classList.add('twt-hidden');
						}
						if (submitBtn) {
							submitBtn.disabled = false;
						}
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Ошибка отправки. Попробуйте позже.');
					
					// Reset button state
					if (submitText && submitLoading) {
						submitText.classList.remove('twt-hidden');
						submitLoading.classList.add('twt-hidden');
					}
					if (submitBtn) {
						submitBtn.disabled = false;
					}
				});
			});
		}
	});
</script>

<?php wp_footer(); ?>

<!-- Bug Report AJAX Localization -->
<script>
	// WordPress AJAX localization for bug reports
	window.bug_report_ajax = {
		ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
		nonce: '<?php echo wp_create_nonce('bug_report_nonce'); ?>'
	};
</script>

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