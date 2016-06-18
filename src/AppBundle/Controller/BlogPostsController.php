<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\BlogPostRepository;
use AppBundle\Form\Type\BlogPostType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @RouteResource("post")
 */
class BlogPostsController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get a single BlogPost.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\BlogPost",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param int         $blogPostId    the BlogPost id
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */
    public function getAction($blogPostId)
    {
        return $this->getBlogPostRepository()->createFindOneByIdQuery($blogPostId)->getSingleResult();
    }

    /**
     * Gets a collection of BlogPosts.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\BlogPost",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */
    public function cgetAction()
    {
        return $this->getBlogPostRepository()->createFindAllQuery()->getResult();
    }


    /**
     * Creates a new BlogPost
     *
     * @ApiDoc(
     *  input = "AppBundle\Form\Type\BlogPostFormType",
     *  output = "AppBundle\Entity\BlogPost",
     *  statusCodes={
     *         201="Returned when a new BlogPost has been successfully created",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(BlogPostType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $blogPost = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        $routeOptions = [
            'blogPostId' => $blogPost->getId(),
            '_format'    => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_post', $routeOptions, Response::HTTP_CREATED);
    }


    /**
     * Replaces existing BlogPost from the submitted data
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\BlogPostType",
     *   output = "AppBundle\Entity\BlogPost",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when errors",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the BlogPost id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function putAction(Request $request, $id)
    {
        $blogPost = $this->getBlogPostRepository()->find($id);

        $form = $this->createForm(BlogPostType::class, $blogPost, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $blogPost = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        $routeOptions = [
            'blogPostId' => $blogPost->getId(),
            '_format'    => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_post', $routeOptions, Response::HTTP_NO_CONTENT);
    }


    /**
     * Deletes a specific BlogPost by ID
     *
     * @ApiDoc(
     *  description="Deletes an existing BlogPost",
     *  statusCodes={
     *         204="Returned when an existing BlogPost has been successfully deleted",
     *         403="Returned when trying to delete a non existent BlogPost"
     *     }
     * )
     *
     * @param int         $id       the BlogPost id
     * @return View
     */
    public function deleteAction($id)
    {
        $blogPost = $this->getBlogPostRepository()->find($id);

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new View(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return BlogPostRepository
     */
    private function getBlogPostRepository()
    {
        return $this->get('crv.doctrine_entity_repository.blog_post');
    }
}