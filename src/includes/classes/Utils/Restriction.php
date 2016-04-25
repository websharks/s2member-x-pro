<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\s2MemberX\Pro\Classes\Utils;

use WebSharks\WpSharks\s2MemberX\Pro\Classes;
use WebSharks\WpSharks\s2MemberX\Pro\Interfaces;
use WebSharks\WpSharks\s2MemberX\Pro\Traits;
#
use WebSharks\WpSharks\s2MemberX\Pro\Classes\AppFacades as a;
use WebSharks\WpSharks\s2MemberX\Pro\Classes\SCoreFacades as s;
use WebSharks\WpSharks\s2MemberX\Pro\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Restriction.
 *
 * @since 16xxxx Security gate.
 */
class Restriction extends SCoreClasses\SCore\Base\Core
{
    /**
     * Icon.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $icon;

    /**
     * Post type.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $post_type;

    /**
     * Client-side prefix.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $client_side_prefix;

    /**
     * Screen.
     *
     * @since 16xxxx
     *
     * @type \WP_Screen|null
     */
    public $screen;

    /**
     * Is screen mobile?
     *
     * @since 16xxxx
     *
     * @type bool
     */
    public $screen_is_mobile;

    /**
     * Class constructor.
     *
     * @since 16xxxx Restrictions.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->icon               = file_get_contents(dirname(__FILE__, 4).'/client-s/images/admin/icon.svg');
        $this->post_type          = 'restriction'; // New post type (unprefixed in this case).
        $this->client_side_prefix = 'fdbmjuxwzjfjtaucytprkbcqfpftudyg';

        $this->screen = $this->screen_is_mobile = null; // See `onCurrentScreen()`.
    }

    /**
     * Register post type.
     *
     * @since 16xxxx Restrictions.
     */
    public function onInitRegisterPostType()
    {
        register_post_type(
            $this->post_type,
            [
                'public'       => false,
                'hierarchical' => false,

                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => true,

                'rewrite'     => false,
                'has_archive' => false,
                'query_var'   => false,

                'supports'         => ['title'],
                'delete_with_user' => false,

                'menu_position' => null,
                'menu_icon'     => 'data:image/svg+xml;base64,'.base64_encode($this->icon),
                'description'   => __('Content Restriction', 's2member-x'),

                'labels' => [ // See: <http://jas.xyz/244m2Sd>
                    'name'                  => __('Restrictions', 's2member-x'),
                    'singular_name'         => __('Restriction', 's2member-x'),
                    'add_new'               => __('Add Restriction', 's2member-x'),
                    'add_new_item'          => __('Add New Restriction', 's2member-x'),
                    'edit_item'             => __('Edit Restriction', 's2member-x'),
                    'new_item'              => __('New Restriction', 's2member-x'),
                    'view_item'             => __('View Restriction', 's2member-x'),
                    'search_items'          => __('Search Restrictions', 's2member-x'),
                    'not_found'             => __('No Restrictions found', 's2member-x'),
                    'not_found_in_trash'    => __('No Restrictions found in Trash', 's2member-x'),
                    'parent_item_colon'     => __('Parent Restriction:', 's2member-x'),
                    'all_items'             => __('All Restrictions', 's2member-x'),
                    'archives'              => __('Restriction Archives', 's2member-x'),
                    'insert_into_item'      => __('Insert into Restriction', 's2member-x'),
                    'uploaded_to_this_item' => __('Upload to this Restriction', 's2member-x'),
                    'featured_image'        => __('Set Featured Image', 's2member-x'),
                    'remove_featured_image' => __('Remove Featured Image', 's2member-x'),
                    'use_featured_image'    => __('Use as Featured Image', 's2member-x'),
                    'filter_items_list'     => __('Filter Restrictions List', 's2member-x'),
                    'items_list_navigation' => __('Restrictions List Navigation', 's2member-x'),
                    'items_list'            => __('Restrictions List', 's2member-x'),
                    'menu_name'             => __('Restrictions', 's2member-x'),
                    'name_admin_bar'        => __('Restriction', 's2member-x'),
                ],

                'map_meta_cap'    => true,
                'capability_type' => [
                    $this->post_type,
                    $this->post_type.'s',
                ],
            ]
        );
        /*[pro strip-from="lite"]*/
        if (s::getOption('restriction_categories_enable')) {
            register_taxonomy(
                $this->post_type.'_category',
                $this->post_type,
                [
                    'public'       => false,
                    'hierarchical' => true,

                    'show_ui'            => true,
                    'show_in_menu'       => true,
                    'show_in_nav_menus'  => false,
                    'show_tagcloud'      => false,
                    'show_in_quick_edit' => true,
                    'show_admin_column'  => true,

                    'rewrite'   => false,
                    'query_var' => false,

                    'description' => __('Content Restriction Tags/Categories', 's2member-x'),

                    'labels' => [ // See: <http://jas.xyz/244m1Oc>
                        'name'                       => __('Categories', 's2member-x'),
                        'singular_name'              => __('Category', 's2member-x'),
                        'search_items'               => __('Search Categories', 's2member-x'),
                        'popular_items'              => __('Popular Categories', 's2member-x'),
                        'all_items'                  => __('All Categories', 's2member-x'),
                        'parent_item'                => __('Parent Category', 's2member-x'),
                        'parent_item_colon'          => __('Parent Category:', 's2member-x'),
                        'edit_item'                  => __('Edit Category', 's2member-x'),
                        'view_item'                  => __('View Category', 's2member-x'),
                        'update_item'                => __('Update Category', 's2member-x'),
                        'add_new_item'               => __('Add New Category', 's2member-x'),
                        'new_item_name'              => __('New Category Name', 's2member-x'),
                        'separate_items_with_commas' => __('Separate Categories w/ Commas', 's2member-x'),
                        'add_or_remove_items'        => __('Add or Remove Categories', 's2member-x'),
                        'choose_from_most_used'      => __('Choose From the Most Used Categories', 's2member-x'),
                        'not_found'                  => __('No Categories Found', 's2member-x'),
                        'no_terms'                   => __('No Categories', 's2member-x'),
                        'items_list_navigation'      => __('Categories List Navigation', 's2member-x'),
                        'items_list'                 => __('Categories List', 's2member-x'),
                        'menu_name'                  => __('Categories', 's2member-x'),
                        'name_admin_bar'             => __('Category', 's2member-x'),
                        'archives'                   => __('All Categories', 's2member-x'),
                    ],

                    'capabilities' => [
                        'assign_terms' => 'edit_'.$this->post_type.'s',
                        'edit_terms'   => 'edit_'.$this->post_type.'s',
                        'manage_terms' => 'edit_others_'.$this->post_type.'s',
                        'delete_terms' => 'delete_others_'.$this->post_type.'s',
                    ],
                ]
            );
        }
        /*[/pro]*/
    }

    /**
     * Get screen object.
     *
     * @since 16xxxx Restrictions.
     */
    public function onCurrentScreen(\WP_Screen $screen)
    {
        if (!s::isMenuPageForPostType($this->post_type)) {
            return; // Not applicable.
        }
        $this->screen           = $screen;
        $this->screen_is_mobile = wp_is_mobile();
    }

    /**
     * Position restrictions.
     *
     * @since 16xxxx Restrictions.
     */
    public function onMenuOrder(array $menu_items): array
    {
        $woocommerce_item = 'woocommerce'; // Position after this.
        $woocommerce_key  = array_search($woocommerce_item, $menu_items, true);

        $restriction_item = 'edit.php?post_type='.$this->post_type;
        $restriction_key  = array_search($restriction_item, $menu_items, true);

        if ($woocommerce_key === false || $restriction_key === false) {
            return $menu_items; // Not possible.
        }
        $new_menu_items = []; // Initialize new menu items.

        foreach ($menu_items as $_key => $_item) {
            if ($_item !== $restriction_item) {
                $new_menu_items[] = $_item;
            }
            if ($_item === $woocommerce_item) {
                $new_menu_items[] = $restriction_item;
            }
        } // unset($_key, $_item); // Housekeeping.

        return $new_menu_items;
    }

    /**
     * Add meta boxes.
     *
     * @since 16xxxx Restrictions.
     *
     * @param string $post_type Post type.
     */
    public function onAddMetaBoxes(string $post_type)
    {
        if ($post_type !== $this->post_type) {
            return; // Not applicable.
        }
        $meta_boxes = [
            $this->client_side_prefix.'-about'          => ['title' => __('About Restrictions', 's2member-x'), 'callback' => 'aboutRestrictionsMetaBox'],
            $this->client_side_prefix.'-post-ids'       => ['title' => __('Protected Posts', 's2member-x'), 'callback' => 'restrictsPostIdsMetaBox'],
            $this->client_side_prefix.'-post-types'     => ['title' => __('Protected Post Types', 's2member-x'), 'callback' => 'restrictsPostTypesMetaBox'],
            $this->client_side_prefix.'-taxonomy-terms' => ['title' => __('Protected Cats/Tags/Terms', 's2member-x'), 'callback' => 'restrictsTaxnomyTermsMetaBox'],
            $this->client_side_prefix.'-roles'          => ['title' => __('Protected Roles', 's2member-x'), 'callback' => 'restrictsRolesMetaBox'],
            $this->client_side_prefix.'-caps'           => ['title' => __('Protected Capabilities', 's2member-x'), 'callback' => 'restrictsCapsMetaBox'],
            $this->client_side_prefix.'-uri-patterns'   => ['title' => __('Protected URI Patterns', 's2member-x'), 'callback' => 'restrictsUriPatternsMetaBox'],
        ];
        $closed_meta_boxes = get_user_option('closedpostboxes_'.$this->post_type);

        foreach ($meta_boxes as $_id => $_data) {
            add_meta_box($_id, $_data['title'], [$this, $_data['callback']], null, 'normal', 'default', []);
            add_filter('postbox_classes_'.$this->post_type.'_'.$_id, function (array $classes) use ($closed_meta_boxes, $_id) : array {
                return !is_array($closed_meta_boxes) && (int) ($_GET['edit'] ?? '') !== $_id
                    && !in_array($_id, [$this->client_side_prefix.'-about'], true)
                    ? array_merge($classes, ['closed']) : $classes;
            });
        } // unset($_id, $_data); // Housekeeping.
    }

    /**
     * Default hidden meta boxes.
     *
     * @since 16xxxx Restrictions.
     *
     * @param array      $hidden Default hidden.
     * @param \WP_Screen $screen Screen object.
     *
     * @return array Default hidden meta boxes.
     */
    public function onDefaultHiddenMetaBoxes(array $hidden, \WP_Screen $screen)
    {
        if (!s::isMenuPageForPostType($this->post_type)) {
            return $hidden; // Not applicable.
        }
        if ($screen->id !== $this->screen->id) {
            return $hidden; // Not applicable.
        }
        return array_diff($hidden, ['slugdiv']);
    }

    /**
     * Enqueue styles/scripts.
     *
     * @since 16xxxx Restrictions.
     */
    public function onAdminEnqueueScripts()
    {
        if (!s::isMenuPageForPostType($this->post_type)) {
            return; // Not applicable.
        }
        s::enqueueJQueryChosen(); // Enqueue jQuery Chosen plugin.

        wp_enqueue_style($this->client_side_prefix.'-post-type', c::appUrl('/client-s/css/admin/restriction-post-type.min.css'), [], $this->App::VERSION, 'all');
        wp_enqueue_script($this->client_side_prefix.'-post-type', c::appUrl('/client-s/js/admin/restriction-post-type.min.js'), ['jquery', 'jquery-chosen'], $this->App::VERSION, true);

        wp_localize_script(
            $this->client_side_prefix.'-post-type',
            $this->client_side_prefix.'RestrictionPostTypeData',
            [
                'is' => [
                    'mobile' => $this->screen_is_mobile,
                ],
                'i18n' => [
                    'titlePlaceholder' => $this->screen_is_mobile
                        ? __('Descriptive summary here...', 's2member-x')
                        : __('Enter a descriptive summary here...', 's2member-x'),
                    'slugPlaceholder' => __('Enter a unique identifier...', 's2member-x'),
                    'suggestedLabel'  => __('Suggested', 's2member-x'),
                    'optionalLabel'   => __('Optional', 's2member-x'),
                ],
            ]
        );
    }

    /**
     * About meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function aboutRestrictionsMetaBox(\WP_Post $post, array $args = [])
    {
        echo '<div class="-about">';

        echo    '<h4>'.__('A Restriction Serves Two Purposes', 's2member-x').'</h4>';
        echo    '<ol>';
        echo        '<li>'.__('It protects content in WordPress. Each individual Restriction that you add can protect multiple Post/Pages/etc.', 's2member-x').'</li>';
        echo        '<li>'.__('It can grant access to the content you\'ve protected; i.e., you can associate each Restriction with a unique ID/Slug and then sell (or otherwise allow) access to what a Restriction protects.', 's2member-x').'</li>';
        echo    '</ol>';
        echo    '<p><em>'.__('Therefore, it helps to think about Restrictions as both a form of protection and also as a way to prepare packages that can be accessed by others.', 's2member-x').'</em></p>';

        echo '</div>';
    }

    /**
     * Post IDs meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsPostIdsMetaBox(\WP_Post $post, array $args = [])
    {
        $current_post_ids = $this->getMeta($post->ID, 'post_ids');

        $post_id_select_options = s::postSelectOptions([
            'exclude_post_ids'           => a::systematicPostIds(),
            'include_post_types'         => get_post_types(['public' => true]),
            'exclude_post_types'         => a::systematicPostTypes(),
            'exclude_password_protected' => false,
            'allow_empty'                => false,
            'current_post_ids'           => $current_post_ids,
        ]);
        echo '<div class="-meta -post-ids">';

        if ($post_id_select_options) {
            echo '<p class="-field -select-field"><select name="'.esc_attr($this->post_type.'_post_ids').'" autocomplete="off" data-toggle="'.($this->screen_is_mobile ? '' : 'jquery-chosen').'" multiple>'.$post_id_select_options.'</select></p>';
            echo $this->screen_is_mobile ? '<p class="-tip -select-tip">'.__('<strong>Tip:</strong> Use <kbd>Ctrl</kbd> or <kbd>⌘</kbd> to select multiple options.', 's2member-x').'</p>' : '';
        } else {
            echo '<p class="-heading -input-heading">'.__('Post IDs to Restrict (WordPress Post IDs, comma-delimited):', 's2member-x').'</p>';
            echo '<p class="-field -input-field"><input type="text" name="'.esc_attr($this->post_type.'_post_ids').'" autocomplete="off" spellcheck="false" placeholder="'.__('e.g., 123, 345, 789, 3492', 's2member-x').'" value="'.esc_attr(implode(',', $current_post_ids)).'"></p>';
        }
        echo '</div>';
    }

    /**
     * Post types meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsPostTypesMetaBox(\WP_Post $post, array $args = [])
    {
        $current_post_types = $this->getMeta($post->ID, 'post_types');

        $post_type_select_options = s::postTypeSelectOptions([
            'include'            => get_post_types(['public' => true]),
            'exclude'            => a::systematicPostTypes(),
            'allow_empty'        => false,
            'current_post_types' => $current_post_types,
        ]);
        echo '<div class="-meta -post-types">';

        if ($post_type_select_options) {
            echo '<p class="-field -select-field"><select name="'.esc_attr($this->post_type.'_post_types').'" autocomplete="off" data-toggle="'.($this->screen_is_mobile ? '' : 'jquery-chosen').'" multiple>'.$post_type_select_options.'</select></p>';
            echo $this->screen_is_mobile ? '<p class="-tip -select-tip">'.__('<strong>Tip:</strong> Use <kbd>Ctrl</kbd> or <kbd>⌘</kbd> to select multiple options.', 's2member-x').'</p>' : '';
        } else {
            echo '<p class="-heading -input-heading">'.__('Post Types to Restrict (WordPress Post Types, comma-delimited):', 's2member-x').'</p>';
            echo '<p class="-field -input-field"><input type="text" name="'.esc_attr($this->post_type.'_post_types').'" autocomplete="off" spellcheck="false" placeholder="'.__('e.g., post, article, movie, book', 's2member-x').'" value="'.esc_attr(implode(',', $current_post_types)).'"></p>';
        }
        echo    '<p>'.__('<strong>Note:</strong> Protecting a Post Type will automatically protect <em>all</em> Posts of that type (everything).', 's2member-x').'</p>';

        echo '</div>';
    }

    /**
     * Taxonomy terms meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsTaxnomyTermsMetaBox(\WP_Post $post, array $args = [])
    {
        // @TODO : Ugh, this will be a fun one!
    }

    /**
     * Roles meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsRolesMetaBox(\WP_Post $post, array $args = [])
    {
        $current_roles = $this->getMeta($post->ID, 'roles');

        $role_select_options = s::roleSelectOptions([
            'exclude'       => a::systematicRoleIds(),
            'allow_empty'   => false,
            'current_roles' => $current_roles,
        ]);
        echo '<div class="-meta -roles">';

        if ($role_select_options) {
            echo '<p class="-heading -select-heading">'.__('<a href="https://developer.wordpress.org/plugins/users/roles-and-capabilities/" target="_blank">WordPress Roles</a> are predefined Capability sets. See also: <a href="https://wordpress.org/plugins/user-role-editor/" target="_blank">Role Editor</a>', 's2member-x').'</p>';
            echo '<p class="-field -select-field"><select name="'.esc_attr($this->post_type.'_roles').'" autocomplete="off" data-toggle="'.($this->screen_is_mobile ? '' : 'jquery-chosen').'" multiple>'.$role_select_options.'</select></p>';
            echo $this->screen_is_mobile ? '<p class="-tip -select-tip">'.__('<strong>Tip:</strong> Use <kbd>Ctrl</kbd> or <kbd>⌘</kbd> to select multiple options.', 's2member-x').'</p>' : '';
        } else {
            echo '<p class="-heading -input-heading">'.__('<a href="https://developer.wordpress.org/plugins/users/roles-and-capabilities/" target="_blank">WordPress Roles</a> in comma-delimited format. See also: <a href="https://wordpress.org/plugins/user-role-editor/" target="_blank">Role Editor</a>', 's2member-x').'</p>';
        }
        echo    '<p>'.sprintf(__('<strong>Note:</strong> There are some Roles that are "reserved" internally and cannot be associated with a Restriction. They include: %1$s', 's2member-x'), esc_html(implode(', ', a::systematicRoleIds()))).'</p>';

        echo '</div>';
    }

    /**
     * Caps meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsCapsMetaBox(\WP_Post $post, array $args = [])
    {
        $current_caps = $this->getMeta($post->ID, 'caps');
        $auto_prefix  = s::getOption('restricted_caps_auto_prefix');

        echo '<div class="-meta -caps">';

        echo    '<p class="-heading -input-heading">'.__('<a href="https://developer.wordpress.org/reference/functions/current_user_can/" target="_blank">WordPress Capabilities</a> in comma-delimited format:', 's2member-x').'</p>';
        echo    '<p class="-field -input-field"><input type="text" name="'.esc_attr($this->post_type.'_caps').'" autocomplete="off" spellcheck="false" placeholder="'.__('e.g., something, something_else, pro_membership', 's2member-x').'" value="'.esc_attr(implode(',', $current_caps)).'"></p>';

        if (!$auto_prefix) {
            echo '<p class="-tip -input-tip">'.__('<strong>Tip:</strong> You can enter <a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Built-In Capabilities</a> or type arbitrary word fragments (i.e., Custom Capabilities) that you will test using: <a href="https://developer.wordpress.org/reference/functions/current_user_can/" target="_blank">current_user_can()</a>.', 's2member-x').'</p>';
            echo '<p><em>'.__('<strong>Note:</strong> Automatic Capability prefixing has been disabled on this installation. What you type is what you test, as entered. Please be careful about what you expose.', 's2member-x').'</em></p>';
        } else {
            echo '<p class="-tip -input-tip">'.sprintf(__('<strong>Note:</strong> Capabilities (i.e., what you enter here) are automatically prefixed with <code>%1$s</code> internally. You can test them with: <a href="https://developer.wordpress.org/reference/functions/current_user_can/" target="_blank" style="text-decoration:none;">current_user_can(\'%1$s<code style="padding:0;">something</code>\')</a>', 's2member-x'), esc_html($auto_prefix)).'</p>';
        }
        echo '</div>';
    }

    /**
     * URI patterns meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsUriPatternsMetaBox(\WP_Post $post, array $args = [])
    {
        $current_uri_patterns = $this->getMeta($post->ID, 'uri_patterns');

        echo '<div class="-meta -uri-patterns">';

        echo    '<p class="-heading -textarea-heading">'.__('URI Patterns in line-delmited format (i.e., one per line):', 's2member-x').'</p>';
        echo    '<p class="-field -textarea-field"><textarea name="'.esc_attr($this->post_type.'_uri_patterns').'" autocomplete="off" spellcheck="false" wrap="soft" placeholder="'.__('e.g., /path/to/members-only/**', 's2member-x').'">'.esc_textarea(implode("\n", $current_uri_patterns)).'</textarea></p>';
        echo    '<p class="-tip -input-tip">'.__('<strong>Tip:</strong> This allows you to protect any location that is served by WordPress, even if it doesn\'t have a formal WordPress content-type (i.e., even if it\'s not a Post/Page).', 's2member-x').'</p>';

        echo    '<h4>'.__('A "URI" is everything after the domain name in a URL:', 's2member-x').'</h4>';
        echo    '<ul class="-examples"><li>'.__('http://example.com<code>/this/is/the-URI/part/in-a-location</code>', 's2member-x').'</li></ul>';

        echo    '<h4>'.__('This watered-down regex syntax can be used in your patterns:', 's2member-x').'</h4>';
        echo    '<ul class="-examples">'; // Expects the use of an wregx (watered-down regex) syntax.
        echo        '<li>'.__('<code>*</code> Matches zero or more characters that are not a <em><strong>/</strong></em>', 's2member-x').'</li>';
        echo        '<li>'.__('<code>**</code> Matches zero or more characters of any kind.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>?</code> Matches exactly one character that is not a <em><strong>/</strong></em>', 's2member-x').'</li>';
        echo        '<li>'.__('<code>??</code> Matches exactly one character of any kind.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>[abc]</code> Matches exactly one character: <em>a</em>, <em>b</em>, or <em>c</em>.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>[a-z0-9]</code> Matches exactly one character: <em>a</em> thru <em>z</em> or <em>0</em> thru <em>9</em>.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>[!abc]</code> A leading <em>!</em> inside <em>[]</em> negates; i.e., anything that is not: <em>a</em>, <em>b</em>, or <em>c</em>.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>{abc,def}</code> Matches the fragment <em>abc</em> or <em>def</em> (one or the other).', 's2member-x').'</li>';
        echo        '<li>'.__('<code>{abc,def,}</code> Matches <em>abc</em>, <em>def</em> or nothing; i.e., an optional match.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>{/**,}</code> Matches a <em>/</em> followed by zero or more characters. Also matches end of string.', 's2member-x').'</li>';
        echo        '<li>'.__('<code>[*?[]{}]</code> Matches a literal special character. One of: <em>?*[]{}</em> explicitly.', 's2member-x').'</li>';
        echo    '</ul>';
        echo    '<p>'.__('<strong>Note:</strong> All comparisons are caSe-insensitive. You <strong>must match the entire URI</strong> and not just a small portion of it; i.e., <em>*</em> and <em>**</em> are your friends. Any trailing slash and/or query string variables on the end of a URI (e.g., <em>.../?p=123&amp;key=value</em>) are stripped before comparison so you don\'t need to worry about them. However, if your pattern includes <code>[?]</code> (i.e., a literal <em>?</em> indicating that you DO want to check the trailing slash &amp; query string), then they are NOT stripped away when comparing, and the pattern you give will be capable of matching. Just remember that query string variables can appear in any arbitrary order, as entered by a user. If you check for query strings, use <em>**</em> around your check; e.g., <em>/members-only{/,}[?]**key=value**</em>', 's2member-x').'</p>';
        echo    '<p>'.sprintf(__('<strong>Note:</strong> There are some URIs that are "reserved" internally and cannot be associated with a Restriction. These reserved URIs include the following: %1$s', 's2member-x'), '<ul><li>'.implode('</li><li>', array_map('esc_html', a::systematicUriPatterns())).'</li></ul>').'</p>';

        echo '</div>';
    }

    /**
     * Get meta values.
     *
     * @since 16xxxx Restrictions.
     *
     * @param string|int $post_id Post ID.
     * @param string     $key     Meta key.
     *
     * @return array Meta values.
     */
    public function getMeta($post_id, string $key): array
    {
        $post_id = (int) $post_id; // Force integer.

        $values = get_post_meta($post_id, $this->post_type.'_'.$key);

        return is_array($values) ? $values : [];
    }

    /**
     * Update meta values.
     *
     * @since 16xxxx Restrictions.
     *
     * @param string|int $post_id Post ID.
     * @param string     $key     Meta key.
     * @param array      $values  Meta values.
     */
    public function updateMeta(int $post_id, string $key, array $values)
    {
        $post_id = (int) $post_id; // Force integer.

        $this->deleteMeta($post_id, $this->post_type.'_'.$key);

        foreach ($values as $_value) {
            add_post_meta($post_id, $this->post_type.'_'.$key, $_value);
        } // unset($_value); // Housekeeping.
    }

    /**
     * Delete meta values.
     *
     * @since 16xxxx Restrictions.
     *
     * @param string|int $post_id Post ID.
     * @param string     $key     Meta key.
     */
    public function deleteMeta(int $post_id, string $key)
    {
        $post_id = (int) $post_id; // Force integer.

        delete_post_meta($post_id, $this->post_type.'_'.$key);
    }
}
