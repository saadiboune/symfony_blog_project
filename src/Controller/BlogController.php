<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('posts/index.html.twig', ['posts' => $postRepository -> findAll()]);
    }

//    /**
//     * @Route("/accueil", name="app_accueil")
//     * @param EntityManagerInterface $em
//     * @return Response
//     */
//    public function recentPosts(EntityManagerInterface $em): Response
//    {
//        $posts = $em->getRepository(Post::class)
//            ->findBy(
//                array(),
//                array('published' => 'ASC')
//            );
//        return $this->render('posts/index.html.twig', ['posts' => $posts]);
//    }

//<\d+>
    /**
     * @Route("/post/{slug}", name="app_post_details")
     * @param Post $post
     * @return Response
     */
    public function getPost(Post $post): Response {
//        getPin(PinRepository $pinRepository, int $id)
//        $pin = $pinRepository -> find($id);
//        if(! $pin) {
//            throw $this -> createNotFoundException('Pin ' . $id . ' Not Found');
//        }
        return $this -> render('posts/postDetails.html.twig', compact('post'));
    }

    /**
     * @Route("/create", name="app_post_create", methods={"GET", "POST", "PATCH"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, EntityManagerInterface $em) {
        $post = new Post;
        $form = $this -> createFormBuilder($post)
            -> add('titre', null, [
                'attr' =>['autofocus' => true]])
            ->add('slug')
            ->add('content', null, ['attr' => ['rows' => 10, 'cols' => 50]])
//            ->add('published', DateType::class, [
//                'widget' => 'single_text',
//            ])
            -> getForm()
        ;
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $post -> setPublished(new \DateTime());
            $em -> persist($post);
//            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('app_post_details', ['slug' => $post -> getSlug()]);
        }
        return $this->render('posts/create.html.twig', [
            'postForm' => $form -> createView()
        ]);
    }


    // delete 

    /**
     * @Route("/delete/{id<\d+>}", name="app_post_delete", methods={"GET", "POST", "PATCH"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete($id, EntityManagerInterface $em) {
        $article = new Post;

        if ($id) {
            $article = $this->getDoctrine()
                ->getRepository(Post::class)
                ->find($id);
                $em->remove($article);
                $em->flush();


            if (!$article){
                throw $this->createNotFoundException('Cet article n\'existe pas');
            }
        }

            return $this->redirectToRoute('app_home');

        

    }

    //update 

    /**
     * @Route("/update/{id<\d+>}", name="app_post_update", methods={"GET", "POST", "PATCH"})
     * @param Request $request
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    public function update(Request $request, $id, EntityManagerInterface $em) {
        $article = new Post;
        if ($id) {
            $article = $this->getDoctrine()
                ->getRepository(Post::class)
                ->find($id);
        }
        $form = $this -> createFormBuilder($article)
            -> add('titre', null, [
                'attr' =>['autofocus' => true]])
            ->add('slug')
            ->add('content', null, ['attr' => ['rows' => 10, 'cols' => 50]])
//            ->add('published', DateType::class, [
//                'widget' => 'single_text',
//            ])
            -> getForm()
        ;
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $article-> setPublished(new \DateTime());
            $em -> persist($article);
//            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('posts/create.html.twig', [
            'postForm' => $form -> createView()
        ]);
    }

}
