<?php
/**
 * Special options to style things!
 *
 *
 */








// load css option fields
foreach (glob(dirname(__FILE__). "/options/*", GLOB_ONLYDIR) as $filename) {
            include('options/' . basename($filename) . '/' . basename($filename) . '.php') ;
};
