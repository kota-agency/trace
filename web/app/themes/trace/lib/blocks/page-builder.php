<?php

/* layout blocks */

if (have_rows('builder')):
    while (have_rows('builder')) : the_row();

        if (get_row_layout() == 'page_header'):
            get_template_part('lib/blocks/page-header');

        elseif (get_row_layout() == 'half_width_content'):
            get_template_part('lib/blocks/half-width-content');

        elseif (get_row_layout() == 'video_modal'):
            get_template_part('lib/blocks/video-modal');

        elseif (get_row_layout() == 'signposts'):
            get_template_part('lib/blocks/signposts');

        elseif (get_row_layout() == 'full_width_image'):
            get_template_part('lib/blocks/full-width-image');

        elseif (get_row_layout() == 'form'):
            get_template_part('lib/blocks/form');

        elseif (get_row_layout() == 'image_content'):
            get_template_part('lib/blocks/image-content');

        elseif (get_row_layout() == 'news'):
            get_template_part('lib/blocks/news');

        elseif (get_row_layout() == 'content_video'):
            get_template_part('lib/blocks/content-video');

        elseif (get_row_layout() == 'testimonials'):
            get_template_part('lib/blocks/testimonials');

        elseif (get_row_layout() == 'quote'):
            get_template_part('lib/blocks/quote');

        elseif (get_row_layout() == 'cta'):
            get_template_part('lib/blocks/cta');

        endif;

    endwhile;

else :

    get_template_part('lib/blocks/no-content');

endif;
