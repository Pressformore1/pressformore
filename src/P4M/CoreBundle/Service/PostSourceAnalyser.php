<?php
namespace P4M\CoreBundle\Service;


/**
 * Description of PostSourceAnalyser
 *
 * @author Jona
 */
class PostSourceAnalyser 
{
    const PRESSFORMORE_AUTHOR_KEY_PATTERN = '#pfm-[a-zA-Z0-9]+#';
    
    
    private $source;
    private $DOM;
    private $essence;
    private $sourceContent;
    private $responseHTTPCode;
    private $oEmbedSource;
    private $sourcesMeta;
    
    
    public function __construct($essence)
    {
        $this->essence = $essence;

    }
    
    public function load($source)
    {
        $this->source = $source;
        $this->setSourceContent();
        if ($this->responseHTTPCode == 429 || $this->responseHTTPCode == 301)
        {
            $this->setSourceContentCurl();
        }
        $this->setDomDocument();
        $this->setoEmbedSource();
        return $this->responseHTTPCode;
    }
    
    public function getCanonical()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        if (array_key_exists('canonical', $this->sourcesMeta))
        {
            return $this->sourcesMeta['canonical'];
        }
        
        return null;
    }
    
    private function setDomDocument()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        $this->DOM = new \DOMDocument();
        @$this->DOM->loadHTML($this->sourceContent);
        $this->setSourceMeta();        
    }
    
    private function setSourceMeta()
    {
        $this->sourcesMeta = [];
        $nodesMeta = $this->DOM->getElementsByTagName('meta');
        $i = 0;
        while ($i<$nodesMeta->length)
        {
            $possibleAttr = ['name','property'];
            
            foreach ($possibleAttr as $attr)
            {
                $name=$nodesMeta->item($i)->getAttribute($attr);
            
                if (!array_key_exists($name, $this->sourcesMeta))
                {
                    $this->sourcesMeta[$name] = '';
                }
                
                if ($this->sourcesMeta[$name] == '')
                {
                    $this->sourcesMeta[$name] = htmlspecialchars_decode($nodesMeta->item($i)->getAttribute('content'),ENT_QUOTES);
                }
                else if(is_array($this->sourcesMeta[$name]))
                {
                    $this->sourcesMeta[$name][] = htmlspecialchars_decode($nodesMeta->item($i)->getAttribute('content'),ENT_QUOTES);
                }
                else if ($this->sourcesMeta[$name] !== htmlspecialchars_decode($nodesMeta->item($i)->getAttribute('content'),ENT_QUOTES))
                {
                    $this->sourcesMeta[$name] = [$this->sourcesMeta[$name],htmlspecialchars_decode($nodesMeta->item($i)->getAttribute('content'),ENT_QUOTES)];
                }
            }
            $i++;
        }
        
        
    }
    public function getLanguage()
    {
        $lang = null;
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        
        $pattern = '#lang=\"([a-zA-Z-]*)\"#';
        $get_lang = preg_match_all($pattern,$this->sourceContent,$matches);
        
        
        if (isset($matches[1][0]))
        {
            $langAttr = $matches[1][0];

            if (strstr($langAttr,'-'))
            {
                $exploded = explode('-',$langAttr);
                $lang = $exploded[0];
            }
            else
            {
                $lang = $langAttr;
            }
        }
        return $lang;
    }
    
    public function getTitle()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        $title = null;
        if (null !== $this->oEmbedSource)
        {
            $title = $this->oEmbedSource->title;
        }
        
        if (array_key_exists('og:title', $this->sourcesMeta))
        {
            $title = is_array($this->sourcesMeta['og:title'])?$this->sourcesMeta['og:title'][0]:$this->sourcesMeta['og:title'];
        }
        
        if (array_key_exists('title', $this->sourcesMeta))
        {
            $title = is_array($this->sourcesMeta['title'])?$this->sourcesMeta['title'][0]:$this->sourcesMeta['title'];
        }
        
        $nodes = $this->DOM->getElementsByTagName('title');
        if ($nodes->length)
        {
            $title = $nodes->item(0)->nodeValue;
        }
        
        if (null === $title)
        {
            $title = $this->getCanonical();
    }
    
        if (is_array($title))
        {
            return $title[0];
        }
        
        return $title;
        
    }
    
    public function getDescription()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        $description = null;
        if (null !== $this->oEmbedSource)
        {
            $description =  $this->oEmbedSource->description;
        }
        
        if (array_key_exists('og:description', $this->sourcesMeta))
        {
            $description = is_array($this->sourcesMeta['og:description'])?$this->sourcesMeta['og:description'][0]:$this->sourcesMeta['og:description'];
        }
        if (array_key_exists('description', $this->sourcesMeta))
        {
            $description = is_array($this->sourcesMeta['description'])?$this->sourcesMeta['description'][0]:$this->sourcesMeta['description'];
        }
        
        if ($description === null)
        {
            $paragraphNodes = $this->DOM->getElementsByTagName('p');
                if ($paragraphNodes->length)
                {
//                    die('t '.print_r($paragraphNodes,true));
            $description = htmlspecialchars_decode($paragraphNodes->item(0)->nodeValue,ENT_QUOTES);
                }
                
                
            if (is_array($description))
            {
                return $description[0];
            }
        }
        
        
        return $description;
        
        
    }
    
    
    public function getTags($removePFMKey = true)
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        
        $tags = null;
        if (array_key_exists('keywords',$this->sourcesMeta))
        {
            $tags = is_array($this->sourcesMeta['keywords'])? $this->sourcesMeta['keywords']:explode(',',$this->sourcesMeta['keywords']);
        }
//        if (array_key_exists('keywords',$this->sourcesMeta))
//        {
//            $tags = explode(',',$this->sourcesMeta['keywords']);
//        }
        if (array_key_exists('og:video:tag',$this->sourcesMeta))
        {
            $tags = is_array($this->sourcesMeta['og:video:tag'])?$this->sourcesMeta['og:video:tag']:explode(',',$this->sourcesMeta['og:video:tag']);
        }
        if (array_key_exists('video:tag',$this->sourcesMeta))
        {
            $tags = is_array($this->sourcesMeta['video:tag'])?$this->sourcesMeta['video:tag']:explode(',',$this->sourcesMeta['video:tag']);
        }
        if (array_key_exists('article:tag',$this->sourcesMeta))
        {
            if (is_array($this->sourcesMeta['article:tag']))
            {
                $tags = $this->sourcesMeta['article:tag'];
            }
            else
            {
                $tags = explode(',',$this->sourcesMeta['article:tag']);
            }
            
            
        }
        
        //Vimeo Fucking Dirty Patch
        $vimeoPattern = '#^http://vimeo.com#';
        if (preg_match($vimeoPattern, $this->source))
        {
            $vimeoUlTagClass = 'tag';
            $nodeUl = $this->DOM->getElementsByTagName('ul');
            $i =0;
            while ($i<$nodeUl->length)
            {
                $classes = explode(' ',$nodeUl->item($i)->getAttribute('class'));
                if (array_search($vimeoUlTagClass, $classes) !== false)
                {
                    $links = $nodeUl->item($i)->getElementByTagName('a');
                    $ii = 0;
                    while ($ii<$links->length)
                    {
                        $tags[] = $links->item($ii)->nodeValue;
                        $ii++;
                    }
                }
                $i++;
            }
        }
        
       
        //Soundcloud Fucking Dirty Patch
        $soundcloudPattern = '#^http(s)?://soundcloud.com#';
        if (preg_match($soundcloudPattern, $this->source))
        {
            $soundcloudTagClass = 'sc-tag';
            $nodeA = $this->DOM->getElementsByTagName('a');
            $i =0;
           
            while ($i<$nodeA->length)
            {
                $classes = explode(' ',$nodeA->item($i)->getAttribute('class'));
                if (array_search($soundcloudTagClass, $classes) !== false)
                {
                    $span = $nodeA->item($i)->getElementsByTagName('span');
                    if ($span->length)
                    {
                        $tags[] = $span->nodeValue;
                        
                    }
                }
                $i++;
            }
        }
        
        
       if ($removePFMKey)
        {
            $tags = $this->removePfmKeyFromTags($tags);
        }
        
        $toReturnTags = [];
        if (is_array($tags))
        {
            foreach ($tags as $tag)
            {
                if (!preg_match(self::PRESSFORMORE_AUTHOR_KEY_PATTERN, $tag))
                {
                    if (!in_array(strtoupper($tag),$toReturnTags))
                    {
                        $toReturnTags[] = strtoupper($tag);
                    }
                }
                else
                {
                    $toReturnTags[] = $tag;
                }
            }
            
        }
        else
        {
            $toReturnTags = $tags;
        }
        
        
        
        return $tags;
        
    }
    
    private function removePfmKeyFromTags($tags)
    {
//        dump($tags);
//        die();
        if ($tags === null)
        {
            return null;
        }
        $newTags = [];
            foreach ($tags as $tag)
            {
                if (!preg_match(self::PRESSFORMORE_AUTHOR_KEY_PATTERN, $tag))
                {
                    $newTags[] = $tag;
                }

            }
        
        return $newTags;
    }
    
    
    public function getKey()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        
        if (array_key_exists('pressformore-key', $this->sourcesMeta))
        {
            return $this->sourcesMeta['pressformore-key'];
        }
        else if(array_key_exists('yandex-verification', $this->sourcesMeta) && preg_match(self::PRESSFORMORE_AUTHOR_KEY_PATTERN, $this->sourcesMeta['yandex-verification']))
        {
           return str_replace('pfm-', '',$this->sourcesMeta['yandex-verification']);
        }
        
        
        
        $tags = $this->getTags(false);
        if (is_array($tags))
        {
            foreach ($tags as $tag)
            {
                if (preg_match(self::PRESSFORMORE_AUTHOR_KEY_PATTERN,$tag))
                {
                    return str_replace('pfm-', '', $tag);
                }
            }
        }
        
        
        return null;
    }
    
    public function getType()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        } 
        if (null !== $this->oEmbedSource)
        {
            return $this->oEmbedSource->type;
        }
        
        return null;
    }
    
    public function getPicture()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        
        $image = null;
        
        if (null !== $this->oEmbedSource)
        {
            $image = $this->oEmbedSource->thumbnailUrl;
        }
        
        if (array_key_exists('og:image', $this->sourcesMeta))
        {
            $image = is_array($this->sourcesMeta['og:image'])?$this->sourcesMeta['og:image'][0]:$this->sourcesMeta['og:image'];
        }
        if (array_key_exists('image', $this->sourcesMeta))
        {
            $image = is_array($this->sourcesMeta['image'])?$this->sourcesMeta['image'][0]:$this->sourcesMeta['image'];
        }
        
        
        
        
        
        if ($image === null)
        {
            $nodesLink = $this->DOM->getElementsByTagName('link');
            $i = 0;
            while ($i<$nodesLink->length)
            {
                $name=$nodesLink->item($i)->getAttribute('rel');
                if ($name == 'image_src')
                {
                    if (strlen($nodesLink->item($i)->getAttribute('href')))
                    {
                        $image= htmlspecialchars_decode($nodesLink->item($i)->getAttribute('href'),ENT_QUOTES);
                    }
                    else if (strlen($nodesLink->item($i)->getAttribute('content')))
                    {
                        $image = htmlspecialchars_decode($nodesLink->item($i)->getAttribute('content'),ENT_QUOTES);
                    }
                }
                $i++;
            }
        }
        
        if (null !== $image && $this->validExtension($image))
        {
            if (preg_match('#^/#',$image))
            {
                preg_match('#^(http(s)?://[a-z0-9_.-]+)#', $this->source, $matches);
                $domain = $matches[1];
                
                $image = $domain.$image;
               
            }
            return $image;
        }
        
        return null;
    }
    public function getPictureList()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        
        $list = [];
        $imgNodes = $this->DOM->getElementsByTagName('img');
        $i = 0;
        while ($i<$imgNodes->length)
        {
            $imageSource = $imgNodes->item($i)->getAttribute('src');
            if (!strstr($imageSource,'//'))
            {
                $parsedUrl = parse_url($this->source);
                $imageSource =  $parsedUrl['scheme'].'://'.$parsedUrl['host'].'/'.$imageSource;
            }

            if($this->validExtension($imageSource))
            {

                    $list[] = $imageSource;
            }

            $i++;
        }
        
        return $list;
    }
    
    private function validExtension($fileName)
    {
        
        $pathInfos = pathinfo($fileName);
        $validExtensions = array('jpg','jpeg','JPG','JPEG','gif','png');
        if (isset($pathInfos['extension']) && strstr($pathInfos['extension'], '?'))
        {
            $pathInfos['extension'] = substr($pathInfos['extension'],0,  stripos($pathInfos['extension'], '?'));
        }
        
        if (isset($pathInfos['extension']) && in_array($pathInfos['extension'] ,$validExtensions))
        {
            return true;
        }
        return false;
    }
    
    public function isIframeAllowed()
    {
        $headers = @get_headers($this->source,1);
        
        if (!isset($headers['X-Frame-Options']))
        {
            return true;
        }
        else
        {
            $xframeOptions = $headers['X-Frame-Options'];
            
            if (is_array($xframeOptions))
            {
                foreach ($xframeOptions as $xframeOptions)
                {
                    if (preg_match('#pressformore.com#', $xframeOptions))
                    {
                        return true;
                    }
                }
            }
            else
            {
                if (preg_match('#pressformore.com#', $xframeOptions))
                {
                    return true;
                }
            }
            
        }
        
        return false;
    }
    
    
    public function setoEmbedSource()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        $this->oEmbedSource = $this->essence->embed($this->source);
        
        
    }
    
    public function getEmbed()
    {
        if ($this->responseHTTPCode !== 200)
        {
            return null;
        }
        if ($this->oEmbedSource !== null)
        {
            return $this->securizeEmbed($this->oEmbedSource->html);
        }
        
        
        return null;
    }




    private function securizeEmbed($embedCode)
    {
        if (!preg_match('#^<iframe[^<]+</iframe>$#', $embedCode))
        {
            return null;
        }
        $pfmAttributes = ' id="post_iframe"';
//        if (!preg_match('#src="//player.vimeo.com#', $embedCode)) // Un jour Vimeo acceptera surement les iframes sandboxées
//        {
//            $pfmAttributes.=  'sandbox="allow-scripts" ';
//        }
        
        $embed = substr($embedCode,0,  stripos($embedCode, ' ')).$pfmAttributes.substr($embedCode, stripos($embedCode, ' '));
        
        return $embed;
    }
    
    
    
    
    private function setSourceContent()
    {
        $params = array(
            'http' => array
            (
                'method'=>"GET",
                'user_agent'=>'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20',
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: SIVISITOR=193.190.122.30.1396432319294469; cfid=a8ef482c-2209-4316-90e2-1056b047af24; end_user_id=oeu1402931619791r0.3914028288348912;optimizelyBuckets={\"1210660040\":\"1216840013\"}"
                    ."optimizelyEndUserId=oeu1402931619791r0.3914028288348912; optimizelyPendingLogEvents=[]; optimizelySegments={\"555980330\":\"ff\",\"53627...l\",\"540150687\":\"false\"}"
                    ."; __gfp_64b=bEtnRgoPf6PBDd_VS5logXucSX7dI89stWyFmDa0RQD.J7; __utma=5255399.1662214534.13981...1402931621.1403608500.4; __utmb=5255399.4.10.1403608500; __utmc=5255399; __utmz=5255399.1402931621.3.3.u...=referral|utmcct=/l.php; bucket_map=1210660040:1216840013"
                
                ."\r\n"
                ."Content-Type: text/html; charset=utf-8" 
            )
         );

        $ctx = stream_context_create($params);
        $fp = @fopen($this->source, 'r', false, $ctx);
        
        $return_code = @explode(' ', $http_response_header[0]);
        
//        die(print_r($return_code));
       
        $this->responseHTTPCode = (int)$return_code[1];

        if ($this->responseHTTPCode == 200)
        {

            $content = stream_get_contents($fp);
            //Youtube double utf8 encoded fix
            $pattern = '#^http(s)?://(www.)?youtube.com#';
            $pattern2 = '#^http(s)?://youtu.be#';
//            die($this->source);
            if (preg_match($pattern, $this->source) || preg_match($pattern2, $this->source))
            {
//                die('match');
                $this->sourceContent = utf8_decode($content);
        }
            else
            {
                $this->sourceContent = $content;
            }
        }
         
    }
    private function setSourceContentCurl()
    {
        $params = array(
            'http' => array
            (
                'method'=>"GET",
                'user_agent'=>'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20',
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: SIVISITOR=193.190.122.30.1396432319294469; cfid=a8ef482c-2209-4316-90e2-1056b047af24; end_user_id=oeu1402931619791r0.3914028288348912;optimizelyBuckets={\"1210660040\":\"1216840013\"}"
                    ."optimizelyEndUserId=oeu1402931619791r0.3914028288348912; optimizelyPendingLogEvents=[]; optimizelySegments={\"555980330\":\"ff\",\"53627...l\",\"540150687\":\"false\"}"
                    ."; __gfp_64b=bEtnRgoPf6PBDd_VS5logXucSX7dI89stWyFmDa0RQD.J7; __utma=5255399.1662214534.13981...1402931621.1403608500.4; __utmb=5255399.4.10.1403608500; __utmc=5255399; __utmz=5255399.1402931621.3.3.u...=referral|utmcct=/l.php; bucket_map=1210660040:1216840013"
                
                ."\r\n"
            )
         );

        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$this->source);
        curl_setopt($curl_handle, CURLOPT_HEADER, 1);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        //TOR
        curl_setopt($curl_handle, CURLOPT_PROXY, "127.0.0.1:9050");
        curl_setopt($curl_handle, CURLOPT_PROXYTYPE, 7);
        
        
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20');
        $query = curl_exec($curl_handle);
        
        $httpcode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        
                
        curl_close($curl_handle);
        
//        die('curl '.$query);
        if ($httpcode !== 200)
        {
            $this->responseHTTPCode = $httpcode;
        }
        else
        {
            //Youtube double utf8 encoded fix
            $pattern = '#^http(s)?://(www.)youtube.com#';
            $pattern2 = '#^http(s)?://youtu.be#';
            if (preg_match($pattern, $this->source) || preg_match($pattern2, $this->source))
            {
//                die('youtube');
                $this->sourceContent = utf8_decode($query);
            }
            else
            {
                $this->sourceContent = $query;
            }
         
            
//            $this->sourceContent = utf8_descode($query);
            $this->responseHTTPCode = 200;
        }
        
        
        
       
         
    }
    
    public function getResponseHTTPCode()
    {
        return $this->responseHTTPCode;
    }
}

