<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDiff;
use AppBundle\Entity\BookFile;
use AppBundle\Entity\BookComment;
use AppBundle\Entity\BookFileDownloadManager;
use AppBundle\Entity\BookGenre;
use AppBundle\Entity\UserBook;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\StringUtils;

class BookController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("company/library", name="books_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);

        $order = $request->get('order');
        $orderBy = $request->get('orderBy');

        $books = $this->getBookRepository()->getAllBooks($filters, $orderBy, $order);
        $issuedBooks = $this->getUserBookRepository()->findAll();
        $users = $this->getUserRepository()->findAll();
        $genreList = $this->getGenreRepository()->findAll();
        $genresGroupedByBook = $this->getBookGenreRepository()->getGenresGroupedByBook();

        for ($i = 0; $i < count($issuedBooks); $i++) {
            $issuedBooks[$i] = $issuedBooks[$i]->getBook();
        }

        return $this->render('library/list.html.twig', [
            'books' => $books,
            'filters' => $filters,
            'order' => $order,
            'orderBy' => $orderBy,
            'issuedBooks' => $issuedBooks,
            'users' => $users,
            'genreList' => $genreList,
            'genresGroupedByBook' => $genresGroupedByBook
        ]);
    }

    /**
     * @Route("company/library/{bookId}/details", name="book_details")
     */
    public function detailsAction(Request $request)
    {
        $bookId = $request->get('bookId');

        /** @var Book $book */
        $book = $this->getBookRepository()->find($bookId);

        $bookChanges = $this->getBookDiffRepository()->getBookChanges($book);
        $bookFiles = $this->getBookFileRepository()->findBy(['book' => $bookId, 'deleted' => null]);
        $issuedBooks = $this->getUserBookRepository()->findAll();
        $users = $this->getUserRepository()->findAll();
        $bookComments = $this->getBookCommentRepository()->findBy(['book' => $book], ['id' => 'ASC']);
        $genreList = $this->getGenreRepository()->findAll();
        $bookGenres = $this->getBookGenreRepository()->findBy(['book' => $book]);

        for ($i = 0; $i < count($issuedBooks); $i++) {
            $issuedBooks[$i] = $issuedBooks[$i]->getBook();
        }

        return $this->render('library/details.html.twig', [
            'book' => $book,
            'bookChanges' => $bookChanges,
            'bookComments' => $bookComments,
            'bookFiles' => $bookFiles,
            'issuedBooks' => $issuedBooks,
            'users' => $users,
            'genreList' => $genreList,
            'bookGenres' => $bookGenres
        ]);
    }

    /**
     * Add book form.
     *
     * @Route("company/library/add", name="book_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        $em = $this->getEm();

        $bookDetails = $request->get('book');

        try {
            if (!empty($bookDetails) && $this->getUser()->canEditLibrary()) {
                $book = new Book();

                $book = $this->buildBook($book, $bookDetails);

                foreach ($bookDetails['genre'] as $genreId) {
                    $genre = $this->getGenreRepository()->find($genreId);
                    $bookGenre = new BookGenre();

                    $bookGenre
                        ->setBook($book)
                        ->setGenre($genre)
                    ;
                    $em->persist($bookGenre);
                }

                $em->persist($book);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit book form.
     *
     * @Route("company/library/edit", name="book_edit")
     * @throws \Exception
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        $em = $this->getEm();

        $bookId = $request->get('id');
        $bookDetails = $request->get('book');

        try {
            if ($this->getUser()->canEditLibrary()) {
                if (!empty($bookDetails)) {
                    /** @var Book $book */
                    $book = $this->getBookRepository()->find($bookId);

                    $book = $this->buildBook($book, $bookDetails);

                    $bookGenreList = $this->getBookGenreRepository()->findBy(['book' => $book]);

                    foreach ($bookGenreList as $bookGenre) {
                        $em->remove($bookGenre);
                    }

                    foreach ($bookDetails['genre'] as $genreId) {
                        $genre = $this->getGenreRepository()->find($genreId);
                        $bookGenre = new BookGenre();

                        $bookGenre
                            ->setBook($book)
                            ->setGenre($genre)
                        ;
                        $em->persist($bookGenre);
                    }


                    $em->persist($book);
                    $em->flush();
                }
            } else {
                throw new \Exception('You do not have permission to edit Library');
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Issue book form.
     *
     * @Route("company/library/{bookId}/issue", name="book_issue")
     */
    public function issueBookAction(Request $request)
    {
        $em = $this->getEm();
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $bookId = $request->get('bookId');
        $userId = $request->get('userId');

        try {
            $book = $this->getBookRepository()->find($bookId);
            $user = $this->getUserRepository()->find($userId);

            $bookDiff = $this->logChanges($book, BookDiff::STATUS_TAKEN);

            $userBook = new UserBook();
            $userBook
                ->setUser($user)
                ->setBook($book)
            ;

            $em->persist($userBook);
            $em->persist($bookDiff);
            $em->flush();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Return book form.
     *
     * @Route("company/library/{bookId}/return", name="book_return")
     */
    public function returnBookAction(Request $request)
    {
        $em = $this->getEm();
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $bookId = $request->get('bookId');

        try {
            $book = $this->getBookRepository()->find($bookId);

            $bookDiff = $this->logChanges($book, BookDiff::STATUS_RETURNED);

            $userBook = $this->getUserBookRepository()->findOneBy(['book' => $book]);

            $em->remove($userBook);
            $em->persist($bookDiff);
            $em->flush();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $book
     * @return BookDiff
     */
    protected function logChanges($book, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $bookDiff = new BookDiff();

        $bookDiff
            ->setUser($this->getUser())
            ->setBook($book)
            ->setStatus($status)
            ->setUpdatedAt(new \DateTime())
        ;

        $em->persist($bookDiff);
        $em->flush();

        return $bookDiff;
    }

    /**
     * @param Book $book
     * @param $bookDetails
     * @return Book
     */
    protected function buildBook(Book $book, $bookDetails)
    {
        $book
            ->setTitle($bookDetails['title'])
            ->setAuthor($bookDetails['author'])
            ->setEditor($bookDetails['editor'])
            ->setDescription($bookDetails['description'])
            ->setYearOfIssue($bookDetails['yearOfIssue'])
            ->setPublishingHouse($bookDetails['publishingHouse'])
        ;
        if (isset($bookDetails['paperVersion'])) {
            $book->setPaperVersion(1);
        } else {
            $book->setPaperVersion(0);
        }

        return $book;
    }

    /**
     * Upload file api.
     *
     * @Route("company/library/{bookId}/upload-file", name="book_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $bookId = $request->get('bookId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var Book $book */
        $book = $this->getBookRepository()->find($bookId);

        $bookFiles = $request->files->get('files');

        foreach($bookFiles as $bookFile) {
            try {
                $this->validateFile($bookFile);
                $this->processFile($book, $bookFile);
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param Book $book
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function processFile(Book $book, $file)
    {

        if ($file instanceof UploadedFile) {
            $bookFile = new BookFile();
            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $bookFile
                ->setFileName($file->getClientOriginalName())
                ->setFormat($format)
                ->setOwner($this->getUser())
                ->setFileSize($file->getSize())
                ->setBook($book)
                ->setUploadedAt(new \DateTime())
            ;

            $this->moveFile($file, $bookFile, $book->getId());

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($bookFile);
            $em->flush();
        }
    }

    /**
     * @param UploadedFile $file
     * @param BookFile $bookFile
     * @param int $bookId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, BookFile $bookFile, $bookId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $bookFile->setStoredFileName($storedFileName);
        $bookFile->setStoredFileDir($dirName);

        $basePath = $this->getParameter('book_files_root_dir') . '/' . $bookId . '/' . $dirName;
        // Move the file to the directory where brochures are stored
        $file->move(
            $basePath,
            $storedFileName
        );
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->getSize() > 102400000) {
            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
        }
    }

    /**
     * Download file file url.
     *
     * @Route("company/library/{bookId}/download/{fileId}/{preview}", name="book_download_file", defaults={"preview": 0})
     *
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var BookFile $bookFile */
        $bookFile = $this->getBookFileRepository()->find($fileId);

        if (!$bookFile->hasAccess($this->getUser())) {
            return $this->redirectToRoute('books_list');
        }

        $bookFileDownloadManager = new BookFileDownloadManager();
        $bookFileDownloadManager
            ->setBookFile($bookFile)
            ->setUser($this->getUser())
            ->setDownloadDate(new \DateTime(date('d.m.Y H:i')))
        ;
        $this->getEm()->persist($bookFileDownloadManager);
        $this->getEm()->flush();

        $fileName = $preview ? $bookFile->getStoredPreviewFileName() : $bookFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $bookFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $bookDir = $this->getParameter('book_files_root_dir') . '/' .
            $bookFile->getBook()->getId() . '/';

        $bookDir .= $bookFile->getStoredFileDir() ? $bookFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($bookDir . $fileName), 200, $headers);
    }

    /**
     * Delete file action.
     *
     * @Route("company/library/{bookId}/remove/{fileId}/", name="book_remove_file")
     */
    public function removeFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $bookFile = $this->getBookFileRepository()->find($fileId);

        /** @var BookFile $bookFile */
        if ($bookFile->canDeleteFile($this->getUser())) {
            $bookFile->setDeleted(true);

            $this->getEm()->persist($bookFile);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Add comment to Book.
     *
     * @Route("company/library/{bookId}/comment", name="book_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $bookId = $request->get('bookId');

        /** @var Book $book */
        $book = $this->getBookRepository()->find($bookId);

        $this->addComment($book, $comment);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('book_details', ['bookId' => $book->getId()]);
    }

    /**
     * @param Book $book
     * @param $comment
     * @return BookComment
     */
    protected function addComment(Book $book, $comment)
    {
        $bookComment = new BookComment();
        $bookComment->setCreatedAt(new \DateTime());

        if (!empty($comment['id'])) {
            $bookComment = $this->getBookCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $bookComment;
        }

        $bookComment
            ->setOwner($this->getUser())
            ->setBook($book)
            ->setCommentText(StringUtils::parseLinks($comment['text']))
        ;

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getBookCommentRepository()->find($comment['reply-id']);
            $bookComment->setParentComment($parentComment);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($bookComment);

        return $bookComment;
    }
}