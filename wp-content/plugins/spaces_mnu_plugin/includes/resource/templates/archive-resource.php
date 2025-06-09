<?php
get_header();

// Проверяем, есть ли записи
if (have_posts()) : ?>

    <main id="primary" class="site-main tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6">

        <h1 class="tw-text-3xl tw-font-bold tw-mb-8">
            <?php post_type_archive_title(); ?>
        </h1>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-6">
            <?php
            // Цикл по записям типа resource
            while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('tw-border tw-rounded-lg tw-shadow-md tw-p-4'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="tw-mb-4">
                            <?php the_post_thumbnail('medium', ['class' => 'tw-rounded-md']); ?>
                        </div>
                    <?php endif; ?>

                    <h2 class="tw-text-2xl tw-font-semibold tw-mb-4">
                        <a href="<?php the_permalink(); ?>" class="tw-text-blue-600 tw-no-underline tw-hover:underline">
                            <?php the_title(); ?>
                        </a>
                    </h2>

                    <div class="tw-text-gray-700">
                        <?php the_excerpt(); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="tw-text-blue-500 tw-mt-4 tw-inline-block">
                        <?php esc_html_e('Read more', 'your-plugin-textdomain'); ?>
                    </a>
                </article>

            <?php endwhile; ?>
        </div>

        <div class="tw-mt-8">
            <?php
            // Пагинация
            the_posts_pagination([
                'prev_text' => __('Previous', 'your-plugin-textdomain'),
                'next_text' => __('Next', 'your-plugin-textdomain'),
            ]);
            ?>
        </div>

    </main>

<?php else : ?>

    <p class="tw-text-gray-700"><?php esc_html_e('No resources found.', 'your-plugin-textdomain'); ?></p>

<?php
endif;

get_footer();
