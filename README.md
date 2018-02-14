# Advanced Topics in Web Development Assignment <br/> 2017-2018.

Need to look at:
- assigment spec
  -
- [Google Charts](https://developers.google.com/chart/)
- etc...

Languages to learn
- XML languages (XML, XPath, XQuery, XSLT)
- PHP
  - [PHP XML Parser](https://www.w3schools.com/php/php_xml_simplexml_read.asp)
  - PHP XMLReader()
  - PHP XMLWriter()
- API calling (probably JSON format...)
  - look through chart data section
- Look up AJAX
- Linda - XQuery & XPath... need to look this shit up!!

What may help
- Development on Atom
  - XSLTransform package = transforms an xml file using an xsl given
  - live-server = start up a server (will autoupdate)

-----------

plan

- convert the large csv into xml file
  - columns of csv:
    - monitor_id (e.g. 6)
    - monitor_description (e.g. fishponds)
    - date (13/02/2016)
    - time (03:15:00)
    - nox (48)
    - no (26)
    - no2 (21)
    - lat (51.478)
    - long (-2.535)
    - location ("(51.478, -2.535)")
      - redundant?
  - each column = xml tag
  - root tag = <list> or <rows>
  - lets see if xslt will work for it or maybe something else?
    - jquery or php (php example given!)??
  - lets make this server side!!
    - so server will create xml file and sub xml files, client will not do much computation
    - client will just be responsible for displaying!

- break up new xml files into 6 xml files
  - one for each monitor_id/desc
    - Brislington.xml, fishponds.xml, ...
  - this can be done XSLT!

-
