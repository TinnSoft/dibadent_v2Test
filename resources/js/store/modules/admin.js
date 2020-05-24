import {
    SAVE_FILTER_GRAPH_ADMIN,
    SAVE_DEFAULT_TAB_POINTS,
    SAVE_CHAT_CONVERSATION_INDEX
} from "../mutation-types";

// state
export const state = {
    filter: "w",
    defaultTabPoints: "levels",
    chatCurrentConversationIndex: 0
};

// getters
export const getters = {
    filter: state => state.filter,
    defaultTabPoints: state => state.defaultTabPoints,
    chatCurrentConversationIndex: state => state.chatCurrentConversationIndex
};

// mutations
export const mutations = {
    [SAVE_FILTER_GRAPH_ADMIN](state, filter) {
        state.filter = filter;
    },

    [SAVE_DEFAULT_TAB_POINTS](state, payload) {
        state.defaultTabPoints = payload;
    },

    [SAVE_CHAT_CONVERSATION_INDEX](state, payload) {
        state.chatCurrentConversationIndex = payload;
    }
};

// actions
export const actions = {
    setGraphFilter({ commit, dispatch }, payload) {
        commit(SAVE_FILTER_GRAPH_ADMIN, payload);
    },

    setDefaultTabPoints({ commit, dispatch }, payload) {
        commit(SAVE_DEFAULT_TAB_POINTS, payload);
    },

    setChatCurrentConversationIndex({ commit, dispatch }, payload) {
        commit(SAVE_CHAT_CONVERSATION_INDEX, payload);
    }
};
