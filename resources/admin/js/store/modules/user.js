import axios from 'axios'
// initial state
// shape: [{ id, quantity }]
const state = {
    client: null,
}

// getters
const getters = {
    currentUser: (state, getters, rootState) => {
        return state.client;
    }
}

// actions
const actions = {
    loadAuthUser({dispatch, state, commit}, options = {}) {
        const {withScope = []} = options;
        let params = {};
        
        // if (Array.isArray(withScope) && withScope.length) {
        //     params.with = withScope.join(',')
        // }

        return axios
            .get('/api/client', {
                params: params
            })
            .then(r => r.data)
            .then(user => {
                commit('setCurrentUser', user)
                return user;
            })
            .catch(error => {
                console.error('Failed to load auth user:', error);
                return null;
            })
    },
}

// mutations
const mutations = {
    setCurrentUser(state, user) {
        state.client = user
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}