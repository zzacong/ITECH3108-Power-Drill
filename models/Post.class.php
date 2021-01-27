
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


  function setId(int $id): self {
    $this->id = $id;
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
        'self' => $this->makePostUri(),
        'reply' => $this->makePostUri(),
        'replies' => $this->makeRepliesUri(),
        'collection' => self::BASE_URI,
        'like' => $this->makeLikeUri(),
        'unlike' => $this->makeUnLikeUri(),
      ],
    ];

    if ($this->replyTo) {
      $post = new self();
      $post->id = $this->replyTo;
      $arr['links']['parent'] = $post->makePostUri();
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


  function makePostUri() {
    return self::BASE_URI . "/$this->id";
  }


  function makeRepliesUri() {
    return self::BASE_URI . "/$this->id/replies";
  }


  function makeLikeUri() {
    return self::BASE_URI . "/$this->id/like";
  }

  function makeUnLikeUri() {
    return self::BASE_URI . "/$this->id/unlike";
  }
}

?>