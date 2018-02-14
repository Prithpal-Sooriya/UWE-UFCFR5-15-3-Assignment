# PHP PRACTICE
- going through the tutorials on [w3schools](https://www.w3schools.com/php/default.asp) to get used to some basic PHP.

- using the [php-server](https://atom.io/packages/php-server) to start a server that understands php.
  - the atom-live-server did not support php...
  - windows path used (was a fucking nightmare...): C:\php\php --> the \php\php = php folder\php exe
- using [live-reload](https://atom.io/packages/livereload) to auto reload the web page
  - need to install the [chrome extension](https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei?hl=en)
  - need to run live reload (ctrl-shift-r, it will show at the bottom of atom)
  - need to paste the URL link into a <script "javascript/text"> </script> tag

-------
## notes

- php uses <?php ?> tag
- php has java style comments
- php does not have strict case
  - only var names are case sensitive
- vars decalred by e.g. $color
  - e.g. $txt = "Hello" //input/declaration
  - echo "hello, $txt" //output string
  - echo "hello" . $txt //same output


```
<?php
//maths example
$x = 5;
$y = 4;
echo $x + $y;
?>
```

- vars have global, local scope
  - use 'global' keyword to access global

```
$x = 5;
$y = 10;

function myTest() {
  //map global to local
    global $x, $y;
    $y = $x + $y;
}
```

- static variables = only 1 instance of value.


- php output = echo or print
  - html tags can be defined in strings

- php vars can have max/min sizes (like java.maxInt...)

- php can have objects, by using 'class'.

- php can have NULL values

- php has a bunch of functions
  - string functions (strlen, strrev, ...)
  - other functions
