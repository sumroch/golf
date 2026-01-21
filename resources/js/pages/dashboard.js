import { createApp } from "vue";
import Clock from "../components/Clock.vue";

const el = document.getElementById("clock-widget");

if (el) {
    createApp(Clock, {
        timezone: el.dataset.timezone,
    }).mount(el);
}