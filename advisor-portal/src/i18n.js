import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import Backend from 'i18next-http-backend';
import LanguageDetector from 'i18next-browser-languagedetector';

i18n
  // load translation using http -> see /public/locales (i.e. https://github.com/i18next/react-i18next/tree/master/example/react/public/locales)
  // learn more: https://github.com/i18next/i18next-http-backend
  .use(Backend)
  // detect user language (Browser / Cached)
  // learn more: https://github.com/i18next/i18next-browser-languageDetector
  .use(LanguageDetector)
  // pass the i18n instance to react-i18next.
  .use(initReactI18next)
  // init i18next
  // for all options read: https://www.i18next.com/overview/configuration-options
  .init({
    fallbackLng: 'en',
    lng: (localStorage.getItem('i18nextLng') == 'en-US' ? 'en' : localStorage.getItem('i18nextLng')) ?? 'en',
    backend: {
      // for all available options read the backend's repository readme file
      loadPath: process.env.PUBLIC_URL + '/locales/{{lng}}/{{ns}}.json'
    },

    interpolation: {
      escapeValue: false, // not needed for react as it escapes by default
    }
  });

export default i18n;

export function getActiveLanguageId() {
  const lang = i18n.language;
  var langId = 1;
  switch (lang) {
    case "ar":
      langId = 2;
      break;
    case "fr":
      langId = 3;
      break;
    case "sp":
      langId = 4;
      break;
    default:
      langId = 1;
  }

  return langId;
}

export function chageAppDirection() {
  document.body.dir = i18n.dir();
  document.documentElement.dir = i18n.dir();
  if (i18n.dir() == 'rtl') {
    document.getElementsByTagName('html')[0].classList.add("rtl");
  }
  else {
    document.getElementsByTagName('html')[0].classList.remove("rtl");
  }
}