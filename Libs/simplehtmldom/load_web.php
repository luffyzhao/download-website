<?php
class load_web{

    private $temp_dir    =   "data/";
    private $images_dir  =   "images/";
    private $css_dir  =   "style/";
    private $js_dir  =   "js/";
    
    private $web_href   =   '';
    private $base_href  =   '';
    
    private $html = null;
    
    private $url    =   '';



    function __construct($url) {
     
        if(!$this->is_url($url)){
            return false;
        }
        
        $this->url  =   $url;
        //获取web_href
        $host    =    parse_url($url);
        $this->web_href    =    $host['scheme'] ."://" . $host['host'] ;       
        
        $this->html =   file_get_html($url);
        
        $this->_set_base();

        $this->_set_dir($host['host']);
    }


    public function start_load($filename = 'index'){
      //css
        $this->_replace_label("link" , "href" , "_load_css" , 1);

        $this->_replace_label("script" , "src" , "_load_js" , 1);

        $this->_replace_label("img" , "src" , "_load_img" , 1);

        $this->_replace_label("a" , "href" , "_load_a" , 1);

        $this->_save_file($filename . ".html", $this->html);


    }

    private function _load_img($url){
        if(!$this->is_url($url)){
            return false;
        }
        //获取文件名
      $filenames = explode("?",basename($url) , 2);

      $filename   = $filenames[0];

      //$img  = $this->_open_url($url);

      $this->_save_file($this->images_dir.$filename,$url , 1);

      return $this->images_dir.$filename;
    }

    private function _load_a($url){      
      return "#";
    }

    private function _load_js($url){
        if(!$this->is_url($url)){
           return false;
       }
        //获取文件名
      $filenames = explode("?",basename($url) , 2);

      $filename   = $filenames[0];

      //$js  = $this->_open_url($url);

      $this->_save_file($this->js_dir.$filename,$url , 1);

      return $this->js_dir.$filename;
    }


    private function _load_css($url){
      if(!$this->is_url($url)){
            return false;
        }


      //获取文件名
      $filenames = explode("?",basename($url) , 2);
      
      $filename   = $filenames[0];
      $filename_ex   = explode(".",$filename); 



      $css  = $this->_open_url($url);
      //if($filename_ex[1] == "css"){
        //echo "<textarea>$css<textarea>";
     // }

      if($filename_ex[1] == "css"){       
         
        //$patterns = '/url\([\"|\']?((.+)[jpg|gif|png][\?]?[\w]{1,30})[\"|\']?\)/U'; //正则根据不同地址需要变换 
        $patterns = '/url\([\"|\']?((.+)[jpg|gif|png][\?]?[^\)]{1,30})[\"|\']?\)/U'; //正则根据不同地址需要变换 
        preg_match_all($patterns, $css, $matches); 
        $imagesUrls = $matches[1]; 

        foreach($imagesUrls as $key=>$image){  
            //修改
           $css = str_replace( $image , "../".  $this->images_dir . basename($image) ,$css);
            //下载
            if( substr($image, 0 , 7) === "http://" ){
                $image_file = $image;
            }
            elseif(substr($image, 0 , 1) == "/"){           
               $image_file    =    $this->web_href . $image;                  
               
            }else{
                $image_file    =    dirname($url) ."/". $image;                                  
            }  
          
            $images = explode("?",basename($image) , 2);      
            $image   = $images[0];            
            $this->_save_file($this->images_dir.$image ,$image_file , 1);  
              
        }
         $this->_save_file($this->css_dir.$filename ,$css); 
        
      }

     return $this->css_dir.$filename;

      
    }


    //设置目录
    private function _set_dir($host){

        $this->temp_dir .= $host . "/";

        if(!is_dir($this->temp_dir)){
          if(!mkdir($this->temp_dir)){
            exit("mkdir is error!");            
          }
        }

        $this->images_dir =  "images/";

        if(!is_dir($this->temp_dir .$this->images_dir)){
          if(!mkdir($this->temp_dir .$this->images_dir)){
            exit("mkdir is error!".$this->images_dir);            
          }
        }

        $this->css_dir =  "style/";

        if(!is_dir($this->temp_dir . $this->css_dir)){
          if(!mkdir($this->temp_dir . $this->css_dir)){
            exit("mkdir is error!".$this->css_dir);            
          }
        }

        $this->js_dir =  "js/";

        if(!is_dir($this->temp_dir .$this->js_dir)){
          if(!mkdir($this->temp_dir .$this->js_dir)){
            exit("mkdir is error!".$this->js_dir);            
          }
        }

    }

    private function _set_base(){

      if($base =  $this->_replace_label('base', 'href')){        
        
        if(substr($base, -1 , 1) === "/"){
            $this->base_href = $base;
        }else
           $this->base_href = $base."/";  
                
      }else
        $this->base_href = $this->web_href."/";  
      
      //echo $this->base_href;
    }
    


    //节点获取
    private function _replace_label($node , $attribute ,  $callback = false , $replace = false){
        $return   = '';
        foreach($this->html->find($node) as $e){

          if(empty($e->$attribute)){
            continue; ;
          }
          //如果 是url链接
          if( substr($e->$attribute, 0 , 7) === "http://" ){
              $return = $e->$attribute;     
              
          }
          //如果是 / 开头
          elseif( substr($e->$attribute, 0 , 1) === "/" )
          {           
            $return = $this->web_href . $e->$attribute;
          }
          else
          {
            $return = $this->base_href . $e->$attribute;
          }

          //如果要保存
          if($callback){
              $filename   = $this->$callback($return);

              if($filename && $replace){
                $e->$attribute =   $filename;      
              }
          }         
        }
        if($node == "base")
                  return $return;
    }



    /**
    * 获取url 内容    *
    */
    private function _open_url($url){

      if(!$this->is_url($url))
          return false;
      
      $content =  @file_get_contents($url);

      if($content === false){
        return false;        
      }

      return $content;

    }


    /**
    * 写入文件    *
    */
    private function _save_file($filename , $data , $is_file = false){
        
        if(file_exists($this->temp_dir.$filename)){
            return false;
        }        
        if($is_file){
          $data = $this->_open_url($data);
        }
        
        file_put_contents($this->temp_dir.$filename, $data);
        
    }
    /**
    * 判断是否为url    *
    */
    private function is_url($str){
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
    }


    public function get_temp_dir(){
      return $this->temp_dir;
    }
    public function myscandir($pathname){
        $filelist = array();
        foreach( glob($pathname."*.html") as $filename ){
            if(is_file($filename)){
                $filelist[] = basename($filename);
            }
        }
        return $filelist;
    }
}