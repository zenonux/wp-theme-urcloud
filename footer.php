<footer class="footer">
    <section class="container">
        <?php wp_nav_menu(['container' => false, 'theme_location' => 'footer_nav', 'depth' => 0]); ?>
        <div style="display: flex;justify-content: space-between;">
            <div class='left'>
                <span>&copy; <?= date('Y') ?> <a href="<?= get_bloginfo('url') ?>"><?= get_bloginfo('name') ?></a></span>
                <?php if (get_option('zh_cn_l10n_icp_num')) { ?>
                    <span> . <a href="https://beian.miit.gov.cn/" target="_blank"><?= get_option('zh_cn_l10n_icp_num') ?></a></span>
                <?php } ?>
            </div>
            <div class='right'>
                <span><a rel="noreferrer" target="_blank" href="//beian.miit.gov.cn" target="_blank"> <?= get_theme_mod('biji_setting_beian'); ?></a></span>
            </div>
        </div>
    </section>
</footer>


<?php wp_footer(); ?>
<script data-no-instant>
    (function($) {
        <?php if (is_user_logged_in()) { ?>
            $('#wpadminbar').attr('data-no-instant', '')
        <?php } ?>
        $.extend({
            adamsOverload: function() {
                $(".post_article a").attr("rel", "external");
                $("a[rel='external']:not([href^='#']),a[rel='external nofollow']:not([href^='#'])").attr("target", "_blank");
                $("a.vi,.gallery a,.attachment a").attr("rel", "");
                <?php if (!get_theme_mod('biji_setting_viewimage')) { ?>
                    $.viewImage({
                        'target': '.gallery a,.gallery img,.attachment a,.post_article img,.post_article a,a.vi',
                        'exclude': '.readerswall img,.gallery a img,.attachment a img'
                    });
                <?php }
                if (!get_theme_mod('biji_setting_lately')) { ?>
                    Lately({
                        'target': '.commentmetadata a:first-child,.infos time,.post-list time'
                    });
                <?php }
                if (!get_theme_mod('biji_setting_prettify')) { ?>
                    Prism.highlightAll();
                    $("pre[class*='language-']").each(function(index, item) {
                        let text = $(item).find("code[class*='language-']").text();
                        let span = $(`<span class="copy"><i class="fa fa-clone"></i></span>`);
                        new ClipboardJS(span[0], {
                            text: () => text
                        }).on('success', () => Qmsg.success('复制成功！'));
                        $(item).append(span);
                    });
                <?php } ?>

                $('ul.links li a').each(function() {
                    if ($(this).parent().find('.bg').length === 0) {
                        $(this).parent().append('<div class="bg" style="background-image:url(https://www.google.com/s2/favicons?domain=' + $(this).attr("href") + ')"></div>')
                    }
                });

                // * Safari
                if (navigator.vendor.indexOf("Apple") > -1) {
                    $("[srcset]").each((index, img) => {
                        img.outerHTML = img.outerHTML;
                    });
                }
                <?php if (!get_theme_mod('biji_setting_placard')) { ?>
                    if ($('.placard').length) {
                        $.get("https://v1.hitokoto.cn", (data) => {
                            $('.placard').text(data.hitokoto);
                        });
                    }
                <?php } ?>
            }
        });
    })(jQuery);
    <?php if (get_theme_mod('biji_setting_footInfo')) {
        echo get_theme_mod('biji_setting_footInfo') . "\n";
    }
    if (!get_theme_mod('biji_setting_pjax')) { ?>
        InstantClick.on('change', function(isInitialLoad) {
            jQuery.adamsOverload();
            if (isInitialLoad === false) {
                // support MathJax
                if (typeof MathJax !== 'undefined') MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                // support Prism code prettify
                if (typeof Prism !== 'undefined') {
                    Prism.highlightAll();
                    $("pre[class*='language-']").each(function(index, item) {
                        let text = $(item).find("code[class*='language-']").text();
                        let span = $(`<span class="copy"><i class="fa fa-clone"></i></span>`);
                        new ClipboardJS(span[0], {
                            text: () => text
                        }).on('success', () => Qmsg.success('复制成功！'));
                        $(item).append(span);
                    });
                }
                // support 百度统计
                if (typeof _hmt !== 'undefined') _hmt.push(['_trackPageview', location.pathname + location.search]);
                // support google analytics
                if (typeof ga !== 'undefined') ga('send', 'pageview', location.pathname + location.search);
            }
        });
        InstantClick.on('wait', function() {
            // pjax href click
        });
        InstantClick.on('fetch', function() {
            // pjax begin
        });
        InstantClick.on('receive', function() {
            // pjax end
        });
        InstantClick.init('mousedown');
    <?php } else { ?>
        jQuery.adamsOverload();
    <?php } ?>
</script>
<!--网站效率：<?php timer_stop(4); ?>秒内查询了<?= get_num_queries(); ?>次数据库-->
</body>

</html>