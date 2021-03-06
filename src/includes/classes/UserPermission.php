<?php
/**
 * User permission.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Classes;

use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Classes;
use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Interfaces;
use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Traits;
#
use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Classes\AppFacades as a;
use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Classes\SCoreFacades as s;
use WebSharks\WpSharks\WooCommerce\Restrictions\Pro\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * User permission.
 *
 * @since 160524 User permission.
 */
class UserPermission extends SCoreClasses\SCore\Base\Core
{
    /**
     * Valid statuses.
     *
     * @since 160524 User permission.
     *
     * @var array Valid statuses.
     */
    protected $valid_statuses;

    /**
     * Class constructor.
     *
     * @since 160524 User permission.
     *
     * @param Classes\App $App  Instance.
     * @param \StdClass   $data Permission data.
     */
    public function __construct(Classes\App $App, \StdClass $data)
    {
        parent::__construct($App);

        $this->valid_statuses = a::userPermissionStatuses();

        $this->fillProperties($data); // Overloading.
    }

    /**
     * A valid permission?
     *
     * @since 160524 User permission.
     *
     * @return bool True if permission is valid.
     */
    public function isValid(): bool
    {
        if (!$this->user_id) {
            return false;
        } elseif (!$this->restriction_id) {
            return false;
        } elseif (!isset($this->valid_statuses[$this->status])) {
            return false;
        } elseif ($this->access_time && $this->expire_time && $this->access_time >= $this->expire_time) {
            return false;
        } elseif (($this->order_id || $this->subscription_id || $this->product_id || $this->item_id) && !$this->expire_directive) {
            return false;
        }
        return true; // No problems.
    }

    /**
     * Allowed to access?
     *
     * @since 160524 User permission.
     *
     * @return bool True if allowed to access.
     */
    public function isAllowed(): bool
    {
        if (!$this->isValid()) {
            return false;
        } elseif ($this->is_trashed) {
            return false;
        } elseif ($this->status !== 'enabled') {
            return false;
        } elseif ($this->access_time && $this->access_time > time()) {
            return false;
        } elseif ($this->expire_time && $this->expire_time <= time()) {
            return false;
        }
        // Note: Not checking `expire_directive` here, because date/time-based diretives
        // are calculated ahead of time by product permissions. Thus, an `expire_time` will exist
        // if the `expire_directive` is date/time-based; i.e., checking `expire_time` is enough in this context.

        return true; // Allowed access.
    }

    /**
     * Delete this permission.
     *
     * @since 160524 User permission.
     *
     * @note Not called during a mass deletion.
     */
    public function delete()
    {
        $WpDb  = s::wpDb(); // DB instance.
        $where = ['ID' => $this->ID]; // This ID.

        if ($this->ID) { // Delete existing permission w/ ID.
            s::doAction('before_user_permissions_delete', $where);
            $WpDb->delete(s::dbPrefix().'user_permissions', $where);
        }
        if ($this->user_id) { // Belonged to a user?
            a::clearUserPermissionsCache($this->user_id);
        }
        if ($this->ID) { // Deleted existing permission w/ ID.
            s::doAction('user_permissions_deleted', $where);
        }
    }

    /**
     * Update/insert/save permission.
     *
     * @since 160524 Adding user permissions.
     *
     * @param \StdClass|null $data Object properties.
     *
     * @note This method is not called during a mass update.
     */
    public function update(\StdClass $data = null)
    {
        if ($data) { // Optional data.
            $this->fillProperties($data);
        }
        if (!$this->isValid()) { // Validation.
            throw c::issue('Invalid properties.');
        }
        if ($this->ID) { // Update existing permission.
            $WpDb                   = s::wpDb();
            $this->last_update_time = time();

            $where       = ['ID' => $this->ID];
            $update_data = c::cloneArray($this->overloadArray());
            unset($update_data['ID']); // Exclude primary key ID.

            s::doAction('before_user_permissions_update', $where, $update_data);

            if ($WpDb->update(s::dbPrefix().'user_permissions', $update_data, $where) === false) {
                $this->ID = 0; // Possible race condition; e.g., ID no longer exists.
                return $this->update($data); // Insert new.
            }
            a::clearUserPermissionsCache($this->user_id); // Clear user cache.

            s::doAction('user_permissions_updated', $where, $update_data);
        } else { // Insertion of a brand new permission.
            $WpDb                 = s::wpDb();
            $this->insertion_time = $this->last_update_time = time();

            $insert_data = c::cloneArray($this->overloadArray());
            unset($insert_data['ID']); // Exclude primary key ID.

            s::doAction('before_user_permissions_insert', [], $insert_data);

            if ((int) $WpDb->insert(s::dbPrefix().'user_permissions', $insert_data) !== 1) {
                throw c::issue('User permission insertion failure.');
            } elseif (!($this->ID = abs((int) $WpDb->insert_id))) {
                throw c::issue('Unable to aquire insert ID.');
            }
            a::clearUserPermissionsCache($this->user_id); // Clear user cache.

            s::doAction('user_permissions_inserted', ['ID' => $this->ID], $insert_data);
        }
    }

    /**
     * Fill properties from data.
     *
     * @since 160524 Adding user permissions.
     *
     * @param \StdClass $data Object properties.
     */
    protected function fillProperties(\StdClass $data)
    {
        $is_overloaded = $this->isOverloaded();

        if ($is_overloaded && (isset($data->ID) || isset($data->user_id))) {
            throw c::issue('Trying to update contruct-only property.');
        }
        if (!$is_overloaded) { // Called by constructor?
            $is_overloaded = true; // Overloading now.

            $defaults = [
                'ID'      => 0,
                'user_id' => 0,

                'order_id'        => 0,
                'subscription_id' => 0,
                'product_id'      => 0,
                'item_id'         => 0,

                'restriction_id'   => 0,
                'access_time'      => 0,
                'expire_time'      => 0,
                'expire_directive' => '',

                'status'     => '',
                'is_trashed' => 0,

                'display_order' => 0,

                'insertion_time'   => 0,
                'last_update_time' => 0,
            ];
            $defaults = (object) $defaults; // Converts to object now.
            // Separate line; see: <https://bugs.php.net/bug.php?id=72219>

            $this->overload($defaults, true); // Public read/write access for performance.
            // Please do NOT write to properties directly. Use {@link update()}.
        }
        $this->ID      = abs((int) ($data->ID ?? $this->ID ?? 0));
        $this->user_id = abs((int) ($data->user_id ?? $this->user_id ?? 0));

        $this->order_id        = abs((int) ($data->order_id ?? $this->order_id ?? 0));
        $this->subscription_id = abs((int) ($data->subscription_id ?? $this->subscription_id ?? 0));
        $this->product_id      = abs((int) ($data->product_id ?? $this->product_id ?? 0));
        $this->item_id         = abs((int) ($data->item_id ?? $this->item_id ?? 0));

        $this->restriction_id   = abs((int) ($data->restriction_id ?? $this->restriction_id ?? 0));
        $this->access_time      = abs((int) ($data->access_time ?? $this->access_time ?? 0));
        $this->expire_time      = abs((int) ($data->expire_time ?? $this->expire_time ?? 0));
        $this->expire_directive = mb_strtolower((string) ($data->expire_directive ?? $this->expire_directive ?? ''));

        $this->status     = mb_strtolower((string) ($data->status ?? $this->status ?? ''));
        $this->is_trashed = abs((int) ($data->is_trashed ?? $this->is_trashed ?? 0));

        $this->display_order = abs((int) ($data->display_order ?? $this->display_order ?? 0));

        $this->insertion_time   = abs((int) ($data->insertion_time ?? $this->insertion_time ?? 0));
        $this->last_update_time = abs((int) ($data->last_update_time ?? $this->last_update_time ?? 0));

        if ($this->status !== 'expired' && $this->expire_time && $this->expire_time <= time()) {
            $this->status = 'expired'; // Force a matching status.
        } elseif ($this->status === 'expired' && $this->expire_time && $this->expire_time > time()) {
            $this->status = 'enabled'; // Force a matching status.
        }
    }
}
