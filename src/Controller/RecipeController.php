<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $reposotory): Response
    {
        $recipes = $reposotory->findWithDurationLowerThan(20);
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route('/recipes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $reposotory): Response
    {
        $recipe = $reposotory->find($id);

        if ($slug !== $recipe->getSlug()) {
            $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }
        
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

     #[Route('recipes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
     public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The recipe was successfully edited');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
     }

     #[Route('recipes/create', name: 'recipe.create')]
     public function create(Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'The recipe was successfully created');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
     }

     #[Route('recipes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
     public function remove(Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'The recipe was successfully deleted');
        return $this->redirectToRoute('recipe.index');
     }
}
