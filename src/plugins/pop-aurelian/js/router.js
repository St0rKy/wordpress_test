import Table from "./components/Table";
import Graph from "./components/Graph";
import Settings from "./components/Settings";
import {createRouter, createWebHashHistory} from "vue-router";


const PAApp = window.PAApp || {};
/**
 * Route Definition
 *
 * @type {[{path: string, component: {data(): {}, methods: {addToCart(*): void}, mounted(): void, props: {table: Object | ObjectConstructor}}, name: string, props: {table: *}},{path: string, component: {data(): {items: [], selected: number}, methods: {addToCart(*): void}, mounted(): void, props: {graph: Object | ObjectConstructor}}, name: string, props: {graph: *}},{path: string, component: {data(): {items: [], selected: number}, methods: {addToCart(*): void}, mounted(): void, props: {settings: Object | ObjectConstructor, routes: Object | ObjectConstructor}}, name: string, props: {settings, routes: *}}]}
 */
const routes = [
    { path: '/', component: Table, props: {table: PAApp.data.table, settings: PAApp.settings, routes: PAApp.routes} , name: ''},
    { path: '/graph', component: Graph, props: {graph: PAApp.data.graph, routes: PAApp.routes}, name: 'graph'},
    { path: '/settings', component: Settings, props: {settings: PAApp.settings, routes: PAApp.routes}, name: 'settings'},
];

/**
 * Creates the router
 *
 * @type {Router}
 */
export const router = createRouter({
    history: createWebHashHistory(),
    routes,
    linkActiveClass: 'active'
});
