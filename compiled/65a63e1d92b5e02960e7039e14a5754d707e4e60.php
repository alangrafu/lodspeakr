<?php
$HAANGA_VERSION  = '1.0.4';
/* Generated from /Users/alvarograves/github/lodspeakr/views/default.view.html */
function haanga_65a63e1d92b5e02960e7039e14a5754d707e4e60($vars, $return=FALSE, $blocks=array())
{
    extract($vars);
    if ($return == TRUE) {
        ob_start();
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" ';
    foreach ($base['ns'] as  $i => $ns) {
        echo 'xmlns:'.htmlspecialchars($i).'="'.htmlspecialchars($ns).'" 
    ';
    }
    echo 'version="XHTML+RDFa 1.0" xml:lang="en">
    <head>
    <title>Page about '.htmlspecialchars($base['this']['value']).'</title>
    <link href="'.htmlspecialchars($base['baseUrl']).'/lodspeakr/css/basic.css" rel="stylesheet" type="text/css" media="screen" />
  </head>
  <body>                               
    <h1>Page about <a href=\''.htmlspecialchars($base['this']['value']).'\'>'.htmlspecialchars($base['this']['curie']).'</a></h1>
  <div>
    <h2>Information from '.htmlspecialchars($base['this']['curie']).'</h2>
    <table about="'.htmlspecialchars($base['this']['value']).'"> 
    ';
    foreach ($r as  $row) {
        echo '
      ';
        if ($row['s1'] != $null) {
            echo '
      <tr>
        <td><a href=\''.htmlspecialchars($row['s1']->value).'\'>'.htmlspecialchars($row['s1']->value).'</a></td>
        ';
            if ($row['p1']->uri == 1) {
                echo '
        <td><a rel=\''.htmlspecialchars($row['s1']->curie).'\' href=\''.htmlspecialchars($row['p1']->value).'\'>'.htmlspecialchars($row['p1']->curie).'</a></td>
        ';
            } else {
                echo '
        <td><span property=\''.htmlspecialchars($row['s1']->curie).'\'>'.htmlspecialchars($row['p1']->value).'</span></td>
        ';
            }
            echo '
        </tr>
      ';
        }
        echo '
    ';
    }
    echo '
    </table>
    <br/><br/>
    <h2>Information pointing to '.htmlspecialchars($base['this']['curie']).'</h2>

    <table about="'.htmlspecialchars($base['this']['value']).'"> 
    ';
    foreach ($r as  $row) {
        echo '
    
      ';
        if ($row['s2'] != $null) {
            echo '
    <tr>
        <td><a rev=\''.htmlspecialchars($row['p2']['curie']).'\' href=\''.htmlspecialchars($row['s2']['value']).'\'>'.htmlspecialchars($row['s2']['curie']).'</a></td>
        <td><a href=\''.htmlspecialchars($row['p2']['value']).'\'>'.htmlspecialchars($row['p2']['curie']).'</a></td>
    </tr>
    ';
        }
        echo '
    ';
    }
    echo '
    </table>
    </div>
  </body>
</html>

';
    if ($return == TRUE) {
        return ob_get_clean();
    }
}