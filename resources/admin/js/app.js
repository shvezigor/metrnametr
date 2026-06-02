/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import 'vue-search-select/dist/VueSearchSelect.css'; // Styles for autocomplete

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Notifications from 'vue-notification';
Vue.use(Notifications);

Vue.use(require('vue-moment'));

Vue.filter('truncate', function (text, stop, clamp) {
  return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('menu-component', require('./components/shared/Menu.vue').default)
Vue.component('header-component', require('./components/shared/Header.vue').default)
Vue.component('modal', require('./components/shared/Modal.vue').default)

const Users = Vue.component('users-list', require('./components/Users.vue').default)

const Articles = Vue.component('articles-list', require('./components/articles/List.vue').default)
const ArticleCreate = Vue.component('article-create', require('./components/articles/Create.vue').default)
const ArticleEdit = Vue.component('article-edit', require('./components/articles/Edit.vue').default)

const Catalog = Vue.component('catalog-list', require('./components/Catalog.vue').default)
const Categories = Vue.component('categories-list', require('./components/Categories.vue').default)

const Products = Vue.component('products-list', require('./components/products/List.vue').default)
const ProductsCreate = Vue.component('products-create', require('./components/products/Create.vue').default)
const ProductsEdit = Vue.component('products-edit', require('./components/products/Edit.vue').default)

const Sizes = Vue.component('sizes-list', require('./components/Sizes.vue').default)
const Types = Vue.component('types-list', require('./components/Types.vue').default)

const Vacancies = Vue.component('vacancies-list', require('./components/vacancies/List.vue').default)
const VacanciesCreate = Vue.component('vacancies-create', require('./components/vacancies/Create.vue').default)
const VacanciesEdit = Vue.component('vacancies-edit', require('./components/vacancies/Edit.vue').default)

const Subscribers = Vue.component('subscribers-list', require('./components/Subscribers.vue').default)
const Messages = Vue.component('messages-list', require('./components/Messages.vue').default)
const Orders = Vue.component('orders-list', require('./components/Orders.vue').default)

const Profile = Vue.component('profile', require('./components/Profile').default);

const PageNotFound = Vue.component("page-not-found", {
  template: "",
  created: function() {
      // Redirect outside the app using plain old javascript
      window.location.href = "/page-not-found";
  }
});

const routes = [
  { path: '/admin', redirect: { name: 'users' } },

  // Users
  { path: '/admin/users', name: 'users', component: Users },

  // Articles
  { path: '/admin/articles', name: 'articles-list', component: Articles },
  { path: '/admin/articles/create', name: 'article-create', component: ArticleCreate },
  { path: '/admin/articles/:id/edit', name: 'article-edit', component: ArticleEdit },

  // Catalog
  { path: '/admin/catalog', name: 'catalog', component: Catalog },

  // Categories
  { path: '/admin/categories', name: 'categories', component: Categories },

  // Products
  { path: '/admin/products', name: 'products-list', component: Products },
  { path: '/admin/products/create', name: 'products-create', component: ProductsCreate },
  { path: '/admin/products/:id/edit', name: 'products-edit', component: ProductsEdit },

  // Sizes
  { path: '/admin/sizes', name: 'sizes', component: Sizes },

  // Types
  { path: '/admin/types', name: 'types', component: Types },

  // Vacancies
  { path: '/admin/vacancies', name: 'vacancies-list', component: Vacancies },
  { path: '/admin/vacancies/create', name: 'vacancies-create', component: VacanciesCreate },
  { path: '/admin/vacancies/:id/edit', name: 'vacancies-edit', component: VacanciesEdit },

  // Mail
  { path: '/admin/subscribers', name: 'subscribers-list', component: Subscribers },
  { path: '/admin/messages', name: 'messages-list', component: Messages },
  { path: '/admin/orders', name: 'orders-list', component: Orders },

  { path: '/admin/profile', name: 'profile', component: Profile },

  { path: '*', component: PageNotFound }
];

const router = new VueRouter({
  mode: 'history',
  routes // short for `routes: routes`
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import i18n from './i18n';
import store from './store'

const app = new Vue({
  el: '#app',
  router,
  i18n,
  store,
  created: function () {

    // axios.interceptors.response.use(undefined, function (err) {
    //   return new Promise(function (resolve, reject) {
    //     if (err.status === 401) {
    //       window.location.href = '/login';
    //     }
    //     throw err;
    //   });
    // });

    axios.interceptors.response.use(null, (error) => {
      if (error.response && (error.response.status == 419 || error.response.status == 401)) {
          // Session Timed Out | Not Authenticated
          window.location.href = '/login';
      }
      return Promise.reject(error);
    });

    this.$store.dispatch("loadAuthUser").catch(err => {
      console.error('Error loading auth user:', err);
    });
  }
});
