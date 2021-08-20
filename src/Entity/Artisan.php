<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use App\DataDefinitions\Field;
use App\DataDefinitions\Fields;
use App\DataDefinitions\FieldsList;
use App\Utils\Accessors\Commission;
use App\Utils\Accessors\Url;
use App\Utils\Artisan\CompletenessCalc;
use App\Utils\Artisan\ContactPermit;
use App\Utils\FieldReadInterface;
use App\Utils\StringList;
use App\Utils\StrUtils;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArtisanRepository")
 * @ORM\Table(name="artisans")
 */
class Artisan implements JsonSerializable, FieldReadInterface, Stringable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=16)
     */
    #[Regex(pattern: '/^[A-Z0-9]{7}$/', message: 'Use only uppercase letters and/or digits (A-Z, 0-9).', groups: ['iu_form'])]
    #[Length(min: 7, max: 7, groups: ['iu_form'])]
    private string $makerId = '';

    /**
     * @ORM\Column(type="string", length=128)
     */
    #[Length(max: 128, groups: ['iu_form'])]
    #[NotBlank(groups: ['iu_form'])]
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $formerly = '';

    /**
     * @ORM\Column(type="string", length=512)
     */
    #[Length(max: 512, groups: ['iu_form'])]
    private string $intro = '';

    /**
     * @ORM\Column(type="string", length=16)
     */
    #[Length(max: 16, groups: ['iu_form'])]
    private string $since = '';

    /**
     * @ORM\Column(type="string", length=16)
     */
    #[Length(max: 16, groups: ['iu_form'])]
    #[NotBlank(groups: ['iu_form'])]
    private string $country = '';

    /**
     * @ORM\Column(type="string", length=32)
     */
    #[Length(max: 32, groups: ['iu_form'])]
    private string $state = '';

    /**
     * @ORM\Column(type="string", length=32)
     */
    #[Length(max: 32, groups: ['iu_form'])]
    private string $city = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $productionModelsComment = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $productionModels = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $stylesComment = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $styles = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $otherStyles = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $orderTypesComment = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $orderTypes = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $otherOrderTypes = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $featuresComment = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $features = '';

    /**
     * @ORM\Column(type="string", length=1024)
     */
    #[Length(max: 1024, groups: ['iu_form'])]
    private string $otherFeatures = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $paymentPlans = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $paymentMethods = '';

    /**
     * @ORM\Column(type="string", length=64)
     */
    #[Length(max: 64, groups: ['iu_form'])]
    private string $currenciesAccepted = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $speciesComment = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $speciesDoes = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $speciesDoesnt = '';

    /**
     * @ORM\Column(type="string", length=256)
     */
    #[Length(max: 256, groups: ['iu_form'])]
    private string $languages = '';

    /**
     * @ORM\Column(type="text")
     */
    #[Length(max: 4096, groups: ['iu_form'])]
    private string $notes = '';

    /**
     * @ORM\Column(type="string", length=512)
     */
    #[Length(max: 512, groups: ['iu_form'])]
    private string $inactiveReason = '';

    /**
     * @ORM\Column(type="string", length=16)
     */
    #[Length(max: 16, groups: ['iu_form'])]
    #[NotBlank(groups: ['iu_form'])]
    private string $contactAllowed = '';

    /**
     * @ORM\Column(type="string", length=32)
     */
    private string $contactMethod = '';

    /**
     * @ORM\Column(type="string", length=128)
     */
    #[Length(max: 128, groups: ['iu_form'])]
    private string $contactInfoObfuscated = '';

    /**
     * @ORM\OneToOne(targetEntity=ArtisanVolatileData::class, mappedBy="artisan", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private ?ArtisanVolatileData $volatileData = null;

    /**
     * @ORM\OneToOne(targetEntity=ArtisanPrivateData::class, mappedBy="artisan", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    #[Valid(groups: ['iu_form'])]
    private ?ArtisanPrivateData $privateData = null;

    /**
     * @ORM\OneToMany(targetEntity=ArtisanUrl::class, mappedBy="artisan", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Collection|ArtisanUrl[]
     */
    private Collection | array $urls;

    /**
     * @ORM\OneToMany(targetEntity=ArtisanCommissionsStatus::class, mappedBy="artisan", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Collection|ArtisanCommissionsStatus[]
     */
    private Collection | array $commissions;

    /**
     * @ORM\OneToMany(targetEntity=MakerId::class, mappedBy="artisan", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Collection|MakerId[]
     */
    private Collection | array $makerIds;

    public function __construct()
    {
        $this->urls = new ArrayCollection();
        $this->commissions = new ArrayCollection();
        $this->makerIds = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->privateData) {
            $this->setPrivateData(clone $this->privateData);
        }

        if ($this->volatileData) {
            $this->setVolatileData(clone $this->volatileData);
        }

        $urlsToClone = $this->urls;
        $this->urls = new ArrayCollection();

        foreach ($urlsToClone as $url) {
            $this->addUrl(clone $url);
        }

        $makerIdsToClone = $this->makerIds;
        $this->makerIds = new ArrayCollection();

        foreach ($makerIdsToClone as $makerId) {
            $this->addMakerId(clone $makerId);
        }

        $commissionsToClone = $this->commissions;
        $this->commissions = new ArrayCollection();

        foreach ($commissionsToClone as $commission) {
            $this->addCommission(clone $commission);
        }
    }

    public function __toString(): string
    {
        return self::class.":$this->id:$this->makerId";
    }

    // ===== LEGITIMATE GETTERS AND SETTERS =====

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMakerId(): string
    {
        return $this->makerId;
    }

    public function setMakerId(string $makerId): self
    {
        $this->makerId = $makerId;

        if ('' !== $makerId) {
            $this->addMakerId($makerId);
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFormerly(): string
    {
        return $this->formerly;
    }

    public function setFormerly(string $formerly): self
    {
        $this->formerly = $formerly;

        return $this;
    }

    public function getIntro(): string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getSince(): string
    {
        return $this->since;
    }

    public function setSince(string $since): self
    {
        $this->since = $since;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getProductionModelsComment(): string
    {
        return $this->productionModelsComment;
    }

    public function setProductionModelsComment(string $productionModelsComment): self
    {
        $this->productionModelsComment = $productionModelsComment;

        return $this;
    }

    public function getProductionModels(): string
    {
        return $this->productionModels;
    }

    public function setProductionModels(string $productionModels): self
    {
        $this->productionModels = $productionModels;

        return $this;
    }

    public function getStylesComment(): string
    {
        return $this->stylesComment;
    }

    public function setStylesComment(string $stylesComment): self
    {
        $this->stylesComment = $stylesComment;

        return $this;
    }

    public function getStyles(): string
    {
        return $this->styles;
    }

    public function setStyles(string $styles): self
    {
        $this->styles = $styles;

        return $this;
    }

    public function getOtherStyles(): string
    {
        return $this->otherStyles;
    }

    public function setOtherStyles(string $otherStyles): self
    {
        $this->otherStyles = $otherStyles;

        return $this;
    }

    public function getOrderTypesComment(): string
    {
        return $this->orderTypesComment;
    }

    public function setOrderTypesComment(string $orderTypesComment): self
    {
        $this->orderTypesComment = $orderTypesComment;

        return $this;
    }

    public function getOrderTypes(): string
    {
        return $this->orderTypes;
    }

    public function setOrderTypes(string $orderTypes): self
    {
        $this->orderTypes = $orderTypes;

        return $this;
    }

    public function getOtherOrderTypes(): string
    {
        return $this->otherOrderTypes;
    }

    public function setOtherOrderTypes(string $otherOrderTypes): self
    {
        $this->otherOrderTypes = $otherOrderTypes;

        return $this;
    }

    public function getFeaturesComment(): string
    {
        return $this->featuresComment;
    }

    public function setFeaturesComment(string $featuresComment): self
    {
        $this->featuresComment = $featuresComment;

        return $this;
    }

    public function getFeatures(): string
    {
        return $this->features;
    }

    public function setFeatures(string $features): self
    {
        $this->features = $features;

        return $this;
    }

    public function getOtherFeatures(): string
    {
        return $this->otherFeatures;
    }

    public function setOtherFeatures(string $otherFeatures): self
    {
        $this->otherFeatures = $otherFeatures;

        return $this;
    }

    public function getPaymentPlans(): string
    {
        return $this->paymentPlans;
    }

    public function setPaymentPlans(string $paymentPlans): self
    {
        $this->paymentPlans = $paymentPlans;

        return $this;
    }

    public function getPaymentMethods(): string
    {
        return $this->paymentMethods;
    }

    public function setPaymentMethods(string $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;

        return $this;
    }

    public function getCurrenciesAccepted(): string
    {
        return $this->currenciesAccepted;
    }

    public function setCurrenciesAccepted(string $currenciesAccepted): self
    {
        $this->currenciesAccepted = $currenciesAccepted;

        return $this;
    }

    public function getSpeciesComment(): string
    {
        return $this->speciesComment;
    }

    public function setSpeciesComment(string $speciesComment): self
    {
        $this->speciesComment = $speciesComment;

        return $this;
    }

    public function getSpeciesDoes(): string
    {
        return $this->speciesDoes;
    }

    public function setSpeciesDoes(string $speciesDoes): self
    {
        $this->speciesDoes = $speciesDoes;

        return $this;
    }

    public function getSpeciesDoesnt(): string
    {
        return $this->speciesDoesnt;
    }

    public function setSpeciesDoesnt($speciesDoesnt): self
    {
        $this->speciesDoesnt = $speciesDoesnt;

        return $this;
    }

    public function getLanguages(): string
    {
        return $this->languages;
    }

    public function setLanguages(string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getInactiveReason(): string
    {
        return $this->inactiveReason;
    }

    public function setInactiveReason(string $inactiveReason): self
    {
        $this->inactiveReason = $inactiveReason;

        return $this;
    }

    public function getContactAllowed(): string
    {
        return $this->contactAllowed;
    }

    public function setContactAllowed(string $contactAllowed): self
    {
        $this->contactAllowed = $contactAllowed;

        return $this;
    }

    public function getContactMethod(): string
    {
        return $this->contactMethod;
    }

    public function setContactMethod(string $contactMethod): self
    {
        $this->contactMethod = $contactMethod;

        return $this;
    }

    public function getContactInfoObfuscated(): string
    {
        return $this->contactInfoObfuscated;
    }

    public function setContactInfoObfuscated(string $contactInfoObfuscated): self
    {
        $this->contactInfoObfuscated = $contactInfoObfuscated;

        return $this;
    }

    public function getVolatileData(): ArtisanVolatileData
    {
        return $this->volatileData ??= (new ArtisanVolatileData())->setArtisan($this);
    }

    public function setVolatileData(ArtisanVolatileData $volatileData): self
    {
        $this->volatileData = $volatileData;

        if ($this !== $volatileData->getArtisan()) {
            $volatileData->setArtisan($this);
        }

        return $this;
    }

    public function getPrivateData(): ArtisanPrivateData
    {
        return $this->privateData ??= (new ArtisanPrivateData())->setArtisan($this);
    }

    public function setPrivateData(ArtisanPrivateData $privateData): self
    {
        $this->privateData = $privateData;

        if ($this !== $privateData->getArtisan()) {
            $privateData->setArtisan($this);
        }

        return $this;
    }

    /**
     * @return Collection|ArtisanUrl[]
     */
    public function getUrls(): Collection | array
    {
        return $this->urls;
    }

    public function addUrl(ArtisanUrl $artisanUrl): self
    {
        if (!$this->urls->contains($artisanUrl)) {
            $this->urls[] = $artisanUrl;
            $artisanUrl->setArtisan($this);
        }

        return $this;
    }

    public function removeUrl(ArtisanUrl $artisanUrl): self
    {
        if ($this->urls->removeElement($artisanUrl)) {
            if ($artisanUrl->getArtisan() === $this) {
                $artisanUrl->setArtisan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MakerId[]
     */
    public function getMakerIds(): Collection | array
    {
        return $this->makerIds;
    }

    public function addMakerId(MakerId | string $makerId): self
    {
        if (!($makerId instanceof MakerId)) {
            if ($this->hasMakerId($makerId)) {
                return $this;
            }

            $makerId = (new MakerId($makerId));
        }

        if (!$this->makerIds->contains($makerId)) {
            $this->makerIds[] = $makerId;
            $makerId->setArtisan($this);
        }

        return $this;
    }

    public function removeMakerId(MakerId $makerId): self
    {
        if ($this->makerIds->removeElement($makerId)) {
            if ($makerId->getArtisan() === $this) {
                $makerId->setArtisan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArtisanCommissionsStatus[]
     */
    public function getCommissions(): Collection | array
    {
        return $this->commissions;
    }

    public function addCommission(ArtisanCommissionsStatus $commission): self
    {
        if (!$this->commissions->contains($commission)) {
            $this->commissions[] = $commission;
            $commission->setArtisan($this);
        }

        return $this;
    }

    public function removeCommission(ArtisanCommissionsStatus $commission): self
    {
        if ($this->commissions->removeElement($commission)) {
            // set the owning side to null (unless already changed)
            if ($commission->getArtisan() === $this) {
                $commission->setArtisan(null);
            }
        }

        return $this;
    }

    // ===== HELPER GETTERS AND SETTERS =====

    public function set(Field | string $field, mixed $newValue): self
    {
        if (!($field instanceof Field)) {
            $field = Fields::get((string) $field);
        }

        $setter = 'set'.ucfirst($field->modelName() ?: 'noModelName');

        if (!method_exists($this, $setter)) {
            throw new InvalidArgumentException("Setter for {$field->name()} does not exist");
        }

        call_user_func([$this, $setter], $newValue);

        return $this;
    }

    public function get(Field | string $field): mixed
    {
        if (!($field instanceof Field)) {
            $field = Fields::get((string) $field);
        }

        $getter = 'get'.ucfirst($field->modelName() ?: 'noModelName');

        if (!method_exists($this, $getter)) {
            throw new InvalidArgumentException("Getter for {$field->name()} does not exist");
        }

        return call_user_func([$this, $getter]);
    }

    public function getLastMakerId(): string
    {
        return $this->getMakerId() ?: current($this->getFormerMakerIdsArr());
    }

    public function hasMakerId(string $makerId): bool
    {
        return in_array($makerId, $this->makerIds
            ->map(fn (MakerId $makerId): string => $makerId->getMakerId())
            ->toArray());
    }

    public function getFormerMakerIds(): string
    {
        return StringList::pack($this->getFormerMakerIdsArr());
    }

    public function setFormerMakerIds(string $formerMakerIdsToSet): self
    {
        $formerMakerIdsToSet = StringList::unpack($formerMakerIdsToSet);
        $allMakerIdsToSet = array_merge($formerMakerIdsToSet, [$this->makerId]);

        foreach ($this->makerIds as $makerId) {
            if (!in_array($makerId->getMakerId(), $allMakerIdsToSet)) {
                $this->removeMakerId($makerId);
            }
        }

        foreach ($formerMakerIdsToSet as $makerId) {
            $this->addMakerId($makerId);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFormerMakerIdsArr(): array
    {
        return $this->makerIds
            ->map(fn (MakerId $makerId): string => $makerId->getMakerId())
            ->filter(fn (string $makerId): bool => $makerId !== $this->makerId)
            ->toArray();
    }

    /**
     * @return string[]
     */
    public function getAllMakerIdsArr(): array
    {
        return array_filter(array_merge([$this->getMakerId()], $this->getFormerMakerIdsArr()));
    }

    /**
     * @return string[]
     */
    public function getFormerlyArr(): array
    {
        return StringList::unpack($this->formerly);
    }

    /**
     * @return string[]
     */
    public function getAllNamesArr(): array
    {
        return array_filter(array_merge([$this->getName()], $this->getFormerlyArr()));
    }

    public function getCompleteness(): int
    {
        return CompletenessCalc::count($this);
    }

    public function allowsFeedback(): bool
    {
        return ContactPermit::FEEDBACK === $this->contactAllowed;
    }

    public function getCsLastCheck(): ?DateTimeInterface
    {
        return $this->getVolatileData()->getLastCsUpdate();
    }

    public function setCsLastCheck(?DateTimeInterface $csLastCheck): void
    {
        $this->getVolatileData()->setLastCsUpdate($csLastCheck);
    }

    public function getCsTrackerIssue(): bool
    {
        return $this->getVolatileData()->getCsTrackerIssue();
    }

    public function setCsTrackerIssue(bool $csTrackerIssue): self
    {
        $this->getVolatileData()->setCsTrackerIssue($csTrackerIssue);

        return $this;
    }

    public function getBpLastCheck(): ?DateTimeInterface
    {
        return $this->getVolatileData()->getLastBpUpdate();
    }

    public function setBpLastCheck(?DateTimeInterface $bpLastCheck): void
    {
        $this->getVolatileData()->setLastBpUpdate($bpLastCheck);
    }

    public function getBpTrackerIssue(): bool
    {
        return $this->getVolatileData()->getBpTrackerIssue();
    }

    public function setBpTrackerIssue(bool $bpTrackerIssue): self
    {
        $this->getVolatileData()->setBpTrackerIssue($bpTrackerIssue);

        return $this;
    }

    public function getOpenFor(): string
    {
        return Commission::get($this, true);
    }

    /**
     * @return string[]
     */
    public function getOpenForArray(): array
    {
        return Commission::getList($this, true);
    }

    public function setOpenFor(string $openFor): self
    {
        Commission::set($this, true, $openFor);

        return $this;
    }

    public function getClosedFor(): string
    {
        return Commission::get($this, false);
    }

    /**
     * @return string[]
     */
    public function getClosedForArray(): array
    {
        return Commission::getList($this, false);
    }

    public function setClosedFor(string $closedFor): self
    {
        Commission::set($this, false, $closedFor);

        return $this;
    }

    //
    // ===== PRIVATE DATA GETTERS AND SETTERS =====
    //

    public function getContactAddressPlain(): string
    {
        return $this->getPrivateData()->getContactAddress();
    }

    public function setContactAddressPlain(string $contactAddressPlain): self
    {
        $this->getPrivateData()->setContactAddress($contactAddressPlain);

        return $this;
    }

    public function getContactInfoOriginal(): string
    {
        return $this->getPrivateData()->getOriginalContactInfo();
    }

    public function setContactInfoOriginal(string $contactInfoOriginal): self
    {
        $this->getPrivateData()->setOriginalContactInfo($contactInfoOriginal);

        return $this;
    }

    public function getPassword(): string
    {
        return $this->getPrivateData()->getPassword();
    }

    public function setPassword(string $password): self
    {
        $this->getPrivateData()->setPassword($password);

        return $this;
    }

    //
    // ===== URLS GETTERS AND SETTERS =====
    //

    public function getFursuitReviewUrl(): string
    {
        return $this->getUrl(Fields::URL_FURSUITREVIEW);
    }

    public function setFursuitReviewUrl(string $fursuitReviewUrl): self
    {
        $this->setUrl(Fields::URL_FURSUITREVIEW, $fursuitReviewUrl);

        return $this;
    }

    public function getFurAffinityUrl(): string
    {
        return $this->getUrl(Fields::URL_FUR_AFFINITY);
    }

    public function setFurAffinityUrl(string $furAffinityUrl): self
    {
        $this->setUrl(Fields::URL_FUR_AFFINITY, $furAffinityUrl);

        return $this;
    }

    public function getDeviantArtUrl(): string
    {
        return $this->getUrl(Fields::URL_DEVIANTART);
    }

    public function setDeviantArtUrl(string $deviantArtUrl): self
    {
        $this->setUrl(Fields::URL_DEVIANTART, $deviantArtUrl);

        return $this;
    }

    public function getWebsiteUrl(): string
    {
        return $this->getUrl(Fields::URL_WEBSITE);
    }

    public function setWebsiteUrl(string $websiteUrl): self
    {
        $this->setUrl(Fields::URL_WEBSITE, $websiteUrl);

        return $this;
    }

    public function getFacebookUrl(): string
    {
        return $this->getUrl(Fields::URL_FACEBOOK);
    }

    public function setFacebookUrl(string $facebookUrl): self
    {
        $this->setUrl(Fields::URL_FACEBOOK, $facebookUrl);

        return $this;
    }

    public function getTwitterUrl(): string
    {
        return $this->getUrl(Fields::URL_TWITTER);
    }

    public function setTwitterUrl(string $twitterUrl): self
    {
        $this->setUrl(Fields::URL_TWITTER, $twitterUrl);

        return $this;
    }

    public function getTumblrUrl(): string
    {
        return $this->getUrl(Fields::URL_TUMBLR);
    }

    public function setTumblrUrl(string $tumblrUrl): self
    {
        $this->setUrl(Fields::URL_TUMBLR, $tumblrUrl);

        return $this;
    }

    public function getCommissionsUrl(): string
    {
        return $this->getUrl(Fields::URL_COMMISSIONS);
    }

    public function setCommissionsUrl(string $commissionsUrl): self
    {
        $this->setUrl(Fields::URL_COMMISSIONS, $commissionsUrl);

        return $this;
    }

    public function getQueueUrl(): string
    {
        return $this->getUrl(Fields::URL_QUEUE);
    }

    public function setQueueUrl(string $queueUrl): self
    {
        $this->setUrl(Fields::URL_QUEUE, $queueUrl);

        return $this;
    }

    public function getInstagramUrl(): string
    {
        return $this->getUrl(Fields::URL_INSTAGRAM);
    }

    public function setInstagramUrl(string $instagramUrl): self
    {
        $this->setUrl(Fields::URL_INSTAGRAM, $instagramUrl);

        return $this;
    }

    public function getYoutubeUrl(): string
    {
        return $this->getUrl(Fields::URL_YOUTUBE);
    }

    public function setYoutubeUrl(string $youtubeUrl): self
    {
        $this->setUrl(Fields::URL_YOUTUBE, $youtubeUrl);

        return $this;
    }

    public function getPricesUrl(): string
    {
        return $this->getUrl(Fields::URL_PRICES);
    }

    /**
     * @return string[]
     */
    public function getPricesUrls(): array
    {
        return StringList::unpack($this->getUrl(Fields::URL_PRICES));
    }

    public function setPricesUrl(string $pricesUrl): self
    {
        $this->setUrl(Fields::URL_PRICES, $pricesUrl);

        return $this;
    }

    public function getFaqUrl(): string
    {
        return $this->getUrl(Fields::URL_FAQ);
    }

    public function setFaqUrl(string $faqUrl): self
    {
        $this->setUrl(Fields::URL_FAQ, $faqUrl);

        return $this;
    }

    public function getLinklistUrl(): string
    {
        return $this->getUrl(Fields::URL_LINKLIST);
    }

    public function setLinklistUrl(string $url): self
    {
        $this->setUrl(Fields::URL_LINKLIST, $url);

        return $this;
    }

    public function getFurryAminoUrl(): string
    {
        return $this->getUrl(Fields::URL_FURRY_AMINO);
    }

    public function setFurryAminoUrl(string $url): self
    {
        $this->setUrl(Fields::URL_FURRY_AMINO, $url);

        return $this;
    }

    public function getEtsyUrl(): string
    {
        return $this->getUrl(Fields::URL_ETSY);
    }

    public function setEtsyUrl(string $url): self
    {
        $this->setUrl(Fields::URL_ETSY, $url);

        return $this;
    }

    public function getTheDealersDenUrl(): string
    {
        return $this->getUrl(Fields::URL_THE_DEALERS_DEN);
    }

    public function setTheDealersDenUrl(string $url): self
    {
        $this->setUrl(Fields::URL_THE_DEALERS_DEN, $url);

        return $this;
    }

    public function getOtherShopUrl(): string
    {
        return $this->getUrl(Fields::URL_OTHER_SHOP);
    }

    public function setOtherShopUrl(string $url): self
    {
        $this->setUrl(Fields::URL_OTHER_SHOP, $url);

        return $this;
    }

    public function getOtherUrls(): string
    {
        return $this->getUrl(Fields::URL_OTHER);
    }

    public function setOtherUrls($otherUrls): self
    {
        $this->setUrl(Fields::URL_OTHER, $otherUrls);

        return $this;
    }

    public function getScritchUrl(): string
    {
        return $this->getUrl(Fields::URL_SCRITCH);
    }

    public function setScritchUrl(string $scritchUrl): self
    {
        $this->setUrl(Fields::URL_SCRITCH, $scritchUrl);

        return $this;
    }

    public function getFurtrackUrl(): string
    {
        return $this->getUrl(Fields::URL_FURTRACK);
    }

    public function setFurtrackUrl(string $furtrackUrl): self
    {
        $this->setUrl(Fields::URL_FURTRACK, $furtrackUrl);

        return $this;
    }

    public function getPhotoUrls(): string
    {
        return $this->getUrl(Fields::URL_PHOTOS);
    }

    public function setPhotoUrls(string $photoUrls): self
    {
        $this->setUrl(Fields::URL_PHOTOS, $photoUrls);

        return $this;
    }

    public function getMiniatureUrls(): string
    {
        return $this->getUrl(Fields::URL_MINIATURES);
    }

    public function setMiniatureUrls(string $scritchMiniatureUrls): self
    {
        $this->setUrl(Fields::URL_MINIATURES, $scritchMiniatureUrls);

        return $this;
    }

    /**
     * @return ArtisanUrl[]
     */
    public function getUrlObjs(string $urlFieldName): array
    {
        return Url::getObjs($this, $urlFieldName);
    }

    private function getUrl(string $urlFieldName): string
    {
        return Url::get($this, $urlFieldName);
    }

    private function setUrl(string $urlFieldName, string $newUrl): void
    {
        Url::set($this, $urlFieldName, $newUrl);
    }

    //
    // ===== JSON STUFF =====
    //

    private function getValuesForJson(FieldsList $fields): array
    {
        return array_map(function (Field $field) {
            $value = match ($field->name()) {
                Fields::COMPLETENESS       => $this->getCompleteness(),
                Fields::CS_LAST_CHECK      => StrUtils::asStr($this->getCsLastCheck()),
                Fields::BP_LAST_CHECK      => StrUtils::asStr($this->getBpLastCheck()),
                default                    => $this->get($field),
            };

            return $field->isList() && !is_array($value) ? StringList::unpack($value) : $value;
        }, $fields->asArray());
    }

    public function getPublicData(): array
    {
        return $this->getValuesForJson(Fields::public());
    }

    public function getAllData(): array
    {
        return $this->getValuesForJson(Fields::getAll());
    }

    public function jsonSerialize(): array
    {
        return $this->getPublicData();
    }

    //
    // ===== NON-TRIVIAL VALIDATION =====
    //

    #[Assert\Callback(groups: ['iu_form'])]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if (ContactPermit::NO !== $this->getContactAllowed() && '' === $this->getContactInfoObfuscated()) {
            $context
                ->buildViolation('This value should not be blank.')
                ->atPath(Fields::get(Fields::CONTACT_INFO_OBFUSCATED)->modelName())
                ->addViolation();
        }
    }
}
