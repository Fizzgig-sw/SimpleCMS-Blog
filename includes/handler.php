<?php
class page_content{
    public $page_content=[
        'id'=>0,
        'title'=>'new_entry',
        'content'=>'',
        'list'=>0,
        'type'=>0,
        'tags'=>'',
    ];
    public function element($key=''){
        return isset($this->page_content[$key]) ? $this->page_content[$key] : null;
    }
    public static function get_content($result='', $i=0){
        $document=new page_content();
        switch ($result==''){
            case true:
                if(isset($_POST['id'])){$document->page_content['id']=$_POST['id'];}
                if($document->page_content['id']==0){$document->page_content['id']=time();}

                if(isset($_POST['title'])){$document->page_content['title']=htmlspecialchars($_POST['title']);}//$GLOBALS['page'];//htmlspecialchars($_GET['page']);
                if(isset($_POST['textarea'])){$document->page_content['content']=htmlspecialchars($_POST['textarea']);}
                if(isset($_POST['list'])){$document->page_content['list']=$_POST['list'];}
                $document->page_content['type'] = (isset($_POST['home']) ? 1:0) + (isset($_POST['nav']) ? 2:0);
                
                //if(isset($_POST['tags'])){$document->page_content['tags']=htmlspecialchars($_POST['tags']);}
                if(isset($_POST['tags'])){$document->page_content['tags']=tags::get_tags()->to_string();}
                
                break;
            case false:
                $document->page_content['id']= DBresult($result,$i,'id');
                $document->page_content['title']= DBresult($result,$i,'title');
                $document->page_content['content']= DBresult($result,$i,'content');
                $document->page_content['list']= DBresult($result,$i,'list');
                $document->page_content['type']= DBresult($result,$i,'type');

                $document->page_content['tags']=tags::get_tags($document->page_content['id'])->to_string();
                break;
        }
        return $document;
    }
}
class tags{
    public $tags=[];
    public $post_id='';
    
    public function to_string(){
        $result='';
        foreach($this->tags as $tag){$result.=$tag.', ';}
        return trim($result, ", ");
    }
    public static function get_tags($post_id=0){
        $tags = new tags();
        switch($post_id==0){
            case true:
                //from post
                $i=0;
                //$split=;
                foreach(explode(',',htmlspecialchars($_POST['tags'])) as $tag){
                    $tags->tags[$i]=trim($tag);
                    $i++;
                }


                break;
            case false:
                //db lookup
                $query="SELECT * FROM ".table('tags')." WHERE post_id = '".$post_id."' ORDER BY tag";
                $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
                for($i=0;$i<$rows;$i++){
                    $tags->tags[$i]=DBresult($result,$i,'tag');
                }
                $tags->post_id=$post_id;
                break;
        }
        return $tags;
    }
}
//Global document
$document=new page_content();

//GET
$tag = isset($_GET['tag']) ? htmlspecialchars($_GET['tag']) : '';
$page = isset($_GET['tag']) ? 'tag' : (isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home');
$edit = (isset($_GET['edit'])?$_GET['edit']==EDIT_PIN:false);
$preview = false;

if(($page=='home'||$page=='')&&$edit==true){header('Location: '.site_url());}

//POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch($_POST['formid']){
        case 'editor':
            switch($_POST['submit']){
                case 'Save': case 'Save and edit':
                    $document=page_content::get_content();
                    
                    //Get list number
                    $query = "SELECT COUNT(*) FROM ".table('page_content');
                    $list=DBresult(DBquery($GLOBALS['link'],$query),0)+1;
                    $query="SELECT * FROM ".table('page_content')." WHERE list = ".$list." LIMIT 1";
                    $rows = DBnum_rows(DBquery($GLOBALS['link'], $query));
                    while ($rows>0){
                        $list++;
                        $rows = DBnum_rows(DBquery($GLOBALS['link'], $query));
                    }

                    $update = "UPDATE ".table('page_content')." SET content = '".$document->element('content')."', type = ".$document->element('type')." WHERE title = '".$document->element('title')."'";
                    $insert = "INSERT INTO ".table('page_content')." VALUES ( ".$document->element('id').", '".$document->element('title')."', '".$document->element('content')."', ".$list.", ".$document->element('type').")";
                    
                    $query="SELECT * FROM ".table('page_content')." WHERE title = '".$document->element('title')."' LIMIT 1";
                    $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
                    DBquery($GLOBALS['link'], ($rows == 1 ? $update : $insert));
                    
                    foreach(tags::get_tags()->tags as $tag){
                        if($tag != ''){
                            if($tag[0]=='-'){
                                $query="DELETE FROM ".table('tags')." WHERE tag = '".trim($tag,'-')."' AND post_id = ".$document->element('id');
                                DBquery($GLOBALS['link'], $query);
                            }
                            else{
                                $insert = "INSERT INTO ".table('tags')." VALUES ( '".$tag."', ".$document->element('id').")";
                                
                                $query="SELECT * FROM ".table('tags')." WHERE tag = '".$tag."' AND post_id = ".$document->element('id')." LIMIT 1";
                                $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
                                if($rows==0){DBquery($GLOBALS['link'], $insert);}
                            }
                        }
                    }

                    header('Location: '.site_link($page).(strpos($_POST['submit'],'edit') === false ? '' : edit_link()));
                    break;
                case 'Preview':
                    $preview=true;
                    break;
                case 'Cancel':
                    $query="SELECT * FROM ".table('page_content')." WHERE title = '".$page."' LIMIT 1";
                    $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
                    
                    header('Location: '.($rows==1 ? site_link($page) : site_url()));
                    break;
                case 'Delete':
                    $query="DELETE FROM ".table('page_content')." WHERE title = '".$page."'"; DBquery($GLOBALS['link'], $query);
                    $query="DELETE FROM ".table('tags')." WHERE post_id = ".$_POST['id']; DBquery($GLOBALS['link'], $query);
                    //$_POST['id'];
                    
                    header('Location: '.site_url());
                    break;
            }
            break;
        case 'sitemap':
            $document=page_content::get_content();
            if(isset($_POST['move'])){
                $query=$GLOBALS['lookup'][$_POST['lookup']]['query'];
                $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
                
                $i=intval($_POST['index']);
                
                $a_id=intval(DBresult($result,$i,'id'));
                $a_list=intval(DBresult($result,$i,'list'));
                
                $b_id=intval(DBresult($result,($_POST['move']==$_POST['up_value']?$i-1:$i+1),'id'));
                $b_list=intval(DBresult($result,($_POST['move']==$_POST['up_value']?$i-1:$i+1),'list'));
                
                $query= "UPDATE ".table('page_content')." SET list = ".$b_list." WHERE id = '".$a_id."'"; DBquery($GLOBALS['link'], $query);
                $query= "UPDATE ".table('page_content')." SET list = ".$a_list." WHERE id = '".$b_id."'"; DBquery($GLOBALS['link'], $query);
            }
            else{
                $update = "UPDATE ".table('page_content')." SET type = ".$document->element('type')." WHERE id = '".$document->element('id')."'";
                DBquery($GLOBALS['link'], $update);
            }
            header('Location: '.site_link($page).edit_link());
            break;
        case 'delete_tag':
            $query="DELETE FROM ".table('tags')." WHERE tag = '".$_POST['submit']."'"; DBquery($GLOBALS['link'], $query);
            header('Location: '.site_link($page).edit_link());
            break;
        case 'settings':
            foreach($settings as $key => $setting){
                $value = ($setting['type']==0 ? htmlspecialchars($_POST[$key]) : ($setting['type']==1 ? (isset($_POST[$key]) ? 'true' : 'false') : version));
                $update = "UPDATE ".table('settings')." SET type = ".$setting['type'].", value = '".$value."' WHERE var = '".$key."'";
                $insert = "INSERT INTO ".table('settings')." VALUES ( '".$key."', ".$setting['type'].", '".$value."')";

                $query="SELECT * FROM ".table('settings')." WHERE var = '".$key."' LIMIT 1";
                $rows = DBnum_rows(DBquery($GLOBALS['link'], $query));
                DBquery($GLOBALS['link'], ($rows == 1 ? $update : $insert));
            }

            header('Location: '.($GLOBALS['setup']==false ? site_link('settings').edit_link() : site_link('docs').('&edit'.(EDIT_PIN!='' ? '='.EDIT_PIN : '')) ));
            break;    
        }
}
?>