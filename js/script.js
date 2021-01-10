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
  const { error } = await res.json()
  if (error) return (createPostForm.querySelector('#error').textContent = error)

  createPostForm.classList.toggle('d-none')
  location.reload()
})

// ! ------
// ? ------

function main() {
  const posts = document.querySelectorAll('.post')

  posts.forEach(post => {
    const id = post.getAttribute('id').split('-')[1]
    const cardFooter = post.querySelector('.card-footer')
    const likeButton = cardFooter.querySelector('.btn-like')
    const unlikeButton = cardFooter.querySelector('.btn-unlike')
    const viewButton = cardFooter.querySelector('.btn-view')
    const replyForm = post.querySelector('.reply-form')
    const likes = post.querySelector('.post-likes')
    likeButton.addEventListener('click', async () => {
      const { data } = await like(id)
      if (data) likes.textContent = data.likes
    })
    unlikeButton.addEventListener('click', async () => {
      const { data } = await unlike(id)
      if (data) likes.textContent = data.likes
    })
    viewButton.addEventListener('click', async () => {
      const replies = post.querySelector('.replies')
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
      // const { data } = await res.json()
      // addReply(post, data)
    })
  })

  // ! ------
  // ? ------

  const replies = document.querySelectorAll('.reply')
  replies.forEach(reply => {
    const id = reply.getAttribute('id').split('-')[1]
    const likeButton = reply.querySelector('.btn-like')
    const unlikeButton = reply.querySelector('.btn-unlike')
    const likes = reply.querySelector('.reply-likes')
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

function addReply(post, data) {
  const li = document.createElement('li')
  li.className = 'list-group-item reply'
  li.setAttribute('id', `reply-${data.id}`)
  li.innerHTML = `
          <table class="table table-sm">
            <tbody>
              <tr>
                <th scope="row">ID</th>
                <td>${data.id}</td>
              </tr>
              <tr>
                <th scope="row">Name</th>
                <td>${data.name}</td>
              </tr>
              <tr>
                <th scope="row">Likes</th>
                <td class="reply-likes">${data.likes}</td>
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
  post.querySelector('.replies').querySelector('ul').appendChild(li)
  return main()
}
