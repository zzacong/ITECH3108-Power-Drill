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
  post = {
    name: createPostForm['name'].value,
    text: createPostForm['text'].value,
  }

  const res = await fetch(BASE_URL + 'create.php', {
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
