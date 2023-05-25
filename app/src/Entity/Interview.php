<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Repository\InterviewRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=InterviewRepository::class)
 */
class Interview
{
    const STATE_CONTACTED = 'contacté';
    const STATE_ATTEMPT_FEEDBACK = 'attente retour';
    const STATE_RELAUNCH = 'relancé';
    const STATE_INTERVIEWED = 'pris en entretien';
    const STATE_PENDING_CUSTOMER = 'en cours client';
    const STATE_FINISHED = 'terminé';

    const STATE_PENDING = 'en attente';
    const STATE_RESERVED = 'en réserve';
    const STATE_NOT_COME = 'pas venu';
    const STATE_RECRUITED = 'recruté';
    const STATE_OK = 'ok';
    const STATE_KO = 'ko';

    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="interviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stateFirstInterview;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stateCustomerInterviewed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isRefusedMailSent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="interviews")
     */
    private $consultant;

    /**
     * @ORM\ManyToOne(targetEntity=InterviewCategory::class, inversedBy="interviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $title;

    public function __construct()
    {
        $this->isRefusedMailSent = false;
        $this->state = self::STATE_PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStateFirstInterview(): ?string
    {
        return $this->stateFirstInterview;
    }

    public function setStateFirstInterview(?string $stateFirstInterview): self
    {
        $this->stateFirstInterview = $stateFirstInterview;

        return $this;
    }

    public function getStateCustomerInterviewed(): ?string
    {
        return $this->stateCustomerInterviewed;
    }

    public function setStateCustomerInterviewed(?string $stateCustomerInterviewed): self
    {
        $this->stateCustomerInterviewed = $stateCustomerInterviewed;

        return $this;
    }

    public function isIsRefusedMailSent(): ?bool
    {
        return $this->isRefusedMailSent;
    }

    public function setIsRefusedMailSent(?bool $isRefusedMailSent): self
    {
        $this->isRefusedMailSent = $isRefusedMailSent;

        return $this;
    }

    public static function allowToSetState(string $value): bool
    {
        return in_array(strtolower($value), [
            strtolower(self::STATE_CONTACTED),
            strtolower(self::STATE_ATTEMPT_FEEDBACK),
            strtolower(self::STATE_RELAUNCH),
            strtolower(self::STATE_INTERVIEWED),
            strtolower(self::STATE_PENDING_CUSTOMER),
            strtolower(self::STATE_FINISHED),
            strtolower(self::STATE_OK),
            strtolower(self::STATE_KO),
            strtolower(self::STATE_RECRUITED),
            strtolower(self::STATE_RESERVED),
            strtolower(self::STATE_NOT_COME),
            strtolower(self::STATE_PENDING),
        ]);
    }

    public static function allowToSetGeneralState(string $value): bool
    {
        return in_array(strtolower($value), [
            strtolower(self::STATE_CONTACTED),
            strtolower(self::STATE_ATTEMPT_FEEDBACK),
            strtolower(self::STATE_RELAUNCH),
            strtolower(self::STATE_INTERVIEWED),
            strtolower(self::STATE_PENDING_CUSTOMER),
            strtolower(self::STATE_FINISHED),
        ]);
    }

    public static function allowToSetStateFirstInterview(string $value): bool
    {
        return in_array(strtolower($value), [
            strtolower(self::STATE_OK),
            strtolower(self::STATE_KO),
        ]);
    }

    public static function allowToSetStateCustomerInterview(string $value): bool
    {
        return in_array(strtolower($value), [
            strtolower(self::STATE_OK),
            strtolower(self::STATE_KO),
            strtolower(self::STATE_PENDING),
        ]);
    }

    public function getConsultant(): ?User
    {
        return $this->consultant;
    }

    public function setConsultant(?User $consultant): self
    {
        $this->consultant = $consultant;

        return $this;
    }

    public function getCategory(): ?InterviewCategory
    {
        return $this->category;
    }

    public function setCategory(?InterviewCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeImmutable $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function buildTitle()
    {
        $this->setTitle('Entretien '.$this->getProfile()->getFullName());

        return $this;
    }

    public function getDateStartFormatted()
    {
        return $this->getDateStart() ? $this->getDateStart()->format('d/m/Y H:i') : '';
    }

    public function getDateEndFormatted()
    {
        return $this->getDateEnd() ? $this->getDateEnd()->format('d/m/Y H:i') : '';
    }

    public function getDefaultEventTextColor()
    {
        $now = new DateTime();
        if ($this->getDateEnd() < $now) {
            return 'black';
        }

        return 'white';
    }

    public function getDefaultEventColor()
    {
        $now = new DateTime();
        if ($this->getDateEnd() < $now) {
            return '#a6dff9';
            //return '#008fae';
        }

        return '#008de9';
    }

    public function getRenderedData()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'start' => $this->getDateStart(),
            'end' => $this->getDateEnd(),
            'hexColor' => $this->getDefaultEventColor(),
            'textColor' => $this->getDefaultEventTextColor(),
        ];
    }
}
