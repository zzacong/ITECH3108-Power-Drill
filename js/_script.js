let oriPosts
const createPostButton = document.querySelector('#createPostButton')
const createPostForm = document.querySelector('#createPostForm')

createPostButton.addEventListener('click', () => {
  const text = createPostButton.textContent
  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = text === 'Create Post' ? 'Hide' : 'Create Post'
  createPostForm.classList.toggle('d-none')
})

createPostForm.addEventListener('submit', async e => {
  e.preventDefault()
  post = {
    name: createPostForm['name'].value,
    text: createPostForm['text'].value,
  }

  const res = await fetch('api/create.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(post),
  })
  const { data, error } = await res.json()
  if (error) return (createPostForm.querySelector('#error').textContent = error)

  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = 'Create Post'

  createPostForm.classList.toggle('d-none')
  createPostForm.reset()
  oriPosts.push(data)
  addPost(data)
})

// ! ------
// ? ------

function main() {
  const posts = document.querySelectorAll('.top-post')

  posts.forEach(post => {
    const id = post.getAttribute('id').split('-')[1]

    const ul = post.querySelector('.reply-list')
    const likes = post.querySelector('.post-likes')
    const replyForm = post.querySelector('.reply-form')
    const replies = post.querySelector('.replies')

    const cardFooter = post.querySelector('.card-footer')
    const likeButton = cardFooter.querySelector('.btn-like')
    const unlikeButton = cardFooter.querySelector('.btn-unlike')
    const viewButton = cardFooter.querySelector('.btn-view')

    likeButton.addEventListener('click', async () => {
      const { data } = await like(id)
      if (data) likes.textContent = data.likes
    })

    unlikeButton.addEventListener('click', async () => {
      const { data } = await unlike(id)
      if (data) likes.textContent = data.likes
    })

    viewButton.addEventListener('click', async () => {
      viewButton.classList.toggle('btn-success')
      viewButton.classList.toggle('btn-secondary')
      const text = viewButton.textContent
      viewButton.textContent = text === 'View' ? 'Hide' : 'View'
      replies.classList.toggle('d-none')
    })

    replyForm.addEventListener('submit', async e => {
      e.preventDefault()
      const res = await fetch('api/create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          name: replyForm.name.value,
          text: replyForm.text.value,
          replyTo: id,
        }),
      })
      const { data, error } = await res.json()
      if (error) return console.log(error)
      replyForm.reset()
      oriPosts.push(data)
      addReply(ul, data)
    })
  })

  // ! ------
  // ? ------

  const replies = document.querySelectorAll('.reply')

  replies.forEach(reply => {
    const id = reply.getAttribute('id').split('-')[1]
    const likeButton = reply.querySelector('.btn-like')
    const unlikeButton = reply.querySelector('.btn-unlike')
    const likes = reply.querySelector('.post-likes')

    likeButton.addEventListener('click', async () => {
      const { data } = await like(id)
      if (data) likes.textContent = data.likes
    })

    unlikeButton.addEventListener('click', async () => {
      const { data } = await unlike(id)
      if (data) likes.textContent = data.likes
    })
  })
}

// ! ------
// ? ------

main()

// ! ------
// ? ------

async function like(id) {
  const res = await fetch('api/like.php', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id }),
  })
  return res.json()
}

async function unlike(id) {
  const res = await fetch('api/unlike.php', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id }),
  })
  return res.json()
}

// ! ------
// ? ------

function addReply(ul, { id, ...data }) {
  // * Render newly added reply
  const li = document.createElement('li')
  li.setAttribute('id', `post-${id}`)
  li.className = 'list-group-item post reply'
  li.innerHTML = `
    <table class="table table-sm">
      <tbody>
        <tr>
          <th scope="row">ID</th>
          <td>${id}</td>
        </tr>
        <tr>
          <th scope="row">Name</th>
          <td>${data.name}</td>
        </tr>
        <tr>
          <th scope="row">Likes</th>
          <td class="post-likes">${data.likes}</td>
        </tr>
        <tr>
          <th scope="row">Reply date</th>
          <td>${data.date}</td>
        </tr>
        <tr>
          <th scope="row">Reply to</th>
          <td>${data.replyTo}</td>
        </tr>
        <tr>
          <th scope="row">Text</th>
          <td>${data.text}</td>
        </tr>
      </tbody>
    </table>
    <div class="d-flex justify-content-end my-2">
      <button class="btn btn-sm btn-primary mx-2 btn-like">Like</button>
      <button class="btn btn-sm btn-danger mx-2 btn-unlike">Unlike</button>
    </div>
  `
  ul.appendChild(li)

  // * Add event listeners
  const likeButton = li.querySelector('.btn-like')
  const unlikeButton = li.querySelector('.btn-unlike')
  const likes = li.querySelector('.post-likes')
  likeButton.addEventListener('click', async () => {
    const { data } = await like(id)
    if (data) likes.textContent = data.likes
  })
  unlikeButton.addEventListener('click', async () => {
    const { data } = await unlike(id)
    if (data) likes.textContent = data.likes
  })
}

// ! ------
// ? ------

function addPost({ id, ...data }) {
  const postSection = document.querySelector('#posts')
  // * Render new post
  const newPost = document.createElement('div')
  newPost.setAttribute('id', `post-${id}`)
  newPost.className = 'card my-4 post top-post'
  newPost.innerHTML = `
    <div class="card-body">
      <h4 class="card-title">Post #${id}</h4>
      <table class="table">
        <tbody>
          <tr>
            <th scope="row">Name</th>
            <td>${data.name}</td>
          </tr>
          <tr>
            <th scope="row">Likes</th>
            <td class="post-likes">${data.likes}</td>
          </tr>
          <tr>
            <th scope="row">Post date</th>
            <td>${data.postDate}</td>
          </tr>
          <tr>
            <th scope="row">Text</th>
            <td>${data.text}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="card-body d-none replies">
      <h5 class="card-text">Replies</h5>
      <ul class="list-group reply-list">
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
  `
  postSection.prepend(newPost)

  // * Add event listeners
  const ul = newPost.querySelector('.reply-list')
  const likes = newPost.querySelector('.post-likes')
  const replyForm = newPost.querySelector('.reply-form')
  const replies = newPost.querySelector('.replies')

  const cardFooter = newPost.querySelector('.card-footer')
  const likeButton = cardFooter.querySelector('.btn-like')
  const unlikeButton = cardFooter.querySelector('.btn-unlike')
  const viewButton = cardFooter.querySelector('.btn-view')

  likeButton.addEventListener('click', async () => {
    const { data } = await like(id)
    if (data) likes.textContent = data.likes
  })

  unlikeButton.addEventListener('click', async () => {
    const { data } = await unlike(id)
    if (data) likes.textContent = data.likes
  })

  viewButton.addEventListener('click', async () => {
    viewButton.classList.toggle('btn-success')
    viewButton.classList.toggle('btn-secondary')
    const text = viewButton.textContent
    viewButton.textContent = text === 'View' ? 'Hide' : 'View'
    replies.classList.toggle('d-none')
  })

  replyForm.addEventListener('submit', async e => {
    e.preventDefault()
    const res = await fetch('api/create.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: replyForm.name.value,
        text: replyForm.text.value,
        replyTo: id,
      }),
    })
    const { data } = await res.json()
    oriPosts.push(data)
    addReply(ul, data)
  })
}

// ! ------
// ? ------

;(async () => {
  const res = await fetch('api/getAll.php')
  const { data } = await res.json()
  oriPosts = data
})()

async function polling() {
  const topLevelPosts = oriPosts.filter(post => post.replyTo === null)
  const replies = oriPosts.filter(post => post.replyTo !== null)

  const res = await fetch('api/getAll.php')
  const { data } = await res.json()

  const updatedTopLevelPosts = data.filter(post => post.replyTo === null)
  const updatedReplies = data.filter(post => post.replyTo !== null)

  // * Check likes changes
  oriPosts.forEach(post => {
    const updatedPost = data.find(p => p.id === post.id)
    if (post.likes !== updatedPost.likes) {
      const postToUpdate = document.querySelector(`#post-${updatedPost.id}`)
      postToUpdate.querySelector('.post-likes').textContent = updatedPost.likes
    }
  })

  // * Check new replies
  const repliesIds = replies.map(post => post.id)
  const newReplies = updatedReplies.filter(
    post => !repliesIds.includes(post.id)
  )
  newReplies.forEach(newReply =>
    addReply(
      document
        .querySelector(`#post-${newReply.replyTo}`)
        .querySelector('.reply-list'),
      newReply
    )
  )

  // * Check new top level post
  const topLevelPostIds = topLevelPosts.map(post => post.id)
  const newTopLevelPosts = updatedTopLevelPosts.filter(
    post => !topLevelPostIds.includes(post.id)
  )
  newTopLevelPosts.forEach(newPost => {
    addPost(newPost)
  })

  oriPosts = data
}

setInterval(polling, 5000)
