<?php

namespace ITMOSOFT\B2B\Enums;

enum B2BBuyerRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Buyer = 'buyer';
    case Approver = 'approver';
    case Viewer = 'viewer';
}
