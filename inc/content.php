<!-- Content -->
<section class="container">
    <div class="ur-post-detail-title">
        <?php echo get_the_title($post->ID); ?>
    </div>
    <article class="post_article" itemscope itemtype="https://schema.org/Article">
        <?php if (have_posts()) while (have_posts()) {
            the_post();
            the_content();
        }; ?>
    </article>
    <div class="ur-post-detail-agree">
        <div class="agree dot-good" data-action="topTop" data-id="<?php the_ID(); ?>">
            <div class="icon">
                <svg class="icon-1 <?php echo isset($_COOKIE['dotGood_' . $post->ID]) ? 'active' : ''; ?>" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                    <path d="M736 128c-65.952 0-128.576 25.024-176.384 70.464-4.576 4.32-28.672 28.736-47.328 47.68L464.96 199.04C417.12 153.216 354.272 128 288 128 146.848 128 32 242.848 32 384c0 82.432 41.184 144.288 76.48 182.496l316.896 320.128C450.464 911.68 478.304 928 512 928s61.568-16.32 86.752-41.504l316.736-320 2.208-2.464C955.904 516.384 992 471.392 992 384c0-141.152-114.848-256-256-256z" fill="#fff"></path>
                </svg>
                <svg class="icon-2 <?php echo isset($_COOKIE['dotGood_' . $post->ID]) ? '' : 'active'; ?>" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                    <path d="M512 928c-28.928 0-57.92-12.672-86.624-41.376L106.272 564C68.064 516.352 32 471.328 32 384c0-141.152 114.848-256 256-256 53.088 0 104 16.096 147.296 46.592 14.432 10.176 17.92 30.144 7.712 44.608-10.176 14.432-30.08 17.92-44.608 7.712C366.016 204.064 327.808 192 288 192c-105.888 0-192 86.112-192 192 0 61.408 20.288 90.112 59.168 138.688l315.584 318.816C486.72 857.472 499.616 863.808 512 864c12.704.192 24.928-6.176 41.376-22.624l316.672-319.904C896.064 493.28 928 445.696 928 384c0-105.888-86.112-192-192-192-48.064 0-94.08 17.856-129.536 50.272l-134.08 134.112c-12.512 12.512-32.736 12.512-45.248 0s-12.512-32.736 0-45.248L562.24 196c48.32-44.192 109.664-68 173.76-68 141.152 0 256 114.848 256 256 0 82.368-41.152 144.288-75.68 181.696l-317.568 320.8C569.952 915.328 540.96 928 512 928z" fill="#fff"></path>
                </svg>
            </div>
            <span class="text count"><?php echo get_post_meta($post->ID, 'dotGood', true) ? get_post_meta($post->ID, 'dotGood', true) : '0'; ?></span>
        </div>
    </div>
    <ul class="tags">
        <?php the_tags('<li>', '</li><li>', '</li>') ?>
    </ul>
    <nav class="nearbypost">
        <div class="alignleft"><?php previous_post_link('%link'); ?></div>
        <div class="alignright"><?php next_post_link('%link'); ?></div>
    </nav>
    <?= get_theme_mod('biji_setting_postAd'); ?>
</section>