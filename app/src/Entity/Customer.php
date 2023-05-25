<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @Gedmo\Loggable()
 */
class Customer
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $manager;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $interlocutor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\ManyToMany(targetEntity=JobSector::class, inversedBy="customers")
     */
    private $sector;

    /**
     * @ORM\OneToMany(targetEntity=Deal::class, mappedBy="customer", orphanRemoval=true, cascade={"persist"})
     */
    private $deals;

    public function __construct()
    {
        $this->sector = new ArrayCollection();
        $this->deals = new ArrayCollection();
        //$this->createdAt = new \DateTime();
        //$this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getManager(): ?string
    {
        return $this->manager;
    }

    public function setManager(string $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function getInterlocutor(): ?string
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(string $interlocutor): self
    {
        $this->interlocutor = $interlocutor;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, JobSector>
     */
    public function getSector(): Collection
    {
        return $this->sector;
    }

    public function getSectorAsString(): string
    {
        $sectors = [];
        /** @var JobSector $sector */
        foreach ($this->getSector() as $sector) {
            $sectors[] = $sector->getName();
        }

        return implode(" / ", $sectors);
    }

    public function addSector(JobSector $sector): self
    {
        if (!$this->sector->contains($sector)) {
            $this->sector[] = $sector;
        }

        return $this;
    }

    public function removeSector(JobSector $sector): self
    {
        $this->sector->removeElement($sector);

        return $this;
    }

    /**
     * @return Collection<int, Deal>
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    public function addDeal(Deal $deal): self
    {
        if (!$this->deals->contains($deal)) {
            $this->deals[] = $deal;
            $deal->setCustomer($this);
        }

        return $this;
    }

    public function removeDeal(Deal $deal): self
    {
        if ($this->deals->removeElement($deal)) {
            // set the owning side to null (unless already changed)
            if ($deal->getCustomer() === $this) {
                $deal->setCustomer(null);
            }
        }

        return $this;
    }

    public function countDeals()
    {
        return $this->getDeals()->count();
    }
}
