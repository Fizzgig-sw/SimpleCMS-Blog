<h2 id="basic-syntax">Basic Syntax</h2>

<table class="table table-bordered">
<thead class="thead-light">
<tr>
<th>Markdown Syntax</th>
<th>Rendered Output</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan=2>Heading</td>
</tr><tr>
<td><code># H1<br />
    ## H2<br />
    ### H3</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('# H1
## H2
### H3').'</td>';?>

</tr>
<tr>
<td colspan=2>Bold</td>
</tr><tr>
<td><code>**bold text**</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('**bold text**').'</td>';?>

</tr>
<tr>
<td colspan=2>Italic</td>
</tr><tr>
<td><code>*italicized text*</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('*italicized text*').'</td>';?>

</tr>
<tr>
<td colspan=2>Quote</td>
</tr><tr>
<td><code>&gt; quote</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('> quote').'</td>';?>

</tr>
<tr>
<td colspan=2>Ordered List</td>
</tr><tr>
<td><code>
  1. First item<br />
  2. Second item<br />
  3. Third item<br />
</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('1. First item
2. Second item
3. Third item').'</td>';?>

</tr>
<tr>
<td colspan=2>Unordered List</td>
</tr><tr>
<td><code>
    - First item<br />
    - Second item<br />
    - Third item<br />
</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('- First item
- Second item
- Third item').'</td>';?>

</tr>
<tr>
<td colspan=2>Code</td>
</tr><tr>
<td><code>`code`</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('`code`').'</td>';?>

</tr>
<tr>
<td colspan=2>Horizontal Rule</td>
</tr><tr>
<td><code>---</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('---').'</td>';?>

</tr>
<tr>
<td colspan=2>Link</td>
</tr><tr>
<td><code>[title](https://www.example.com)</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('[title](https://www.example.com)').'</td>';?>

</tr>
<tr>
<td colspan=2>Image</td>
</tr><tr>
<td><code>![alt text](image.jpg)</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('![Example Image]('.site_url().'/content/images/Example.jpg)').'</td>';?>

</tr>
</tbody>
</table>

<h2 id="extended-syntax">Extended Syntax</h2>

<table class="table table-bordered">
<thead class="thead-light">
<tr>
<th>Markdown Syntax</th>
<th>Rendered Output</th>
</tr>
</thead>
<tbody>
<tr>
<td colspan=2>Table</td>
</tr><tr>
<td><pre>
| Syntax      | Description |
| ----------- | ----------- |
| Header      | Title       |
| Paragraph   | Text        |
</pre></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('
| Syntax      | Description |
| ----------- | ----------- |
| Header      | Title       |
| Paragraph   | Text        |').'</td>';?>

</tr>
<tr>
<td colspan=2>Fenced Code Block</td>
</tr><tr>
<td><pre>
```
{
  "firstName": "John",
  "lastName": "Smith",
  "age": 25
}
```
</pre></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('
```
{
  "firstName": "John",
  "lastName": "Smith",
  "age": 25
}
```
').'</td>';?>

</tr>
<tr>
<td colspan=2>Footnote</td>
</tr><tr>
<td><code>
  Here's a sentence with a footnote. [^1]<br /><br />
  [^1]: This is the footnote.
</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('
Here\'s a sentence with a footnote. [^1]

[^1]: This is the footnote.
').'</td>';?>

</tr>
<tr>
<td colspan=2>Heading ID</td>
</tr><tr>
<td><code>### My Great Heading {#custom-id}</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('### My Great Heading {#custom-id}').'</td>';?>

</tr>
<tr>
<td colspan=2>Definition List</td>
</tr><tr>
<td><code>
  term<br />
  : definition
</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('
term
: definition
').'</td>';?>

</tr>
<tr>
<td colspan=2>Strikethrough</td>
</tr><tr>
<td><code>~~The world is flat.~~</code></td>
<?php echo '<td>'.$GLOBALS['markdown']->text('~~The world is flat.~~').'</td>';?>

</tr>

</tbody>
</table>
