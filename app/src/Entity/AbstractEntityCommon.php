<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Repository\BatchCustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * AbstractEntityCommon
 */
abstract class AbstractEntityCommon
{
    public function setFieldWithControl($field, $value)
    {
        if (!empty($value)) {
            $this->{'set'.ucfirst($field)}($value);
        }

        return $this;
    }
}
