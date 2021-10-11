<div class="ur-search-box">
    <div class="s">
        <form method="get" action="<?php bloginfo('url'); ?>" class="search">
            <input class="search-key" name="s" autocomplete="off" placeholder="输入关键词..." type="text" value="" required="required">
        </form>
    </div>
    <a class="sosearch"><i class="fa fa-search"></i></a>
    <div class="ur-search-tags">
        <?php wp_tag_cloud(); ?>
    </div>
</div>

<script>
    $('.ur-search-box input').focus(function() {
        $('.ur-search-tags').fadeIn()
    }).blur(function() {
        setTimeout(function() {
            $('.ur-search-tags').hide()
        }, 200)
    })
</script>