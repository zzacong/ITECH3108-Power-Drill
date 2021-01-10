<?php

require_once 'utils/utils.php';

$raw = file_get_contents('http://localhost/powerdrill/api/getAll.php');
$postData = json_decode($raw)->data;

function filter_reply($var) {
  return $var->replyTo !== null;
}

?>
<!-- <pre> -->
<?php
// print_r($postData);
?>
<!-- </pre> -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <link rel="stylesheet" href="styles/style.css">
  <script defer src="js/script.js"></script>
  <title>Power Drill Grapevine</title>
</head>

<body>

  <nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="container-fluid">
      <a href="<?= $_SERVER['PHP_SELF'] ?>" class="navbar-brand">Power Drill Grapevine</a>
      <button id="createPostButton" class="btn btn-outline-success nav-link">Create Post</button>
    </div>
  </nav>

  <main class="container py-5">

    <form id="createPostForm" class="d-none">
      <h2>Create Post</h2>
      <div class="form-group">
        <label for="inputName">Name</label>
        <input type="text" class="form-control" id="inputName" name="name" placeholder="name">
      </div>
      <div class="form-group">
        <label for="inputText">Text</label>
        <textarea type="password" class="form-control" id="inputText" name="text" placeholder="body text..."></textarea>
      </div>
      <p id="error" class="text-danger mb-4"></p>
      <button type="submit" class="btn btn-sm btn-success">Submit</button>
    </form>

    <section id="posts">
      <?php foreach ($postData as $post) :; ?>
        <div id="post-<?= html($post->id) ?>" class="card my-4 post">
          <div class="card-body">
            <h4 class="card-title">Post #<?= html($post->id); ?></h4>
            <table class="table">
              <tbody>
                <tr>
                  <th scope="row">Name</th>
                  <td><?= html($post->name); ?></td>
                </tr>
                <tr>
                  <th scope="row">Likes</th>
                  <td class="post-likes"><?= html($post->likes); ?></td>
                </tr>
                <tr>
                  <th scope="row">Post date</th>
                  <td><?= html($post->postDate); ?></td>
                </tr>
                <tr>
                  <th scope="row">Text</th>
                  <td><?= html($post->text); ?></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-body d-none replies">
            <?php
            $raw = file_get_contents("http://localhost/powerdrill/api/get.php?id=$post->id");
            $replyData = json_decode($raw)->data;
            $replyData = array_filter($replyData, 'filter_reply');
            ?>
            <h5 class="card-text">Replies</h5>
            <ul class="list-group">
              <?php foreach ($replyData as $reply) :; ?>
                <li id="reply-<?= html($reply->id) ?>" class="list-group-item reply">
                  <table class="table table-sm">
                    <tbody>
                      <tr>
                        <th scope="row">ID</th>
                        <td><?= html($reply->id); ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Name</th>
                        <td><?= html($reply->name); ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Likes</th>
                        <td class="reply-likes"><?= html($reply->likes); ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Reply date</th>
                        <td><?= html($reply->postDate); ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Reply to</th>
                        <td><?= html($reply->replyTo); ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Text</th>
                        <td><?= html($reply->text); ?></td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="d-flex justify-content-end my-2">
                    <button class="btn btn-sm btn-primary mx-2 btn-like">Like</button>
                    <button class="btn btn-sm btn-danger mx-2 btn-unlike">Unlike</button>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="container mt-3">
              <form class="row reply-form">
                <div class="col-2 px-0">
                  <input type="text" name="name" placeholder="name" class="form-control">
                </div>
                <div class="col-9">
                  <input type="text" name="text" placeholder="reply..." class="form-control">
                </div>
                <div class="col-1 px-0">
                  <button type="submit" class="btn btn-primary">Reply</button>
                </div>
              </form>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <div>
              <button class="btn btn-sm btn-primary btn-like">Like</button>
              <button class="btn btn-sm btn-danger btn-unlike">Unlike</button>
            </div>
            <button class="btn btn-sm btn-success btn-view">View</button>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </main>

</body>

</html>