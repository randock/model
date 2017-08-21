<?php

declare(strict_types=1);

namespace Randock\Model\Definition;

/**
 * Interface PatchableInterface.
 */
interface PatchableInterface
{
    public function patch(\stdClass $data);
}
