<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DayOff;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/profile", name="profile")
     */
    public function detailsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $dayOffs = $this->getDayOffRepository()->findBy([
            'owner' => $user->getId()
        ]);
        $types = DayOff::getTypes();

        if ($request->getMethod() == "POST") {
            $profile = $request->get('profile');
            $userImage = $request->files->get('user_image');

            if (!empty($profile['firstname'])) {
                $user->setFirstname($profile['firstname']);
            }

            if (!empty($profile['middlename'])) {
                $user->setMiddlename($profile['middlename']);
            }

            if (!empty($profile['phone'])) {
                $user->setPhone($profile['phone']);
            }

            if (!empty($profile['lastname'])) {
                $user->setLastname($profile['lastname']);
            }

            if (!empty($profile['theme'])) {
                $user->setTheme($profile['theme']);
            }

            if (!empty($profile['showMenu'])) {
                $user->setShowMenu($profile['showMenu']);
            }

            if (!empty($profile['biography'])) {
                $user->setBiography($profile['biography']);
            }

            if (!empty($profile['telegram_username'])) {
                $user->setTelegramUsername($profile['telegram_username']);
            }

            if ($userImage instanceof UploadedFile) {
                $imageUrl = $this->moveFile($userImage, md5(time() . $user->getUsername()));

                $user->setImageUrl($imageUrl);
            }

            $this->getDoctrine()->getEntityManager()->persist($user);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'dayOffs' => $dayOffs,
            'types' => $types
        ]);
    }

    /**
     * @Route("/profile/add-days-off", name="add_days_off")
     */
    public function addDaysOffAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var DayOff $dayOff */
        $dayOff = new DayOff();

        if ($request->getMethod() == "POST") {
            $dayOffDetails = $request->get('dayOff');

            $dayOff->setOwner($user);

            if (!empty($dayOffDetails['type'])) {
                $dayOff->setType($dayOffDetails['type']);
            }

            if (!empty($dayOffDetails['date'])) {
                list($startAt, $endAt) = explode(' - ', $dayOffDetails['date']);

                $startAt = (new \DateTime($startAt))->setTime(9,0);
                $endAt = (new \DateTime($endAt))->setTime(18,0);

                $dayOff->setDateStart($startAt);
                $dayOff->setDateEnd($endAt);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($dayOff);
            $em->flush();
        }

        return $this->redirectToRoute('profile');
    }

    /**
     * @param UploadedFile $file
     * @param $filename
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, $filename)
    {
        // Generate a unique name for the file before saving it
        $fileName = $filename .'.'.$file->guessExtension();

        if ($file->getSize() > 10000000) {
            throw new \Exception("Максимальный размер файла {$filename} 10MB");
        }

        // Move the file to the directory where brochures are stored
        $file->move(
            $this->getParameter('user_images_root_dir'),
            $fileName
        );

        $fileUrl = $this->getParameter('user_images_root_dir') . '/' . $fileName;

        $thumb = new \Imagick($fileUrl);
        $thumb->resizeImage(400, 300, \Imagick::FILTER_LANCZOS, 1, 0);
        $thumb->writeImage($fileUrl);

        return $this->getParameter('user_images_dir')  . '/' . $fileName;
    }
}
