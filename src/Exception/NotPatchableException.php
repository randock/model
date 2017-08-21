<?php

declare(strict_types=1);

namespace Randock\Model\Exception;

/**
 * Class NotPatchableException.
 */
class NotPatchableException extends \Exception
{
    public function __construct()
    {
        parent::__construct('randock.exception.not_patchable');
    }
}
