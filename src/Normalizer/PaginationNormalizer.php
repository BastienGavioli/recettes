<?php

namespace App\Normalizer;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    ) {}

    public function normalize(mixed $data, string|null $format = null, array $context = []):
        array|bool|float|int|string|\ArrayObject|null
    {
        if (!($data instanceof PaginationInterface)) {
            throw new RuntimeException();
        }

        return [
            'items' => array_map(fn (Recipe $recipe) => $this->normalizer->normalize($recipe, 'json', $context), $data->getItems()),
            'total' => $data->getTotalItemCount(),
            'page' => $data->getCurrentPageNumber(),
            'lastPage' => ceil($data->getTotalItemCount() / $data->getItemNumberPerPage())
        ];
    }

    public function supportsNormalization(mixed $data, string|null $format = null, array $context = []): bool
    {
        return $data instanceof PaginationInterface;
    }

    public function getSupportedTypes(string|null $format): array
    {
        return [
            PaginationInterface::class => true
        ];
    }
}