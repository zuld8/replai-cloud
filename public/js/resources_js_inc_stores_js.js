"use strict";
(self["webpackChunkwhatsmail_pro_version"] = self["webpackChunkwhatsmail_pro_version"] || []).push([["resources_js_inc_stores_js"],{

/***/ "./resources/js/inc/stores.js":
/*!************************************!*\
  !*** ./resources/js/inc/stores.js ***!
  \************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm-bundler.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (new vuex__WEBPACK_IMPORTED_MODULE_0__["default"].Store({
  state: {
    activetab: "",
    server_url: "",
    contacts: []
  },
  mutations: {
    set_tab: function set_tab(state, payload) {
      state.activetab = payload;
    },
    set_url: function set_url(state, payload) {
      state.server_url = payload;
    },
    saving_contacts: function saving_contacts(state, payload) {
      state.contacts.push(payload);
    },
    saving_contact_list: function saving_contact_list(state, payload) {
      payload.forEach(function (newItem) {
        var existingIndex = state.contacts.findIndex(function (item) {
          return item.id === newItem.id;
        });
        if (existingIndex !== -1) {
          state.contacts.splice(existingIndex, 1);
        }
      });
      state.contacts = [].concat(_toConsumableArray(payload), _toConsumableArray(state.contacts.filter(function (item) {
        return !payload.some(function (newItem) {
          return newItem.id === item.id;
        });
      })));
    },
    update_contact_pict: function update_contact_pict(state, payload) {
      var userIndex = state.contacts.findIndex(function (i) {
        return payload.id == i.id;
      });
      if (userIndex !== -1) {
        state.contacts[userIndex].photo = payload.photo == null ? state.contacts[userIndex].photo : payload.photo;
        state.contacts[userIndex].getpict = true;
      }
    }
  },
  getters: {
    get_active_tab: function get_active_tab(state) {
      return state.activetab;
    },
    get_server_url: function get_server_url(state) {
      return state.server_url;
    },
    get_contacts: function get_contacts(state) {
      return state.contacts;
    }
  },
  actions: {
    set_tab: function set_tab(_ref, payload) {
      var commit = _ref.commit;
      return commit("set_tab", payload);
    },
    saving_contacts: function saving_contacts(_ref2, payload) {
      var commit = _ref2.commit;
      return commit("saving_contacts", payload);
    },
    saving_contact_list: function saving_contact_list(_ref3, payload) {
      var commit = _ref3.commit;
      return commit("saving_contact_list", payload);
    },
    update_contact_pict: function update_contact_pict(_ref4, payload) {
      var commit = _ref4.commit;
      return commit("update_contact_pict", payload);
    },
    set_url: function set_url(_ref5, payload) {
      var commit = _ref5.commit;
      return commit("set_url", payload);
    }
  }
}));

/***/ })

}]);