<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Repository\BatchCustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=BatchCustomerRepository::class)
 * @Gedmo\Loggable()
 */
class BatchCustomer
{
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $sendingDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $batchNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Deal::class, inversedBy="batchCustomers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deal;

    /**
     * @ORM\ManyToMany(targetEntity=Profile::class, inversedBy="batchCustomers")
     */
    private $profiles;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        //$this->createdAt = new \DateTime();
        //$this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendingDate(): ?\DateTimeInterface
    {
        return $this->sendingDate;
    }

    public function setSendingDate(\DateTimeInterface $sendingDate): self
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    public function getBatchNumber(): ?int
    {
        return $this->batchNumber;
    }

    public function setBatchNumber(?int $batchNumber): self
    {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    public function getDeal(): ?Deal
    {
        return $this->deal;
    }

    public function setDeal(?Deal $deal): self
    {
        $this->deal = $deal;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        $this->profiles->removeElement($profile);

        return $this;
    }
}
