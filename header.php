<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="keywords" content="<?= get_theme_mod('ur_setting_meta_keywords'); ?>">
    <meta name="description" content="<?= get_theme_mod('ur_setting_meta_description'); ?>">
    <link href="<?php bloginfo('template_url'); ?>/style.css?<?php echo THEME_DB_VERSION; ?>" type="text/css" rel="stylesheet">
    <?php wp_head(); ?>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <section class="container">
            <hgroup itemscope itemtype="https://schema.org/WPHeader">
                <a href="<?php bloginfo('url'); ?>">
                    <h1 class="fullname"><?php bloginfo('name'); ?></h1>
                </a>
            </hgroup>
            <?php
            wp_nav_menu(
                array(
                    'container' => false,
                    'theme_location' => 'social_nav',
                    'items_wrap' => '<nav class="social"><ul id="%1$s" class="%2$s">%3$s</ul></nav>',
                    'walker' => new description_walker(),
                    'depth' => 0
                )
            );
            ?>
            <div class="header_nav_wrapper">
                <?php
                wp_nav_menu(
                    array(
                        'container' => false,
                        'theme_location' => 'header_nav',
                        'items_wrap' => '<nav class="header_nav"><ul id="%1$s" class="%2$s">%3$s</ul></nav>',
                        'depth' => 0
                    )
                );
                ?>
                <?php include('searchform.php'); ?>
            </div>
        </section>

        <section class="infos">
            <div class="container">
                <?php if (is_single() || is_page()) { ?>
                    <a href="<?php bloginfo('url'); ?>">
                        <h2 class="fixed-title"></h2>
                    </a>
                    <div class="fields">
                        <span><i class="fa fa-clock-o"></i> <time datetime="<?php echo get_the_time('c'); ?>" title="<?php echo get_the_time('c'); ?>" itemprop="datePublished" pubdate><?php the_time('Y-m-d') ?></time></span> /
                        <?php if (get_the_author_meta('display_name', $post->post_author)) { ?>
                            <span><i class="fa fa-user-o"></i> <?php echo get_the_author_meta('display_name', $post->post_author);  ?></span> /
                        <?php } ?>
                        <span><i class="fa fa-comment-o"></i> <?php comments_number('0', '1', '%'); ?>评</span> /
                        <a href="javascript:;" data-action="topTop" data-id="<?php the_ID(); ?>" class="dot-good <?php echo isset($_COOKIE['dotGood_' . $post->ID]) ? 'done' : ''; ?>">
                            <i class="fa fa-thumbs-o-up"></i><i class="fa fa-thumbs-up"></i>
                            <span class="count"><?php echo get_post_meta($post->ID, 'dotGood', true) ? get_post_meta($post->ID, 'dotGood', true) : '0'; ?></span>赞
                        </a>
                    </div>
                <?php } elseif (is_search()) { ?>
                    <a href="<?php bloginfo('url'); ?>">
                        <h2 class="fixed-title"></h2>
                    </a>
                    <div class="fixed-menus"></div>
                    <div class="search-text-info">
                        ”<?php the_search_query(); ?>”的搜索结果
                    </div>
                <?php } else { ?>
                    <a href="<?php bloginfo('url'); ?>">
                        <h2 class="fixed-title"></h2>
                    </a>
                    <div class="fixed-menus"></div>
                    <div class="placard">
                        <?= get_theme_mod('biji_setting_placard'); ?>
                    </div>
                <?php } ?>

            </div>
        </section>
    </header>