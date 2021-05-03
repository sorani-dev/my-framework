<?= $renderer->render('header'); ?>
<h1>Welcome to the Blog!</h1>

<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'azeaze20-7']); ?>">Article 1</a></li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
</ul>
<?= $renderer->render('footer'); ?>