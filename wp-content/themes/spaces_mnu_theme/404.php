<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package MNU_theme
 */

get_header();
?>
<?php
$current_lang = apply_filters('wpml_current_language', null);
?>

<main id="primary" class="site-main twt-bg-[#0f0f0f] twt-min-h-[90vh] twt-text-white twt-flex twt-flex-col twt-justify-center twt-items-center">
	<section class="twt-pt-8 twt-flex twt-flex-col twt-gap-4 twt-justify-center md:twt-items-center twt-w-full twt-max-w-5xl twt-mx-auto twt-px-8">
		<h1 class="twt-text-[8em] twt-text-left md:twt-text-center twt-font-extrabold twt-bg-gradient-to-tr twt-from-orange-500 twt-to-white twt-bg-clip-text twt-text-transparent twt-leading-none">404</h1>
		<p class="twt-text-white twt-text-left md:twt-text-center twt-text-2xl"><?= _e("Page not found", "spaces_mnu_theme"); ?></p>
	</section>
	<section class="twt-py-8 twt-flex twt-flex-col twt-gap-8 md:twt-justify-center md:twt-items-center twt-w-full twt-max-w-5xl md:twt-mx-auto">
		<div class="twt-max-w-3xl twt-flex twt-gap-2 twt-flex-wrap twt-mx-auto md:twt-justify-center twt-items-center twt-px-8">
			<a href="<?php echo str_starts_with($current_lang, 'en') ? '/' : "/{$current_lang}" ?>"
				class="twt-border-[1px] twt-rounded-xl twt-px-4 twt-py-2 twt-bg-white twt-bg-opacity-25 hover:twt-bg-white focus:twt-bg-white hover:twt-text-black">
				<?= __('Home', "spaces_mnu_theme"); ?>
			</a>
		</div>
	</section>
</main>

<?php
get_footer();
