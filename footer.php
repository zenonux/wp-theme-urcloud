<footer class="footer">
    <section class="container">
        <?php wp_nav_menu(['container' => false, 'theme_location' => 'footer_nav', 'depth' => 0]); ?>
        <div style="display: flex;justify-content: space-between;">
            <div class='left'>
                <span>&copy; <?= get_copyrights_years() ?> <a href="<?= get_bloginfo('url') ?>"><?= get_bloginfo('name') ?></a></span>
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
<script>
    (function($) {
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
    jQuery.adamsOverload();
    <?php if (get_theme_mod('biji_setting_footInfo')) {
        echo get_theme_mod('biji_setting_footInfo') . "\n";
    } ?>
</script>
</body>

</html>