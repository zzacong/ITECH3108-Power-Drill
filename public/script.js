const BASE_URL = 'http://localhost:8080/powerdrill/api/'

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
  const newPost = {
    name: createPostForm['name'].value,
    text: createPostForm['text'].value,
  }
  const { data, error } = await usePost('create.php', newPost)
  if (error) return (createPostForm.querySelector('#error').textContent = error)

  createPostButton.classList.toggle('btn-outline-success')
  createPostButton.classList.toggle('btn-outline-secondary')
  createPostButton.textContent = 'Create Post'

  createPostForm.classList.toggle('d-none')
  createPostForm.reset()
  addPostDOM(data)
})

// !! --- --- ---
// ?? --- --- ---

const topLevelPostList = document.querySelector('#topLevelPostList')

async function init() {
  const { data } = await useFetch('getTopLevel.php')
  data.forEach(data => {
    addPostDOM(data)
  })
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
  return usePut('like.php', { id })
}

async function unlike(id) {
  return usePut('unlike.php', { id })
}

function addPostDOM({ id, name, postDate, likes, text }) {
  const postCard = document.querySelector('#postTemplate').cloneNode(true)

  postCard.setAttribute('id', `post-${id}`)
  postCard.querySelector('.post-id').textContent = id
  postCard.querySelector('.post-name').textContent = name
  postCard.querySelector('.post-date').textContent = postDate
  postCard.querySelector('.post-text').textContent = text
  const postLikes = postCard.querySelector('.post-likes')
  postLikes.textContent = likes
  postCard.classList.remove('d-none')
  topLevelPostList.appendChild(postCard)

  const btnLike = postCard.querySelector('.btn-like')
  const btnUnlike = postCard.querySelector('.btn-unlike')
  const btnView = postCard.querySelector('.btn-view')

  btnLike.addEventListener('click', async () => {
    const { data } = await like(id)
    if (data) postLikes.textContent = data.likes
  })
  btnUnlike.addEventListener('click', async () => {
    const { data } = await unlike(id)
    if (data) postLikes.textContent = data.likes
  })
  btnView.addEventListener('click', () => {
    document.querySelector('#replySection').classList.remove('d-none')
    document.querySelector('#replyToId').textContent = id
    document
      .querySelectorAll('.reply-list')
      .forEach(list => list.classList.add('d-none'))
    document.querySelector(`#replyList-${id}`).classList.remove('d-none')
  })

  const replyList = document.querySelector('#replyListTemplate').cloneNode(true)
  replyList.setAttribute('id', `replyList-${id}`)
  document.querySelector('#replyLists').appendChild(replyList)
  populateReplies(id, replyList)
  return postCard
}

async function populateReplies(id, replyList) {
  const { data } = await useFetch(`get.php?id=${id}`)
  data
    .filter(reply => reply.replyTo !== null)
    .forEach(reply => addReplyDOM(replyList, reply))
}

function addReplyDOM(replyList, { id, name, postDate, replyTo, likes, text }) {
  const replyItem = document.querySelector('#replyItemTemplate').cloneNode(true)
  replyItem.classList.remove('d-none')
  replyItem.querySelector('.reply-id').textContent = id
  replyItem.querySelector('.reply-name').textContent = name
  replyItem.querySelector('.reply-date').textContent = postDate
  replyItem.querySelector('.reply-to').textContent = replyTo
  replyItem.querySelector('.reply-text').textContent = text
  const replyLikes = replyItem.querySelector('.reply-likes')
  replyLikes.textContent = likes
  replyList.appendChild(replyItem)

  const btnLike = replyItem.querySelector('.btn-like')
  const btnUnlike = replyItem.querySelector('.btn-unlike')

  btnLike.addEventListener('click', async () => {
    const { data } = await like(id)
    if (data) replyLikes.textContent = data.likes
  })
  btnUnlike.addEventListener('click', async () => {
    const { data } = await unlike(id)
    if (data) replyLikes.textContent = data.likes
  })
}
