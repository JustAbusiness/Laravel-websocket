import Axios from 'axios'
import { createStore } from 'vuex'


export default createStore ({
  state: () => ({
    userList: [],
    userMessage: []
  }),
  mutations: {
    setUserList (state, payload) {
      return (state.userList = payload)
    },
    userMessage (state, payload) {
      return (state.userMessage = payload)
    },
    setUserList(state, userList) {
      state.userList = userList;
  },
  },
  actions: {
    userList (context) {
      Axios.get('/userlist').then(response => {
        context.commit('setUserList', response.data)
      })
    },
    userMessage (context, payload) {
      Axios.get('/usermessage/' + payload).then(response => {
        context.commit('userMessage', response.data)
      })
    }
  },
  getters: {
    userList (state) {
      return state.userList
    },
    userMessage (state) {
      return state.userMessage
    }
  }
});


