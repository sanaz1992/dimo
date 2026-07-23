<?php

namespace Modules\Order\Exceptions;

use Exception;

class OutOfStockException extends Exception
{
    protected $item;

    public function __construct(string $message, $item = null)
    {
        parent::__construct($message);
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }
}
