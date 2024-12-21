<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\UserManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager implements UserManagerInterface
{

    public function __construct(
        private readonly UserPasswordHasherInterface             $passwordHasher,
        #[Autowire('%avatar_folder%')] private readonly string   $avatarFolder,
        #[Autowire('%avatar_base_url%')] private readonly string $avatarBaseUrl,
        #[Autowire('%banner_folder%')] private readonly string   $bannerFolder,
        #[Autowire('%banner_base_url%')] private readonly string $bannerBaseUrl
    )
    {
    }

    public function processNewUser(User $user, ?string $plainPassword): void
    {
        $this->hashPassword($user, $plainPassword);
    }

    private function hashPassword(User $user, ?string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }

    public function encodePassword(User $user, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    /**
     * @throws \Exception
     */
    public function saveAvatar(User $user, ?UploadedFile $file): void
    {
        if ($file !== null) {
            try {
                $id = $this->convertToWebp($file, $this->avatarFolder, $user->getAvatar());
                $user->setAvatar($id);
            } catch (\Exception $e) {
                throw new \Exception('An error occurred while processing the image.');
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function convertToWebp(UploadedFile $file, string $destination, ?string $prevId): string
    {
        $extension = strtolower($file->guessExtension());

        $id = uniqid();
        $fileName = $id . '.webp';
        $webpPath = $destination . '/' . $fileName;

        if ($extension === 'webp') {
            $file->move($destination, $fileName);
        } else {
            $image = match ($extension) {
                'jpeg', 'jpg' => imagecreatefromjpeg($file->getPathname()),
                'png' => imagecreatefrompng($file->getPathname()),
                default => throw new \Exception('Unsupported image type.'),
            };

            if ($image !== null) {
                imagewebp($image, $webpPath);
                imagedestroy($image);
            }
        }

        if ($prevId !== null) {
            $this->deletePreviousImage($prevId, $destination);
        }

        return $id;
    }

    private function deletePreviousImage(string $prevId, string $destination): void
    {
        $extensions = ['webp', 'jpeg', 'jpg', 'png'];

        foreach ($extensions as $extension) {
            $prevPath = $destination . '/' . $prevId . '.' . $extension;
            if (file_exists($prevPath)) {
                unlink($prevPath);
            }
        }
    }


    /**
     * @throws \Exception
     */
    public function saveBanner(User $user, ?UploadedFile $file): void
    {
        if ($file !== null) {
            try {
                $id = $this->convertToWebp($file, $this->bannerFolder, $user->getBanner());
                $user->setBanner($id);
            } catch (\Exception $e) {
                throw new \Exception('An error occurred while converting the image.');
            }
        }
    }
}