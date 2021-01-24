
<?php

class Post {

  private $id;
  private $name;
  private $text;
  private $likes;
  private $post_date;
  private $reply_to;
  private $replies = array();

  private $mapper;


  function __construct(array $arr, PostMapper $mapper) {
    $this->id = $arr['id'];
    $this->name = $arr['name'];
    $this->text = $arr['text'];
    $this->likes = $arr['likes'];
    $this->post_date = $arr['post_date'];
    $this->reply_to = $arr['reply_to'];
    $this->mapper = $mapper;
  }


  function __serialize(): array {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'text' => $this->text,
      'likes' => $this->likes,
      'postDate' => $this->post_date,
      'replyTo' => $this->reply_to,
      'links' => [
        'post' => $this->makePostUrl(),
        'like' => $this->makeLikeUrl(),
        'unlike' => $this->makeUnLikeUrl(),
      ],
      'replies' => $this->getReplies(),
    ];
  }


  function getReplies(): array {
    $stmt = $this->mapper->selectAll()->where('reply_to', $this->id)->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      $post = new Post($row, $this->mapper);
      $single_reply = $post->__serialize();
      $this->replies[] = $single_reply;
    }

    return $this->replies;
  }


  private function makeUrl() {
    $base_url = '/api/posts/';
    return $base_url;
  }


  private function makePostUrl() {
    return $this->makeUrl() . $this->id;
  }


  private function makeLikeUrl() {
    return $this->makeUrl() . $this->id . '/like';
  }

  private function makeUnLikeUrl() {
    return $this->makeUrl() . $this->id . '/like';
  }
}

?>