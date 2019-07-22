<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskFile;
use AppBundle\Entity\TaskFileDownloadManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Repository\RepositoryAwareTrait;

class FilesController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 50;

    /**
     *
     * @Route("/files", name="files_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $allFiles = $this->getFileRepository()->getFiles($filters, $this->getUser(), $currentPage, self::PER_PAGE);
        $projects = $this->getProjectRepository()->findAll();
        $formatFiles = $this->getFileRepository()->getUniqueFormatFiles();

        $maxRows = $allFiles->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('files/list.html.twig', [
            'allFiles' => $allFiles,
            'projects' => $projects,
            'formatFiles' => $formatFiles,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * @Route("/file/{fileId}/history-download/{category}", name="history_download_file")
     */
    public function historyDownloadFile(Request $request)
    {
        $fileId = $request->get('fileId');
        $category = $request->get('category');

        /** @var TaskFile $file */
        $file = $this->getTaskFileRepository()->find($fileId);

        /** @var TaskFileDownloadManager $history */
        $downloads = $this->getTaskFileDownloadManager()->getDownloadsFile($file);

        return $this->render('partial/history_downloads_list.html.twig', [
            'downloads' => $downloads,
            'category' => $category
        ]);
    }
}