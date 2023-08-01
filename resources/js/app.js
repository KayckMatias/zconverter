import "./bootstrap";

import { createApp } from "vue";
import { authController } from "./controllers/authController";

import router from "./router/index.js";

import App from "./App.vue";

import "vue3-toastify/dist/index.css";

const app = createApp(App);

app.config.globalProperties.$authController = authController;

import Pusher from "pusher-js";
window.Pusher = Pusher;

if (authController.getToken()) {
    authController.startLaravelEcho();
}

app.use(router).mount("#app");
