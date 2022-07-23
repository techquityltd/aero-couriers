/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: {
    label: {
      type: String,
      required: true
    },
    parcels: {
      type: Object,
      required: true
    },
    locked: {
      type: Boolean,
      "default": false
    },
    courier: {
      type: String,
      required: true
    },
    withDimensions: {
      type: Boolean,
      "default": false
    },
    defaultWeight: {
      type: Number,
      "default": 1
    },
    defaultLength: {
      type: Number,
      "default": 1
    },
    defaultWidth: {
      type: Number,
      "default": 1
    },
    defaultHeight: {
      type: Number,
      "default": 1
    },
    weightUnit: {
      type: String,
      "default": 'g'
    },
    dimensionUnit: {
      type: String,
      "default": 'cm'
    }
  },
  data: function data() {
    return {
      numberOfParcels: this.parcels.amount,
      weights: [],
      lengths: [],
      widths: [],
      heights: []
    };
  },
  beforeMount: function beforeMount() {
    for (var i = 1; i < 10; i++) {
      this.weights[i] = this.isset(this.parcels.weights, i) ? this.parcels.weights[i] : this.defaultWeight;
      this.lengths[i] = this.isset(this.parcels.lengths, i) ? this.parcels.lengths[i] : this.defaultLength;
      this.widths[i] = this.isset(this.parcels.widths, i) ? this.parcels.widths[i] : this.defaultWidth;
      this.heights[i] = this.isset(this.parcels.heights, i) ? this.parcels.heights[i] : this.defaultHeight;
    }
  },
  methods: {
    isset: function isset(data) {
      var key = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (typeof data !== 'undefined') {
        if (key) {
          if (typeof data[key] !== 'undefined') {
            return true;
          } else {
            return false;
          }
        }

        return true;
      }

      return false;
    }
  },
  watch: {
    numberOfParcels: function numberOfParcels(newParcels) {
      if (newParcels < 1) {
        this.numberOfParcels = 1;
      }

      if (newParcels > 9) {
        this.numberOfParcels = 9;
      }
    }
  }
});

/***/ }),

/***/ "./resources/js/components/SplitConsignment.vue":
/*!******************************************************!*\
  !*** ./resources/js/components/SplitConsignment.vue ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SplitConsignment.vue?vue&type=template&id=57eaa4e4& */ "./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4&");
/* harmony import */ var _SplitConsignment_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SplitConsignment.vue?vue&type=script&lang=js& */ "./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SplitConsignment_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__.render,
  _SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/SplitConsignment.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SplitConsignment_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./SplitConsignment.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SplitConsignment_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4&":
/*!*************************************************************************************!*\
  !*** ./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4& ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SplitConsignment_vue_vue_type_template_id_57eaa4e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./SplitConsignment.vue?vue&type=template&id=57eaa4e4& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4&":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/SplitConsignment.vue?vue&type=template&id=57eaa4e4& ***!
  \****************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h3", [_vm._v("Consignments")]),
    _vm._v(" "),
    _c("div", [
      _c("div", { staticClass: "w-1/3" }, [
        _c("label", { staticClass: "block" }, [_vm._v("Parcels")]),
        _vm._v(" "),
        _c("input", {
          directives: [
            {
              name: "model",
              rawName: "v-model",
              value: _vm.numberOfParcels,
              expression: "numberOfParcels",
            },
          ],
          staticClass: "w-full",
          attrs: {
            type: "number",
            id: _vm.label,
            name:
              "configuration[" + _vm.courier + "][" + _vm.label + "][amount]",
            disabled: _vm.locked === "true",
            required: "",
          },
          domProps: { value: _vm.numberOfParcels },
          on: {
            input: function ($event) {
              if ($event.target.composing) {
                return
              }
              _vm.numberOfParcels = $event.target.value
            },
          },
        }),
      ]),
    ]),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "flex flex-wrap" },
      _vm._l(Number(_vm.numberOfParcels), function (index) {
        return _c("div", { key: index, staticClass: "w-1/3 my-4" }, [
          _c("div", { staticClass: "flex flex-col card m-2" }, [
            _c("h3", [
              _vm._v("Parcel #"),
              _c("span", { domProps: { innerHTML: _vm._s(index) } }),
            ]),
            _vm._v(" "),
            _c("div", [
              _c("label", { staticClass: "block" }, [_vm._v("Weight")]),
              _vm._v(" "),
              _c("div", { staticClass: "price price--right" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.weights[index],
                      expression: "weights[index]",
                    },
                  ],
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    autocomplete: "off",
                    min: "0",
                    name:
                      "configuration[" +
                      _vm.courier +
                      "][" +
                      _vm.label +
                      "][weights][" +
                      index +
                      "]",
                    disabled: _vm.locked === "true",
                    required: "",
                  },
                  domProps: { value: _vm.weights[index] },
                  on: {
                    input: function ($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(_vm.weights, index, $event.target.value)
                    },
                  },
                }),
                _vm._v(" "),
                _c("label", {
                  domProps: { innerHTML: _vm._s(_vm.weightUnit) },
                }),
              ]),
            ]),
            _vm._v(" "),
            _vm.withDimensions
              ? _c("div", [
                  _c("div", { staticClass: "mt-2" }, [
                    _c("label", { staticClass: "block" }, [_vm._v("Length")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "price price--right" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.lengths[index],
                            expression: "lengths[index]",
                          },
                        ],
                        staticClass: "w-full",
                        attrs: {
                          type: "number",
                          autocomplete: "off",
                          min: "0",
                          name:
                            "configuration[" +
                            _vm.courier +
                            "][" +
                            _vm.label +
                            "][length][" +
                            index +
                            "]",
                          disabled: _vm.locked === "true",
                          required: "",
                        },
                        domProps: { value: _vm.lengths[index] },
                        on: {
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.lengths, index, $event.target.value)
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("label", {
                        domProps: { innerHTML: _vm._s(_vm.dimensionUnit) },
                      }),
                    ]),
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "mt-2" }, [
                    _c("label", { staticClass: "block" }, [_vm._v("Width")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "price price--right" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.widths[index],
                            expression: "widths[index]",
                          },
                        ],
                        staticClass: "w-full",
                        attrs: {
                          type: "number",
                          autocomplete: "off",
                          min: "0",
                          name:
                            "configuration[" +
                            _vm.courier +
                            "][" +
                            _vm.label +
                            "][weights][" +
                            index +
                            "]",
                          disabled: _vm.locked === "true",
                          required: "",
                        },
                        domProps: { value: _vm.widths[index] },
                        on: {
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.widths, index, $event.target.value)
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("label", {
                        domProps: { innerHTML: _vm._s(_vm.dimensionUnit) },
                      }),
                    ]),
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "mt-2" }, [
                    _c("label", { staticClass: "block" }, [_vm._v("Height")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "price price--right" }, [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.heights[index],
                            expression: "heights[index]",
                          },
                        ],
                        staticClass: "w-full",
                        attrs: {
                          type: "number",
                          autocomplete: "off",
                          min: "0",
                          name:
                            "configuration[" +
                            _vm.courier +
                            "][" +
                            _vm.label +
                            "][weights][" +
                            index +
                            "]",
                          disabled: _vm.locked === "true",
                          required: "",
                        },
                        domProps: { value: _vm.heights[index] },
                        on: {
                          input: function ($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.heights, index, $event.target.value)
                          },
                        },
                      }),
                      _vm._v(" "),
                      _c("label", {
                        domProps: { innerHTML: _vm._s(_vm.dimensionUnit) },
                      }),
                    ]),
                  ]),
                ])
              : _vm._e(),
          ]),
        ])
      }),
      0
    ),
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        )
      }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./resources/js/components.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_SplitConsignment__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/SplitConsignment */ "./resources/js/components/SplitConsignment.vue");

window.couriersComponents = {
  install: function install(Vue) {
    Vue.component('split-consignment', _components_SplitConsignment__WEBPACK_IMPORTED_MODULE_0__["default"]);
  }
};
})();

/******/ })()
;