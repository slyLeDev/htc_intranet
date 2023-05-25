<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Interfaces\ViewablePdfFileInterface;
use App\Repository\DealRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=DealRepository::class)
 * @Gedmo\Loggable()
 */
class Deal implements ViewablePdfFileInterface
{
    const SALARY_VARIABLE = 0;
    const SALARY_EXACT = 1;

    const STATUS_PENDING = 0;
    const STATUS_INTERRUPTED = 1;
    const STATUS_CLOSE = 2;

    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $jobName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $jobDescription;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deadline;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="deals")
     */
    private $responsibleConsultant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryMax;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryExact;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="deals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=BatchCustomer::class, mappedBy="deal", orphanRemoval=true)
     */
    private $batchCustomers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dealFilename;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryState;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    public function __construct()
    {
        $this->responsibleConsultant = new ArrayCollection();
        $this->batchCustomers = new ArrayCollection();
        $this->quantity = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobName(): ?string
    {
        return $this->jobName;
    }

    public function setJobName(string $jobName): self
    {
        $this->jobName = $jobName;

        return $this;
    }

    public function getJobDescription(): ?string
    {
        return $this->jobDescription;
    }

    public function setJobDescription(?string $jobDescription): self
    {
        $this->jobDescription = $jobDescription;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getResponsibleConsultant(): Collection
    {
        return $this->responsibleConsultant;
    }

    public function addResponsibleConsultant(User $responsibleConsultant): self
    {
        if (!$this->responsibleConsultant->contains($responsibleConsultant)) {
            $this->responsibleConsultant[] = $responsibleConsultant;
        }

        return $this;
    }

    public function removeResponsibleConsultant(User $responsibleConsultant): self
    {
        $this->responsibleConsultant->removeElement($responsibleConsultant);

        return $this;
    }

    public function getSalaryMin(): ?int
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(?int $salaryMin): self
    {
        $this->salaryMin = $salaryMin;

        return $this;
    }

    public function getSalaryMax(): ?int
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(?int $salaryMax): self
    {
        $this->salaryMax = $salaryMax;

        return $this;
    }

    public function getSalaryExact(): ?int
    {
        return $this->salaryExact;
    }

    public function setSalaryExact(?int $salaryExact): self
    {
        $this->salaryExact = $salaryExact;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, BatchCustomer>
     */
    public function getBatchCustomers(): Collection
    {
        return $this->batchCustomers;
    }

    public function addBatchCustomer(BatchCustomer $batchCustomer): self
    {
        if (!$this->batchCustomers->contains($batchCustomer)) {
            $this->batchCustomers[] = $batchCustomer;
            $batchCustomer->setDeal($this);
        }

        return $this;
    }

    public function removeBatchCustomer(BatchCustomer $batchCustomer): self
    {
        if ($this->batchCustomers->removeElement($batchCustomer)) {
            // set the owning side to null (unless already changed)
            if ($batchCustomer->getDeal() === $this) {
                $batchCustomer->setDeal(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatutText(): string
    {
        $status = '';

        switch ($this->getStatus()) {
            case self::STATUS_PENDING:
                $status = 'En cours';
                break;
            case self::STATUS_INTERRUPTED:
                $status = 'Suspendu';
                break;
            case self::STATUS_CLOSE:
                $status = 'Cloturé';
                break;
        }

        return $status;
    }

    public function getDealFilename(): ?string
    {
        return $this->dealFilename;
    }

    public function setDealFilename(?string $dealFilename): self
    {
        $this->dealFilename = $dealFilename;

        return $this;
    }

    public function getStatutBadgeLabel(): string
    {
        $status = '';

        switch ($this->getStatus()) {
            case self::STATUS_PENDING:
                $status = 'badge badge-success';
                break;
            case self::STATUS_INTERRUPTED:
                $status = 'badge badge-warning';
                break;
            case self::STATUS_CLOSE:
                $status = 'badge badge-danger';
                break;
        }

        return $status;
    }

    public function getConsultantResponsibleAsString(): string
    {
        $responsible = [];
        /** @var User $consultant */
        foreach ($this->getResponsibleConsultant() as $consultant) {
            $responsible[] = $consultant->getFullName();
        }

        return implode(', ', $responsible);
    }

    public function getSalaryState(): ?int
    {
        return $this->salaryState;
    }

    public function setSalaryState(?int $salaryState): self
    {
        $this->salaryState = $salaryState;

        return $this;
    }

    public function isDeadlineLate()
    {
        return $this->isPending() && $this->getDeadline() && $this->getDeadline() <= (new DateTime('now'));
    }

    public function getDeadlineFormatted()
    {
        return $this->getDeadline() ? $this->getDeadline()->format('d/m/Y') : '';
    }

    public function reference(): string
    {
        return sprintf('C_%s_%s_%s', [$this->getCustomer()->getName(), 'HTC', $this->getCreatedAt()->format('dmYHis')]);
    }

    public function isSalaryVariable()
    {
        return $this->getSalaryState() === self::SALARY_VARIABLE;
    }

    public function getSalaryText()
    {
        if ($this->isSalaryVariable()) {
            return number_format($this->getSalaryMin(), 0, '.', ' ').
                ' Ar à '.number_format($this->getSalaryMax(), 0, '.', ' ').' Ar';
        }

        if ($this->isSalaryExact()) {
            return number_format($this->getSalaryExact(), 0, '.', ' ').' Ar';
        }

        return '';
    }

    public function isSalaryExact()
    {
        return $this->getSalaryState() === self::SALARY_EXACT;
    }

    public function isPending()
    {
        return (int) $this->getStatus() === self::STATUS_PENDING;
    }

    public function getViewableFilename()
    {
        return $this->getId() ? str_replace(ViewablePdfFileInterface::EXTENSION_TO_REPLACE, 'pdf', $this->getDealFilename()) : '';
    }

    public function isViewableFile(string $targetPath): bool
    {
        return $this->getId() && is_file($targetPath.$this->getViewableFilename());
    }

    public function getQuantity(): ?int
    {
        return (0 === $this->quantity || empty($this->quantity)) ? 1 : $this->quantity;
    }

    public function getQuantityAsString(): string
    {
        return (0 === $this->quantity || empty($this->quantity)) ? '01' : str_pad($this->quantity, 2, '0', STR_PAD_LEFT);
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
