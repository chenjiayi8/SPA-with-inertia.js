import { createApp, h } from "vue";
import {createInertiaApp, Link, Head} from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import Layout from "./Shared/Layout";

createInertiaApp({
    resolve: async name => {
        // import(tab not quote ./Pages ...
        let page = (await import(`./Pages/${name}`)).default;
        if (page.layout === undefined){
            page.layout =Layout;
        }
        // page.title ??= name;
        // console.log(page.title);
        // page.layout = Layout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component("Link", Link)
            .component("Head", Head)
            .mount(el);
    },
    title: title => `My app - ${title}`,
});

InertiaProgress.init({
    color: "red",
    showSpinner: true,
});
