<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Achievement;
use AppBundle\Entity\ApplicantComment;
use AppBundle\Entity\ApplicantStatus;
use AppBundle\Entity\Book;
use AppBundle\Entity\CategoryPrice;
use AppBundle\Entity\MedicalInstitution;
use AppBundle\Entity\MedicalInstitutionCategory;
use AppBundle\Entity\PriceIteration;
use AppBundle\Entity\ProjectPassport;
use AppBundle\Entity\ProjectPassportCategory;
use AppBundle\Entity\Genre;
use AppBundle\Entity\BookGenre;
use AppBundle\Entity\ProjectPrice;
use AppBundle\Entity\UserBook;
use AppBundle\Entity\BookDiff;
use AppBundle\Entity\BookFile;
use AppBundle\Entity\BookComment;
use AppBundle\Entity\City;
use AppBundle\Entity\Client;
use AppBundle\Entity\Country;
use AppBundle\Entity\DayOff;
use AppBundle\Entity\Monitoring;
use AppBundle\Entity\MonitoringHostname;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Person;
use AppBundle\Entity\Cost;
use AppBundle\Entity\File;
use AppBundle\Entity\ProductionCalendar;
use AppBundle\Entity\ProjectCategory;
use AppBundle\Entity\ProjectFile;
use AppBundle\Entity\ProjectMember;
use AppBundle\Entity\ProjectRole;
use AppBundle\Entity\ProjectStage;
use AppBundle\Entity\ProjectStageProgress;
use AppBundle\Entity\ProjectStatus;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\ProjectDiff;
use AppBundle\Entity\ProtocolMembers;
use AppBundle\Entity\Specification;
use AppBundle\Entity\TaskComment;
use AppBundle\Entity\TaskDiff;
use AppBundle\Entity\TaskFile;
use AppBundle\Entity\TaskFileDownloadManager;
use AppBundle\Entity\TaskResult;
use AppBundle\Entity\TeamSpace;
use AppBundle\Entity\UserAchievement;
use AppBundle\Entity\WorkLog;
use DevelopmentBundle\Entity\CompanyCode;
use DevelopmentBundle\Entity\EngineeringDocument;
use DevelopmentBundle\Entity\EngineeringDocumentClassifier;
use DevelopmentBundle\Entity\EngineeringDocumentFile;
use DevelopmentBundle\Entity\ProgrammingDocument;
use DevelopmentBundle\Entity\ProgrammingDocumentFile;
use DevelopmentBundle\Entity\ProgrammingDocumentType;
use DevelopmentBundle\Repository\CompanyCodeRepository;
use DevelopmentBundle\Repository\EngineeringDocumentClassifierRepository;
use DevelopmentBundle\Repository\EngineeringDocumentFileRepository;
use DevelopmentBundle\Repository\EngineeringDocumentRepository;
use DevelopmentBundle\Repository\ProgrammingDocumentFileRepository;
use DevelopmentBundle\Repository\ProgrammingDocumentRepository;
use DevelopmentBundle\Repository\ProgrammingDocumentTypeRepository;
use DevelopmentBundle\Repository\ProjectCodeRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use AppBundle\Entity\Team;
use DocumentBundle\Entity\Activity;
use DocumentBundle\Entity\ActivityDiff;
use DocumentBundle\Entity\ActivityEvents;
use DocumentBundle\Entity\Document;
use DocumentBundle\Entity\DocumentCategory;
use DocumentBundle\Entity\DocumentComment;
use DocumentBundle\Entity\DocumentDiff;
use DocumentBundle\Entity\DocumentFile;
use DocumentBundle\Entity\DocumentRevision;
use DocumentBundle\Entity\DocumentSignatory;
use DocumentBundle\Entity\DocumentTemplate;
use DocumentBundle\Entity\TechnicalMap;
use DocumentBundle\Entity\TechnicalMapComment;
use DocumentBundle\Entity\TechnicalMapDiff;
use DocumentBundle\Entity\TechnicalMapFile;
use DocumentBundle\Entity\TechnicalMapSignatory;
use DocumentBundle\Entity\TechnicalMapSolutions;
use DocumentBundle\Repository\ActivityDiffRepository;
use DocumentBundle\Repository\ActivityEventsRepository;
use DocumentBundle\Repository\ActivityRepository;
use DocumentBundle\Repository\DocumentDiffRepository;
use DocumentBundle\Repository\DocumentFileRepository;
use DocumentBundle\Repository\DocumentRepository;
use DocumentBundle\Repository\DocumentRevisionRepository;
use DocumentBundle\Repository\DocumentSignatoryRepository;
use DocumentBundle\Repository\DocumentTemplateRepository;
use DocumentBundle\Repository\DocumentCategoryRepository;
use DocumentBundle\Repository\DocumentCommentRepository;
use DocumentBundle\Repository\TechnicalMapCommentRepository;
use DocumentBundle\Repository\TechnicalMapDiffRepository;
use DocumentBundle\Repository\TechnicalMapFileRepository;
use DocumentBundle\Repository\TechnicalMapRepository;
use DocumentBundle\Repository\TechnicalMapSignatoryRepository;
use DocumentBundle\Repository\TechnicalMapSolutionsRepository;
use InfrastructureBundle\Entity\Computer;
use InfrastructureBundle\Entity\ComputerDiff;
use InfrastructureBundle\Entity\ComputerPart;
use InfrastructureBundle\Entity\ComputerParts;
use InfrastructureBundle\Repository\ComputerDiffRepository;
use InfrastructureBundle\Repository\ComputerPartRepository;
use InfrastructureBundle\Repository\ComputerPartsRepository;
use InfrastructureBundle\Repository\ComputerRepository;
use ProductionBundle\Entity\Material;
use ProductionBundle\Entity\Serial;
use ProductionBundle\Entity\SerialCategory;
use ProductionBundle\Entity\Tool;
use ProductionBundle\Entity\ToolWorkLog;
use ProductionBundle\Entity\Ware;
use ProductionBundle\Repository\MaterialRepository;
use ProductionBundle\Repository\SerialCategoryRepository;
use ProductionBundle\Repository\SerialRepository;
use ProductionBundle\Repository\ToolRepository;
use ProductionBundle\Repository\ToolWorkLogRepository;
use ProductionBundle\Repository\WareRepository;
use PurchaseBundle\Entity\ManagerStats;
use PurchaseBundle\Entity\Rent;
use PurchaseBundle\Entity\SupplierIncident;
use PurchaseBundle\Entity\SupplierLegalForm;
use PurchaseBundle\Entity\Tenant;
use PurchaseBundle\Entity\Tenement;
use PurchaseBundle\Entity\Invoice;
use PurchaseBundle\Entity\InvoiceRegistry;
use PurchaseBundle\Entity\InvoiceRegistryDiff;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestCategory;
use PurchaseBundle\Entity\PurchaseRequestComment;
use PurchaseBundle\Entity\PurchaseRequestDelivery;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\Entity\PurchaseRequestFavorite;
use PurchaseBundle\Entity\RequestFile;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\Entity\SupplierComment;
use PurchaseBundle\Entity\SupplierDiff;
use PurchaseBundle\Entity\SupplierPerson;
use PurchaseBundle\Entity\Supplier;
use PurchaseBundle\Entity\SuppliesCategory;
use PurchaseBundle\Entity\Unit;
use PurchaseBundle\Entity\Warehouse;
use DevelopmentBundle\Entity\ProjectCode;
use PurchaseBundle\Repository\InvoiceRegistryRepository;
use PurchaseBundle\Repository\InvoiceRegistryDiffRepository;
use PurchaseBundle\Repository\InvoiceRepository;
use PurchaseBundle\Repository\ManagerStatsRepository;
use PurchaseBundle\Repository\PurchaseRequestCategoryRepository;
use PurchaseBundle\Repository\PurchaseRequestCommentRepository;
use PurchaseBundle\Repository\PurchaseRequestDeliveryRepository;
use PurchaseBundle\Repository\PurchaseRequestDiffRepository;
use PurchaseBundle\Repository\PurchaseRequestFavoriteRepository;
use PurchaseBundle\Repository\RentRepository;
use PurchaseBundle\Repository\RequestFileRepository;
use PurchaseBundle\Repository\RequestItemRepository;
use PurchaseBundle\Repository\PurchaseRequestRepository;
use PurchaseBundle\Repository\SupplierBlackListRepository;
use PurchaseBundle\Repository\SupplierCommentRepository;
use PurchaseBundle\Repository\SupplierDiffRepository;
use PurchaseBundle\Repository\SupplierIncidentRepository;
use PurchaseBundle\Repository\SupplierLegalFormRepository;
use PurchaseBundle\Repository\SupplierPersonRepository;
use PurchaseBundle\Repository\SupplierRepository;
use PurchaseBundle\Repository\SuppliesCategoryRepository;
use PurchaseBundle\Repository\TenantRepository;
use PurchaseBundle\Repository\TenementRepository;
use PurchaseBundle\Repository\UnitRepository;
use PurchaseBundle\Repository\WarehouseRepository;
use WarehouseBundle\Entity\Nomenclature;
use WarehouseBundle\Entity\NomenclatureGroup;
use WarehouseBundle\Repository\NomenclatureGroupRepository;
use WarehouseBundle\Repository\NomenclatureRepository;
use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantDiff;
use AppBundle\Entity\ApplicantFile;
use AppBundle\Entity\Vacancy;
use AppBundle\Entity\Interview;
use DevelopmentBundle\Entity\Company;

Trait RepositoryAwareTrait
{
    /**
     * @return Registry
     */
    abstract protected function getDoctrine();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return ProjectRepository
     */
    protected function getProjectRepository()
    {
        return $this->getDoctrine()->getRepository(Project::class);
    }

    /**
     * @return SupplierRepository
     */
    protected function getSupplierRepository()
    {
        return $this->getDoctrine()->getRepository(Supplier::class);
    }

    /**
     * @return InvoiceRegistryRepository
     */
    protected function getInvoiceRegistryRepository()
    {
        return $this->getDoctrine()->getRepository(InvoiceRegistry::class);
    }

    /**
     * @return InvoiceRegistryDiffRepository
     */
    protected function getInvoiceRegistryDiffRepository()
    {
        return $this->getDoctrine()->getRepository(InvoiceRegistryDiff::class);
    }

    /**
     * @return ApplicantDiffRepository
     */
    protected function getApplicantDiffRepository()
    {
        return $this->getDoctrine()->getRepository(ApplicantDiff::class);
    }

    /**
     * @return ApplicantStatusRepository
     */
    protected function getApplicantStatusRepository()
    {
        return $this->getDoctrine()->getRepository(ApplicantStatus::class);
    }

    /**
     * @return SupplierCommentRepository
     */
    protected function getSupplierCommentRepository()
    {
        return $this->getDoctrine()->getRepository(SupplierComment::class);
    }

    /**
     * @return SupplierDiffRepository
     */
    protected function getSupplierDiffRepository()
    {
        return $this->getDoctrine()->getRepository(SupplierDiff::class);
    }

    /**
     * @return ClientRepository
     */
    protected function getClientRepository()
    {
        return $this->getDoctrine()->getRepository(Client::class);
    }

    /**
     * @return SuppliesCategoryRepository
     */
    protected function getSuppliesCategoryRepository()
    {
        return $this->getDoctrine()->getRepository(SuppliesCategory::class);
    }

    /**
     * @return PurchaseRequestCommentRepository
     */
    protected function getPurchaseRequestCommentRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequestComment::class);
    }

    /**
     * @return DocumentCommentRepository
     */
    protected function getDocumentCommentRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentComment::class);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }

    /**
     * @return BookRepository
     */
    protected function getBookRepository()
    {
        return $this->getDoctrine()->getRepository(Book::class);
    }

    /**
     * @return UserBookRepository
     */
    protected function getUserBookRepository()
    {
        return $this->getDoctrine()->getRepository(UserBook::class);
    }

    /**
     * @return BookDiffRepository
     */
    protected function getBookDiffRepository()
    {
        return $this->getDoctrine()->getRepository(BookDiff::class);
    }

    /**
     * @return BookFileRepository
     */
    protected function getBookFileRepository()
    {
        return $this->getDoctrine()->getRepository(BookFile::class);
    }

    /**
     * @return GenreRepository
     */
    protected function getGenreRepository()
    {
        return $this->getDoctrine()->getRepository(Genre::class);
    }

    /**
     * @return BookGenreRepository
     */
    protected function getBookGenreRepository()
    {
        return $this->getDoctrine()->getRepository(BookGenre::class);
    }

    /**
     * @return BookCommentRepository
     */
    protected function getBookCommentRepository()
    {
        return $this->getDoctrine()->getRepository(BookComment::class);
    }

    /**
     * @return NotificationRepository
     */
    protected function getNotificationRepository()
    {
        return $this->getDoctrine()->getRepository(Notification::class);
    }

    /**
     * @return PurchaseRequestCategoryRepository
     */
    protected function getPurchaseRequestCategoriesRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequestCategory::class);
    }

    /**
     * @return RequestFileRepository
     */
    protected function getRequestFileRepository()
    {
        return $this->getDoctrine()->getRepository(RequestFile::class);
    }

    /**
     * @return PurchaseRequestRepository
     */
    protected function getPurchaseRequestRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequest::class);
    }

    /**
     * @return PurchaseRequestDiffRepository
     */
    protected function getPurchaseRequestDiffRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequestDiff::class);
    }

    /**
     * @return PurchaseRequestFavoriteRepository
     */
    protected function getPurchaseRequestFavoriteRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequestFavorite::class);
    }

    /**
     * @return RequestItemRepository
     */
    protected function getRequestItemRepository()
    {
        return $this->getDoctrine()->getRepository(RequestItem::class);
    }

    /**
     * @return UnitRepository
     */
    protected function getUnitRepository()
    {
        return $this->getDoctrine()->getRepository(Unit::class);
    }

    /**
     * @return TeamRepository
     */
    protected function getTeamRepository()
    {
        return $this->getDoctrine()->getRepository(Team::class);
    }

    /**
     * @return ProjectRepository
     */
    protected function getTeamSpaceRepository()
    {
        return $this->getDoctrine()->getRepository(TeamSpace::class);
    }

    /**
     * @return ProjectStatusRepository
     */
    protected function getProjectStatusRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectStatus::class);
    }

    /**
     * @return ProjectMemberRepository
     */
    protected function getProjectMemberRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectMember::class);
    }

    /**
     * @return FileRepository
     */
    protected function getFileRepository()
    {
        return $this->getDoctrine()->getRepository(File::class);
    }

    /**
     * @return FileRepository
     */
    protected function getProjectFileRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectFile::class);
    }

    /**
     * @return ProjectCategoryRepository
     */
    protected function getProjectCategoryRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectCategory::class);
    }

    /**
     * @return ProjectStageProgressRepository
     */
    protected function getProjectStageProgressRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectStageProgress::class);
    }

    /**
     * @return ProjectStageRepository
     */
    protected function getProjectStageRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectStage::class);
    }

    /**
     * @return ProjectTaskRepository
     */
    protected function getProjectTaskRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectTask::class);
    }

    /**
     * @return ProtocolMembersRepository
     */
    protected function getProtocolMembersRepository()
    {
        return $this->getDoctrine()->getRepository(ProtocolMembers::class);
    }

    /**
     * @return TaskCommentRepository
     */
    protected function getTaskCommentRepository()
    {
        return $this->getDoctrine()->getRepository(TaskComment::class);
    }

    /**
     * @return FileRepository
     */
    protected function getTaskFileRepository()
    {
        return $this->getDoctrine()->getRepository(TaskFile::class);
    }

    /**
     * @return WorkLogRepository
     */
    protected function getWorkLogRepository()
    {
        return $this->getDoctrine()->getRepository(WorkLog::class);
    }

    /**
     * @return RentRepository
     */
    protected function getRentRepository()
    {
        return $this->getDoctrine()->getRepository(Rent::class);
    }

    /**
     * @return TenantRepository
     */
    protected function getTenantRepository()
    {
        return $this->getDoctrine()->getRepository(Tenant::class);
    }

    /**
     * @return TenementRepository
     */
    protected function getTenementRepository()
    {
        return $this->getDoctrine()->getRepository(Tenement::class);
    }

    /**
     * @return CostRepository
     */
    protected function getProjectCostRepository()
    {
        return $this->getDoctrine()->getRepository(Cost::class);
    }

    /**
     * @return ToolRepository
     */
    protected function getToolRepository()
    {
        return $this->getDoctrine()->getRepository(Tool::class);
    }

    /**
     * @return ToolWorkLogRepository
     */
    protected function getToolWorkLogRepository()
    {
        return $this->getDoctrine()->getRepository(ToolWorkLog::class);
    }

    /**
     * @return DocumentRepository
     */
    protected function getDocumentRepository()
    {
        return $this->getDoctrine()->getRepository(Document::class);
    }

    /**
     * @return DocumentTemplateRepository
     */
    protected function getDocumentTemplateRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentTemplate::class);
    }

    /**
     * @return DocumentCategoryRepository
     */
    protected function getDocumentCategoryRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentCategory::class);
    }

    /**
     * @return DocumentDiffRepository
     */
    protected function getDocumentDiffRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentDiff::class);
    }

    /**
     * @return DocumentRevisionRepository
     */
    protected function getDocumentRevisionRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentRevision::class);
    }

    /**
     * @return DocumentSignatoryRepository
     */
    protected function getDocumentSignatoryRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentSignatory::class);
    }

    /**
     * @return DocumentFileRepository
     */
    protected function getDocumentFileRepository()
    {
        return $this->getDoctrine()->getRepository(DocumentFile::class);
    }

    /**
     * @return AchievementRepository
     */
    protected function getAchievementRepository()
    {
        return $this->getDoctrine()->getRepository(Achievement::class);
    }

    /**
     * @return UserAchievementRepository
     */
    protected function getUserAchievementRepository()
    {
        return $this->getDoctrine()->getRepository(UserAchievement::class);
    }

    /**
     * @return TaskDiffRepository
     */
    protected function getTaskDiffRepository()
    {
        return $this->getDoctrine()->getRepository(TaskDiff::class);
    }

    /**
     * @return ProjectRoleRepository
     */
    protected function getProjectRoleRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectRole::class);
    }

    /**
     * @return PersonRepository
     */
    protected function getPersonRepository()
    {
        return $this->getDoctrine()->getRepository(Person::class);
    }

    /**
     * @return WareRepository
     */
    protected function getWareRepository()
    {
        return $this->getDoctrine()->getRepository(Ware::class);
    }

    /**
     * @return DayOffRepository
     */
    protected function getDayOffRepository()
    {
        return $this->getDoctrine()->getRepository(DayOff::class);
    }

    /**
     * @return MaterialRepository
     */
    protected function getMaterialRepository()
    {
        return $this->getDoctrine()->getRepository(Material::class);
    }

    /**
     * @return EngineeringDocumentRepository
     */
    protected function getEngineeringDocumentRepository()
    {
        return $this->getDoctrine()->getRepository(EngineeringDocument::class);
    }

    /**
     * @return EngineeringDocumentFileRepository
     */
    protected function getEngineeringDocumentFileRepository()
    {
        return $this->getDoctrine()->getRepository(EngineeringDocumentFile::class);
    }

    /**
     * @return TaskResultRepository
     */
    protected function getTaskResultRepository()
    {
        return $this->getDoctrine()->getRepository(TaskResult::class);
    }

    /**
     * @return SpecificationRepository
     */
    protected function getSpecificationRepository()
    {
        return $this->getDoctrine()->getRepository(Specification::class);
    }

    /**
     * @return SupplierPersonRepository
     */
    protected function getSupplierPersonRepository()
    {
        return $this->getDoctrine()->getRepository(SupplierPerson::class);
    }

    /**
     * @return ActivityRepository
     */
    protected function getActivityRepository()
    {
        return $this->getDoctrine()->getRepository(Activity::class);
    }

    /**
     * @return ActivityEventsRepository
     */
    protected function getActivityEventsRepository()
    {
        return $this->getDoctrine()->getRepository(ActivityEvents::class);
    }

    /**
     * @return ComputerRepository
     */
    protected function getComputerRepository()
    {
        return $this->getDoctrine()->getRepository(Computer::class);
    }

    /**
     * @return ComputerPartRepository
     */
    protected function getComputerPartRepository()
    {
        return $this->getDoctrine()->getRepository(ComputerPart::class);
    }

    /**
     * @return ActivityDiffRepository
     */
    protected function getActivityDiffRepository()
    {
        return $this->getDoctrine()->getRepository(ActivityDiff::class);
    }

    /**
     * @return ProgrammingDocumentRepository
     */
    protected function getProgrammingDocumentRepository()
    {
        return $this->getDoctrine()->getRepository(ProgrammingDocument::class);
    }

    /**
     * @return ProgrammingDocumentFileRepository
     */
    protected function getProgrammingDocumentFileRepository()
    {
        return $this->getDoctrine()->getRepository(ProgrammingDocumentFile::class);
    }

    /**
     * @return ProgrammingDocumentTypeRepository
     */
    protected function getProgrammingDocumentTypeRepository()
    {
        return $this->getDoctrine()->getRepository(ProgrammingDocumentType::class);
    }

    /**
     * @return TechnicalMapRepository
     */
    protected function getTechnicalMapRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMap::class);
    }

    /**
     * @return TechnicalMapSolutionsRepository
     */
    protected function getTechnicalMapSolutionsRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMapSolutions::class);
    }

    /**
     * @return TechnicalMapCommentRepository
     */
    protected function getTechnicalMapCommentRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMapComment::class);
    }

    /**
     * @return TechnicalMapSignatoryRepository
     */
    protected function getTechnicalMapSignatoryRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMapSignatory::class);
    }

    /**
     * @return TechnicalMapFileRepository
     */
    protected function getTechnicalMapFileRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMapFile::class);
    }

    /**
     * @return TechnicalMapDiffRepository
     */
    protected function getTechnicalMapDiffRepository()
    {
        return $this->getDoctrine()->getRepository(TechnicalMapDiff::class);
    }

    /**
     * @return ComputerDiffRepository
     */
    protected function getComputerDiffRepository()
    {
        return $this->getDoctrine()->getRepository(ComputerDiff::class);
    }

    /**
     * @return InvoiceRepository
     */
    protected function getInvoiceRepository()
    {
        return $this->getDoctrine()->getRepository(Invoice::class);
    }

    /**
     * @return SerialRepository
     */
    protected function getSerialRepository()
    {
        return $this->getDoctrine()->getRepository(Serial::class);
    }

    /**
     * @return SerialCategoryRepository
     */
    protected function getSerialCategoryRepository()
    {
        return $this->getDoctrine()->getRepository(SerialCategory::class);
    }

    /**
     * @return ComputerPartsRepository
     */
    protected function getComputerPartsRepository()
    {
        return $this->getDoctrine()->getRepository(ComputerParts::class);
    }

    /**
     * @return NomenclatureGroupRepository
     */
    protected function getNomenclatureGroupRepository()
    {
        return $this->getDoctrine()->getRepository(NomenclatureGroup::class);
    }

    /**
     * @return NomenclatureRepository
     */
    protected function getNomenclatureRepository()
    {
        return $this->getDoctrine()->getRepository(Nomenclature::class);
    }

    /**
     * @return MonitoringRepository
     */
    protected function getMonitoringRepository()
    {
        return $this->getDoctrine()->getRepository(Monitoring::class);
    }

    /**
     * @return MonitoringHostnameRepository
     */
    protected function getMonitoringHostnameRepository()
    {
        return $this->getDoctrine()->getRepository(MonitoringHostname::class);
    }

    /**
     * @return ApplicantRepository
     */
    protected function getApplicantRepository()
    {
        return $this->getDoctrine()->getRepository(Applicant::class);
    }

    /**
     * @return ApplicantFileRepository
     */
    protected function getApplicantFileRepository()
    {
        return $this->getDoctrine()->getRepository(ApplicantFile::class);
    }

    /**
     * @return ApplicantCommentRepository
     */
    protected function getApplicantCommentRepository()
    {
        return $this->getDoctrine()->getRepository(ApplicantComment::class);
    }

    /**
     * @return VacancyRepository
     */
    protected function getVacancyRepository()
    {
        return $this->getDoctrine()->getRepository(Vacancy::class);
    }

    /**
     * @return InterviewRepository
     */
    protected function getInterviewRepository()
    {
        return $this->getDoctrine()->getRepository(Interview::class);
    }

    /**
     * @return ProjectCodeRepository
     */
    protected function getProjectCodeRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectCode::class);
    }

    /**
     * @return EngineeringDocumentClassifierRepository
     */
    protected function getEngineeringDocumentClassifierRepository()
    {
        return $this->getDoctrine()->getRepository(EngineeringDocumentClassifier::class);
    }

    /**
     * @return WarehouseRepository
     */
    protected function getWarehouseRepository()
    {
        return $this->getDoctrine()->getRepository(Warehouse::class);
    }

    /**
     * @return CompanyCodeRepository
     */
    protected function getCompanyCodeRepository()
    {
        return $this->getDoctrine()->getRepository(CompanyCode::class);
    }

    /**
     * @return TaskFileDownloadManagerRepository
     */
    protected function getTaskFileDownloadManager()
    {
        return $this->getDoctrine()->getRepository(TaskFileDownloadManager::class);
    }

    /**
     * @return CountryRepository
     */
    protected function getCountryRepository()
    {
        return $this->getDoctrine()->getRepository(Country::class);
    }

    /**
     * @return CityRepository
     */
    protected function getCityRepository()
    {
        return $this->getDoctrine()->getRepository(City::class);
    }

    /**
     * @return PurchaseRequestDeliveryRepository
     */
    protected function getPurchaseRequestDeliveryRepository()
    {
        return $this->getDoctrine()->getRepository(PurchaseRequestDelivery::class);
    }

    /**
     * @return ProjectDiffRepository
     */
    protected function getProjectDiffRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectDiff::class);
    }

    /**
     * @return SupplierLegalFormRepository
     */
    protected function getSupplierLegalFormRepository()
    {
        return $this->getDoctrine()->getRepository(SupplierLegalForm::class);
    }

    /**
     * @return ManagerStatsRepository
     */
    protected function getManagerStatsRepository()
    {
        return $this->getDoctrine()->getRepository(ManagerStats::class);
    }

    /**
     * @return ProductionCalendarRepository
     */
    protected function getProductionCalendarRepository()
    {
        return $this->getDoctrine()->getRepository(ProductionCalendar::class);
    }

    /**
     * @return SupplierIncidentRepository
     */
    protected function getSupplierIncidentRepository()
    {
        return $this->getDoctrine()->getRepository(SupplierIncident::class);
    }

    /**
     * @return ProjectPassportRepository
     */
    protected function getProjectPassportRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectPassport::class);
    }

    /**
     * @return MedicalInstitutionRepository
     */
    protected function getMedicalInstitutionRepository()
    {
        return $this->getDoctrine()->getRepository(MedicalInstitution::class);
    }

    /**
     * @return MedicalInstitutionCategoryRepository
     */
    protected function getMedicalInstitutionCategoryRepository()
    {
        return $this->getDoctrine()->getRepository(MedicalInstitutionCategory::class);
    }

    /**
     * @return ProjectPriceRepository
     */
    protected function getProjectPriceRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectPrice::class);
    }

    /**
     * @return PriceIterationRepository
     */
    protected function getPriceIterationRepository()
    {
        return $this->getDoctrine()->getRepository(PriceIteration::class);
    }

    /**
     * @return CategoryPriceRepository
     */
    protected function getCategoryPriceRepository()
    {
        return $this->getDoctrine()->getRepository(CategoryPrice::class);
    }
}