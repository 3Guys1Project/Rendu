<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AvatarExtension extends AbstractExtension
{
    public function __construct(#[Autowire('%avatar_base_url%')] private readonly string $avatarBaseUrl,
                                #[Autowire('%banner_base_url%')] private readonly string $bannerBaseUrl)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('avatar_path', [$this, 'getAvatarPath']),
            new TwigFunction('banner_path', [$this, 'getBannerPath']),
        ];
    }

    /**
     * Retourne l'URL de l'avatar pour un utilisateur donné.
     *
     * @param ?string $id
     * @return string
     */
    public function getAvatarPath(?string $id): string
    {
        if ($id === null) {
            return $this->avatarBaseUrl . '/default.webp';
        }
        return $this->avatarBaseUrl . '/' . $id . '.webp';
    }

    public function getBannerPath(?string $id): string|null
    {
        if ($id === null) {
            return null;
        }
        return $this->bannerBaseUrl . '/' . $id . '.webp';
    }
}
