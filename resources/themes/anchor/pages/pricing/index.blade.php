<?php
use function Laravel\Folio\{name, render};

name('pricing');

render(fn() => redirect()->route('products', [], 301));
?>
