<?php
class AlphabetControl extends GenTag
{

public static function build()
{
return <<<HTML
<div id="alphabet" class="clearfix w-100cent">
    <a class="first" href="#">A</a>
    <a href="#">B</a>
    <a href="#">C</a>
    <a href="#">D</a>
    <a href="#">E</a>
    <a href="#">F</a>
    <a href="#">G</a>
    <a href="#">H</a>
    <a href="#">I</a>
    <a href="#">J</a>
    <a href="#">K</a>
    <a href="#">L</a>
    <a href="#">M</a>
    <a href="#">N</a>
    <a href="#">O</a>
    <a href="#">P</a>
    <a href="#">Q</a>
    <a href="#">R</a>
    <a href="#">S</a>
    <a href="#">T</a>
    <a href="#">U</a>
    <a href="#">V</a>
    <a href="#">W</a>
    <a href="#">X</a>
    <a href="#">Y</a>
    <a class="last" href="#">Z</a>
</div>
HTML;
}
}