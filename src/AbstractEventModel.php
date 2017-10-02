<?php

declare(strict_types=1);

namespace Randock\Model;

use Randock\Event\Definition\DomainEvent;
use Doctrine\Common\Collections\Collection;
use Randock\Model\Definition\PatchableInterface;
use Randock\Model\Exception\NotPatchableException;

/**
 * Class EventModel.
 */
abstract class AbstractEventModel implements PatchableInterface
{

}
