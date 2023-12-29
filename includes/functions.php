<?php
require_once('includes/parsedown-1.7.4/Parsedown.php');
require_once('includes/parsedown-extra-0.8.1/ParsedownExtra.php');

$markdown=new ParsedownExtra();
$setup=false;
$uarr="&#8593";//8657";//8679";
$darr="&#8595";//8681";

$lookup=[
    ['category'=>"Menu:", 'type'=>2,
        'query'=>"SELECT * FROM ".table('page_content')." WHERE (type & 2) != 0 ORDER BY list ASC"],
    
    ['category'=>"Featured:", 'type'=>1,
        'query'=>"SELECT * FROM ".table('page_content')." WHERE (type & 1) != 0 ORDER BY list DESC"],
    
    ['category'=>"Other:", 'type'=>0,
        'query'=>"SELECT * FROM ".table('page_content')." WHERE type = 0 ORDER BY id DESC"],
];

$settings = [
    //Types: 0=string 1=boolean 2=system control (hidden in the list)
    'name'       => ['type'=>0, 'description'=>'', 'placeholder'=>'Sample'],
    'credit'     => ['type'=>0, 'description'=>'', 'placeholder'=>'Example.com'],
    'site_url'   => ['type'=>0, 'description'=>'', 'placeholder'=>'https://example.com'],
    'pretty_uri' => ['type'=>1, 'description'=>'', 'placeholder'=>''],
    'nav_menu'   => ['type'=>1, 'description'=>'', 'placeholder'=>''],
    'home_link'  => ['type'=>0, 'description'=>'', 'placeholder'=>'Home'],
    'page_link'  => ['type'=>0, 'description'=>'', 'placeholder'=>'Read more...'],
    'home_date'  => ['type'=>0, 'description'=>'(Disabled if blank)', 'placeholder'=>'M Y'],
    'page_date'  => ['type'=>0, 'description'=>'(Disabled if blank)', 'placeholder'=>'F j, Y, g:i a'],
    'footer'     => ['type'=>1, 'description'=>'', 'placeholder'=>''],
    'version'    => ['type'=>2, 'description'=>'', 'placeholder'=>''],
];

//Create Tables
function create_tables($link){
    $query="CREATE TABLE IF NOT EXISTS ".table('settings')." (var varchar(20), type int(1), value varchar(64))"; DBquery($link,$query);
    $query="CREATE TABLE IF NOT EXISTS ".table('page_content')." (id int(10) PRIMARY KEY UNIQUE, title varchar(64), content blob, list int(1), type int(1))"; DBquery($link,$query);
    $query="CREATE TABLE IF NOT EXISTS ".table('tags')." (tag varchar(64), post_id int(10))"; DBquery($link,$query);
}

function config($key = ''){
    if($key==''){
        $query="SELECT * FROM ".table('settings')." WHERE var = 'version' LIMIT 1";
        $rows = DBnum_rows(DBquery($GLOBALS['link'], $query));
        if($rows==0){$GLOBALS['setup']=true;$GLOBALS['edit']=true;}
    }
    else{
        $query="SELECT * FROM ".table('settings')." WHERE var = '".$key."' LIMIT 1";
        $result=DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
        
        $value=null;
        if($rows==1){
            $type=DBresult($result,0,'type');
            switch($type){
            case 0:$value=DBresult($result,0,'value'); break;
            case 1:$value=(DBresult($result,0,'value')=='true'?true:false); break;
            }
        }
        return $value;
    }
}

// Displays config values.
function site_name(){return config('name');}
function site_url(){return config('site_url');}
function site_credit(){return config('credit');}
function site_link($uri){return config('site_url').'/'.(config('pretty_uri') || $uri == '' ? '' : '?page=').$uri;}
function edit_link(){return ($GLOBALS['edit'] ? ('&edit'.(EDIT_PIN!='' ? '='.EDIT_PIN : '')) : '');}

//Website navigation.
function nav_menu($sep = ' | ') {
    if(config('nav_menu')){
        $nav_menu = nav_link('',config('home_link')).$sep;
        
        $query=$GLOBALS['lookup'][0]['query'];//"SELECT * FROM ".table('page_content')." WHERE (type & 2) != 0 ORDER BY id";
        $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
        for($i=0;$i<$rows;$i++){
            $uri=DBresult($result,$i,'title');
            $nav_menu .= nav_link($uri,page_title($uri)).$sep;
        }

        echo '<nav class="menu">'.trim($nav_menu, $sep).'</nav>';
    }
}

function nav_link($uri, $name){
    $class = str_replace('page=', '', $_SERVER['QUERY_STRING']) == $uri ? ' active' : '';
    $url = site_link($uri);
    return '<a href="'.$url.'" title="'.$name.'" class="item '.$class.'">'.$name.'</a>';
}

function tag_list($page_id){
    if($GLOBALS['edit']==false){
        $query = "SELECT DISTINCT tag FROM ".table('tags')." WHERE post_id = ".$page_id." ORDER BY tag";
        $result=DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
        if($rows!=0){
            for($i=0;$i<$rows;$i++){
                $t=DBresult($result,$i,'tag');
                echo '<a href="'.site_link('&tag='.$t).'"><button class="tag">'.$t.'</button></a> ';
            }
        }
    }
}

//Displays page title.
function page_title($page = '') {
    $page = ($page != '' ? htmlspecialchars($page) : $GLOBALS['page']);
    return ucwords(str_replace('-', ' ', $page));
}

//Displays page content.
function page_content($page = '') {
    $document = $GLOBALS['document'];
    $date = ($page != '' ? config('home_date') : config('page_date'));
    $page = ($page != '' ? htmlspecialchars($page) : $GLOBALS['page']);

    $query="SELECT * FROM ".table('page_content')." WHERE title = '".$page."' LIMIT 1";
    $result = DBquery($GLOBALS['link'], $query);

    if(DBnum_rows($result)==1){$document=page_content::get_content($result);}
    
    switch($GLOBALS['edit']){
        case false:
            if($document->page_content['content']==''){include(getcwd().'/content/404.php'); }
            else{
                $date = (($document->page_content['type']&2)==2?'':$date);
                echo ($date!=''?'<p>'.date($date, $document->page_content['id']).'</p>':'');
                echo $GLOBALS['markdown']->text($document->page_content['content']);
            }
            break;
        case true:
            if($GLOBALS['preview']){$document=page_content::get_content();}
            include(getcwd().'/content/edit.php');
            break;
    }
    $GLOBALS['document']=$document;
}

function get_article(){
    if($GLOBALS['setup']){$GLOBALS['page']='settings';$GLOBALS['edit']=true;}
    
    switch(strtolower($GLOBALS['page'])){
        case 'docs': article_docs(); break;
        case 'home': article_home(); break;
        case 'markdown': article_markdown(); break;        
        case 'settings': article_settings(); break;
        case 'sitemap': article_sitemap(); break;        
        case 'tag': case 'tags': article_tags(); break;
        default:
            echo '<article>';    
            echo '<h2>'.page_title().'</h2>';
            page_content(); tag_list($GLOBALS['document']->page_content['id']);
            echo '</article>';
            break;
    }
}

function article_docs(){
    echo '<article>';    
    echo '<h2>'.page_title('docs').'</h2>';
    if($GLOBALS['edit']==false){include(getcwd().'/content/403.php');}
    else{echo $GLOBALS['markdown']->text(file_get_contents(getcwd().'/README.md'));}
    echo '</article>';    
}

function article_home(){
    $GLOBALS['edit']=false;
            
    $query=$GLOBALS['lookup'][1]['query'];//"SELECT * FROM ".table('page_content')." WHERE (type & 1) != 0 ORDER BY id DESC";
    $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
    if($rows==0){echo '<article><h2>Empty</h2>This is a blank page.<br><br></article>';}
    for($i=0;$i<$rows;$i++){
        echo '<article>';    
        $uri=DBresult($result,$i,'title'); $page_id=DBresult($result,$i,'id');
        echo '<h2>'.page_title($uri).'</h2>'; page_content($uri); tag_list($page_id);
        echo '<div style="text-align:right;"><a href="'.site_link($uri).'">'.config('page_link').'</a></div>';
        echo '</article>';
    }
}
function article_markdown(){
    $page='markdown-manual';
    echo '<article>';    
    echo '<h2>'.page_title('markdown-manual').'</h2>';
    include(getcwd().'/content/markdown.php');
    echo '</article>';
}
function article_sitemap(){
    echo '<article>';
    //$num = ($GLOBALS['edit']?count($GLOBALS['lookup']):count($GLOBALS['lookup'])-1);
    $num = count($GLOBALS['lookup']);
    $count=0;

    for($t=0;$t<$num;$t++){
        $query=$GLOBALS['lookup'][$t]['query'];
        $result = DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
        if($rows!=0){
            echo '<h2>'.$GLOBALS['lookup'][$t]['category'].'</h2>';
            echo '<ul>';
            for($i=0;$i<$rows;$i++){
                $uri=DBresult($result,$i,'title');
                
                echo '<li>';
                echo '<a href="'.site_link($uri).'">'.page_title($uri).'</a>';
                if($GLOBALS['edit']){
                    echo "<div style='float:right;margin-right:10%'>";
                    $id=DBresult($result,$i,'id');
                    $type=DBresult($result,$i,'type');

                    echo "<form method=\"post\"><input type='hidden' name='formid' value='sitemap'>";
                    echo "<input type='hidden' name='id' value='$id'><input type='hidden' name='index' value=".$i."><input type='hidden' name='lookup' value=".$t.">";
                    
                    if($GLOBALS['lookup'][$t]['type']!=0){
                        echo "<input type='hidden' name='up_value' value=".$GLOBALS['uarr'].">";
                        echo "<input type='hidden' name='down_value' value=".$GLOBALS['darr'].">";
                        echo "<input name='move' type='submit' value='".$GLOBALS['uarr']."' ".($i==0?'disabled':'').">&nbsp;";
                        echo "<input name='move' type='submit' value='".$GLOBALS['darr']."' ".($i+1==$rows?'disabled':'').">&nbsp;&nbsp;";
                    }

                    echo '<a href="'.site_link($uri).edit_link().'"><input type="button" value="Edit" /></a>';

                    echo " | Main Page:<input name='home' type='checkbox' ".($type == 1 || $type == 3?"checked":'')." onChange=\"this.form.submit()\">";
                    echo " | Menu:<input name='nav' type='checkbox' ".($type == 2 || $type == 3?"checked":'')." onChange=\"this.form.submit()\">";
                
                    echo "</form></div>";
                }
                $count++;
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    if($count==0){echo '<h2>Sitemap</h2>This is a blank page.<br><br>';}

    echo '</article>';
}
function article_tags(){
    if ($GLOBALS['tag']==''){
        $query = "SELECT DISTINCT tag FROM ".table('tags')." ORDER BY tag";
        $result=DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($result);
        echo '<article>';
        echo '<h2>'.page_title('tags').'</h2>';
        $count=0;
        for($i=0;$i<$rows;$i++){
            $t=DBresult($result,$i,'tag');
            echo '<a href="'.site_link('&tag='.$t).'"><button class="tag">'.$t.'</button></a> ';
            if($GLOBALS['edit']){
                echo "<div style='float:left;margin-right:8px'>";
                echo "<form method=\"post\"><input type='hidden' name='formid' value='delete_tag'>";
                echo '<button name=\'submit\' type=\'submit\' value=\''.$t.'\' onclick="return confirm(\'[Warning]\nDelete the tag. Are you sure?\')">x</button>';
                echo '</form></div>';
            }
            echo '<br>';
            $count++;
        }
        if($count==0){echo 'This is a blank page.<br><br>';}
        echo '</article>';
    }
    else{
        $query = "SELECT DISTINCT post_id FROM ".table('tags')." WHERE tag = '".$GLOBALS['tag']."' ORDER BY post_id DESC";
        $posts=DBquery($GLOBALS['link'], $query); $rows = DBnum_rows($posts);
        if($rows==0){echo '<article><h2>Empty</h2>This is a blank page.<br><br></article>';}
        for($i=0;$i<$rows;$i++){
            $post_id=DBresult($posts,$i,'post_id');
            $query="SELECT * FROM ".table('page_content')." WHERE id = ".$post_id." LIMIT 1";
            $result = DBquery($GLOBALS['link'], $query);$r = DBnum_rows($result);
            if($r==1){
                echo '<article>';    
                $uri=DBresult($result,0,'title'); $page_id=DBresult($result,0,'id');
                echo '<h2>'.page_title($uri).'</h2>'; page_content($uri); tag_list($page_id);
                echo '<div style="text-align:right;"><a href="'.site_link($uri).'">'.config('page_link').'</a></div>';
                echo '</article>';
            }
        }
    }
}
function article_settings(){
    if($GLOBALS['setup']){echo "<div style='width:30%;margin:auto;'>";}
    echo '<article>';    
    echo '<h2>'.page_title('Settings').'</h2>';
    
    if($GLOBALS['edit']==false){include(getcwd().'/content/403.php');}
    else{
        if($GLOBALS['setup']){echo 'This is the initial setup.<br>';}
        echo "<form id='settings' method='post' action=''><input type='hidden' name='formid' value='settings'>";
        foreach($GLOBALS['settings'] as $key => $setting){
            if($setting['type']!=2){
                $value=$GLOBALS['setup']?'':config($key);

                echo "<div style='border-bottom:1px solid #ccc;padding-top:4px;padding-bottom:4px;margin-right:30%;'>";
                echo $key;
                echo "<div style='float:right;'>";
                echo ($setting['type']==0?
                    "<input id='cancel-submit' type='text' name='".$key."' value='".$value."' placeholder='".$setting['placeholder']."'>":
                    "<label class='switch'><input id='cancel-submit' type='checkbox' name='".$key."' ".($value?'checked':'')."><span class='slider'></span></label>");
                echo '</div>';
                echo ($setting['description']==''?'':"<br><small>".$setting['description']."</small>");

                echo '</div>';
            }
        }
        echo "<br><input type='submit' value='Save'>";
        echo "</form>";

        include('includes/scripts.php');
    }

    echo '</article>';
    if($GLOBALS['setup']){echo "</div>";}
}
?>