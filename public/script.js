// const BASE_URL = window.location.origin + '/powerdrill/api/'
const BASE_URL = '/powerdrill/api'

const createPostButton = document.querySelector('#createPostButton')
const createPostForm = document.querySelector('#createPostForm')
const main = document.querySelector('#main')
const topLevelPostList = document.querySelector('#topLevelPostList')
const postDetail = document.querySelector('#postDetail')
const replyList = document.querySelector('#replyList')
const replyForm = document.querySelector('#replyForm')

let topLevelPostArr
let currPost
let currReplyArr

createPostButton.addEventListener('click', () => {
  const text = createPostButton.textContent
  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = text === 'Create Post' ? 'Hide' : 'Create Post'
  createPostForm.classList.toggle('d-none')
})

createPostForm.addEventListener('submit', async e => {
  e.preventDefault()
  const newPost = {
    name: createPostForm.name.value,
    text: createPostForm.text.value,
  }
  const { error, ...data } = await usePost('/posts', newPost)
  if (error) return (createPostForm.querySelector('#error').textContent = error)

  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = 'Create Post'

  createPostForm.classList.toggle('d-none')
  createPostForm.reset()
  addPostDOM(data, true)
  topLevelPostArr.push(data)
})

// !! --- --- ---
// ?? --- --- ---

async function init() {
  topLevelPostList.innerHTML = ''
  const data = await useFetch('/posts/toplevel?sort=postDate:desc')
  data.forEach(data => {
    addPostDOM(data)
  })
  topLevelPostArr = data
}

init().catch(console.log)

// !! --- --- ---
// ?? --- --- ---

async function useFetch(endpoint) {
  const res = await fetch(BASE_URL + endpoint)
  return res.json()
}

async function usePost(endpoint, data) {
  const res = await fetch(BASE_URL + endpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  })
  return res.json()
}

async function usePut(endpoint, data) {
  const res = await fetch(BASE_URL + endpoint, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  })
  return res.json()
}

async function like(id) {
  return usePut(`/posts/${id}/like`)
}

async function unlike(id) {
  return usePut(`/posts/${id}/unlike`)
}

function addPostDOM({ id, postDate, likes }, prepend = false) {
  const postItem = document
    .querySelector('#topLevelPostTemplate')
    .cloneNode(true)
  postItem.classList.remove('d-none')
  postItem.classList.add('top-level-post')
  postItem.removeAttribute('id')
  postItem.querySelector('.post-id').textContent = id
  postItem.querySelector('.post-date').textContent = postDate
  postItem.querySelector('.post-likes').textContent = likes
  postItem.addEventListener('click', () => {
    document
      .querySelectorAll('.top-level-post')
      .forEach(post => post.classList.remove('active'))
    postItem.classList.add('active')
    main.classList.remove('d-none')
    getPostDetails(id)
    replyForm.text.classList.remove('border-danger')
    replyForm.text.placeholder = 'reply...'
    replyForm.reset()
  })

  if (prepend) topLevelPostList.prepend(postItem)
  else topLevelPostList.appendChild(postItem)

  return postItem
}

async function getPostDetails(id) {
  const { replies, ...post } = await useFetch(`/posts/${id}`)
  currPost = post
  currReplyArr = replies
  fillPostDetails(post)
  replyList.innerHTML = ''
  replies?.forEach(reply => addReplyDOM(reply))
}

function fillPostDetails({ id, name, postDate, likes, text }) {
  postDetail.querySelector('.post-id').textContent = id
  postDetail.querySelector('.post-name').textContent = name
  postDetail.querySelector('.post-date').textContent = postDate
  postDetail.querySelector('.post-text').textContent = text
  const postLikes = postDetail.querySelector('.post-likes')
  const btnLike = postDetail.querySelector('.btn-like')
  const btnUnlike = postDetail.querySelector('.btn-unlike')
  postLikes.textContent = likes
  btnLike.onclick = async () => {
    const data = await like(id)
    if (data) postLikes.textContent = data.likes
  }
  btnUnlike.onclick = async () => {
    const data = await unlike(id)
    if (data) postLikes.textContent = data.likes
  }
}

function addReplyDOM({ id, name, postDate, replyTo, likes, text }) {
  const replyItem = document.querySelector('#replyItemTemplate').cloneNode(true)
  replyItem.classList.remove('d-none')
  replyItem.classList.add('post')
  replyItem.querySelector('.post-id').textContent = id
  replyItem.querySelector('.post-name').textContent = name
  replyItem.querySelector('.post-date').textContent = postDate
  replyItem.querySelector('.post-replyto').textContent = replyTo
  replyItem.querySelector('.post-text').textContent = text
  const replyLikes = replyItem.querySelector('.post-likes')
  const btnLike = replyItem.querySelector('.btn-like')
  const btnUnlike = replyItem.querySelector('.btn-unlike')
  replyLikes.textContent = likes
  replyList.appendChild(replyItem)
  btnLike.addEventListener('click', async () => {
    const data = await like(id)
    if (data) replyLikes.textContent = data.likes
  })
  btnUnlike.addEventListener('click', async () => {
    const data = await unlike(id)
    if (data) replyLikes.textContent = data.likes
  })
}

replyForm.addEventListener('submit', async e => {
  e.preventDefault()
  const { error, ...data } = await usePost('/posts', {
    name: replyForm.name.value,
    text: replyForm.text.value,
    replyTo: currPost.id,
  })
  if (error) {
    replyForm.text.classList.add('border-danger')
    replyForm.text.placeholder = error
  } else {
    replyForm.text.classList.remove('border-danger')
    replyForm.text.placeholder = 'reply...'
    replyForm.reset()
    addReplyDOM(data)
    currReplyArr.push(data)
  }
})

setInterval(async () => {
  const data = await useFetch('/posts/toplevel?sort=postDate:desc')
  document.querySelectorAll('.top-level-post').forEach(postNode => {
    const currId = postNode.querySelector('.post-id').textContent
    const currLikes = postNode.querySelector('.post-likes')
    const { likes } = data.find(p => p.id === currId)
    if (likes && currLikes.textContent !== likes) currLikes.textContent = likes
  })

  if (topLevelPostArr.length !== data.length) {
    const newPostArr = data.filter(
      d => !topLevelPostArr.map(p => p.id).includes(d.id)
    )
    newPostArr.forEach(post => addPostDOM(post, true))
    topLevelPostArr = data
  }
}, 5000)

setInterval(async () => {
  if (currPost) {
    const { replies, ...post } = await useFetch(`/posts/${currPost.id}`)
    document.querySelectorAll('.post').forEach(postNode => {
      const currId = postNode.querySelector('.post-id').textContent
      const currLikes = postNode.querySelector('.post-likes')
      const { likes } = [post, ...replies].find(p => p.id === currId)
      if (currLikes.textContent !== likes) currLikes.textContent = likes
    })

    if (currReplyArr.length !== replies.length) {
      replies
        .filter(p => !currReplyArr.map(p => p.id).includes(p.id))
        .forEach(reply => addReplyDOM(reply))
      currReplyArr = replies
    }
  }
}, 5000)
