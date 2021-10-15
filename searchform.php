<div class="ur-search-box">
    <div class="s">
        <form method="get" action="<?php bloginfo('url'); ?>" class="search">
            <input class="search-key" name="s" autocomplete="off" placeholder="输入关键词..." type="text" value="" required="required">
        </form>
    </div>
    <a class="sosearch"><i class="fa fa-search"></i></a>
    <div class="ur-search-tags">
        <?php wp_tag_cloud('smallest=12&largest=12&unit=px'); ?>
    </div>
</div>

<script>
    var $tags_list = $(`<?php wp_tag_cloud('smallest=12&largest=12&unit=px'); ?>`).filter('a');
    searchTag()
    $('.ur-search-box input').on('input', $.debounce(200, {}, searchTag))
    $('.ur-search-box input').focus(function() {
        $('.ur-search-tags').fadeIn()
    }).blur(function() {
        setTimeout(function() {
            $('.ur-search-tags').hide()
        }, 200)
    })

    function searchTag() {
        var value = $('.ur-search-box input').val();
        if (!value) {
            var matched = Array.from($tags_list).slice(0, 9);
            $('.ur-search-tags').html(matched)
        } else {
            var reg = new RegExp(value, 'gim');
            var matched = Array.from($tags_list).filter(function(item) {
                let text = $(item).text();
                return reg.test(text)
            }).slice(0, 9)
            $('.ur-search-tags').html(matched)
        }
    }
</script>