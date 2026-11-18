<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    const OWNER = 'Owner';
    const SUPER_ADMIN = 'Super Admin';
    const ADMIN = 'Admin';
    const KEPALA_STAF_DESAINER = 'Kepala Staf Desainer';
    const DESAINER = 'Desainer';
    const CUSTOMER = 'Customer';
}
