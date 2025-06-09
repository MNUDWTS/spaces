<?php

/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package MNU_theme
 */

get_header();
?>

<head>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>


<main class="twt-px-8 md:twt-px-24 twt-flex twt-flex-col twt-gap-4">
	<section class="twt-max-w-7xl twt-w-full twt-mx-auto twt-flex twt-flex-col twt-justify-center twt-items-center">
		<lottie-player src="https://lottie.host/e5185f1f-f1b5-4458-9bd3-acdc5562e9f6/alW4gGCi3v.json" background="#FFFFFF" speed="1" style="width: 300px; height: 200px" loop autoplay direction="1" mode="normal"></lottie-player>
		<!-- <?php get_search_form(); ?> -->
		<form role="search" method="get" id="searchform" class="twt-w-full twt-max-w-6xl twt-px-6 md:twt-px-12 md:twt-px-40" action="<?php echo home_url('/'); ?>">
			<div class="twt-relative twt-text-[#8B9C9E] focus-within:twt-[#8B9C9E] twt-w-full">
				<span class="twt-absolute twt-inset-y-0 twt-left-0 twt-flex twt-items-center twt-pl-2">
					<button type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>" class="twt-p-1 focus:twt-outline-none focus:twt-shadow-outline">
						<svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="twt-w-6 twt-h-6">
							<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
						</svg>
					</button>
				</span>
				<input type="search" value="<?php echo get_search_query(); ?>" name="s" id="s" class="focus:twt-ring-transparent focus:twt-shadow-none focus:twt-border-transparent twt-w-full twt-py-2 twt-text-sm twt-bg-white twt-rounded-md twt-pl-10 focus:twt-outline-none focus:twt-bg-black focus:twt-bg-opacity-10 focus:twt-text-gray-900 twt-rounded-xl hover:twt-rounded-xl focus:twt-rounded-xl" placeholder="<?php _e('Поиск по сайту', 'mnu_text'); ?>" autocomplete="off">
			</div>
		</form>
	</section>
	<section class="twt-max-w-7xl twt-mx-auto twt-flex twt-flex-col twt-justify-center twt-items-start twt-py-8 twt-gap-6 twt-w-full">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<?php get_template_part('template-parts/content', 'search'); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php _e("Совпадений не найдено", "mnu_text"); ?></p>
		<?php endif; ?>

	</section>
	<div class="twt-flex twt-justify-center gap-1 twt-max-w-screen twt-mt-8 twt-mb-24">
		<?php
		$links = paginate_links(array(
			'total'        => $wp_query->max_num_pages,
			'current'      => max(1, get_query_var('paged')),
			'mid_size'     => 0,
			'end_size'     => 0,
			'prev_text'    => __('<span class="twt-inline-flex twt-items-center hover:twt-bg-gray-50">' . __('<', 'mnu_text') . '</span>'),
			'next_text'    => __('<span class="twt-inline-flex twt-items-center hover:twt-bg-gray-50">' . __('>', 'mnu_text') . '</span>'),
			'type'         => 'array',
		));

		if ($links) :
			echo '<ul class="twt-flex twt-space-x-2">';
			foreach ($links as $link) {
				if (strpos($link, 'current') !== false) {
					echo '<li class="twt-inline"><span class="twt-inline-flex twt-items-center twt-justify-center twt-px-4 twt-py-2 twt-border twt-border-[#D62E1F] twt-rounded-md twt-bg-[#D62E1F] twt-text-sm twt-font-medium twt-text-white">' . $link . '</span></li>';
				} else {
					echo '<li class="twt-inline">' . str_replace('page-numbers', 'twt-inline-flex twt-items-center twt-justify-center twt-px-4 twt-py-2 twt-border twt-border-gray-300 twt-rounded-md twt-bg-white twt-text-sm twt-font-medium twt-text-gray-700 hover:twt-bg-gray-50', $link) . '</li>';
				}
			}
			echo '</ul>';
		endif;
		?>
	</div>



</main>

<?php
get_footer();
