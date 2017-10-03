<?php


namespace Randock\Model\Traits;

use Doctrine\Common\Collections\Collection;
use Randock\Model\Definition\PatchableInterface;
use Randock\Model\Exception\NotPatchableException;

trait PatchableTrait
{
    /**
     * @param \stdClass $data
     *
     * @throws NotPatchableException
     *
     * @return array
     */
    public function patch(\stdClass $data)
    {
        $extraFields = [];
        foreach ($data as $key => $value) {
            // getter and setter
            $getter = sprintf('get%s', ucfirst($key));
            $setter = sprintf('set%s', ucfirst($key));

            if (method_exists($this, $getter) && method_exists($this, $setter)) {
                // Model? Execute it's patch
                if ($this->$getter() instanceof PatchableInterface) {
                    if (!$value instanceof \stdClass) {
                        throw new NotPatchableException();
                    }

                    $aux = $this->$getter()->patch($value);

                    if (!empty($aux)) {
                        $extraFields[$key] = $aux;
                    }
                    continue;
                }

                // ArrayCollection
                if ($this->$getter() instanceof Collection) {
                    if (count($value) !== $this->$getter()->count()) {
                        throw new NotPatchableException();
                    }

                    for ($i = 0; $i < count($value); ++$i) {
                        $row = $value[$i];

                        $aux = $this->$getter()[$i]->patch($row);
                        if (!empty($aux)) {
                            if (is_array($aux)) {
                                foreach ($aux as $auxKey => $auxValue) {
                                    $extraFields[$key][$i][$auxKey][$auxValue] = '';
                                }
                            } else {
                                $extraFields[$key][$i][$aux] = '';
                            }
                        }
                    }
                    continue;
                }

                // DateTime? Change the value
                if ($this->$getter() instanceof \DateTime) {
                    $value = new \DateTime($value);
                }

                // default, use setter
                if (is_callable([$this, $setter])) {
                    $this->$setter($value);
                }
            } else {
                $extraFields[$key] = $value;
            }
        }

        return $extraFields;
    }
}
