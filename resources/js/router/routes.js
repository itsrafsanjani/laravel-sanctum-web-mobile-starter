import Vue from 'vue'
import VueRouter from 'vue-router';
import Dashboard from '../pages/Dashboard';

Vue.use(VueRouter);

const routes = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/dashboard',
            component: Dashboard,
            name: 'dashboard'
        }
    ]
});

export default routes;
