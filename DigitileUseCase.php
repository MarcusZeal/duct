<?php

class DigitileUseCase
{
    public $limit   = 3;
    public $hasMore = false;

    public function scripts()
    {
        wp_enqueue_style('style1', plugin_dir_url(__FILE__) . 'styles.css');
        wp_enqueue_style('oc',  plugin_dir_url(__FILE__) . 'includes/OwlCarousel/assets/owl.carousel.min.css' );
        wp_enqueue_script('uct', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('ocjs', plugin_dir_url(__FILE__) . 'includes/OwlCarousel/owl.carousel.min.js', array('jquery'), '1.0.0', true);
        wp_localize_script('uct', 'uct', array('ajaxurl' => admin_url('admin-ajax.php')));

    }

    public function addCustomUseCase($slug, $name, $singular_name)
    {
        register_post_type($slug, $this->getUseCaseSetting($name, $singular_name));
    }
    public function getUseCaseSetting($name, $singular_name)
    {
        return [
            'label'           => __($singular_name, 'uct'),
            'description'     => __($name . '. Used in the toggle shortcode.', 'uct'),
            'labels'          => $this->getUseCaseLabel($name, $singular_name),
            'supports'        => array(
                'title',
            ),
            'show_in_menu'    => true,
            'menu_position'   => 5,
            'has_archive'     => true,
            'capability_type' => 'page',
			'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queryable' => true,  // you should be able to query it
			'show_ui' => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
			'has_archive' => false,  // it shouldn't have archive page
			'rewrite' => false,  // it shouldn't have rewrite rules
        ];
    }
    public function getUseCaseLabel($name, $singular_name)
    {
        return [
            'name'           => _x($name, 'Use Case Card General Name', 'uct'),
            'singular_name'  => _x($singular_name, 'Use Case Card Singular Name', 'uct'),
            'menu_name'      => __($name, 'uct'),
            'name_admin_bar' => __($singular_name, 'uct'),
        ];
    }
    public function getPosts($page = 1, $skip = null)
    {
        $this->skip = $skip;

        $args = [
            "posts_per_page" => $this->limit,
            "post_type"      => 'use_case_card',
            "paged"          => $page,
            'orderby'        => 'post_date',
            'order'          => 'DESC',
        ];
        if ($skip) {
            $args['post__not_in'] = explode(',', $skip);
        }

        return new WP_Query($args);
    }
    public function getCardTemplate($id, $title, $img, $link, $linkText)
    {
        return '<article id="uct-card-' . $id . '" class="uct-card">
                        <div class="img">
							<a href="' . $link . '" class="card-link">
                        	    <img src="' . $img['sizes']['use_case_card'] . '" alt="' . $img['alt'] . '">
                        	</a>
						</div>
                        <div class="uct-card-detail">
                            <h3 class="uct-card-title">' . $title . '</h3>
                            <p>
                                <a href=" ' . $link . '" class="uct-link">' . $linkText . '</a>
                            </p>
                        </div>
                    </article>';
    }
    public function getCards($page = 1, $skip)
    {
        $cards = ($page == 1) ? $this->getTemplateWrapper($skip) : '';

        $posts         = $this->getPosts($page, $skip);
        $this->hasMore = ($posts->found_posts - ($page * $this->limit)) > 0;

        if ($posts->have_posts()) {
            foreach ($posts->posts as $post) {
                $cards .= $this->getCardTemplate($post->ID,  get_field('card_title', $post->ID), get_field('card_image', $post->ID), get_field('card_link', $post->ID), get_field('card_link_text', $post->ID));
            }
        }
        $cards .= ($page == 1) ? $this->getTemplateWrapperEnd() : '';
        $cards .= ($page == 1) ? $this->getTemplateButton() : '';

        return $cards;
    }

    public function getTemplateWrapper($skip = "")
    {
        return "<section class='uct-cards' skip='" . $skip . "'>";
    }
    public function getTemplateButton()
    {
        $class = (!$this->hasMore) ? 'hidden' : '';

        return "
        <div class='clearfix'></div>
        <div class='uct-button-wrapper'>
            <button class='uct-card-button " . $class . "' type='button' id='2'>
                See All Use Cases
                <img src='" . plugin_dir_url(__FILE__) . "images/loading.gif' alt='loading...'>
            </button>
        </div>";
    }
    public function getTemplateWrapperEnd()
    {
        return "</section>";
    }
}
