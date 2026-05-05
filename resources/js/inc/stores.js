import Vuex from "vuex";

export default new Vuex.Store({
    state: {
        activetab: "",
        server_url: "",
        contacts: [],
    },
    mutations: {
        set_tab(state, payload) {
            state.activetab = payload;
        },

        set_url(state, payload) {
            state.server_url = payload;
        },

        saving_contacts(state, payload) {
            state.contacts.push(payload);
        },

        saving_contact_list(state, payload) {
            payload.forEach((newItem) => {
                const existingIndex = state.contacts.findIndex(
                    (item) => item.id === newItem.id
                );
                if (existingIndex !== -1) {
                    state.contacts.splice(existingIndex, 1);
                }
            });

            state.contacts = [
                ...payload,
                ...state.contacts.filter(
                    (item) => !payload.some((newItem) => newItem.id === item.id)
                ),
            ];
        },

        update_contact_pict(state, payload) {
            var userIndex = state.contacts.findIndex(
                (i) => payload.id == i.id
            );

            if (userIndex !== -1) { 
                state.contacts[userIndex].photo = payload.photo == null ? state.contacts[userIndex].photo : payload.photo; 
                state.contacts[userIndex].getpict = true
            }  

        }
    },
    getters: {
        get_active_tab: (state) => state.activetab,
        get_server_url: (state) => state.server_url,
        get_contacts: (state) => state.contacts,
    },
    actions: {
        set_tab: ({ commit }, payload) => commit("set_tab", payload),
        saving_contacts: ({ commit }, payload) =>
            commit("saving_contacts", payload),
        saving_contact_list: ({ commit }, payload) =>
            commit("saving_contact_list", payload),
        update_contact_pict: ({ commit }, payload) =>
            commit("update_contact_pict", payload),
        set_url: ({ commit }, payload) => commit("set_url", payload),
    },
});
