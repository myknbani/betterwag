<?php

namespace App\Enums;

enum CampaignType: string
{
    case Recurring = 'recurring';
    case OneOff = 'one_off';
}
