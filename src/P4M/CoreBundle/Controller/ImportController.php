<?php

namespace P4M\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Category;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\Wall;
use P4M\CoreBundle\Entity\PostType;
use P4M\UserBundle\Entity\User;

use P4M\PinocchioBundle\Event\PinocchioEvents;
use P4M\PinocchioBundle\Event\PostActivityEvent;


class ImportController extends Controller
{
    public function fromUserToAuthorAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $repo->findAll();
        
        
        foreach ($posts as $post)
        {
            $post->setAuthor($post->getUser());
            $em->persist($post);
            
        }
        
        $em->flush();
        
        return new Response('I love U');
        
    }
    
    
    private function stripAccents($string){
            return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
    
    public function defaultStrewsAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $repo = $em->getRepository('P4MUserBundle:User');
        $users = $repo->findAll();
        
        $wallCount = 0;
        
        foreach ($users as $user)
        {
            if (!count($user->getWalls()))
            {
                $wall = new Wall();
        
                $catNames = ['Art','Science','Politics','Internet'];
                $catRepo = $em->getRepository('P4MCoreBundle:Category');

                foreach ($catNames as $catName)
                {
                    $cat = $catRepo->findOneByName($catName);
                    if (null !== $cat)
                    {
                        $wall->addIncludedCategorie($cat);
                    }            
                }

                if (count($wall->getIncludedCategories()))
                {
        //            die('default wall');
                    $wall->setName('Example Strew');
                    $wall->setDescription('This is an example. You may want to edit and to customize this strew.');
                    $wall->setUser($user);
                    $wall->setPicture(null);
//                    $wall->setSlug(strtolower(str_replace(' ','-',$user->getUsername())).'/example-strew');
                    $em->persist($wall);
                    $wallCount ++;
                   

                }
                
//                die();
                 
                
            }
        }
        $em->flush();
        
        return new Response($wallCount.' walls created');
        
        
        
    }
    
    
    public function setFlagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $repo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $repo->findAll();
        
        foreach ($posts as $post)
        {
            $post->setQuarantaine(false);
            $em->persist($post);
            
        }
        $em->flush();
        
        return new Response('Flagged');
    }
    
    
    public function setPicturesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $postRepo->findAll();
        
                
        foreach ($posts as $post)
        {
            $extension = substr($post->getPicture(),  strripos($post->getPicture(), '.'));
            if (strstr($extension, '?') && !strstr($extension, '/'))
            {
                $extension = substr($extension, 0,  stripos($extension, '?'));
//                die($extension);
            }
            
            $path = __DIR__.'/../../../../web/images/posts/'.substr(utf8_encode($this->stripAccents($post->getTitle())),0,10).$post->getDateAddedTimeStamp().$extension;
//            die($path);
            
            if ($extension == '.jpg' || $extension == '.jpeg' || $extension == '.JPG' || $extension == '.JPEG' || $extension == '.png' || $extension == '.gif')
            {
                
                $handle = curl_init($post->getPicture());
                curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

                /* Get the HTML or whatever is linked in $url. */
                $response = curl_exec($handle);

                /* Check for 404 (file not found). */
                $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if($httpCode == 200) {
                    if (copy($post->getPicture(),$path))
                    {
                        $post->setLocalPicture('images/posts/'.substr(utf8_encode($this->stripAccents($post->getTitle())),0,10).$post->getDateAddedTimeStamp().$extension);
                        $em->persist($post);
                        chmod($path, 0755);
                    }
                }

                curl_close($handle);
            }
        }
        $em->flush();
        return new Response('OK</body>');
        
    }
    
    
    public function setScoreAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $postRepo->findAll();
        
        $user = $this->getUser();
        
        foreach ($posts as $post)
        {
            $event = new PostActivityEvent($post, $user);
            // On déclenche l'évènement
            $this->get('event_dispatcher')
                 ->dispatch(PinocchioEvents::onPostActivity, $event);
        }
        
        return new Response('OK');
        
    }
    
    
    
    public function importUsersAction()
    {
        return new Response('old');
        $em = $this->getDoctrine()->getManager();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pressformore.com/askMe.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = array(
//            'request' => 'getAllCategories'
            'request' => 'getAllUsers'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        $output = $this->convertJavaCode($output);
        
//        return new Response('<pre>'.print_r($output,true).'</pre>');
        
        $userList = json_decode($output,true);
        
        $persistableUsers = array();
        foreach($userList as $importedUser)
        {
            $user = new User();
            $user->setSalt('');
            $user->setEmail($importedUser['data']['user_email']);
            $user->setPassword($importedUser['data']['user_pass']);
            $user->setStatus($importedUser['data']['user_status']);
            $user->setUrl($importedUser['data']['user_url']);
            $user->setUsername($importedUser['data']['user_login']);
            $user->setWpId($importedUser['ID']);
            foreach ($importedUser['categories'] as $userCat)
            {
                $user->addCategorie($this->createCategory($userCat));
            }
            
            $persistableUsers[]=$user;
        }
        
        
        $i=0;
        while($i<count($persistableUsers))
        {
            $em->persist($persistableUsers[$i]);
            $i++;
        }
        
        $em->flush();
       
       return new Response('<pre>'.print_r($persistableUsers,true).'</pre>');
        
    }
    
    public function importAction()
    {
        return new Response('old');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pressformore.com/askMe.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = array(
//            'request' => 'getAllCategories'
            'request' => 'getAllPosts'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        $output = $this->convertJavaCode($output);
        
//       return new Response('<pre>'.$output.'</pre>');
        
       $list = json_decode($output,true);
       $postList = array();
        
        
        
        $em = $this->getDoctrine()->getManager();
        $postTypeRepo = $this->getDoctrine()->getRepository('P4MCoreBundle:PostType');
        $userRepo = $this->getDoctrine()->getRepository('P4MUserBundle:User');
        $categoryRepo = $this->getDoctrine()->getRepository('P4MCoreBundle:Category');
        /*7
        * 
        *  Ajoueter auteur !!! 
        */
        
        foreach ($list as $wp_post)
        {
//            return new Response(print_r($wp_post,true));
            $post = new Post();
            $post->setContent($wp_post['content'][0]);
            $post->setDateAdded(new \DateTime($wp_post['post_date']));
//            $post->setContent($wp_post['content']);
            $post->setPicture($wp_post['picture'][0]);
            $post->setTitle($wp_post['title']);
            $post->setSourceUrl($wp_post['sourceUrl'][0]);
            $post->setStatus(Post::STATUS_PUBLISHED);
            
            $postType = $postTypeRepo->findOneByName($wp_post['post_type']);
            if ($postType == null)
            {
                $newPostType = new PostType();
                $newPostType->setName($wp_post['post_type']);
                $em->persist($newPostType);
                $em->flush();
                $post->setType($newPostType);
            }
            else
            {
                $post->setType($postType);
            }
            
            $postUser = $userRepo->findOneByWpId($wp_post['post_author']);
            if ($postUser != null)
            {
                $post->setUser($postUser);
            }
            
            foreach ($wp_post['category'] as $catId)
            {
                $category = $categoryRepo->findOneByWpId($catId['cat_ID']);
//                return new Response('<pre>'.var_dump($category).'</pre>');
                if ($category != null)
                {
                    $post->addCategorie($category);
                }
            }
            
            
            
            
            $postList[]=$post;
            
            
            
//            $user = $em->
        }
        
//        return new Response('<pre>'.  print_r($post).'</pre>');
        
        $i=0;
        while ($i<count($postList))
        {
            $em->persist($postList[$i]);
            $i++;
        }
        
        $em->flush();
//        var_dump($postList);
        return new Response(count($postList));
//        
    }
    
    
    
   private function createCategory($datas)
   {
       $em = $this->getDoctrine()->getManager();
       $categoryRepo = $this->getDoctrine()->getRepository('P4MCoreBundle:Category');
       if (isset($datas['cat_ID']))
       {
           $category = $categoryRepo->findOneByWpId($datas['cat_ID']);
           if ($category == null)
           {
               $category = new Category();
               $category->setWpId($datas['cat_ID']);
               $category->setName($datas['name']);
               $category->setDescription($datas['description']);
               $category->setIcon('');
               $category->setColor('');
               if (isset($datas['icon']))
               {
                   $category->setIcon($datas['icon']);
               }
               if (isset($datas['color']) && $datas['color'] != null)
               {
                   $category->setColor($datas['color']);
               }
               
               $em->persist($category);
               $em->flush();
           }
           
           return $category;
 
       }
   }
   
   public function deletePostsAction()
   {
       $em = $this->getDoctrine()->getManager();
       $postRepo = $em->getRepository('P4MCoreBundle:Post');
       
       $posts = $postRepo->getNewPosts();
       
       foreach ($posts as $post)
       {
           $em->remove($post);
        }
        
        $em->flush();
        
        return new Response('cool');
       
       
       
   }
   public function assignTagsAction()
   {
       $em = $this->getDoctrine()->getManager();
       
       $postRepo = $em->getRepository('P4MCoreBundle:Post');
       $tagsRepo = $em->getRepository('P4MCoreBundle:Tag');
       
       $tags = $tagsRepo->findAll();
       $posts = $postRepo->findAll();
       
       $reponse = '';
       $i= 0;
       while($i<count($posts))
       {
           if (!count($posts[$i]->getTags()))
           {
               $tag = $tags[rand(0,count($tags)-1)];
               $tag->addPost($posts[$i]);
               $em->persist($tag);
//               $posTags = $posts[$i]->getTags();
               $reponse .= $tag->getName().'<br />';
               $em->flush();
           }
           $i++;
        }
       
        
//        $em->flush();
        
        return new Response($reponse.'</body>');
       
       
       
   }
    
    
    
    
    private function convertJavaCode($strInput)
    {
        $javaList = array
            (
                array
                    (
                        'search'=>"\u00e0",
                        'replace'=>"à"
                    ),
                array
                    (
                        'search'=>"\u00e2",
                        'replace'=>"â"
                    ),
                array
                    (
                        'search'=>"\u00e7",
                        'replace'=>"ç"
                    ),
                array
                    (
                        'search'=>"\u00e8",
                        'replace'=>"è"
                    ),
            
                array
                    (
                        'search'=>"\u00e9",
                        'replace'=>"é"
                    ),
            
                array
                    (
                        'search'=>"\u00ea",
                        'replace'=>"ê"
                    ),
            
                array
                    (
                        'search'=>"\u00eb",
                        'replace'=>"ë"
                    ),
                array
                    (
                        'search'=>"\u00ee",
                        'replace'=>"î"
                    ),
                array
                    (
                        'search'=>"\u00f4",
                        'replace'=>"ô"
                    ),
                array
                    (
                        'search'=>"\u00fb",
                        'replace'=>"û"
                    ),
            
            );
        
            foreach ($javaList as $value)
            {
                //Replace accented characters
                $strInput = str_replace($value['search'], $value['replace'], $strInput);
            }
        
       
        $strOutput = $strInput;
        return $strOutput;
        
        
    }
    
    public function postSlugAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postsRepo = $em->getRepository('P4MCoreBundle:Post');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pressformore.com/askMe.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = array(
//            'request' => 'getAllCategories'
            'request' => 'getAllPosts'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
//        return new Response($output);
        $list = json_decode($output,true);
        
        $posts = array();
        foreach ($list as $wpPost)
        {
            $post = $postsRepo->findOneBySourceUrl($wpPost['sourceUrl']);
            if (null != $post)
            {
                $post->setSlug($wpPost['wp_post']['post_name']);
                $posts[] = $post;
                $em->persist($posts[count($posts)-1]);
            }
        }

        $em->flush();
        return new Response('slugged'.$post->getSlug());
    }
    
    public function resetSlugAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postsRepo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $postsRepo->findAll();
        
        $toSlug = array();
        foreach ($posts as $post)
        {
            $post->setTitle($post->getTitle());
            array_push($toSlug, $post);
            $em->persist($toSlug[count($toSlug)-1]);
        }
        $em->flush();
        
        return new Response('slugged'.$toSlug[count($toSlug)-1]->getSlug());
    }
    
    public function updateActivityAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $postsRepo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $postsRepo->findAll();
        
        foreach ($posts as $post)
        {
            $activity = new \P4M\TrackingBundle\Entity\UserActivity();
            $activity->setDate($post->getDateAdded());
            $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_POST_ADDED);
            $activity->setUser($post->getUser());
            $activity->setPost($post);
            $em->persist($activity);
                    
        }
        
        $wallsRepo = $em->getRepository('P4MCoreBundle:Wall');
        $walls = $wallsRepo->findAll();
        
        foreach ($walls as $wall)
        {
            $activity = new \P4M\TrackingBundle\Entity\UserActivity();
            $activity->setDate($wall->getDateAdded());
            $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_WALL_CREATED);
            $activity->setUser($wall->getUser());
            $activity->setWall($wall);
            $em->persist($activity);
                    
        }
        $commentsRepo = $em->getRepository('P4MCoreBundle:Comment');
        $comments = $commentsRepo->findAll();
        
        foreach ($comments as $comment)
        {
            $activity = new \P4M\TrackingBundle\Entity\UserActivity();
            $activity->setDate($comment->getDateAdded());
            if ($comment->getWall() !== null)
            {
                $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_WALL_COMMENTED);
                $activity->setWall($comment->getWall());
                $activity->setComment($comment);
            }
            elseif($comment->getPost() !== null)
            {
                $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_POST_COMMENTED);
                $activity->setPost($comment->getPost());
                $activity->setComment($comment);
            }
            
            $activity->setUser($comment->getUser());
            
            $em->persist($activity);
                    
        }
        
        
        $votesRepo = $em->getRepository('P4MCoreBundle:Vote');
        $votes = $votesRepo->findAll();
        
        foreach ($votes as $vote)
        {
            $activity = new \P4M\TrackingBundle\Entity\UserActivity();
            $activity->setDate($vote->getDate());
            if ($vote->getWall() !== null)
            {
                $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_WALL_VOTED);
                $activity->setWall($vote->getWall());
                $activity->setVote($vote);
            }
            elseif($vote->getPost() !== null)
            {
                $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_POST_VOTED);
                $activity->setPost($vote->getPost());
                $activity->setVote($vote);
            }
            elseif($vote->getComment() !== null)
            {
                $activity->setType(\P4M\TrackingBundle\Entity\UserActivity::TYPE_COMMENT_VOTED);
                $activity->setComment($vote->getComment());
                $activity->setVote($vote);
            }
            
            $activity->setUser($comment->getUser());
            
            $em->persist($activity);
                    
        }
        
        $em->flush();
        
        return new Response('ok');
        
        
        
        
    }
    
    public function insertCountryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $isoCountries = '[{"name":"Afghanistan","alpha-2":"AF","alpha-3":"AFG","country-code":"004","iso_3166-2":"ISO 3166-2:AF","region-code":"142","sub-region-code":"034"},{"name":"Åland Islands","alpha-2":"AX","alpha-3":"ALA","country-code":"248","iso_3166-2":"ISO 3166-2:AX","region-code":"150","sub-region-code":"154"},{"name":"Albania","alpha-2":"AL","alpha-3":"ALB","country-code":"008","iso_3166-2":"ISO 3166-2:AL","region-code":"150","sub-region-code":"039"},{"name":"Algeria","alpha-2":"DZ","alpha-3":"DZA","country-code":"012","iso_3166-2":"ISO 3166-2:DZ","region-code":"002","sub-region-code":"015"},{"name":"American Samoa","alpha-2":"AS","alpha-3":"ASM","country-code":"016","iso_3166-2":"ISO 3166-2:AS","region-code":"009","sub-region-code":"061"},{"name":"Andorra","alpha-2":"AD","alpha-3":"AND","country-code":"020","iso_3166-2":"ISO 3166-2:AD","region-code":"150","sub-region-code":"039"},{"name":"Angola","alpha-2":"AO","alpha-3":"AGO","country-code":"024","iso_3166-2":"ISO 3166-2:AO","region-code":"002","sub-region-code":"017"},{"name":"Anguilla","alpha-2":"AI","alpha-3":"AIA","country-code":"660","iso_3166-2":"ISO 3166-2:AI","region-code":"019","sub-region-code":"029"},{"name":"Antarctica","alpha-2":"AQ","alpha-3":"ATA","country-code":"010","iso_3166-2":"ISO 3166-2:AQ"},{"name":"Antigua and Barbuda","alpha-2":"AG","alpha-3":"ATG","country-code":"028","iso_3166-2":"ISO 3166-2:AG","region-code":"019","sub-region-code":"029"},{"name":"Argentina","alpha-2":"AR","alpha-3":"ARG","country-code":"032","iso_3166-2":"ISO 3166-2:AR","region-code":"019","sub-region-code":"005"},{"name":"Armenia","alpha-2":"AM","alpha-3":"ARM","country-code":"051","iso_3166-2":"ISO 3166-2:AM","region-code":"142","sub-region-code":"145"},{"name":"Aruba","alpha-2":"AW","alpha-3":"ABW","country-code":"533","iso_3166-2":"ISO 3166-2:AW","region-code":"019","sub-region-code":"029"},{"name":"Australia","alpha-2":"AU","alpha-3":"AUS","country-code":"036","iso_3166-2":"ISO 3166-2:AU","region-code":"009","sub-region-code":"053"},{"name":"Austria","alpha-2":"AT","alpha-3":"AUT","country-code":"040","iso_3166-2":"ISO 3166-2:AT","region-code":"150","sub-region-code":"155"},{"name":"Azerbaijan","alpha-2":"AZ","alpha-3":"AZE","country-code":"031","iso_3166-2":"ISO 3166-2:AZ","region-code":"142","sub-region-code":"145"},{"name":"Bahamas","alpha-2":"BS","alpha-3":"BHS","country-code":"044","iso_3166-2":"ISO 3166-2:BS","region-code":"019","sub-region-code":"029"},{"name":"Bahrain","alpha-2":"BH","alpha-3":"BHR","country-code":"048","iso_3166-2":"ISO 3166-2:BH","region-code":"142","sub-region-code":"145"},{"name":"Bangladesh","alpha-2":"BD","alpha-3":"BGD","country-code":"050","iso_3166-2":"ISO 3166-2:BD","region-code":"142","sub-region-code":"034"},{"name":"Barbados","alpha-2":"BB","alpha-3":"BRB","country-code":"052","iso_3166-2":"ISO 3166-2:BB","region-code":"019","sub-region-code":"029"},{"name":"Belarus","alpha-2":"BY","alpha-3":"BLR","country-code":"112","iso_3166-2":"ISO 3166-2:BY","region-code":"150","sub-region-code":"151"},{"name":"Belgium","alpha-2":"BE","alpha-3":"BEL","country-code":"056","iso_3166-2":"ISO 3166-2:BE","region-code":"150","sub-region-code":"155"},{"name":"Belize","alpha-2":"BZ","alpha-3":"BLZ","country-code":"084","iso_3166-2":"ISO 3166-2:BZ","region-code":"019","sub-region-code":"013"},{"name":"Benin","alpha-2":"BJ","alpha-3":"BEN","country-code":"204","iso_3166-2":"ISO 3166-2:BJ","region-code":"002","sub-region-code":"011"},{"name":"Bermuda","alpha-2":"BM","alpha-3":"BMU","country-code":"060","iso_3166-2":"ISO 3166-2:BM","region-code":"019","sub-region-code":"021"},{"name":"Bhutan","alpha-2":"BT","alpha-3":"BTN","country-code":"064","iso_3166-2":"ISO 3166-2:BT","region-code":"142","sub-region-code":"034"},{"name":"Bolivia, Plurinational State of","alpha-2":"BO","alpha-3":"BOL","country-code":"068","iso_3166-2":"ISO 3166-2:BO","region-code":"019","sub-region-code":"005"},{"name":"Bonaire, Sint Eustatius and Saba","alpha-2":"BQ","alpha-3":"BES","country-code":"535","iso_3166-2":"ISO 3166-2:BQ","region-code":"019","sub-region-code":"029"},{"name":"Bosnia and Herzegovina","alpha-2":"BA","alpha-3":"BIH","country-code":"070","iso_3166-2":"ISO 3166-2:BA","region-code":"150","sub-region-code":"039"},{"name":"Botswana","alpha-2":"BW","alpha-3":"BWA","country-code":"072","iso_3166-2":"ISO 3166-2:BW","region-code":"002","sub-region-code":"018"},{"name":"Bouvet Island","alpha-2":"BV","alpha-3":"BVT","country-code":"074","iso_3166-2":"ISO 3166-2:BV"},{"name":"Brazil","alpha-2":"BR","alpha-3":"BRA","country-code":"076","iso_3166-2":"ISO 3166-2:BR","region-code":"019","sub-region-code":"005"},{"name":"British Indian Ocean Territory","alpha-2":"IO","alpha-3":"IOT","country-code":"086","iso_3166-2":"ISO 3166-2:IO"},{"name":"Brunei Darussalam","alpha-2":"BN","alpha-3":"BRN","country-code":"096","iso_3166-2":"ISO 3166-2:BN","region-code":"142","sub-region-code":"035"},{"name":"Bulgaria","alpha-2":"BG","alpha-3":"BGR","country-code":"100","iso_3166-2":"ISO 3166-2:BG","region-code":"150","sub-region-code":"151"},{"name":"Burkina Faso","alpha-2":"BF","alpha-3":"BFA","country-code":"854","iso_3166-2":"ISO 3166-2:BF","region-code":"002","sub-region-code":"011"},{"name":"Burundi","alpha-2":"BI","alpha-3":"BDI","country-code":"108","iso_3166-2":"ISO 3166-2:BI","region-code":"002","sub-region-code":"014"},{"name":"Cambodia","alpha-2":"KH","alpha-3":"KHM","country-code":"116","iso_3166-2":"ISO 3166-2:KH","region-code":"142","sub-region-code":"035"},{"name":"Cameroon","alpha-2":"CM","alpha-3":"CMR","country-code":"120","iso_3166-2":"ISO 3166-2:CM","region-code":"002","sub-region-code":"017"},{"name":"Canada","alpha-2":"CA","alpha-3":"CAN","country-code":"124","iso_3166-2":"ISO 3166-2:CA","region-code":"019","sub-region-code":"021"},{"name":"Cape Verde","alpha-2":"CV","alpha-3":"CPV","country-code":"132","iso_3166-2":"ISO 3166-2:CV","region-code":"002","sub-region-code":"011"},{"name":"Cayman Islands","alpha-2":"KY","alpha-3":"CYM","country-code":"136","iso_3166-2":"ISO 3166-2:KY","region-code":"019","sub-region-code":"029"},{"name":"Central African Republic","alpha-2":"CF","alpha-3":"CAF","country-code":"140","iso_3166-2":"ISO 3166-2:CF","region-code":"002","sub-region-code":"017"},{"name":"Chad","alpha-2":"TD","alpha-3":"TCD","country-code":"148","iso_3166-2":"ISO 3166-2:TD","region-code":"002","sub-region-code":"017"},{"name":"Chile","alpha-2":"CL","alpha-3":"CHL","country-code":"152","iso_3166-2":"ISO 3166-2:CL","region-code":"019","sub-region-code":"005"},{"name":"China","alpha-2":"CN","alpha-3":"CHN","country-code":"156","iso_3166-2":"ISO 3166-2:CN","region-code":"142","sub-region-code":"030"},{"name":"Christmas Island","alpha-2":"CX","alpha-3":"CXR","country-code":"162","iso_3166-2":"ISO 3166-2:CX"},{"name":"Cocos (Keeling) Islands","alpha-2":"CC","alpha-3":"CCK","country-code":"166","iso_3166-2":"ISO 3166-2:CC"},{"name":"Colombia","alpha-2":"CO","alpha-3":"COL","country-code":"170","iso_3166-2":"ISO 3166-2:CO","region-code":"019","sub-region-code":"005"},{"name":"Comoros","alpha-2":"KM","alpha-3":"COM","country-code":"174","iso_3166-2":"ISO 3166-2:KM","region-code":"002","sub-region-code":"014"},{"name":"Congo","alpha-2":"CG","alpha-3":"COG","country-code":"178","iso_3166-2":"ISO 3166-2:CG","region-code":"002","sub-region-code":"017"},{"name":"Congo, the Democratic Republic of the","alpha-2":"CD","alpha-3":"COD","country-code":"180","iso_3166-2":"ISO 3166-2:CD","region-code":"002","sub-region-code":"017"},{"name":"Cook Islands","alpha-2":"CK","alpha-3":"COK","country-code":"184","iso_3166-2":"ISO 3166-2:CK","region-code":"009","sub-region-code":"061"},{"name":"Costa Rica","alpha-2":"CR","alpha-3":"CRI","country-code":"188","iso_3166-2":"ISO 3166-2:CR","region-code":"019","sub-region-code":"013"},{"name":"Côte d\'Ivoire","alpha-2":"CI","alpha-3":"CIV","country-code":"384","iso_3166-2":"ISO 3166-2:CI","region-code":"002","sub-region-code":"011"},{"name":"Croatia","alpha-2":"HR","alpha-3":"HRV","country-code":"191","iso_3166-2":"ISO 3166-2:HR","region-code":"150","sub-region-code":"039"},{"name":"Cuba","alpha-2":"CU","alpha-3":"CUB","country-code":"192","iso_3166-2":"ISO 3166-2:CU","region-code":"019","sub-region-code":"029"},{"name":"Curaçao","alpha-2":"CW","alpha-3":"CUW","country-code":"531","iso_3166-2":"ISO 3166-2:CW","region-code":"019","sub-region-code":"029"},{"name":"Cyprus","alpha-2":"CY","alpha-3":"CYP","country-code":"196","iso_3166-2":"ISO 3166-2:CY","region-code":"142","sub-region-code":"145"},{"name":"Czech Republic","alpha-2":"CZ","alpha-3":"CZE","country-code":"203","iso_3166-2":"ISO 3166-2:CZ","region-code":"150","sub-region-code":"151"},{"name":"Denmark","alpha-2":"DK","alpha-3":"DNK","country-code":"208","iso_3166-2":"ISO 3166-2:DK","region-code":"150","sub-region-code":"154"},{"name":"Djibouti","alpha-2":"DJ","alpha-3":"DJI","country-code":"262","iso_3166-2":"ISO 3166-2:DJ","region-code":"002","sub-region-code":"014"},{"name":"Dominica","alpha-2":"DM","alpha-3":"DMA","country-code":"212","iso_3166-2":"ISO 3166-2:DM","region-code":"019","sub-region-code":"029"},{"name":"Dominican Republic","alpha-2":"DO","alpha-3":"DOM","country-code":"214","iso_3166-2":"ISO 3166-2:DO","region-code":"019","sub-region-code":"029"},{"name":"Ecuador","alpha-2":"EC","alpha-3":"ECU","country-code":"218","iso_3166-2":"ISO 3166-2:EC","region-code":"019","sub-region-code":"005"},{"name":"Egypt","alpha-2":"EG","alpha-3":"EGY","country-code":"818","iso_3166-2":"ISO 3166-2:EG","region-code":"002","sub-region-code":"015"},{"name":"El Salvador","alpha-2":"SV","alpha-3":"SLV","country-code":"222","iso_3166-2":"ISO 3166-2:SV","region-code":"019","sub-region-code":"013"},{"name":"Equatorial Guinea","alpha-2":"GQ","alpha-3":"GNQ","country-code":"226","iso_3166-2":"ISO 3166-2:GQ","region-code":"002","sub-region-code":"017"},{"name":"Eritrea","alpha-2":"ER","alpha-3":"ERI","country-code":"232","iso_3166-2":"ISO 3166-2:ER","region-code":"002","sub-region-code":"014"},{"name":"Estonia","alpha-2":"EE","alpha-3":"EST","country-code":"233","iso_3166-2":"ISO 3166-2:EE","region-code":"150","sub-region-code":"154"},{"name":"Ethiopia","alpha-2":"ET","alpha-3":"ETH","country-code":"231","iso_3166-2":"ISO 3166-2:ET","region-code":"002","sub-region-code":"014"},{"name":"Falkland Islands (Malvinas)","alpha-2":"FK","alpha-3":"FLK","country-code":"238","iso_3166-2":"ISO 3166-2:FK","region-code":"019","sub-region-code":"005"},{"name":"Faroe Islands","alpha-2":"FO","alpha-3":"FRO","country-code":"234","iso_3166-2":"ISO 3166-2:FO","region-code":"150","sub-region-code":"154"},{"name":"Fiji","alpha-2":"FJ","alpha-3":"FJI","country-code":"242","iso_3166-2":"ISO 3166-2:FJ","region-code":"009","sub-region-code":"054"},{"name":"Finland","alpha-2":"FI","alpha-3":"FIN","country-code":"246","iso_3166-2":"ISO 3166-2:FI","region-code":"150","sub-region-code":"154"},{"name":"France","alpha-2":"FR","alpha-3":"FRA","country-code":"250","iso_3166-2":"ISO 3166-2:FR","region-code":"150","sub-region-code":"155"},{"name":"French Guiana","alpha-2":"GF","alpha-3":"GUF","country-code":"254","iso_3166-2":"ISO 3166-2:GF","region-code":"019","sub-region-code":"005"},{"name":"French Polynesia","alpha-2":"PF","alpha-3":"PYF","country-code":"258","iso_3166-2":"ISO 3166-2:PF","region-code":"009","sub-region-code":"061"},{"name":"French Southern Territories","alpha-2":"TF","alpha-3":"ATF","country-code":"260","iso_3166-2":"ISO 3166-2:TF"},{"name":"Gabon","alpha-2":"GA","alpha-3":"GAB","country-code":"266","iso_3166-2":"ISO 3166-2:GA","region-code":"002","sub-region-code":"017"},{"name":"Gambia","alpha-2":"GM","alpha-3":"GMB","country-code":"270","iso_3166-2":"ISO 3166-2:GM","region-code":"002","sub-region-code":"011"},{"name":"Georgia","alpha-2":"GE","alpha-3":"GEO","country-code":"268","iso_3166-2":"ISO 3166-2:GE","region-code":"142","sub-region-code":"145"},{"name":"Germany","alpha-2":"DE","alpha-3":"DEU","country-code":"276","iso_3166-2":"ISO 3166-2:DE","region-code":"150","sub-region-code":"155"},{"name":"Ghana","alpha-2":"GH","alpha-3":"GHA","country-code":"288","iso_3166-2":"ISO 3166-2:GH","region-code":"002","sub-region-code":"011"},{"name":"Gibraltar","alpha-2":"GI","alpha-3":"GIB","country-code":"292","iso_3166-2":"ISO 3166-2:GI","region-code":"150","sub-region-code":"039"},{"name":"Greece","alpha-2":"GR","alpha-3":"GRC","country-code":"300","iso_3166-2":"ISO 3166-2:GR","region-code":"150","sub-region-code":"039"},{"name":"Greenland","alpha-2":"GL","alpha-3":"GRL","country-code":"304","iso_3166-2":"ISO 3166-2:GL","region-code":"019","sub-region-code":"021"},{"name":"Grenada","alpha-2":"GD","alpha-3":"GRD","country-code":"308","iso_3166-2":"ISO 3166-2:GD","region-code":"019","sub-region-code":"029"},{"name":"Guadeloupe","alpha-2":"GP","alpha-3":"GLP","country-code":"312","iso_3166-2":"ISO 3166-2:GP","region-code":"019","sub-region-code":"029"},{"name":"Guam","alpha-2":"GU","alpha-3":"GUM","country-code":"316","iso_3166-2":"ISO 3166-2:GU","region-code":"009","sub-region-code":"057"},{"name":"Guatemala","alpha-2":"GT","alpha-3":"GTM","country-code":"320","iso_3166-2":"ISO 3166-2:GT","region-code":"019","sub-region-code":"013"},{"name":"Guernsey","alpha-2":"GG","alpha-3":"GGY","country-code":"831","iso_3166-2":"ISO 3166-2:GG","region-code":"150","sub-region-code":"154"},{"name":"Guinea","alpha-2":"GN","alpha-3":"GIN","country-code":"324","iso_3166-2":"ISO 3166-2:GN","region-code":"002","sub-region-code":"011"},{"name":"Guinea-Bissau","alpha-2":"GW","alpha-3":"GNB","country-code":"624","iso_3166-2":"ISO 3166-2:GW","region-code":"002","sub-region-code":"011"},{"name":"Guyana","alpha-2":"GY","alpha-3":"GUY","country-code":"328","iso_3166-2":"ISO 3166-2:GY","region-code":"019","sub-region-code":"005"},{"name":"Haiti","alpha-2":"HT","alpha-3":"HTI","country-code":"332","iso_3166-2":"ISO 3166-2:HT","region-code":"019","sub-region-code":"029"},{"name":"Heard Island and McDonald Islands","alpha-2":"HM","alpha-3":"HMD","country-code":"334","iso_3166-2":"ISO 3166-2:HM"},{"name":"Holy See (Vatican City State)","alpha-2":"VA","alpha-3":"VAT","country-code":"336","iso_3166-2":"ISO 3166-2:VA","region-code":"150","sub-region-code":"039"},{"name":"Honduras","alpha-2":"HN","alpha-3":"HND","country-code":"340","iso_3166-2":"ISO 3166-2:HN","region-code":"019","sub-region-code":"013"},{"name":"Hong Kong","alpha-2":"HK","alpha-3":"HKG","country-code":"344","iso_3166-2":"ISO 3166-2:HK","region-code":"142","sub-region-code":"030"},{"name":"Hungary","alpha-2":"HU","alpha-3":"HUN","country-code":"348","iso_3166-2":"ISO 3166-2:HU","region-code":"150","sub-region-code":"151"},{"name":"Iceland","alpha-2":"IS","alpha-3":"ISL","country-code":"352","iso_3166-2":"ISO 3166-2:IS","region-code":"150","sub-region-code":"154"},{"name":"India","alpha-2":"IN","alpha-3":"IND","country-code":"356","iso_3166-2":"ISO 3166-2:IN","region-code":"142","sub-region-code":"034"},{"name":"Indonesia","alpha-2":"ID","alpha-3":"IDN","country-code":"360","iso_3166-2":"ISO 3166-2:ID","region-code":"142","sub-region-code":"035"},{"name":"Iran, Islamic Republic of","alpha-2":"IR","alpha-3":"IRN","country-code":"364","iso_3166-2":"ISO 3166-2:IR","region-code":"142","sub-region-code":"034"},{"name":"Iraq","alpha-2":"IQ","alpha-3":"IRQ","country-code":"368","iso_3166-2":"ISO 3166-2:IQ","region-code":"142","sub-region-code":"145"},{"name":"Ireland","alpha-2":"IE","alpha-3":"IRL","country-code":"372","iso_3166-2":"ISO 3166-2:IE","region-code":"150","sub-region-code":"154"},{"name":"Isle of Man","alpha-2":"IM","alpha-3":"IMN","country-code":"833","iso_3166-2":"ISO 3166-2:IM","region-code":"150","sub-region-code":"154"},{"name":"Israel","alpha-2":"IL","alpha-3":"ISR","country-code":"376","iso_3166-2":"ISO 3166-2:IL","region-code":"142","sub-region-code":"145"},{"name":"Italy","alpha-2":"IT","alpha-3":"ITA","country-code":"380","iso_3166-2":"ISO 3166-2:IT","region-code":"150","sub-region-code":"039"},{"name":"Jamaica","alpha-2":"JM","alpha-3":"JAM","country-code":"388","iso_3166-2":"ISO 3166-2:JM","region-code":"019","sub-region-code":"029"},{"name":"Japan","alpha-2":"JP","alpha-3":"JPN","country-code":"392","iso_3166-2":"ISO 3166-2:JP","region-code":"142","sub-region-code":"030"},{"name":"Jersey","alpha-2":"JE","alpha-3":"JEY","country-code":"832","iso_3166-2":"ISO 3166-2:JE","region-code":"150","sub-region-code":"154"},{"name":"Jordan","alpha-2":"JO","alpha-3":"JOR","country-code":"400","iso_3166-2":"ISO 3166-2:JO","region-code":"142","sub-region-code":"145"},{"name":"Kazakhstan","alpha-2":"KZ","alpha-3":"KAZ","country-code":"398","iso_3166-2":"ISO 3166-2:KZ","region-code":"142","sub-region-code":"143"},{"name":"Kenya","alpha-2":"KE","alpha-3":"KEN","country-code":"404","iso_3166-2":"ISO 3166-2:KE","region-code":"002","sub-region-code":"014"},{"name":"Kiribati","alpha-2":"KI","alpha-3":"KIR","country-code":"296","iso_3166-2":"ISO 3166-2:KI","region-code":"009","sub-region-code":"057"},{"name":"Korea, Democratic People\'s Republic of","alpha-2":"KP","alpha-3":"PRK","country-code":"408","iso_3166-2":"ISO 3166-2:KP","region-code":"142","sub-region-code":"030"},{"name":"Korea, Republic of","alpha-2":"KR","alpha-3":"KOR","country-code":"410","iso_3166-2":"ISO 3166-2:KR","region-code":"142","sub-region-code":"030"},{"name":"Kuwait","alpha-2":"KW","alpha-3":"KWT","country-code":"414","iso_3166-2":"ISO 3166-2:KW","region-code":"142","sub-region-code":"145"},{"name":"Kyrgyzstan","alpha-2":"KG","alpha-3":"KGZ","country-code":"417","iso_3166-2":"ISO 3166-2:KG","region-code":"142","sub-region-code":"143"},{"name":"Lao People\'s Democratic Republic","alpha-2":"LA","alpha-3":"LAO","country-code":"418","iso_3166-2":"ISO 3166-2:LA","region-code":"142","sub-region-code":"035"},{"name":"Latvia","alpha-2":"LV","alpha-3":"LVA","country-code":"428","iso_3166-2":"ISO 3166-2:LV","region-code":"150","sub-region-code":"154"},{"name":"Lebanon","alpha-2":"LB","alpha-3":"LBN","country-code":"422","iso_3166-2":"ISO 3166-2:LB","region-code":"142","sub-region-code":"145"},{"name":"Lesotho","alpha-2":"LS","alpha-3":"LSO","country-code":"426","iso_3166-2":"ISO 3166-2:LS","region-code":"002","sub-region-code":"018"},{"name":"Liberia","alpha-2":"LR","alpha-3":"LBR","country-code":"430","iso_3166-2":"ISO 3166-2:LR","region-code":"002","sub-region-code":"011"},{"name":"Libya","alpha-2":"LY","alpha-3":"LBY","country-code":"434","iso_3166-2":"ISO 3166-2:LY","region-code":"002","sub-region-code":"015"},{"name":"Liechtenstein","alpha-2":"LI","alpha-3":"LIE","country-code":"438","iso_3166-2":"ISO 3166-2:LI","region-code":"150","sub-region-code":"155"},{"name":"Lithuania","alpha-2":"LT","alpha-3":"LTU","country-code":"440","iso_3166-2":"ISO 3166-2:LT","region-code":"150","sub-region-code":"154"},{"name":"Luxembourg","alpha-2":"LU","alpha-3":"LUX","country-code":"442","iso_3166-2":"ISO 3166-2:LU","region-code":"150","sub-region-code":"155"},{"name":"Macao","alpha-2":"MO","alpha-3":"MAC","country-code":"446","iso_3166-2":"ISO 3166-2:MO","region-code":"142","sub-region-code":"030"},{"name":"Macedonia, the former Yugoslav Republic of","alpha-2":"MK","alpha-3":"MKD","country-code":"807","iso_3166-2":"ISO 3166-2:MK","region-code":"150","sub-region-code":"039"},{"name":"Madagascar","alpha-2":"MG","alpha-3":"MDG","country-code":"450","iso_3166-2":"ISO 3166-2:MG","region-code":"002","sub-region-code":"014"},{"name":"Malawi","alpha-2":"MW","alpha-3":"MWI","country-code":"454","iso_3166-2":"ISO 3166-2:MW","region-code":"002","sub-region-code":"014"},{"name":"Malaysia","alpha-2":"MY","alpha-3":"MYS","country-code":"458","iso_3166-2":"ISO 3166-2:MY","region-code":"142","sub-region-code":"035"},{"name":"Maldives","alpha-2":"MV","alpha-3":"MDV","country-code":"462","iso_3166-2":"ISO 3166-2:MV","region-code":"142","sub-region-code":"034"},{"name":"Mali","alpha-2":"ML","alpha-3":"MLI","country-code":"466","iso_3166-2":"ISO 3166-2:ML","region-code":"002","sub-region-code":"011"},{"name":"Malta","alpha-2":"MT","alpha-3":"MLT","country-code":"470","iso_3166-2":"ISO 3166-2:MT","region-code":"150","sub-region-code":"039"},{"name":"Marshall Islands","alpha-2":"MH","alpha-3":"MHL","country-code":"584","iso_3166-2":"ISO 3166-2:MH","region-code":"009","sub-region-code":"057"},{"name":"Martinique","alpha-2":"MQ","alpha-3":"MTQ","country-code":"474","iso_3166-2":"ISO 3166-2:MQ","region-code":"019","sub-region-code":"029"},{"name":"Mauritania","alpha-2":"MR","alpha-3":"MRT","country-code":"478","iso_3166-2":"ISO 3166-2:MR","region-code":"002","sub-region-code":"011"},{"name":"Mauritius","alpha-2":"MU","alpha-3":"MUS","country-code":"480","iso_3166-2":"ISO 3166-2:MU","region-code":"002","sub-region-code":"014"},{"name":"Mayotte","alpha-2":"YT","alpha-3":"MYT","country-code":"175","iso_3166-2":"ISO 3166-2:YT","region-code":"002","sub-region-code":"014"},{"name":"Mexico","alpha-2":"MX","alpha-3":"MEX","country-code":"484","iso_3166-2":"ISO 3166-2:MX","region-code":"019","sub-region-code":"013"},{"name":"Micronesia, Federated States of","alpha-2":"FM","alpha-3":"FSM","country-code":"583","iso_3166-2":"ISO 3166-2:FM","region-code":"009","sub-region-code":"057"},{"name":"Moldova, Republic of","alpha-2":"MD","alpha-3":"MDA","country-code":"498","iso_3166-2":"ISO 3166-2:MD","region-code":"150","sub-region-code":"151"},{"name":"Monaco","alpha-2":"MC","alpha-3":"MCO","country-code":"492","iso_3166-2":"ISO 3166-2:MC","region-code":"150","sub-region-code":"155"},{"name":"Mongolia","alpha-2":"MN","alpha-3":"MNG","country-code":"496","iso_3166-2":"ISO 3166-2:MN","region-code":"142","sub-region-code":"030"},{"name":"Montenegro","alpha-2":"ME","alpha-3":"MNE","country-code":"499","iso_3166-2":"ISO 3166-2:ME","region-code":"150","sub-region-code":"039"},{"name":"Montserrat","alpha-2":"MS","alpha-3":"MSR","country-code":"500","iso_3166-2":"ISO 3166-2:MS","region-code":"019","sub-region-code":"029"},{"name":"Morocco","alpha-2":"MA","alpha-3":"MAR","country-code":"504","iso_3166-2":"ISO 3166-2:MA","region-code":"002","sub-region-code":"015"},{"name":"Mozambique","alpha-2":"MZ","alpha-3":"MOZ","country-code":"508","iso_3166-2":"ISO 3166-2:MZ","region-code":"002","sub-region-code":"014"},{"name":"Myanmar","alpha-2":"MM","alpha-3":"MMR","country-code":"104","iso_3166-2":"ISO 3166-2:MM","region-code":"142","sub-region-code":"035"},{"name":"Namibia","alpha-2":"NA","alpha-3":"NAM","country-code":"516","iso_3166-2":"ISO 3166-2:NA","region-code":"002","sub-region-code":"018"},{"name":"Nauru","alpha-2":"NR","alpha-3":"NRU","country-code":"520","iso_3166-2":"ISO 3166-2:NR","region-code":"009","sub-region-code":"057"},{"name":"Nepal","alpha-2":"NP","alpha-3":"NPL","country-code":"524","iso_3166-2":"ISO 3166-2:NP","region-code":"142","sub-region-code":"034"},{"name":"Netherlands","alpha-2":"NL","alpha-3":"NLD","country-code":"528","iso_3166-2":"ISO 3166-2:NL","region-code":"150","sub-region-code":"155"},{"name":"New Caledonia","alpha-2":"NC","alpha-3":"NCL","country-code":"540","iso_3166-2":"ISO 3166-2:NC","region-code":"009","sub-region-code":"054"},{"name":"New Zealand","alpha-2":"NZ","alpha-3":"NZL","country-code":"554","iso_3166-2":"ISO 3166-2:NZ","region-code":"009","sub-region-code":"053"},{"name":"Nicaragua","alpha-2":"NI","alpha-3":"NIC","country-code":"558","iso_3166-2":"ISO 3166-2:NI","region-code":"019","sub-region-code":"013"},{"name":"Niger","alpha-2":"NE","alpha-3":"NER","country-code":"562","iso_3166-2":"ISO 3166-2:NE","region-code":"002","sub-region-code":"011"},{"name":"Nigeria","alpha-2":"NG","alpha-3":"NGA","country-code":"566","iso_3166-2":"ISO 3166-2:NG","region-code":"002","sub-region-code":"011"},{"name":"Niue","alpha-2":"NU","alpha-3":"NIU","country-code":"570","iso_3166-2":"ISO 3166-2:NU","region-code":"009","sub-region-code":"061"},{"name":"Norfolk Island","alpha-2":"NF","alpha-3":"NFK","country-code":"574","iso_3166-2":"ISO 3166-2:NF","region-code":"009","sub-region-code":"053"},{"name":"Northern Mariana Islands","alpha-2":"MP","alpha-3":"MNP","country-code":"580","iso_3166-2":"ISO 3166-2:MP","region-code":"009","sub-region-code":"057"},{"name":"Norway","alpha-2":"NO","alpha-3":"NOR","country-code":"578","iso_3166-2":"ISO 3166-2:NO","region-code":"150","sub-region-code":"154"},{"name":"Oman","alpha-2":"OM","alpha-3":"OMN","country-code":"512","iso_3166-2":"ISO 3166-2:OM","region-code":"142","sub-region-code":"145"},{"name":"Pakistan","alpha-2":"PK","alpha-3":"PAK","country-code":"586","iso_3166-2":"ISO 3166-2:PK","region-code":"142","sub-region-code":"034"},{"name":"Palau","alpha-2":"PW","alpha-3":"PLW","country-code":"585","iso_3166-2":"ISO 3166-2:PW","region-code":"009","sub-region-code":"057"},{"name":"Palestinian Territory, Occupied","alpha-2":"PS","alpha-3":"PSE","country-code":"275","iso_3166-2":"ISO 3166-2:PS","region-code":"142","sub-region-code":"145"},{"name":"Panama","alpha-2":"PA","alpha-3":"PAN","country-code":"591","iso_3166-2":"ISO 3166-2:PA","region-code":"019","sub-region-code":"013"},{"name":"Papua New Guinea","alpha-2":"PG","alpha-3":"PNG","country-code":"598","iso_3166-2":"ISO 3166-2:PG","region-code":"009","sub-region-code":"054"},{"name":"Paraguay","alpha-2":"PY","alpha-3":"PRY","country-code":"600","iso_3166-2":"ISO 3166-2:PY","region-code":"019","sub-region-code":"005"},{"name":"Peru","alpha-2":"PE","alpha-3":"PER","country-code":"604","iso_3166-2":"ISO 3166-2:PE","region-code":"019","sub-region-code":"005"},{"name":"Philippines","alpha-2":"PH","alpha-3":"PHL","country-code":"608","iso_3166-2":"ISO 3166-2:PH","region-code":"142","sub-region-code":"035"},{"name":"Pitcairn","alpha-2":"PN","alpha-3":"PCN","country-code":"612","iso_3166-2":"ISO 3166-2:PN","region-code":"009","sub-region-code":"061"},{"name":"Poland","alpha-2":"PL","alpha-3":"POL","country-code":"616","iso_3166-2":"ISO 3166-2:PL","region-code":"150","sub-region-code":"151"},{"name":"Portugal","alpha-2":"PT","alpha-3":"PRT","country-code":"620","iso_3166-2":"ISO 3166-2:PT","region-code":"150","sub-region-code":"039"},{"name":"Puerto Rico","alpha-2":"PR","alpha-3":"PRI","country-code":"630","iso_3166-2":"ISO 3166-2:PR","region-code":"019","sub-region-code":"029"},{"name":"Qatar","alpha-2":"QA","alpha-3":"QAT","country-code":"634","iso_3166-2":"ISO 3166-2:QA","region-code":"142","sub-region-code":"145"},{"name":"Réunion","alpha-2":"RE","alpha-3":"REU","country-code":"638","iso_3166-2":"ISO 3166-2:RE","region-code":"002","sub-region-code":"014"},{"name":"Romania","alpha-2":"RO","alpha-3":"ROU","country-code":"642","iso_3166-2":"ISO 3166-2:RO","region-code":"150","sub-region-code":"151"},{"name":"Russian Federation","alpha-2":"RU","alpha-3":"RUS","country-code":"643","iso_3166-2":"ISO 3166-2:RU","region-code":"150","sub-region-code":"151"},{"name":"Rwanda","alpha-2":"RW","alpha-3":"RWA","country-code":"646","iso_3166-2":"ISO 3166-2:RW","region-code":"002","sub-region-code":"014"},{"name":"Saint Barthélemy","alpha-2":"BL","alpha-3":"BLM","country-code":"652","iso_3166-2":"ISO 3166-2:BL","region-code":"019","sub-region-code":"029"},{"name":"Saint Helena, Ascension and Tristan da Cunha","alpha-2":"SH","alpha-3":"SHN","country-code":"654","iso_3166-2":"ISO 3166-2:SH","region-code":"002","sub-region-code":"011"},{"name":"Saint Kitts and Nevis","alpha-2":"KN","alpha-3":"KNA","country-code":"659","iso_3166-2":"ISO 3166-2:KN","region-code":"019","sub-region-code":"029"},{"name":"Saint Lucia","alpha-2":"LC","alpha-3":"LCA","country-code":"662","iso_3166-2":"ISO 3166-2:LC","region-code":"019","sub-region-code":"029"},{"name":"Saint Martin (French part)","alpha-2":"MF","alpha-3":"MAF","country-code":"663","iso_3166-2":"ISO 3166-2:MF","region-code":"019","sub-region-code":"029"},{"name":"Saint Pierre and Miquelon","alpha-2":"PM","alpha-3":"SPM","country-code":"666","iso_3166-2":"ISO 3166-2:PM","region-code":"019","sub-region-code":"021"},{"name":"Saint Vincent and the Grenadines","alpha-2":"VC","alpha-3":"VCT","country-code":"670","iso_3166-2":"ISO 3166-2:VC","region-code":"019","sub-region-code":"029"},{"name":"Samoa","alpha-2":"WS","alpha-3":"WSM","country-code":"882","iso_3166-2":"ISO 3166-2:WS","region-code":"009","sub-region-code":"061"},{"name":"San Marino","alpha-2":"SM","alpha-3":"SMR","country-code":"674","iso_3166-2":"ISO 3166-2:SM","region-code":"150","sub-region-code":"039"},{"name":"Sao Tome and Principe","alpha-2":"ST","alpha-3":"STP","country-code":"678","iso_3166-2":"ISO 3166-2:ST","region-code":"002","sub-region-code":"017"},{"name":"Saudi Arabia","alpha-2":"SA","alpha-3":"SAU","country-code":"682","iso_3166-2":"ISO 3166-2:SA","region-code":"142","sub-region-code":"145"},{"name":"Senegal","alpha-2":"SN","alpha-3":"SEN","country-code":"686","iso_3166-2":"ISO 3166-2:SN","region-code":"002","sub-region-code":"011"},{"name":"Serbia","alpha-2":"RS","alpha-3":"SRB","country-code":"688","iso_3166-2":"ISO 3166-2:RS","region-code":"150","sub-region-code":"039"},{"name":"Seychelles","alpha-2":"SC","alpha-3":"SYC","country-code":"690","iso_3166-2":"ISO 3166-2:SC","region-code":"002","sub-region-code":"014"},{"name":"Sierra Leone","alpha-2":"SL","alpha-3":"SLE","country-code":"694","iso_3166-2":"ISO 3166-2:SL","region-code":"002","sub-region-code":"011"},{"name":"Singapore","alpha-2":"SG","alpha-3":"SGP","country-code":"702","iso_3166-2":"ISO 3166-2:SG","region-code":"142","sub-region-code":"035"},{"name":"Sint Maarten (Dutch part)","alpha-2":"SX","alpha-3":"SXM","country-code":"534","iso_3166-2":"ISO 3166-2:SX","region-code":"019","sub-region-code":"029"},{"name":"Slovakia","alpha-2":"SK","alpha-3":"SVK","country-code":"703","iso_3166-2":"ISO 3166-2:SK","region-code":"150","sub-region-code":"151"},{"name":"Slovenia","alpha-2":"SI","alpha-3":"SVN","country-code":"705","iso_3166-2":"ISO 3166-2:SI","region-code":"150","sub-region-code":"039"},{"name":"Solomon Islands","alpha-2":"SB","alpha-3":"SLB","country-code":"090","iso_3166-2":"ISO 3166-2:SB","region-code":"009","sub-region-code":"054"},{"name":"Somalia","alpha-2":"SO","alpha-3":"SOM","country-code":"706","iso_3166-2":"ISO 3166-2:SO","region-code":"002","sub-region-code":"014"},{"name":"South Africa","alpha-2":"ZA","alpha-3":"ZAF","country-code":"710","iso_3166-2":"ISO 3166-2:ZA","region-code":"002","sub-region-code":"018"},{"name":"South Georgia and the South Sandwich Islands","alpha-2":"GS","alpha-3":"SGS","country-code":"239","iso_3166-2":"ISO 3166-2:GS"},{"name":"South Sudan","alpha-2":"SS","alpha-3":"SSD","country-code":"728","iso_3166-2":"ISO 3166-2:SS","region-code":"002","sub-region-code":"015"},{"name":"Spain","alpha-2":"ES","alpha-3":"ESP","country-code":"724","iso_3166-2":"ISO 3166-2:ES","region-code":"150","sub-region-code":"039"},{"name":"Sri Lanka","alpha-2":"LK","alpha-3":"LKA","country-code":"144","iso_3166-2":"ISO 3166-2:LK","region-code":"142","sub-region-code":"034"},{"name":"Sudan","alpha-2":"SD","alpha-3":"SDN","country-code":"729","iso_3166-2":"ISO 3166-2:SD","region-code":"002","sub-region-code":"015"},{"name":"Suriname","alpha-2":"SR","alpha-3":"SUR","country-code":"740","iso_3166-2":"ISO 3166-2:SR","region-code":"019","sub-region-code":"005"},{"name":"Svalbard and Jan Mayen","alpha-2":"SJ","alpha-3":"SJM","country-code":"744","iso_3166-2":"ISO 3166-2:SJ","region-code":"150","sub-region-code":"154"},{"name":"Swaziland","alpha-2":"SZ","alpha-3":"SWZ","country-code":"748","iso_3166-2":"ISO 3166-2:SZ","region-code":"002","sub-region-code":"018"},{"name":"Sweden","alpha-2":"SE","alpha-3":"SWE","country-code":"752","iso_3166-2":"ISO 3166-2:SE","region-code":"150","sub-region-code":"154"},{"name":"Switzerland","alpha-2":"CH","alpha-3":"CHE","country-code":"756","iso_3166-2":"ISO 3166-2:CH","region-code":"150","sub-region-code":"155"},{"name":"Syrian Arab Republic","alpha-2":"SY","alpha-3":"SYR","country-code":"760","iso_3166-2":"ISO 3166-2:SY","region-code":"142","sub-region-code":"145"},{"name":"Taiwan, Province of China","alpha-2":"TW","alpha-3":"TWN","country-code":"158","iso_3166-2":"ISO 3166-2:TW","region-code":"142","sub-region-code":"030"},{"name":"Tajikistan","alpha-2":"TJ","alpha-3":"TJK","country-code":"762","iso_3166-2":"ISO 3166-2:TJ","region-code":"142","sub-region-code":"143"},{"name":"Tanzania, United Republic of","alpha-2":"TZ","alpha-3":"TZA","country-code":"834","iso_3166-2":"ISO 3166-2:TZ","region-code":"002","sub-region-code":"014"},{"name":"Thailand","alpha-2":"TH","alpha-3":"THA","country-code":"764","iso_3166-2":"ISO 3166-2:TH","region-code":"142","sub-region-code":"035"},{"name":"Timor-Leste","alpha-2":"TL","alpha-3":"TLS","country-code":"626","iso_3166-2":"ISO 3166-2:TL","region-code":"142","sub-region-code":"035"},{"name":"Togo","alpha-2":"TG","alpha-3":"TGO","country-code":"768","iso_3166-2":"ISO 3166-2:TG","region-code":"002","sub-region-code":"011"},{"name":"Tokelau","alpha-2":"TK","alpha-3":"TKL","country-code":"772","iso_3166-2":"ISO 3166-2:TK","region-code":"009","sub-region-code":"061"},{"name":"Tonga","alpha-2":"TO","alpha-3":"TON","country-code":"776","iso_3166-2":"ISO 3166-2:TO","region-code":"009","sub-region-code":"061"},{"name":"Trinidad and Tobago","alpha-2":"TT","alpha-3":"TTO","country-code":"780","iso_3166-2":"ISO 3166-2:TT","region-code":"019","sub-region-code":"029"},{"name":"Tunisia","alpha-2":"TN","alpha-3":"TUN","country-code":"788","iso_3166-2":"ISO 3166-2:TN","region-code":"002","sub-region-code":"015"},{"name":"Turkey","alpha-2":"TR","alpha-3":"TUR","country-code":"792","iso_3166-2":"ISO 3166-2:TR","region-code":"142","sub-region-code":"145"},{"name":"Turkmenistan","alpha-2":"TM","alpha-3":"TKM","country-code":"795","iso_3166-2":"ISO 3166-2:TM","region-code":"142","sub-region-code":"143"},{"name":"Turks and Caicos Islands","alpha-2":"TC","alpha-3":"TCA","country-code":"796","iso_3166-2":"ISO 3166-2:TC","region-code":"019","sub-region-code":"029"},{"name":"Tuvalu","alpha-2":"TV","alpha-3":"TUV","country-code":"798","iso_3166-2":"ISO 3166-2:TV","region-code":"009","sub-region-code":"061"},{"name":"Uganda","alpha-2":"UG","alpha-3":"UGA","country-code":"800","iso_3166-2":"ISO 3166-2:UG","region-code":"002","sub-region-code":"014"},{"name":"Ukraine","alpha-2":"UA","alpha-3":"UKR","country-code":"804","iso_3166-2":"ISO 3166-2:UA","region-code":"150","sub-region-code":"151"},{"name":"United Arab Emirates","alpha-2":"AE","alpha-3":"ARE","country-code":"784","iso_3166-2":"ISO 3166-2:AE","region-code":"142","sub-region-code":"145"},{"name":"United Kingdom","alpha-2":"GB","alpha-3":"GBR","country-code":"826","iso_3166-2":"ISO 3166-2:GB","region-code":"150","sub-region-code":"154"},{"name":"United States","alpha-2":"US","alpha-3":"USA","country-code":"840","iso_3166-2":"ISO 3166-2:US","region-code":"019","sub-region-code":"021"},{"name":"United States Minor Outlying Islands","alpha-2":"UM","alpha-3":"UMI","country-code":"581","iso_3166-2":"ISO 3166-2:UM"},{"name":"Uruguay","alpha-2":"UY","alpha-3":"URY","country-code":"858","iso_3166-2":"ISO 3166-2:UY","region-code":"019","sub-region-code":"005"},{"name":"Uzbekistan","alpha-2":"UZ","alpha-3":"UZB","country-code":"860","iso_3166-2":"ISO 3166-2:UZ","region-code":"142","sub-region-code":"143"},{"name":"Vanuatu","alpha-2":"VU","alpha-3":"VUT","country-code":"548","iso_3166-2":"ISO 3166-2:VU","region-code":"009","sub-region-code":"054"},{"name":"Venezuela, Bolivarian Republic of","alpha-2":"VE","alpha-3":"VEN","country-code":"862","iso_3166-2":"ISO 3166-2:VE","region-code":"019","sub-region-code":"005"},{"name":"Viet Nam","alpha-2":"VN","alpha-3":"VNM","country-code":"704","iso_3166-2":"ISO 3166-2:VN","region-code":"142","sub-region-code":"035"},{"name":"Virgin Islands, British","alpha-2":"VG","alpha-3":"VGB","country-code":"092","iso_3166-2":"ISO 3166-2:VG","region-code":"019","sub-region-code":"029"},{"name":"Virgin Islands, U.S.","alpha-2":"VI","alpha-3":"VIR","country-code":"850","iso_3166-2":"ISO 3166-2:VI","region-code":"019","sub-region-code":"029"},{"name":"Wallis and Futuna","alpha-2":"WF","alpha-3":"WLF","country-code":"876","iso_3166-2":"ISO 3166-2:WF","region-code":"009","sub-region-code":"061"},{"name":"Western Sahara","alpha-2":"EH","alpha-3":"ESH","country-code":"732","iso_3166-2":"ISO 3166-2:EH","region-code":"002","sub-region-code":"015"},{"name":"Yemen","alpha-2":"YE","alpha-3":"YEM","country-code":"887","iso_3166-2":"ISO 3166-2:YE","region-code":"142","sub-region-code":"145"},{"name":"Zambia","alpha-2":"ZM","alpha-3":"ZMB","country-code":"894","iso_3166-2":"ISO 3166-2:ZM","region-code":"002","sub-region-code":"014"},{"name":"Zimbabwe","alpha-2":"ZW","alpha-3":"ZWE","country-code":"716","iso_3166-2":"ISO 3166-2:ZW","region-code":"002","sub-region-code":"014"}]';
        
        $countries = json_decode($isoCountries,true);
        
        foreach ($countries as $country)
        {
            $countryEntity = new \P4M\UserBundle\Entity\Country();
            $countryEntity->setAlpha2($country['alpha-2']);
            $countryEntity->setAlpha3($country['alpha-3']);
            $countryEntity->setIso($country['iso_3166-2']);
            $countryEntity->setCountryCode($country['country-code']);
            $countryEntity->setName($country['name']);
            if (isset($country['region-code']))
            {
                $countryEntity->setRegionCode($country['region-code']);
            }
            if (isset($country['sub-region-code']))
            {
                $countryEntity->setSubRegionCode($country['sub-region-code']);
            }
            
            $em->persist($countryEntity);
        }
        $em->flush();
        
        return new Response('OK</body>');
    }
    
    public function insertLanguesAction()
    {
        /**
 * @author Phil Teare
 * using wikipedia data
 */
    $isoLangs = '{
    "ab":{
        "name":"Abkhaz",
        "nativeName":"аҧсуа"
    },
    "aa":{
        "name":"Afar",
        "nativeName":"Afaraf"
    },
    "af":{
        "name":"Afrikaans",
        "nativeName":"Afrikaans"
    },
    "ak":{
        "name":"Akan",
        "nativeName":"Akan"
    },
    "sq":{
        "name":"Albanian",
        "nativeName":"Shqip"
    },
    "am":{
        "name":"Amharic",
        "nativeName":"አማርኛ"
    },
    "ar":{
        "name":"Arabic",
        "nativeName":"العربية"
    },
    "an":{
        "name":"Aragonese",
        "nativeName":"Aragonés"
    },
    "hy":{
        "name":"Armenian",
        "nativeName":"Հայերեն"
    },
    "as":{
        "name":"Assamese",
        "nativeName":"অসমীয়া"
    },
    "av":{
        "name":"Avaric",
        "nativeName":"авар мацӀ, магӀарул мацӀ"
    },
    "ae":{
        "name":"Avestan",
        "nativeName":"avesta"
    },
    "ay":{
        "name":"Aymara",
        "nativeName":"aymar aru"
    },
    "az":{
        "name":"Azerbaijani",
        "nativeName":"azərbaycan dili"
    },
    "bm":{
        "name":"Bambara",
        "nativeName":"bamanankan"
    },
    "ba":{
        "name":"Bashkir",
        "nativeName":"башҡорт теле"
    },
    "eu":{
        "name":"Basque",
        "nativeName":"euskara, euskera"
    },
    "be":{
        "name":"Belarusian",
        "nativeName":"Беларуская"
    },
    "bn":{
        "name":"Bengali",
        "nativeName":"বাংলা"
    },
    "bh":{
        "name":"Bihari",
        "nativeName":"भोजपुरी"
    },
    "bi":{
        "name":"Bislama",
        "nativeName":"Bislama"
    },
    "bs":{
        "name":"Bosnian",
        "nativeName":"bosanski jezik"
    },
    "br":{
        "name":"Breton",
        "nativeName":"brezhoneg"
    },
    "bg":{
        "name":"Bulgarian",
        "nativeName":"български език"
    },
    "my":{
        "name":"Burmese",
        "nativeName":"ဗမာစာ"
    },
    "ca":{
        "name":"Catalan; Valencian",
        "nativeName":"Català"
    },
    "ch":{
        "name":"Chamorro",
        "nativeName":"Chamoru"
    },
    "ce":{
        "name":"Chechen",
        "nativeName":"нохчийн мотт"
    },
    "ny":{
        "name":"Chichewa; Chewa; Nyanja",
        "nativeName":"chiCheŵa, chinyanja"
    },
    "zh":{
        "name":"Chinese",
        "nativeName":"中文 (Zhōngwén), 汉语, 漢語"
    },
    "cv":{
        "name":"Chuvash",
        "nativeName":"чӑваш чӗлхи"
    },
    "kw":{
        "name":"Cornish",
        "nativeName":"Kernewek"
    },
    "co":{
        "name":"Corsican",
        "nativeName":"corsu, lingua corsa"
    },
    "cr":{
        "name":"Cree",
        "nativeName":"ᓀᐦᐃᔭᐍᐏᐣ"
    },
    "hr":{
        "name":"Croatian",
        "nativeName":"hrvatski"
    },
    "cs":{
        "name":"Czech",
        "nativeName":"česky, čeština"
    },
    "da":{
        "name":"Danish",
        "nativeName":"dansk"
    },
    "dv":{
        "name":"Divehi; Dhivehi; Maldivian;",
        "nativeName":"ދިވެހި"
    },
    "nl":{
        "name":"Dutch",
        "nativeName":"Nederlands, Vlaams"
    },
    "en":{
        "name":"English",
        "nativeName":"English"
    },
    "eo":{
        "name":"Esperanto",
        "nativeName":"Esperanto"
    },
    "et":{
        "name":"Estonian",
        "nativeName":"eesti, eesti keel"
    },
    "ee":{
        "name":"Ewe",
        "nativeName":"Eʋegbe"
    },
    "fo":{
        "name":"Faroese",
        "nativeName":"føroyskt"
    },
    "fj":{
        "name":"Fijian",
        "nativeName":"vosa Vakaviti"
    },
    "fi":{
        "name":"Finnish",
        "nativeName":"suomi, suomen kieli"
    },
    "fr":{
        "name":"French",
        "nativeName":"français, langue française"
    },
    "ff":{
        "name":"Fula; Fulah; Pulaar; Pular",
        "nativeName":"Fulfulde, Pulaar, Pular"
    },
    "gl":{
        "name":"Galician",
        "nativeName":"Galego"
    },
    "ka":{
        "name":"Georgian",
        "nativeName":"ქართული"
    },
    "de":{
        "name":"German",
        "nativeName":"Deutsch"
    },
    "el":{
        "name":"Greek, Modern",
        "nativeName":"Ελληνικά"
    },
    "gn":{
        "name":"Guaraní",
        "nativeName":"Avañeẽ"
    },
    "gu":{
        "name":"Gujarati",
        "nativeName":"ગુજરાતી"
    },
    "ht":{
        "name":"Haitian; Haitian Creole",
        "nativeName":"Kreyòl ayisyen"
    },
    "ha":{
        "name":"Hausa",
        "nativeName":"Hausa, هَوُسَ"
    },
    "he":{
        "name":"Hebrew (modern)",
        "nativeName":"עברית"
    },
    "hz":{
        "name":"Herero",
        "nativeName":"Otjiherero"
    },
    "hi":{
        "name":"Hindi",
        "nativeName":"हिन्दी, हिंदी"
    },
    "ho":{
        "name":"Hiri Motu",
        "nativeName":"Hiri Motu"
    },
    "hu":{
        "name":"Hungarian",
        "nativeName":"Magyar"
    },
    "ia":{
        "name":"Interlingua",
        "nativeName":"Interlingua"
    },
    "id":{
        "name":"Indonesian",
        "nativeName":"Bahasa Indonesia"
    },
    "ie":{
        "name":"Interlingue",
        "nativeName":"Originally called Occidental; then Interlingue after WWII"
    },
    "ga":{
        "name":"Irish",
        "nativeName":"Gaeilge"
    },
    "ig":{
        "name":"Igbo",
        "nativeName":"Asụsụ Igbo"
    },
    "ik":{
        "name":"Inupiaq",
        "nativeName":"Iñupiaq, Iñupiatun"
    },
    "io":{
        "name":"Ido",
        "nativeName":"Ido"
    },
    "is":{
        "name":"Icelandic",
        "nativeName":"Íslenska"
    },
    "it":{
        "name":"Italian",
        "nativeName":"Italiano"
    },
    "iu":{
        "name":"Inuktitut",
        "nativeName":"ᐃᓄᒃᑎᑐᑦ"
    },
    "ja":{
        "name":"Japanese",
        "nativeName":"日本語 (にほんご／にっぽんご)"
    },
    "jv":{
        "name":"Javanese",
        "nativeName":"basa Jawa"
    },
    "kl":{
        "name":"Kalaallisut, Greenlandic",
        "nativeName":"kalaallisut, kalaallit oqaasii"
    },
    "kn":{
        "name":"Kannada",
        "nativeName":"ಕನ್ನಡ"
    },
    "kr":{
        "name":"Kanuri",
        "nativeName":"Kanuri"
    },
    "ks":{
        "name":"Kashmiri",
        "nativeName":"कश्मीरी, كشميري‎"
    },
    "kk":{
        "name":"Kazakh",
        "nativeName":"Қазақ тілі"
    },
    "km":{
        "name":"Khmer",
        "nativeName":"ភាសាខ្មែរ"
    },
    "ki":{
        "name":"Kikuyu, Gikuyu",
        "nativeName":"Gĩkũyũ"
    },
    "rw":{
        "name":"Kinyarwanda",
        "nativeName":"Ikinyarwanda"
    },
    "ky":{
        "name":"Kirghiz, Kyrgyz",
        "nativeName":"кыргыз тили"
    },
    "kv":{
        "name":"Komi",
        "nativeName":"коми кыв"
    },
    "kg":{
        "name":"Kongo",
        "nativeName":"KiKongo"
    },
    "ko":{
        "name":"Korean",
        "nativeName":"한국어 (韓國語), 조선말 (朝鮮語)"
    },
    "ku":{
        "name":"Kurdish",
        "nativeName":"Kurdî, كوردی‎"
    },
    "kj":{
        "name":"Kwanyama, Kuanyama",
        "nativeName":"Kuanyama"
    },
    "la":{
        "name":"Latin",
        "nativeName":"latine, lingua latina"
    },
    "lb":{
        "name":"Luxembourgish, Letzeburgesch",
        "nativeName":"Lëtzebuergesch"
    },
    "lg":{
        "name":"Luganda",
        "nativeName":"Luganda"
    },
    "li":{
        "name":"Limburgish, Limburgan, Limburger",
        "nativeName":"Limburgs"
    },
    "ln":{
        "name":"Lingala",
        "nativeName":"Lingála"
    },
    "lo":{
        "name":"Lao",
        "nativeName":"ພາສາລາວ"
    },
    "lt":{
        "name":"Lithuanian",
        "nativeName":"lietuvių kalba"
    },
    "lu":{
        "name":"Luba-Katanga",
        "nativeName":""
    },
    "lv":{
        "name":"Latvian",
        "nativeName":"latviešu valoda"
    },
    "gv":{
        "name":"Manx",
        "nativeName":"Gaelg, Gailck"
    },
    "mk":{
        "name":"Macedonian",
        "nativeName":"македонски јазик"
    },
    "mg":{
        "name":"Malagasy",
        "nativeName":"Malagasy fiteny"
    },
    "ms":{
        "name":"Malay",
        "nativeName":"bahasa Melayu, بهاس ملايو‎"
    },
    "ml":{
        "name":"Malayalam",
        "nativeName":"മലയാളം"
    },
    "mt":{
        "name":"Maltese",
        "nativeName":"Malti"
    },
    "mi":{
        "name":"Māori",
        "nativeName":"te reo Māori"
    },
    "mr":{
        "name":"Marathi (Marāṭhī)",
        "nativeName":"मराठी"
    },
    "mh":{
        "name":"Marshallese",
        "nativeName":"Kajin M̧ajeļ"
    },
    "mn":{
        "name":"Mongolian",
        "nativeName":"монгол"
    },
    "na":{
        "name":"Nauru",
        "nativeName":"Ekakairũ Naoero"
    },
    "nv":{
        "name":"Navajo, Navaho",
        "nativeName":"Diné bizaad, Dinékʼehǰí"
    },
    "nb":{
        "name":"Norwegian Bokmål",
        "nativeName":"Norsk bokmål"
    },
    "nd":{
        "name":"North Ndebele",
        "nativeName":"isiNdebele"
    },
    "ne":{
        "name":"Nepali",
        "nativeName":"नेपाली"
    },
    "ng":{
        "name":"Ndonga",
        "nativeName":"Owambo"
    },
    "nn":{
        "name":"Norwegian Nynorsk",
        "nativeName":"Norsk nynorsk"
    },
    "no":{
        "name":"Norwegian",
        "nativeName":"Norsk"
    },
    "ii":{
        "name":"Nuosu",
        "nativeName":"ꆈꌠ꒿ Nuosuhxop"
    },
    "nr":{
        "name":"South Ndebele",
        "nativeName":"isiNdebele"
    },
    "oc":{
        "name":"Occitan",
        "nativeName":"Occitan"
    },
    "oj":{
        "name":"Ojibwe, Ojibwa",
        "nativeName":"ᐊᓂᔑᓈᐯᒧᐎᓐ"
    },
    "cu":{
        "name":"Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic",
        "nativeName":"ѩзыкъ словѣньскъ"
    },
    "om":{
        "name":"Oromo",
        "nativeName":"Afaan Oromoo"
    },
    "or":{
        "name":"Oriya",
        "nativeName":"ଓଡ଼ିଆ"
    },
    "os":{
        "name":"Ossetian, Ossetic",
        "nativeName":"ирон æвзаг"
    },
    "pa":{
        "name":"Panjabi, Punjabi",
        "nativeName":"ਪੰਜਾਬੀ, پنجابی‎"
    },
    "pi":{
        "name":"Pāli",
        "nativeName":"पाऴि"
    },
    "fa":{
        "name":"Persian",
        "nativeName":"فارسی"
    },
    "pl":{
        "name":"Polish",
        "nativeName":"polski"
    },
    "ps":{
        "name":"Pashto, Pushto",
        "nativeName":"پښتو"
    },
    "pt":{
        "name":"Portuguese",
        "nativeName":"Português"
    },
    "qu":{
        "name":"Quechua",
        "nativeName":"Runa Simi, Kichwa"
    },
    "rm":{
        "name":"Romansh",
        "nativeName":"rumantsch grischun"
    },
    "rn":{
        "name":"Kirundi",
        "nativeName":"kiRundi"
    },
    "ro":{
        "name":"Romanian, Moldavian, Moldovan",
        "nativeName":"română"
    },
    "ru":{
        "name":"Russian",
        "nativeName":"русский язык"
    },
    "sa":{
        "name":"Sanskrit (Saṁskṛta)",
        "nativeName":"संस्कृतम्"
    },
    "sc":{
        "name":"Sardinian",
        "nativeName":"sardu"
    },
    "sd":{
        "name":"Sindhi",
        "nativeName":"सिन्धी, سنڌي، سندھی‎"
    },
    "se":{
        "name":"Northern Sami",
        "nativeName":"Davvisámegiella"
    },
    "sm":{
        "name":"Samoan",
        "nativeName":"gagana faa Samoa"
    },
    "sg":{
        "name":"Sango",
        "nativeName":"yângâ tî sängö"
    },
    "sr":{
        "name":"Serbian",
        "nativeName":"српски језик"
    },
    "gd":{
        "name":"Scottish Gaelic; Gaelic",
        "nativeName":"Gàidhlig"
    },
    "sn":{
        "name":"Shona",
        "nativeName":"chiShona"
    },
    "si":{
        "name":"Sinhala, Sinhalese",
        "nativeName":"සිංහල"
    },
    "sk":{
        "name":"Slovak",
        "nativeName":"slovenčina"
    },
    "sl":{
        "name":"Slovene",
        "nativeName":"slovenščina"
    },
    "so":{
        "name":"Somali",
        "nativeName":"Soomaaliga, af Soomaali"
    },
    "st":{
        "name":"Southern Sotho",
        "nativeName":"Sesotho"
    },
    "es":{
        "name":"Spanish; Castilian",
        "nativeName":"español, castellano"
    },
    "su":{
        "name":"Sundanese",
        "nativeName":"Basa Sunda"
    },
    "sw":{
        "name":"Swahili",
        "nativeName":"Kiswahili"
    },
    "ss":{
        "name":"Swati",
        "nativeName":"SiSwati"
    },
    "sv":{
        "name":"Swedish",
        "nativeName":"svenska"
    },
    "ta":{
        "name":"Tamil",
        "nativeName":"தமிழ்"
    },
    "te":{
        "name":"Telugu",
        "nativeName":"తెలుగు"
    },
    "tg":{
        "name":"Tajik",
        "nativeName":"тоҷикӣ, toğikī, تاجیکی‎"
    },
    "th":{
        "name":"Thai",
        "nativeName":"ไทย"
    },
    "ti":{
        "name":"Tigrinya",
        "nativeName":"ትግርኛ"
    },
    "bo":{
        "name":"Tibetan Standard, Tibetan, Central",
        "nativeName":"བོད་ཡིག"
    },
    "tk":{
        "name":"Turkmen",
        "nativeName":"Türkmen, Түркмен"
    },
    "tl":{
        "name":"Tagalog",
        "nativeName":"Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔"
    },
    "tn":{
        "name":"Tswana",
        "nativeName":"Setswana"
    },
    "to":{
        "name":"Tonga (Tonga Islands)",
        "nativeName":"faka Tonga"
    },
    "tr":{
        "name":"Turkish",
        "nativeName":"Türkçe"
    },
    "ts":{
        "name":"Tsonga",
        "nativeName":"Xitsonga"
    },
    "tt":{
        "name":"Tatar",
        "nativeName":"татарча, tatarça, تاتارچا‎"
    },
    "tw":{
        "name":"Twi",
        "nativeName":"Twi"
    },
    "ty":{
        "name":"Tahitian",
        "nativeName":"Reo Tahiti"
    },
    "ug":{
        "name":"Uighur, Uyghur",
        "nativeName":"Uyƣurqə, ئۇيغۇرچە‎"
    },
    "uk":{
        "name":"Ukrainian",
        "nativeName":"українська"
    },
    "ur":{
        "name":"Urdu",
        "nativeName":"اردو"
    },
    "uz":{
        "name":"Uzbek",
        "nativeName":"zbek, Ўзбек, أۇزبېك‎"
    },
    "ve":{
        "name":"Venda",
        "nativeName":"Tshivenḓa"
    },
    "vi":{
        "name":"Vietnamese",
        "nativeName":"Tiếng Việt"
    },
    "vo":{
        "name":"Volapük",
        "nativeName":"Volapük"
    },
    "wa":{
        "name":"Walloon",
        "nativeName":"Walon"
    },
    "cy":{
        "name":"Welsh",
        "nativeName":"Cymraeg"
    },
    "wo":{
        "name":"Wolof",
        "nativeName":"Wollof"
    },
    "fy":{
        "name":"Western Frisian",
        "nativeName":"Frysk"
    },
    "xh":{
        "name":"Xhosa",
        "nativeName":"isiXhosa"
    },
    "yi":{
        "name":"Yiddish",
        "nativeName":"ייִדיש"
    },
    "yo":{
        "name":"Yoruba",
        "nativeName":"Yorùbá"
    },
    "za":{
        "name":"Zhuang, Chuang",
        "nativeName":"Saɯ cueŋƅ, Saw cuengh"
    }
}';
        $langues = json_decode($isoLangs,true);
        
        $em = $this->getDoctrine()->getManager();

        foreach ($langues as $code =>$langInfos)
        {
//            die($langInfos['nativeName']);
            $langue = new \P4M\CoreBundle\Entity\Lang();
            $langue->setCode($code);
            $langue->setName($langInfos['name']);
            
            
//           die(mb_check_encoding($langInfos['nativeName'], 'UTF-8'));
            $langue->setNativeName($langInfos['nativeName']);
            if (null === $langue->getNativeName())
            {
                $langue->setNativeName($langInfos['name']);
            }
//            $langue->setNativeName=$langInfos['nativeName'];
            $em->persist($langue);
        }
        
        $em->flush();
        
        return new Response('ok');
    
    }
    
    private function hasUnknonwnCharacters($teststring)
    {
        $teststring_broken = utf8_decode($teststring); 
        $teststring_converted = iconv("UTF-8", "UTF-8//IGNORE", $teststring_broken );
        if (strlen($teststring_converted) != strlen($teststring_broken  ))
        {
            return true;
        }
        return false;
    }
    
    public function setRolesAction($userName,$role)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $user = $userRepo->findAll();
        foreach ($users as $user)
        {
            $user->addRole('ROLE_USER');
            $em->persist($user);
            die(print_r($user->getRoles(),true));
        }
        $em->flush();
        
        return new Response('Yeah Baby');
    }
   
}
