<?php

namespace Ntb\RestAPI;

/**
 * Implements the IPermission interface and uses the Silverstripe permission system.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SilverstripePermission implements IPermissionChecks {

    /**
     * @param \Member $member
     * @return bool
     */
    public function isAdmin($member) {
        return \Permission::checkMember($member, 'ADMIN');
    }
}