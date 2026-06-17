<?php

namespace App\Enums;

enum Role: string
{
    /** app-wide God mode */
    case Admin = 'admin';

    /** shelter-wide God mode */
    case ShelterManager = 'shelter_manager';

    /** non-shelter staff who donates to campaigns, fosters, adopts, etc. */
    case External = 'external';
}
