<?php

/**
 * @link https://blog.urcloud.co
 *
 * @package WordPress
 * @subpackage URCloud
 */
if (!defined('THEME_NAME')) define('THEME_NAME', 'URCloud');
if (!defined('THEME_DB_VERSION')) {
    define('THEME_DB_VERSION', wp_get_theme()->Version);
}
if (version_compare($GLOBALS['wp_version'], '4.4-alpha', '<')) {
    wp_die('Please upgrade to version 4.4 or higher');
}

require(get_template_directory() . '/inc/core.de.php');



/**
 * 挂载样式和脚本
 */
function ur_enqueue_styles_scripts()
{

    wp_deregister_script('jquery');
    wp_enqueue_script(
        'jquery-min',
        '//cdn.staticfile.org/jquery/3.1.1/jquery.min.js',
        array(),
        THEME_DB_VERSION
    );


    wp_enqueue_style(
        'font-awesome',
        '//cdn.bootcdn.net/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        array(),
        THEME_DB_VERSION
    );

    wp_enqueue_script(
        'jquery-throttle-debounce',
        '//cdn.bootcdn.net/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js',
        array(),
        THEME_DB_VERSION
    );


    wp_enqueue_script(
        'js-cookie',
        '//cdn.bootcdn.net/ajax/libs/js-cookie/3.0.1/js.cookie.min.js',
        array(),
        THEME_DB_VERSION
    );

    wp_enqueue_script(
        'ajax-comment',
        get_template_directory_uri() . '/static/ajax-comment.js',
        array(),
        THEME_DB_VERSION,
        true
    );
    wp_localize_script(
        'ajax-comment',
        'themeAdminAjax',
        array(
            'url' => admin_url('admin-ajax.php')
        )
    );

    wp_enqueue_script(
        'script-js',
        get_template_directory_uri() . '/static/script.js',
        array(),
        THEME_DB_VERSION
    );
}

add_action('wp_enqueue_scripts', 'ur_enqueue_styles_scripts', 1);

// 优化代码
remove_action('wp_head', 'feed_links_extra', 3); // 额外的feed,例如category, tag页
remove_action('wp_head', 'wp_generator'); //隐藏wordpress版本
remove_filter('the_content', 'wptexturize'); //取消标点符号转义
remove_action('admin_print_scripts', 'print_emoji_detection_script'); // 禁用Emoji表情
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// 禁止wp-embed.min.js
function disable_embeds_init()
{
    global $wp;
    $wp->public_query_vars = array_diff($wp->public_query_vars, array(
        'embed',
    ));
    remove_action('rest_api_init', 'wp_oembed_register_route');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin');
    add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
}

add_action('init', 'disable_embeds_init', 9999);
function disable_embeds_tiny_mce_plugin($plugins)
{
    return array_diff($plugins, array('wpembed'));
}

function disable_embeds_rewrites($rules)
{
    foreach ($rules as $rule => $rewrite) {
        if (false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }
    return $rules;
}

function disable_embeds_remove_rewrite_rules()
{
    add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'disable_embeds_remove_rewrite_rules');
function disable_embeds_flush_rewrite_rules()
{
    remove_filter('rewrite_rules_array', 'disable_embeds_rewrites');
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'disable_embeds_flush_rewrite_rules');


function get_copyrights_years()
{
    $start = get_theme_mod('ur_setting_website_start');
    $now = date('Y');
    if (!$start) {
        return $now;
    }
    $year = substr($start, 0, 4);
    if ($year == $now) {
        return $now;
    }
    return $year . '-' . $now;
}

// 阻止站内文章互相Pingback 
function theme_noself_ping(&$links)
{
    $home = get_theme_mod('home');
    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
}

add_action('pre_ping', 'theme_noself_ping');

// 网页标题
function biji_add_theme_support_title()
{
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'biji_add_theme_support_title');

// 编辑器增强
function enable_more_buttons($buttons)
{
    $buttons[] = 'hr';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'wp_page';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    return $buttons;
}

add_filter("mce_buttons_3", "enable_more_buttons");

// 评论@回复
function idevs_comment_add_at($comment_text, $comment = '')
{
    if ($comment->comment_parent > 0) {
        $comment_text = '@<a href="#comment-' . $comment->comment_parent . '">' . get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
    }
    return $comment_text;
}

add_filter('comment_text', 'idevs_comment_add_at', 20, 2);

// 评论邮件
add_action('comment_post', 'comment_mail_notify');
/* comment_mail_notify v1.0 by willin kan. (所有回复都发邮件) */
function comment_mail_notify($comment_id)
{
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if (($parent_id != '') && ($spam_confirmed != 'spam')) {
        $wp_email = 'no-reply@' . preg_replace('#^www.#', '', strtolower($_SERVER['SERVER_NAME'])); //e-mail 发出点, no-reply 可改为可用的 e-mail.
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '您在 [' . get_option("blogname") . '] 的留言有了回复';
        $message = '
    <table cellpadding="0" cellspacing="0" class="email-container" align="center" width="550" style="font-size: 15px; font-weight: normal; line-height: 22px; text-align: left; border: 1px solid rgb(177, 213, 245); width: 550px;">
<tbody><tr>
<td>
<table cellpadding="0" cellspacing="0" class="padding" width="100%" style="padding-left: 40px; padding-right: 40px; padding-top: 30px; padding-bottom: 35px;">
<tbody>
<tr class="logo">
<td align="center">
<table class="logo" style="margin-bottom: 10px;">
<tbody>
<tr>
<td>
<span style="font-size: 22px;padding: 10px 20px;margin-bottom: 5%;color: #65c5ff;border: 1px solid;box-shadow: 0 5px 20px -10px;border-radius: 2px;display: inline-block;">' . get_option("blogname") . '</span>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr class="content">
<td>
<hr style="height: 1px;border: 0;width: 100%;background: #eee;margin: 15px 0;display: inline-block;">
<p>Hi ' . trim(get_comment($parent_id)->comment_author) . '!<br>Your comment by "' . get_the_title($comment->comment_post_ID) . '":</p>
<p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">' . trim(get_comment($parent_id)->comment_content) . '</p>
<p>' . $comment->comment_author . ' give you reply:</p>
<p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">' . trim($comment->comment_content) . '</p>
</td>
</tr>
<tr>
<td align="center">
<table cellpadding="12" border="0" style="font-family: Lato, \'Lucida Sans\', \'Lucida Grande\', SegoeUI, \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 25px; color: #444444; text-align: left;">
<tbody><tr>
<td style="text-align: center;">
<a target="_blank" style="color: #fff;background: #65c5ff;box-shadow: 0 5px 20px -10px #44b0f1;border: 1px solid #44b0f1;width: 200px;font-size: 14px;padding: 10px 0;border-radius: 2px;margin: 10% 0 5%;text-align:center;display: inline-block;text-decoration: none;" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">Now Reply</a>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" align="center" class="footer" style="max-width: 550px; font-family: Lato, \'Lucida Sans\', \'Lucida Grande\', SegoeUI, \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px; color: #444444; text-align: left; padding: 20px 0; font-weight: normal;">
<tbody><tr>
<td align="center" style="text-align: center; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
</td>
</tr>
<tr>
<td style="text-align: center; font-weight: normal; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
<p>Please do not reply to this message , because it is automatically sent.</p>
<p>© ' . date("Y") . ' <a name="footer_copyright" href="' . home_url() . '" style="color: rgb(43, 136, 217); text-decoration: underline;" target="_blank">' . get_option("blogname") . '</a></p>
</td>
</tr>
</tbody>
</table>';
        $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    }
}

// -- END ----------------------------------------
function recover_comment_fields($comment_fields)
{
    $comment = array_shift($comment_fields);
    $comment_fields = array_merge($comment_fields, array('comment' => $comment));
    return $comment_fields;
}

add_filter('comment_form_fields', 'recover_comment_fields');

// 404页面
function biji_404_template($template)
{
    if (!is_404()) return $template;
?>
    <!DOCTYPE HTML>
    <html>

    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="none" />
        <title>404 Not Found</title>
        <style>
            * {
                font-family: "Microsoft Yahei";
                margin: 0;
                font-weight: lighter;
                text-decoration: none;
                text-align: center;
                line-height: 2.2em;
            }

            html,
            body {
                height: 100%;
            }

            h1 {
                font-size: 100px;
                line-height: 1em;
            }

            table {
                width: 100%;
                height: 100%;
                border: 0;
            }
        </style>
        <?php if (get_theme_mod('biji_setting_style')) echo "<div style=\"display:none\">" . get_theme_mod('biji_setting_style') . "</div>\n"; ?>
    </head>

    <body>
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <h1>404</h1>
                                <h3>大事不妙啦！</h3>
                                <p>你访问的页面好像不小心被博主给弄丢了~<br /><a href="<?php bloginfo('url'); ?>">惩罚博主 ></a></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

    </html>
    <?php die;
}

add_filter('template_include', 'biji_404_template');

/**
 * AJAX 提交评论
 * by：https://fatesinger.com/jquery-ajax-comments.html
 **/
if (!function_exists('biji_ajax_comment_scripts')) :
    function biji_ajax_comment_scripts()
    {
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        wp_localize_script('ajax-comment', 'ajaxcomment', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'order' => get_option('comment_order'),
            'formpostion' => 'after',
        ));
    }
endif;

if (!function_exists('biji_ajax_comment_err')) :
    function biji_ajax_comment_err($a)
    {
        header('HTTP/1.0 500 Internal Server Error');
        header('Content-Type: text/plain;charset=UTF-8');
        echo $a;
        exit;
    }
endif;

if (!function_exists('biji_ajax_comment_callback')) :
    function biji_ajax_comment_callback()
    {
        $comment = wp_handle_comment_submission(wp_unslash($_POST));
        if (is_wp_error($comment)) {
            $data = $comment->get_error_data();
            if (!empty($data)) {
                biji_ajax_comment_err($comment->get_error_message());
            } else {
                exit;
            }
        }
        $user = wp_get_current_user();
        do_action('set_comment_cookies', $comment, $user);
        $GLOBALS['comment'] = $comment;
    ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="comment-body new-comment">
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, $size = '64'); ?>
                    <?php printf(__('<cite class="fn">%s</cite> <span class="says">说道：</span>'), get_comment_author_link()); ?>
                </div>
                <div class="comment-meta commentmetadata" style="right:8px;"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)'), ' '); ?>
                </div>
                <p><?php comment_text(); ?></p>
                <div class="reply">提交成功</div>
            </div>
        </li>
<?php die();
    }
endif;

add_action('wp_enqueue_scripts', 'biji_ajax_comment_scripts');
add_action('wp_ajax_nopriv_ajax_comment', 'biji_ajax_comment_callback');
add_action('wp_ajax_ajax_comment', 'biji_ajax_comment_callback');

//友情链接
add_filter('pre_option_link_manager_enabled', '__return_true');


// 全部配置完毕
