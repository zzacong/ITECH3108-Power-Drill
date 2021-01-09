const root = document.querySelector('#posts')

async function main() {
  const res = await fetch('api/getAll.php')
  const { data } = await res.json()
  root.innerHTML = ''

  // ? ----------
  // ? ----------

  const sortedData = data.sort((a, b) => {
    const d1 = new Date(a.postDate)
    const d2 = new Date(b.postDate)
    return d2 - d1
  })

  // ? ----------
  // ? ----------
  for (const post of sortedData) {
    const card = document.createElement('div')
    card.className = 'card my-4'

    const cardPost = document.createElement('div')
    cardPost.className = 'card-body'

    for (const key in post) {
      const p = document.createElement('p')
      p.className = 'mb-1'
      p.textContent = `${key}: ${post[key]}`
      cardPost.appendChild(p)
    }

    // ? --------
    // ? --------
    // ? --------
    // ? --------

    const cardReply = document.createElement('div')
    cardReply.className = 'card-body d-none reply'

    const res = await fetch(`api/get.php?id=${post.id}`)
    const { data } = await res.json()

    const ul = document.createElement('ul')
    ul.classList.add('list-group')

    const sortedReply = data
      .filter(post => post.replyTo !== null)
      .sort((r1, r2) => {
        const d1 = new Date(r1.postDate)
        const d2 = new Date(r2.postDate)
        return d2 - d1
      })

    for (reply of sortedReply) {
      const { id } = reply
      const li = document.createElement('li')
      li.classList.add('list-group-item')

      for (const key in reply) {
        const p = document.createElement('p')
        p.className = 'mb-1'
        p.textContent = `${key}: ${reply[key]}`
        li.appendChild(p)
      }
      const btnLikee = document.createElement('button')
      btnLikee.className = 'btn btn-sm btn-primary'
      btnLikee.textContent = 'Like'
      btnLikee.addEventListener('click', async () => {
        await like(id)
        main()
      })

      const btnUnlikee = document.createElement('button')
      btnUnlikee.className = 'btn btn-sm btn-danger mx-2'
      btnUnlikee.textContent = 'Unlike'
      btnUnlikee.addEventListener('click', async () => {
        await unlike(id)
        main()
      })

      const div = document.createElement('div')
      div.className = 'd-flex justify-content-end mt-2'

      div.append(btnLikee, btnUnlikee)
      li.appendChild(div)
      ul.appendChild(li)
    }

    cardReply.appendChild(ul)

    // ? --------
    // ? --------
    // ? --------

    const replySec = document.createElement('div')
    replySec.className = 'container mt-3'

    replySec.innerHTML = `
    <form class="row">
      <div class="col-2 px-0">
        <input type="text" name="name" placeholder="name" class="form-control">
      </div>
      <div class="col-9">
        <input type="text" name="reply" placeholder="reply..." class="form-control">
      </div>
      <div class="col-1 px-0">
        <button type="submit" class="btn btn-primary">Reply</button>
      </div> 
    </form>
    `

    cardReply.appendChild(replySec)

    // ? --------
    // ? --------
    // ? --------

    const replyForm = replySec.querySelector('form')
    replyForm.addEventListener('submit', async e => {
      e.preventDefault()
      await fetch('api/create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          name: replyForm.name.value,
          text: replyForm.reply.value,
          replyTo: post.id,
        }),
      })
      main()
    })

    // ? --------
    // ? --------
    // ? --------

    const cardFooter = document.createElement('div')
    cardFooter.className = 'card-footer d-flex justify-content-between'

    const btnLike = document.createElement('button')
    btnLike.className = 'btn btn-sm btn-primary'
    btnLike.textContent = 'Like'
    btnLike.addEventListener('click', async () => {
      await like(post.id)
      main()
    })

    const btnUnlike = document.createElement('button')
    btnUnlike.className = 'btn btn-sm btn-danger mx-2'
    btnUnlike.textContent = 'Unlike'
    btnUnlike.addEventListener('click', async () => {
      await unlike(post.id)
      main()
    })

    const btnView = document.createElement('button')
    btnView.className = 'btn btn-sm btn-success mx-2'
    btnView.textContent = 'View'
    btnView.addEventListener('click', () => {
      btnView.textContent = btnView.textContent === 'View' ? 'Hide' : 'View'
      card.querySelector('.reply').classList.toggle('d-none')
    })

    const btnGroup = document.createElement('div')
    btnGroup.append(btnLike, btnUnlike)

    cardFooter.append(btnGroup, btnView)
    card.append(cardPost, cardReply, cardFooter)
    root.appendChild(card)
  }
}

main()

// ? ----------
// ? ----------
// ? ----------

const btnCreate = document.querySelector('#btnCreate')
const form = document.querySelector('#formCreate')

btnCreate.addEventListener('click', () => {
  form.classList.toggle('d-none')
})

form.addEventListener('submit', async e => {
  e.preventDefault()
  console.log(form.name.value)
  console.log(form.text.value)

  post = {
    name: form['name'].value,
    text: form['text'].value,
  }
  const res = await fetch('api/create.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(post),
  })
  const data = await res.json()
  if (data.data) {
    form.classList.toggle('d-none')
    return main()
  }
  form.querySelector('#error').textContent = data.message
})

async function like(id) {
  return fetch('api/like.php', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id }),
  })
}

async function unlike(id) {
  return fetch('api/unlike.php', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id }),
  })
}
