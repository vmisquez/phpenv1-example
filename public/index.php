<?php

use Particle\Validator\Validator;

require_once '../vendor/autoload.php';

$file = '../storage/database.db';
if (is_writeable('../storage/database.local.db')) {
    $file = '../storage/database.local.db';
}
$database = new medoo([
    'database_type' => 'sqlite',
    'database_file' => $file
]);

$comment = new SitePoint\Comment($database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator();
    $v->required('name')->lengthBetween(1,100)->alnum(true);
    $v->required('email')->email()->lengthBetween(5,255);
    $v->required('comment')->lengthBetween(10, null);

    $result = $v->validate($_POST);

    if ($result->isValid()) {
        try {
          $comment
            ->setName($_POST['name'])
            ->setEmail($_POST['email'])
            ->setComment($_POST['comment'])
            ->save();
         header('Location: /');
         return;

         } catch (\Exception $e) {
           die($e->getMessage());
       }
    } else {
        dump($result->getMessages());
    }
}
?>

<?php foreach ($comment->findAll() as $comment) : ?>
    <link rel="stylesheet" href="css/custom.css">
    <div class="comment">
      <h3>On <?= $comment->getSubmissionDate() ?>, <?= $comment->getName() ?> wrote:</h3>

      <p><?= $comment->getComment(); ?></p>
    </div>
<?php endforeach; ?>

<form method="post">
    <label>Name:  <input type="text" name="name"  placeholder="Your name"></label>
    <label>Email: <input type="text" name="email" placeholder="your@email.com"></label>
    <label>Comment: <textarea name="comment" cols="30" rows="10"></textarea></label>
    <input type="submit" value="Save">
</form>
