# To Read before Upgrading System Files

Please make sure to keep this document up-to-date when updating CodeIgniter System Files in order to override the changes after upgrading CodeIgniter. 

### Security Class

To prevent command execution like Windows CMD, we changed the file `\system\core\Security.php` at line 148 in `$_never_allowed_str` by adding `'cmd|'` to the end of the array as shown below:

```php
protected $_never_allowed_str =	array(
		'document.cookie' => '[removed]',
		'(document).cookie' => '[removed]',
		'document.write'  => '[removed]',
		'(document).write'  => '[removed]',
		'.parentNode'     => '[removed]',
		'.innerHTML'      => '[removed]',
		'-moz-binding'    => '[removed]',
		'<!--'            => '&lt;!--',
		'-->'             => '--&gt;',
		'<![CDATA['       => '&lt;![CDATA[',
		'<comment>'	  => '&lt;comment&gt;',
		'<%'              => '&lt;&#37;',
        'cmd|'            => ''
	);
```
