<?php

namespace ITMOSOFT\B2B\Enums;

enum B2BAccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Closed = 'closed';
}
