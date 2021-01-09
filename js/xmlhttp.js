// const xhttp = new XMLHttpRequest()

// xhttp.onreadystatechange = function () {
//   if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
//     const { data } = JSON.parse(this.responseText)

//     data.forEach(post => {
//       const card = document.createElement('div')
//       card.className = 'card my-4 w-75'

//       const ul = document.createElement('ul')
//       ul.classList.add('list-group')
//       for (const [key, value] of Object.entries(post)) {
//         const li = document.createElement('li')
//         li.classList.add('list-group-item')
//         li.textContent = `${key}: ${value}`
//         ul.appendChild(li)
//       }

//       const cardFooter = document.createElement('div')
//       cardFooter.className = 'card-footer d-flex justify-content-end'

//       const btnLike = document.createElement('button')
//       btnLike.className = 'btn btn-small btn-primary '
//       btnLike.textContent = 'Like'
//       btnLike.addEventListener('click', () => {
//         const xhttp = new XMLHttpRequest()
//         xhttp.addEventListener('load', () => {
//           console.log(xhttp.responseText)
//         })
//         xhttp.open('PUT', 'api/like.php', true)
//         xhttp.setRequestHeader('Content-Type', 'application/json')
//         xhttp.send(JSON.stringify({ id: post.id }))
//       })

//       const btnUnlike = document.createElement('button')
//       btnUnlike.className = 'btn btn-small btn-danger mx-2'
//       btnUnlike.textContent = 'Unlike'
//       btnUnlike.addEventListener('click', () => {
//         const xhttp = new XMLHttpRequest()
//         xhttp.addEventListener('load', () => {
//           console.log(xhttp.responseText)
//         })
//         xhttp.open('PUT', 'api/unlike.php', true)
//         xhttp.setRequestHeader('Content-Type', 'application/json')
//         xhttp.send(JSON.stringify({ id: post.id }))
//       })

//       cardFooter.appendChild(btnLike)
//       cardFooter.appendChild(btnUnlike)
//       card.appendChild(ul)
//       card.appendChild(cardFooter)
//       root.appendChild(card)
//     })
//   }
// }
// xhttp.open('GET', '/grapevine/api/getAll.php', true)
// xhttp.send()
