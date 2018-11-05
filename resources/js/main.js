
const NotFoundComponent = {
    template: document.querySelector('template#not-found').innerHTML,
};

const Home = {
    template: document.querySelector('template#home').innerHTML,
};



const Routes = [
    { path: '/',      component: Home },
    // { path: '/foo',   component: Foo },
    // { path: '/bar',   component: Bar },
    { path: '*',      component: NotFoundComponent },
];

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const Router = new VueRouter({
    mode:   'history',
    routes: Routes,
})

// 4. Create and mount the root instance.
// Make sure to inject the router with the router option to make the
// whole app router-aware.
const app = new Vue({
    router: Router,
    data: {
        message: 'Hello Vue!',
    },
}).$mount('#app')
