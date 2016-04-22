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
     * Post type var.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $post_type_var;

    /**
     * Post type slug.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $post_type_slug;

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

        $this->icon           = file_get_contents(dirname(__FILE__, 4).'/client-s/images/icon.svg');
        $this->post_type      = $this->App->Config->©brand['©prefix'].'_restriction';
        $this->post_type_var  = c::slugToVar($this->post_type);
        $this->post_type_slug = c::varToSlug($this->post_type_var);
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
                    $this->post_type_var,
                    $this->post_type_var.'s',
                ],
            ]
        );
        register_taxonomy(
            $this->post_type_var.'_cat',
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
                    'assign_terms' => 'edit_'.$this->post_type_var.'s',
                    'edit_terms'   => 'edit_'.$this->post_type_var.'s',
                    'manage_terms' => 'edit_others_'.$this->post_type_var.'s',
                    'delete_terms' => 'delete_others_'.$this->post_type_var.'s',
                ],
            ]
        );
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
            $this->post_type_slug.'s-posts'          => ['title' => __('Restricts Posts', 's2member-x'), 'callback' => 'restrictsPostsMetaBox'],
            $this->post_type_slug.'s-post-types'     => ['title' => __('Restricts Post Types', 's2member-x'), 'callback' => 'restrictsPostTypesMetaBox'],
            $this->post_type_slug.'s-taxonomy-terms' => ['title' => __('Restricts Taxnomy Terms', 's2member-x'), 'callback' => 'restrictsTaxnomyTermsMetaBox'],
            $this->post_type_slug.'s-uri-patterns'   => ['title' => __('Restricts URI Patterns', 's2member-x'), 'callback' => 'restrictsUriPatternsMetaBox'],
            $this->post_type_slug.'s-caps'           => ['title' => __('Restricts Capabilities', 's2member-x'), 'callback' => 'restrictsCapabilitiesMetaBox'],
        ];
        foreach ($meta_boxes as $_id => $_data) {
            add_meta_box($_id, $_data['title'], [$this, $_data['callback']], null, 'normal', 'default', []);
        } // unset($_id, $_data); // Housekeeping.
    }

    /**
     * Meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsPostsMetaBox(\WP_Post $post, array $args = [])
    {
        $current_type = $this->getMeta($post->ID, 'type');

        echo '<div id="'.esc_attr($this->post_type_slug.'s-item').'" style="margin-top:1em;">';
        echo    '<div id="'.esc_attr($this->post_type_slug.'s-uri-pattern-item').'">';
        echo        '<p style="margin-bottom:0;">'.__('URI Pattern to Restrict:', 's2member-x').'</p>';
        echo        '<p style="margin-top:0;"><input type="text" name="'.esc_attr($this->post_type_var.'_uri_pattern_item').'" spellcheck="false" style="width:100%;" /></p>';
        echo    '</div>';
        echo    '<div id="'.esc_attr($this->post_type_slug.'s-cap-item').'">';
        echo        '<p style="margin-bottom:0;">'.__('Capability to Restrict:', 's2member-x').'</p>';
        echo        '<p style="margin-top:0;"><input type="text" name="'.esc_attr($this->post_type_var.'_cap_item').'" spellcheck="false" style="width:100%;" /></p>';
        echo    '</div>';
        echo    '<div id="'.esc_attr($this->post_type_slug.'s-cap-item').'">';
        echo        '<p style="margin-bottom:0;">'.__('Capability to Restrict:', 's2member-x').'</p>';
        echo        '<p style="margin-top:0;"><input type="text" name="'.esc_attr($this->post_type_var.'_cap_item').'" spellcheck="false" style="width:100%;" /></p>';
        echo    '</div>';
        echo '</div>';
    }

    /**
     * Meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsPostTypesMetaBox(\WP_Post $post, array $args = [])
    {
    }

    /**
     * Meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsTaxnomyTermsMetaBox(\WP_Post $post, array $args = [])
    {
    }

    /**
     * Meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsUriPatternsMetaBox(\WP_Post $post, array $args = [])
    {
        $current_uri_patterns = $this->getMeta($post->ID, 'uri_patterns');
        $current_uri_patterns = is_array($current_uri_patterns) ? $current_uri_patterns : [];

        echo '<div id="'.esc_attr($this->post_type_slug.'s-cap-item').'">';
        echo    '<p style="margin-bottom:0;">'.__('URI Patterns to Restrict (one per line):', 's2member-x').'</p>';
        echo    '<p style="margin-top:0;"><textarea name="'.esc_attr($this->post_type_var.'_cap_item').'" spellcheck="false" wrap="soft" style="width:100%; height:200px; white-space:pre; word-wrap:normal; overflow-x:scroll;"></textarea></p>';
        echo '</div>';
    }

    /**
     * Meta box.
     *
     * @since 16xxxx Restrictions.
     *
     * @param \WP_Post $post Post object.
     * @param array    $args Callback args, if any.
     */
    public function restrictsCapabilitiesMetaBox(\WP_Post $post, array $args = [])
    {
    }

    /**
     * Get meta key.
     *
     * @since 16xxxx Restrictions.
     *
     * @param int    $post_id Post ID.
     * @param string $key     Meta key.
     *
     * @return mixed Meta value.
     */
    public function getMeta(int $post_id, string $key)
    {
        return (string) get_post_meta($post_id, $this->post_type_var.'_'.$key, true);
    }

    /**
     * Update meta key.
     *
     * @since 16xxxx Restrictions.
     *
     * @param int    $post_id Post ID.
     * @param string $key     Meta key.
     * @param mixed  $value   Meta value.
     */
    public function updateMeta(int $post_id, string $key, $value)
    {
        update_post_meta($post_id, $this->post_type_var.'_'.$key, $value);
    }

    /**
     * Delete meta key.
     *
     * @since 16xxxx Restrictions.
     *
     * @param int    $post_id Post ID.
     * @param string $key     Meta key.
     */
    public function deleteMeta(int $post_id, string $key)
    {
        delete_post_meta($post_id, $this->post_type_var.'_'.$key);
    }

    /**
     * Restriction pointers.
     *
     * @since 16xxxx Restrictions.
     */
    public function setupPointers()
    {
        // @TODO: <http://wptavern.com/create-your-own-custom-pointers-in-the-wordpress-admin>
    }
}