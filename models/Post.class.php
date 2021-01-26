
<?php

class Post {

  const BASE_URI = '/api/posts';

  private $id;
  private $name;
  private $text;
  private $likes;
  private $postDate;
  private $replyTo;
  private $replies = array();

  private $mapper;


  function __construct(array $arr = null) {
    if ($arr) {
      $this->id = $arr['id'];
      $this->name = $arr['name'];
      $this->text = $arr['text'];
      $this->likes = $arr['likes'];
      $this->postDate = $arr['post_date'];
      $this->replyTo = $arr['reply_to'];
    }
  }


  function setMapper(PostMapper $mapper): self {
    $this->mapper = $mapper;
    return $this;
  }


  function __serialize(): array {
    $arr = [
      'id' => $this->id,
      'name' => $this->name,
      'text' => $this->text,
      'likes' => $this->likes,
      'postDate' => $this->postDate,
      'replyTo' => $this->replyTo,
      'links' => [
        'self' => $this->makePostUrl(),
        'reply' => $this->makePostUrl(),
        'collection' => self::BASE_URI,
        'like' => $this->makeLikeUrl(),
        'unlike' => $this->makeUnLikeUrl(),
      ],
    ];

    if ($this->replyTo) {
      $post = new self();
      $post->id = $this->replyTo;
      $arr['links']['parent'] = $post->makePostUrl();
    }

    return $arr;
  }


  function getReplies(): array {
    $stmt = $this->mapper->selectAll()->where('reply_to', $this->id)->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $post = new self($row);
      $single_reply = $post->__serialize();
      $this->replies[] = $single_reply;
    }

    return $this->replies;
  }


  private function makePostUrl() {
    return self::BASE_URI . "/$this->id";
  }


  private function makeLikeUrl() {
    return self::BASE_URI . "/$this->id/like";
  }

  private function makeUnLikeUrl() {
    return self::BASE_URI . "/$this->id/unlike";
  }
}

?>