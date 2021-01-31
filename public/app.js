const BASE_URL = window.location.pathname + '..'
const TOP_LEVEL_URI = '/api/posts?replyTo=null&sort=postDate:desc'

const createPostButton = document.querySelector('#createPostButton')
const createPostForm = document.querySelector('#createPostForm')
const topLevelPostList = document.querySelector('#topLevelPostList')
const postDetail = document.querySelector('#postDetail')
const repliesSection = document.querySelector('#repliesSection')
const replyList = document.querySelector('#replyList')
const replyForm = document.querySelector('#replyForm')
const btnPrevious = document.querySelector('#previousButton')

const currPostId = postDetail.querySelector('.post-id')
const currPostName = postDetail.querySelector('.post-name')
const currPostDate = postDetail.querySelector('.post-date')
const currPostText = postDetail.querySelector('.post-text')
const currPostLikes = postDetail.querySelector('.post-likes')
const currPostReplyTo = postDetail.querySelector('.post-replyto')
const currPostBtnLike = postDetail.querySelector('.btn-like')
const currPostBtnUnlike = postDetail.querySelector('.btn-unlike')

let topLevelPosts
let createPostLink
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
  const { error, ...data } = await usePost(createPostLink, newPost)
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

// *
// * Initial method. Only run once on first load / refresh
async function init() {
  topLevelPostList.innerHTML = ''
  const { data, links } = await useFetch(TOP_LEVEL_URI)
  createPostLink = links.create
  topLevelPosts = data
  topLevelPosts.forEach(post => {
    addPostDOM(post)
  })

  // Show the latest top-level post on first load
  document.querySelector('.top-level-post').classList.add('active')
  showPost(topLevelPosts[0].links.self)
}

init().catch(console.log)

// !! --- --- ---
// ?? --- --- ---

// *
// * Called whenever a new top-level post needs to be added to DOM
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
    document
      .querySelectorAll('.top-level-post')
      .forEach(postNode => postNode.classList.remove('active'))
    postItem.classList.add('active')
    showPost(post.links.self)
  })

  if (prepend) topLevelPostList.prepend(postItem)
  else topLevelPostList.appendChild(postItem)

  return postItem
}

// *
// * Called whenever a post is selected
// * Fetch the details and replies of the post
async function showPost(links) {
  const post = await useFetch(links)
  currPost = post
  const { replies } = currPost
  fillPostDetails(post)

  replyList.innerHTML = ''
  replies.forEach(reply => addReplyDOM(reply))

  replyForm.text.classList.remove('border-danger')
  replyForm.text.placeholder = 'reply...'
  replyForm.reset()
}

// *
// * Handle re-rendering of the details of the current selected post to the DOM
function fillPostDetails({ id, name, postDate, likes, text, replyTo }) {
  currPostId.textContent = id
  currPostDate.textContent = postDate
  currPostText.textContent = text
  currPostLikes.textContent = likes
  currPostReplyTo.textContent = replyTo ? `Post #${replyTo}` : 'null'
  currPostName.textContent = name || 'anonymous'
  if (name) currPostName.classList.remove('text-warning')
  else currPostName.classList.add('text-warning')

  if (replyTo) btnPrevious.classList.remove('hide')
  else btnPrevious.classList.add('hide')
}

// *
// * Called whenever a new reply needs to be added to the DOM
function addReplyDOM({ id, name, postDate, likes, text, links, replyTo }) {
  const replyItem = document.querySelector('#replyItemTemplate').cloneNode(true)
  replyItem.classList.remove('d-none')
  replyItem.classList.add('post')
  replyItem.querySelector('.post-id').textContent = id
  replyItem.querySelector('.post-date').textContent = postDate
  replyItem.querySelector('.post-replyto').textContent = replyTo
  replyItem.querySelector('.post-text').textContent = text

  const replyName = replyItem.querySelector('.post-name')
  const replyLikes = replyItem.querySelector('.post-likes')
  const btnLike = replyItem.querySelector('.btn-like')
  const btnUnlike = replyItem.querySelector('.btn-unlike')
  const btnView = replyItem.querySelector('.btn-view')

  replyName.textContent = name || 'anonymous'
  if (name) replyName.classList.remove('text-warning')
  else replyName.classList.add('text-warning')

  replyLikes.textContent = likes
  replyList.appendChild(replyItem)

  btnLike.addEventListener('click', async () => {
    const { likes } = await usePost(links.like)
    if (likes) replyLikes.textContent = likes
  })
  btnUnlike.addEventListener('click', async () => {
    const { likes } = await usePost(links.unlike)
    if (likes) replyLikes.textContent = likes
  })
  btnView.addEventListener('click', async () => {
    showPost(links.self)
  })
}

// !! --- --- ---
// ?? --- --- ---

btnPrevious.addEventListener('click', () => {
  if (currPost) showPost(currPost.links.parent)
})

currPostBtnLike.addEventListener('click', async () => {
  const { likes } = await usePost(currPost?.links?.like)
  if (likes) currPostLikes.textContent = likes
})

currPostBtnUnlike.addEventListener('click', async () => {
  const { likes } = await usePost(currPost?.links?.unlike)
  if (likes) currPostLikes.textContent = likes
})

replyForm.addEventListener('submit', async e => {
  e.preventDefault()
  if (currPost) {
    const { error, ...data } = await usePost(currPost.links.reply, {
      name: replyForm.name.value,
      text: replyForm.text.value,
      replyTo: currPost.id,
    })
    if (error) {
      replyForm.text.classList.add('border-danger')
      console.log(error)
      replyForm.text.placeholder = error
    } else {
      replyForm.text.classList.remove('border-danger')
      replyForm.text.placeholder = 'reply...'
      replyForm.reset()
      addReplyDOM(data)
      currPost.replies.push(data)
    }
  }
})

// !! --- --- ---
// ?? --- --- ---

setInterval(async () => {
  const { data } = await useFetch(TOP_LEVEL_URI)
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
    const newPost = await useFetch(currPost.links.self)
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

// !! --- --- ---
// ?? --- --- ---

async function useFetch(endpoint) {
  const res = await fetch(BASE_URL + endpoint)
  return res.json()
}

async function usePost(endpoint, data = null) {
  const options = {
    method: 'POST',
  }
  if (data) {
    options.headers = { 'Content-Type': 'application/json' }
    options.body = JSON.stringify(data)
  }
  const res = await fetch(BASE_URL + endpoint, options)
  return res.json()
}
