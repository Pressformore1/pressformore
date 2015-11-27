<?php

namespace P4M\CoreBundle\Twig;

use \P4M\CoreBundle\Entity\Wall;
use \P4M\CoreBundle\Entity\Post;
use \P4M\UserBundle\Entity\User;

class CoreExtension extends \Twig_Extension
{
    private $salt;
    private $container;
    public function __construct($salt,$em,$container)
    {
//        parent::__construct();
        $this->salt=$salt;
        $this->em = $em;
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'isTrustedSource' => new \Twig_Filter_Method($this, 'isTrustedSource'),
            'followUser' => new \Twig_Filter_Method($this, 'followUser'),
            'sha512' => new \Twig_Filter_Method($this, 'sha512'),
            'domain' => new \Twig_Filter_Method($this, 'domain'),
            'percentVote' => new \Twig_Filter_Method($this, 'percentVote'),
            'commentVoteScore' => new \Twig_Filter_Method($this, 'commentVoteScore'),
            'postVoteScore' => new \Twig_Filter_Method($this, 'postVoteScore'),
            'commentedPost' => new \Twig_Filter_Method($this, 'commentedPost'),
            'newPostCountByWall' => new \Twig_Filter_Method($this, 'newPostCountByWall'),
            'isToReadLater' => new \Twig_Filter_Method($this, 'isToReadLater'),
            'scoreRatio' => new \Twig_Filter_Method($this, 'scoreRatio'),
            'getClass' => new \Twig_Filter_Method($this, 'getClass'),
            'filterIframeUrl' => new \Twig_Filter_Method($this, 'filterIframeUrl'),
            'evaluate' => new \Twig_Filter_Method( $this, 'evaluate', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true
                )
            )),
            'pressformClass' => new \Twig_Filter_Method($this, 'pressformClass'),
            ''
        );
    }
    
    public function getFunctions()
    {
        return array(
            'getTopScore' => new \Twig_Function_Method($this, 'getTopScore'),
            'getUnreadNotificationNumber' => new \Twig_Function_Method($this, 'getUnreadNotificationNumber'),
            'getLanguages' => new \Twig_Function_Method($this, 'getLanguages'),
            'getWallsCategories' => new \Twig_Function_Method($this, 'getWallsCategories'),
            'getPostAction' => new \Twig_Function_Method($this, 'getPostAction'),
            'getWallAction' => new \Twig_Function_Method($this, 'getWallAction'),
            'isMobile' => new \Twig_Function_Method($this, 'isMobile'),
        );
    }

    
    public function isMobile()
    {
        $mobilePattern = '/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte|AppleWebKit\/53[01234])/i'; 
        if(preg_match($mobilePattern, $this->container->get('request')->headers->get('user-agent')))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function isTrustedSource($soucrceUrl)
    {
        $trustedRepo = $this->em->getRepository('P4MCoreBundle:TrustedSource');
        $parse = parse_url($soucrceUrl);
        $domain =  $parse['host'];
        
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $domain = $regs['domain'];
          }
          
//          die($domain);
        $trustedSource = $trustedRepo->findOneBy(['domain'=>$domain]);
        
        if (null === $trustedSource)
        {
            return false;
        }
        else
        {
            return true;
        }
        
                
    }

    public function followUser(User $follower = null,User $followed=null)
    {
        $follow = false;
        if (null === $follower || null === $followed)
        {
            return $follow;
        }
        
        foreach ($follower->getFollowing() as $link)
        {
            if ($link->getFollowing() === $followed)
            {
                $follow = true;
            }
        }
        
        return $follow;
    }
    
    /**
     * This function will evaluate $string through the $environment, and return its results.
     * 
     * @param array $context
     * @param string $string 
     */
    public function evaluate( \Twig_Environment $environment, $context, $string ) {
        $loader = $environment->getLoader( );

        $parsed = $this->parseString( $environment, $context, $string );

        $environment->setLoader( $loader );
        return $parsed;
    }

    /**
     * Sets the parser for the environment to Twig_Loader_String, and parsed the string $string.
     * 
     * @param \Twig_Environment $environment
     * @param array $context
     * @param string $string
     * @return string 
     */
    protected function parseString( \Twig_Environment $environment, $context, $string ) {
        $environment->setLoader( new \Twig_Loader_String( ) );
        return $environment->render( $string, $context );
    }

    
    public function sha512($input)
    {
        return hash('sha512', $input.$this->salt);
    }
    public function getClass($entity,$absolute = false)
    {
        if ($absolute)
        {
            return get_class($entity);
        }
        else
        {
            return substr(get_class($entity),  strrpos(get_class($entity), '\\' ) +1 );
        }
        
    }
    
    public function filterIframeUrl($url)
    {
        if (preg_match("#^https?://(?:www\.)?youtube.com#", $url))
        {
                preg_match('/(?<=v=)[^&]+/', $url, $vid_id);
                $source = 'http://www.youtube.com/embed/'.$vid_id[0].'?wmode=transparent';
        }
        else if (preg_match("#^https?://(?:www\.)?vimeo.com#", $url))
        {
                preg_match('vimeo\.com\/([0-9]{1,10})', $url, $vid_id);
                $source = 'http://player.vimeo.com/video/'.$vid_id[0].'?wmode=transparent';
        }
        else if (preg_match("#^https?://(?:www\.)?dailymotion.com#", $url))
        {
            $vid_id =  strtok(basename($url), '_');
            $source = '//www.dailymotion.com/embed/video/'.$vid_id.'?wmode=transparent';
        }
        else
        {
            $source = $url;
        }
        
        return $source;
    }
    public function pressformClass(Post $post, User $user)
    {
        
        foreach ($post->getPressforms() as $pressform)
        {
            if ($pressform->getSender() === $user)
            {
                return 'icon-logo pfm-red';
            }
        }
        if ($post->getAuthor() !== null)
        {
            return 'icon-logo pfm-grey';
        }
        
        return '';
        
        //Obsolete
//        $pressformRepo = $this->em->getRepository('P4MCoreBundle:Pressform');
//        $pressform = $pressformRepo->findOneBy(['sender'=>$user,'post'=>$post]);
//        if( null !== $pressform)
//        {
//            return 'icon-logo pfm-red';
//        }
//        elseif ($post->getAuthor() !== null)
//        {
//            return 'icon-logo pfm-grey';
//        }
//        
//        return '';
    }
    
    public function domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
          return $regs['domain'];
        }
        return false;
    }
    
    public function percentVote($ammount, $total)
    {
        if ($ammount == 0 && $total==0)
        {
            return 50;
        }
        else if($ammount == 0)
        {
            return 0;
        }
        else
        {
            return ($ammount*100)/$total;
        }   
    }
    
    public function getName()
    {
        return 'p4m_extension';
    }
    
    public function getWallsCategories($walls)
    {
//        die('getWallsCategories');
        $categories = array();
        
        $count = 0;
        foreach ($walls as $wall)
        {
            if ($wall instanceof Wall)
            {
                $includedCats = $wall->getIncludedCategories();
                foreach ($includedCats as $categorie)
                {
                   $count ++; 
                    if (!in_array($categorie,$categories))
                    {
                        $categories[] = $categorie;
                    }
                }
                   
            }
            
        }
//        die($count);
        
        return $categories;
        
    }
    public function getLanguages(array $posts)
    {
        $languages = array();
        
        foreach ($posts as $post)
        {
            if ($post instanceof Post)
            {
                   if (!in_array($post->getLang(),$languages,true) && null !== $post->getLang())
                   {
                       $languages[] = $post->getLang();
                   }
            }
            
        }
        
        return $languages;
        
    }
    public function commentVoteScore($userId,$commentId)
    {
        $voteRepo = $this->em->getRepository('P4MCoreBundle:Vote');
        
        $vote = $voteRepo->findOneBy(array('comment'=>$commentId,'user'=>$userId));
        
        if (null !== $vote)
        {
            return $vote->getScore();
        }
        return 0;
        
    }
    
    public function postVoteScore($userId,$postId)
    {
        $voteRepo = $this->em->getRepository('P4MCoreBundle:Vote');
        
        $vote = $voteRepo->findOneBy(array('post'=>$postId,'user'=>$userId));
        
        if (null !== $vote)
        {
            return $vote->getScore();
        }
        return 0;
        
    }
    public function commentedPost($userId,$postId)
    {
        $commentRepo = $this->em->getRepository('P4MCoreBundle:Comment');
        
        $comment = $commentRepo->findOneBy(array('post'=>$postId,'user'=>$userId));
        
        if (null === $comment)
        {
            return false;
        }
        return true;
        
    }
    
    
    public function newPostCountByWall(User $user,Wall $wall)
    {
        $wallViewRepo = $this->em->getRepository('P4MTrackingBundle:WallView');
        $wallView=$wallViewRepo->findOneBy(['user'=>$user,'wall'=>$wall],['date'=>'DESC']);
        
        if ($wallView !== null)
        {
            $lastDate = $wallView->getDate();
//            return ($lastDate->format('d-m'));
        }
        else
        {
            $lastDate = new \DateTime();
            $lastDate->modify('-1 year');
//            $lastDate = $lastDate->modify('-1 year');
        }
        
        
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
        
        
        $postData['categories'] = $wall->getIncludedCatsId();
        $postData['tags'] = $wall->getIncludedTagsId();
        $postData['excludedCategories']=$wall->getExcludedCatsId();
        $postData['excludedTags']=$wall->getExcludedTagsId();
        $postData['afterDate'] = $lastDate->getTimestamp();
        
        
        $searchResult = $repository->findCustom(null,$postData,0);
        $newPostCount = count($searchResult['entities']);

        
        if ($newPostCount>20)
        {
            return '+20';
        }
        else 
        {
            return $newPostCount;
        }
        
    }
   
    public function isToReadLater(Post $post,User $user)
    {
        foreach ($post->getReadLater() as $readLater)
        {
            if ($readLater->getUser() === $user)
            {
                return true;
            }
        }
        
        return false;
            
        
//        Uncomment to charge Mysql
//        $rlRepo = $this->em->getRepository('P4MBackofficeBundle:ReadPostLater');
//        $rl = $rlRepo->findOneBy(array('user' => $user, 'post' => $post));
//        
//        
//        
//        
//        if (null === $rl)
//        {
//            return false;
//        }
//        return true;
        
    }
    
    public function scoreRatio(Post $post,User $user)
    {
        
        $rlRepo = $this->em->getRepository('P4MBackofficeBundle:ReadPostLater');
        $rl = $rlRepo->findOneBy(array('user' => $user, 'post' => $post));
        
        
        
        
        if (null === $rl)
        {
            return false;
        }
        return true;
        
    }
    
    
    
    /*
     * 
     * TWIG FUNCTION
     * 
     */
    
    public function getTopScore()
    {
//        die('$score');
        $scoreRepo = $this->em->getRepository('P4MPinocchioBundle:PostScore');
        $score = $scoreRepo->findBestScore();
        
        
        return intval($score);
        
    }
    public function getUnreadNotificationNumber($user)
    {
//        die('$score');
        $notificationRepo = $this->em->getRepository('P4MNotificationBundle:Notification');
        $number = $notificationRepo->findUnreadNumber($user);
        
        
        return intval($number);
        
    }
    
    public function getPostAction($post,User $user)
    {
        $activityRepo = $this->em->getRepository('P4MTrackingBundle:UserActivity');
        $activities = $activityRepo->findBy(['user'=>$user,'post'=>$post]);
        $types = [];
        if ($activities !== null)
        {
            foreach ($activities as $activity)
            {
                $types[] = $activity->getType();
            }
        }
        
        return $types;
        
        
    }
    public function getWallAction($wall,User $user)
    {
        $activityRepo = $this->em->getRepository('P4MTrackingBundle:UserActivity');
        $activities = $activityRepo->findBy(['user'=>$user,'wall'=>$wall]);
        $types = [];
        if ($activities !== null)
        {
            foreach ($activities as $activity)
            {
                $types[] = $activity->getType();
            }
        }
        
        return $types;
        
        
    }
}
