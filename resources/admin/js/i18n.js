import Vue from 'vue';
import VueI18n from 'vue-i18n';
import $ from 'jquery';

Vue.use(VueI18n);

let locale = $('html').attr('lang');
// locale = (typeof locale !== 'undefined') ? locale : 'en';
locale = 'ua'; // TODO: SET HARDCODE

// Create VueI18n instance with options
const i18n = new VueI18n({
  locale: locale, // set locale
  messages: {
    en: require('./locales/en.json'),
    ua: require('./locales/ua.json'),
  },
  fallbackLocale: 'en',
  silentTranslationWarn: true,
});

export default i18n;
