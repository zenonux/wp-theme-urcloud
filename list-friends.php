<?php
/*
Template Name: 友情链接
*/
get_header(); ?>
<div class="container">
    <div class="ur-friends-head">友情链接：</div>
    <ul class="ur-friends-links">
        <?php wp_list_bookmarks('title_li='); ?>
    </ul>
    <div class="ur-friends-head">申请说明：</div>
    <ul class="ur-friends-section">
        <li>
            请确定贵站可以长期稳定运营(重要！！！)
        </li>
        <li>
            原创博客优先，技术类博客优先，设计、视觉类博客优先;
        </li>
        <li>
            随缘交换
        </li>
    </ul>
    <div class="ur-friends-head">申请格式：</div>
    <ul class="ur-friends-section">
        <li>
            网址：https://blog.urcloud.co
        </li>
        <li>
            名称：URCloud
        </li>
        <li>
            描述：前端技术分享交流
        </li>
    </ul>
    <?php
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>
</div>

<?php get_footer(); ?>