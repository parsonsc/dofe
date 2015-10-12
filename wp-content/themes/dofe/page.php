<?php get_header(); ?>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php
                    // the content (pretty self explanatory huh)
                    the_content();

                ?>
            

        <?php endwhile; endif; ?>

<?php get_footer(); ?>
