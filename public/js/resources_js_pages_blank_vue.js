"use strict";
(self["webpackChunkwhatsmail_pro_version"] = self["webpackChunkwhatsmail_pro_version"] || []).push([["resources_js_pages_blank_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _assets_icons_new_message_png__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/assets/icons/new-message.png */ "./resources/js/assets/icons/new-message.png");
/* harmony import */ var _assets_icons_user_png__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/assets/icons/user.png */ "./resources/js/assets/icons/user.png");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {},
  data: function data() {
    return {
      contact: {
        search: "",
        phone: ""
      },
      attribute: {
        image: _assets_icons_new_message_png__WEBPACK_IMPORTED_MODULE_0__["default"],
        user: _assets_icons_user_png__WEBPACK_IMPORTED_MODULE_1__["default"],
        modal: false
      }
    };
  },
  computed: {},
  methods: {
    newChat: function newChat() {
      var modal = new bootstrap.Modal(this.$refs.newChatModal, {
        backdrop: "static",
        keyboard: false
      });
      modal.show();
    },
    newChatPhone: function newChatPhone() {
      this.closeModal();
      return this.$router.push({
        name: "chat_room",
        params: {
          id: this.$route.params.id,
          chatid: "".concat(this.contact.phone, "-s-whatsapp-net")
        },
        query: {
          name: this.contact.phone,
          photo: this.attribute.user
        }
      });
    },
    newChatContact: function newChatContact(detail) {
      this.closeModal();
      return this.$router.push({
        name: "chat_room",
        params: {
          id: this.$route.params.id,
          chatid: "".concat(detail.phone, "-s-whatsapp-net")
        },
        query: {
          name: detail.name,
          photo: detail.photo
        }
      });
    },
    closeModal: function closeModal() {
      var buttonClose = document.getElementById('closeModalNewChat');
      buttonClose.click();
    }
  },
  beforeDestroy: function beforeDestroy() {},
  updated: function updated() {},
  mounted: function mounted() {},
  watch: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

var _hoisted_1 = {
  "class": "welcome-content d-flex align-items-center justify-content-center"
};
var _hoisted_2 = ["src"];
var _hoisted_3 = {
  "class": "modal fade",
  id: "newChatModal",
  tabindex: "-1",
  "aria-labelledby": "filePreviewNewChatModal",
  "aria-hidden": "true",
  ref: "newChatModal"
};
var _hoisted_4 = {
  "class": "modal-dialog modal-dialog-centered"
};
var _hoisted_5 = {
  "class": "modal-content"
};
var _hoisted_6 = {
  "class": "modal-body"
};
var _hoisted_7 = {
  "class": "search-wrap contact-search mb-3"
};
var _hoisted_8 = {
  "class": "input-group"
};
var _hoisted_9 = {
  "class": "mb-3 fw-medium fs-16"
};
var _hoisted_10 = {
  "class": "contact-scroll contact-select mb-3"
};
var _hoisted_11 = {
  "class": "contact-user d-flex align-items-center justify-content-between"
};
var _hoisted_12 = {
  "class": "input-group"
};
var _hoisted_13 = {
  "class": "d-flex align-items-center"
};
var _hoisted_14 = {
  "class": "avatar avatar-lg"
};
var _hoisted_15 = ["src"];
var _hoisted_16 = {
  "class": "ms-2"
};
var _hoisted_17 = ["onClick"];
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
    src: $data.attribute.image,
    "class": "w-25"
  }, null, 8 /* PROPS */, _hoisted_2), _cache[5] || (_cache[5] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "welcome-info text-center"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "welcome-box bg-white d-inline-flex align-items-center mt-2"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", null, " Start New Message Or Select Chat available in history ")])], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "javascript:void(0);",
    "class": "btn btn-primary mt-2",
    onClick: _cache[0] || (_cache[0] = function ($event) {
      return $options.newChat();
    })
  }, _cache[4] || (_cache[4] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-location me-2"
  }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("Start New Message", -1 /* CACHED */)]))]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Chat Group "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_5, [_cache[9] || (_cache[9] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "modal-header"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h4", {
    "class": "modal-title"
  }, "New Message"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    type: "button",
    "class": "btn-close",
    id: "closeModalNewChat",
    "data-bs-dismiss": "modal",
    "aria-label": "Close"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-x"
  })])], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    type: "text",
    "class": "form-control",
    "onUpdate:modelValue": _cache[1] || (_cache[1] = function ($event) {
      return $data.contact.search = $event;
    }),
    placeholder: "Search Contacts...."
  }, null, 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.contact.search]]), _cache[6] || (_cache[6] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "javascript:void(0);",
    "class": "input-group-text"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-search"
  })], -1 /* CACHED */))])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", _hoisted_9, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.contact.search == "" || $data.contact.search == null ? "Contact List" : "Search Contact : " + $data.contact.search), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    type: "text",
    "class": "form-control",
    "onUpdate:modelValue": _cache[2] || (_cache[2] = function ($event) {
      return $data.contact.phone = $event;
    }),
    placeholder: "Enter Whatsapp Number"
  }, null, 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.contact.phone]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    onClick: _cache[3] || (_cache[3] = function ($event) {
      return $options.newChatPhone();
    }),
    href: "javascript:void(0);",
    "class": "input-group-text"
  }, _cache[7] || (_cache[7] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-send"
  }, null, -1 /* CACHED */)]))])]), ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.$store.getters.get_contacts.filter(function (item) {
    return item.name.toLowerCase().includes($data.contact.search.toLowerCase());
  }), function (cp, index) {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
      "class": "contact-user d-flex align-items-center justify-content-between",
      key: index
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: cp.photo,
      "class": "rounded-circle",
      alt: "image"
    }, null, 8 /* PROPS */, _hoisted_15)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_16, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(cp.name), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(cp.phone), 1 /* TEXT */)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
      "class": "btn btn-primary btn-sm",
      type: "button",
      onClick: function onClick($event) {
        return $options.newChatContact(cp);
      }
    }, _toConsumableArray(_cache[8] || (_cache[8] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-send"
    }, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_17)]);
  }), 128 /* KEYED_FRAGMENT */))])])])])], 512 /* NEED_PATCH */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Chat Group ")], 64 /* STABLE_FRAGMENT */);
}

/***/ }),

/***/ "./resources/js/assets/icons/new-message.png":
/*!***************************************************!*\
  !*** ./resources/js/assets/icons/new-message.png ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/new-message.png?eeb9c293f59f45fd140dd869efaaa41d");

/***/ }),

/***/ "./resources/js/pages/blank.vue":
/*!**************************************!*\
  !*** ./resources/js/pages/blank.vue ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _blank_vue_vue_type_template_id_e6bd208a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blank.vue?vue&type=template&id=e6bd208a */ "./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a");
/* harmony import */ var _blank_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blank.vue?vue&type=script&lang=js */ "./resources/js/pages/blank.vue?vue&type=script&lang=js");
/* harmony import */ var _Users_mdedehhidayatullah_Documents_myprojects_whatsmail_azira_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_Users_mdedehhidayatullah_Documents_myprojects_whatsmail_azira_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_blank_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_blank_vue_vue_type_template_id_e6bd208a__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"resources/js/pages/blank.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./resources/js/pages/blank.vue?vue&type=script&lang=js":
/*!**************************************************************!*\
  !*** ./resources/js/pages/blank.vue?vue&type=script&lang=js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_blank_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_blank_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./blank.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a":
/*!********************************************************************!*\
  !*** ./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_blank_vue_vue_type_template_id_e6bd208a__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_blank_vue_vue_type_template_id_e6bd208a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./blank.vue?vue&type=template&id=e6bd208a */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/blank.vue?vue&type=template&id=e6bd208a");


/***/ })

}]);