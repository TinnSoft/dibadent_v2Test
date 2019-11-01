import store from '~/store'

export default (to, from, next) => {

  console.log('admin: ',store.getters['auth/user'])
  if (store.getters['auth/user'].profile.description !== 'ADMIN') {
    next({ name: 'home' })
  } else {
    next()
  }
}