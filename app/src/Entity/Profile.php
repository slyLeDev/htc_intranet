<?php
/**
 * @author hR.
 */

namespace App\Entity;

use App\Repository\ProfileRepository;
use App\Tools\DateTools;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @Gedmo\Loggable()
 */
class Profile extends AbstractEntityCommon
{
    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    const GENDER_UNDEFINED = 'ND';

    const RECEIVED = 'Reçue';
    const INTERVIEWED = 'Pris en entretien';
    const PLACED = 'Placé';
    const SENT = 'Envoyé à un client';
    const BLACKLISTED = 'Blackisté';

    const XP_YEAR_TRAINEE = -1;
    const XP_YEAR_BEGINNER = 0;
    const XP_UNDER_TWO = 1;

    const ORDER_BY_FULLNAME = 'fullName';
    const ORDER_BY_XP_YEAR = 'xpYear';
    const ORDER_BY_RECEIVED_AT = 'receivedAt';

    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"searchable"})
     *
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"searchable"})
     *
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $yearsOld;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $actuallyJobTitle;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $degree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"searchable"})
     */
    private $phone;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $receivedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hopeJobTitle;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $yearOfExperience;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $timelineExperience;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locality;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $fullAddress;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentSalary;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryExpectationMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryExpectationMax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $currentState;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $curriculumVitae;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePhoto;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\ManyToMany(targetEntity=JobSector::class, inversedBy="profiles", cascade={"persist"}, orphanRemoval=true)
     */
    private $sectors;

    /**
     * @ORM\ManyToMany(targetEntity=BatchCustomer::class, cascade={"persist"}, mappedBy="profiles")
     */
    private $batchCustomers;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fullNameSlugged;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $xpYear;

    /**
     * @Groups({"searchable"})
     *
     * @ORM\Column(type="boolean")
     */
    private $neededInformationIsComplete;

    /**
     * @ORM\OneToMany(targetEntity=Experiences::class, cascade={"persist"}, mappedBy="profile")
     */
    private $experiences;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salaryExpectationFix;

    /**
     * @ORM\OneToMany(targetEntity=Interview::class, cascade={"persist"}, mappedBy="profile")
     */
    private $interviews;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $blacklistedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasonBlacklisted;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $proposedPosition;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $lastMajAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $experiencesFromExtract;

    public function __construct()
    {
        $this->sectors = new ArrayCollection();
        $this->batchCustomers = new ArrayCollection();
        $this->status = self::RECEIVED;
        $this->neededInformationIsComplete = false;
        $this->experiences = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        //$this->createdAt = new \DateTime();
        //$this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReceivedAt(): ?\DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(\DateTimeImmutable $receivedAt): self
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getYearsOld(): ?int
    {
        return $this->yearsOld;
    }

    public function setYearsOld(?int $yearsOld): self
    {
        $this->yearsOld = $yearsOld;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        if ($gender) {
            $gender = strtoupper($gender);
            if (in_array($gender, ['M', 'MASCULIN', 'HOMME'])) {
                $this->gender = self::GENDER_MALE;

                return $this;
            }

            if (in_array($gender, ['F', 'FEMININ', 'FEMME'])) {
                $this->gender = self::GENDER_FEMALE;

                return $this;
            }
        }

        $this->gender = $gender;

        return $this;
    }

    public function getActuallyJobTitle(): ?string
    {
        return $this->actuallyJobTitle;
    }

    public function setActuallyJobTitle(?string $actuallyJobTitle): self
    {
        $this->actuallyJobTitle = $actuallyJobTitle;

        return $this;
    }

    public function getHopeJobTitle(): ?string
    {
        return $this->hopeJobTitle;
    }

    public function setHopeJobTitle(?string $hopeJobTitle): self
    {
        $this->hopeJobTitle = $hopeJobTitle;

        return $this;
    }

    public function getYearOfExperience(): ?string
    {
        return $this->yearOfExperience;
    }

    public function setYearOfExperience(?string $yearOfExperience): self
    {
        $this->yearOfExperience = $yearOfExperience;

        return $this;
    }

    public function getTimelineExperience(): ?string
    {
        return $this->timelineExperience;
    }

    public function setTimelineExperience(?string $timelineExperience): self
    {
        $this->timelineExperience = $timelineExperience;

        return $this;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(?string $degree): self
    {
        $this->degree = $degree;

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

    public function getFullAddress(): ?string
    {
        return $this->fullAddress;
    }

    public function setFullAddress(?string $fullAddress): self
    {
        $this->fullAddress = $fullAddress;

        return $this;
    }

    public function getCurrentSalary(): ?int
    {
        return $this->currentSalary;
    }

    public function setCurrentSalary(?int $currentSalary): self
    {
        $this->currentSalary = $currentSalary;

        return $this;
    }

    public function getSalaryExpectationMin(): ?int
    {
        return $this->salaryExpectationMin;
    }

    public function setSalaryExpectationMin(?int $salaryExpectationMin): self
    {
        $this->salaryExpectationMin = $salaryExpectationMin;

        return $this;
    }

    public function getSalaryExpectationMax(): ?int
    {
        return $this->salaryExpectationMax;
    }

    public function setSalaryExpectationMax(?int $salaryExpectationMax): self
    {
        $this->salaryExpectationMax = $salaryExpectationMax;

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    public function setCurrentState(?string $currentState): self
    {
        $this->currentState = $currentState;

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

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCurriculumVitae(): ?string
    {
        return $this->curriculumVitae;
    }

    public function setCurriculumVitae(?string $curriculumVitae): self
    {
        $this->curriculumVitae = $curriculumVitae;

        return $this;
    }

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    /**
     * @return Collection<int, JobSector>
     */
    public function getSectors(): Collection
    {
        return $this->sectors;
    }

    public function addSector(JobSector $sector): self
    {
        if (!$this->sectors->contains($sector)) {
            $this->sectors[] = $sector;
        }

        return $this;
    }

    public function removeSector(JobSector $sector): self
    {
        $this->sectors->removeElement($sector);

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
            $batchCustomer->addProfile($this);
        }

        return $this;
    }

    public function removeBatchCustomer(BatchCustomer $batchCustomer): self
    {
        if ($this->batchCustomers->removeElement($batchCustomer)) {
            $batchCustomer->removeProfile($this);
        }

        return $this;
    }

    public function getFullNameSlugged(): ?string
    {
        return $this->fullNameSlugged;
    }

    public function setFullNameSlugged(?string $fullNameSlugged): self
    {
        $this->fullNameSlugged = $fullNameSlugged;

        return $this;
    }

    public static function recognizeGender($value): ?string
    {
        $value = strtoupper($value);
        if (in_array($value, ['F', 'FEMME'])) {
            return self::GENDER_FEMALE;
        }

        if (in_array($value, ['H', 'HOMME'])) {
            return self::GENDER_MALE;
        }

        return self::GENDER_UNDEFINED;
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

    public function getGenderBgColor()
    {
        if (self::GENDER_FEMALE === $this->gender) {
            return '36b9cc';
        }

        if (self::GENDER_MALE === $this->gender) {
            return '4e73df';
        }

        return '17375e';
    }

    public function getXpYear(): ?float
    {
        return $this->xpYear;
    }

    public function setXpYear(?float $xpYear): self
    {
        $this->xpYear = $xpYear;

        return $this;
    }

    public function isNeededInformationIsComplete(): ?bool
    {
        return $this->neededInformationIsComplete;
    }

    public function setNeededInformationIsComplete(bool $neededInformationIsComplete): self
    {
        $this->neededInformationIsComplete = $neededInformationIsComplete;

        return $this;
    }

    public function buildIsNeededInformationIsComplete()
    {
        $isNeededInfoComplete = !empty($this->getFullName()) &&
            !empty($this->getActuallyJobTitle()) &&
            !empty($this->getXpYear()) &&
            !empty($this->getDegree()) &&
            !empty($this->getYearOfExperience()) &&
            (!empty($this->getEmail()) || !empty($this->getPhone()))
        ;

        $this->setNeededInformationIsComplete($isNeededInfoComplete);
    }

    /**
     * @return Collection<int, Experiences>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experiences $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setProfile($this);
        }

        return $this;
    }

    public function removeExperience(Experiences $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getProfile() === $this) {
                $experience->setProfile(null);
            }
        }

        return $this;
    }

    public function getNbTotalExperience($getXpYearNumber = false)
    {
        $totalMonth = 0;

        /** @var Experiences $experience */
        foreach ($this->getExperiences() as $experience) {
            $totalMonth += DateTools::getCountMonth($experience->getStartAt(), $experience->getEndAt());
        }

        return DateTools::getTotalTimeline($totalMonth, $getXpYearNumber);
    }

    public function getSalaryExpectationFix(): ?string
    {
        return $this->salaryExpectationFix;
    }

    public function setSalaryExpectationFix(?string $salaryExpectationFix): self
    {
        $this->salaryExpectationFix = $salaryExpectationFix;

        return $this;
    }

    /**
     * @return Collection<int, Interview>
     */
    public function getInterviews(): Collection
    {
        return $this->interviews;
    }

    public function addInterview(Interview $interview): self
    {
        if (!$this->interviews->contains($interview)) {
            $this->interviews[] = $interview;
            $interview->setProfile($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            // set the owning side to null (unless already changed)
            if ($interview->getProfile() === $this) {
                $interview->setProfile(null);
            }
        }

        return $this;
    }

    public function getBlacklistedAt(): ?\DateTimeImmutable
    {
        return $this->blacklistedAt;
    }

    public function setBlacklistedAt(?\DateTimeImmutable $blacklistedAt): self
    {
        $this->blacklistedAt = $blacklistedAt;

        return $this;
    }

    public function getReasonBlacklisted(): ?string
    {
        return $this->reasonBlacklisted;
    }

    public function setReasonBlacklisted(?string $reasonBlacklisted): self
    {
        $this->reasonBlacklisted = $reasonBlacklisted;

        return $this;
    }

    public function getProposedPosition(): ?string
    {
        return $this->proposedPosition;
    }

    public function setProposedPosition(?string $proposedPosition): self
    {
        $this->proposedPosition = $proposedPosition;

        return $this;
    }

    public function getLastMajAt(): ?\DateTimeImmutable
    {
        return $this->lastMajAt;
    }

    public function setLastMajAt(?\DateTimeImmutable $lastMajAt): self
    {
        $this->lastMajAt = $lastMajAt;

        return $this;
    }

    public function getExperiencesFromExtract(): ?string
    {
        return $this->experiencesFromExtract;
    }

    public function setExperiencesFromExtract(?string $experiencesFromExtract): self
    {
        $this->experiencesFromExtract = $experiencesFromExtract;

        return $this;
    }
}
