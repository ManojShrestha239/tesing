<?php
declare(strict_types=1);

namespace App\Enums\v2\siteRegistration;

enum FollowUpType: string
{
    case CALL = 'call';
    case VISIT = 'visit';
    case DEMO = 'demo';
    case SAMPLING = 'sampling';
    case OTHERS = 'others';
}

