<?php

namespace App\Enums;

enum AdoptionStatus: string
{
    case Rescued = 'rescued';
    case Available = 'available';
    case Fostered = 'fostered';
    case Adopted = 'adopted';
    case RainbowBridge = 'rainbow_bridge';
}
