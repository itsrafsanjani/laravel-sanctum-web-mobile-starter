import Vue from 'vue'
import VueRouter from 'vue-router';
import Dashboard from '../pages/Dashboard';
import About from '../pages/About';
import Welcome from '../pages/Welcome';

Vue.use(VueRouter);

const routes = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            component: Welcome,
            name: 'welcome'
        },
        {
            path: '/dashboard',
            component: Dashboard,
            name: 'dashboard'
        },
        {
            path: '/about',
            component: About,
            name: 'about'
        }
    ]
});

export default routes;
