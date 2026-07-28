<?php

namespace Modules\Order\Enums;

class OrderInventoryStatus
{
    public const NOT_RESERVED = 'not_reserved';

    public const RESERVED = 'reserved';

    public const DEDUCTED = 'deducted';

    public const RELEASED = 'released';

    public const FAILED = 'failed';
}
