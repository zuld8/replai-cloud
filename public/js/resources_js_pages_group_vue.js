"use strict";
(self["webpackChunkwhatsmail_pro_version"] = self["webpackChunkwhatsmail_pro_version"] || []).push([["resources_js_pages_group_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _assets_icons_image_png__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/assets/icons/image.png */ "./resources/js/assets/icons/image.png");
/* harmony import */ var _assets_icons_audio_png__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/assets/icons/audio.png */ "./resources/js/assets/icons/audio.png");
/* harmony import */ var _assets_icons_video_png__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/assets/icons/video.png */ "./resources/js/assets/icons/video.png");
/* harmony import */ var _assets_icons_documents_png__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @/assets/icons/documents.png */ "./resources/js/assets/icons/documents.png");
/* harmony import */ var _assets_icons_map_png__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @/assets/icons/map.png */ "./resources/js/assets/icons/map.png");
/* harmony import */ var _assets_icons_user_png__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @/assets/icons/user.png */ "./resources/js/assets/icons/user.png");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! nprogress */ "./node_modules/nprogress/nprogress.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(nprogress__WEBPACK_IMPORTED_MODULE_6__);
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
var _methods;
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }







/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {},
  data: function data() {
    return {
      intervalId: null,
      chatId: null,
      camera: {
        modal: false,
        picture: null,
        stream: null
      },
      fileTypes: ".pdf, .doc, .docx, .xlsx, .zip, .html, .php, .css, .js, .ppt, .txt",
      attribute: {
        image: _assets_icons_image_png__WEBPACK_IMPORTED_MODULE_0__["default"],
        audio: _assets_icons_audio_png__WEBPACK_IMPORTED_MODULE_1__["default"],
        video: _assets_icons_video_png__WEBPACK_IMPORTED_MODULE_2__["default"],
        document: _assets_icons_documents_png__WEBPACK_IMPORTED_MODULE_3__["default"],
        location: _assets_icons_map_png__WEBPACK_IMPORTED_MODULE_4__["default"],
        user: _assets_icons_user_png__WEBPACK_IMPORTED_MODULE_5__["default"],
        modal: false
      },
      message: {
        list: [],
        loader: true,
        last_chat: "",
        search: ""
      },
      send: {
        loader: false,
        type: "text",
        text: "",
        file: null,
        location: {
          "long": "",
          lang: ""
        }
      },
      file: {
        file: null,
        filePreview: null,
        previewType: "",
        fileName: "",
        isPDF: false
      }
    };
  },
  computed: {},
  methods: (_methods = {
    getPhotoPict: function getPhotoPict(phone) {
      var userIndex = this.$store.getters.get_contacts.findIndex(function (i) {
        return phone == i.phone;
      });
      if (userIndex !== -1) {
        return this.$store.getters.get_contacts[userIndex].url;
      } else {
        return this.attribute.user;
      }
    },
    openModalCam: function openModalCam() {
      var _this = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var modal;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.n) {
            case 0:
              _this.camera.modal = true;
              modal = new bootstrap.Modal(_this.$refs.cameraModal, {
                backdrop: "static",
                keyboard: false
              });
              modal.show();
              _context.n = 1;
              return _this.openCamera();
            case 1:
              return _context.a(2);
          }
        }, _callee);
      }))();
    },
    openCamera: function openCamera() {
      var _this2 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2() {
        var _t;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.p = _context2.n) {
            case 0:
              _context2.p = 0;
              _context2.n = 1;
              return navigator.mediaDevices.getUserMedia({
                video: true
              });
            case 1:
              _this2.camera.stream = _context2.v;
              _this2.$refs.videoCamera.srcObject = _this2.camera.stream;
              _context2.n = 3;
              break;
            case 2:
              _context2.p = 2;
              _t = _context2.v;
              console.error("Kamera tidak dapat diakses", _t);
            case 3:
              return _context2.a(2);
          }
        }, _callee2, null, [[0, 2]]);
      }))();
    },
    capturePhoto: function capturePhoto() {
      var canvas = this.$refs.canvas;
      var video = this.$refs.videoCamera;
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      var context = canvas.getContext("2d");
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      this.camera.picture = canvas.toDataURL("image/jpeg");
      if (this.camera.stream) {
        var tracks = this.camera.stream.getTracks();
        tracks.forEach(function (track) {
          return track.stop();
        });
      }
    },
    reTakePhoto: function reTakePhoto() {
      var _this3 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee3() {
        return _regenerator().w(function (_context3) {
          while (1) switch (_context3.n) {
            case 0:
              _this3.camera = {
                modal: false,
                picture: null,
                stream: null
              };
              _this3.openCamera();
            case 1:
              return _context3.a(2);
          }
        }, _callee3);
      }))();
    },
    sendPhoto: function sendPhoto() {
      var _this4 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee4() {
        var tracks, modal;
        return _regenerator().w(function (_context4) {
          while (1) switch (_context4.n) {
            case 0:
              _this4.send.type = "photo";
              _this4.send.file = _this4.camera.picture;
              if (_this4.camera.stream) {
                tracks = _this4.camera.stream.getTracks();
                tracks.forEach(function (track) {
                  return track.stop();
                });
              }
              _context4.n = 1;
              return _this4.sendMessage();
            case 1:
              _this4.camera = {
                modal: false,
                picture: null,
                stream: null
              };
              modal = document.getElementById("closeModalCamera");
              modal.click();
            case 2:
              return _context4.a(2);
          }
        }, _callee4);
      }))();
    },
    closeModal: function closeModal() {},
    confirmFile: function confirmFile() {
      var _this5 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee5() {
        return _regenerator().w(function (_context5) {
          while (1) switch (_context5.n) {
            case 0:
              _context5.n = 1;
              return _this5.sendMessage();
            case 1:
              _this5.closeModal();
            case 2:
              return _context5.a(2);
          }
        }, _callee5);
      }))();
    },
    closeCamera: function closeCamera() {
      if (this.camera.stream) {
        var tracks = this.camera.stream.getTracks();
        tracks.forEach(function (track) {
          return track.stop();
        });
      }
      this.camera = {
        modal: false,
        picture: null,
        stream: null
      };
    },
    triggerFileInput: function triggerFileInput() {
      this.$refs.fileInput.click();
    },
    setFileType: function setFileType(types) {
      var _this6 = this;
      this.fileTypes = types;
      setTimeout(function () {
        _this6.triggerFileInput();
      }, 1000);
    },
    handleFileChange: function handleFileChange(event) {
      var file = event.target.files[0];
      if (file) {
        this.file.fileName = file.name;
        this.send.file = file;
        if (file.type.startsWith("image")) {
          this.file.previewType = "image";
          this.file.filePreview = URL.createObjectURL(file);
          this.send.type = "media";
        } else if (file.type.startsWith("video")) {
          this.file.previewType = "video";
          this.file.filePreview = URL.createObjectURL(file);
          this.send.type = "media";
        } else if (file.type.startsWith("audio")) {
          this.file.previewType = "audio";
          this.file.filePreview = URL.createObjectURL(file);
          this.send.type = "media";
        } else if (file.type === "application/pdf") {
          this.file.previewType = "document";
          this.send.type = "document";
          this.file.filePreview = URL.createObjectURL(file);
          this.file.isPDF = true;
        } else {
          this.send.type = "document";
          this.file.previewType = "document";
          this.file.filePreview = null;
          this.file.isPDF = false;
        }

        // Show the modal
        var modal = new bootstrap.Modal(this.$refs.fileModal, {
          backdrop: "static",
          // mencegah modal tertutup dengan klik di luar
          keyboard: false // mencegah modal tertutup dengan tombol Esc
        });
        modal.show();
      }
    }
  }, _defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_methods, "closeModal", function closeModal() {
    var modal = document.getElementById("closeModal");
    modal.click();
  }), "confirmFile", function confirmFile() {
    var _this7 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee6() {
      return _regenerator().w(function (_context6) {
        while (1) switch (_context6.n) {
          case 0:
            _context6.n = 1;
            return _this7.sendMessage();
          case 1:
            _this7.closeModal();
          case 2:
            return _context6.a(2);
        }
      }, _callee6);
    }))();
  }), "resetFile", function resetFile() {
    document.getElementById("files").value = "";
    this.send.type = "text";
    this.send.file = null;
    this.file.filePreview = null;
    this.file.fileName = "";
    this.file.previewType = "";
    this.file.isPDF = false;
  }), "getMessages", function getMessages() {
    var _arguments = arguments,
      _this8 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee7() {
      var lastChat, _this8$message$list, response, data, listData, _t2;
      return _regenerator().w(function (_context7) {
        while (1) switch (_context7.p = _context7.n) {
          case 0:
            lastChat = _arguments.length > 0 && _arguments[0] !== undefined ? _arguments[0] : "";
            _context7.p = 1;
            _context7.n = 2;
            return _this8.$axios.get("/device/chats/detail/".concat(_this8.$route.params.id, "/").concat(_this8.$route.params.chatid, "?last_id=").concat(lastChat, "&is_group=true"));
          case 2:
            response = _context7.v;
            data = response.data;
            listData = Array.isArray(data.list) ? data.list : Object.values(data.list);
            listData = listData.filter(function (newItem) {
              return !_this8.message.list.some(function (existingItem) {
                return existingItem.id === newItem.id;
              });
            });
            _this8.chatId = data.chatid;
            _this8.message.loader = false;
            _this8.message.last_chat = data.last_chat;
            (_this8$message$list = _this8.message.list).push.apply(_this8$message$list, _toConsumableArray(listData));

            // function for mark to read message
            if (listData.length > 0) {
              _this8.scrollToBottom();
            }
            _context7.n = 4;
            break;
          case 3:
            _context7.p = 3;
            _t2 = _context7.v;
            _this8.$handleErrorResponse(_t2);
            console.log(_t2);
          case 4:
            return _context7.a(2);
        }
      }, _callee7, null, [[1, 3]]);
    }))();
  }), "deleteMessage", function deleteMessage(detail) {
    var _this9 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee8() {
      var messageIndex, _t3;
      return _regenerator().w(function (_context8) {
        while (1) switch (_context8.p = _context8.n) {
          case 0:
            _context8.p = 0;
            _context8.n = 1;
            return _this9.$axios.post("/device/misc/delete-message/".concat(_this9.$route.params.id), {
              chatid: _this9.chatId,
              message: {
                key: {
                  remoteJid: _this9.chatId,
                  fromMe: detail.sender,
                  id: detail.id
                },
                deleteMedia: detail.type == "text" && detail.type == "location" ? false : true,
                timestamp: detail.timestamp
              }
            }, {
              withCredentials: false
            });
          case 1:
            messageIndex = _this9.message.list.findIndex(function (i) {
              return detail.id == i.id;
            });
            if (messageIndex !== -1) {
              _this9.message.list.splice(messageIndex, 1);
            }
            _context8.n = 3;
            break;
          case 2:
            _context8.p = 2;
            _t3 = _context8.v;
            console.log(_t3);
          case 3:
            return _context8.a(2);
        }
      }, _callee8, null, [[0, 2]]);
    }))();
  }), "deleteEveryOne", function deleteEveryOne(detail) {
    var _this0 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee9() {
      var messageIndex, _t4;
      return _regenerator().w(function (_context9) {
        while (1) switch (_context9.p = _context9.n) {
          case 0:
            _context9.p = 0;
            _context9.n = 1;
            return _this0.$axios.post("/device/misc/delete-everyone/".concat(_this0.$route.params.id), {
              chatid: _this0.chatId,
              message: {
                remoteJid: _this0.chatId,
                fromMe: detail.sender,
                id: detail.id
              }
            }, {
              withCredentials: false
            });
          case 1:
            messageIndex = _this0.message.list.findIndex(function (i) {
              return detail.id == i.id;
            });
            if (messageIndex !== -1) {
              _this0.message.list.splice(messageIndex, 1);
            }
            _context9.n = 3;
            break;
          case 2:
            _context9.p = 2;
            _t4 = _context9.v;
            console.log(_t4);
          case 3:
            return _context9.a(2);
        }
      }, _callee9, null, [[0, 2]]);
    }))();
  }), "formattedText", function formattedText(message) {
    return message.replace(/\n/g, "<br>");
  }), "scrollToBottom", function scrollToBottom() {
    var messageContent = document.querySelector(".chat-area");
    if (messageContent) {
      messageContent.scrollTop = messageContent.scrollHeight;
    }
  }), "startPolling", function startPolling() {
    var _this1 = this;
    if (this.intervalId) clearInterval(this.intervalId);
    this.intervalId = setInterval(function () {
      if (_this1.$route.name == "group_room") {
        if (!_this1.message.loader) {
          _this1.getMessages(_this1.message.last_chat);
        }
      } else {
        _this1.stopPolling();
      }
    }, 5000);
  }), "stopPolling", function stopPolling() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
    }
  }), _defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_defineProperty(_methods, "downloadMedia", function downloadMedia(message, name, mime) {
    var _this10 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee0() {
      var response, data, findMessage, _t5;
      return _regenerator().w(function (_context0) {
        while (1) switch (_context0.p = _context0.n) {
          case 0:
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().start();
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().set(0.1);
            _context0.p = 1;
            _context0.n = 2;
            return _this10.$axios.post("/device/misc/download-media/".concat(_this10.$route.params.id), {
              type: mime,
              medianame: name,
              message: message
            }, {
              withCredentials: false
            });
          case 2:
            response = _context0.v;
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().done();
            data = response.data;
            _this10.$showToast("Berhasil mengunduh media", "info", 3000);
            if (response.status == 200) {
              findMessage = _this10.message.list.findIndex(function (i) {
                return name == i.id;
              });
              if (findMessage !== -1) {
                _this10.message.list[findMessage].message.detail.url = data.data.downloadPath;
                _this10.message.list[findMessage].message.detail.asset = true;
              }
            }
            _context0.n = 4;
            break;
          case 3:
            _context0.p = 3;
            _t5 = _context0.v;
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().done();
            _this10.$showToast("Media gagal di unduh", "info", 3000);
          case 4:
            return _context0.a(2);
        }
      }, _callee0, null, [[1, 3]]);
    }))();
  }), "autoResize", function autoResize() {
    var textarea = this.$refs.messageInput;
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  }), "autoResizeModal", function autoResizeModal() {
    var textarea = this.$refs.messageInputModal;
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  }), "handleEnter", function handleEnter(event) {
    if (!event.shiftKey) {
      event.preventDefault();
      this.sendMessage();
    }
  }), "autoResizeCamera", function autoResizeCamera() {
    var textarea = this.$refs.messageInputCamera;
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  }), "insertLineBreak", function insertLineBreak() {
    var _this11 = this;
    this.send.text += "\n";
    this.$nextTick(function () {
      _this11.autoResize();
    });
  }), "sendMessage", function sendMessage() {
    var _this12 = this;
    return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee1() {
      var formData, response, _t6;
      return _regenerator().w(function (_context1) {
        while (1) switch (_context1.p = _context1.n) {
          case 0:
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().start();
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().set(0.1);
            _this12.send.loader = true;
            _context1.p = 1;
            formData = new FormData();
            formData.append("type", _this12.send.type);
            formData.append("isgroup", true);
            formData.append("phone", _this12.$route.params.chatid);
            formData.append("text", _this12.send.text);
            if (_this12.send.file) {
              if (_this12.send.type == "photo") {
                formData.append("photo", _this12.send.file);
              } else {
                formData.append("file", _this12.send.file);
              }
            }
            _context1.n = 2;
            return _this12.$axios.post("device/chats/send/".concat(_this12.$route.params.id), formData, {
              headers: {
                "Content-Type": "multipart/form-data"
              }
            });
          case 2:
            response = _context1.v;
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().done();
            _this12.send = {
              loader: false,
              type: "text",
              text: "",
              file: null,
              location: {
                "long": "",
                lang: ""
              }
            };
            _this12.resetFile();
            _this12.$showToast("Berhasil mengirim pesan", "info", 3000);
            _context1.n = 4;
            break;
          case 3:
            _context1.p = 3;
            _t6 = _context1.v;
            nprogress__WEBPACK_IMPORTED_MODULE_6___default().done();
            _this12.send.loader = false;
            _this12.$handleErrorResponse(_t6);
          case 4:
            return _context1.a(2);
        }
      }, _callee1, null, [[1, 3]]);
    }))();
  })),
  beforeDestroy: function beforeDestroy() {
    this.stopPolling();
  },
  updated: function updated() {
    this.scrollToBottom();
  },
  mounted: function mounted() {
    var _this13 = this;
    $(".chat-search-btn").on("click", function () {
      $(".chat-search").toggleClass("visible-chat");
    });
    $(".close-btn-chat").on("click", function () {
      $(".chat-search").removeClass("visible-chat");
    });
    $("#closeModal").on("click", function () {
      _this13.resetFile();
    });
    $("#closeModalCamera").on("click", function () {
      _this13.closeCamera();
    });
  },
  watch: {
    "$route.params.chatid": {
      handler: function handler() {
        var _this14 = this;
        this.stopPolling();
        if (this.$route.name == "group_room") {
          this.message = {
            list: [],
            loader: true,
            last_chat: "",
            search: ""
          };
          this.getMessages("").then(function () {
            _this14.startPolling();
          });
        }
        this.message = {
          list: [],
          loader: true,
          last_chat: "",
          search: ""
        };
        this.getMessages("").then(function () {
          _this14.startPolling();
        });
      },
      immediate: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=template&id=5be37134":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=template&id=5be37134 ***!
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
  "class": "chat chat-messages show",
  id: "middle"
};
var _hoisted_2 = {
  "class": "chat-header"
};
var _hoisted_3 = {
  "class": "user-details"
};
var _hoisted_4 = {
  "class": "avatar avatar-lg online flex-shrink-0"
};
var _hoisted_5 = ["src"];
var _hoisted_6 = {
  "class": "ms-2 overflow-hidden"
};
var _hoisted_7 = {
  "class": "chat-options"
};
var _hoisted_8 = {
  "class": "dropdown-menu dropdown-menu-end p-3"
};
var _hoisted_9 = {
  "class": "chat-search search-wrap contact-search"
};
var _hoisted_10 = {
  "class": "input-group"
};
var _hoisted_11 = {
  "class": "chat-body chat-page-group slimscroll"
};
var _hoisted_12 = {
  key: 0,
  "class": "d-flex justify-content-center"
};
var _hoisted_13 = {
  "class": "messages chat-area",
  style: {
    "overflow": "scroll",
    "height": "80vh"
  }
};
var _hoisted_14 = {
  "class": "chat-avatar"
};
var _hoisted_15 = ["src"];
var _hoisted_16 = {
  "class": "chat-content"
};
var _hoisted_17 = {
  "class": "chat-time"
};
var _hoisted_18 = {
  key: 0,
  "class": "msg-read success"
};
var _hoisted_19 = {
  "class": "chat-info"
};
var _hoisted_20 = {
  key: 0,
  "class": "chat-img"
};
var _hoisted_21 = {
  "class": "img-wrap"
};
var _hoisted_22 = ["src"];
var _hoisted_23 = {
  "class": "img-overlay"
};
var _hoisted_24 = ["onClick"];
var _hoisted_25 = ["href", "title"];
var _hoisted_26 = {
  key: 1,
  "class": "chat-img"
};
var _hoisted_27 = {
  "class": "img-wrap"
};
var _hoisted_28 = ["src"];
var _hoisted_29 = {
  "class": "img-overlay"
};
var _hoisted_30 = ["onClick"];
var _hoisted_31 = {
  key: 2,
  "class": "message-video"
};
var _hoisted_32 = {
  controls: ""
};
var _hoisted_33 = ["src"];
var _hoisted_34 = {
  key: 3,
  "class": "chat-img"
};
var _hoisted_35 = {
  "class": "img-wrap"
};
var _hoisted_36 = ["src"];
var _hoisted_37 = {
  "class": "img-overlay"
};
var _hoisted_38 = ["onClick"];
var _hoisted_39 = {
  key: 4,
  "class": "message-audio"
};
var _hoisted_40 = {
  controls: ""
};
var _hoisted_41 = ["src"];
var _hoisted_42 = {
  key: 5,
  "class": "chat-img"
};
var _hoisted_43 = {
  "class": "img-wrap"
};
var _hoisted_44 = ["src"];
var _hoisted_45 = {
  "class": "img-overlay"
};
var _hoisted_46 = ["onClick"];
var _hoisted_47 = {
  key: 6,
  "class": "file-attach"
};
var _hoisted_48 = {
  "class": "ms-2 overflow-hidden"
};
var _hoisted_49 = {
  "class": "mb-1"
};
var _hoisted_50 = ["href"];
var _hoisted_51 = {
  key: 7,
  "class": "chat-img"
};
var _hoisted_52 = {
  "class": "img-wrap"
};
var _hoisted_53 = ["src"];
var _hoisted_54 = {
  "class": "img-overlay"
};
var _hoisted_55 = ["href"];
var _hoisted_56 = ["innerHTML"];
var _hoisted_57 = {
  key: 8,
  "class": "message-link"
};
var _hoisted_58 = ["href"];
var _hoisted_59 = {
  "class": "chat-actions"
};
var _hoisted_60 = {
  "class": "dropdown-menu dropdown-menu-end p-3"
};
var _hoisted_61 = ["onClick"];
var _hoisted_62 = {
  key: 0
};
var _hoisted_63 = ["onClick"];
var _hoisted_64 = {
  "class": "chat-footer"
};
var _hoisted_65 = {
  "class": "footer-form"
};
var _hoisted_66 = {
  "class": "chat-footer-wrap"
};
var _hoisted_67 = {
  "class": "form-wrap"
};
var _hoisted_68 = ["disabled"];
var _hoisted_69 = {
  "class": "form-item position-relative d-flex align-items-center justify-content-center"
};
var _hoisted_70 = ["accept"];
var _hoisted_71 = {
  "class": "form-item"
};
var _hoisted_72 = {
  "class": "dropdown-menu dropdown-menu-end p-3"
};
var _hoisted_73 = {
  "class": "form-btn"
};
var _hoisted_74 = ["disabled"];
var _hoisted_75 = {
  "class": "modal fade",
  id: "previewmodal",
  tabindex: "-1",
  "aria-labelledby": "filePreviewModalLabel",
  "aria-hidden": "true",
  ref: "fileModal"
};
var _hoisted_76 = {
  "class": "modal-dialog modal-dialog-centered modal-lg"
};
var _hoisted_77 = {
  "class": "modal-content"
};
var _hoisted_78 = {
  "class": "modal-header"
};
var _hoisted_79 = {
  "class": "modal-title"
};
var _hoisted_80 = {
  "class": "modal-body row"
};
var _hoisted_81 = {
  key: 0,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_82 = ["src"];
var _hoisted_83 = {
  key: 1,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_84 = {
  controls: ""
};
var _hoisted_85 = ["src"];
var _hoisted_86 = {
  key: 2,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_87 = {
  controls: ""
};
var _hoisted_88 = ["src"];
var _hoisted_89 = {
  key: 3,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_90 = ["src"];
var _hoisted_91 = ["src"];
var _hoisted_92 = {
  "class": "col-12 mt-4"
};
var _hoisted_93 = ["disabled"];
var _hoisted_94 = {
  "class": "modal-footer"
};
var _hoisted_95 = ["disabled"];
var _hoisted_96 = {
  "class": "modal fade",
  id: "cameraModal",
  tabindex: "-1",
  "aria-labelledby": "filePreviewModalLabel",
  "aria-hidden": "true",
  ref: "cameraModal"
};
var _hoisted_97 = {
  "class": "modal-dialog modal-dialog-centered modal-lg"
};
var _hoisted_98 = {
  "class": "modal-content"
};
var _hoisted_99 = {
  "class": "modal-body row"
};
var _hoisted_100 = {
  key: 0,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_101 = {
  ref: "videoCamera",
  autoplay: ""
};
var _hoisted_102 = {
  ref: "canvas",
  style: {
    "display": "none"
  }
};
var _hoisted_103 = {
  key: 1,
  "class": "col-12 d-flex justify-content-center"
};
var _hoisted_104 = ["src"];
var _hoisted_105 = {
  "class": "col-12 mt-4"
};
var _hoisted_106 = ["disabled"];
var _hoisted_107 = {
  "class": "modal-footer"
};
var _hoisted_108 = ["disabled"];
function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_router_link = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("router-link");
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [_cache[22] || (_cache[22] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "d-xl-none"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    "class": "text-muted chat-close me-2",
    href: "#"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "fas fa-arrow-left"
  })])], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
    src: _ctx.$route.query.photo,
    "class": "rounded-circle",
    alt: "image"
  }, null, 8 /* PROPS */, _hoisted_5)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.$route.query.name), 1 /* TEXT */), _cache[21] || (_cache[21] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
    "class": "last-seen"
  }, "-", -1 /* CACHED */))])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", null, [_cache[25] || (_cache[25] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "javascript:void(0)",
    "class": "btn chat-search-btn",
    "data-bs-toggle": "tooltip",
    "data-bs-placement": "bottom",
    title: "Search"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-search"
  })])], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [_cache[24] || (_cache[24] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    "class": "btn no-bg",
    href: "#",
    "data-bs-toggle": "dropdown"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-dots-vertical"
  })], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {
    to: {
      name: 'blank_chat',
      params: {
        id: _ctx.$route.params.id
      }
    },
    "class": "dropdown-item"
  }, {
    "default": (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(function () {
      return _cache[23] || (_cache[23] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
        "class": "ti ti-x me-2"
      }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("Close Chat", -1 /* CACHED */)]);
    }),
    _: 1 /* STABLE */,
    __: [23]
  }, 8 /* PROPS */, ["to"])])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    type: "text",
    "class": "form-control",
    "onUpdate:modelValue": _cache[0] || (_cache[0] = function ($event) {
      return $data.message.search = $event;
    }),
    placeholder: "Search Message"
  }, null, 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.message.search]]), _cache[26] || (_cache[26] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
    "class": "input-group-text"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-search"
  })], -1 /* CACHED */))])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [$data.message.loader ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_12, _cache[27] || (_cache[27] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "lds-roller"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div")], -1 /* CACHED */)]))) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" List Message "), ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($data.message.list.filter(function (item) {
    return item.message.detail.text.toLowerCase().includes($data.message.search.toLowerCase());
  }), function (list, index) {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["chats", list.sender ? 'chats-right' : '']),
      key: index
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: $options.getPhotoPict(list.details.participant),
      "class": "rounded-circle",
      alt: "image"
    }, null, 8 /* PROPS */, _hoisted_15)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_16, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["chat-profile-name d-flex justify-content-end", list.sender ? 'me-4' : 'ms-4'])
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(list.time), 1 /* TEXT */), list.status == 'READ' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_18, _toConsumableArray(_cache[28] || (_cache[28] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-checks"
    }, null, -1 /* CACHED */)])))) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])], 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_19, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["message-content", list.message.type == 'audio' && !list.sender ? 'bg-transparent p-0' : ''])
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Image not ready "), list.message.type == 'image' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_20, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_21, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: list.message.detail.asset ? list.message.detail.url : $data.attribute.image,
      alt: "img",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)(!list.message.detail.asset ? 'height: 100%' : '')
    }, null, 12 /* STYLE, PROPS */, _hoisted_22), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_23, [!list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
      key: 0,
      "class": "ti ti-download fs-30",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.downloadMedia(list.details, list.id, list.message.mime);
      }
    }, _toConsumableArray(_cache[29] || (_cache[29] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_24)) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
      key: 1,
      "class": "gallery-img",
      "data-fancybox": "gallery-img",
      href: list.message.detail.url,
      title: list.message.detail.text
    }, _toConsumableArray(_cache[30] || (_cache[30] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-eye"
    }, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_25))])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Image not ready "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Video "), list.message.type == 'video' && !list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_26, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_27, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: $data.attribute.video,
      alt: "img",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)(!list.message.detail.asset ? 'height: 100%' : '')
    }, null, 12 /* STYLE, PROPS */, _hoisted_28), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_29, [!list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
      key: 0,
      "class": "ti ti-download fs-30",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.downloadMedia(list.details, list.id, list.message.mime);
      }
    }, _toConsumableArray(_cache[31] || (_cache[31] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_30)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), list.message.type == 'video' && list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_31, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("video", _hoisted_32, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("source", {
      src: list.message.detail.url,
      type: "video/mp4"
    }, null, 8 /* PROPS */, _hoisted_33)])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Video "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Audio "), list.message.type == 'audio' && !list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_34, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_35, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: $data.attribute.audio,
      alt: "img",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)(!list.message.detail.asset ? 'height: 100%' : '')
    }, null, 12 /* STYLE, PROPS */, _hoisted_36), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_37, [!list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
      key: 0,
      "class": "ti ti-download fs-30",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.downloadMedia(list.details, list.id, list.message.mime);
      }
    }, _toConsumableArray(_cache[32] || (_cache[32] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_38)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), list.message.type == 'audio' && list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_39, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("audio", _hoisted_40, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("source", {
      src: list.message.detail.url,
      type: "audio/mpeg"
    }, null, 8 /* PROPS */, _hoisted_41)])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Audio "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Document "), list.message.type == 'document' && !list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_42, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_43, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: $data.attribute.document,
      alt: "img",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)(!list.message.detail.asset ? 'height: 100%' : '')
    }, null, 12 /* STYLE, PROPS */, _hoisted_44), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_45, [!list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
      key: 0,
      "class": "ti ti-download fs-30",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.downloadMedia(list.details, list.id, list.message.mime);
      }
    }, _toConsumableArray(_cache[33] || (_cache[33] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_46)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), list.message.type == 'document' && list.message.detail.asset ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_47, [_cache[35] || (_cache[35] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
      "class": "file-icon"
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-files"
    })], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_48, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h6", _hoisted_49, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(list.message.detail.title), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", null, " File " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(list.message.mime), 1 /* TEXT */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      target: "_blank",
      href: list.message.detail.url,
      "class": "download-icon"
    }, _toConsumableArray(_cache[34] || (_cache[34] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-download"
    }, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_50)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Document "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Location "), list.message.type == 'location' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_51, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_52, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      src: $data.attribute.location,
      alt: "img",
      style: {
        "height": "100%"
      }
    }, null, 8 /* PROPS */, _hoisted_53), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_54, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      "class": "ti ti-url fs-30",
      target: "_blank",
      href: list.message.detail.url
    }, _toConsumableArray(_cache[36] || (_cache[36] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, null, -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_55)])])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Location "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      innerHTML: $options.formattedText(list.message.detail.text)
    }, null, 8 /* PROPS */, _hoisted_56), list.message.url != null ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_57, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      href: list.message.url,
      target: "_blank",
      "class": "link-primary mt-2"
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(list.message.url), 9 /* TEXT, PROPS */, _hoisted_58)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" For Image "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_59, [_cache[39] || (_cache[39] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      "class": "#",
      href: "#",
      "data-bs-toggle": "dropdown"
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-dots-vertical"
    })], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", _hoisted_60, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" <li>\n                                            <a\n                                                class=\"dropdown-item reply-btn\"\n                                                href=\"#\"\n                                                ><i\n                                                    class=\"ti ti-corner-up-left me-2\"\n                                                ></i\n                                                >Reply</a\n                                            >\n                                        </li> "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      "class": "dropdown-item",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.deleteMessage(list);
      }
    }, _toConsumableArray(_cache[37] || (_cache[37] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-trash me-2"
    }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("Delete Message", -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_61)]), list.sender ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("li", _hoisted_62, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
      "class": "dropdown-item",
      href: "javascript:void(0);",
      onClick: function onClick($event) {
        return $options.deleteEveryOne(list);
      }
    }, _toConsumableArray(_cache[38] || (_cache[38] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
      "class": "ti ti-trash me-2"
    }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("Pull Message", -1 /* CACHED */)])), 8 /* PROPS */, _hoisted_63)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])])], 2 /* CLASS */);
  }), 128 /* KEYED_FRAGMENT */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Message ")])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_64, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("form", _hoisted_65, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_66, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_67, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("textarea", {
    "class": "form-control",
    placeholder: "Type Your Message",
    ref: "messageInput",
    rows: "1",
    disabled: $data.send.loader,
    onInput: _cache[1] || (_cache[1] = function () {
      return $options.autoResize && $options.autoResize.apply($options, arguments);
    }),
    onKeydown: [_cache[2] || (_cache[2] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withKeys)(function () {
      return $options.handleEnter && $options.handleEnter.apply($options, arguments);
    }, ["enter"])), _cache[3] || (_cache[3] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withKeys)((0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $options.insertLineBreak && $options.insertLineBreak.apply($options, arguments);
    }, ["shift", "prevent"]), ["enter"]))],
    "onUpdate:modelValue": _cache[4] || (_cache[4] = function ($event) {
      return $data.send.text = $event;
    })
  }, null, 40 /* PROPS, NEED_HYDRATION */, _hoisted_68), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.send.text]])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_69, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "#",
    onClick: _cache[5] || (_cache[5] = function ($event) {
      return $options.setFileType('.pdf, .doc, .docx, .xlsx, .zip, .html, .php, .css, .js, .ppt, .txt');
    }),
    "class": "action-circle position-absolute"
  }, _cache[40] || (_cache[40] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-file"
  }, null, -1 /* CACHED */)])), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    type: "file",
    "class": "open-file position-relative",
    name: "files",
    ref: "fileInput",
    id: "files",
    accept: $data.fileTypes,
    onChange: _cache[6] || (_cache[6] = function () {
      return $options.handleFileChange && $options.handleFileChange.apply($options, arguments);
    })
  }, null, 40 /* PROPS, NEED_HYDRATION */, _hoisted_70)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_71, [_cache[44] || (_cache[44] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "#",
    "data-bs-toggle": "dropdown"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-dots-vertical"
  })], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_72, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "javascript:void(0);",
    onClick: _cache[7] || (_cache[7] = function () {
      return $options.openModalCam && $options.openModalCam.apply($options, arguments);
    }),
    "class": "dropdown-item"
  }, _cache[41] || (_cache[41] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-camera-selfie me-2"
  }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("Camera", -1 /* CACHED */)])), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "#",
    "class": "dropdown-item",
    onClick: _cache[8] || (_cache[8] = function ($event) {
      return $options.setFileType('image/*');
    })
  }, _cache[42] || (_cache[42] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-photo-up me-2"
  }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Gallery ", -1 /* CACHED */)])), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "#",
    "class": "dropdown-item",
    onClick: _cache[9] || (_cache[9] = function ($event) {
      return $options.setFileType('video/*');
    })
  }, _cache[43] || (_cache[43] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-video me-2"
  }, null, -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Video ", -1 /* CACHED */)]))])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_73, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    "class": "btn btn-primary",
    type: "button",
    disabled: $data.send.loader,
    onClick: _cache[10] || (_cache[10] = function () {
      return $options.sendMessage && $options.sendMessage.apply($options, arguments);
    })
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)($data.send.loader ? 'ti ti-circle' : 'ti ti-send')
  }, null, 2 /* CLASS */)], 8 /* PROPS */, _hoisted_74)])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Modal for update file "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_75, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_76, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_77, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_78, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h4", _hoisted_79, " Send " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.file.previewType == "document" ? "Dokument" : "Media"), 1 /* TEXT */), _cache[45] || (_cache[45] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    type: "button",
    "class": "btn-close",
    id: "closeModal",
    "data-bs-dismiss": "modal"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-x"
  })], -1 /* CACHED */))]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_80, [$data.file.previewType === 'image' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_81, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
    src: $data.file.filePreview,
    alt: "Image Preview",
    "class": "img-fluid"
  }, null, 8 /* PROPS */, _hoisted_82)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.file.previewType === 'video' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_83, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("video", _hoisted_84, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("source", {
    src: $data.file.filePreview,
    type: "video/mp4"
  }, null, 8 /* PROPS */, _hoisted_85)])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.file.previewType === 'audio' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_86, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("audio", _hoisted_87, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("source", {
    src: $data.file.filePreview,
    type: "audio/mpeg"
  }, null, 8 /* PROPS */, _hoisted_88)])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.file.previewType === 'document' ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_89, [$data.file.isPDF ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("embed", {
    key: 0,
    src: $data.file.filePreview,
    type: "application/pdf",
    width: "100%",
    height: "400px"
  }, null, 8 /* PROPS */, _hoisted_90)) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("img", {
    key: 1,
    src: $data.attribute.document,
    "class": "w-50",
    alt: "img"
  }, null, 8 /* PROPS */, _hoisted_91))])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_92, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("textarea", {
    "class": "form-control",
    placeholder: "Type Your Message",
    ref: "messageInputModal",
    rows: "1",
    disabled: $data.send.loader,
    onInput: _cache[11] || (_cache[11] = function () {
      return $options.autoResizeModal && $options.autoResizeModal.apply($options, arguments);
    }),
    onKeydown: _cache[12] || (_cache[12] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withKeys)((0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $options.insertLineBreak && $options.insertLineBreak.apply($options, arguments);
    }, ["shift", "prevent"]), ["enter"])),
    "onUpdate:modelValue": _cache[13] || (_cache[13] = function ($event) {
      return $data.send.text = $event;
    })
  }, null, 40 /* PROPS, NEED_HYDRATION */, _hoisted_93), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.send.text]])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_94, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    type: "button",
    disabled: $data.send.loader,
    "class": "btn btn-primary",
    onClick: _cache[14] || (_cache[14] = function () {
      return $options.confirmFile && $options.confirmFile.apply($options, arguments);
    })
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.send.loader ? "Loading..." : "Send Message") + " ", 1 /* TEXT */), _cache[46] || (_cache[46] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-send ms-2"
  }, null, -1 /* CACHED */))], 8 /* PROPS */, _hoisted_95)])])])], 512 /* NEED_PATCH */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Modal for update "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Modal For Take Photo "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_96, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_97, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_98, [_cache[50] || (_cache[50] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    "class": "modal-header"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h4", {
    "class": "modal-title"
  }, "Take Photo"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
    type: "button",
    "class": "btn-close",
    id: "closeModalCamera",
    "data-bs-dismiss": "modal"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-x"
  })])], -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_99, [$data.camera.picture == null ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_100, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("video", _hoisted_101, null, 512 /* NEED_PATCH */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("canvas", _hoisted_102, null, 512 /* NEED_PATCH */)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.camera.picture != null ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_103, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
    src: $data.camera.picture,
    alt: "Image Preview",
    "class": "img-fluid"
  }, null, 8 /* PROPS */, _hoisted_104)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_105, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("textarea", {
    "class": "form-control",
    placeholder: "Type Your Message",
    ref: "messageInputCamera",
    rows: "1",
    disabled: $data.send.loader,
    onInput: _cache[15] || (_cache[15] = function () {
      return $options.autoResizeCamera && $options.autoResizeCamera.apply($options, arguments);
    }),
    onKeydown: _cache[16] || (_cache[16] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withKeys)((0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(function () {
      return $options.insertLineBreak && $options.insertLineBreak.apply($options, arguments);
    }, ["shift", "prevent"]), ["enter"])),
    "onUpdate:modelValue": _cache[17] || (_cache[17] = function ($event) {
      return $data.send.text = $event;
    })
  }, null, 40 /* PROPS, NEED_HYDRATION */, _hoisted_106), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.send.text]])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_107, [$data.camera.picture == null ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
    key: 0,
    type: "button",
    "class": "btn btn-primary",
    onClick: _cache[18] || (_cache[18] = function () {
      return $options.capturePhoto && $options.capturePhoto.apply($options, arguments);
    })
  }, _cache[47] || (_cache[47] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Take Photo ", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-camera ms-2"
  }, null, -1 /* CACHED */)]))) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.camera.picture != null && !$data.send.loader ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
    key: 1,
    type: "button",
    "class": "btn btn-primary me-2",
    onClick: _cache[19] || (_cache[19] = function () {
      return $options.reTakePhoto && $options.reTakePhoto.apply($options, arguments);
    })
  }, _cache[48] || (_cache[48] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" Re-Take ", -1 /* CACHED */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-camera ms-2"
  }, null, -1 /* CACHED */)]))) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $data.camera.picture != null ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
    key: 2,
    type: "button",
    disabled: $data.send.loader,
    onClick: _cache[20] || (_cache[20] = function () {
      return $options.sendPhoto && $options.sendPhoto.apply($options, arguments);
    }),
    "class": "btn btn-primary"
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.send.loader ? "Loading..." : "Send Photo") + " ", 1 /* TEXT */), _cache[49] || (_cache[49] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    "class": "ti ti-send ms-2"
  }, null, -1 /* CACHED */))], 8 /* PROPS */, _hoisted_108)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])], 512 /* NEED_PATCH */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" End Modal ")], 64 /* STABLE_FRAGMENT */);
}

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.lds-roller,\n.lds-roller div,\n.lds-roller div:after {\n    box-sizing: border-box;\n}\n.lds-roller {\n    display: inline-block;\n    position: relative;\n    width: 80px;\n    height: 80px;\n}\n.lds-roller div {\n    animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;\n    transform-origin: 40px 40px;\n}\n.lds-roller div:after {\n    content: \" \";\n    display: block;\n    position: absolute;\n    width: 7.2px;\n    height: 7.2px;\n    border-radius: 50%;\n    background: currentColor;\n    color: #005d4c;\n    margin: -3.6px 0 0 -3.6px;\n}\n.lds-roller div:nth-child(1) {\n    animation-delay: -0.036s;\n}\n.lds-roller div:nth-child(1):after {\n    top: 62.62742px;\n    left: 62.62742px;\n}\n.lds-roller div:nth-child(2) {\n    animation-delay: -0.072s;\n}\n.lds-roller div:nth-child(2):after {\n    top: 67.71281px;\n    left: 56px;\n}\n.lds-roller div:nth-child(3) {\n    animation-delay: -0.108s;\n}\n.lds-roller div:nth-child(3):after {\n    top: 70.90963px;\n    left: 48.28221px;\n}\n.lds-roller div:nth-child(4) {\n    animation-delay: -0.144s;\n}\n.lds-roller div:nth-child(4):after {\n    top: 72px;\n    left: 40px;\n}\n.lds-roller div:nth-child(5) {\n    animation-delay: -0.18s;\n}\n.lds-roller div:nth-child(5):after {\n    top: 70.90963px;\n    left: 31.71779px;\n}\n.lds-roller div:nth-child(6) {\n    animation-delay: -0.216s;\n}\n.lds-roller div:nth-child(6):after {\n    top: 67.71281px;\n    left: 24px;\n}\n.lds-roller div:nth-child(7) {\n    animation-delay: -0.252s;\n}\n.lds-roller div:nth-child(7):after {\n    top: 62.62742px;\n    left: 17.37258px;\n}\n.lds-roller div:nth-child(8) {\n    animation-delay: -0.288s;\n}\n.lds-roller div:nth-child(8):after {\n    top: 56px;\n    left: 12.28719px;\n}\n@keyframes lds-roller {\n0% {\n        transform: rotate(0deg);\n}\n100% {\n        transform: rotate(360deg);\n}\n}\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_laravel_mix_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_style_index_0_id_5be37134_lang_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./group.vue?vue&type=style&index=0&id=5be37134&lang=css */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_laravel_mix_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_style_index_0_id_5be37134_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_laravel_mix_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_style_index_0_id_5be37134_lang_css__WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./resources/js/assets/icons/audio.png":
/*!*********************************************!*\
  !*** ./resources/js/assets/icons/audio.png ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/audio.png?46d84dece79a860e9e9680ee40dc2a53");

/***/ }),

/***/ "./resources/js/assets/icons/documents.png":
/*!*************************************************!*\
  !*** ./resources/js/assets/icons/documents.png ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/documents.png?449ba13cda9d456205f9f027f1fe4356");

/***/ }),

/***/ "./resources/js/assets/icons/image.png":
/*!*********************************************!*\
  !*** ./resources/js/assets/icons/image.png ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/image.png?5a26f50495a314ffa59fbb93402cc804");

/***/ }),

/***/ "./resources/js/assets/icons/map.png":
/*!*******************************************!*\
  !*** ./resources/js/assets/icons/map.png ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/map.png?82056140ce3605dfbf34029e764aae71");

/***/ }),

/***/ "./resources/js/assets/icons/video.png":
/*!*********************************************!*\
  !*** ./resources/js/assets/icons/video.png ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("/images/video.png?825fa04ed0470feedc474ee57eb57f6a");

/***/ }),

/***/ "./resources/js/pages/group.vue":
/*!**************************************!*\
  !*** ./resources/js/pages/group.vue ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _group_vue_vue_type_template_id_5be37134__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./group.vue?vue&type=template&id=5be37134 */ "./resources/js/pages/group.vue?vue&type=template&id=5be37134");
/* harmony import */ var _group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./group.vue?vue&type=script&lang=js */ "./resources/js/pages/group.vue?vue&type=script&lang=js");
/* harmony import */ var _group_vue_vue_type_style_index_0_id_5be37134_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./group.vue?vue&type=style&index=0&id=5be37134&lang=css */ "./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css");
/* harmony import */ var _Users_mdedehhidayatullah_Documents_myprojects_whatsmail_azira_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_Users_mdedehhidayatullah_Documents_myprojects_whatsmail_azira_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_group_vue_vue_type_template_id_5be37134__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"resources/js/pages/group.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./resources/js/pages/group.vue?vue&type=script&lang=js":
/*!**************************************************************!*\
  !*** ./resources/js/pages/group.vue?vue&type=script&lang=js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./group.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css":
/*!**********************************************************************************!*\
  !*** ./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_laravel_mix_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_style_index_0_id_5be37134_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./group.vue?vue&type=style&index=0&id=5be37134&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/laravel-mix/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=style&index=0&id=5be37134&lang=css");


/***/ }),

/***/ "./resources/js/pages/group.vue?vue&type=template&id=5be37134":
/*!********************************************************************!*\
  !*** ./resources/js/pages/group.vue?vue&type=template&id=5be37134 ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_template_id_5be37134__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_group_vue_vue_type_template_id_5be37134__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./group.vue?vue&type=template&id=5be37134 */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./resources/js/pages/group.vue?vue&type=template&id=5be37134");


/***/ })

}]);