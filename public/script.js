const BASE_URL = '/powerdrill'

const createPostButton = document.querySelector('#createPostButton')
const createPostForm = document.querySelector('#createPostForm')
const topLevelPostList = document.querySelector('#topLevelPostList')
const postDetail = document.querySelector('#postDetail')
const repliesSection = document.querySelector('#repliesSection')
const replyList = document.querySelector('#replyList')
const replyForm = document.querySelector('#replyForm')

let topLevelPosts
let currPost

// !! --- --- ---
// ?? --- --- ---

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
  const { error, ...data } = await usePost('/api/posts', newPost)
  if (error) return (createPostForm.querySelector('#error').textContent = error)

  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = 'Create Post'

  createPostForm.classList.toggle('d-none')
  createPostForm.reset()
  addPostDOM(data, true)
  topLevelPosts.push(data)
})

// !! --- --- ---
// ?? --- --- ---

async function init() {
  topLevelPostList.innerHTML = ''
  const data = await useFetch('/api/posts?replyTo=null&sort=postDate:desc')
  topLevelPosts = data
  topLevelPosts.forEach(post => {
    addPostDOM(post)
  })
}

init().catch(console.log)

// !! --- --- ---
// ?? --- --- ---

function addPostDOM(post, prepend = false) {
  const postItem = document
    .querySelector('#topLevelPostTemplate')
    .cloneNode(true)
  postItem.classList.remove('d-none')
  postItem.classList.add('top-level-post')
  postItem.removeAttribute('id')
  postItem.querySelector('.post-id').textContent = post.id
  postItem.querySelector('.post-date').textContent = post.postDate
  postItem.querySelector('.post-likes').textContent = post.likes

  postItem.addEventListener('click', () => {
    postDetail.classList.remove('d-none')
    repliesSection.classList.remove('d-none')
    document
      .querySelectorAll('.top-level-post')
      .forEach(post => post.classList.remove('active'))
    postItem.classList.add('active')
    showPost(post.id)
  })

  if (prepend) topLevelPostList.prepend(postItem)
  else topLevelPostList.appendChild(postItem)

  return postItem
}

async function showPost(id) {
  const post = topLevelPosts.find(p => p.id === id)
  const { replies } = post
  currPost = post
  fillPostDetails(post)
  replyList.innerHTML = ''
  replies.forEach(reply => addReplyDOM(reply))

  replyForm.text.classList.remove('border-danger')
  replyForm.text.placeholder = 'reply...'
  replyForm.reset()
}

function fillPostDetails({ id, name, postDate, likes, text, links }) {
  postDetail.querySelector('.post-id').textContent = id
  postDetail.querySelector('.post-name').textContent = name
  postDetail.querySelector('.post-date').textContent = postDate
  postDetail.querySelector('.post-text').textContent = text
  const postLikes = postDetail.querySelector('.post-likes')
  const btnLike = postDetail.querySelector('.btn-like')
  const btnUnlike = postDetail.querySelector('.btn-unlike')
  postLikes.textContent = likes
  btnLike.onclick = async () => {
    const { likes } = await usePut(links.like)
    if (likes) postLikes.textContent = likes
  }
  btnUnlike.onclick = async () => {
    const { likes } = await usePut(links.unlike)
    if (likes) postLikes.textContent = likes
  }
}

function addReplyDOM({ id, name, postDate, replyTo, likes, text, links }) {
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
    const { likes } = await usePut(links.like)
    if (likes) replyLikes.textContent = likes
  })
  btnUnlike.addEventListener('click', async () => {
    const { likes } = await usePut(links.unlike)
    if (likes) replyLikes.textContent = likes
  })
}

replyForm.addEventListener('submit', async e => {
  e.preventDefault()
  const { error, ...data } = await usePost('/api/posts', {
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
    currPost.replies.push(data)
  }
})

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

async function usePut(endpoint) {
  const res = await fetch(BASE_URL + endpoint, {
    method: 'PUT',
  })
  return res.json()
}

// !! --- --- ---
// ?? --- --- ---

setInterval(async () => {
  const data = await useFetch('/api/posts?replyTo=null&sort=postDate:desc')
  document.querySelectorAll('.top-level-post').forEach(postNode => {
    const postId = postNode.querySelector('.post-id').textContent
    const postLikes = postNode.querySelector('.post-likes')
    const { likes } = data.find(p => p.id === postId)
    if (postLikes.textContent !== likes) postLikes.textContent = likes
  })

  if (topLevelPosts.length !== data.length) {
    const ids = topLevelPosts.map(p => p.id)
    const newPosts = data.filter(p => !ids.includes(p.id))
    newPosts.forEach(post => addPostDOM(post, true))
  }

  if (currPost) {
    const newPost = data.find(p => p.id === currPost.id)
    document.querySelectorAll('.post').forEach(postNode => {
      const postId = postNode.querySelector('.post-id').textContent
      const postLikes = postNode.querySelector('.post-likes')
      const { likes } = [newPost, ...newPost.replies].find(p => p.id === postId)
      if (postLikes.textContent !== likes) postLikes.textContent = likes
    })

    if (currPost.replies.length !== newPost.replies.length) {
      const ids = currPost.replies.map(p => p.id)
      newPost.replies
        .filter(p => !ids.includes(p.id))
        .forEach(reply => addReplyDOM(reply))
    }
    currPost = newPost
  }

  topLevelPosts = data
}, 5000)
