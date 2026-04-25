<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/recipes', name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $reposotory): Response
    {
        $recipes = $reposotory->findWithDurationLowerThan(20);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'The recipe was successfully created');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

     #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
     public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The recipe was successfully edited');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
     }

     #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
     public function remove(Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'The recipe was successfully deleted');
        return $this->redirectToRoute('admin.recipe.index');
     }
}
