<?php

namespace Nxmad\Larapay\Contracts;

interface Fields
{
    // Internal payment id
    const ID = 'id';

    // External payment id
    const UID = 'uid';

    // Payment description/comment/etc.
    const DESCRIPTION = 'description';

    // Payment date
    const DATE = 'date';

    // Payment amount
    const AMOUNT = 'amount';

    // Payment currency
    const CURRENCY = 'currency';

    // Payment request signature
    const SIGNATURE = 'signature';

    // Determine if payment made in test environment
    const TEST = 'test';
}
