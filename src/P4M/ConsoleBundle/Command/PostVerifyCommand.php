<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostVerifyCommand extends ContainerAwareCommand{

    protected function configure()
    {
        $this->setName('post:verify:url')
            ->setDescription("Vérifie les url de la liste (prend du temps)");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')->findAll();
        $c = $em->getRepository('P4MCoreBundle:Post')->countPosts();
        $i=0;
        $r = 0;
        $d = 0;
        $progress_bar = new ProgressBar($output, $c);
        $progress_bar->start();
        foreach ($posts as $post){
            $url = $post->getSourceUrl();
            //$output->writeln($post->getId());
            $code = $this->get_http_response_code($url);
//            $output->writeln($code);
            if($code == 301 or $code == 302){
                $new_url = $this->get_final_url($url);
//                $output->writeln('1: '.$url);
//                $output->writeln("2: ".$new_url);
                $test_new_url = $em->getRepository('P4MCoreBundle:Post')->findOneBySourceUrl($new_url);
                if(null == $test_new_url){
                    if(is_array($new_url)){
                        if (strlen($new_url[0]) > 255){
                            $new_url = substr($new_url[0], 0, 255);
                        }
                        $post->setSourceUrl($new_url);
                    }else{
                        if (strlen($new_url) > 255){
                            $new_url = substr($new_url, 0, 255);
                        }
                        $post->setSourceUrl($new_url);
                    }
                    $em->persist($post);
                    $r++;
                    $i++;
                }
                else{
                    $em->remove($post);
                    $d++;
                    $i++;
                }

            }elseif ($code == 404 or $code == 410 ){
                $em->remove($post);
                $d++;
                $i++;
            }
            $progress_bar->advance();
            $em->flush();
        }
        $progress_bar->finish();
        $output->writeln($i . ' posts ont été mis à jours'
            .$r.' posts ont été rediriger'
            .$d.' posts ont été supprimer');

    }
    /**
     * get_redirect_url()
     * Gets the address that the provided URL redirects to,
     * or FALSE if there's no redirect.
     *
     * @param string $url
     * @return string
     */
    protected function get_redirect_url($url){
        $redirect_url = null;

        $url_parts = @parse_url($url);
        if (!$url_parts) return false;
        if (!isset($url_parts['host'])) return false; //can't process relative URLs
        if (!isset($url_parts['path'])) $url_parts['path'] = '/';

        $sock = @fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 10);
        if (!$sock) return false;

        $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
        $request .= 'Host: ' . $url_parts['host'] . "\r\n";
        $request .= "Connection: Close\r\n\r\n";
        fwrite($sock, $request);
        $response = '';
        while(!feof($sock)) $response .= fread($sock, 8192);
        fclose($sock);

        if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
            if ( substr($matches[1], 0, 1) == "/" )
                return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
            else
                return trim($matches[1]);

        } else {
            return false;
        }

    }

    /**
     * get_all_redirects()
     * Follows and collects all redirects, in order, for the given URL.
     *
     * @param string $url
     * @return array
     */
    protected function get_all_redirects($url){
        $redirects = array();
        while ($newurl = $this->get_redirect_url($url)){
            if (in_array($newurl, $redirects)){
                break;
            }
            $redirects[] = $newurl;
            $url = $newurl;
        }
        return $redirects;
    }

    /**
     * get_final_url()
     * Gets the address that the URL ultimately leads to.
     * Returns $url itself if it isn't a redirect.
     *
     * @param string $url
     * @return string
     */
    protected function get_final_url($url){
        $redirects = $this->get_all_redirects($url);
        if (count($redirects)>0){
            return array_pop($redirects);
        } else {
            return $url;
        }
    }
    protected function get_http_response_code($theURL) {
        $headers = @get_headers($theURL);
        return substr($headers[0], 9, 3);
    }
}