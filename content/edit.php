<form method="post" action="">
    <input type='hidden' name='formid' value='editor'>
<div style="text-align:right;">Main Page:
<!-- this should use proper bitmasking -->
<input name='home' type='checkbox' <?php echo ($document->page_content['type'] == 1 || $document->page_content['type'] == 3?"checked":'');?>> | Menu:
<input name='nav' type='checkbox' <?php echo ($document->page_content['type'] == 2 || $document->page_content['type'] == 3?"checked":'');?>>
</div>
<textarea id='content' name="textarea" style="width:100%; height:250px;"><?php echo $document->page_content['content']; ?></textarea>
<br>
<strong>Tags: </strong><small>Enter tags separated by a ',' comma. Use '-tag' to remove.</small><br>
<input id="cancel-submit" name="tags" type="input" style="width:100%" value="<?php echo $document->page_content['tags'];?>">
<br><br>
<input name='submit' type='submit' value='Save'>
<input name='submit' type='submit' value='Save and edit'>
<input name='submit' type='submit' value='Preview'>
<input name='submit' type='submit' value='Cancel'>
<?php echo '<input name=\'submit\' type=\'submit\' value=\'Delete\' onclick="return confirm(\'[Warning]\nDelete the page. Are you sure?\')">';?>
<input type='hidden' name='id' value='<?php echo $document->page_content['id'];?>'>
<input type='hidden' id='title' name='title' value='<?php echo ($document->page_content['title']='new_entry' ? $GLOBALS['page'] : $document->page_content['title']);?>'>
<input type='hidden' name='list' value='<?php echo $document->page_content['list'];?>'>
</form>
<br>
<button id='save' onClick="download()">Save to File</button>
<br>
<br>
<a id="markdown-help" href="javascript:toggle('markdown-help-content','markdown-help','Show - Markdown Manual','Hide - Markdown Manual');"><span style="font-size: 80%;">Show - Markdown Manual</span></a>
</article>

<?php include('includes/scripts.php');?>

<?php
echo '<div id="markdown-help-content" style="display: none;">';
echo '<h3>Markdown Manual</h3>';
echo '<article>';
include(getcwd().'/content/markdown.php');
echo '</article>';
echo '</div>';
?>

<?php
if($GLOBALS['preview']){
    echo '<h3>Preview</h3><p><strong>This page is not saved</strong></p>';
    echo '<article>';
    echo '<h2>'.page_title($GLOBALS['page']).'</h2>';
    echo $GLOBALS['markdown']->text($document->page_content['content']);
    echo '</article>';
    echo '<p><strong>End of preview - Remember to save</strong><br></p>';
}
?>